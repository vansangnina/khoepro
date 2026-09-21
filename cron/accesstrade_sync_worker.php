<?php
/**
 * FITNADO Background ACCESSTRADE Sync Worker
 * Phase 10.1: Production Readiness & ACCESSTRADE Publisher API Integration
 * Usage CLI: php cron/accesstrade_sync_worker.php
 * Usage HTTP: https://domain/cron/accesstrade_sync_worker.php?token=SECRET_TOKEN&days=30
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
if (!$ops->isAutomationEnabled('affiliate')) {
    $msg = "Affiliate Automation is disabled by Operations Center. Exiting.";
    if ($isCli) {
        echo "[" . date('Y-m-d H:i:s') . "] {$msg}\n";
    } else {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'message' => $msg));
    }
    exit(0);
}

$ops->recordWorkerStart('accesstrade_sync', 'worker');

require_once LIBRARIES . 'class/class.AccessTradeProvider.php';

$days = !empty($_GET['days']) ? (int)$_GET['days'] : 30;
$limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 100;

$startTime = microtime(true);

try {
    $ops->recordHeartbeat('accesstrade_sync', array('days' => $days, 'limit' => $limit));

    $provider = new AccessTradeProvider($d, $func);
    $syncRes = $provider->syncTransactions(array(
        'since_days' => $days,
        'limit' => $limit
    ));

    $duration = round(microtime(true) - $startTime, 3);

    if ($syncRes['success']) {
        $ops->recordWorkerSuccess('accesstrade_sync', array(
            'total' => $syncRes['total'] ?? 0,
            'imported' => $syncRes['imported'] ?? 0,
            'updated' => $syncRes['updated'] ?? 0,
            'skipped' => $syncRes['skipped'] ?? 0
        ));
    } else {
        $ops->recordWorkerError('accesstrade_sync', $syncRes['error'] ?? 'Sync failed');
    }

    $output = array(
        'status' => $syncRes['success'] ? 'success' : 'error',
        'timestamp' => time(),
        'datetime' => date('Y-m-d H:i:s'),
        'duration_seconds' => $duration,
        'results' => $syncRes
    );

    if ($isCli) {
        echo "=======================================================\n";
        echo "FITNADO ACCESSTRADE SYNC WORKER\n";
        echo "Time: " . date('Y-m-d H:i:s') . "\n";
        echo "Status: " . ($syncRes['success'] ? 'SUCCESS' : 'FAILED') . "\n";
        echo "Total Fetched: " . ($syncRes['total'] ?? 0) . "\n";
        echo " - New Imported: " . ($syncRes['imported'] ?? 0) . "\n";
        echo " - Status Updated: " . ($syncRes['updated'] ?? 0) . "\n";
        echo " - Skipped / Unchanged: " . ($syncRes['skipped'] ?? 0) . "\n";
        if (!empty($syncRes['error'])) {
            echo " - Error: " . $syncRes['error'] . "\n";
        }
        echo "Duration: {$duration}s\n";
        echo "=======================================================\n";
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    $ops->recordWorkerError('accesstrade_sync', $e->getMessage());
    if ($isCli) {
        echo "[" . date('Y-m-d H:i:s') . "] Fatal error in ACCESSTRADE Sync worker: " . $e->getMessage() . "\n";
    } else {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'error' => $e->getMessage()));
    }
}
