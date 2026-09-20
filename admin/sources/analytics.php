<?php
if (!defined('SOURCES')) die("Error");

require_once LIBRARIES . 'class/class.AnalyticsService.php';
require_once LIBRARIES . 'class/class.WinnerDetectionEngine.php';
require_once LIBRARIES . 'class/class.ConversionImporter.php';

$analytics = new AnalyticsService($d, $func);
$winnerEngine = new WinnerDetectionEngine($d, $analytics);
$conversionImporter = new ConversionImporter($d);

$linkOverview = "index.php?com=analytics&act=overview";
$linkProducts = "index.php?com=analytics&act=products";
$linkPosts = "index.php?com=analytics&act=posts";
$linkVideos = "index.php?com=analytics&act=videos";
$linkContent = "index.php?com=analytics&act=content";
$linkConversions = "index.php?com=analytics&act=conversions";
$linkImport = "index.php?com=analytics&act=conversion_import";
$linkWinner = "index.php?com=analytics&act=winner_detection";
$linkRules = "index.php?com=analytics&act=winner_rules";

// Time range filter from query string (default '30d')
$timeRange = !empty($_GET['time_range']) ? htmlspecialchars(trim($_GET['time_range'])) : '30d';
if (!in_array($timeRange, array('today', '7d', '30d', 'all'))) {
    $timeRange = '30d';
}

switch ($act) {
    /* 1. Overview Dashboard */
    case "overview":
        viewAnalyticsOverview();
        $template = "analytics/overview";
        break;

    /* 2. Product Performance */
    case "products":
        viewProductPerformance();
        $template = "analytics/products";
        break;

    /* 3. Post Performance */
    case "posts":
        viewPostPerformance();
        $template = "analytics/posts";
        break;

    /* 4. Video Performance */
    case "videos":
        viewVideoPerformance();
        $template = "analytics/videos";
        break;

    /* 5. Content & Hook Performance */
    case "content":
        viewContentHookPerformance();
        $template = "analytics/content";
        break;

    /* 6. Conversions List */
    case "conversions":
        viewConversionsList();
        $template = "analytics/conversions";
        break;

    /* 7. CSV Conversion Import */
    case "conversion_import":
        viewConversionImport();
        $template = "analytics/conversion_import";
        break;

    /* 8. Process CSV Import */
    case "process_import":
        processCsvImportAction();
        break;

    /* 9. Manual Attribution Match */
    case "manual_match":
        processManualMatchAction();
        break;

    /* 10. Winner Detection Dashboard */
    case "winner_detection":
        viewWinnerDetection();
        $template = "analytics/winner_detection";
        break;

    /* 11. Run Winner Evaluation */
    case "evaluate_winner":
        processWinnerEvaluationAction();
        break;

    /* 12. Winner Rules & Settings */
    case "winner_rules":
        viewWinnerRules();
        $template = "analytics/winner_rules";
        break;

    /* 13. Save Winner Rules */
    case "save_rules":
        saveWinnerRulesAction();
        break;

    default:
        viewAnalyticsOverview();
        $template = "analytics/overview";
        break;
}

/**
 * 1. Overview Dashboard
 */
function viewAnalyticsOverview() {
    global $d, $analytics, $winnerEngine, $timeRange, $overview, $rulesConfig;
    $overview = $analytics->getOverviewMetrics($timeRange);
    $rulesConfig = $winnerEngine->getRulesConfig();
}

/**
 * 2. Product Performance
 */
function viewProductPerformance() {
    global $d, $analytics, $timeRange, $productMetrics;
    $productMetrics = $analytics->getProductMetrics(null, $timeRange);
}

/**
 * 3. Post Performance
 */
function viewPostPerformance() {
    global $d, $analytics, $timeRange, $postMetrics;
    $postMetrics = $analytics->getPostMetrics(null, $timeRange);
}

/**
 * 4. Video Performance
 */
function viewVideoPerformance() {
    global $d, $analytics, $timeRange, $videoMetrics;
    $videoMetrics = $analytics->getVideoMetrics(null, $timeRange);
}

/**
 * 5. Content & Hook Performance
 */
function viewContentHookPerformance() {
    global $d, $analytics, $timeRange, $contentMetrics;
    $contentMetrics = $analytics->getContentHookMetrics($timeRange);
}

/**
 * 6. Conversions List
 */
function viewConversionsList() {
    global $d, $conversions, $curPage, $paging, $filterStatus, $filterPlatform, $allProducts, $allPosts;

    $filterStatus = !empty($_GET['status']) ? htmlspecialchars(trim($_GET['status'])) : '';
    $filterPlatform = !empty($_GET['platform']) ? htmlspecialchars(trim($_GET['platform'])) : '';
    $filterAttributed = isset($_GET['attributed']) ? htmlspecialchars(trim($_GET['attributed'])) : '';

    $where = "WHERE 1=1";
    $params = array();

    if (!empty($filterStatus)) {
        $where .= " AND c.status = ?";
        $params[] = $filterStatus;
    }
    if (!empty($filterPlatform)) {
        $where .= " AND c.platform = ?";
        $params[] = $filterPlatform;
    }
    if ($filterAttributed === 'yes') {
        $where .= " AND c.id_product IS NOT NULL";
    } elseif ($filterAttributed === 'no') {
        $where .= " AND c.id_product IS NULL";
    }

    $sql = "SELECT c.*, p.namevi as product_name, post.title as post_title 
            FROM table_affiliate_conversion c 
            LEFT JOIN table_product p ON (c.id_product = p.id) 
            LEFT JOIN table_publish_post post ON (c.id_post = post.id) 
            {$where} 
            ORDER BY c.conversion_at DESC, c.id DESC LIMIT 100";

    $conversions = $d->rawQuery($sql, $params);

    // Products & Posts list for manual matching dropdown
    $allProducts = $d->rawQuery("SELECT id, namevi FROM table_product WHERE find_in_set('hienthi', status) ORDER BY id DESC LIMIT 50");
    $allPosts = $d->rawQuery("SELECT id, title, platform FROM table_publish_post ORDER BY id DESC LIMIT 50");
}

/**
 * 7. CSV Conversion Import Page & Preview
 */
function viewConversionImport() {
    global $d, $conversionImporter, $previewResult, $importLogs, $uploadError;

    $previewResult = null;
    $uploadError = null;

    // Load recent import history
    $importLogs = $d->rawQuery("SELECT * FROM table_conversion_import_log ORDER BY id DESC LIMIT 10");

    // Handle Upload & Preview
    if (!empty($_FILES['csv_file']['tmp_name'])) {
        $tmpName = $_FILES['csv_file']['tmp_name'];
        $origName = $_FILES['csv_file']['name'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if ($ext !== 'csv' && $ext !== 'txt') {
            $uploadError = 'Chỉ chấp nhận file có định dạng .csv hoặc .txt.';
            return;
        }

        $platform = !empty($_POST['platform']) ? htmlspecialchars(trim($_POST['platform'])) : 'shopee';
        
        // Save file to scratch directory for processing
        $uploadDir = __DIR__ . '/../../scratch/';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);
        
        $savedPath = $uploadDir . 'import_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $origName);
        if (move_uploaded_file($tmpName, $savedPath)) {
            $previewResult = $conversionImporter->previewCsv($savedPath, $platform);
            $previewResult['saved_file_path'] = $savedPath;
            $previewResult['platform'] = $platform;
        } else {
            $uploadError = 'Không thể lưu file tải lên máy chủ.';
        }
    }
}

/**
 * 8. Process CSV Import Action
 */
function processCsvImportAction() {
    global $func, $configBase, $conversionImporter;

    $filePath = !empty($_POST['saved_file_path']) ? trim($_POST['saved_file_path']) : '';
    $platform = !empty($_POST['platform']) ? trim($_POST['platform']) : 'shopee';
    $adminUser = $_SESSION['login_admin']['username'] ?? 'admin';

    if (empty($filePath) || !file_exists($filePath)) {
        $func->transfer("File tải lên không tồn tại hoặc đã hết hạn.", "index.php?com=analytics&act=conversion_import", false);
        return;
    }

    $res = $conversionImporter->executeImport($filePath, $platform, $adminUser);
    
    // Clean up temporary file
    @unlink($filePath);

    if (!empty($res['success'])) {
        $msg = "Nhập dữ liệu thành công: {$res['imported_count']} đơn hàng đã ghi nhận ({$res['matched_count']} đơn khớp chiến dịch, {$res['unattributed_count']} đơn chưa rõ nguồn, {$res['duplicate_count']} đơn trùng lặp).";
        $func->transfer($msg, "index.php?com=analytics&act=conversions");
    } else {
        $func->transfer("Nhập dữ liệu thất bại: " . ($res['error'] ?? 'Lỗi không xác định.'), "index.php?com=analytics&act=conversion_import", false);
    }
}

/**
 * 9. Manual Attribution Match Action
 */
function processManualMatchAction() {
    global $func, $conversionImporter;

    $conversionId = !empty($_POST['conversion_id']) ? (int)$_POST['conversion_id'] : 0;
    $productId = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $postId = !empty($_POST['post_id']) ? (int)$_POST['post_id'] : null;
    $notes = !empty($_POST['match_notes']) ? htmlspecialchars(trim($_POST['match_notes'])) : '';
    $adminUser = $_SESSION['login_admin']['username'] ?? 'admin';

    if (!$conversionId || !$productId) {
        $func->transfer("Vui lòng chọn đơn hàng và sản phẩm cần gán.", "index.php?com=analytics&act=conversions", false);
        return;
    }

    $ok = $conversionImporter->manualMatchConversion($conversionId, $productId, $postId, $adminUser, $notes);
    if ($ok) {
        $func->transfer("Gán nguồn chuyển đổi thành công.", "index.php?com=analytics&act=conversions");
    } else {
        $func->transfer("Gán nguồn chuyển đổi thất bại.", "index.php?com=analytics&act=conversions", false);
    }
}

/**
 * 10. Winner Detection Dashboard
 */
function viewWinnerDetection() {
    global $d, $winnerEngine, $analytics, $evaluatedProducts, $rulesConfig, $summaryStats;

    $rulesConfig = $winnerEngine->getRulesConfig();
    $evaluatedProducts = $analytics->getProductMetrics(null, 'all');

    // Winner summary counts
    $summaryStats = array(
        'WINNER' => 0,
        'PROMISING' => 0,
        'TESTING' => 0,
        'UNDERPERFORMING' => 0,
        'INSUFFICIENT_DATA' => 0
    );

    if (!empty($evaluatedProducts)) {
        foreach ($evaluatedProducts as $p) {
            $st = $p['winner_status'] ?? 'INSUFFICIENT_DATA';
            if (isset($summaryStats[$st])) $summaryStats[$st]++;
        }
    }
}

/**
 * 11. Winner Evaluation Trigger Action
 */
function processWinnerEvaluationAction() {
    global $func, $winnerEngine;

    $productId = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $adminUser = $_SESSION['login_admin']['username'] ?? 'admin';

    if ($productId > 0) {
        $res = $winnerEngine->evaluateProduct($productId, $adminUser, true);
        if (!empty($res['success'])) {
            $func->transfer("Đã đánh giá lại sản phẩm #{$productId}: Trạng thái [{$res['status']}] - Tín hiệu [{$res['signal_level']}].", "index.php?com=analytics&act=winner_detection");
        } else {
            $func->transfer("Lỗi đánh giá: " . ($res['error'] ?? 'Không rõ'), "index.php?com=analytics&act=winner_detection", false);
        }
    } else {
        $res = $winnerEngine->evaluateAllProducts($adminUser);
        $func->transfer("Đã đánh giá toàn bộ {$res['evaluated_count']} sản phẩm: {$res['counts']['WINNER']} Winner, {$res['counts']['PROMISING']} Tiềm năng, {$res['counts']['INSUFFICIENT_DATA']} Chưa đủ dữ liệu.", "index.php?com=analytics&act=winner_detection");
    }
}

/**
 * 12. Winner Rules & Settings View
 */
function viewWinnerRules() {
    global $d, $analytics, $winnerEngine, $rulesConfig, $currentSettings;

    $rulesConfig = $winnerEngine->getRulesConfig();
    $currentSettings = array(
        'attribution_window_days' => $analytics->getSetting('attribution_window_days', 30),
        'min_landing_sessions' => $analytics->getSetting('min_landing_sessions', 30),
        'min_affiliate_clicks' => $analytics->getSetting('min_affiliate_clicks', 10),
        'min_conversions' => $analytics->getSetting('min_conversions', 2),
        'min_test_age_days' => $analytics->getSetting('min_test_age_days', 3),
        'promising_ctr_pct' => $analytics->getSetting('promising_ctr_pct', 5.0),
        'winner_ctr_pct' => $analytics->getSetting('winner_ctr_pct', 10.0),
        'winner_cvr_pct' => $analytics->getSetting('winner_cvr_pct', 5.0),
        'underperforming_ctr_pct' => $analytics->getSetting('underperforming_ctr_pct', 1.0),
        'internal_ips' => $analytics->getSetting('internal_ips', '["127.0.0.1", "::1"]')
    );
}

/**
 * 13. Save Winner Rules Action
 */
function saveWinnerRulesAction() {
    global $func, $analytics;

    $keys = array(
        'attribution_window_days' => 'attribution',
        'min_landing_sessions' => 'winner_rules',
        'min_affiliate_clicks' => 'winner_rules',
        'min_conversions' => 'winner_rules',
        'min_test_age_days' => 'winner_rules',
        'promising_ctr_pct' => 'winner_rules',
        'winner_ctr_pct' => 'winner_rules',
        'winner_cvr_pct' => 'winner_rules',
        'underperforming_ctr_pct' => 'winner_rules',
        'internal_ips' => 'traffic'
    );

    foreach ($keys as $k => $grp) {
        if (isset($_POST[$k])) {
            $val = trim($_POST[$k]);
            $analytics->saveSetting($k, $val, $grp);
        }
    }

    $func->transfer("Đã lưu cấu hình quy tắc Analytics & Winner Detection thành công.", "index.php?com=analytics&act=winner_rules");
}
