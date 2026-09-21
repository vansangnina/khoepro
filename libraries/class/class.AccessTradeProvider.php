<?php
/**
 * FITNADO - ACCESSTRADE Publisher API Integration Provider (Phase 10.1)
 * Official Publisher API client for Campaign Discovery, Product Datafeeds,
 * Dynamic Tracking Link Generation & Automated Transaction Reconciliation.
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

class AccessTradeProvider
{
    private $d;
    private $func;
    private $accessKey;
    private $baseUrl;
    private $timeout;
    private $rateLimitPerMinute;

    const DEFAULT_BASE_URL = 'https://api.accesstrade.vn';
    const DEFAULT_TIMEOUT = 30;
    const DEFAULT_RATE_LIMIT = 30; // 30 req/min

    // ACCESSTRADE Transaction Status Mappings
    // 0: Pending (Đang chờ duyệt) -> PENDING
    // 1: Approved (Đã duyệt hoa hồng) -> CONFIRMED
    // 2: Rejected (Bị hủy / Hoàn hàng) -> REVERSED
    const STATUS_PENDING   = 'PENDING';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_REVERSED  = 'REVERSED';

    public function __construct($d = null, $func = null, array $customConfig = array())
    {
        global $config;
        $this->d = $d;
        $this->func = $func;

        $atConfig = !empty($config['accesstrade']) ? $config['accesstrade'] : array();
        $this->accessKey = $customConfig['access_key'] ?? ($atConfig['access_key'] ?? '');
        $this->baseUrl = rtrim($customConfig['base_url'] ?? ($atConfig['base_url'] ?? self::DEFAULT_BASE_URL), '/');
        $this->timeout = (int)($customConfig['timeout'] ?? ($atConfig['timeout'] ?? self::DEFAULT_TIMEOUT));
        $this->rateLimitPerMinute = (int)($customConfig['rate_limit_per_minute'] ?? ($atConfig['rate_limit_per_minute'] ?? self::DEFAULT_RATE_LIMIT));
    }

    /**
     * Check if provider has active credentials configured
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->accessKey);
    }

    /**
     * Get Masked Access Key for UI display
     * @return string
     */
    public function getMaskedAccessKey()
    {
        if (empty($this->accessKey)) {
            return 'NOT_CONFIGURED';
        }
        $len = strlen($this->accessKey);
        if ($len <= 8) {
            return '****';
        }
        return substr($this->accessKey, 0, 4) . str_repeat('*', $len - 8) . substr($this->accessKey, -4);
    }

    /**
     * Read-only test connection probe to ACCESSTRADE API
     * @return array ['success' => bool, 'message' => string, 'http_code' => int, 'latency_ms' => float]
     */
    public function testConnection()
    {
        if (!$this->isConfigured()) {
            return array(
                'success' => false,
                'message' => 'Chưa cấu hình API Access Key cho ACCESSTRADE.',
                'http_code' => 0,
                'latency_ms' => 0
            );
        }

        $start = microtime(true);
        $res = $this->request('GET', '/v1/campaigns', array('limit' => 1));
        $latency = round((microtime(true) - $start) * 1000, 2);

        if ($res['success']) {
            return array(
                'success' => true,
                'message' => 'Kết nối thành công đến ACCESSTRADE Publisher API (' . $latency . 'ms).',
                'http_code' => $res['http_code'],
                'latency_ms' => $latency,
                'data_count' => isset($res['data']['data']) ? count($res['data']['data']) : (isset($res['data']) ? count($res['data']) : 0)
            );
        }

        $errMsg = 'Lỗi kết nối (' . $res['http_code'] . '): ' . ($res['error'] ?: 'Không thể xác thực');
        if ($res['http_code'] === 401 || $res['http_code'] === 403) {
            $errMsg = 'Xác thực thất bại (401/403): API Access Key không hợp lệ hoặc tài khoản bị khóa.';
        } elseif ($res['http_code'] === 429) {
            $errMsg = 'Vượt quá giới hạn tần suất yêu cầu (429 Rate Limit Exceeded).';
        }

        return array(
            'success' => false,
            'message' => $errMsg,
            'http_code' => $res['http_code'],
            'latency_ms' => $latency
        );
    }

    /**
     * Get Campaigns from ACCESSTRADE API
     * @param array $params ['status' => 1, 'category' => '', 'page' => 1, 'limit' => 50]
     * @return array ['success' => bool, 'campaigns' => array(), 'total' => int, 'error' => string]
     */
    public function getCampaigns(array $params = array())
    {
        $queryParams = array(
            'status' => $params['status'] ?? 1, // 1: Active
            'page' => (int)($params['page'] ?? 1),
            'limit' => min(100, max(1, (int)($params['limit'] ?? 50)))
        );

        if (!empty($params['keyword'])) {
            $queryParams['keyword'] = trim($params['keyword']);
        }
        if (!empty($params['category'])) {
            $queryParams['category'] = trim($params['category']);
        }

        $res = $this->request('GET', '/v1/campaigns', $queryParams);
        if (!$res['success']) {
            return array('success' => false, 'campaigns' => array(), 'total' => 0, 'error' => $res['error']);
        }

        $rawList = $res['data']['data'] ?? ($res['data'] ?? array());
        $campaigns = array();

        if (is_array($rawList)) {
            foreach ($rawList as $c) {
                $campaigns[] = array(
                    'campaign_id' => (string)($c['id'] ?? $c['campaign_id'] ?? ''),
                    'name' => (string)($c['name'] ?? ''),
                    'merchant' => (string)($c['merchant'] ?? $c['name'] ?? ''),
                    'platform' => strtolower($c['merchant'] ?? 'accesstrade'),
                    'url' => (string)($c['url'] ?? ''),
                    'category' => (string)($c['category'] ?? $c['category_name'] ?? 'general'),
                    'approval_status' => strtolower($c['approval_status'] ?? 'approved'),
                    'cookie_duration_days' => (int)($c['cookie_duration'] ?? 30),
                    'commission_policy' => (string)($c['commission_policy'] ?? $c['max_commission'] ?? ''),
                    'max_commission_rate' => (float)($c['max_commission_rate'] ?? 0),
                    'scope' => (string)($c['scope'] ?? 'VN'),
                    'raw' => $c
                );
            }
        }

        return array(
            'success' => true,
            'campaigns' => $campaigns,
            'total' => count($campaigns),
            'error' => null
        );
    }

    /**
     * Alias for getCampaigns
     */
    public function searchCampaigns(array $params = array())
    {
        $res = $this->getCampaigns($params);
        return array(
            'success' => $res['success'],
            'data' => $res['campaigns'] ?? array(),
            'total' => $res['total'] ?? 0,
            'error' => $res['error'] ?? null
        );
    }


    /**
     * Filter campaigns matching FITNADO Niche (Gym, Fitness, Sports, Health Equipment)
     * @param array $campaigns
     * @return array
     */
    public function filterGymCampaigns(array $campaigns)
    {
        $keywords = array('gym', 'fitness', 'sport', 'thể thao', 'dinh dưỡng', 'whey', 'sức khỏe', 'thể hình', 'chạy bộ', 'phụ kiện tập');
        $filtered = array();

        foreach ($campaigns as $c) {
            $haystack = mb_strtolower(($c['name'] ?? '') . ' ' . ($c['category'] ?? '') . ' ' . ($c['merchant'] ?? ''), 'UTF-8');
            $matched = false;
            foreach ($keywords as $kw) {
                if (mb_strpos($haystack, $kw, 0, 'UTF-8') !== false) {
                    $matched = true;
                    break;
                }
            }
            if ($matched) {
                $filtered[] = $c;
            }
        }

        return $filtered;
    }

    /**
     * Get Datafeed Products with array parameter signature
     * @param array $params
     * @return array
     */
    public function getDatafeedProducts(array $params = array())
    {
        $keyword = $params['keyword'] ?? '';
        return $this->searchProducts($keyword, $params);
    }

    /**
     * Search Products from ACCESSTRADE Datafeed API
     * @param string $keyword
     * @param array $params
     * @return array
     */
    public function searchProducts($keyword, array $params = array())
    {
        $queryParams = array(
            'keyword' => trim($keyword),
            'page' => (int)($params['page'] ?? 1),
            'limit' => min(50, max(1, (int)($params['limit'] ?? 20)))
        );

        if (!empty($params['campaign_id'])) {
            $queryParams['campaign_id'] = $params['campaign_id'];
        }

        $res = $this->request('GET', '/v1/datafeeds', $queryParams);
        if (!$res['success']) {
            return array('success' => false, 'products' => array(), 'total' => 0, 'error' => $res['error']);
        }

        $rawList = $res['data']['data'] ?? ($res['data'] ?? array());
        $products = array();

        if (is_array($rawList)) {
            foreach ($rawList as $p) {
                $products[] = array(
                    'product_id' => (string)($p['product_id'] ?? $p['id'] ?? ''),
                    'name' => (string)($p['name'] ?? ''),
                    'price' => (float)($p['price'] ?? 0),
                    'original_price' => (float)($p['original_price'] ?? 0),
                    'discount_pct' => (float)($p['discount_rate'] ?? 0),
                    'image' => (string)($p['image'] ?? $p['image_url'] ?? ''),
                    'url' => (string)($p['url'] ?? $p['aff_url'] ?? ''),
                    'merchant' => (string)($p['merchant'] ?? ''),
                    'brand' => (string)($p['brand'] ?? ''),
                    'category' => (string)($p['category'] ?? ''),
                    'commission_rate' => (float)($p['commission_rate'] ?? 0),
                    'commission_value' => (float)($p['commission_value'] ?? 0),
                    'in_stock' => !isset($p['in_stock']) || (bool)$p['in_stock'],
                    'sales_count' => (int)($p['sold_count'] ?? $p['sales'] ?? 0),
                    'rating' => (float)($p['rating'] ?? 5.0),
                    'raw' => $p
                );
            }
        }

        return array(
            'success' => true,
            'products' => $products,
            'total' => count($products),
            'error' => null
        );
    }

    /**
     * Normalize ACCESSTRADE raw datafeed product into FITNADO catalog schema
     * @param array $p Raw product from API or CSV
     * @return array
     */
    public function normalizeDatafeedProduct(array $p)
    {
        $name = trim($p['name'] ?? ($p['title'] ?? ''));
        $price = (float)($p['price'] ?? 0);
        $originalPrice = (float)($p['original_price'] ?? $price);
        $merchant = trim($p['merchant'] ?? 'ACCESSTRADE');
        $category = trim($p['category_name'] ?? ($p['category'] ?? 'Gym & Fitness'));
        $affUrl = trim($p['aff_url'] ?? ($p['url'] ?? ($p['affiliate_url'] ?? '')));
        $image = trim($p['image'] ?? ($p['image_url'] ?? ''));
        $atProductId = (string)($p['product_id'] ?? ($p['id'] ?? ''));

        return array(
            'title' => $name,
            'name' => $name,
            'price' => $price,
            'original_price' => $originalPrice,
            'affiliate_url' => $affUrl,
            'image_url' => $image,
            'merchant' => $merchant,
            'at_merchant' => $merchant,
            'category' => $category,
            'discovery_source' => 'ACCESSTRADE_API',
            'is_accesstrade' => 1,
            'at_campaign_id' => (string)($p['campaign_id'] ?? ''),
            'external_product_id' => $atProductId,
            'commission_rate' => (float)($p['commission_rate'] ?? 0),
            'commission_value' => (float)($p['commission_value'] ?? 0),
            'raw_data' => $p
        );
    }

    /**
     * Map ACCESSTRADE raw status code (0, 1, 2) to FITNADO / standard status
     * @param int|string $rawStatus
     * @return string
     */
    public function mapTransactionStatus($rawStatus)
    {
        $statusNum = (int)$rawStatus;
        if ($statusNum === 1 || $rawStatus === 'APPROVED' || $rawStatus === 'approved') {
            return 'APPROVED';
        } elseif ($statusNum === 2 || $rawStatus === 'REJECTED' || $rawStatus === 'rejected') {
            return 'REJECTED';
        }
        return 'PENDING';
    }

    /**
     * Reconcile an array of raw/normalized transactions into database idempotently
     * @param array $transactions
     * @param string $adminUser
     * @return array
     */
    public function reconcileTransactions(array $transactions, $adminUser = 'system')
    {
        if (!$this->d) {
            return array('success' => false, 'error' => 'Database connection unavailable.');
        }

        $now = time();
        $importedCount = 0;
        $updatedCount = 0;
        $duplicateCount = 0;
        $skippedCount = 0;

        foreach ($transactions as $t) {
            $externalId = (string)($t['order_id'] ?? ($t['conversion_id'] ?? ($t['external_id'] ?? '')));
            if (empty($externalId)) continue;

            $platform = 'accesstrade';
            $rawStatus = $t['status'] ?? 0;
            $mappedStatus = $this->mapTransactionStatus($rawStatus);
            $orderValue = (float)($t['order_amount'] ?? ($t['order_value'] ?? 0));
            $commission = (float)($t['commission'] ?? ($t['commission_value'] ?? 0));
            $clickTime = !empty($t['click_time']) ? (is_numeric($t['click_time']) ? (int)$t['click_time'] : strtotime($t['click_time'])) : 0;
            $convTime = !empty($t['conversion_time']) ? (is_numeric($t['conversion_time']) ? (int)$t['conversion_time'] : strtotime($t['conversion_time'])) : $now;
            $trackingCode = $t['sub4'] ?? ($t['tracking_code'] ?? null);
            $idProduct = !empty($t['sub1']) ? (int)$t['sub1'] : (!empty($t['id_product']) ? (int)$t['id_product'] : 1);
            $idPost = !empty($t['sub2']) ? (int)$t['sub2'] : (!empty($t['id_post']) ? (int)$t['id_post'] : null);
            $idExp = !empty($t['sub3']) ? (int)$t['sub3'] : null;

            // Check existing record
            $existing = $this->d->rawQueryOne(
                "SELECT id, status, commission_value FROM table_affiliate_conversion WHERE platform = ? AND external_conversion_id = ? LIMIT 1",
                array($platform, $externalId)
            );

            if (!empty($existing)) {
                if ($existing['status'] !== $mappedStatus) {
                    $this->d->where('id', $existing['id']);
                    $this->d->update('affiliate_conversion', array(
                        'status' => $mappedStatus,
                        'commission_value' => $commission,
                        'confirmed_at' => ($mappedStatus === 'APPROVED') ? $now : null,
                        'date_updated' => $now
                    ));
                    $updatedCount++;
                } else {
                    $duplicateCount++;
                    $skippedCount++;
                }
                continue;
            }

            // Insert new conversion
            $insData = array(
                'conversion_id' => $externalId,
                'platform' => $platform,
                'external_conversion_id' => $externalId,
                'at_transaction_id' => (string)($t['transaction_id'] ?? ($t['click_id'] ?? '')),
                'id_product' => $idProduct,
                'id_post' => $idPost,
                'tracking_code' => $trackingCode,
                'order_value' => $orderValue,
                'commission_value' => $commission,
                'currency' => 'VND',
                'status' => $mappedStatus,
                'attribution_type' => !empty($trackingCode) ? 'TRACKING_CODE' : 'DIRECT',
                'conversion_at' => $convTime,
                'confirmed_at' => ($mappedStatus === 'APPROVED') ? $now : null,
                'at_click_time' => $clickTime,
                'at_conversion_time' => $convTime,
                'raw_reference' => json_encode($t, JSON_UNESCAPED_UNICODE),
                'is_manual_matched' => 0,
                'date_created' => $now,
                'date_updated' => $now
            );
            $this->d->insert('affiliate_conversion', $insData);
            $importedCount++;
        }

        return array(
            'success' => true,
            'total' => count($transactions),
            'imported' => $importedCount,
            'updated' => $updatedCount,
            'duplicate' => $duplicateCount,
            'skipped' => $skippedCount
        );
    }

    /**
     * Generate ACCESSTRADE Custom Tracking Link with FITNADO sub-ID parameters
     *
     * Logical Sub-ID Parameter Strategy:
     * - utm_source = fitnado
     * - utm_medium = website | tiktok | social
     * - utm_campaign = <campaign_slug>
     * - utm_content = <tracking_code>
     * - sub1 = <id_product> (FITNADO Product Identity)
     * - sub2 = <id_post> (FITNADO Post Identity)
     * - sub3 = <id_experiment> (FITNADO Experiment Identity)
     * - sub4 = <tracking_code> (FITNADO Unified Tracking Fingerprint)
     *
     * @param string $destinationUrl Merchant product URL
     * @param string $campaignId ACCESSTRADE campaign ID
     * @param array $trackingParams Extra tracking metadata
     * @return array ['success' => bool, 'tracking_url' => string, 'short_url' => string, 'error' => string]
     */
    public function generateTrackingLink($destinationUrl, $campaignId = '', array $trackingParams = array())
    {
        if (is_array($campaignId)) {
            $trackingParams = $campaignId;
            $campaignId = '';
        }

        $destinationUrl = trim($destinationUrl);
        if (empty($destinationUrl)) {
            return array('success' => false, 'tracking_url' => '', 'short_url' => '', 'error' => 'Đường dẫn sản phẩm đích (destinationUrl) không được để trống.');
        }

        $utmSource = $trackingParams['utm_source'] ?? 'fitnado';
        $utmMedium = $trackingParams['utm_medium'] ?? 'organic_video';
        $utmCampaign = $trackingParams['utm_campaign'] ?? 'fitnado_product';
        $utmContent = $trackingParams['utm_content'] ?? ($trackingParams['tracking_code'] ?? '');
        $sub1 = !empty($trackingParams['id_product']) ? (string)$trackingParams['id_product'] : ($trackingParams['sub1'] ?? '');
        $sub2 = !empty($trackingParams['id_post']) ? (string)$trackingParams['id_post'] : ($trackingParams['sub2'] ?? '');
        $sub3 = !empty($trackingParams['id_experiment']) ? (string)$trackingParams['id_experiment'] : ($trackingParams['sub3'] ?? '');
        $sub4 = !empty($trackingParams['tracking_code']) ? (string)$trackingParams['tracking_code'] : ($trackingParams['sub4'] ?? '');

        // If API configured, generate live deep link from API
        if ($this->isConfigured()) {
            $payload = array(
                'url' => $destinationUrl,
                'campaign_id' => $campaignId,
                'utm_source' => $utmSource,
                'utm_medium' => $utmMedium,
                'utm_campaign' => $utmCampaign,
                'utm_content' => $utmContent,
                'sub1' => $sub1,
                'sub2' => $sub2,
                'sub3' => $sub3,
                'sub4' => $sub4
            );

            $res = $this->request('POST', '/v1/toplink/customlink', $payload);
            if ($res['success'] && !empty($res['data']['short_url'] || !empty($res['data']['tracking_url']))) {
                return array(
                    'success' => true,
                    'tracking_url' => $res['data']['tracking_url'] ?? $res['data']['short_url'],
                    'short_url' => $res['data']['short_url'] ?? ($res['data']['tracking_url'] ?? ''),
                    'is_live_api' => true,
                    'error' => null
                );
            }
        }

        // Deterministic Standard Fallback Deep Link Generation
        // Formats official fallback deep URL if API key not set or offline
        $queryParts = array(
            'url' => $destinationUrl,
            'utm_source' => $utmSource,
            'utm_medium' => $utmMedium,
            'utm_campaign' => $utmCampaign
        );
        if (!empty($utmContent)) $queryParts['utm_content'] = $utmContent;
        if (!empty($sub1)) $queryParts['sub1'] = $sub1;
        if (!empty($sub2)) $queryParts['sub2'] = $sub2;
        if (!empty($sub3)) $queryParts['sub3'] = $sub3;
        if (!empty($sub4)) $queryParts['sub4'] = $sub4;

        $trackingUrl = 'https://go.isclix.com/deep_link/' . ($campaignId ?: '4348614214463921506') . '?' . http_build_query($queryParts);

        return array(
            'success' => true,
            'tracking_url' => $trackingUrl,
            'short_url' => $trackingUrl,
            'is_live_api' => false,
            'error' => null
        );
    }

    /**
     * Fetch Orders/Transactions from ACCESSTRADE Transaction API
     * @param string|int|null $since Start timestamp or YYYY-MM-DD
     * @param string|int|null $until End timestamp or YYYY-MM-DD
     * @param string|null $status 0, 1, 2 or null for all
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function fetchTransactions($since = null, $until = null, $status = null, $page = 1, $limit = 100)
    {
        if (!$this->isConfigured()) {
            return array('success' => false, 'orders' => array(), 'total' => 0, 'error' => 'Chưa cấu hình API Access Key cho ACCESSTRADE.');
        }

        $now = time();
        $sinceDate = $since ? (is_numeric($since) ? date('Y-m-d', (int)$since) : $since) : date('Y-m-d', $now - (30 * 86400));
        $untilDate = $until ? (is_numeric($until) ? date('Y-m-d', (int)$until) : $until) : date('Y-m-d', $now);

        $queryParams = array(
            'since' => $sinceDate,
            'until' => $untilDate,
            'page' => (int)$page,
            'limit' => min(100, max(1, (int)$limit))
        );

        if ($status !== null && $status !== '') {
            $queryParams['status'] = $status;
        }

        $res = $this->request('GET', '/v1/orders', $queryParams);
        if (!$res['success']) {
            return array('success' => false, 'orders' => array(), 'total' => 0, 'error' => $res['error']);
        }

        $rawOrders = $res['data']['data'] ?? ($res['data'] ?? array());
        $orders = array();

        if (is_array($rawOrders)) {
            foreach ($rawOrders as $o) {
                // Map ACCESSTRADE transaction status
                // 0: Pending, 1: Approved, 2: Rejected
                $rawStatus = (int)($o['status'] ?? 0);
                $statusMapped = self::STATUS_PENDING;
                if ($rawStatus === 1 || $rawStatus === 3) {
                    $statusMapped = self::STATUS_CONFIRMED;
                } elseif ($rawStatus === 2 || $rawStatus === -1) {
                    $statusMapped = self::STATUS_REVERSED;
                }

                $orderId = (string)($o['order_id'] ?? $o['transaction_id'] ?? $o['id'] ?? '');
                $transId = (string)($o['transaction_id'] ?? $o['id'] ?? $orderId);
                $merchant = (string)($o['merchant'] ?? $o['campaign_name'] ?? 'accesstrade');
                $orderValue = (float)($o['order_value'] ?? $o['price'] ?? 0);
                $commission = (float)($o['pub_commission'] ?? $o['commission'] ?? 0);
                $transTime = !empty($o['transaction_time']) ? strtotime($o['transaction_time']) : (!empty($o['click_time']) ? strtotime($o['click_time']) : $now);
                $clickTime = !empty($o['click_time']) ? strtotime($o['click_time']) : null;

                // Extract Sub-IDs
                $sub1 = (string)($o['sub1'] ?? '');
                $sub2 = (string)($o['sub2'] ?? '');
                $sub3 = (string)($o['sub3'] ?? '');
                $sub4 = (string)($o['sub4'] ?? '');
                $utmContent = (string)($o['utm_content'] ?? '');
                $trackingCode = $sub4 ?: ($utmContent ?: '');

                $orders[] = array(
                    'order_id' => $orderId,
                    'transaction_id' => $transId,
                    'merchant' => $merchant,
                    'platform' => 'accesstrade',
                    'order_value' => $orderValue,
                    'commission' => $commission,
                    'currency' => 'VND',
                    'status' => $statusMapped,
                    'raw_status' => $rawStatus,
                    'transaction_time' => $transTime,
                    'click_time' => $clickTime,
                    'tracking_code' => $trackingCode,
                    'id_product' => !empty($sub1) && is_numeric($sub1) ? (int)$sub1 : null,
                    'id_post' => !empty($sub2) && is_numeric($sub2) ? (int)$sub2 : null,
                    'id_experiment' => !empty($sub3) && is_numeric($sub3) ? (int)$sub3 : null,
                    'raw_data' => $o
                );
            }
        }

        return array(
            'success' => true,
            'orders' => $orders,
            'total' => count($orders),
            'error' => null
        );
    }

    /**
     * Incremental Automated Transaction Sync & Attribution Projection
     * Reconciles external transactions with database with full duplicate protection
     * @param array $options ['since' => timestamp, 'limit' => 100, 'admin_user' => 'system']
     * @return array Sync summary
     */
    public function syncTransactions(array $options = array())
    {
        if (!$this->d) {
            return array('success' => false, 'error' => 'Database connection unavailable.');
        }

        $now = time();
        $adminUser = $options['admin_user'] ?? 'system_cron';

        // 1. Get last sync time with safety overlap (default 60 mins overlap)
        $lastSyncSetting = $this->d->rawQueryOne("SELECT setting_value FROM table_analytics_setting WHERE setting_key = 'accesstrade_last_sync_time' LIMIT 1");
        $lastSyncTime = !empty($lastSyncSetting['setting_value']) ? (int)$lastSyncSetting['setting_value'] : 0;
        
        $overlapSeconds = 3600; // 1 hour overlap
        $fetchSince = ($lastSyncTime > 0) ? ($lastSyncTime - $overlapSeconds) : ($now - (30 * 86400));
        if ($fetchSince < ($now - (90 * 86400))) {
            $fetchSince = $now - (90 * 86400); // max 90 days
        }

        $fetchRes = $this->fetchTransactions($fetchSince, $now, null, 1, (int)($options['limit'] ?? 100));
        if (!$fetchRes['success']) {
            return array(
                'success' => false,
                'imported_count' => 0,
                'updated_count' => 0,
                'duplicate_count' => 0,
                'unattributed_count' => 0,
                'error' => $fetchRes['error']
            );
        }

        $importedCount = 0;
        $updatedCount = 0;
        $duplicateCount = 0;
        $unattributedCount = 0;
        $totalCommissionVND = 0.0;
        $totalOrderValVND = 0.0;

        require_once LIBRARIES . 'class/class.AnalyticsService.php';
        $analytics = new AnalyticsService($this->d, $this->func);

        foreach ($fetchRes['orders'] as $ord) {
            $externalId = $ord['order_id'];
            $platform = 'accesstrade';

            // Check if already exists in table_affiliate_conversion
            $existing = $this->d->rawQueryOne(
                "SELECT id, status, commission_value FROM table_affiliate_conversion WHERE platform = ? AND external_conversion_id = ? LIMIT 1",
                array($platform, $externalId)
            );

            // Resolve attribution if missing product/post
            $idProduct = $ord['id_product'];
            $idPost = $ord['id_post'];
            $idVideo = null;
            $idContent = null;
            $idOffer = null;

            if (empty($idProduct) && !empty($ord['tracking_code'])) {
                // Try resolve via post
                $post = $this->d->rawQueryOne("SELECT id, id_product, id_video, id_ai_content, affiliate_offer_id FROM table_publish_post WHERE tracking_code = ? LIMIT 1", array($ord['tracking_code']));
                if (!empty($post)) {
                    $idPost = (int)$post['id'];
                    $idProduct = (int)$post['id_product'];
                    $idVideo = !empty($post['id_video']) ? (int)$post['id_video'] : null;
                    $idContent = !empty($post['id_ai_content']) ? (int)$post['id_ai_content'] : null;
                    $idOffer = !empty($post['affiliate_offer_id']) ? (int)$post['affiliate_offer_id'] : null;
                } else {
                    // Try resolve via click
                    $click = $this->d->rawQueryOne("SELECT id_product, id_post, id_video, id_content, id_affiliate FROM table_affiliate_click WHERE tracking_code = ? ORDER BY id DESC LIMIT 1", array($ord['tracking_code']));
                    if (!empty($click)) {
                        $idProduct = (int)$click['id_product'];
                        $idPost = !empty($click['id_post']) ? (int)$click['id_post'] : null;
                        $idVideo = !empty($click['id_video']) ? (int)$click['id_video'] : null;
                        $idContent = !empty($click['id_content']) ? (int)$click['id_content'] : null;
                        $idOffer = !empty($click['id_affiliate']) ? (int)$click['id_affiliate'] : null;
                    }
                }
            }

            if (empty($idProduct)) {
                $unattributedCount++;
            }

            if (!empty($existing)) {
                // Duplicate / Status update
                if ($existing['status'] !== $ord['status']) {
                    $this->d->where('id', $existing['id']);
                    $this->d->update('affiliate_conversion', array(
                        'status' => $ord['status'],
                        'commission_value' => (float)$ord['commission'],
                        'confirmed_at' => ($ord['status'] === self::STATUS_CONFIRMED) ? $now : null,
                        'date_updated' => $now
                    ));
                    $updatedCount++;
                } else {
                    $duplicateCount++;
                }
                continue;
            }

            // Insert New Conversion
            $convData = array(
                'conversion_id' => $externalId,
                'platform' => $platform,
                'external_conversion_id' => $externalId,
                'at_transaction_id' => $ord['transaction_id'],
                'id_product' => $idProduct,
                'id_post' => $idPost,
                'id_video' => $idVideo,
                'id_content' => $idContent,
                'id_affiliate_offer' => $idOffer,
                'tracking_code' => $ord['tracking_code'] ?: null,
                'session_id' => null,
                'order_value' => (float)$ord['order_value'],
                'commission_value' => (float)$ord['commission'],
                'currency' => 'VND',
                'status' => $ord['status'],
                'attribution_type' => !empty($idProduct) ? 'AUTO_MATCHED' : 'UNATTRIBUTED',
                'conversion_at' => (int)$ord['transaction_time'],
                'confirmed_at' => ($ord['status'] === self::STATUS_CONFIRMED) ? $now : null,
                'at_click_time' => (int)($ord['click_time'] ?? 0),
                'at_conversion_time' => (int)$ord['transaction_time'],
                'raw_reference' => json_encode($ord['raw_data']),
                'is_manual_matched' => 0,
                'date_created' => $now,
                'date_updated' => $now
            );

            $insId = $this->d->insert('affiliate_conversion', $convData);
            if ($insId) {
                $importedCount++;
                $totalCommissionVND += (float)$ord['commission'];
                $totalOrderValVND += (float)$ord['order_value'];

                // Project into Analytics Event Stream
                $analytics->logEvent(AnalyticsService::EVENT_CONVERSION, array(
                    'id_product' => $idProduct,
                    'id_post' => $idPost,
                    'id_video' => $idVideo,
                    'id_content' => $idContent,
                    'id_affiliate_offer' => $idOffer,
                    'tracking_code' => $ord['tracking_code'],
                    'source' => 'accesstrade',
                    'medium' => 'affiliate_api',
                    'metadata' => array(
                        'conversion_id' => $insId,
                        'external_id' => $externalId,
                        'order_value' => $ord['order_value'],
                        'commission_value' => $ord['commission'],
                        'currency' => 'VND',
                        'status' => $ord['status']
                    ),
                    'event_time' => (int)$ord['transaction_time']
                ));
            }
        }

        // Record Import History in table_conversion_import_log
        $this->d->insert('conversion_import_log', array(
            'filename' => 'ACCESSTRADE_API_SYNC_' . date('Ymd_His', $now),
            'platform' => 'accesstrade',
            'total_rows' => count($fetchRes['orders']),
            'imported_count' => $importedCount,
            'duplicate_count' => $duplicateCount,
            'failed_count' => 0,
            'total_order_value' => $totalOrderValVND,
            'total_commission' => $totalCommissionVND,
            'currency' => 'VND',
            'admin_user' => $adminUser,
            'imported_by' => $adminUser,
            'status' => 'SUCCESS',
            'summary_json' => json_encode(array(
                'updated_count' => $updatedCount,
                'unattributed_count' => $unattributedCount,
                'sync_since' => $fetchSince,
                'sync_until' => $now
            )),
            'date_created' => $now
        ));

        // Update last sync timestamp
        $this->d->rawQuery("INSERT INTO table_analytics_setting (setting_key, setting_value, setting_group, description, date_updated)
            VALUES ('accesstrade_last_sync_time', ?, 'accesstrade', 'Last sync timestamp', ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), date_updated = VALUES(date_updated)",
            array((string)$now, $now)
        );

        return array(
            'success' => true,
            'total_fetched' => count($fetchRes['orders']),
            'imported_count' => $importedCount,
            'updated_count' => $updatedCount,
            'duplicate_count' => $duplicateCount,
            'unattributed_count' => $unattributedCount,
            'total_commission_vnd' => $totalCommissionVND,
            'last_sync_time' => $now,
            'error' => null
        );
    }

    /**
     * Low-level HTTP Client for ACCESSTRADE API
     * @param string $method GET or POST
     * @param string $endpoint e.g. /v1/campaigns
     * @param array $data Query params or POST body
     * @return array ['success' => bool, 'http_code' => int, 'data' => mixed, 'error' => string]
     */
    private function request($method = 'GET', $endpoint = '', array $data = array())
    {
        if (!$this->isConfigured()) {
            return array(
                'success' => false,
                'http_code' => 0,
                'data' => null,
                'error' => 'Chưa cấu hình API Access Key cho ACCESSTRADE.'
            );
        }

        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $headers = array(
            'Authorization: Token ' . $this->accessKey,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: FITNADO-Publisher-Agent/1.0'
        );

        if (strtoupper($method) === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if (!empty($curlError)) {
            return array(
                'success' => false,
                'http_code' => $httpCode,
                'data' => null,
                'error' => 'cURL Error: ' . $curlError
            );
        }

        $decoded = json_decode($response, true);
        $isSuccess = ($httpCode >= 200 && $httpCode < 300);

        return array(
            'success' => $isSuccess,
            'http_code' => $httpCode,
            'data' => $decoded !== null ? $decoded : $response,
            'error' => $isSuccess ? null : ('HTTP Error ' . $httpCode . ($decoded['message'] ? ': ' . $decoded['message'] : ''))
        );
    }
}
