<?php
/**
 * KHOEPRO Background Multi-Social Publish Worker & Scheduler
 * Processes scheduled video publications, dynamic buffer quota scheduling, and engagement metrics synchronization.
 * 
 * Usage CLI: php cron/publish_worker.php
 * Usage HTTP: https://domain/cron/publish_worker.php?token=SECRET_TOKEN
 * PHP 7.4 & 8.x Compatible
 */

define('LIBRARIES', __DIR__ . '/../libraries/');
require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$isCli = (php_sapi_name() === 'cli');
if (!$isCli) {
    $token = !empty($_GET['token']) ? trim($_GET['token']) : '';
    $expectedToken = md5(NN_CONTRACT . ($config['database']['dbname'] ?? 'masterpdo'));
    if ($token !== $expectedToken) {
        header('HTTP/1.0 403 Forbidden');
        die("403 Forbidden: Invalid access token.");
    }
}

$dbConfig = $config['database'];
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);

require_once LIBRARIES . 'class/class.OperationsService.php';
$ops = new OperationsService($d, $func);

// Automation switch check
if (!$ops->isAutomationEnabled('publishing')) {
    $msg = "Publishing Automation is disabled by Operations Center. Exiting.";
    if ($isCli) {
        echo "[" . date('Y-m-d H:i:s') . "] {$msg}\n";
    } else {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'message' => $msg));
    }
    exit(0);
}

$ops->recordWorkerStart('publish_worker', 'worker');

require_once LIBRARIES . 'class/class.PublishJobQueue.php';
require_once LIBRARIES . 'class/class.SocialSchedulerEngine.php';
require_once LIBRARIES . 'class/class.SocialAnalyticsSync.php';

$scheduler = new SocialSchedulerEngine($d, $func);
$analyticsSync = new SocialAnalyticsSync($d, $func);
$queue = new PublishJobQueue($d, $func);

$limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;
$startTime = microtime(true);

try {
    $ops->recordHeartbeat('publish_worker', array('limit' => $limit));

    // 1. Dynamic Scheduling: Fill daily slots for active accounts from Approved Pool
    $activeAccounts = $d->rawQuery("SELECT id, platform FROM table_publish_account WHERE status = 'active' LIMIT 10");
    $scheduledDetails = array();
    foreach ($activeAccounts as $acc) {
        $schedRes = $scheduler->scheduleApprovedPool((int)$acc['id'], $isCli ? 'cli_scheduler' : 'http_scheduler');
        $scheduledDetails[$acc['platform'] . '_' . $acc['id']] = $schedRes;
    }

    // 2. Dispatch Due Posts with Atomic Locking & Retry Backoff
    $dispatchResults = $scheduler->dispatchDuePosts($limit, $isCli ? 'cli_worker' : 'http_worker');

    // 3. Sync Social Post Engagement Metrics & Feedback Loop
    $metricsRes = $analyticsSync->syncPostMetrics(20);
    $feedbackRes = $analyticsSync->applyFeedbackLoop();

    $duration = round(microtime(true) - $startTime, 3);
    $ops->recordWorkerSuccess('publish_worker', array(
        'dispatched' => $dispatchResults['dispatched'] ?? 0,
        'api_published' => $dispatchResults['api_published'] ?? 0,
        'manual_ready' => $dispatchResults['manual_ready'] ?? 0,
        'metrics_synced' => $metricsRes['synced'] ?? 0
    ));

    $output = array(
        'status' => 'success',
        'timestamp' => time(),
        'datetime' => date('Y-m-d H:i:s'),
        'duration_seconds' => $duration,
        'scheduling' => $scheduledDetails,
        'dispatching' => $dispatchResults,
        'analytics_sync' => $metricsRes,
        'feedback_loop' => $feedbackRes
    );

    if ($isCli) {
        echo "=======================================================\n";
        echo "KHOEPRO MULTI-SOCIAL PUBLISH WORKER & SCHEDULER\n";
        echo "Time: " . date('Y-m-d H:i:s') . "\n";
        echo "Dispatched Posts: {$dispatchResults['dispatched']}\n";
        echo " - API Published: {$dispatchResults['api_published']}\n";
        echo " - Manual Ready: {$dispatchResults['manual_ready']}\n";
        echo " - Failed / Retrying: {$dispatchResults['failed']}\n";
        echo "Metrics Synced: {$metricsRes['synced']} posts\n";
        echo "Feedback Loop: {$feedbackRes['products_updated']} products score boosted\n";
        echo "Duration: {$duration}s\n";
        echo "=======================================================\n";
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    $ops->recordWorkerError('publish_worker', $e->getMessage());
    if ($isCli) {
        echo "[" . date('Y-m-d H:i:s') . "] Fatal error in Publish worker: " . $e->getMessage() . "\n";
    } else {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => $e->getMessage()));
    }
}
