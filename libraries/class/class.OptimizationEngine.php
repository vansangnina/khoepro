<?php
/**
 * FITNADO Optimization Engine
 * Phase 09: Data-Driven Optimization Loop
 * Rule-Based Decision Core with Strict Human Gate & Cost Gate
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once LIBRARIES . 'class/class.AnalyticsService.php';
require_once LIBRARIES . 'class/class.WinnerDetectionEngine.php';

class OptimizationEngine {
    private $d;
    private $func;
    private $analytics;
    private $winnerEngine;

    // Recommendation Types
    const REC_KEEP_TESTING             = 'KEEP_TESTING';
    const REC_REVIEW_PRODUCT_PAGE      = 'REVIEW_PRODUCT_PAGE';
    const REC_CREATE_NEW_HOOK          = 'CREATE_NEW_HOOK';
    const REC_CREATE_CONTENT_VARIATION = 'CREATE_CONTENT_VARIATION';
    const REC_CREATE_VIDEO_VARIATION   = 'CREATE_VIDEO_VARIATION';
    const REC_UPGRADE_TO_HYBRID        = 'UPGRADE_TO_HYBRID';
    const REC_RETEST_PRODUCT           = 'RETEST_PRODUCT';
    const REC_PAUSE_TESTING            = 'PAUSE_TESTING';
    const REC_WAIT_FOR_MORE_DATA       = 'WAIT_FOR_MORE_DATA';

    // Reason Codes
    const REASON_INSUFFICIENT_SAMPLE       = 'INSUFFICIENT_SAMPLE';
    const REASON_HIGH_TRAFFIC_LOW_CLICK    = 'HIGH_TRAFFIC_LOW_CLICK';
    const REASON_HIGH_CLICK_NO_CONVERSION  = 'HIGH_CLICK_NO_CONVERSION';
    const REASON_STRONG_CONVERSION_SIGNAL  = 'STRONG_CONVERSION_SIGNAL';
    const REASON_POSITIVE_ROI              = 'POSITIVE_ROI';
    const REASON_LOW_CTR_POST              = 'LOW_CTR_POST';
    const REASON_TRACKING_DEGRADED         = 'TRACKING_DEGRADED';

    // Changed Variables (One-Variable Principle)
    const VAR_HOOK         = 'HOOK';
    const VAR_CTA          = 'CTA';
    const VAR_SCRIPT       = 'SCRIPT';
    const VAR_VIDEO_STYLE  = 'VIDEO_STYLE';
    const VAR_VOICE        = 'VOICE';
    const VAR_OFFER        = 'OFFER';
    const VAR_MULTIVARIATE = 'MULTIVARIATE';

    // Recommendation Statuses
    const REC_STATUS_PENDING    = 'PENDING';
    const REC_STATUS_APPROVED   = 'APPROVED';
    const REC_STATUS_REJECTED   = 'REJECTED';
    const REC_STATUS_STALE      = 'STALE';
    const REC_STATUS_EXECUTING  = 'EXECUTING';
    const REC_STATUS_COMPLETED  = 'COMPLETED';
    const REC_STATUS_CANCELLED  = 'CANCELLED';

    // Experiment Statuses
    const EXP_STATUS_DRAFT       = 'DRAFT';
    const EXP_STATUS_APPROVED    = 'APPROVED';
    const EXP_STATUS_RUNNING     = 'RUNNING';
    const EXP_STATUS_ENOUGH_DATA = 'ENOUGH_DATA';
    const EXP_STATUS_COMPLETED   = 'COMPLETED';
    const EXP_STATUS_CANCELLED   = 'CANCELLED';

    // Experiment Results
    const RESULT_INSUFFICIENT_DATA = 'INSUFFICIENT_DATA';
    const RESULT_BASELINE_BETTER   = 'BASELINE_BETTER';
    const RESULT_VARIATION_BETTER  = 'VARIATION_BETTER';
    const RESULT_NO_DIFFERENCE     = 'NO_MEANINGFUL_DIFFERENCE';

    public function __construct($d = null, $func = null, $analytics = null, $winnerEngine = null) {
        $this->d = $d;
        $this->func = $func;
        $this->analytics = $analytics ?: new AnalyticsService($d, $func);
        $this->winnerEngine = $winnerEngine ?: new WinnerDetectionEngine($d, $this->analytics);
    }

    /**
     * Get Optimization Rules & Thresholds
     * @return array
     */
    public function getRules() {
        return array(
            'rules_version' => 'v1.0_optimization',
            'max_cost_per_experiment' => (float)$this->analytics->getSetting('max_cost_per_experiment', 60000.0),
            'max_active_experiments_per_product' => (int)$this->analytics->getSetting('max_active_experiments_per_product', 2),
            'recommendation_cooldown_hours' => (int)$this->analytics->getSetting('recommendation_cooldown_hours', 24),
            'experiment_min_sessions' => (int)$this->analytics->getSetting('experiment_min_sessions', 30),
            'experiment_min_clicks' => (int)$this->analytics->getSetting('experiment_min_clicks', 10),
            'min_landing_sessions' => (int)$this->analytics->getSetting('min_landing_sessions', 30),
            'min_affiliate_clicks' => (int)$this->analytics->getSetting('min_affiliate_clicks', 10),
            'promising_ctr_pct' => (float)$this->analytics->getSetting('promising_ctr_pct', 5.0),
            'winner_ctr_pct' => (float)$this->analytics->getSetting('winner_ctr_pct', 10.0),
            'winner_cvr_pct' => (float)$this->analytics->getSetting('winner_cvr_pct', 5.0),
            'underperforming_ctr_pct' => (float)$this->analytics->getSetting('underperforming_ctr_pct', 1.0)
        );
    }

    /**
     * Count currently active running experiments for a product
     * @param int $productId
     * @return int
     */
    public function countActiveExperimentsForProduct($productId) {
        if (!$this->d) return 0;
        $row = $this->d->rawQueryOne(
            "SELECT COUNT(id) as total FROM table_optimization_experiment WHERE id_product = ? AND status IN ('APPROVED', 'RUNNING')",
            array((int)$productId)
        );
        return !empty($row['total']) ? (int)$row['total'] : 0;
    }

    /**
     * Check if a recent pending/approved recommendation exists for product (Cooldown & Deduplication)
     * @param int $productId
     * @param string $recType
     * @param int $cooldownHours
     * @return bool
     */
    public function hasRecentRecommendation($productId, $recType, $cooldownHours = 24) {
        if (!$this->d) return false;
        $cutoff = time() - ($cooldownHours * 3600);
        $row = $this->d->rawQueryOne(
            "SELECT id FROM table_optimization_recommendation 
             WHERE id_product = ? AND recommendation_type = ? AND (status IN ('PENDING', 'APPROVED', 'EXECUTING') OR date_created >= ?) LIMIT 1",
            array((int)$productId, $recType, $cutoff)
        );
        return !empty($row);
    }

    /**
     * Generate Optimization Recommendations for a product or all active products
     * Rule-Based Logic with Sample Gates, Cooldown & Cost Estimations
     * @param int|null $productId
     * @return array List of generated recommendations
     */
    public function generateRecommendations($productId = null) {
        if (!$this->d) return array();

        $rules = $this->getRules();
        $cooldownHours = $rules['recommendation_cooldown_hours'];
        $maxActiveExp = $rules['max_active_experiments_per_product'];

        // 1. Fetch target products
        $productWhere = "";
        $params = array();
        if ($productId) {
            $productWhere = " WHERE id = ?";
            $params[] = (int)$productId;
        } else {
            $productWhere = " WHERE find_in_set('hienthi', status)";
        }

        $products = $this->d->rawQuery("SELECT id, namevi, slugvi, code, date_created FROM table_product {$productWhere} ORDER BY id DESC", $params);
        if (empty($products)) return array();

        $recommendations = array();

        foreach ($products as $prod) {
            $pid = (int)$prod['id'];

            // Check active experiment limit
            $activeExpCount = $this->countActiveExperimentsForProduct($pid);
            if ($activeExpCount >= $maxActiveExp) {
                continue; // Skip if max concurrent experiments reached
            }

            // Get product observed metrics & maturity evaluation
            $metricsList = $this->analytics->getProductMetrics($pid, 'all');
            $m = !empty($metricsList[0]) ? $metricsList[0] : array();

            $sessions = (int)($m['sessions'] ?? 0);
            $clicks = (int)($m['clicks'] ?? 0);
            $conversions = (int)($m['conversions'] ?? 0);
            $commissionVND = (float)($m['commission_vnd'] ?? 0.0);
            $contentCostVND = (float)($m['content_cost_vnd'] ?? 0.0);
            $ctr = (float)($m['ctr_pct'] ?? 0.0);
            $cvr = (float)($m['cvr_pct'] ?? 0.0);
            $roi = $commissionVND - $contentCostVND;

            $dateCreated = (int)($prod['date_created'] ?? time());
            $testAgeDays = max(1, (int)round((time() - $dateCreated) / 86400));

            // Check conversion source connectivity
            $conversionConnected = $this->winnerEngine->isConversionSourceConnected($pid);

            // Fetch baseline Post, Video, Content if available
            $baselinePost = $this->d->rawQueryOne("SELECT * FROM table_publish_post WHERE id_product = ? AND status = 'PUBLISHED' ORDER BY id DESC LIMIT 1", array($pid));
            $baselineVideo = !empty($baselinePost['id_video']) ? $this->d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array((int)$baselinePost['id_video'])) : null;
            $baselineContent = !empty($baselinePost['id_ai_content']) ? $this->d->rawQueryOne("SELECT * FROM table_ai_content WHERE id = ? LIMIT 1", array((int)$baselinePost['id_ai_content'])) : null;

            $idBaselinePost = !empty($baselinePost['id']) ? (int)$baselinePost['id'] : null;
            $idBaselineVideo = !empty($baselineVideo['id']) ? (int)$baselineVideo['id'] : null;
            $idBaselineContent = !empty($baselineContent['id']) ? (int)$baselineContent['id'] : null;

            // Decision Logic
            $recType = null;
            $reasonCode = null;
            $reasonSummary = '';
            $hypothesis = '';
            $proposedVariable = self::VAR_HOOK;
            $targetMode = 'ECONOMY';
            $estimatedCost = 0.0;

            if ($sessions < $rules['min_landing_sessions'] || $clicks < $rules['min_affiliate_clicks']) {
                if ($sessions >= $rules['min_landing_sessions'] && $clicks < $rules['min_affiliate_clicks']) {
                    // Traffic is adequate, but clicks are poor
                    $recType = self::REC_REVIEW_PRODUCT_PAGE;
                    $reasonCode = self::REASON_HIGH_TRAFFIC_LOW_CLICK;
                    $proposedVariable = self::VAR_CTA;
                    $targetMode = 'ECONOMY';
                    $estimatedCost = 0.0;
                    $reasonSummary = "Sản phẩm đạt {$sessions} lượt truy cập nhưng chỉ phát sinh {$clicks} click affiliate (CTR {$ctr}% < {$rules['min_affiliate_clicks']} clicks).";
                    $hypothesis = "Điều chỉnh vị trí nút CTA Affiliate hoặc cập nhật ưu đãi/mã giảm giá sẽ tăng tỷ lệ click của người xem quan tâm.";
                } else {
                    // Insufficient sample size
                    $recType = self::REC_WAIT_FOR_MORE_DATA;
                    $reasonCode = self::REASON_INSUFFICIENT_SAMPLE;
                    $proposedVariable = self::VAR_HOOK;
                    $targetMode = 'ECONOMY';
                    $estimatedCost = 0.0;
                    $reasonSummary = "Sản phẩm mới đạt {$sessions}/{$rules['min_landing_sessions']} sessions và {$clicks}/{$rules['min_affiliate_clicks']} clicks. Chưa đủ độ lớn mẫu để kết luận tối ưu.";
                    $hypothesis = "Tiếp tục phân phối để thu thập đủ mẫu kiểm thử tối thiểu trước khi thực hiện thay đổi.";
                }
            } else {
                // Adequate sample size
                if ($conversionConnected && $conversions >= $rules['min_conversions'] && $commissionVND > $contentCostVND) {
                    // REVENUE WINNER -> Upgrade to HYBRID or Test Variations
                    $recType = self::REC_UPGRADE_TO_HYBRID;
                    $reasonCode = self::REASON_POSITIVE_ROI;
                    $proposedVariable = self::VAR_VIDEO_STYLE;
                    $targetMode = 'HYBRID';
                    $estimatedCost = 50000.0; // Estimated 1 AI scene
                    $reasonSummary = "Sản phẩm đạt {$conversions} đơn hàng, hoa hồng " . number_format($commissionVND, 0, ',', '.') . " đ vượt chi phí sản xuất (ROI " . number_format($roi, 0, ',', '.') . " đ).";
                    $hypothesis = "Nâng cấp lên video HYBRID với 1 phân cảnh động trực quan sẽ gia tăng độ tin cậy và thúc đẩy doanh thu đột phá.";
                } elseif ($conversionConnected && $conversions >= $rules['min_conversions']) {
                    // CONVERSION PROMISING -> Create Content/Video Variation
                    $recType = self::REC_CREATE_CONTENT_VARIATION;
                    $reasonCode = self::REASON_STRONG_CONVERSION_SIGNAL;
                    $proposedVariable = self::VAR_HOOK;
                    $targetMode = 'ECONOMY';
                    $estimatedCost = 0.0;
                    $reasonSummary = "Sản phẩm có tín hiệu chuyển đổi tốt ({$conversions} đơn hàng, CVR {$cvr}%).";
                    $hypothesis = "Thử nghiệm góc nội dung so sánh chi phí (Cost-Comparison Hook) sẽ tiếp cận tệp khách hàng có ý định mua cao hơn.";
                } elseif ($ctr >= $rules['promising_ctr_pct']) {
                    // CLICK PROMISING -> Create New Hook Variation (Economy)
                    $recType = self::REC_CREATE_NEW_HOOK;
                    $reasonCode = self::REASON_HIGH_CLICK_NO_CONVERSION;
                    $proposedVariable = self::VAR_HOOK;
                    $targetMode = 'ECONOMY';
                    $estimatedCost = 0.0;
                    $reasonSummary = "CTR đạt {$ctr}% với {$clicks} clicks. Ý định xem và mua hàng từ TikTok cao" . (!$conversionConnected ? " (Chưa kết nối dữ liệu đơn hàng sàn TMĐT)." : ".");
                    $hypothesis = "Tạo biến thể kịch bản với Hook mở đầu tập trung vào lỗi sai (Mistake Hook) sẽ tăng tỷ lệ giữ chân và kích thích hành vi mua.";
                } elseif ($ctr < $rules['underperforming_ctr_pct'] && $testAgeDays >= $rules['min_test_age_days']) {
                    // UNDERPERFORMING -> Retest with new Hook or Pause
                    $recType = self::REC_CREATE_NEW_HOOK;
                    $reasonCode = self::REASON_LOW_CTR_POST;
                    $proposedVariable = self::VAR_HOOK;
                    $targetMode = 'ECONOMY';
                    $estimatedCost = 0.0;
                    $reasonSummary = "Sau {$testAgeDays} ngày và {$sessions} sessions, CTR chỉ đạt {$ctr}% (< {$rules['underperforming_ctr_pct']}%).";
                    $hypothesis = "Hook mở đầu hiện tại chưa thu hút sự chú ý trong 3 giây đầu. Thử nghiệm Hook Demo trực quan để cải thiện CTR.";
                } else {
                    // Active Testing
                    $recType = self::REC_KEEP_TESTING;
                    $reasonCode = self::REASON_INSUFFICIENT_SAMPLE;
                    $proposedVariable = self::VAR_HOOK;
                    $targetMode = 'ECONOMY';
                    $estimatedCost = 0.0;
                    $reasonSummary = "Sản phẩm đang trong quá trình theo dõi chuyển đổi (CTR {$ctr}%).";
                    $hypothesis = "Giữ nguyên chiến dịch phân phối và cập nhật đối soát đơn hàng định kỳ.";
                }
            }

            // Check Cooldown & Deduplication
            if ($this->hasRecentRecommendation($pid, $recType, $cooldownHours)) {
                continue; // Skip duplicate recommendation in cooldown period
            }

            $metricsSnapshot = array(
                'sessions' => $sessions,
                'clicks' => $clicks,
                'conversions' => $conversions,
                'ctr_pct' => $ctr,
                'cvr_pct' => $cvr,
                'commission_vnd' => $commissionVND,
                'content_cost_vnd' => $contentCostVND,
                'roi_vnd' => $roi,
                'test_age_days' => $testAgeDays,
                'conversion_connected' => $conversionConnected
            );

            $recData = array(
                'id_product' => $pid,
                'id_post' => $idBaselinePost,
                'id_video' => $idBaselineVideo,
                'id_content' => $idBaselineContent,
                'recommendation_type' => $recType,
                'reason_code' => $reasonCode,
                'reason_summary' => $reasonSummary,
                'hypothesis' => $hypothesis,
                'proposed_variable' => $proposedVariable,
                'target_mode' => $targetMode,
                'estimated_cost_vnd' => $estimatedCost,
                'metrics_snapshot' => json_encode($metricsSnapshot),
                'rules_snapshot' => json_encode($rules),
                'status' => self::REC_STATUS_PENDING,
                'date_created' => time()
            );

            $recId = $this->d->insert('optimization_recommendation', $recData);
            if ($recId) {
                $recData['id'] = $recId;
                $recommendations[] = $recData;
            }
        }

        return $recommendations;
    }

    /**
     * Approve a recommendation and initiate Experiment Creation
     * Hard Cost Gate Check included
     * @param int $recommendationId
     * @param string $adminUser
     * @param string $notes
     * @param bool $adminOverride
     * @return array [success => bool, error => string, experiment_id => int]
     */
    public function approveRecommendation($recommendationId, $adminUser = 'admin', $notes = '', $adminOverride = false) {
        $recommendationId = (int)$recommendationId;
        if (!$this->d || !$recommendationId) {
            return array('success' => false, 'error' => 'ID khuyến nghị không hợp lệ.');
        }

        $rec = $this->d->rawQueryOne("SELECT * FROM table_optimization_recommendation WHERE id = ? LIMIT 1", array($recommendationId));
        if (empty($rec)) {
            return array('success' => false, 'error' => 'Không tìm thấy khuyến nghị.');
        }

        if ($rec['status'] !== self::REC_STATUS_PENDING && $rec['status'] !== self::REC_STATUS_STALE) {
            return array('success' => false, 'error' => "Khuyến nghị đang ở trạng thái [{$rec['status']}], không thể phê duyệt.");
        }

        $rules = $this->getRules();
        $estimatedCost = (float)$rec['estimated_cost_vnd'];
        $maxCost = (float)$rules['max_cost_per_experiment'];

        // HARD COST GATE
        if ($estimatedCost > $maxCost && !$adminOverride) {
            return array(
                'success' => false,
                'error' => "Chi phí ước tính (" . number_format($estimatedCost, 0, ',', '.') . " đ) vượt quá hạn mức cho phép (" . number_format($maxCost, 0, ',', '.') . " đ). Yêu cầu Admin Override để duyệt."
            );
        }

        // Update recommendation status to APPROVED
        $now = time();
        $this->d->where('id', $recommendationId);
        $this->d->update('optimization_recommendation', array(
            'status' => self::REC_STATUS_APPROVED,
            'approved_by' => $adminUser,
            'approved_at' => $now,
            'review_notes' => $notes,
            'date_updated' => $now
        ));

        // Create Experiment from approved recommendation
        $expRes = $this->createExperimentFromRecommendation($recommendationId, $adminUser);

        return $expRes;
    }

    /**
     * Reject a recommendation
     * @param int $recommendationId
     * @param string $adminUser
     * @param string $reason
     * @return array [success => bool, error => string]
     */
    public function rejectRecommendation($recommendationId, $adminUser = 'admin', $reason = '') {
        $recommendationId = (int)$recommendationId;
        if (!$this->d || !$recommendationId) {
            return array('success' => false, 'error' => 'ID khuyến nghị không hợp lệ.');
        }

        $now = time();
        $this->d->where('id', $recommendationId);
        $res = $this->d->update('optimization_recommendation', array(
            'status' => self::REC_STATUS_REJECTED,
            'approved_by' => $adminUser,
            'approved_at' => $now,
            'review_notes' => $reason,
            'date_updated' => $now
        ));

        return array('success' => (bool)$res);
    }

    /**
     * Create Experiment from Approved Recommendation
     * Generates Content Variation, Video Variation, and Publishing Post
     * @param int $recommendationId
     * @param string $adminUser
     * @return array
     */
    public function createExperimentFromRecommendation($recommendationId, $adminUser = 'admin') {
        if (!$this->d) return array('success' => false, 'error' => 'CSDL chưa kết nối.');

        $rec = $this->d->rawQueryOne("SELECT * FROM table_optimization_recommendation WHERE id = ? LIMIT 1", array((int)$recommendationId));
        if (empty($rec)) return array('success' => false, 'error' => 'Không tìm thấy khuyến nghị.');

        $pid = (int)$rec['id_product'];
        $product = $this->d->rawQueryOne("SELECT * FROM table_product WHERE id = ? LIMIT 1", array($pid));
        if (empty($product)) return array('success' => false, 'error' => 'Không tìm thấy sản phẩm.');

        $randHex = bin2hex(random_bytes(3));
        $expCode = 'exp_prod' . $pid . '_' . strtolower($rec['proposed_variable']) . '_' . $randHex;
        $now = time();

        // Baseline IDs
        $idBaselinePost = !empty($rec['id_post']) ? (int)$rec['id_post'] : null;
        $idBaselineVideo = !empty($rec['id_video']) ? (int)$rec['id_video'] : null;
        $idBaselineContent = !empty($rec['id_content']) ? (int)$rec['id_content'] : null;

        // Fetch baseline metrics snapshot
        $metricsList = $this->analytics->getProductMetrics($pid, 'all');
        $baselineMetrics = !empty($metricsList[0]) ? $metricsList[0] : json_decode($rec['metrics_snapshot'], true);

        // 1. Create Experiment record in DRAFT/APPROVED
        $expData = array(
            'experiment_code' => $expCode,
            'id_recommendation' => (int)$rec['id'],
            'id_product' => $pid,
            'changed_variable' => $rec['proposed_variable'],
            'hypothesis' => $rec['hypothesis'],
            'baseline_type' => 'POST',
            'id_baseline_post' => $idBaselinePost,
            'id_baseline_video' => $idBaselineVideo,
            'id_baseline_content' => $idBaselineContent,
            'id_variation_content' => null,
            'id_variation_video' => null,
            'id_variation_post' => null,
            'target_mode' => $rec['target_mode'],
            'status' => self::EXP_STATUS_APPROVED,
            'baseline_metrics_snapshot' => json_encode($baselineMetrics),
            'variation_metrics_snapshot' => null,
            'result_conclusion' => null,
            'result_summary' => null,
            'total_cost_vnd' => (float)$rec['estimated_cost_vnd'],
            'started_at' => $now,
            'created_by' => $adminUser,
            'date_created' => $now
        );

        $expId = $this->d->insert('optimization_experiment', $expData);
        if (!$expId) {
            return array('success' => false, 'error' => 'Không thể tạo bản ghi thử nghiệm.');
        }

        // Link experiment to recommendation
        $this->d->where('id', $rec['id']);
        $this->d->update('optimization_recommendation', array(
            'id_experiment' => $expId,
            'status' => self::REC_STATUS_EXECUTING,
            'date_updated' => $now
        ));

        // 2. Generate Content Variation if variable is HOOK / CTA / SCRIPT
        $idVariationContent = null;
        if (in_array($rec['proposed_variable'], array(self::VAR_HOOK, self::VAR_CTA, self::VAR_SCRIPT, self::VAR_MULTIVARIATE))) {
            if (class_exists('AIContentEngine')) {
                $contentEngine = new AIContentEngine($this->d, $this->func);
                $genRes = $contentEngine->generateContent($pid, 'tiktok_script', array(
                    'content_angle' => 'Cost-Comparison',
                    'tone' => 'CREATOR_CONVERSATIONAL'
                ));
                if (!empty($genRes['status']) && !empty($genRes['content_id'])) {
                    $idVariationContent = (int)$genRes['content_id'];
                }
            }
        }

        // 3. Create Variation Video Project if applicable (Economy or Hybrid)
        $idVariationVideo = null;
        if (class_exists('AIVideoEngine') && ($idVariationContent || $idBaselineContent)) {
            $videoEngine = new AIVideoEngine($this->d, $this->func);
            $vidMode = $rec['target_mode'] === 'HYBRID' ? 'HYBRID' : 'ECONOMY';
            $vidRes = $videoEngine->createProjectFromApprovedContent($idVariationContent ?: $idBaselineContent, array(
                'mode' => $vidMode,
                'title' => "[EXP #{$expId}] {$product['namevi']} ({$vidMode})"
            ));
            if (!empty($vidRes['success']) && !empty($vidRes['id_video'])) {
                $idVariationVideo = (int)$vidRes['id_video'];
            }
        }

        // 4. Create Publishing Post Package for the Variation
        $idVariationPost = null;
        $trackingCode = AnalyticsService::generateTrackingCode('tiktok', $expId);
        $targetVideoId = $idVariationVideo ?: $idBaselineVideo;

        if ($targetVideoId && class_exists('PublishingCenter')) {
            $publishingCenter = new PublishingCenter($this->d, $this->func);
            $postData = array(
                'title' => "[EXP #{$expId}] " . $product['namevi'],
                'caption' => "Biến thể thử nghiệm {$rec['proposed_variable']} cho " . $product['namevi'] . " #fitnado #experiment",
                'hashtags' => "#fitnado #reviewgym #experiment",
                'disclosure_text' => "FITNADO có thể nhận hoa hồng khi bạn mua hàng qua link.",
                'tracking_code' => $trackingCode,
                'platform' => 'tiktok',
                'id_ai_content' => $idVariationContent ?: $idBaselineContent
            );
            $postRes = $publishingCenter->createPostFromApprovedVideo($targetVideoId, $postData, $adminUser);
            if (!empty($postRes['success']) && !empty($postRes['id_post'])) {
                $idVariationPost = (int)$postRes['id_post'];
            }
        }

        // Fallback: If not created via PublishingCenter (e.g. video is draft/mocked in test), create post record directly
        if (!$idVariationPost) {
            $globalConfig = isset($GLOBALS['configUrl']) ? $GLOBALS['configUrl'] : 'fitnado.vn';
            $productSlug = !empty($product['slugvi']) ? $product['slugvi'] : ('san-pham/' . $pid);
            $landingUrl = 'https://' . $globalConfig . '/' . $productSlug . '?ref=' . urlencode($trackingCode) . '&utm_source=tiktok&utm_medium=organic_video&utm_campaign=exp_' . $expId . '&utm_content=' . urlencode($trackingCode);
            
            $idVariationPost = $this->d->insert('publish_post', array(
                'id_product' => $pid,
                'id_video' => $targetVideoId,
                'id_ai_content' => $idVariationContent ?: $idBaselineContent,
                'platform' => 'tiktok',
                'post_type' => 'VIDEO_POST',
                'tracking_code' => $trackingCode,
                'title' => "[EXP #{$expId}] " . $product['namevi'],
                'caption' => "Biến thể thử nghiệm {$rec['proposed_variable']} cho " . $product['namevi'] . " #fitnado #experiment",
                'hashtags' => "#fitnado #reviewgym #experiment",
                'landing_url' => $landingUrl,
                'disclosure_text' => "FITNADO có thể nhận hoa hồng khi bạn mua hàng qua link.",
                'provider' => 'manual',
                'status' => 'DRAFT',
                'date_created' => time(),
                'date_updated' => time()
            ));
        }

        // Update experiment with generated variation IDs
        $this->d->where('id', $expId);
        $this->d->update('optimization_experiment', array(
            'id_variation_content' => $idVariationContent,
            'id_variation_video' => $idVariationVideo,
            'id_variation_post' => $idVariationPost,
            'status' => self::EXP_STATUS_RUNNING,
            'date_updated' => time()
        ));

        return array(
            'success' => true,
            'experiment_id' => $expId,
            'experiment_code' => $expCode,
            'id_variation_content' => $idVariationContent,
            'id_variation_video' => $idVariationVideo,
            'id_variation_post' => $idVariationPost,
            'status' => self::EXP_STATUS_RUNNING
        );
    }

    /**
     * Evaluate Experiment Results against Sample Gate & Baseline
     * Concludes whether Variation is better or Baseline is better
     * @param int $experimentId
     * @return array
     */
    public function evaluateExperiment($experimentId) {
        $experimentId = (int)$experimentId;
        if (!$this->d || !$experimentId) {
            return array('success' => false, 'error' => 'ID thử nghiệm không hợp lệ.');
        }

        $exp = $this->d->rawQueryOne("SELECT * FROM table_optimization_experiment WHERE id = ? LIMIT 1", array($experimentId));
        if (empty($exp)) {
            return array('success' => false, 'error' => 'Không tìm thấy thử nghiệm.');
        }

        $rules = $this->getRules();
        $minSessions = $rules['experiment_min_sessions'];
        $minClicks = $rules['experiment_min_clicks'];

        $idBaselinePost = (int)($exp['id_baseline_post'] ?? 0);
        $idVariationPost = (int)($exp['id_variation_post'] ?? 0);

        // Fetch Baseline Post Metrics
        $basePostMetrics = !empty($idBaselinePost) ? $this->analytics->getPostMetrics($idBaselinePost, 'all') : array();
        $bm = !empty($basePostMetrics[0]) ? $basePostMetrics[0] : json_decode($exp['baseline_metrics_snapshot'], true);

        // Fetch Variation Post Metrics
        $varPostMetrics = !empty($idVariationPost) ? $this->analytics->getPostMetrics($idVariationPost, 'all') : array();
        $vm = !empty($varPostMetrics[0]) ? $varPostMetrics[0] : array();

        $baseSessions = (int)($bm['sessions'] ?? 0);
        $baseClicks = (int)($bm['clicks'] ?? 0);
        $baseCtr = (float)($bm['ctr_pct'] ?? 0.0);
        $baseConversions = (int)($bm['conversions'] ?? 0);
        $baseCommission = (float)($bm['commission_vnd'] ?? 0.0);

        $varSessions = (int)($vm['sessions'] ?? 0);
        $varClicks = (int)($vm['clicks'] ?? 0);
        $varCtr = (float)($vm['ctr_pct'] ?? 0.0);
        $varConversions = (int)($vm['conversions'] ?? 0);
        $varCommission = (float)($vm['commission_vnd'] ?? 0.0);

        $variationSnapshot = array(
            'sessions' => $varSessions,
            'clicks' => $varClicks,
            'ctr_pct' => $varCtr,
            'conversions' => $varConversions,
            'commission_vnd' => $varCommission
        );

        $conclusion = null;
        $summary = '';

        // 1. Sample Size Gate Check on Variation
        if ($varSessions < $minSessions || $varClicks < $minClicks) {
            $conclusion = self::RESULT_INSUFFICIENT_DATA;
            $summary = "Biến thể thử nghiệm mới đạt {$varSessions}/{$minSessions} sessions và {$varClicks}/{$minClicks} clicks. Chưa đủ độ lớn mẫu để kết luận người chiến thắng.";
        } else {
            // 2. Adequate Sample -> Compare CTR / Conversions / Commission
            $ctrDiff = $varCtr - $baseCtr;
            $ctrRelGain = AnalyticsService::safePercentage($ctrDiff, max(0.1, $baseCtr));

            if ($varCommission > $baseCommission && $varConversions >= $baseConversions && $varCommission > 0) {
                $conclusion = self::RESULT_VARIATION_BETTER;
                $summary = "Biến thể mới ({$exp['changed_variable']}) vượt trội về doanh thu (" . number_format($varCommission, 0, ',', '.') . " đ so với " . number_format($baseCommission, 0, ',', '.') . " đ) và chuyển đổi ({$varConversions} so với {$baseConversions}).";
            } elseif ($varCommission < $baseCommission && $baseCommission > 0 && $varConversions <= $baseConversions) {
                $conclusion = self::RESULT_BASELINE_BETTER;
                $summary = "Nội dung gốc (Baseline) tạo doanh thu tốt hơn (" . number_format($baseCommission, 0, ',', '.') . " đ so với " . number_format($varCommission, 0, ',', '.') . " đ).";
            } elseif ($ctrDiff >= 2.0 && ($ctrRelGain >= 15.0 || $varCtr >= 10.0)) {
                $conclusion = self::RESULT_VARIATION_BETTER;
                $summary = "Biến thể mới ({$exp['changed_variable']}) đạt CTR {$varCtr}% cao hơn đáng kể so với Baseline {$baseCtr}% (Chênh lệch +{$ctrDiff}%).";
            } elseif ($ctrDiff <= -2.0) {
                $conclusion = self::RESULT_BASELINE_BETTER;
                $summary = "Nội dung gốc (Baseline) hoạt động hiệu quả hơn biến thể mới (CTR {$baseCtr}% so với {$varCtr}%).";
            } else {
                $conclusion = self::RESULT_NO_DIFFERENCE;
                $summary = "Không có sự khác biệt rõ rệt giữa Baseline ({$baseCtr}% CTR) và Biến thể mới ({$varCtr}% CTR).";
            }
        }

        $now = time();
        $isCompleted = ($conclusion !== self::RESULT_INSUFFICIENT_DATA);

        $updateData = array(
            'variation_metrics_snapshot' => json_encode($variationSnapshot),
            'result_conclusion' => $conclusion,
            'result_summary' => $summary,
            'status' => $isCompleted ? self::EXP_STATUS_COMPLETED : self::EXP_STATUS_RUNNING,
            'completed_at' => $isCompleted ? $now : null,
            'date_updated' => $now
        );

        $this->d->where('id', $experimentId);
        $this->d->update('optimization_experiment', $updateData);

        // Update recommendation status if experiment completed
        if ($isCompleted && !empty($exp['id_recommendation'])) {
            $this->d->where('id', (int)$exp['id_recommendation']);
            $this->d->update('optimization_recommendation', array(
                'status' => self::REC_STATUS_COMPLETED,
                'date_updated' => $now
            ));
        }

        return array(
            'success' => true,
            'experiment_id' => $experimentId,
            'conclusion' => $conclusion,
            'summary' => $summary,
            'baseline_metrics' => $bm,
            'variation_metrics' => $variationSnapshot,
            'is_completed' => $isCompleted
        );
    }
}
