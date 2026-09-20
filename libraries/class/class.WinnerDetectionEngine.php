<?php
/**
 * FITNADO Winner Detection Engine
 * Phase 08: Analytics, Affiliate Attribution & Winner Detection
 * Rule-Based Analytics with Strict Sample Size Gating
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once LIBRARIES . 'class/class.AnalyticsService.php';

class WinnerDetectionEngine {
    private $d;
    private $analytics;

    // Performance Maturity Statuses (Phase 09 Clean Separation)
    const STATUS_INSUFFICIENT_DATA    = 'INSUFFICIENT_DATA';
    const STATUS_TRAFFIC_PROMISING    = 'TRAFFIC_PROMISING';
    const STATUS_CLICK_PROMISING      = 'CLICK_PROMISING';
    const STATUS_CONVERSION_PROMISING = 'CONVERSION_PROMISING';
    const STATUS_REVENUE_WINNER       = 'REVENUE_WINNER';
    const STATUS_UNDERPERFORMING      = 'UNDERPERFORMING';
    const STATUS_TESTING              = 'TESTING';
    const STATUS_PROMISING            = 'CLICK_PROMISING'; // Alias for backward compatibility
    const STATUS_WINNER               = 'REVENUE_WINNER';  // Alias for backward compatibility

    // Signal Levels
    const SIGNAL_NONE              = 'NONE';
    const SIGNAL_TRAFFIC           = 'TRAFFIC_SIGNAL';
    const SIGNAL_CLICK             = 'CLICK_SIGNAL';
    const SIGNAL_CONVERSION        = 'CONVERSION_SIGNAL';
    const SIGNAL_REVENUE           = 'REVENUE_SIGNAL';

    // Business Recommendations
    const REC_KEEP_TESTING             = 'KEEP_TESTING';
    const REC_REVIEW_PRODUCT_PAGE      = 'REVIEW_PRODUCT_PAGE';
    const REC_CREATE_VARIATION         = 'CREATE_CONTENT_VARIATION';
    const REC_CREATE_NEW_HOOK          = 'CREATE_NEW_HOOK';
    const REC_CREATE_VIDEO_VARIATION   = 'CREATE_VIDEO_VARIATION';
    const REC_UPGRADE_TO_HYBRID        = 'UPGRADE_TO_HYBRID';
    const REC_RETEST_PRODUCT           = 'RETEST_PRODUCT';
    const REC_STOP_TESTING             = 'PAUSE_TESTING';
    const REC_WAIT_FOR_MORE_DATA       = 'WAIT_FOR_MORE_DATA';

    public function __construct($d = null, $analytics = null) {
        $this->d = $d;
        $this->analytics = $analytics ?: new AnalyticsService($d);
    }

    /**
     * Get Current Active Rules Configuration
     * @return array
     */
    public function getRulesConfig() {
        return array(
            'rules_version' => 'v2.0_maturity',
            'min_landing_sessions' => (int)$this->analytics->getSetting('min_landing_sessions', 30),
            'min_affiliate_clicks' => (int)$this->analytics->getSetting('min_affiliate_clicks', 10),
            'min_conversions' => (int)$this->analytics->getSetting('min_conversions', 2),
            'min_test_age_days' => (int)$this->analytics->getSetting('min_test_age_days', 3),
            'promising_ctr_pct' => (float)$this->analytics->getSetting('promising_ctr_pct', 5.0),
            'winner_ctr_pct' => (float)$this->analytics->getSetting('winner_ctr_pct', 10.0),
            'winner_cvr_pct' => (float)$this->analytics->getSetting('winner_cvr_pct', 5.0),
            'underperforming_ctr_pct' => (float)$this->analytics->getSetting('underperforming_ctr_pct', 1.0)
        );
    }

    /**
     * Check if conversion source data is connected in the system or for this product
     * @param int|null $productId
     * @return bool
     */
    public function isConversionSourceConnected($productId = null) {
        if (!$this->d) return false;
        if ($productId) {
            $row = $this->d->rawQueryOne("SELECT id FROM table_affiliate_conversion WHERE id_product = ? LIMIT 1", array((int)$productId));
            if (!empty($row)) return true;
        }
        $globalRow = $this->d->rawQueryOne("SELECT id FROM table_affiliate_conversion LIMIT 1");
        return !empty($globalRow);
    }

    /**
     * Evaluate a Product's Performance against Rule-based Thresholds
     * Strict Sample Size Gate & Data Source Gate
     * @param int $productId
     * @param string $evaluatedBy Admin username or 'cli_worker'
     * @param bool $saveSnapshot Whether to record into table_winner_evaluation
     * @param string|null $aiAnalysis Optional commentary labeled AI_ANALYSIS
     * @return array Evaluation result
     */
    public function evaluateProduct($productId, $evaluatedBy = 'admin', $saveSnapshot = true, $aiAnalysis = null) {
        $productId = (int)$productId;
        if (!$productId || !$this->d) {
            return array('success' => false, 'error' => 'ID sản phẩm không hợp lệ.');
        }

        $rules = $this->getRulesConfig();

        // Query product details & creation time
        $product = $this->d->rawQueryOne("SELECT * FROM table_product WHERE id = ? LIMIT 1", array($productId));
        if (empty($product)) {
            return array('success' => false, 'error' => "Không tìm thấy sản phẩm #{$productId}.");
        }

        // Aggregate Product Observed Metrics (All-time or test window)
        $metricsList = $this->analytics->getProductMetrics($productId, 'all');
        $m = !empty($metricsList[0]) ? $metricsList[0] : array();

        $sessions = (int)($m['sessions'] ?? 0);
        $clicks = (int)($m['clicks'] ?? 0);
        $views = (int)($m['views'] ?? 0);
        $conversions = (int)($m['conversions'] ?? 0);
        $commissionVND = (float)($m['commission_vnd'] ?? 0.0);
        $contentCostVND = (float)($m['content_cost_vnd'] ?? 0.0);
        $ctr = (float)($m['ctr_pct'] ?? 0.0);
        $cvr = (float)($m['cvr_pct'] ?? 0.0);
        $roi = $commissionVND - $contentCostVND;

        // Check if conversion data source is connected
        $conversionSourceConnected = $this->isConversionSourceConnected($productId);

        // Calculate test age
        $dateCreated = (int)($product['date_created'] ?? time());
        $testAgeDays = max(1, (int)round((time() - $dateCreated) / 86400));

        // 1. SAMPLE SIZE GATE CHECK
        // If traffic and clicks are below minimum gates, NEVER declare WINNER or LOSER
        if ($sessions < $rules['min_landing_sessions'] || $clicks < $rules['min_affiliate_clicks']) {
            if ($sessions >= $rules['min_landing_sessions'] && $clicks < $rules['min_affiliate_clicks']) {
                $status = self::STATUS_TRAFFIC_PROMISING;
                $signal = self::SIGNAL_TRAFFIC;
                $recommendations = array(
                    'action' => self::REC_REVIEW_PRODUCT_PAGE,
                    'title' => 'Lượng truy cập tốt nhưng ít click (Traffic Promising)',
                    'reason' => "Sản phẩm đạt {$sessions} sessions nhưng chỉ có {$clicks} click affiliate (CTR {$ctr}%).",
                    'next_step' => 'Đề xuất tối ưu hóa vị trí nút Affiliate CTA, nội dung trang sản phẩm hoặc cập nhật ưu đãi/mã giảm giá.'
                );
            } else {
                $status = self::STATUS_INSUFFICIENT_DATA;
                $signal = ($sessions > 0 || $clicks > 0) ? self::SIGNAL_TRAFFIC : self::SIGNAL_NONE;
                $recommendations = array(
                    'action' => self::REC_WAIT_FOR_MORE_DATA,
                    'title' => 'Cần thêm dữ liệu kiểm thử (Insufficient Data)',
                    'reason' => "Sản phẩm mới đạt {$sessions}/{$rules['min_landing_sessions']} sessions và {$clicks}/{$rules['min_affiliate_clicks']} clicks. Chưa đủ độ lớn mẫu để kết luận.",
                    'next_step' => 'Tiếp tục phân phối video hiện tại để đạt mẫu kiểm thử tối thiểu.'
                );
            }
        } else {
            // Sample size is adequate. Evaluate Performance Metrics & Maturity Level.
            
            // Determine Signal Level
            if ($conversionSourceConnected && $commissionVND > $contentCostVND && $conversions >= $rules['min_conversions']) {
                $signal = self::SIGNAL_REVENUE;
            } elseif ($conversionSourceConnected && $conversions >= $rules['min_conversions']) {
                $signal = self::SIGNAL_CONVERSION;
            } elseif ($ctr >= $rules['promising_ctr_pct']) {
                $signal = self::SIGNAL_CLICK;
            } else {
                $signal = self::SIGNAL_TRAFFIC;
            }

            // Determine Maturity Status & Recommendations
            if ($conversionSourceConnected && $conversions >= $rules['min_conversions'] && $commissionVND > $contentCostVND) {
                // 1. REVENUE_WINNER Status (Validated Revenue & Positive ROI)
                $status = self::STATUS_REVENUE_WINNER;
                $recommendations = array(
                    'action' => self::REC_UPGRADE_TO_HYBRID,
                    'title' => 'Sản phẩm Thắng Doanh thu (Revenue Winner)',
                    'reason' => "Sản phẩm tạo {$conversions} đơn hàng, hoa hồng đạt " . number_format($commissionVND, 0, ',', '.') . " đ vượt chi phí sản xuất (ROI " . number_format($roi, 0, ',', '.') . " đ).",
                    'next_step' => 'Đề xuất nâng cấp sản xuất video sang chế độ HYBRID và tạo thêm biến thể kịch bản/Hook để mở rộng quy mô.'
                );
            } elseif ($conversionSourceConnected && $conversions >= $rules['min_conversions']) {
                // 2. CONVERSION_PROMISING Status (Orders Confirmed)
                $status = self::STATUS_CONVERSION_PROMISING;
                $recommendations = array(
                    'action' => self::REC_CREATE_VARIATION,
                    'title' => 'Sản phẩm Tiềm năng Chuyển đổi (Conversion Promising)',
                    'reason' => "Sản phẩm đã tạo {$conversions} chuyển đổi trên sàn TMĐT với CVR đạt {$cvr}%.",
                    'next_step' => 'Tạo thêm biến thể nội dung góc tiếp cận mới để tối ưu hóa tỷ lệ chuyển đổi.'
                );
            } elseif ($ctr >= $rules['promising_ctr_pct']) {
                // 3. CLICK_PROMISING Status (Strong Click Signal, No/Pending Conversion Data)
                $status = self::STATUS_CLICK_PROMISING;
                $recommendations = array(
                    'action' => self::REC_KEEP_TESTING,
                    'title' => 'Sản phẩm Tiềm năng Lượt click (Click Promising)',
                    'reason' => "CTR đạt {$ctr}% (vượt ngưỡng {$rules['promising_ctr_pct']}%) với {$clicks} clicks. Ý định mua hàng cao." . (!$conversionSourceConnected ? " (Chưa kết nối dữ liệu đơn hàng sàn TMĐT)." : ""),
                    'next_step' => 'Tiếp tục theo dõi và nhập file CSV đối soát đơn hàng để kiểm tra doanh thu thực tế.'
                );
            } elseif ($ctr < $rules['underperforming_ctr_pct'] && $testAgeDays >= $rules['min_test_age_days']) {
                // 4. UNDERPERFORMING Status
                $status = self::STATUS_UNDERPERFORMING;
                $recommendations = array(
                    'action' => self::REC_CREATE_NEW_HOOK,
                    'title' => 'Hiệu suất thấp (Underperforming)',
                    'reason' => "Đã có {$sessions} sessions nhưng CTR chỉ đạt {$ctr}% (< {$rules['underperforming_ctr_pct']}%).",
                    'next_step' => 'Thử nghiệm Hook mở đầu gây tò mò hơn hoặc cân nhắc tạm dừng nếu nội dung không thu hút.'
                );
            } else {
                // 5. TESTING Status
                $status = self::STATUS_TESTING;
                $recommendations = array(
                    'action' => self::REC_KEEP_TESTING,
                    'title' => 'Đang trong giai đoạn theo dõi (Active Testing)',
                    'reason' => "CTR đạt {$ctr}%, đang tiệm cận ngưỡng tiềm năng. Cần theo dõi thêm chuyển đổi.",
                    'next_step' => 'Giữ nguyên chiến dịch phân phối và cập nhật đơn hàng đối soát CSV định kỳ.'
                );
            }
        }

        $metricsSnapshot = array(
            'sessions' => $sessions,
            'views' => $views,
            'clicks' => $clicks,
            'ctr_pct' => $ctr,
            'conversions' => $conversions,
            'cvr_pct' => $cvr,
            'commission_vnd' => $commissionVND,
            'content_cost_vnd' => $contentCostVND,
            'roi_vnd' => $roi,
            'test_age_days' => $testAgeDays
        );

        $evalRecord = array(
            'id_product' => $productId,
            'id_post' => null,
            'id_video' => null,
            'id_content' => null,
            'winner_status' => $status,
            'signal_level' => $signal,
            'metrics_snapshot' => json_encode($metricsSnapshot),
            'rules_snapshot' => json_encode($rules),
            'recommendation' => json_encode($recommendations),
            'ai_analysis' => !empty($aiAnalysis) ? ("[AI_ANALYSIS] " . trim($aiAnalysis)) : null,
            'evaluated_at' => time(),
            'evaluated_by' => $evaluatedBy,
            'date_created' => time()
        );

        $evalId = null;
        if ($saveSnapshot) {
            $evalId = $this->d->insert('winner_evaluation', $evalRecord);
        }

        return array(
            'success' => true,
            'evaluation_id' => $evalId,
            'id_product' => $productId,
            'product_name' => $product['namevi'],
            'status' => $status,
            'signal_level' => $signal,
            'metrics' => $metricsSnapshot,
            'rules' => $rules,
            'recommendation' => $recommendations,
            'ai_analysis' => $evalRecord['ai_analysis'],
            'evaluated_at' => $evalRecord['evaluated_at'],
            'evaluated_by' => $evaluatedBy
        );
    }

    /**
     * Run Batch Evaluation for All Active Products
     * @param string $evaluatedBy
     * @return array Summary of evaluations
     */
    public function evaluateAllProducts($evaluatedBy = 'admin') {
        if (!$this->d) return array('success' => false, 'error' => 'Chưa kết nối CSDL.');

        $products = $this->d->rawQuery("SELECT id FROM table_product WHERE find_in_set('hienthi', status)");
        if (empty($products)) {
            return array('success' => true, 'evaluated_count' => 0, 'results' => array());
        }

        $results = array();
        $counts = array(
            self::STATUS_INSUFFICIENT_DATA => 0,
            self::STATUS_TRAFFIC_PROMISING => 0,
            self::STATUS_CLICK_PROMISING => 0,
            self::STATUS_CONVERSION_PROMISING => 0,
            self::STATUS_REVENUE_WINNER => 0,
            self::STATUS_TESTING => 0,
            self::STATUS_UNDERPERFORMING => 0
        );

        foreach ($products as $p) {
            $eval = $this->evaluateProduct($p['id'], $evaluatedBy, true);
            if (!empty($eval['success'])) {
                $status = $eval['status'];
                if (isset($counts[$status])) $counts[$status]++;
                $results[] = $eval;
            }
        }

        return array(
            'success' => true,
            'evaluated_count' => count($results),
            'counts' => $counts,
            'results' => $results
        );
    }

    /**
     * Get Evaluation History for a Product
     * @param int $productId
     * @param int $limit
     * @return array
     */
    public function getEvaluationHistory($productId, $limit = 10) {
        if (!$this->d) return array();
        $productId = (int)$productId;
        $rows = $this->d->rawQuery(
            "SELECT * FROM table_winner_evaluation WHERE id_product = ? ORDER BY id DESC LIMIT ?",
            array($productId, (int)$limit)
        );

        if (!empty($rows)) {
            foreach ($rows as &$r) {
                $r['metrics_snapshot'] = json_decode($r['metrics_snapshot'] ?? '{}', true);
                $r['rules_snapshot'] = json_decode($r['rules_snapshot'] ?? '{}', true);
                $r['recommendation'] = json_decode($r['recommendation'] ?? '{}', true);
            }
        }

        return $rows ?: array();
    }
}
