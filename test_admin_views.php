<?php
/**
 * FITNADO Phase 08 - Deep Admin View Template Rendering Test
 */

$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/admin/index.php?com=analytics&act=overview';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'CLI-Test';
$_SERVER['DOCUMENT_ROOT'] = __DIR__;

define('LIBRARIES', __DIR__ . '/libraries/');
define('SOURCES', __DIR__ . '/sources/');
define('LAYOUT', 'layout/');
define('THUMBS', 'thumbs');
define('WATERMARK', 'watermark');

require_once LIBRARIES . 'config.php';
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$d = new PDODb($config['database']);
$cache = new Cache($d);
$func = new Functions($d, $cache);

require_once LIBRARIES . 'class/class.AnalyticsService.php';
require_once LIBRARIES . 'class/class.WinnerDetectionEngine.php';
require_once LIBRARIES . 'class/class.ConversionImporter.php';

$analytics = new AnalyticsService($d, $func);
$winnerEngine = new WinnerDetectionEngine($d, $analytics);
$importer = new ConversionImporter($d);

$views = [
    'overview_tpl.php' => function() use ($d, $func, $analytics, $winnerEngine) {
        $timeRange = '30d';
        $overview = $analytics->getOverviewMetrics($timeRange);
        $rulesConfig = $winnerEngine->getRulesConfig();
        ob_start();
        include __DIR__ . '/admin/templates/analytics/overview_tpl.php';
        return ob_get_clean();
    },
    'products_tpl.php' => function() use ($d, $func, $analytics) {
        $timeRange = '30d';
        $productMetrics = $analytics->getProductMetrics(null, $timeRange);
        ob_start();
        include __DIR__ . '/admin/templates/analytics/products_tpl.php';
        return ob_get_clean();
    },
    'posts_tpl.php' => function() use ($d, $func, $analytics) {
        $timeRange = '30d';
        $postMetrics = $analytics->getPostMetrics(null, $timeRange);
        ob_start();
        include __DIR__ . '/admin/templates/analytics/posts_tpl.php';
        return ob_get_clean();
    },
    'videos_tpl.php' => function() use ($d, $func, $analytics) {
        $timeRange = '30d';
        $videoMetrics = $analytics->getVideoMetrics(null, $timeRange);
        ob_start();
        include __DIR__ . '/admin/templates/analytics/videos_tpl.php';
        return ob_get_clean();
    },
    'content_tpl.php' => function() use ($d, $func, $analytics) {
        $timeRange = '30d';
        $contentMetrics = $analytics->getContentHookMetrics($timeRange);
        ob_start();
        include __DIR__ . '/admin/templates/analytics/content_tpl.php';
        return ob_get_clean();
    },
    'conversions_tpl.php' => function() use ($d, $func, $analytics) {
        $filterStatus = '';
        $filterPlatform = '';
        $filterAttributed = '';
        $conversions = $d->rawQuery("SELECT c.*, p.namevi as product_name, post.title as post_title FROM table_affiliate_conversion c LEFT JOIN table_product p ON (c.id_product = p.id) LEFT JOIN table_publish_post post ON (c.id_post = post.id) ORDER BY c.id DESC LIMIT 100");
        $allProducts = $d->rawQuery("SELECT id, namevi FROM table_product WHERE find_in_set('hienthi', status) ORDER BY id DESC LIMIT 50");
        $allPosts = $d->rawQuery("SELECT id, title, platform FROM table_publish_post ORDER BY id DESC LIMIT 50");
        ob_start();
        include __DIR__ . '/admin/templates/analytics/conversions_tpl.php';
        return ob_get_clean();
    },
    'winner_detection_tpl.php' => function() use ($d, $func, $winnerEngine, $analytics) {
        $rulesConfig = $winnerEngine->getRulesConfig();
        $evaluatedProducts = $analytics->getProductMetrics(null, 'all');
        $summaryStats = array('WINNER' => 0, 'PROMISING' => 0, 'TESTING' => 0, 'UNDERPERFORMING' => 0, 'INSUFFICIENT_DATA' => 0);
        foreach ($evaluatedProducts as $p) {
            $st = $p['winner_status'] ?? 'INSUFFICIENT_DATA';
            if (isset($summaryStats[$st])) $summaryStats[$st]++;
        }
        ob_start();
        include __DIR__ . '/admin/templates/analytics/winner_detection_tpl.php';
        return ob_get_clean();
    },
    'winner_rules_tpl.php' => function() use ($d, $func, $winnerEngine, $analytics) {
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
        ob_start();
        include __DIR__ . '/admin/templates/analytics/winner_rules_tpl.php';
        return ob_get_clean();
    },
    'conversion_import_tpl.php' => function() use ($d, $func, $importer) {
        $importLogs = $d->rawQuery("SELECT * FROM table_conversion_import_log ORDER BY id DESC LIMIT 10");
        $previewResult = null;
        $uploadError = null;
        ob_start();
        include __DIR__ . '/admin/templates/analytics/conversion_import_tpl.php';
        return ob_get_clean();
    }
];

echo "=======================================================\n";
echo "FITNADO PHASE 08 - DIRECT TEMPLATE RENDERING VERIFICATION\n";
echo "=======================================================\n\n";

$pass = 0;
$fail = 0;

foreach ($views as $viewName => $renderer) {
    try {
        $html = $renderer();
        if (strlen($html) > 500 && (stripos($html, '<table') !== false || stripos($html, '<form') !== false || stripos($html, 'card') !== false)) {
            echo sprintf("[PASS] %-30s | Rendered %6d bytes\n", $viewName, strlen($html));
            $pass++;
        } else {
            echo sprintf("[FAIL] %-30s | Output too small (%d bytes)\n", $viewName, strlen($html));
            $fail++;
        }
    } catch (Throwable $e) {
        echo sprintf("[FAIL] %-30s | Exception: %s (Line %d in %s)\n", $viewName, $e->getMessage(), $e->getLine(), $e->getFile());
        $fail++;
    }
}

echo "\n=======================================================\n";
echo sprintf("TEMPLATE RESULTS: %d PASSED, %d FAILED\n", $pass, $fail);
echo "=======================================================\n";

if ($fail > 0) exit(1);
exit(0);
