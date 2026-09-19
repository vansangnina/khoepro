<?php
/**
 * FITNADO - Research Job Queue & Execution Engine (Phase 04)
 * PHP 7.4 Compatible
 */

if (!class_exists('ProductResearch')) {
    require_once __DIR__ . '/class.ProductResearch.php';
}
if (!class_exists('AIResearchAgent')) {
    require_once __DIR__ . '/class.AIResearchAgent.php';
}
if (!class_exists('ResearchProviderFactory')) {
    require_once __DIR__ . '/class.ResearchProvider.php';
}

class ResearchJobQueue
{
    private $d;
    private $func;
    private $productResearch;
    private $aiAgent;

    public function __construct($d = null, $func = null)
    {
        $this->d = $d;
        $this->func = $func;
        $this->productResearch = new ProductResearch($d, $func);
        $this->aiAgent = new AIResearchAgent($d, $func);
    }

    /**
     * Create a new research job
     */
    public function createJob($seedId, $provider = 'ai_agent', $depth = 'STANDARD', array $payload = array())
    {
        if (!$this->d) return false;

        $jobData = array(
            'id_seed' => (int)$seedId,
            'provider' => $provider,
            'depth' => in_array($depth, array('QUICK', 'STANDARD', 'DEEP')) ? $depth : 'STANDARD',
            'status' => 'PENDING',
            'attempts' => 0,
            'max_attempts' => 3,
            'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'candidates_found' => 0,
            'candidates_created' => 0,
            'duplicates_count' => 0,
            'started_at' => 0,
            'finished_at' => 0,
            'duration' => 0,
            'date_created' => time(),
            'date_updated' => time()
        );

        return $this->d->insert('product_research_job', $jobData);
    }

    /**
     * Get next pending job with concurrency locking
     */
    public function getNextPendingJob()
    {
        if (!$this->d) return null;

        // Auto recover stale jobs running > 10 mins
        $this->recoverStaleJobs();

        // Pick 1 oldest pending job
        $job = $this->d->rawQueryOne(
            "select * from #_product_research_job where status = 'PENDING' order by id asc limit 0,1"
        );

        if (!empty($job)) {
            // Lock and mark running
            $updated = $this->d->rawQuery(
                "update #_product_research_job set status = 'RUNNING', started_at = ?, attempts = attempts + 1, date_updated = ? where id = ? and status = 'PENDING'",
                array(time(), time(), $job['id'])
            );
            return $this->d->rawQueryOne("select * from #_product_research_job where id = ? limit 0,1", array($job['id']));
        }

        return null;
    }

    /**
     * Recover jobs that got stuck in RUNNING state (e.g. timeout / process crash)
     */
    public function recoverStaleJobs($timeoutSeconds = 600)
    {
        if (!$this->d) return 0;
        $staleTime = time() - $timeoutSeconds;
        return $this->d->rawQuery(
            "update #_product_research_job set status = 'PENDING', date_updated = ? where status = 'RUNNING' and started_at < ? and attempts < max_attempts",
            array(time(), $staleTime)
        );
    }

    /**
     * Retry a failed job
     */
    public function retryJob($jobId)
    {
        if (!$this->d) return false;
        return $this->d->rawQuery(
            "update #_product_research_job set status = 'PENDING', attempts = 0, error_message = NULL, date_updated = ? where id = ?",
            array(time(), (int)$jobId)
        );
    }

    /**
     * Execute a job end-to-end
     */
    public function executeJob($jobId)
    {
        if (!$this->d) return array('status' => false, 'error' => 'Database connection failed');

        $job = $this->d->rawQueryOne("select * from #_product_research_job where id = ? limit 0,1", array((int)$jobId));
        if (empty($job)) {
            return array('status' => false, 'error' => 'Job not found');
        }

        $startTime = microtime(true);
        $candidatesFound = 0;
        $candidatesCreated = 0;
        $duplicatesCount = 0;

        try {
            // 1. Load Seed Data if id_seed is present
            $seed = null;
            if (!empty($job['id_seed'])) {
                $seed = $this->d->rawQueryOne("select * from #_product_research_seed where id = ? limit 0,1", array((int)$job['id_seed']));
            }

            $payload = !empty($job['payload']) ? json_decode($job['payload'], true) : array();
            if (!is_array($payload)) $payload = array();

            // 2. Instantiate Discovery Provider
            $providerName = $job['provider'] ?: ($seed['platform'] ?? 'ai_agent');
            $provider = ResearchProviderFactory::create($providerName, $this->d, $this->func);

            $options = array(
                'depth' => $job['depth'] ?? 'STANDARD',
                'max_results' => $seed['max_results'] ?? ($payload['max_results'] ?? 5),
                'rows' => $payload['rows'] ?? array()
            );

            // 3. Run Provider Discovery
            $seedInput = $seed ?: ($payload['keyword'] ?? 'Gym & Fitness equipment');
            $discoveredList = $provider->discover($seedInput, $options);
            $candidatesFound = count($discoveredList);

            // 4. Ingest & Process Each Candidate
            foreach ($discoveredList as $dto) {
                if (!($dto instanceof ResearchCandidateDTO)) {
                    $dto = new ResearchCandidateDTO((array)$dto);
                }

                if (empty($dto->name)) continue;

                // Normalize URL & Name
                $normUrl = $this->productResearch->normalizeUrl($dto->source_url);
                $normName = $this->productResearch->normalizeName($dto->name);
                $platform = $dto->platform ?: 'other';
                $extId = $dto->external_product_id;

                // 5. Check Duplicate using Phase 03 engine
                $dupCheck = $this->productResearch->checkDuplicate($platform, $extId, $dto->source_url, $dto->name, $dto->brand_hint);

                if ($dupCheck['is_duplicate'] && in_array($dupCheck['type'], array('EXACT_EXTERNAL_ID', 'EXACT_URL'))) {
                    // Update existing candidate's last_seen_at & create Snapshot
                    $matchedId = (int)$dupCheck['matched_id'];
                    $duplicatesCount++;

                    $this->d->rawQuery(
                        "update #_product_research set last_seen_at = ?, date_updated = ? where id = ?",
                        array(time(), time(), $matchedId)
                    );

                    // Record Snapshot
                    $this->d->insert('product_research_snapshot', array(
                        'id_research' => $matchedId,
                        'id_job' => (int)$job['id'],
                        'price' => $dto->price,
                        'sales_count' => $dto->sales_count,
                        'rating' => $dto->rating,
                        'review_count' => $dto->review_count,
                        'commission_rate' => $dto->commission_rate,
                        'creator_count' => $dto->creator_count,
                        'video_count' => $dto->video_count,
                        'top_video_views' => $dto->top_video_views,
                        'captured_at' => time()
                    ));

                    continue; // Skip creating duplicate candidate
                }

                // 6. AI In-depth Analysis (if not already fully populated)
                // 6. AI Analysis if requested or missing insights
                $aiAnalysis = null;
                $aiConfidence = 80;
                if (!empty($dto->raw_data) && is_array($dto->raw_data) && !empty($dto->raw_data['content_angles'])) {
                    $aiAnalysis = $dto->raw_data;
                    $aiConfidence = $dto->raw_data['confidence'] ?? 80;
                } elseif (empty($dto->problem_solved) || $job['depth'] === 'DEEP' || $providerName === 'ai_agent') {
                    $aiRes = $this->aiAgent->analyzeCandidate($dto->toArray(), array('depth' => $job['depth']));
                    if ($aiRes['status'] && !empty($aiRes['data'])) {
                        $aiAnalysis = $aiRes['data'];
                        if (empty($dto->problem_solved) && !empty($aiRes['data']['problem_solved'])) {
                            $dto->problem_solved = $aiRes['data']['problem_solved'];
                        }
                        if (empty($dto->target_audience) && !empty($aiRes['data']['target_audience'])) {
                            $dto->target_audience = $aiRes['data']['target_audience'];
                        }
                        $aiConfidence = $aiRes['data']['confidence'] ?? 80;
                    }
                }

                // 7. Auto Calculate Score using Phase 03 engine
                $candidateArray = $dto->toArray();
                $candidateArray['normalized_name'] = $normName;
                $candidateArray['normalized_url'] = $normUrl;
                $scoreResult = $this->productResearch->calculateTotalScore($candidateArray);

                // Prepare DB Record
                $candidateRecord = array(
                    'name' => $dto->name,
                    'normalized_name' => $normName,
                    'category_hint' => $dto->category_hint,
                    'brand_hint' => $dto->brand_hint,
                    'platform' => $platform,
                    'source_url' => $dto->source_url,
                    'normalized_url' => $normUrl,
                    'external_product_id' => $extId,
                    'image_url' => $dto->image_url,
                    'price' => $dto->price,
                    'original_price' => $dto->original_price,
                    'currency' => $dto->currency ?: 'VND',
                    'sales_count' => $dto->sales_count,
                    'rating' => $dto->rating,
                    'review_count' => $dto->review_count,
                    'commission_rate' => $dto->commission_rate,
                    'commission_value' => $dto->commission_value,
                    'estimated_gmv' => $dto->estimated_gmv,
                    'creator_count' => $dto->creator_count,
                    'video_count' => $dto->video_count,
                    'top_video_views' => $dto->top_video_views,
                    'problem_solved' => $dto->problem_solved,
                    'target_audience' => $dto->target_audience,
                    'research_notes' => $dto->research_notes,
                    'primary_keyword' => $dto->primary_keyword,
                    'demand_score' => $scoreResult['demand_score'],
                    'content_score' => $scoreResult['content_score'],
                    'commission_score' => $scoreResult['commission_score'],
                    'competition_score' => $scoreResult['competition_score'],
                    'seo_score' => $scoreResult['seo_score'],
                    'total_score' => $scoreResult['total_score'],
                    'score_breakdown' => json_encode($scoreResult, JSON_UNESCAPED_UNICODE),
                    'status' => 'RESEARCHED', // Stops at RESEARCHED for Admin Review
                    'discovery_source' => $dto->discovery_source ?: $providerName,
                    'ai_analysis' => !empty($aiAnalysis) ? json_encode($aiAnalysis, JSON_UNESCAPED_UNICODE) : null,
                    'ai_confidence' => $aiConfidence,
                    'first_seen_at' => time(),
                    'last_seen_at' => time(),
                    'history' => json_encode(array(
                        array('action' => 'AUTO_DISCOVERED', 'job_id' => (int)$job['id'], 'provider' => $providerName, 'date' => date('Y-m-d H:i:s'), 'time' => time())
                    ), JSON_UNESCAPED_UNICODE),
                    'date_created' => time(),
                    'date_updated' => time()
                );

                $newCandidateId = $this->d->insert('product_research', $candidateRecord);

                if ($newCandidateId) {
                    $candidatesCreated++;

                    // 8. Save Evidence records
                    if (!empty($dto->evidence) && is_array($dto->evidence)) {
                        foreach ($dto->evidence as $ev) {
                            $this->d->insert('product_research_evidence', array(
                                'id_research' => $newCandidateId,
                                'id_job' => (int)$job['id'],
                                'provider' => $providerName,
                                'source_url' => $ev['source_url'] ?? $dto->source_url,
                                'evidence_type' => $ev['evidence_type'] ?? 'FACT',
                                'field_name' => $ev['field_name'] ?? 'unknown',
                                'field_value' => is_array($ev['field_value'] ?? null) ? json_encode($ev['field_value'], JSON_UNESCAPED_UNICODE) : (string)($ev['field_value'] ?? ''),
                                'captured_at' => time()
                            ));
                        }
                    }

                    // 9. Save Initial Snapshot
                    $this->d->insert('product_research_snapshot', array(
                        'id_research' => $newCandidateId,
                        'id_job' => (int)$job['id'],
                        'price' => $dto->price,
                        'sales_count' => $dto->sales_count,
                        'rating' => $dto->rating,
                        'review_count' => $dto->review_count,
                        'commission_rate' => $dto->commission_rate,
                        'creator_count' => $dto->creator_count,
                        'video_count' => $dto->video_count,
                        'top_video_views' => $dto->top_video_views,
                        'captured_at' => time()
                    ));
                }
            }

            // 10. Update Seed last_run & next_run
            if (!empty($seed)) {
                $frequency = $seed['frequency'] ?? 'manual';
                $nextRun = 0;
                if ($frequency === 'daily') $nextRun = time() + 86400;
                elseif ($frequency === 'weekly') $nextRun = time() + (86400 * 7);

                $this->d->rawQuery(
                    "update #_product_research_seed set last_run = ?, next_run = ?, date_updated = ? where id = ?",
                    array(time(), $nextRun, time(), $seed['id'])
                );
            }

            // 11. Mark Job SUCCESS
            $duration = round(microtime(true) - $startTime, 2);
            $summary = "Tìm thấy {$candidatesFound} sản phẩm | Thêm mới: {$candidatesCreated} | Trùng lặp: {$duplicatesCount}";

            $this->d->rawQuery(
                "update #_product_research_job set status = 'SUCCESS', candidates_found = ?, candidates_created = ?, duplicates_count = ?, result_summary = ?, finished_at = ?, duration = ?, date_updated = ? where id = ?",
                array($candidatesFound, $candidatesCreated, $duplicatesCount, $summary, time(), $duration, time(), $job['id'])
            );

            return array(
                'status' => true,
                'job_id' => $job['id'],
                'candidates_found' => $candidatesFound,
                'candidates_created' => $candidatesCreated,
                'duplicates_count' => $duplicatesCount,
                'duration' => $duration
            );

        } catch (Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);
            $errorMessage = $e->getMessage();

            $status = ($job['attempts'] + 1 >= $job['max_attempts']) ? 'FAILED' : 'RETRY';

            $this->d->rawQuery(
                "update #_product_research_job set status = ?, error_message = ?, finished_at = ?, duration = ?, date_updated = ? where id = ?",
                array($status, $errorMessage, time(), $duration, time(), $job['id'])
            );

            return array(
                'status' => false,
                'job_id' => $job['id'],
                'error' => $errorMessage
            );
        }
    }
}
