<?php
/**
 * FITNADO Video Render Background Worker
 * Phase 06: AI Video Production Engine
 * PHP 7.4 Compatible
 *
 * Usage CLI:
 *   php cron/video_render_worker.php --limit=5
 *
 * Usage HTTP:
 *   GET /cron/video_render_worker.php?token=FITNADO_VIDEO_SECRET_TOKEN_2026&limit=5
 */

define('LIBRARIES', __DIR__ . '/../libraries/');
require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$dbConfig = $config['database'];
if (file_exists('/Applications/MAMP/tmp/mysql/mysql.sock')) {
    $dbConfig['unix_socket'] = '/Applications/MAMP/tmp/mysql/mysql.sock';
}
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);

require_once LIBRARIES . 'class/class.VideoProvider.php';
require_once LIBRARIES . 'class/class.AIVideoEngine.php';
require_once LIBRARIES . 'class/class.AIVideoJobQueue.php';

// Bảo vệ bằng Token nếu chạy qua Web HTTP
$isCli = (php_sapi_name() === 'cli');
if (!$isCli) {
    $validToken = 'FITNADO_VIDEO_SECRET_TOKEN_2026';
    $reqToken = !empty($_GET['token']) ? $_GET['token'] : '';
    if ($reqToken !== $validToken) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(array('error' => 'Unauthorized access token'));
        exit();
    }
}

// Giới hạn số lượng job mỗi lần chạy
$limit = 5;
if ($isCli) {
    global $argv;
    foreach ($argv as $arg) {
        if (strpos($arg, '--limit=') === 0) {
            $limit = (int)str_replace('--limit=', '', $arg);
        }
    }
} else {
    if (!empty($_GET['limit'])) {
        $limit = (int)$_GET['limit'];
    }
}
$limit = max(1, min($limit, 20));

$queue = new AIVideoJobQueue($d, $func);
$batchResult = $queue->processBatch($limit);

if ($isCli) {
    echo "====================================================\n";
    echo "FITNADO AI VIDEO WORKER EXECUTED\n";
    echo "Processed Jobs: " . $batchResult['processed_count'] . "\n";
    foreach ($batchResult['results'] as $res) {
        $statusStr = !empty($res['success']) ? 'SUCCESS (' . ($res['status'] ?? 'OK') . ')' : 'FAILED (' . ($res['error'] ?? 'Unknown') . ')';
        echo " - Job #" . ($res['id_job'] ?? 'N/A') . " (Video #" . ($res['id_video'] ?? 'N/A') . "): " . $statusStr . "\n";
    }
    echo "====================================================\n";
} else {
    header('Content-Type: application/json');
    echo json_encode($batchResult);
}
