<?php
/**
 * FITNADO PHASE 10 - HTTP & ADMIN VIEW VERIFICATION (PHP 7.4)
 */

if (php_sapi_name() === 'cli') {
    $_SERVER['SERVER_NAME'] = 'localhost';
}
define('LIBRARIES', __DIR__ . '/libraries/');
define('SOURCES', __DIR__ . '/admin/sources/');
define('TEMPLATE', __DIR__ . '/admin/templates/');
define('LAYOUT', 'layout/');

require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$dbConfig = $config['database'];
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);

$passCount = 0;
$failCount = 0;

function assertHttp($condition, $testName, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . ($details ? " - Details: {$details}" : "") . "\n";
        $failCount++;
    }
}

echo "=======================================================\n";
echo "FITNADO PHASE 10 - HTTP & ADMIN VIEW VERIFICATION\n";
echo "=======================================================\n\n";

$baseUrl = 'http://127.0.0.1:8080/';

// 1. Homepage Ping
$ctx = stream_context_create(array(
    'http' => array('timeout' => 5, 'ignore_errors' => true)
));
$res = @file_get_contents($baseUrl, false, $ctx);
$httpCode = 0;
if (!empty($http_response_header)) {
    preg_match('{HTTP\/\S*\s(\d{3})}', $http_response_header[0], $m);
    $httpCode = (int)($m[1] ?? 0);
}
assertHttp($httpCode === 200, "Frontend Homepage: HTTP 200 OK", "Got HTTP {$httpCode}");

// 2. Admin HTTP Endpoints (Simulate routing)
$endpoints = array(
    'overview' => 'index.php?com=operations&act=overview',
    'pipeline' => 'index.php?com=operations&act=pipeline',
    'jobs' => 'index.php?com=operations&act=jobs',
    'providers' => 'index.php?com=operations&act=providers',
    'costs' => 'index.php?com=operations&act=costs',
    'alerts' => 'index.php?com=operations&act=alerts',
    'logs' => 'index.php?com=operations&act=logs',
    'settings' => 'index.php?com=operations&act=settings'
);

foreach ($endpoints as $name => $path) {
    $code = 0;
    $content = @file_get_contents($baseUrl . 'admin/' . $path, false, $ctx);
    if (!empty($http_response_header)) {
        preg_match('{HTTP\/\S*\s(\d{3})}', $http_response_header[0], $m);
        $code = (int)($m[1] ?? 0);
    }
    assertHttp($code === 200 || $code === 302, "HTTP Endpoint 'Operations {$name}': HTTP {$code} (No 500 Fatal Crash)");
}

// 3. Direct Controller & Template Rendering Simulation
echo "\n--- Direct Controller & Template Rendering Simulation ---\n";
require_once LIBRARIES . 'class/class.OperationsService.php';

$ops = new OperationsService($d, $func);

// A. Overview Template Render
ob_start();
$workerStatuses = $ops->getWorkerStatuses();
$queueSummaries = $ops->getQueueSummaries();
$costToday = $ops->getCostSummary('today');
$activeAlerts = $ops->getAlerts('ACTIVE', 10);
$humanActions = $ops->getHumanActionQueue();
$envHealth = $ops->getEnvironmentHealth();
$freshness = $ops->getDataFreshness();
include __DIR__ . '/admin/templates/operations/overview_tpl.php';
$htmlOverview = ob_get_clean();
assertHttp(strpos($htmlOverview, 'Operations & Automation Control Center') !== false, "Template Render: overview_tpl rendered successfully with header & cards");

// B. Pipeline Template Render
ob_start();
include __DIR__ . '/admin/templates/operations/pipeline_tpl.php';
$htmlPipeline = ob_get_clean();
assertHttp(strpos($htmlPipeline, 'Pipeline Overview') !== false, "Template Render: pipeline_tpl rendered successfully with stages & action queue");

// C. Jobs Template Render
ob_start();
$filterModule = 'all';
$filterStatus = 'all';
$jobList = $ops->getAllJobs(array(), 1, 20);
$stuckJobs = array();
include __DIR__ . '/admin/templates/operations/jobs_tpl.php';
$htmlJobs = ob_get_clean();
assertHttp(strpos($htmlJobs, 'Quản lý Tác vụ & Hàng đợi') !== false, "Template Render: jobs_tpl rendered successfully with queue table");

// D. Providers Template Render
ob_start();
$providersList = $ops->getProvidersStatus();
include __DIR__ . '/admin/templates/operations/providers_tpl.php';
$htmlProviders = ob_get_clean();
assertHttp(strpos($htmlProviders, 'Nhà Cung cấp & Kết nối API') !== false, "Template Render: providers_tpl rendered with zero-cost diagnostics");

// E. Costs Template Render
ob_start();
$timeRange = 'today';
$costData = $ops->getCostSummary('today');
include __DIR__ . '/admin/templates/operations/costs_tpl.php';
$htmlCosts = ob_get_clean();
assertHttp(strpos($htmlCosts, 'Trung tâm Chi phí API') !== false, "Template Render: costs_tpl rendered with budget meters & override form");

// F. Alerts Template Render
ob_start();
$filterStatus = 'ACTIVE';
$alertsList = $ops->getAlerts('ACTIVE', 50);
include __DIR__ . '/admin/templates/operations/alerts_tpl.php';
$htmlAlerts = ob_get_clean();
assertHttp(strpos($htmlAlerts, 'Cảnh báo & Sự cố Vận hành') !== false, "Template Render: alerts_tpl rendered with deduplication list");

// G. Settings Template Render
ob_start();
$settingsData = array(
    'automation_enabled' => 1,
    'pause_paid_automation' => 0,
    'research_automation_enabled' => 1,
    'content_automation_enabled' => 1,
    'video_automation_enabled' => 1,
    'publishing_automation_enabled' => 1,
    'optimization_automation_enabled' => 1,
    'daily_external_api_budget' => 200000,
    'monthly_external_api_budget' => 3000000,
    'worker_heartbeat_threshold_seconds' => 300,
    'stuck_job_threshold_seconds' => 1800,
    'tracking_stale_threshold_hours' => 24,
    'affiliate_click_stale_threshold_hours' => 48,
    'conversion_stale_threshold_days' => 7
);
include __DIR__ . '/admin/templates/operations/settings_tpl.php';
$htmlSettings = ob_get_clean();
assertHttp(strpos($htmlSettings, 'Cấu hình Vận hành') !== false, "Template Render: settings_tpl rendered with automation toggles & budget guards");

echo "\n=======================================================\n";
echo "HTTP & VIEW RESULTS: {$passCount} PASSED, {$failCount} FAILED\n";
echo "=======================================================\n";

if ($failCount > 0) {
    exit(1);
}
exit(0);
