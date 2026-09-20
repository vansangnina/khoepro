<?php
if (!defined('SOURCES')) die("Error");

require_once LIBRARIES . 'class/class.AnalyticsService.php';
require_once LIBRARIES . 'class/class.WinnerDetectionEngine.php';
require_once LIBRARIES . 'class/class.OptimizationEngine.php';

$analytics = new AnalyticsService($d, $func);
$winnerEngine = new WinnerDetectionEngine($d, $analytics);
$optimizationEngine = new OptimizationEngine($d, $func, $analytics, $winnerEngine);

$linkRecommendations = "index.php?com=optimization&act=recommendations";
$linkExperiments = "index.php?com=optimization&act=experiments";
$linkRules = "index.php?com=optimization&act=rules";

switch ($act) {
    /* 1. Recommendations List */
    case "recommendations":
        viewRecommendations();
        $template = "optimization/recommendations";
        break;

    /* 2. Recommendation Detail & Approval Screen */
    case "recommendation_detail":
        viewRecommendationDetail();
        $template = "optimization/recommendation_detail";
        break;

    /* 3. Generate Recommendations Trigger */
    case "generate_recommendations":
        generateRecommendationsAction();
        break;

    /* 4. Approve Recommendation Action */
    case "approve_recommendation":
        approveRecommendationAction();
        break;

    /* 5. Reject Recommendation Action */
    case "reject_recommendation":
        rejectRecommendationAction();
        break;

    /* 6. Experiments List */
    case "experiments":
        viewExperiments();
        $template = "optimization/experiments";
        break;

    /* 7. Experiment Detail & Comparison */
    case "experiment_detail":
        viewExperimentDetail();
        $template = "optimization/experiment_detail";
        break;

    /* 8. Evaluate Experiment Trigger */
    case "evaluate_experiment":
        evaluateExperimentAction();
        break;

    /* 9. Rules & Settings */
    case "rules":
        viewOptimizationRules();
        $template = "optimization/rules";
        break;

    /* 10. Save Rules */
    case "save_rules":
        saveOptimizationRulesAction();
        break;

    default:
        viewRecommendations();
        $template = "optimization/recommendations";
        break;
}

/**
 * 1. Recommendations List
 */
function viewRecommendations() {
    global $d, $recommendations, $filterStatus, $filterType, $rulesConfig;

    $filterStatus = !empty($_GET['status']) ? htmlspecialchars(trim($_GET['status'])) : '';
    $filterType = !empty($_GET['type']) ? htmlspecialchars(trim($_GET['type'])) : '';

    $where = "WHERE 1=1";
    $params = array();

    if (!empty($filterStatus)) {
        $where .= " AND r.status = ?";
        $params[] = $filterStatus;
    }
    if (!empty($filterType)) {
        $where .= " AND r.recommendation_type = ?";
        $params[] = $filterType;
    }

    $sql = "SELECT r.*, p.namevi as product_name, p.photo as product_photo, post.title as post_title 
            FROM table_optimization_recommendation r 
            LEFT JOIN table_product p ON (r.id_product = p.id) 
            LEFT JOIN table_publish_post post ON (r.id_post = post.id) 
            {$where} 
            ORDER BY r.id DESC LIMIT 100";

    $recommendations = $d->rawQuery($sql, $params);
    $rulesConfig = $d->rawQuery("SELECT setting_key, setting_value FROM table_analytics_setting WHERE setting_group = 'optimization'");
}

/**
 * 2. Recommendation Detail & Approval Screen
 */
function viewRecommendationDetail() {
    global $d, $func, $recommendation, $product, $post, $video, $content, $metricsSnapshot, $rulesSnapshot;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        $func->transfer("Không tìm thấy ID khuyến nghị.", "index.php?com=optimization&act=recommendations", false);
        return;
    }

    $recommendation = $d->rawQueryOne("SELECT * FROM table_optimization_recommendation WHERE id = ? LIMIT 1", array($id));
    if (empty($recommendation)) {
        $func->transfer("Khuyến nghị không tồn tại.", "index.php?com=optimization&act=recommendations", false);
        return;
    }

    $product = $d->rawQueryOne("SELECT * FROM table_product WHERE id = ? LIMIT 1", array((int)$recommendation['id_product']));
    $post = !empty($recommendation['id_post']) ? $d->rawQueryOne("SELECT * FROM table_publish_post WHERE id = ? LIMIT 1", array((int)$recommendation['id_post'])) : null;
    $video = !empty($recommendation['id_video']) ? $d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array((int)$recommendation['id_video'])) : null;
    $content = !empty($recommendation['id_content']) ? $d->rawQueryOne("SELECT * FROM table_ai_content WHERE id = ? LIMIT 1", array((int)$recommendation['id_content'])) : null;

    $metricsSnapshot = json_decode($recommendation['metrics_snapshot'] ?? '{}', true);
    $rulesSnapshot = json_decode($recommendation['rules_snapshot'] ?? '{}', true);
}

/**
 * 3. Generate Recommendations Trigger
 */
function generateRecommendationsAction() {
    global $func, $optimizationEngine;

    $productId = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $recs = $optimizationEngine->generateRecommendations($productId);
    $count = count($recs);

    $msg = "Đã quét và sinh {$count} khuyến nghị tối ưu hóa mới.";
    $func->transfer($msg, "index.php?com=optimization&act=recommendations");
}

/**
 * 4. Approve Recommendation Action
 */
function approveRecommendationAction() {
    global $func, $optimizationEngine;

    $id = !empty($_POST['recommendation_id']) ? (int)$_POST['recommendation_id'] : 0;
    $notes = !empty($_POST['review_notes']) ? htmlspecialchars(trim($_POST['review_notes'])) : '';
    $override = !empty($_POST['admin_override']) ? true : false;
    $adminUser = $_SESSION['login_admin']['username'] ?? 'admin';

    $res = $optimizationEngine->approveRecommendation($id, $adminUser, $notes, $override);
    if (!empty($res['success'])) {
        $msg = "Đã phê duyệt khuyến nghị và khởi tạo Thử nghiệm A/B (#{$res['experiment_id']}).";
        $func->transfer($msg, "index.php?com=optimization&act=experiment_detail&id={$res['experiment_id']}");
    } else {
        $func->transfer("Phê duyệt thất bại: " . ($res['error'] ?? 'Lỗi không xác định.'), "index.php?com=optimization&act=recommendation_detail&id={$id}", false);
    }
}

/**
 * 5. Reject Recommendation Action
 */
function rejectRecommendationAction() {
    global $func, $optimizationEngine;

    $id = !empty($_POST['recommendation_id']) ? (int)$_POST['recommendation_id'] : 0;
    $reason = !empty($_POST['reject_reason']) ? htmlspecialchars(trim($_POST['reject_reason'])) : 'Không phù hợp mục tiêu kinh doanh';
    $adminUser = $_SESSION['login_admin']['username'] ?? 'admin';

    $res = $optimizationEngine->rejectRecommendation($id, $adminUser, $reason);
    if (!empty($res['success'])) {
        $func->transfer("Đã từ chối khuyến nghị tối ưu hóa.", "index.php?com=optimization&act=recommendations");
    } else {
        $func->transfer("Từ chối khuyến nghị thất bại.", "index.php?com=optimization&act=recommendation_detail&id={$id}", false);
    }
}

/**
 * 6. Experiments List
 */
function viewExperiments() {
    global $d, $experiments, $filterStatus, $filterVariable;

    $filterStatus = !empty($_GET['status']) ? htmlspecialchars(trim($_GET['status'])) : '';
    $filterVariable = !empty($_GET['variable']) ? htmlspecialchars(trim($_GET['variable'])) : '';

    $where = "WHERE 1=1";
    $params = array();

    if (!empty($filterStatus)) {
        $where .= " AND e.status = ?";
        $params[] = $filterStatus;
    }
    if (!empty($filterVariable)) {
        $where .= " AND e.changed_variable = ?";
        $params[] = $filterVariable;
    }

    $sql = "SELECT e.*, p.namevi as product_name, p.photo as product_photo, 
                   base_post.title as base_post_title, var_post.title as var_post_title,
                   base_post.tracking_code as base_tracking_code, var_post.tracking_code as var_tracking_code
            FROM table_optimization_experiment e
            LEFT JOIN table_product p ON (e.id_product = p.id)
            LEFT JOIN table_publish_post base_post ON (e.id_baseline_post = base_post.id)
            LEFT JOIN table_publish_post var_post ON (e.id_variation_post = var_post.id)
            {$where}
            ORDER BY e.id DESC LIMIT 100";

    $experiments = $d->rawQuery($sql, $params);
}

/**
 * 7. Experiment Detail & Comparison
 */
function viewExperimentDetail() {
    global $d, $func, $analytics, $experiment, $product, $baselinePost, $variationPost, $baselineVideo, $variationVideo, $baselineMetrics, $variationMetrics;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        $func->transfer("Không tìm thấy ID thử nghiệm.", "index.php?com=optimization&act=experiments", false);
        return;
    }

    $experiment = $d->rawQueryOne("SELECT * FROM table_optimization_experiment WHERE id = ? LIMIT 1", array($id));
    if (empty($experiment)) {
        $func->transfer("Thử nghiệm không tồn tại.", "index.php?com=optimization&act=experiments", false);
        return;
    }

    $product = $d->rawQueryOne("SELECT * FROM table_product WHERE id = ? LIMIT 1", array((int)$experiment['id_product']));

    // Baseline & Variation Posts
    $baselinePost = !empty($experiment['id_baseline_post']) ? $d->rawQueryOne("SELECT * FROM table_publish_post WHERE id = ? LIMIT 1", array((int)$experiment['id_baseline_post'])) : null;
    $variationPost = !empty($experiment['id_variation_post']) ? $d->rawQueryOne("SELECT * FROM table_publish_post WHERE id = ? LIMIT 1", array((int)$experiment['id_variation_post'])) : null;

    // Baseline & Variation Videos
    $baselineVideo = !empty($experiment['id_baseline_video']) ? $d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array((int)$experiment['id_baseline_video'])) : null;
    $variationVideo = !empty($experiment['id_variation_video']) ? $d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array((int)$experiment['id_variation_video'])) : null;

    // Real-time Metrics from AnalyticsService
    $baseMetricsList = !empty($experiment['id_baseline_post']) ? $analytics->getPostMetrics((int)$experiment['id_baseline_post'], 'all') : array();
    $baselineMetrics = !empty($baseMetricsList[0]) ? $baseMetricsList[0] : json_decode($experiment['baseline_metrics_snapshot'] ?? '{}', true);

    $varMetricsList = !empty($experiment['id_variation_post']) ? $analytics->getPostMetrics((int)$experiment['id_variation_post'], 'all') : array();
    $variationMetrics = !empty($varMetricsList[0]) ? $varMetricsList[0] : json_decode($experiment['variation_metrics_snapshot'] ?? '{}', true);
}

/**
 * 8. Evaluate Experiment Action
 */
function evaluateExperimentAction() {
    global $func, $optimizationEngine;

    $id = !empty($_POST['experiment_id']) ? (int)$_POST['experiment_id'] : 0;
    $res = $optimizationEngine->evaluateExperiment($id);

    if (!empty($res['success'])) {
        $msg = "Đã đánh giá thử nghiệm #{$id}: Kết luận [{$res['conclusion']}].";
        $func->transfer($msg, "index.php?com=optimization&act=experiment_detail&id={$id}");
    } else {
        $func->transfer("Đánh giá thất bại: " . ($res['error'] ?? 'Lỗi không xác định.'), "index.php?com=optimization&act=experiment_detail&id={$id}", false);
    }
}

/**
 * 9. Rules & Settings View
 */
function viewOptimizationRules() {
    global $analytics, $currentSettings;

    $currentSettings = array(
        'max_cost_per_experiment' => $analytics->getSetting('max_cost_per_experiment', 60000.0),
        'max_active_experiments_per_product' => $analytics->getSetting('max_active_experiments_per_product', 2),
        'recommendation_cooldown_hours' => $analytics->getSetting('recommendation_cooldown_hours', 24),
        'experiment_min_sessions' => $analytics->getSetting('experiment_min_sessions', 30),
        'experiment_min_clicks' => $analytics->getSetting('experiment_min_clicks', 10)
    );
}

/**
 * 10. Save Optimization Rules
 */
function saveOptimizationRulesAction() {
    global $func, $analytics;

    $keys = array(
        'max_cost_per_experiment' => 'optimization',
        'max_active_experiments_per_product' => 'optimization',
        'recommendation_cooldown_hours' => 'optimization',
        'experiment_min_sessions' => 'optimization',
        'experiment_min_clicks' => 'optimization'
    );

    foreach ($keys as $k => $grp) {
        if (isset($_POST[$k])) {
            $val = trim($_POST[$k]);
            $analytics->saveSetting($k, $val, $grp);
        }
    }

    $func->transfer("Đã lưu cấu hình quy tắc Optimization thành công.", "index.php?com=optimization&act=rules");
}
