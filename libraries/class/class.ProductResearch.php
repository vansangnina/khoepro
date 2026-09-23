<?php
/**
 * FITNADO - Product Research Engine & Helper (Phase 03)
 * PHP 7.4 Compatible
 */
class ProductResearch
{
    private $d;
    private $func;
    private $defaultWeights = array(
        'demand' => 30,
        'content' => 25,
        'commission' => 20,
        'competition' => 15,
        'seo' => 10
    );

    public function __construct($d = null, $func = null)
    {
        $this->d = $d;
        $this->func = $func;
    }

    /**
     * Get active scoring weights
     */
    public function getWeights()
    {
        if ($this->d) {
            $setting = $this->d->rawQueryOne("select options from #_setting limit 0,1");
            if (!empty($setting['options'])) {
                $opts = json_decode($setting['options'], true);
                if (!empty($opts['product_research_weights']) && is_array($opts['product_research_weights'])) {
                    $saved = $opts['product_research_weights'];
                    $sum = (float)($saved['demand'] ?? 0) + (float)($saved['content'] ?? 0) + (float)($saved['commission'] ?? 0) + (float)($saved['competition'] ?? 0) + (float)($saved['seo'] ?? 0);
                    if (abs($sum - 100) < 0.01) {
                        return array(
                            'demand' => (float)$saved['demand'],
                            'content' => (float)$saved['content'],
                            'commission' => (float)$saved['commission'],
                            'competition' => (float)$saved['competition'],
                            'seo' => (float)$saved['seo']
                        );
                    }
                }
            }
        }
        return $this->defaultWeights;
    }

    /**
     * Save scoring weights
     */
    public function setWeights($weights)
    {
        $demand = isset($weights['demand']) ? (float)$weights['demand'] : 0;
        $content = isset($weights['content']) ? (float)$weights['content'] : 0;
        $commission = isset($weights['commission']) ? (float)$weights['commission'] : 0;
        $competition = isset($weights['competition']) ? (float)$weights['competition'] : 0;
        $seo = isset($weights['seo']) ? (float)$weights['seo'] : 0;

        $total = $demand + $content + $commission + $competition + $seo;
        if (abs($total - 100) > 0.01) {
            return array('status' => false, 'message' => 'Tổng trọng số phải bằng 100% (Hiện tại: ' . $total . '%)');
        }

        if ($this->d) {
            $setting = $this->d->rawQueryOne("select id, options from #_setting limit 0,1");
            $opts = (!empty($setting['options'])) ? json_decode($setting['options'], true) : array();
            if (!is_array($opts)) $opts = array();

            $opts['product_research_weights'] = array(
                'demand' => $demand,
                'content' => $content,
                'commission' => $commission,
                'competition' => $competition,
                'seo' => $seo
            );

            $this->d->rawQuery("update #_setting set options = ? where id = ?", array(json_encode($opts, JSON_UNESCAPED_UNICODE), $setting['id'] ?? 1));
            return array('status' => true, 'message' => 'Cập nhật trọng số thành công');
        }

        return array('status' => false, 'message' => 'Không thể kết nối CSDL');
    }

    /**
     * Calculate Demand Score (0 - 100)
     * Factors: sales_count, rating, review_count, estimated_gmv
     * Rule: NULL != 0 (Missing fields are not penalized)
     */
    public function calculateDemandScore($data)
    {
        $components = array();
        $weights = array();

        // 1. Sales Volume
        if (isset($data['sales_count']) && $data['sales_count'] !== null && $data['sales_count'] !== '') {
            $sales = (int)$data['sales_count'];
            if ($sales >= 10000) $score = 100;
            elseif ($sales >= 5000) $score = 90;
            elseif ($sales >= 2000) $score = 80;
            elseif ($sales >= 1000) $score = 70;
            elseif ($sales >= 500) $score = 60;
            elseif ($sales >= 100) $score = 45;
            elseif ($sales > 0) $score = 30;
            else $score = 10;
            $components['sales'] = $score;
            $weights['sales'] = 40;
        }

        // 2. Rating
        if (isset($data['rating']) && $data['rating'] !== null && $data['rating'] !== '') {
            $rating = (float)$data['rating'];
            if ($rating >= 4.9) $score = 100;
            elseif ($rating >= 4.7) $score = 90;
            elseif ($rating >= 4.5) $score = 80;
            elseif ($rating >= 4.2) $score = 65;
            elseif ($rating >= 4.0) $score = 50;
            elseif ($rating >= 3.5) $score = 30;
            else $score = 10;
            $components['rating'] = $score;
            $weights['rating'] = 30;
        }

        // 3. Review Count
        if (isset($data['review_count']) && $data['review_count'] !== null && $data['review_count'] !== '') {
            $reviews = (int)$data['review_count'];
            if ($reviews >= 2000) $score = 100;
            elseif ($reviews >= 1000) $score = 90;
            elseif ($reviews >= 500) $score = 80;
            elseif ($reviews >= 200) $score = 70;
            elseif ($reviews >= 50) $score = 55;
            elseif ($reviews > 0) $score = 35;
            else $score = 10;
            $components['reviews'] = $score;
            $weights['reviews'] = 20;
        }

        // 4. Estimated GMV
        if (isset($data['estimated_gmv']) && $data['estimated_gmv'] !== null && $data['estimated_gmv'] !== '') {
            $gmv = (float)$data['estimated_gmv'];
            if ($gmv >= 500000000) $score = 100; // >= 500M VND
            elseif ($gmv >= 200000000) $score = 85;
            elseif ($gmv >= 50000000) $score = 70;
            elseif ($gmv >= 10000000) $score = 50;
            else $score = 30;
            $components['gmv'] = $score;
            $weights['gmv'] = 10;
        }

        if (empty($components)) {
            return null; // All missing -> NULL
        }

        $totalWeight = array_sum($weights);
        $weightedSum = 0;
        foreach ($components as $key => $val) {
            $weightedSum += $val * ($weights[$key] / $totalWeight);
        }

        return round($weightedSum, 1);
    }

    /**
     * Calculate Content Potential Score (0 - 100)
     * Factors: top_video_views, video_count, creator_count, problem_solved, target_audience
     */
    public function calculateContentScore($data)
    {
        $components = array();
        $weights = array();

        // 1. Top Video Views
        if (isset($data['top_video_views']) && $data['top_video_views'] !== null && $data['top_video_views'] !== '') {
            $views = (int)$data['top_video_views'];
            if ($views >= 2000000) $score = 100;
            elseif ($views >= 1000000) $score = 90;
            elseif ($views >= 500000) $score = 80;
            elseif ($views >= 100000) $score = 65;
            elseif ($views >= 20000) $score = 50;
            elseif ($views > 0) $score = 30;
            else $score = 10;
            $components['video_views'] = $score;
            $weights['video_views'] = 35;
        }

        // 2. Creator Engagement
        if (isset($data['creator_count']) && $data['creator_count'] !== null && $data['creator_count'] !== '') {
            $creators = (int)$data['creator_count'];
            if ($creators >= 50) $score = 100;
            elseif ($creators >= 20) $score = 85;
            elseif ($creators >= 10) $score = 70;
            elseif ($creators >= 3) $score = 50;
            elseif ($creators > 0) $score = 30;
            else $score = 10;
            $components['creators'] = $score;
            $weights['creators'] = 25;
        }

        // 3. Problem Solved Clarity (Pain Point)
        if (isset($data['problem_solved']) && $data['problem_solved'] !== null && trim($data['problem_solved']) !== '') {
            $len = mb_strlen(trim($data['problem_solved']), 'UTF-8');
            if ($len >= 80) $score = 90;
            elseif ($len >= 30) $score = 75;
            else $score = 50;
            $components['problem'] = $score;
            $weights['problem'] = 25;
        }

        // 4. Target Audience Clarity
        if (isset($data['target_audience']) && $data['target_audience'] !== null && trim($data['target_audience']) !== '') {
            $len = mb_strlen(trim($data['target_audience']), 'UTF-8');
            if ($len >= 40) $score = 90;
            elseif ($len >= 15) $score = 75;
            else $score = 50;
            $components['audience'] = $score;
            $weights['audience'] = 15;
        }

        if (empty($components)) {
            return null;
        }

        $totalWeight = array_sum($weights);
        $weightedSum = 0;
        foreach ($components as $key => $val) {
            $weightedSum += $val * ($weights[$key] / $totalWeight);
        }

        return round($weightedSum, 1);
    }

    /**
     * Calculate Commission Score (0 - 100)
     * Factors: commission_rate, commission_value, price
     */
    public function calculateCommissionScore($data)
    {
        $components = array();
        $weights = array();

        // 1. Commission Rate (%)
        if (isset($data['commission_rate']) && $data['commission_rate'] !== null && $data['commission_rate'] !== '') {
            $rate = (float)$data['commission_rate'];
            if ($rate >= 20) $score = 100;
            elseif ($rate >= 15) $score = 90;
            elseif ($rate >= 10) $score = 80;
            elseif ($rate >= 7) $score = 65;
            elseif ($rate >= 4) $score = 50;
            elseif ($rate > 0) $score = 30;
            else $score = 10;
            $components['rate'] = $score;
            $weights['rate'] = 60;
        }

        // 2. Commission Value (VND per order)
        if (isset($data['commission_value']) && $data['commission_value'] !== null && $data['commission_value'] !== '') {
            $val = (float)$data['commission_value'];
            if ($val >= 200000) $score = 100; // >= 200k VND
            elseif ($val >= 100000) $score = 85;
            elseif ($val >= 50000) $score = 70;
            elseif ($val >= 20000) $score = 50;
            elseif ($val > 0) $score = 30;
            else $score = 10;
            $components['value'] = $score;
            $weights['value'] = 40;
        } elseif (isset($data['price']) && isset($data['commission_rate']) && $data['price'] !== null && $data['commission_rate'] !== null) {
            $val = (float)$data['price'] * ((float)$data['commission_rate'] / 100);
            if ($val >= 200000) $score = 100;
            elseif ($val >= 100000) $score = 85;
            elseif ($val >= 50000) $score = 70;
            elseif ($val >= 20000) $score = 50;
            elseif ($val > 0) $score = 30;
            else $score = 10;
            $components['value'] = $score;
            $weights['value'] = 40;
        }

        if (empty($components)) {
            return null;
        }

        $totalWeight = array_sum($weights);
        $weightedSum = 0;
        foreach ($components as $key => $val) {
            $weightedSum += $val * ($weights[$key] / $totalWeight);
        }

        return round($weightedSum, 1);
    }

    /**
     * Calculate Competition Opportunity Score (0 - 100)
     * High score = Great opportunity / Low market saturation
     */
    public function calculateCompetitionScore($data)
    {
        if (isset($data['competition_score']) && $data['competition_score'] !== null && $data['competition_score'] !== '') {
            return min(100, max(0, (float)$data['competition_score']));
        }

        // Infer opportunity based on creator count / sales ratio if available
        if (isset($data['sales_count']) && isset($data['creator_count']) && $data['sales_count'] > 500 && $data['creator_count'] !== null) {
            if ($data['creator_count'] <= 5) return 85.0; // High sales, low competition
            if ($data['creator_count'] <= 15) return 70.0;
            if ($data['creator_count'] <= 35) return 55.0;
            return 40.0; // Saturated
        }

        return null;
    }

    /**
     * Calculate SEO Score (0 - 100)
     */
    public function calculateSeoScore($data)
    {
        if (isset($data['seo_score']) && $data['seo_score'] !== null && $data['seo_score'] !== '') {
            return min(100, max(0, (float)$data['seo_score']));
        }

        // Basic estimation from primary keyword presence
        if (!empty($data['primary_keyword'])) {
            $kw = trim($data['primary_keyword']);
            $words = explode(' ', $kw);
            if (count($words) >= 3) return 80.0; // Long-tail keyword = High conversion potential
            if (count($words) >= 2) return 70.0;
            return 55.0;
        }

        return null;
    }

    /**
     * Calculate Complete Weighted Score & Explanations
     */
    public function calculateTotalScore($data, $customWeights = null)
    {
        $weights = $customWeights ?: $this->getWeights();

        $demand = $this->calculateDemandScore($data);
        $content = $this->calculateContentScore($data);
        $commission = $this->calculateCommissionScore($data);
        $competition = $this->calculateCompetitionScore($data);
        $seo = $this->calculateSeoScore($data);

        $scores = array(
            'demand' => $demand,
            'content' => $content,
            'commission' => $commission,
            'competition' => $competition,
            'seo' => $seo
        );

        $reasons = array();
        $activeWeights = array();
        $weightedTotal = 0;

        foreach ($scores as $dim => $score) {
            if ($score !== null) {
                $w = $weights[$dim] ?? 0;
                $activeWeights[$dim] = $w;
                $weightedTotal += $score * $w;

                // Build human-readable reasons
                switch ($dim) {
                    case 'demand':
                        $reasons[] = "Nhu cầu thị trường đạt {$score}/100" . (!empty($data['sales_count']) ? " ({$data['sales_count']} lượt bán)" : "");
                        break;
                    case 'content':
                        $reasons[] = "Tiềm năng sáng tạo nội dung đạt {$score}/100" . (!empty($data['top_video_views']) ? " (Top video: " . number_format($data['top_video_views']) . " views)" : "");
                        break;
                    case 'commission':
                        $reasons[] = "Tiềm năng hoa hồng đạt {$score}/100" . (!empty($data['commission_rate']) ? " ({$data['commission_rate']}%)" : "");
                        break;
                    case 'competition':
                        $reasons[] = "Cơ hội cạnh tranh đạt {$score}/100";
                        break;
                    case 'seo':
                        $reasons[] = "Cơ hội SEO đạt {$score}/100" . (!empty($data['primary_keyword']) ? " (Từ khóa: {$data['primary_keyword']})" : "");
                        break;
                }
            } else {
                $reasons[] = "Chiều " . strtoupper($dim) . " chưa đủ dữ liệu (NULL)";
            }
        }

        $sumActiveWeights = array_sum($activeWeights);
        $finalTotal = ($sumActiveWeights > 0) ? round($weightedTotal / $sumActiveWeights, 1) : null;

        return array(
            'demand_score' => $demand,
            'content_score' => $content,
            'commission_score' => $commission,
            'competition_score' => $competition,
            'seo_score' => $seo,
            'total_score' => $finalTotal,
            'breakdown' => $scores,
            'reasons' => $reasons,
            'weights_used' => $weights
        );
    }

    /**
     * Calculate Platform-Specific Potential Scores (TikTok, Facebook, YouTube)
     * Dynamic weight formulation based on platform strengths.
     * @param array $data Product and research attributes
     * @return array ['global_score' => float, 'tiktok_score' => float, 'facebook_score' => float, 'youtube_score' => float, 'breakdown' => array, 'reasons' => array]
     */
    public function calculatePlatformScores($data)
    {
        $baseTotal = $this->calculateTotalScore($data);
        $globalScore = (float)($baseTotal['total_score'] ?? 50.0);
        $demand = (float)($baseTotal['demand_score'] ?? 50.0);
        $content = (float)($baseTotal['content_score'] ?? 50.0);
        $commission = (float)($baseTotal['commission_score'] ?? 50.0);
        $competition = (float)($baseTotal['competition_score'] ?? 50.0);
        $seo = (float)($baseTotal['seo_score'] ?? 50.0);

        $price = (float)($data['price'] ?? ($data['sale_price'] ?? 0));
        $views = (int)($data['top_video_views'] ?? 0);
        $hasPainPoint = !empty(trim($data['problem_solved'] ?? ''));

        // 1. TikTok Potential Score:
        // Prioritizes: High Content potential (40%), Demand/Views (30%), Commission (20%), Affordable Impulse Buying price <= 450k (10%)
        $tiktokPriceBonus = ($price > 0 && $price <= 450000) ? 10.0 : (($price <= 800000) ? 5.0 : 0.0);
        $tiktokViralBonus = ($views >= 100000) ? 10.0 : ($views >= 20000 ? 5.0 : 0.0);
        $tiktokScore = round(($content * 0.40) + ($demand * 0.30) + ($commission * 0.20) + ($competition * 0.10) + ($tiktokPriceBonus * 0.5) + ($tiktokViralBonus * 0.5), 1);
        $tiktokScore = min(100.0, max(10.0, $tiktokScore));

        // 2. Facebook Reels / Fanpage Potential Score:
        // Prioritizes: Demand & Social Proof/Rating (35%), Commission Value (30%), Problem/Solution Clarity (25%), Mid-range price 200k-1.5M (10%)
        $fbPriceBonus = ($price >= 200000 && $price <= 1500000) ? 10.0 : 5.0;
        $fbClarityBonus = $hasPainPoint ? 8.0 : 0.0;
        $fbScore = round(($demand * 0.35) + ($commission * 0.30) + ($content * 0.25) + ($seo * 0.10) + ($fbPriceBonus * 0.5) + ($fbClarityBonus * 0.5), 1);
        $fbScore = min(100.0, max(10.0, $fbScore));

        // 3. YouTube Shorts / Search Potential Score:
        // Prioritizes: SEO / Search intent (35%), Content In-depth potential (30%), Demand (20%), Competition Opportunity (15%)
        $ytSeoBonus = (!empty($data['primary_keyword']) && count(explode(' ', trim($data['primary_keyword']))) >= 2) ? 10.0 : 0.0;
        $ytScore = round(($seo * 0.35) + ($content * 0.30) + ($demand * 0.20) + ($competition * 0.15) + ($ytSeoBonus * 0.5), 1);
        $ytScore = min(100.0, max(10.0, $ytScore));

        $reasons = array(
            "TikTok: {$tiktokScore}/100 (Trọng tâm visual, nỗi đau nhanh và giá dễ chốt)",
            "Facebook: {$fbScore}/100 (Trọng tâm giải pháp rõ ràng, hoa hồng đơn cao)",
            "YouTube: {$ytScore}/100 (Trọng tâm từ khóa tìm kiếm và đánh giá chuyên sâu)"
        );

        return array(
            'global_score' => $globalScore,
            'tiktok_score' => $tiktokScore,
            'facebook_score' => $fbScore,
            'youtube_score' => $ytScore,
            'breakdown' => array(
                'global' => $globalScore,
                'tiktok' => $tiktokScore,
                'facebook' => $fbScore,
                'youtube' => $ytScore
            ),
            'reasons' => $reasons
        );
    }

    /**
     * Strict Product Eligibility Gate for Content Automation
     * Verifies if product meets all criteria before entering Content Candidate Pool
     * @param array $productData
     * @param string $platform
     * @param int $cooldownDays
     * @return array ['eligible' => bool, 'status' => string, 'reasons' => array(), 'cooldown_until' => int|null]
     */
    public function checkEligibilityForContent(array $productData, $platform = 'all', $cooldownDays = 14)
    {
        $reasons = array();
        $isEligible = true;

        $name = trim($productData['namevi'] ?? ($productData['name'] ?? ($productData['title'] ?? '')));
        $price = (float)($productData['sale_price'] ?? ($productData['price'] ?? ($productData['regular_price'] ?? 0)));
        $affUrl = trim($productData['affiliate_url'] ?? ($productData['aff_url'] ?? ($productData['url'] ?? '')));
        $photo = trim($productData['photo'] ?? ($productData['image_url'] ?? ($productData['image'] ?? '')));
        $status = (string)($productData['status'] ?? '');
        $productId = (int)($productData['id'] ?? ($productData['id_product'] ?? 0));

        // 1. Mandatory Data Completeness
        if (empty($name)) {
            $isEligible = false;
            $reasons[] = 'Thiếu tên sản phẩm';
        }
        if ($price <= 0) {
            $isEligible = false;
            $reasons[] = 'Giá sản phẩm không hợp lệ (<= 0)';
        }
        if (empty($affUrl)) {
            $isEligible = false;
            $reasons[] = 'Chưa có đường dẫn Affiliate (URL)';
        }
        if (empty($photo)) {
            $isEligible = false;
            $reasons[] = 'Thiếu hình ảnh sản phẩm';
        }

        // 2. Active Status Check
        if (!empty($status) && strpos($status, 'hienthi') === false && $status !== 'APPROVED' && $status !== 'ACTIVE' && $status !== 'active') {
            $isEligible = false;
            $reasons[] = 'Trạng thái sản phẩm không hoạt động (' . $status . ')';
        }

        // 3. Cooldown Verification (Anti-Spam)
        $cooldownUntil = null;
        if ($this->d && $productId > 0) {
            $lastPost = $this->d->rawQueryOne(
                "SELECT date_created, published_at FROM table_publish_post WHERE id_product = ? AND status IN ('PUBLISHED', 'SCHEDULED', 'QUEUED') ORDER BY id DESC LIMIT 1",
                array($productId)
            );
            if (!empty($lastPost)) {
                $lastTime = !empty($lastPost['published_at']) ? (int)$lastPost['published_at'] : (int)$lastPost['date_created'];
                $cooldownSeconds = $cooldownDays * 86400;
                if ((time() - $lastTime) < $cooldownSeconds) {
                    $isEligible = false;
                    $cooldownUntil = $lastTime + $cooldownSeconds;
                    $reasons[] = "Sản phẩm đang trong thời gian giãn cách đăng bài (Cooldown đến " . date('d/m/Y H:i', $cooldownUntil) . ")";
                }
            }
        }

        return array(
            'eligible' => $isEligible,
            'status' => $isEligible ? 'ELIGIBLE' : ($cooldownUntil ? 'COOLDOWN' : 'INELIGIBLE'),
            'reasons' => $reasons,
            'cooldown_until' => $cooldownUntil
        );
    }

    /**
     * Normalize Product URL
     * Strips UTM tags, affiliate tracking, session IDs, and normalizes format
     */
    public function normalizeUrl($url)
    {
        if (empty($url)) return '';
        $url = trim($url);

        $parsed = parse_url($url);
        if (!$parsed || empty($parsed['host'])) return $url;

        $scheme = isset($parsed['scheme']) ? strtolower($parsed['scheme']) : 'https';
        $host = strtolower($parsed['host']);
        // Remove www.
        if (substr($host, 0, 4) === 'www.') $host = substr($host, 4);

        $path = isset($parsed['path']) ? rtrim($parsed['path'], '/') : '';

        // Query parameters cleaning
        $cleanedQuery = '';
        if (!empty($parsed['query'])) {
            parse_str($parsed['query'], $queryParams);
            $ignoreKeys = array(
                'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
                'spm', 'aff_trace_key', 'aff_platform', 'fbclid', 'gclid', 'ref',
                'tag', 'tracking_id', 'click_id', 'share_token', 'tt_medium',
                'sec_user_id', 'source', 'ug_source', 'is_copy_url'
            );

            foreach ($ignoreKeys as $ik) {
                unset($queryParams[$ik]);
            }

            // Remove any param starting with utm_ or aff_
            foreach (array_keys($queryParams) as $k) {
                if (strpos($k, 'utm_') === 0 || strpos($k, 'aff_') === 0) {
                    unset($queryParams[$k]);
                }
            }

            if (!empty($queryParams)) {
                ksort($queryParams);
                $cleanedQuery = '?' . http_build_query($queryParams);
            }
        }

        return $scheme . '://' . $host . $path . $cleanedQuery;
    }

    /**
     * Normalize Product Name
     */
    public function normalizeName($name)
    {
        if (empty($name)) return '';
        $str = mb_strtolower(trim($name), 'UTF-8');
        // Remove Vietnamese diacritics
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ'
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        // Remove non-alphanumeric chars except spaces
        $str = preg_replace('/[^a-z0-9\s]/', ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);
        return trim($str);
    }

    /**
     * Check for Duplicate Candidates or Products
     */
    public function checkDuplicate($platform, $externalId, $sourceUrl, $name, $brand = '', $excludeId = 0)
    {
        if (!$this->d) {
            return array('is_duplicate' => false, 'type' => 'NONE', 'message' => '');
        }

        $excludeSql = $excludeId ? " and id != " . (int)$excludeId : "";

        // 1. Exact Match: Platform + External Product ID
        if (!empty($platform) && !empty($externalId)) {
            $match = $this->d->rawQueryOne(
                "select id, name, status, id_product from #_product_research where platform = ? and external_product_id = ? $excludeSql limit 0,1",
                array($platform, trim($externalId))
            );
            if (!empty($match)) {
                return array(
                    'is_duplicate' => true,
                    'severity' => 'danger',
                    'type' => 'EXACT_EXTERNAL_ID',
                    'message' => "Trùng mã ID ngoài '{$externalId}' trên nền tảng '{$platform}' (Ứng viên ID: #{$match['id']} - {$match['name']})",
                    'matched_id' => $match['id'],
                    'matched_name' => $match['name'],
                    'status' => $match['status'],
                    'id_product' => $match['id_product']
                );
            }
        }

        // 2. Exact Match: Normalized URL
        $normUrl = $this->normalizeUrl($sourceUrl);
        if (!empty($normUrl)) {
            $match = $this->d->rawQueryOne(
                "select id, name, status, id_product from #_product_research where normalized_url = ? $excludeSql limit 0,1",
                array($normUrl)
            );
            if (!empty($match)) {
                return array(
                    'is_duplicate' => true,
                    'severity' => 'warning',
                    'type' => 'EXACT_URL',
                    'message' => "Trùng liên kết nguồn sau khi chuẩn hóa (Ứng viên ID: #{$match['id']} - {$match['name']})",
                    'matched_id' => $match['id'],
                    'matched_name' => $match['name'],
                    'status' => $match['status'],
                    'id_product' => $match['id_product']
                );
            }
        }

        // 3. Similar Name Warning
        $normName = $this->normalizeName($name);
        if (!empty($normName) && strlen($normName) >= 6) {
            $match = $this->d->rawQueryOne(
                "select id, name, status, id_product from #_product_research where normalized_name = ? $excludeSql limit 0,1",
                array($normName)
            );
            if (!empty($match)) {
                return array(
                    'is_duplicate' => true,
                    'severity' => 'info',
                    'type' => 'SIMILAR_NAME',
                    'message' => "Tên sản phẩm trùng khớp với ứng viên ID: #{$match['id']} ({$match['name']})",
                    'matched_id' => $match['id'],
                    'matched_name' => $match['name'],
                    'status' => $match['status'],
                    'id_product' => $match['id_product']
                );
            }

            // Also check existing live products in table_product
            $matchProd = $this->d->rawQueryOne(
                "select id, namevi, status from #_product where namevi LIKE ? limit 0,1",
                array('%' . trim($name) . '%')
            );
            if (!empty($matchProd)) {
                return array(
                    'is_duplicate' => true,
                    'severity' => 'info',
                    'type' => 'EXISTING_PRODUCT',
                    'message' => "Tên sản phẩm tương đồng với sản phẩm đã có trên website (Product ID: #{$matchProd['id']} - {$matchProd['namevi']})",
                    'matched_id' => $matchProd['id'],
                    'matched_name' => $matchProd['namevi'],
                    'status' => $matchProd['status'],
                    'id_product' => $matchProd['id']
                );
            }
        }

        return array('is_duplicate' => false, 'type' => 'NONE', 'message' => '');
    }

    /**
     * Map & Create Draft Product in table_product from Approved Candidate
     */
    public function createProductFromCandidate($candidateId, $mappingData = array())
    {
        if (!$this->d) return array('status' => false, 'message' => 'Lỗi kết nối CSDL');

        $candidate = $this->d->rawQueryOne("select * from #_product_research where id = ? limit 0,1", array((int)$candidateId));
        if (empty($candidate)) {
            return array('status' => false, 'message' => 'Không tìm thấy ứng viên nghiên cứu');
        }

        if (!empty($candidate['id_product'])) {
            return array('status' => false, 'message' => 'Ứng viên này đã được tạo sản phẩm (Product ID: #' . $candidate['id_product'] . ')');
        }

        if ($candidate['status'] !== 'APPROVED') {
            return array('status' => false, 'message' => 'Ứng viên phải ở trạng thái APPROVED mới được tạo sản phẩm');
        }

        // Prepare product data
        $name = !empty($mappingData['namevi']) ? trim($mappingData['namevi']) : $candidate['name'];
        $slug = !empty($mappingData['slugvi']) ? trim($mappingData['slugvi']) : ($this->func ? $this->func->changeTitle($name) : strtolower(str_replace(' ', '-', $name)));

        // Ensure slug uniqueness in table_product
        $slugCheck = $this->d->rawQueryOne("select id from #_product where slugvi = ? limit 0,1", array($slug));
        if (!empty($slugCheck)) {
            $slug .= '-' . time();
        }

        $idList = !empty($mappingData['id_list']) ? (int)$mappingData['id_list'] : 0;
        $idCat = !empty($mappingData['id_cat']) ? (int)$mappingData['id_cat'] : 0;
        $idBrand = !empty($mappingData['id_brand']) ? (int)$mappingData['id_brand'] : 0;
        $regularPrice = isset($mappingData['regular_price']) ? (float)$mappingData['regular_price'] : (float)($candidate['price'] ?? 0);
        $salePrice = isset($mappingData['sale_price']) ? (float)$mappingData['sale_price'] : 0;

        $desc = !empty($mappingData['descvi']) ? $mappingData['descvi'] : $candidate['problem_solved'];
        $specs = !empty($mappingData['specs']) ? $mappingData['specs'] : $candidate['research_notes'];

        $productData = array(
            'namevi' => $name,
            'nameen' => $name,
            'slugvi' => $slug,
            'slugen' => $slug,
            'id_list' => $idList,
            'id_cat' => $idCat,
            'id_brand' => $idBrand,
            'regular_price' => $regularPrice,
            'sale_price' => $salePrice,
            'descvi' => $desc,
            'specs' => $specs,
            'best_for' => $candidate['target_audience'] ?? '',
            'review_score' => (float)($candidate['rating'] ?? 0),
            'review_count' => (int)($candidate['review_count'] ?? 0),
            'metaindex' => 'index,follow',
            'metaorder' => '',
            'discount' => 0,
            'numb' => 1,
            'view' => 0,
            'type' => 'san-pham',
            'status' => '', // NOT published by default (no 'hienthi')
            'review_type' => 'AI_ANALYSIS',
            'is_real_test' => 0,
            'date_created' => time(),
            'date_updated' => time()
        );

        $newProductId = $this->d->insert('product', $productData);

        if ($newProductId) {
            // Seed initial affiliate offer in table_product_affiliate if candidate has source URL or affiliate URL
            $affUrl = !empty($mappingData['affiliate_url']) ? trim($mappingData['affiliate_url']) : $candidate['source_url'];
            if (!empty($affUrl)) {
                $affData = array(
                    'id_product' => $newProductId,
                    'platform' => in_array($candidate['platform'], array('shopee', 'lazada', 'tiki', 'tiktok', 'brand')) ? $candidate['platform'] : 'other',
                    'seller_name' => $candidate['brand_hint'] ?: ucfirst($candidate['platform']),
                    'affiliate_url' => $affUrl,
                    'original_url' => $candidate['source_url'],
                    'price' => $regularPrice,
                    'original_price' => (float)($candidate['original_price'] ?? 0),
                    'commission_rate' => (float)($candidate['commission_rate'] ?? 0),
                    'commission_value' => (float)($candidate['commission_value'] ?? 0),
                    'is_best_deal' => 1,
                    'priority' => 10,
                    'status' => 'hienthi',
                    'date_created' => time(),
                    'date_updated' => time()
                );
                $this->d->insert('product_affiliate', $affData);
            }

            // Update candidate record
            $history = !empty($candidate['history']) ? json_decode($candidate['history'], true) : array();
            if (!is_array($history)) $history = array();
            $history[] = array(
                'action' => 'PRODUCT_CREATED',
                'id_product' => $newProductId,
                'time' => time(),
                'date' => date('Y-m-d H:i:s')
            );

            $this->d->rawQuery(
                "update #_product_research set id_product = ?, status = 'PRODUCT_CREATED', history = ?, date_updated = ? where id = ?",
                array($newProductId, json_encode($history, JSON_UNESCAPED_UNICODE), time(), $candidateId)
            );

            return array(
                'status' => true,
                'message' => "Tạo sản phẩm thành công (ID #{$newProductId})",
                'product_id' => $newProductId
            );
        }

        return array('status' => false, 'message' => 'Không thể thêm sản phẩm vào CSDL');
    }
}
