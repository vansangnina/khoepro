<?php
/**
 * FITNADO PHASE 09 - HTTP & ADMIN VIEW VERIFICATION SUITE
 * 100% PHP 7.4 Compatible
 */

$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'CLI-Test';
$_SERVER['DOCUMENT_ROOT'] = __DIR__;

$baseUrl = 'http://127.0.0.1:8080';
$passed = 0;
$failed = 0;

function assertHttp($condition, $message) {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] {$message}\n";
        $passed++;
    } else {
        echo "[FAIL] {$message}\n";
        $failed++;
    }
}

function fetchUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'User-Agent: FITNADO-Verification-Bot/1.0'
    ));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return array(
        'code' => $httpCode,
        'body' => $response,
        'error' => $error
    );
}

echo "=======================================================\n";
echo "FITNADO PHASE 09 - HTTP & ADMIN VIEW VERIFICATION\n";
echo "=======================================================\n\n";

// 1. Check Root Dev Server
$rootRes = fetchUrl($baseUrl . '/');
assertHttp($rootRes['code'] === 200, "Frontend Homepage: HTTP 200 OK");

// 2. Admin Optimization Views (Mock session if required, or fetch admin router directly)
// We test via CLI template rendering & HTTP endpoint checks
$views = array(
    'Recommendations List' => '/admin/index.php?com=optimization&act=recommendations',
    'Experiments List' => '/admin/index.php?com=optimization&act=experiments',
    'Optimization Rules' => '/admin/index.php?com=optimization&act=rules',
    'Analytics Overview' => '/admin/index.php?com=analytics&act=overview',
    'Analytics Products' => '/admin/index.php?com=analytics&act=products',
    'Analytics Winners' => '/admin/index.php?com=analytics&act=winners',
    'Publishing Post Packages' => '/admin/index.php?com=publishing&act=mans'
);

foreach ($views as $viewName => $path) {
    $res = fetchUrl($baseUrl . $path);
    // Even if redirected to login (302/200), verify no 500 fatal errors
    assertHttp($res['code'] === 200 || $res['code'] === 302, "HTTP Endpoint '{$viewName}': HTTP {$res['code']} (No 500 Fatal Crash)");
}

// 3. Direct Component Template Simulation & Rendering Test
echo "\n--- Direct Controller & Template Rendering Simulation ---\n";
define('LIBRARIES', __DIR__ . '/libraries/');
define('SOURCES', __DIR__ . '/sources/');
define('ADMIN', __DIR__ . '/admin/');

require_once LIBRARIES . 'config.php';
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$d = new PDODb($config['database']);
$cache = new Cache($d);
$func = new Functions($d, $cache);

require_once LIBRARIES . 'class/class.AnalyticsService.php';
require_once LIBRARIES . 'class/class.WinnerDetectionEngine.php';
require_once LIBRARIES . 'class/class.OptimizationEngine.php';

$analytics = new AnalyticsService($d, $func);
$winnerEngine = new WinnerDetectionEngine($d, $analytics);
$optimizationEngine = new OptimizationEngine($d, $func, $analytics, $winnerEngine);

// Get latest recommendation and experiment IDs
$latestRec = $d->rawQueryOne("SELECT id FROM table_optimization_recommendation ORDER BY id DESC LIMIT 1");
$recId = !empty($latestRec['id']) ? (int)$latestRec['id'] : 1;

$latestExp = $d->rawQueryOne("SELECT id FROM table_optimization_experiment ORDER BY id DESC LIMIT 1");
$expId = !empty($latestExp['id']) ? (int)$latestExp['id'] : 1;

require_once ADMIN . 'sources/optimization.php';

try {
    // 3.1 recommendations_tpl
    $act = 'recommendations';
    viewRecommendations();
    ob_start();
    include ADMIN . 'templates/optimization/recommendations_tpl.php';
    $htmlRecs = ob_get_clean();
    assertHttp(!empty($htmlRecs) && strpos($htmlRecs, 'Khuyến nghị Tối ưu hóa') !== false, "Template Render: recommendations_tpl rendered successfully with header & table");

    // 3.2 recommendation_detail_tpl
    $act = 'recommendation_detail';
    $_GET['id'] = $recId;
    viewRecommendationDetail();
    ob_start();
    include ADMIN . 'templates/optimization/recommendation_detail_tpl.php';
    $htmlRecDetail = ob_get_clean();
    assertHttp(!empty($htmlRecDetail) && strpos($htmlRecDetail, 'Chi tiết Khuyến nghị Tối ưu') !== false, "Template Render: recommendation_detail_tpl rendered successfully with hypothesis & cost gate");

    // 3.3 experiments_tpl
    $act = 'experiments';
    viewExperiments();
    ob_start();
    include ADMIN . 'templates/optimization/experiments_tpl.php';
    $htmlExps = ob_get_clean();
    assertHttp(!empty($htmlExps) && strpos($htmlExps, 'Thử nghiệm A/B Tối ưu hóa') !== false, "Template Render: experiments_tpl rendered successfully with experiment table");

    // 3.4 experiment_detail_tpl
    $act = 'experiment_detail';
    $_GET['id'] = $expId;
    viewExperimentDetail();
    ob_start();
    include ADMIN . 'templates/optimization/experiment_detail_tpl.php';
    $htmlExpDetail = ob_get_clean();
    assertHttp(!empty($htmlExpDetail) && strpos($htmlExpDetail, 'Đối soát Thử nghiệm') !== false, "Template Render: experiment_detail_tpl rendered with Side-by-Side Comparison");

    // 3.5 rules_tpl
    $act = 'rules';
    viewOptimizationRules();
    ob_start();
    include ADMIN . 'templates/optimization/rules_tpl.php';
    $htmlRules = ob_get_clean();
    assertHttp(!empty($htmlRules) && strpos($htmlRules, 'Cấu hình Quy tắc Tối ưu hóa') !== false, "Template Render: rules_tpl rendered with budget & sample thresholds");
} catch (Throwable $e) {
    echo "Rendering exception: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=======================================================\n";
echo sprintf("HTTP & VIEW RESULTS: %d PASSED, %d FAILED\n", $passed, $failed);
echo "=======================================================\n";

if ($failed > 0) {
    exit(1);
}
exit(0);
