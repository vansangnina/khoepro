<?php
/**
 * FITNADO - Background AI Content Worker (Phase 05 & Phase 10 Operations)
 * Can be executed via CLI: php cron/ai_content_worker.php
 * Or via secure HTTP token: /cron/ai_content_worker.php?token=SECRET_TOKEN
 * PHP 7.4 Compatible
 */

define('LIBRARIES', dirname(__DIR__) . '/libraries/');

// Set server variables if CLI
if (php_sapi_name() === 'cli') {
    $_SERVER['SERVER_NAME'] = 'localhost';
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['REQUEST_URI'] = '/cron/ai_content_worker.php';
}

require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

// Secret token protection for HTTP execution
$cronToken = 'fitnado_ai_content_cron_2026';
if (php_sapi_name() !== 'cli') {
    $reqToken = $_GET['token'] ?? '';
    if ($reqToken !== $cronToken) {
        header('HTTP/1.0 403 Forbidden');
        die(json_encode(array('status' => false, 'error' => 'Invalid or missing cron token')));
    }
}

$dbConfig = $config['database'];
if (file_exists('/Applications/MAMP/tmp/mysql/mysql.sock')) {
    $dbConfig['unix_socket'] = '/Applications/MAMP/tmp/mysql/mysql.sock';
}
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);

require_once LIBRARIES . 'class/class.OperationsService.php';
$ops = new OperationsService($d, $func);

// Automation switch check
if (!$ops->isAutomationEnabled('content')) {
    $msg = "AI Content Automation is disabled by Operations Center. Exiting.";
    echo "[" . date('Y-m-d H:i:s') . "] {$msg}\n";
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: application/json');
        echo json_encode(array('status' => false, 'message' => $msg));
    }
    exit(0);
}

$ops->recordWorkerStart('ai_content_worker', 'worker');

require_once LIBRARIES . 'class/class.AIContentJobQueue.php';
$queue = new AIContentJobQueue($d, $func);

// 1. Recover stale jobs (> 10 mins)
$queue->recoverStaleJobs(600);

$maxJobsPerRun = 5;
$processed = 0;
$log = array();

echo "[" . date('Y-m-d H:i:s') . "] Starting FITNADO AI Content Worker...\n";

try {
    while ($processed < $maxJobsPerRun) {
        $job = $queue->getNextPendingJob();
        if (!$job) {
            break;
        }

        $ops->recordHeartbeat('ai_content_worker', array('current_job_id' => $job['id']));
        echo "Processing Content Job #{$job['id']} (Product #{$job['id_product']}, Types: {$job['content_types']})...\n";

        $execRes = $queue->executeJob($job['id']);
        $processed++;

        if ($execRes['status']) {
            echo "Job #{$job['id']} SUCCESS: Generated: {$execRes['generated_count']} items (Time: {$execRes['duration']}s)\n";
        } else {
            echo "Job #{$job['id']} FAILED: " . ($execRes['error'] ?? 'Unknown error') . "\n";
        }

        $log[] = array('job_id' => $job['id'], 'result' => $execRes);
    }

    $ops->recordWorkerSuccess('ai_content_worker', array('processed_count' => $processed));
    echo "[" . date('Y-m-d H:i:s') . "] AI Content Worker finished. Total jobs processed: {$processed}.\n";
} catch (Exception $e) {
    $ops->recordWorkerError('ai_content_worker', $e->getMessage());
    echo "[" . date('Y-m-d H:i:s') . "] Fatal error in AI Content worker: " . $e->getMessage() . "\n";
}

if (php_sapi_name() !== 'cli') {
    header('Content-Type: application/json');
    echo json_encode(array(
        'status' => true,
        'processed' => $processed,
        'log' => $log,
        'timestamp' => time()
    ));
}
