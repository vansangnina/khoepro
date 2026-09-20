<?php
/**
 * FITNADO Background Publish Worker
 * Phase 07: Publishing Center & Phase 10 Operations
 * CLI or HTTP Token protected cron worker for processing scheduled video publications
 * Usage CLI: php cron/publish_worker.php
 * Usage HTTP: https://domain/cron/publish_worker.php?token=SECRET_TOKEN
 * PHP 7.4 Compatible
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

$queue = new PublishJobQueue($d, $func);
$limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;

$startTime = microtime(true);

try {
    $ops->recordHeartbeat('publish_worker', array('limit' => $limit));
    $results = $queue->processScheduledPosts($limit, $isCli ? 'cli_worker' : 'http_worker');
    $duration = round(microtime(true) - $startTime, 3);
    $ops->recordWorkerSuccess('publish_worker', array('processed' => $results['processed'] ?? 0));

    $output = array(
        'status' => 'success',
        'timestamp' => time(),
        'datetime' => date('Y-m-d H:i:s'),
        'duration_seconds' => $duration,
        'results' => $results
    );

    if ($isCli) {
        echo "=======================================================\n";
        echo "FITNADO PUBLISH WORKER (Cron Engine)\n";
        echo "Time: " . date('Y-m-d H:i:s') . "\n";
        echo "Processed: {$results['processed']} scheduled posts\n";
        echo " - Manual Due (Ready for Admin): {$results['manual_due']}\n";
        echo " - API Published: {$results['api_published']}\n";
        echo " - Failed: {$results['failed']}\n";
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
