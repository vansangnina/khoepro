<?php
/**
 * FITNADO Analytics & Attribution Service
 * Phase 08: Analytics, Affiliate Attribution & Winner Detection
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

class AnalyticsService {
    private $d;
    private $func;
    private $settingsCache = null;

    // Supported Event Types
    const EVENT_PAGE_VIEW        = 'PAGE_VIEW';
    const EVENT_PRODUCT_VIEW     = 'PRODUCT_VIEW';
    const EVENT_AFFILIATE_CLICK  = 'AFFILIATE_CLICK';
    const EVENT_POST_VIEW        = 'POST_VIEW';
    const EVENT_VIDEO_VIEW       = 'VIDEO_VIEW';
    const EVENT_ENGAGEMENT       = 'ENGAGEMENT';
    const EVENT_ADD_TO_CART      = 'ADD_TO_CART';
    const EVENT_CONVERSION       = 'CONVERSION';
    const EVENT_REVENUE          = 'REVENUE';

    public function __construct($d = null, $func = null) {
        $this->d = $d;
        $this->func = $func;
    }

    /**
     * Get setting value with cache and default fallback
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting($key, $default = null) {
        if ($this->settingsCache === null && $this->d) {
            $this->settingsCache = array();
            $rows = $this->d->rawQuery("SELECT setting_key, setting_value FROM table_analytics_setting");
            if (!empty($rows)) {
                foreach ($rows as $r) {
                    $this->settingsCache[$r['setting_key']] = $r['setting_value'];
                }
            }
        }

        if (isset($this->settingsCache[$key])) {
            $val = $this->settingsCache[$key];
            if (is_numeric($val)) {
                return (strpos($val, '.') !== false) ? (float)$val : (int)$val;
            }
            return $val;
        }

        return $default;
    }

    /**
     * Save/Update setting value
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string $desc
     * @return bool
     */
    public function saveSetting($key, $value, $group = 'general', $desc = '') {
        if (!$this->d) return false;
        
        $strVal = is_array($value) ? json_encode($value) : (string)$value;
        $exists = $this->d->rawQueryOne("SELECT id FROM table_analytics_setting WHERE setting_key = ? LIMIT 1", array($key));
        
        $data = array(
            'setting_value' => $strVal,
            'date_updated' => time()
        );

        if (!empty($exists)) {
            $this->d->where('setting_key', $key);
            $res = $this->d->update('analytics_setting', $data);
        } else {
            $data['setting_key'] = $key;
            $data['setting_group'] = $group;
            $data['description'] = $desc;
            $res = $this->d->insert('analytics_setting', $data);
        }

        if ($this->settingsCache !== null) {
            $this->settingsCache[$key] = $strVal;
        }

        return (bool)$res;
    }

    /**
     * Check if client IP is internal/admin
     * @param string $ip
     * @return bool
     */
    public function isInternalIp($ip = null) {
        if ($ip === null) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        }

        $internalIpsRaw = $this->getSetting('internal_ips', '["127.0.0.1", "::1"]');
        $internalIps = is_array($internalIpsRaw) ? $internalIpsRaw : json_decode($internalIpsRaw, true);
        if (!is_array($internalIps)) {
            $internalIps = array('127.0.0.1', '::1');
        }

        return in_array($ip, $internalIps);
    }

    /**
     * Generate anonymous Session ID
     * @return string
     */
    public static function generateSessionId() {
        return 'sess_' . bin2hex(random_bytes(16));
    }

    /**
     * Generate Unique Tracking Code for a Post
     * Format: fp_<platform_prefix>_<hash8>
     * @param string $platform
     * @param int $idPost
     * @return string
     */
    public static function generateTrackingCode($platform = 'tiktok', $idPost = 0) {
        $prefix = strtolower(substr(preg_replace('/[^a-z]/', '', $platform) ?: 'post', 0, 4));
        $randHex = bin2hex(random_bytes(4));
        return 'fp_' . $prefix . '_' . ($idPost > 0 ? ($idPost . '_') : '') . $randHex;
    }

    /**
     * Parse & Resolve Landing Attribution from URL query parameters
     * Identifies: tracking_code, post_id, video_id, content_id, product_id, UTM parameters
     * Model: LAST ELIGIBLE FITNADO CONTENT TOUCH
     * @param array $queryParams ($_GET)
     * @param array|null $existingSessionContext
     * @return array Attribution context
     */
    public function resolveLandingAttribution(array $queryParams = array(), $existingSessionContext = null) {
        $now = time();
        $windowDays = (int)$this->getSetting('attribution_window_days', 30);
        $windowSeconds = $windowDays * 86400;

        $trackingCode = !empty($queryParams['ref']) ? trim($queryParams['ref']) : (!empty($queryParams['utm_content']) ? trim($queryParams['utm_content']) : '');
        $utmSource = !empty($queryParams['utm_source']) ? trim($queryParams['utm_source']) : '';
        $utmMedium = !empty($queryParams['utm_medium']) ? trim($queryParams['utm_medium']) : '';
        $utmCampaign = !empty($queryParams['utm_campaign']) ? trim($queryParams['utm_campaign']) : '';
        $referrer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

        // If a new valid tracking code or UTM touch arrives, query database to resolve identity
        if (!empty($trackingCode) && $this->d) {
            $post = $this->d->rawQueryOne("SELECT * FROM table_publish_post WHERE tracking_code = ? LIMIT 1", array($trackingCode));
            if (!empty($post)) {
                return array(
                    'tracking_code' => $trackingCode,
                    'id_post' => (int)$post['id'],
                    'id_product' => (int)$post['id_product'],
                    'id_video' => !empty($post['id_video']) ? (int)$post['id_video'] : null,
                    'id_content' => !empty($post['id_ai_content']) ? (int)$post['id_ai_content'] : null,
                    'id_affiliate_offer' => !empty($post['affiliate_offer_id']) ? (int)$post['affiliate_offer_id'] : null,
                    'source' => $utmSource ?: $post['platform'],
                    'medium' => $utmMedium ?: 'organic_video',
                    'campaign' => $utmCampaign ?: ('prod_' . $post['id_product']),
                    'content_ref' => $trackingCode,
                    'referrer' => $referrer,
                    'touch_time' => $now,
                    'expires_at' => $now + $windowSeconds,
                    'is_direct' => false
                );
            }
        }

        // If no new post tracking touch, check if existing session attribution is still within window
        if (!empty($existingSessionContext) && is_array($existingSessionContext)) {
            $touchTime = !empty($existingSessionContext['touch_time']) ? (int)$existingSessionContext['touch_time'] : 0;
            if (($now - $touchTime) <= $windowSeconds) {
                return $existingSessionContext; // Retain active touch
            }
        }

        // Direct Traffic (post_id = NULL)
        return array(
            'tracking_code' => null,
            'id_post' => null,
            'id_product' => !empty($queryParams['id']) ? (int)$queryParams['id'] : null,
            'id_video' => null,
            'id_content' => null,
            'id_affiliate_offer' => null,
            'source' => $utmSource ?: 'direct',
            'medium' => $utmMedium ?: 'direct',
            'campaign' => $utmCampaign ?: null,
            'content_ref' => null,
            'referrer' => $referrer,
            'touch_time' => $now,
            'expires_at' => $now + $windowSeconds,
            'is_direct' => true
        );
    }

    /**
     * Log an Analytics Event into table_analytics_event
     * @param string $eventType
     * @param array $data
     * @return int|bool Event ID or false
     */
    public function logEvent($eventType, array $data = array()) {
        if (!$this->d) return false;

        $now = time();
        $ip = !empty($data['ip']) ? $data['ip'] : ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $userAgent = !empty($data['user_agent']) ? $data['user_agent'] : ($_SERVER['HTTP_USER_AGENT'] ?? '');
        $deviceType = !empty($data['device_type']) ? $data['device_type'] : 'desktop';
        
        if (empty($data['device_type']) && preg_match('/(android|iphone|ipad|mobile)/i', $userAgent)) {
            $deviceType = 'mobile';
        }

        $isInternal = isset($data['is_internal']) ? (int)$data['is_internal'] : ($this->isInternalIp($ip) ? 1 : 0);

        $record = array(
            'event_type' => $eventType,
            'id_product' => !empty($data['id_product']) ? (int)$data['id_product'] : null,
            'id_post' => !empty($data['id_post']) ? (int)$data['id_post'] : null,
            'id_video' => !empty($data['id_video']) ? (int)$data['id_video'] : null,
            'id_content' => !empty($data['id_content']) ? (int)$data['id_content'] : null,
            'id_affiliate_offer' => !empty($data['id_affiliate_offer']) ? (int)$data['id_affiliate_offer'] : null,
            'tracking_code' => !empty($data['tracking_code']) ? trim($data['tracking_code']) : null,
            'session_id' => !empty($data['session_id']) ? trim($data['session_id']) : null,
            'source' => !empty($data['source']) ? substr(trim($data['source']), 0, 50) : null,
            'medium' => !empty($data['medium']) ? substr(trim($data['medium']), 0, 50) : null,
            'campaign' => !empty($data['campaign']) ? substr(trim($data['campaign']), 0, 100) : null,
            'content_ref' => !empty($data['content_ref']) ? substr(trim($data['content_ref']), 0, 100) : null,
            'referrer' => !empty($data['referrer']) ? substr(trim($data['referrer']), 0, 500) : null,
            'ip_hash' => hash('sha256', $ip),
            'user_agent' => substr($userAgent, 0, 255),
            'device_type' => $deviceType,
            'is_internal' => $isInternal,
            'metadata' => !empty($data['metadata']) ? (is_array($data['metadata']) ? json_encode($data['metadata']) : $data['metadata']) : null,
            'event_time' => !empty($data['event_time']) ? (int)$data['event_time'] : $now,
            'date_created' => $now
        );

        return $this->d->insert('analytics_event', $record);
    }

    /**
     * Record and enrich Affiliate Click Tracking
     * Updates transactional table_affiliate_click and projects event into table_analytics_event
     * @param int $affiliateId
     * @param array $attributionContext
     * @param array $extra [source_page, ip, user_agent, device_type]
     * @return int|bool Click ID or false
     */
    public function recordAffiliateClick($affiliateId, array $attributionContext = array(), array $extra = array()) {
        if (!$this->d) return false;

        $affiliateId = (int)$affiliateId;
        $offer = $this->d->rawQueryOne("SELECT * FROM table_product_affiliate WHERE id = ? LIMIT 1", array($affiliateId));
        if (empty($offer)) return false;

        $now = time();
        $ip = !empty($extra['ip']) ? $extra['ip'] : ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $userAgent = !empty($extra['user_agent']) ? $extra['user_agent'] : ($_SERVER['HTTP_USER_AGENT'] ?? '');
        $deviceType = !empty($extra['device_type']) ? $extra['device_type'] : 'desktop';
        if (empty($extra['device_type']) && preg_match('/(android|iphone|ipad|mobile)/i', $userAgent)) {
            $deviceType = 'mobile';
        }
        $sourcePage = !empty($extra['source_page']) ? $extra['source_page'] : 'product_detail';
        $referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $isInternal = $this->isInternalIp($ip) ? 1 : 0;

        $idProduct = (int)$offer['id_product'];
        $idPost = !empty($attributionContext['id_post']) ? (int)$attributionContext['id_post'] : null;
        $idVideo = !empty($attributionContext['id_video']) ? (int)$attributionContext['id_video'] : null;
        $idContent = !empty($attributionContext['id_content']) ? (int)$attributionContext['id_content'] : null;
        $trackingCode = !empty($attributionContext['tracking_code']) ? trim($attributionContext['tracking_code']) : null;
        $sessionId = !empty($attributionContext['session_id']) ? trim($attributionContext['session_id']) : null;

        // 1. Transactional source: table_affiliate_click
        $clickData = array(
            'id_product' => $idProduct,
            'id_affiliate' => $affiliateId,
            'tracking_code' => $trackingCode,
            'session_id' => $sessionId,
            'id_post' => $idPost,
            'id_video' => $idVideo,
            'id_content' => $idContent,
            'platform' => htmlspecialchars($offer['platform']),
            'source_page' => htmlspecialchars($sourcePage),
            'device_type' => $deviceType,
            'ip_hash' => hash('sha256', $ip),
            'user_agent' => substr($userAgent, 0, 255),
            'referer' => $referer,
            'is_internal' => $isInternal,
            'date_created' => $now
        );
        $clickId = $this->d->insert('affiliate_click', $clickData);

        // 2. Normalized analytics projection: table_analytics_event
        $this->logEvent(self::EVENT_AFFILIATE_CLICK, array(
            'id_product' => $idProduct,
            'id_affiliate_offer' => $affiliateId,
            'id_post' => $idPost,
            'id_video' => $idVideo,
            'id_content' => $idContent,
            'tracking_code' => $trackingCode,
            'session_id' => $sessionId,
            'source' => !empty($attributionContext['source']) ? $attributionContext['source'] : 'fitnado_web',
            'medium' => !empty($attributionContext['medium']) ? $attributionContext['medium'] : 'affiliate_redirect',
            'campaign' => !empty($attributionContext['campaign']) ? $attributionContext['campaign'] : null,
            'content_ref' => $trackingCode,
            'referrer' => $referer,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'is_internal' => $isInternal,
            'metadata' => array(
                'click_id' => $clickId,
                'offer_price' => (float)$offer['price'],
                'platform' => $offer['platform']
            ),
            'event_time' => $now
        ));

        return $clickId;
    }

    /**
     * Build standard Time Range WHERE SQL clause
     * @param string $timeRange ('today', '7d', '30d', 'all', or 'custom')
     * @param int|null $customStart
     * @param int|null $customEnd
     * @param string $timeField
     * @return array [sql, params]
     */
    public static function buildTimeRangeClause($timeRange = '30d', $customStart = null, $customEnd = null, $timeField = 'event_time') {
        $now = time();
        $params = array();

        switch ($timeRange) {
            case 'today':
                $start = strtotime('today midnight');
                $sql = "{$timeField} >= ?";
                $params[] = $start;
                break;
            case '7d':
                $start = $now - (7 * 86400);
                $sql = "{$timeField} >= ?";
                $params[] = $start;
                break;
            case '30d':
                $start = $now - (30 * 86400);
                $sql = "{$timeField} >= ?";
                $params[] = $start;
                break;
            case 'custom':
                if ($customStart && $customEnd) {
                    $sql = "{$timeField} BETWEEN ? AND ?";
                    $params[] = (int)$customStart;
                    $params[] = (int)$customEnd;
                } elseif ($customStart) {
                    $sql = "{$timeField} >= ?";
                    $params[] = (int)$customStart;
                } else {
                    $sql = "1=1";
                }
                break;
            case 'all':
            default:
                $sql = "1=1";
                break;
        }

        return array('sql' => $sql, 'params' => $params);
    }

    /**
     * Safe Division Helper (Guards against division by zero)
     * @param float|int $numerator
     * @param float|int $denominator
     * @param int $decimals
     * @return float|int
     */
    public static function safeDivide($numerator, $denominator, $decimals = 2) {
        $num = (float)$numerator;
        $den = (float)$denominator;
        if ($den <= 0) {
            return 0.0;
        }
        return round(($num / $den), $decimals);
    }

    /**
     * Calculate Safe Percentage (e.g. CTR = (clicks / sessions) * 100)
     * @param float|int $numerator
     * @param float|int $denominator
     * @param int $decimals
     * @return float
     */
    public static function safePercentage($numerator, $denominator, $decimals = 2) {
        $num = (float)$numerator;
        $den = (float)$denominator;
        if ($den <= 0) {
            return 0.0;
        }
        return round(($num / $den) * 100, $decimals);
    }

    /**
     * Get High-Level Overview KPIs
     * @param string $timeRange
     * @param array $filters [exclude_internal => bool]
     * @return array
     */
    public function getOverviewMetrics($timeRange = '30d', array $filters = array()) {
        if (!$this->d) return array();

        $excludeInternal = isset($filters['exclude_internal']) ? (bool)$filters['exclude_internal'] : true;
        $timeClause = self::buildTimeRangeClause($timeRange, $filters['start'] ?? null, $filters['end'] ?? null, 'event_time');
        
        $internalSql = $excludeInternal ? " AND is_internal = 0" : "";
        $whereSql = "WHERE {$timeClause['sql']}{$internalSql}";

        // 1. Landing Sessions (Count distinct session_id from PAGE_VIEW / PRODUCT_VIEW)
        $sessionRow = $this->d->rawQueryOne(
            "SELECT COUNT(DISTINCT session_id) as total_sessions FROM table_analytics_event {$whereSql} AND event_type IN ('PAGE_VIEW', 'PRODUCT_VIEW')",
            $timeClause['params']
        );
        $totalSessions = !empty($sessionRow['total_sessions']) ? (int)$sessionRow['total_sessions'] : 0;

        // 2. Product Views
        $prodViewRow = $this->d->rawQueryOne(
            "SELECT COUNT(id) as total_prod_views FROM table_analytics_event {$whereSql} AND event_type = 'PRODUCT_VIEW'",
            $timeClause['params']
        );
        $totalProductViews = !empty($prodViewRow['total_prod_views']) ? (int)$prodViewRow['total_prod_views'] : 0;

        // 3. Affiliate Clicks
        $clickRow = $this->d->rawQueryOne(
            "SELECT COUNT(id) as total_clicks FROM table_analytics_event {$whereSql} AND event_type = 'AFFILIATE_CLICK'",
            $timeClause['params']
        );
        $totalClicks = !empty($clickRow['total_clicks']) ? (int)$clickRow['total_clicks'] : 0;

        // 4. Conversions & Commission
        $convTimeClause = self::buildTimeRangeClause($timeRange, $filters['start'] ?? null, $filters['end'] ?? null, 'conversion_at');
        $convRows = $this->d->rawQuery(
            "SELECT status, currency, COUNT(id) as total_count, SUM(order_value) as sum_order_val, SUM(commission_value) as sum_comm_val 
             FROM table_affiliate_conversion 
             WHERE {$convTimeClause['sql']} 
             GROUP BY status, currency",
            $convTimeClause['params']
        );

        $conversionsCount = 0;
        $confirmedCommissionVND = 0.0;
        $confirmedCommissionUSD = 0.0;
        $pendingCommissionVND = 0.0;
        $hasConversionsConnected = !empty($convRows);

        if (!empty($convRows)) {
            foreach ($convRows as $cRow) {
                if ($cRow['status'] === 'CONFIRMED') {
                    $conversionsCount += (int)$cRow['total_count'];
                    if (strtoupper($cRow['currency']) === 'USD') {
                        $confirmedCommissionUSD += (float)$cRow['sum_comm_val'];
                    } else {
                        $confirmedCommissionVND += (float)$cRow['sum_comm_val'];
                    }
                } elseif ($cRow['status'] === 'PENDING') {
                    if (strtoupper($cRow['currency']) === 'USD') {
                        // USD pending
                    } else {
                        $pendingCommissionVND += (float)$cRow['sum_comm_val'];
                    }
                }
            }
        }

        // 5. Total Video Production Costs (TTS + AI Video API cost from table_ai_video)
        $costRow = $this->d->rawQueryOne("SELECT SUM(tts_cost + ai_video_cost) as total_ext_cost FROM table_ai_video");
        $totalExternalCost = !empty($costRow['total_ext_cost']) ? (float)$costRow['total_ext_cost'] : 0.0;

        // 6. Winner & Promising Counts
        $winnerCount = 0;
        $promisingCount = 0;
        $evalRows = $this->d->rawQuery("SELECT winner_status, COUNT(DISTINCT id_product) as cnt FROM table_winner_evaluation GROUP BY winner_status");
        if (!empty($evalRows)) {
            foreach ($evalRows as $eRow) {
                if ($eRow['winner_status'] === 'WINNER') $winnerCount = (int)$eRow['cnt'];
                if ($eRow['winner_status'] === 'PROMISING') $promisingCount = (int)$eRow['cnt'];
            }
        }

        // Calculated Rates
        $overallCTR = self::safePercentage($totalClicks, $totalSessions);
        $overallCVR = self::safePercentage($conversionsCount, $totalClicks);
        $overallEPC = self::safeDivide($confirmedCommissionVND, $totalClicks);
        $revenuePerSession = self::safeDivide($confirmedCommissionVND, $totalSessions);
        $netContentRoiVND = $confirmedCommissionVND - $totalExternalCost;

        return array(
            'total_sessions' => $totalSessions,
            'total_product_views' => $totalProductViews,
            'total_clicks' => $totalClicks,
            'ctr_pct' => $overallCTR,
            'conversions_count' => $conversionsCount,
            'cvr_pct' => $overallCVR,
            'confirmed_commission_vnd' => $confirmedCommissionVND,
            'confirmed_commission_usd' => $confirmedCommissionUSD,
            'pending_commission_vnd' => $pendingCommissionVND,
            'epc_vnd' => $overallEPC,
            'revenue_per_session_vnd' => $revenuePerSession,
            'total_external_cost_vnd' => $totalExternalCost,
            'content_roi_vnd' => $netContentRoiVND,
            'has_conversions_connected' => $hasConversionsConnected,
            'winner_count' => $winnerCount,
            'promising_count' => $promisingCount,
            'time_range' => $timeRange
        );
    }

    /**
     * Get Product Performance Metrics Table
     * @param int|null $productId
     * @param string $timeRange
     * @param array $filters
     * @return array
     */
    public function getProductMetrics($productId = null, $timeRange = '30d', array $filters = array()) {
        if (!$this->d) return array();

        $excludeInternal = isset($filters['exclude_internal']) ? (bool)$filters['exclude_internal'] : true;
        $timeClause = self::buildTimeRangeClause($timeRange, $filters['start'] ?? null, $filters['end'] ?? null, 'e.event_time');
        $internalSql = $excludeInternal ? " AND e.is_internal = 0" : "";

        $productWhere = "";
        $params = $timeClause['params'];
        if ($productId) {
            $productWhere = " WHERE p.id = ?";
            $params[] = (int)$productId;
        }

        // Base products query
        $sql = "SELECT p.id as product_id, p.namevi as product_name, p.slugvi as product_slug, p.photo, p.regular_price, p.sale_price,
                       r.total_score as research_score, r.ai_confidence
                FROM table_product p
                LEFT JOIN table_product_research r ON (p.id = r.id_product)
                {$productWhere}
                ORDER BY p.id DESC";

        $products = $this->d->rawQuery($sql, $productId ? array((int)$productId) : array());
        if (empty($products)) return array();

        $results = array();
        foreach ($products as $prod) {
            $pid = (int)$prod['product_id'];

            // Query sessions for this product
            $sessRow = $this->d->rawQueryOne(
                "SELECT COUNT(DISTINCT session_id) as sessions, COUNT(id) as views 
                 FROM table_analytics_event e 
                 WHERE e.id_product = ? AND {$timeClause['sql']}{$internalSql} AND e.event_type IN ('PAGE_VIEW', 'PRODUCT_VIEW')",
                array_merge(array($pid), $timeClause['params'])
            );
            $sessions = !empty($sessRow['sessions']) ? (int)$sessRow['sessions'] : 0;
            $views = !empty($sessRow['views']) ? (int)$sessRow['views'] : 0;

            // Query affiliate clicks
            $clickRow = $this->d->rawQueryOne(
                "SELECT COUNT(id) as clicks 
                 FROM table_analytics_event e 
                 WHERE e.id_product = ? AND {$timeClause['sql']}{$internalSql} AND e.event_type = 'AFFILIATE_CLICK'",
                array_merge(array($pid), $timeClause['params'])
            );
            $clicks = !empty($clickRow['clicks']) ? (int)$clickRow['clicks'] : 0;

            // Query conversions
            $convRow = $this->d->rawQueryOne(
                "SELECT COUNT(id) as conv_count, SUM(commission_value) as commission_vnd 
                 FROM table_affiliate_conversion 
                 WHERE id_product = ? AND status = 'CONFIRMED' AND currency = 'VND'",
                array($pid)
            );
            $conversions = !empty($convRow['conv_count']) ? (int)$convRow['conv_count'] : 0;
            $commissionVND = !empty($convRow['commission_vnd']) ? (float)$convRow['commission_vnd'] : 0.0;

            // Query external video cost
            $costRow = $this->d->rawQueryOne(
                "SELECT SUM(tts_cost + ai_video_cost) as prod_cost FROM table_ai_video WHERE id_product = ?",
                array($pid)
            );
            $contentCost = !empty($costRow['prod_cost']) ? (float)$costRow['prod_cost'] : 0.0;

            // Latest Winner Evaluation
            $latestEval = $this->d->rawQueryOne(
                "SELECT winner_status, signal_level, evaluated_at FROM table_winner_evaluation WHERE id_product = ? ORDER BY id DESC LIMIT 1",
                array($pid)
            );

            $ctr = self::safePercentage($clicks, $sessions);
            $cvr = self::safePercentage($conversions, $clicks);
            $roi = $commissionVND - $contentCost;

            $results[] = array(
                'id_product' => $pid,
                'name' => $prod['product_name'],
                'slug' => $prod['product_slug'],
                'photo' => $prod['photo'],
                'regular_price' => (float)$prod['regular_price'],
                'sale_price' => (float)$prod['sale_price'],
                'research_score' => $prod['research_score'] !== null ? (float)$prod['research_score'] : null,
                'ai_confidence' => $prod['ai_confidence'] !== null ? (float)$prod['ai_confidence'] : null,
                'sessions' => $sessions,
                'views' => $views,
                'clicks' => $clicks,
                'ctr_pct' => $ctr,
                'conversions' => $conversions,
                'cvr_pct' => $cvr,
                'commission_vnd' => $commissionVND,
                'content_cost_vnd' => $contentCost,
                'roi_vnd' => $roi,
                'winner_status' => $latestEval['winner_status'] ?? 'INSUFFICIENT_DATA',
                'signal_level' => $latestEval['signal_level'] ?? 'NONE',
                'evaluated_at' => $latestEval['evaluated_at'] ?? null
            );
        }

        return $results;
    }

    /**
     * Get Post Performance Metrics Table
     * @param int|null $postId
     * @param string $timeRange
     * @param array $filters
     * @return array
     */
    public function getPostMetrics($postId = null, $timeRange = '30d', array $filters = array()) {
        if (!$this->d) return array();

        $excludeInternal = isset($filters['exclude_internal']) ? (bool)$filters['exclude_internal'] : true;
        $timeClause = self::buildTimeRangeClause($timeRange, $filters['start'] ?? null, $filters['end'] ?? null, 'e.event_time');
        $internalSql = $excludeInternal ? " AND e.is_internal = 0" : "";

        $wherePost = $postId ? " WHERE p.id = ?" : "";
        $params = $postId ? array((int)$postId) : array();

        $sql = "SELECT p.*, prod.namevi as product_name, prod.slugvi as product_slug, v.mode as video_mode
                FROM table_publish_post p
                LEFT JOIN table_product prod ON (p.id_product = prod.id)
                LEFT JOIN table_ai_video v ON (p.id_video = v.id)
                {$wherePost}
                ORDER BY p.id DESC";

        $posts = $this->d->rawQuery($sql, $params);
        if (empty($posts)) return array();

        $results = array();
        foreach ($posts as $post) {
            $pid = (int)$post['id'];
            $trackingCode = $post['tracking_code'];

            // Query sessions associated with this post
            $sessRow = $this->d->rawQueryOne(
                "SELECT COUNT(DISTINCT session_id) as sessions 
                 FROM table_analytics_event e 
                 WHERE (e.id_post = ? OR e.tracking_code = ?) AND {$timeClause['sql']}{$internalSql} AND e.event_type IN ('PAGE_VIEW', 'PRODUCT_VIEW')",
                array_merge(array($pid, $trackingCode), $timeClause['params'])
            );
            $sessions = !empty($sessRow['sessions']) ? (int)$sessRow['sessions'] : 0;

            // Query clicks
            $clickRow = $this->d->rawQueryOne(
                "SELECT COUNT(id) as clicks 
                 FROM table_analytics_event e 
                 WHERE (e.id_post = ? OR e.tracking_code = ?) AND {$timeClause['sql']}{$internalSql} AND e.event_type = 'AFFILIATE_CLICK'",
                array_merge(array($pid, $trackingCode), $timeClause['params'])
            );
            $clicks = !empty($clickRow['clicks']) ? (int)$clickRow['clicks'] : 0;

            // Query conversions
            $convRow = $this->d->rawQueryOne(
                "SELECT COUNT(id) as conv_count, SUM(commission_value) as commission_vnd 
                 FROM table_affiliate_conversion 
                 WHERE (id_post = ? OR tracking_code = ?) AND status = 'CONFIRMED' AND currency = 'VND'",
                array($pid, $trackingCode)
            );
            $conversions = !empty($convRow['conv_count']) ? (int)$convRow['conv_count'] : 0;
            $commissionVND = !empty($convRow['commission_vnd']) ? (float)$convRow['commission_vnd'] : 0.0;

            $ctr = self::safePercentage($clicks, $sessions);
            $cvr = self::safePercentage($conversions, $clicks);

            $results[] = array(
                'id_post' => $pid,
                'tracking_code' => $trackingCode,
                'title' => $post['title'],
                'platform' => $post['platform'],
                'status' => $post['status'],
                'published_at' => $post['published_at'],
                'external_post_url' => $post['external_post_url'],
                'id_product' => (int)$post['id_product'],
                'product_name' => $post['product_name'],
                'video_mode' => $post['video_mode'] ?? 'ECONOMY',
                'sessions' => $sessions,
                'clicks' => $clicks,
                'ctr_pct' => $ctr,
                'conversions' => $conversions,
                'cvr_pct' => $cvr,
                'commission_vnd' => $commissionVND
            );
        }

        return $results;
    }

    /**
     * Get Video Performance Metrics Table
     * @param int|null $videoId
     * @param string $timeRange
     * @param array $filters
     * @return array
     */
    public function getVideoMetrics($videoId = null, $timeRange = '30d', array $filters = array()) {
        if (!$this->d) return array();

        $excludeInternal = isset($filters['exclude_internal']) ? (bool)$filters['exclude_internal'] : true;
        $timeClause = self::buildTimeRangeClause($timeRange, $filters['start'] ?? null, $filters['end'] ?? null, 'e.event_time');
        $internalSql = $excludeInternal ? " AND e.is_internal = 0" : "";

        $whereVideo = $videoId ? " WHERE v.id = ?" : "";
        $params = $videoId ? array((int)$videoId) : array();

        $sql = "SELECT v.*, p.namevi as product_name, p.slugvi as product_slug 
                FROM table_ai_video v
                LEFT JOIN table_product p ON (v.id_product = p.id)
                {$whereVideo}
                ORDER BY v.id DESC";

        $videos = $this->d->rawQuery($sql, $params);
        if (empty($videos)) return array();

        $results = array();
        foreach ($videos as $v) {
            $vid = (int)$v['id'];

            // Query sessions mapped to this video
            $sessRow = $this->d->rawQueryOne(
                "SELECT COUNT(DISTINCT session_id) as sessions 
                 FROM table_analytics_event e 
                 WHERE e.id_video = ? AND {$timeClause['sql']}{$internalSql} AND e.event_type IN ('PAGE_VIEW', 'PRODUCT_VIEW')",
                array_merge(array($vid), $timeClause['params'])
            );
            $sessions = !empty($sessRow['sessions']) ? (int)$sessRow['sessions'] : 0;

            // Query clicks
            $clickRow = $this->d->rawQueryOne(
                "SELECT COUNT(id) as clicks 
                 FROM table_analytics_event e 
                 WHERE e.id_video = ? AND {$timeClause['sql']}{$internalSql} AND e.event_type = 'AFFILIATE_CLICK'",
                array_merge(array($vid), $timeClause['params'])
            );
            $clicks = !empty($clickRow['clicks']) ? (int)$clickRow['clicks'] : 0;

            // Query conversions
            $convRow = $this->d->rawQueryOne(
                "SELECT COUNT(id) as conv_count, SUM(commission_value) as commission_vnd 
                 FROM table_affiliate_conversion 
                 WHERE id_video = ? AND status = 'CONFIRMED' AND currency = 'VND'",
                array($vid)
            );
            $conversions = !empty($convRow['conv_count']) ? (int)$convRow['conv_count'] : 0;
            $commissionVND = !empty($convRow['commission_vnd']) ? (float)$convRow['commission_vnd'] : 0.0;

            $totalCost = (float)$v['tts_cost'] + (float)$v['ai_video_cost'];
            $ctr = self::safePercentage($clicks, $sessions);
            $cvr = self::safePercentage($conversions, $clicks);
            $roi = $commissionVND - $totalCost;

            $results[] = array(
                'id_video' => $vid,
                'title' => $v['title'],
                'mode' => $v['mode'] ?? 'ECONOMY',
                'status' => $v['status'],
                'duration_actual' => $v['duration_actual'],
                'id_product' => (int)$v['id_product'],
                'product_name' => $v['product_name'],
                'tts_cost' => (float)$v['tts_cost'],
                'ai_video_cost' => (float)$v['ai_video_cost'],
                'total_cost_vnd' => $totalCost,
                'sessions' => $sessions,
                'clicks' => $clicks,
                'ctr_pct' => $ctr,
                'conversions' => $conversions,
                'cvr_pct' => $cvr,
                'commission_vnd' => $commissionVND,
                'roi_vnd' => $roi
            );
        }

        return $results;
    }

    /**
     * Get Content & Hook Performance Aggregations
     * Maps performance back to AI Content packages and Hook types
     * @param string $timeRange
     * @param array $filters
     * @return array
     */
    public function getContentHookMetrics($timeRange = '30d', array $filters = array()) {
        if (!$this->d) return array();

        $excludeInternal = isset($filters['exclude_internal']) ? (bool)$filters['exclude_internal'] : true;
        $timeClause = self::buildTimeRangeClause($timeRange, $filters['start'] ?? null, $filters['end'] ?? null, 'e.event_time');
        $internalSql = $excludeInternal ? " AND e.is_internal = 0" : "";

        $contents = $this->d->rawQuery("SELECT c.*, p.namevi as product_name FROM table_ai_content c LEFT JOIN table_product p ON (c.id_product = p.id) ORDER BY c.id DESC");
        if (empty($contents)) return array();

        $results = array();
        foreach ($contents as $c) {
            $cid = (int)$c['id'];

            // Query sessions
            $sessRow = $this->d->rawQueryOne(
                "SELECT COUNT(DISTINCT session_id) as sessions 
                 FROM table_analytics_event e 
                 WHERE e.id_content = ? AND {$timeClause['sql']}{$internalSql} AND e.event_type IN ('PAGE_VIEW', 'PRODUCT_VIEW')",
                array_merge(array($cid), $timeClause['params'])
            );
            $sessions = !empty($sessRow['sessions']) ? (int)$sessRow['sessions'] : 0;

            // Query clicks
            $clickRow = $this->d->rawQueryOne(
                "SELECT COUNT(id) as clicks 
                 FROM table_analytics_event e 
                 WHERE e.id_content = ? AND {$timeClause['sql']}{$internalSql} AND e.event_type = 'AFFILIATE_CLICK'",
                array_merge(array($cid), $timeClause['params'])
            );
            $clicks = !empty($clickRow['clicks']) ? (int)$clickRow['clicks'] : 0;

            // Count number of posts using this content
            $postCountRow = $this->d->rawQueryOne("SELECT COUNT(id) as post_count FROM table_publish_post WHERE id_ai_content = ?", array($cid));
            $postCount = !empty($postCountRow['post_count']) ? (int)$postCountRow['post_count'] : 0;

            // Query conversions
            $convRow = $this->d->rawQueryOne(
                "SELECT COUNT(id) as conv_count, SUM(commission_value) as commission_vnd 
                 FROM table_affiliate_conversion 
                 WHERE id_content = ? AND status = 'CONFIRMED' AND currency = 'VND'",
                array($cid)
            );
            $conversions = !empty($convRow['conv_count']) ? (int)$convRow['conv_count'] : 0;
            $commissionVND = !empty($convRow['commission_vnd']) ? (float)$convRow['commission_vnd'] : 0.0;

            $ctr = self::safePercentage($clicks, $sessions);

            $results[] = array(
                'id_content' => $cid,
                'title' => $c['title'],
                'version' => $c['version'],
                'id_product' => (int)$c['id_product'],
                'product_name' => $c['product_name'],
                'post_count' => $postCount,
                'sessions' => $sessions,
                'clicks' => $clicks,
                'ctr_pct' => $ctr,
                'conversions' => $conversions,
                'commission_vnd' => $commissionVND
            );
        }

        return $results;
    }
}
