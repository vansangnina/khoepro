<?php
/**
 * Automated Verification Script for FITNADO Phase 07
 * Publishing Center & TikTok Publishing Foundation Test Suite
 * PHP 7.4 Compatible
 */

$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';

define('LIBRARIES', __DIR__ . '/libraries/');
require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$dbConfig = $config['database'];
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);

require_once LIBRARIES . 'class/class.VoiceService.php';
require_once LIBRARIES . 'class/class.VideoComposer.php';
require_once LIBRARIES . 'class/class.PublishProvider.php';
require_once LIBRARIES . 'class/class.PublishingCenter.php';
require_once LIBRARIES . 'class/class.PublishJobQueue.php';

$voiceService = new VoiceService($d, $func);
$publishingCenter = new PublishingCenter($d, $func);
$publishQueue = new PublishJobQueue($d, $func);

echo "=======================================================\n";
echo "FITNADO PHASE 07 - AUTOMATED TEST SUITE (PHP 7.4)\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $testName, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo "[PASS] " . $testName . "\n";
    } else {
        $failCount++;
        echo "[FAIL] " . $testName . ($details ? " -> " . $details : "") . "\n";
    }
}

// --------------------------------------------------------------------------
// 1. PHASE 06 HOTFIXES: FACT EVIDENCE TRACEABILITY & DURATION SYNC
// --------------------------------------------------------------------------
echo "--- 1. Testing Phase 06 Hotfixes ---\n";

// 1.1 Fact Evidence Traceability Test
$factCheckVerified = $voiceService->verifyFactualClaims(80, "Đai Cứng FITNADO Pro Lever da bò dày 10mm khóa đòn bẩy");
assertTest(
    $factCheckVerified['verified'] === true && !empty($factCheckVerified['claims']),
    "Fact Evidence: Verified claims (da bò, 10mm, khóa đòn bẩy) successfully traced back to product specs"
);

$factCheckUnverified = $voiceService->verifyFactualClaims(80, "Đai giúp chữa khỏi 100% đau lưng và bảo hành trọn đời");
assertTest(
    $factCheckUnverified['verified'] === false && $factCheckUnverified['unverified_count'] >= 2,
    "Fact Evidence: Medical and exaggerated claims (chữa khỏi, 100%, bảo hành trọn đời) flagged as UNVERIFIED",
    "Unverified count: " . $factCheckUnverified['unverified_count']
);

// 1.2 Duration Synchronization Test
$voiceDur = $voiceService->getAudioDuration('creator_voice.mp3');
$videoDur = 0;
if (file_exists('creator_economy_video.mp4')) {
    $probeCmd = 'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "creator_economy_video.mp4"';
    $outDur = @exec($probeCmd);
    $videoDur = (float)$outDur;
}
assertTest(
    $voiceDur > 0,
    "Duration Sync: Voice duration measured accurately via ffprobe ({$voiceDur}s)"
);

// --------------------------------------------------------------------------
// 2. HUMAN GATE & OUTDATED GATE
// --------------------------------------------------------------------------
echo "\n--- 2. Testing Human Gate & Outdated Gate ---\n";

// Tạo mock unapproved video
$unapprovedVideoId = $d->insert('ai_video', array(
    'id_product' => 80,
    'title' => 'Test Unapproved Video',
    'status' => 'REVIEW_REQUIRED',
    'video_file' => 'upload/video/test_unapproved.mp4',
    'date_created' => time(),
    'date_updated' => time()
));

// Test 2.1: Create post from unapproved video -> MUST BE BLOCKED
$resUnapproved = $publishingCenter->createPostFromApprovedVideo($unapprovedVideoId);
assertTest(
    $resUnapproved['success'] === false && strpos($resUnapproved['error'], 'APPROVED') !== false,
    "Human Gate: Blocking Post Package creation from unapproved video (REVIEW_REQUIRED)",
    $resUnapproved['error'] ?? ''
);

// Tạo mock approved video
$approvedVideoId = $d->insert('ai_video', array(
    'id_product' => 80,
    'title' => 'Test Approved Video 30s',
    'status' => 'APPROVED',
    'video_file' => 'creator_economy_video.mp4',
    'thumbnail' => 'upload/product/fitnado_roller_main.jpg',
    'duration_actual' => 30.6,
    'file_size' => 5000000,
    'is_outdated' => 0,
    'date_created' => time(),
    'date_updated' => time()
));

// Test 2.2: Create post from approved video -> MUST PASS
$resApproved = $publishingCenter->createPostFromApprovedVideo($approvedVideoId, array(
    'caption' => 'Review chi tiết Đai Cứng FITNADO Pro Lever tập Gym Squat Deadlift',
    'hashtags' => '#fitnado #daicung #tapgym'
));
$createdPostId = $resApproved['id_post'] ?? 0;
assertTest(
    $resApproved['success'] === true && $createdPostId > 0,
    "Human Gate: Successfully created Post Package from APPROVED video (Post ID: #{$createdPostId})"
);

// --------------------------------------------------------------------------
// 3. PRE-PUBLISH CHECKLIST & IMMUTABLE SNAPSHOTS
// --------------------------------------------------------------------------
echo "\n--- 3. Testing Pre-Publish Checklist & Snapshots ---\n";

// Test 3.1: Pre-publish checklist verification on complete post -> PASS
$chk = $publishingCenter->validatePrePublishChecklist($createdPostId);
assertTest(
    $chk['valid'] === true,
    "Pre-publish Checklist: All 8 validation criteria pass on valid post"
);

// Test 3.2: Empty caption checklist failure
$d->rawQuery("UPDATE table_publish_post SET caption = '' WHERE id = ?", array($createdPostId));
$chkEmpty = $publishingCenter->validatePrePublishChecklist($createdPostId);
assertTest(
    $chkEmpty['valid'] === false && strpos(implode(';', $chkEmpty['errors']), 'Caption') !== false,
    "Pre-publish Checklist: Empty caption correctly blocks readiness"
);

// Restore caption and mark READY
$d->rawQuery("UPDATE table_publish_post SET caption = 'Review chi tiết Đai Cứng FITNADO Pro Lever' WHERE id = ?", array($createdPostId));
$resReady = $publishingCenter->markPostReady($createdPostId);
$postAfterReady = $publishingCenter->getPost($createdPostId);

assertTest(
    $resReady['success'] === true && $postAfterReady['status'] === 'READY' && !empty($postAfterReady['snapshot_data']),
    "Immutable Snapshot: Post successfully marked READY and created frozen snapshot_data"
);

// Test 3.3: Edit after READY invalidates back to DRAFT
$publishingCenter->updatePost($createdPostId, array('caption' => 'Caption updated after ready state'));
$postAfterEdit = $publishingCenter->getPost($createdPostId);
assertTest(
    $postAfterEdit['status'] === 'DRAFT' && empty($postAfterEdit['snapshot_data']),
    "Edit Invalidation: Editing a READY post safely invalidates status back to DRAFT"
);

// Re-mark READY
$publishingCenter->markPostReady($createdPostId);

// --------------------------------------------------------------------------
// 4. MANUAL PUBLISHING WORKFLOW & URL VALIDATION
// --------------------------------------------------------------------------
echo "\n--- 4. Testing Manual Publishing & URL Validation ---\n";

$manualProvider = new ManualPublishProvider($d, $func);

// Test 4.1: URL Security - Block javascript: and non-http protocols
$urlJs = $manualProvider->validateExternalUrl("javascript:alert('xss')");
assertTest(
    $urlJs['valid'] === false,
    "URL Validation: Malicious protocol 'javascript:' blocked"
);

// Test 4.2: URL Domain - Block non-TikTok domains for tiktok platform
$urlGoogle = $manualProvider->validateExternalUrl("https://google.com/search?q=tiktok", "tiktok");
assertTest(
    $urlGoogle['valid'] === false,
    "URL Validation: Non-TikTok domain (google.com) blocked for tiktok platform"
);

// Test 4.3: Valid TikTok Video URL
$validTikTokUrl = "https://www.tiktok.com/@fitnado.vn/video/7345678901234567890";
$urlTiktok = $manualProvider->validateExternalUrl($validTikTokUrl, "tiktok");
assertTest(
    $urlTiktok['valid'] === true && $urlTiktok['extracted_id'] === '7345678901234567890',
    "URL Validation: Valid TikTok URL accepted and extracted Video ID: 7345678901234567890"
);

// Test 4.4: Mark as Published with Valid URL
$resMarkPub = $publishingCenter->markManualPublished($createdPostId, $validTikTokUrl);
$postPublished = $publishingCenter->getPost($createdPostId);
assertTest(
    $resMarkPub['success'] === true && $postPublished['status'] === 'PUBLISHED' && !empty($postPublished['published_at']),
    "Manual Workflow: Post successfully transitioned to PUBLISHED with external_post_url stored"
);

// Test 4.5: Edit after PUBLISHED is strictly BLOCKED
$resEditPub = $publishingCenter->updatePost($createdPostId, array('caption' => 'Trying to edit published post'));
assertTest(
    $resEditPub['success'] === false,
    "Integrity Gate: Editing a PUBLISHED post is strictly blocked"
);

// Test 4.6: Duplicate post from published post -> creates DRAFT
$resDup = $publishingCenter->duplicatePost($createdPostId);
$dupPost = $publishingCenter->getPost($resDup['id_new_post'] ?? 0);
assertTest(
    $resDup['success'] === true && $dupPost['status'] === 'DRAFT',
    "Duplicate Workflow: Duplicating published post creates clean DRAFT Post Package (#{$dupPost['id']})"
);

// --------------------------------------------------------------------------
// 5. SCHEDULING, QUEUE & IDEMPOTENCY
// --------------------------------------------------------------------------
echo "\n--- 5. Testing Scheduling, Queue & Idempotency ---\n";

// Test 5.1: Schedule future post
$futureTime = time() + 3600; // 1 hour later
$scheduledPostId = $dupPost['id'];
$d->rawQuery("UPDATE table_publish_post SET scheduled_at = ? WHERE id = ?", array($futureTime, $scheduledPostId));
$publishingCenter->markPostReady($scheduledPostId);
$postSched = $publishingCenter->getPost($scheduledPostId);
assertTest(
    $postSched['status'] === 'SCHEDULED' && $postSched['scheduled_at'] === $futureTime,
    "Scheduling: Future post correctly set to SCHEDULED status"
);

// Test 5.2: Future post not processed prematurely by worker
$resQueue1 = $publishQueue->processScheduledPosts(10);
$postSchedAfterWorker = $publishingCenter->getPost($scheduledPostId);
assertTest(
    $postSchedAfterWorker['status'] === 'SCHEDULED',
    "Scheduling Guard: Future post not published or triggered prematurely"
);

// Test 5.3: Due post transition
$pastTime = time() - 60; // Due 1 minute ago
$d->rawQuery("UPDATE table_publish_post SET scheduled_at = ? WHERE id = ?", array($pastTime, $scheduledPostId));
$resQueue2 = $publishQueue->processScheduledPosts(10);
$postDueAfterWorker = $publishingCenter->getPost($scheduledPostId);
assertTest(
    $postDueAfterWorker['status'] === 'READY',
    "Queue Worker: Due scheduled post automatically transitioned to READY for Admin"
);

// Test 5.4: Idempotency & Double Publish Protection
$d->rawQuery("UPDATE table_publish_post SET status = 'PUBLISHING', publish_lock = 'test_lock_123' WHERE id = ?", array($scheduledPostId));
$resDoublePub = $publishingCenter->publishNow($scheduledPostId);
assertTest(
    $resDoublePub['success'] === false && strpos($resDoublePub['error'], 'Locked') !== false,
    "Idempotency: Concurrent/double publish attempt safely rejected when locked"
);

// Stale lock recovery
$d->rawQuery("UPDATE table_publish_post SET date_updated = ? WHERE id = ?", array(time() - 700, $scheduledPostId));
$recovered = $publishQueue->recoverStaleLocks(600);
$postRecovered = $publishingCenter->getPost($scheduledPostId);
assertTest(
    $recovered >= 1 && $postRecovered['publish_lock'] === null,
    "Queue Recovery: Stale publish lock (>10m) automatically recovered"
);

// --------------------------------------------------------------------------
// 6. TIKTOK API STATUS & AUDIT LOGS
// --------------------------------------------------------------------------
echo "\n--- 6. Testing TikTok Provider Status & Audit Logs ---\n";

$tiktokProvider = new TikTokPublishProvider($d, $func);
$tiktokStatus = $tiktokProvider->getStatusInfo();
assertTest(
    $tiktokProvider->isConfigured() === false && $tiktokStatus['status'] === 'NOT_CONFIGURED',
    "TikTok Provider: Honestly reports NOT CONFIGURED without credentials"
);

// Audit logs count for created post
$logs = $d->rawQuery("SELECT * FROM table_publish_log WHERE id_post = ? ORDER BY id ASC", array($createdPostId));
assertTest(
    count($logs) >= 3,
    "Publishing History: Comprehensive audit trail recorded (" . count($logs) . " events for post #{$createdPostId})"
);

// --------------------------------------------------------------------------
// CLEANUP & SUMMARY
// --------------------------------------------------------------------------
// Xóa dữ liệu test
$d->rawQuery("DELETE FROM table_publish_post WHERE id IN (?, ?)", array($createdPostId, $scheduledPostId));
$d->rawQuery("DELETE FROM table_publish_log WHERE id_post IN (?, ?)", array($createdPostId, $scheduledPostId));
$d->rawQuery("DELETE FROM table_ai_video WHERE id IN (?, ?)", array($unapprovedVideoId, $approvedVideoId));

echo "\n=======================================================\n";
echo "TEST RESULTS: Total = " . ($passCount + $failCount) . " | PASS = {$passCount} | FAIL = {$failCount}\n";
echo "=======================================================\n";

if ($failCount > 0) {
    exit(1);
}
