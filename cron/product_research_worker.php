<?php
/**
 * FITNADO - Background Product Research Worker (Phase 04)
 * CLI Cron & Secure Web Runner
 * PHP 7.4 Compatible
 */

// Environment initialization
if (php_sapi_name() !== 'cli') {
    // Web request security token check
    $secretToken = 'FITNADO_RESEARCH_SECRET_2026';
    if (!isset($_GET['token']) || $_GET['token'] !== $secretToken) {
        http_response_code(403);
        die("403 Forbidden: Invalid or missing security token.");
    }
}

$_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'localhost';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';

define('LIBRARIES', __DIR__ . '/../libraries/');
require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$dbConfig = $config['database'];
// Local socket support
if (file_exists('/Applications/MAMP/tmp/mysql/mysql.sock')) {
    $dbConfig['unix_socket'] = '/Applications/MAMP/tmp/mysql/mysql.sock';
}
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);

$queue = new ResearchJobQueue($d, $func);

echo "[" . date('Y-m-d H:i:s') . "] Starting FITNADO Product Research Worker...\n";

// 1. Process Due Scheduled Seeds (Daily/Weekly Seeds)
$now = time();
$dueSeeds = $d->rawQuery(
    "select * from #_product_research_seed where status = 'active' and frequency in ('daily', 'weekly') and next_run > 0 and next_run <= ?",
    array($now)
);

if (!empty($dueSeeds)) {
    echo "Found " . count($dueSeeds) . " scheduled seeds due for research.\n";
    foreach ($dueSeeds as $sd) {
        $jobId = $queue->createJob($sd['id'], $sd['platform'] ?: 'ai_agent', $sd['depth'] ?: 'STANDARD');
        echo "Created scheduled Job #{$jobId} for Seed: '{$sd['title']}'\n";
    }
}

// 2. Process Pending Jobs in Queue
$processedCount = 0;
$maxJobsPerRun = 5;

while ($processedCount < $maxJobsPerRun) {
    $job = $queue->getNextPendingJob();
    if (empty($job)) {
        break; // Queue is empty
    }

    echo "Processing Job #{$job['id']} (Provider: {$job['provider']}, Depth: {$job['depth']})...\n";
    $result = $queue->executeJob($job['id']);

    if ($result['status']) {
        echo "Job #{$job['id']} SUCCESS: Found: {$result['candidates_found']}, Created: {$result['candidates_created']}, Duplicates: {$result['duplicates_count']} (Time: {$result['duration']}s)\n";
    } else {
        echo "Job #{$job['id']} FAILED: " . ($result['error'] ?? 'Unknown error') . "\n";
    }

    $processedCount++;
}

echo "[" . date('Y-m-d H:i:s') . "] Worker finished. Total jobs processed: {$processedCount}.\n";
