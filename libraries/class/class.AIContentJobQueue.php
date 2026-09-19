<?php
/**
 * FITNADO - AI Content Background Job Queue (Phase 05)
 * PHP 7.4 Compatible
 */

if (!class_exists('AIContentEngine')) {
    require_once __DIR__ . '/class.AIContentEngine.php';
}

class AIContentJobQueue
{
    private $d;
    private $func;
    private $contentEngine;

    public function __construct($d = null, $func = null)
    {
        $this->d = $d;
        $this->func = $func;
        $this->contentEngine = new AIContentEngine($d, $func);
    }

    /**
     * Create a new Content Generation Job
     */
    public function createJob($productId, array $contentTypes = array(), array $options = array(), $batchId = null)
    {
        if (!$this->d) return false;

        $typesStr = !empty($contentTypes) ? implode(',', $contentTypes) : 'product_analysis,tiktok_hooks,tiktok_script,seo_content,review_draft,faq';

        $jobData = array(
            'id_product' => (int)$productId,
            'batch_id' => $batchId ?: ('BATCH_' . date('Ymd_His') . '_' . substr(md5(uniqid()), 0, 6)),
            'content_types' => $typesStr,
            'language' => $options['language'] ?? 'vi',
            'tone' => $options['tone'] ?? 'FITNADO_DEFAULT',
            'target_duration' => isset($options['target_duration']) ? (int)$options['target_duration'] : 30,
            'content_angle' => $options['content_angle'] ?? 'Problem/Solution',
            'priority' => isset($options['priority']) ? (int)$options['priority'] : 10,
            'status' => 'PENDING',
            'attempts' => 0,
            'max_attempts' => 3,
            'date_created' => time(),
            'date_updated' => time()
        );

        return $this->d->insert('ai_content_job', $jobData);
    }

    /**
     * Create Batch Jobs for multiple products
     */
    public function createBatchJobs(array $productIds, array $contentTypes = array(), array $options = array())
    {
        $batchId = 'BATCH_' . date('Ymd_His') . '_' . substr(md5(uniqid()), 0, 6);
        $jobIds = array();

        foreach ($productIds as $pid) {
            $jid = $this->createJob($pid, $contentTypes, $options, $batchId);
            if ($jid) {
                $jobIds[] = $jid;
            }
        }

        return array(
            'batch_id' => $batchId,
            'job_ids' => $jobIds,
            'total_dispatched' => count($jobIds)
        );
    }

    /**
     * Acquire next pending job with concurrency lock
     */
    public function getNextPendingJob()
    {
        if (!$this->d) return null;

        $pending = $this->d->rawQueryOne(
            "select * from #_ai_content_job where status = 'PENDING' and attempts < max_attempts order by priority desc, id asc limit 0,1"
        );

        if (empty($pending)) return null;

        // Atomic lock
        $locked = $this->d->rawQuery(
            "update #_ai_content_job set status = 'RUNNING', started_at = ?, attempts = attempts + 1, date_updated = ? where id = ? and status = 'PENDING'",
            array(time(), time(), $pending['id'])
        );

        return $this->d->rawQueryOne("select * from #_ai_content_job where id = ? limit 0,1", array($pending['id']));
    }

    /**
     * Execute a specific Content Job
     */
    public function executeJob($jobId)
    {
        if (!$this->d) return array('status' => false, 'error' => 'Database connection missing');

        $job = $this->d->rawQueryOne("select * from #_ai_content_job where id = ? limit 0,1", array((int)$jobId));
        if (empty($job)) {
            return array('status' => false, 'error' => 'Job not found: #' . $jobId);
        }

        $startTime = microtime(true);
        $productId = (int)$job['id_product'];
        $rawTypes = explode(',', $job['content_types']);
        $types = array_filter(array_map('trim', $rawTypes));

        $results = array();
        $hasError = false;
        $errorMsgs = array();

        $options = array(
            'language' => $job['language'] ?: 'vi',
            'tone' => $job['tone'] ?: 'FITNADO_DEFAULT',
            'target_duration' => (int)($job['target_duration'] ?: 30),
            'content_angle' => $job['content_angle'] ?: 'Problem/Solution'
        );

        foreach ($types as $cType) {
            if (empty($cType)) continue;

            $genRes = $this->contentEngine->generateContent($productId, $cType, $options);
            if ($genRes['status']) {
                $results[$cType] = array(
                    'content_id' => $genRes['content_id'],
                    'version' => $genRes['version'],
                    'status' => 'SUCCESS'
                );
            } else {
                $hasError = true;
                $err = "Type [{$cType}]: " . ($genRes['error'] ?? 'Unknown error');
                $errorMsgs[] = $err;
                $results[$cType] = array('status' => 'FAILED', 'error' => $err);
            }
        }

        $duration = max(0.001, round(microtime(true) - $startTime, 3));
        $finalStatus = (!$hasError || count($results) > 0) ? 'SUCCESS' : 'FAILED';
        $errMsg = !empty($errorMsgs) ? implode('; ', $errorMsgs) : null;

        $this->d->rawQuery(
            "update #_ai_content_job set status = ?, completed_at = ?, duration = ?, error_message = ?, results_summary = ?, date_updated = ? where id = ?",
            array(
                $finalStatus,
                time(),
                $duration,
                $errMsg,
                json_encode($results, JSON_UNESCAPED_UNICODE),
                time(),
                (int)$jobId
            )
        );

        return array(
            'status' => ($finalStatus === 'SUCCESS'),
            'job_id' => (int)$jobId,
            'duration' => $duration,
            'generated_count' => count($results),
            'results' => $results,
            'error' => $errMsg
        );
    }

    /**
     * Recover jobs stuck in RUNNING for longer than timeout
     */
    public function recoverStaleJobs($timeoutSeconds = 600)
    {
        if (!$this->d) return 0;
        $threshold = time() - $timeoutSeconds;

        return $this->d->rawQuery(
            "update #_ai_content_job set status = 'PENDING', date_updated = ? where status = 'RUNNING' and started_at < ?",
            array(time(), $threshold)
        );
    }

    /**
     * Retry a failed job
     */
    public function retryJob($jobId)
    {
        if (!$this->d) return false;

        return $this->d->rawQuery(
            "update #_ai_content_job set status = 'PENDING', attempts = 0, error_message = null, date_updated = ? where id = ?",
            array(time(), (int)$jobId)
        );
    }
}
