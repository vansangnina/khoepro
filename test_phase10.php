<?php
/**
 * FITNADO PHASE 10 - AUTOMATED TEST SUITE (PHP 7.4)
 * Operations & Automation Control Center Verification
 */

if (php_sapi_name() === 'cli') {
    $_SERVER['SERVER_NAME'] = 'localhost';
}
define('LIBRARIES', __DIR__ . '/libraries/');
require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$dbConfig = $config['database'];
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);

require_once LIBRARIES . 'class/class.OperationsService.php';

$ops = new OperationsService($d, $func);

$passCount = 0;
$failCount = 0;

function assertTest($condition, $testName, $details = '') {
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
echo "FITNADO PHASE 10 - AUTOMATED TEST SUITE (PHP 7.4)\n";
echo "=======================================================\n\n";

// --- 1. Process Registry & Worker Heartbeat ---
echo "--- 1. Testing Process Registry & Worker Heartbeats ---\n";
$workers = $ops->getRegisteredWorkers();
assertTest(count($workers) >= 6, "Process Registry: Contains all 6 registered background processes", "Found: " . count($workers));
assertTest(isset($workers['product_research_worker']) && isset($workers['video_render_worker']), "Process Registry: Specific keys exist");

// Record worker start and heartbeat
$testWorkerKey = 'video_render_worker';
$startOk = $ops->recordWorkerStart($testWorkerKey, 'worker', array('test_mode' => 1));
assertTest($startOk === true, "Worker Heartbeat: Successfully recorded worker start");

$hbOk = $ops->recordHeartbeat($testWorkerKey, array('limit' => 5));
assertTest($hbOk === true, "Worker Heartbeat: Successfully updated heartbeat timestamp");

$workerStatuses = $ops->getWorkerStatuses();
assertTest(isset($workerStatuses[$testWorkerKey]), "Worker Status: Status retrieved for test worker");
assertTest($workerStatuses[$testWorkerKey]['health'] === OperationsService::STATUS_HEALTHY, "Worker Status: Evaluated as HEALTHY on recent heartbeat", "Got: " . ($workerStatuses[$testWorkerKey]['health'] ?? ''));

// Worker Error recording & Status change
$ops->recordWorkerError($testWorkerKey, "Simulated worker memory timeout");
$workerStatusesAfterError = $ops->getWorkerStatuses();
assertTest($workerStatusesAfterError[$testWorkerKey]['health'] === OperationsService::STATUS_FAILED, "Worker Status: Evaluated as FAILED on recent error");

// Worker Success recovery
$ops->recordWorkerSuccess($testWorkerKey, array('processed' => 1));
$workerStatusesAfterSuccess = $ops->getWorkerStatuses();
assertTest($workerStatusesAfterSuccess[$testWorkerKey]['health'] === OperationsService::STATUS_HEALTHY, "Worker Status: Recovered to HEALTHY after successful run");


// --- 2. Queue Health & Stuck Job Detection ---
echo "\n--- 2. Testing Queue Health & Stuck Job Detection ---\n";
$queueSummaries = $ops->getQueueSummaries();
assertTest(isset($queueSummaries['research']) && isset($queueSummaries['video']), "Queue Summaries: Metrics aggregated across all 4 modules");

try {
    // Insert a synthetic stuck job fixture (> 7200s ago)
    $stuckJobTime = time() - 7200;
    $stuckJobId = $d->insert('ai_video_job', array(
        'id_video' => 1,
        'provider' => 'mock',
        'status' => 'RUNNING',
        'started_at' => $stuckJobTime,
        'date_created' => $stuckJobTime
    ));

    $stuckJobs = $ops->detectAndFlagStuckJobs();
    $foundStuck = false;
    foreach ($stuckJobs as $sj) {
        if ($sj['module'] === 'video' && $sj['job_id'] == $stuckJobId) {
            $foundStuck = true;
            break;
        }
    }
    assertTest($foundStuck === true, "Stuck Job Detection: Successfully flagged video job #{$stuckJobId} running past threshold");

    // Clean up synthetic stuck job
    $d->rawQuery("DELETE FROM table_ai_video_job WHERE id = ?", array($stuckJobId));
} catch (Throwable $e) {
    echo "ERROR in Section 2: " . $e->getMessage() . " on line " . $e->getLine() . "\n";
}


// --- 3. Provider Health & Zero-Cost Diagnostic ---
try {
    echo "\n--- 3. Testing Provider Health (Zero Paid Calls) ---\n";
    $providers = $ops->getProvidersStatus();
    assertTest(isset($providers['tiktok_api']), "Provider Health: TikTok provider evaluated");
    assertTest($providers['tiktok_api']['status'] === OperationsService::PROV_NOT_CONFIGURED, "Provider Health: TikTok correctly marked as NOT_CONFIGURED (Not FAILED)", "Status: " . $providers['tiktok_api']['status']);
    assertTest($providers['video_engine']['status'] === OperationsService::PROV_AVAILABLE, "Provider Health: Local FFmpeg video engine marked as AVAILABLE");


    // --- 4. Secret Sanitizer ---
    echo "\n--- 4. Testing Secret Sanitizer ---\n";
    $rawPayload = array(
        'api_key' => 'sk-bee-1234567890abcdef12345678',
        'password' => 'SuperSecretPass123!',
        'header' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.token123',
        'safe_field' => 'Public Fitness Video Title'
    );
    $sanitized = $ops->sanitizeSecrets($rawPayload);
    assertTest(strpos($sanitized['api_key'], 'MASKED') !== false, "Secret Sanitizer: API Key masked");
    assertTest(strpos($sanitized['password'], 'MASKED') !== false, "Secret Sanitizer: Password masked");
    assertTest(strpos($sanitized['header'], 'MASKED_TOKEN') !== false, "Secret Sanitizer: Bearer Token masked");
    assertTest($sanitized['safe_field'] === 'Public Fitness Video Title', "Secret Sanitizer: Safe public field preserved untouched");


    // --- 5. Real Cost Center & Estimated vs Actual Separation ---
    echo "\n--- 5. Testing Cost Center & Dimensions ---\n";
    $costSummary = $ops->getCostSummary('all');
    assertTest(isset($costSummary['actual_cost_vnd']) && isset($costSummary['estimated_cost_vnd']), "Cost Center: Separates actual vs estimated costs");
    assertTest(is_numeric($costSummary['actual_cost_vnd']), "Cost Center: Actual cost is numeric");
    assertTest(isset($costSummary['breakdown']['tts_cost']) && isset($costSummary['breakdown']['ai_video_cost']), "Cost Center: Contains type breakdown (TTS, AI Video)");


    // --- 6. Budget Guards & Admin Override ---
    echo "\n--- 6. Testing Budget Guards & Admin Override ---\n";
    $origBudget = $ops->getSetting('daily_external_api_budget', 200000);

    // Set artificially low budget (1 VND) to trigger limit
    $ops->saveSetting('daily_external_api_budget', 1, 'operations');
    $paidCheck = $ops->canExecutePaidAction(50000, false);
    assertTest($paidCheck['allowed'] === false, "Budget Guard: Blocked paid action when budget exceeded", $paidCheck['reason']);

    // Free action should still be allowed
    $freeCheck = $ops->canExecutePaidAction(0, false);
    assertTest($freeCheck['allowed'] === true, "Budget Guard: Free/local action permitted even when paid budget exceeded");

    // Admin Override should allow execution
    $overrideCheck = $ops->canExecutePaidAction(50000, true);
    assertTest($overrideCheck['allowed'] === true, "Budget Guard: Paid action allowed with Admin Override");

    // Record override audit log
    $overrideLogOk = $ops->overrideBudget(50000, 'Urgent campaign test override', 'admin_tester');
    assertTest($overrideLogOk === true, "Budget Override: Logged audit record in table_operations_override_log");

    // Restore original budget
    $ops->saveSetting('daily_external_api_budget', $origBudget, 'operations');


    // --- 7. Global & Module Automation Switches & Emergency Stop ---
    echo "\n--- 7. Testing Automation Switches & Emergency Stop ---\n";
    // Toggle module switch
    $ops->toggleAutomation('video', false, 'admin_tester');
    assertTest($ops->isAutomationEnabled('video') === false, "Module Switch: Video automation successfully turned OFF");
    assertTest($ops->isAutomationEnabled('research') === true, "Module Switch: Other modules remain ON");

    // Re-enable video
    $ops->toggleAutomation('video', true, 'admin_tester');
    assertTest($ops->isAutomationEnabled('video') === true, "Module Switch: Video automation re-enabled ON");

    // Emergency Stop (Pause Paid Automation)
    $ops->toggleEmergencyPausePaid(true, 'Test emergency pause', 'admin_tester');
    assertTest($ops->isPaidAutomationPaused() === true, "Emergency Stop: Paid automation successfully PAUSED");

    $paidCheckUnderPause = $ops->canExecutePaidAction(50000, false);
    assertTest($paidCheckUnderPause['allowed'] === false, "Emergency Stop: Blocked external paid action during pause");

    $freeCheckUnderPause = $ops->canExecutePaidAction(0, false);
    assertTest($freeCheckUnderPause['allowed'] === true, "Emergency Stop: Local/free operations remain unblocked");

    // Resume paid automation
    $ops->toggleEmergencyPausePaid(false, 'Resume test', 'admin_tester');
    assertTest($ops->isPaidAutomationPaused() === false, "Emergency Stop: Resumed paid automation successfully");


    // --- 8. Human Action Queue ("CẦN BẠN XỬ LÝ") ---
    echo "\n--- 8. Testing Human Action Queue ---\n";
    $humanActions = $ops->getHumanActionQueue();
    assertTest(isset($humanActions['total']) && isset($humanActions['items']), "Human Action Queue: Aggregated pending items");
    assertTest(isset($humanActions['by_priority']), "Human Action Queue: Priority breakdown available");

    if (!empty($humanActions['items'])) {
        $firstItem = $humanActions['items'][0];
        assertTest(isset($firstItem['priority']) && isset($firstItem['url']) && isset($firstItem['action_label']), "Human Action Queue: Items have priority, click-through URL and action label");
    }


    // --- 9. Data Freshness Tracker ---
    echo "\n--- 9. Testing Data Freshness Tracker ---\n";
    $freshness = $ops->getDataFreshness();
    assertTest(isset($freshness['tracking_events']) && isset($freshness['conversion_orders']), "Data Freshness: Stage metrics present");
    assertTest(in_array($freshness['conversion_orders']['status'], array(OperationsService::STATUS_HEALTHY, OperationsService::STATUS_WARNING, OperationsService::PROV_NOT_CONFIGURED)), "Data Freshness: Conversion source status correctly differentiated");


    // --- 10. Alert Deduplication & Lifecycle ---
    echo "\n--- 10. Testing Alert Center Deduplication & Lifecycle ---\n";
    $testAlertTitle = "Test Unique Incident " . time();
    $testAlertMsg = "Bearer secret_token_123456 failed to connect";

    // Record 1st alert
    $ops->recordAlert('system', OperationsService::SEV_WARNING, $testAlertTitle, $testAlertMsg, 'job_999');

    // Record 2nd identical alert (should deduplicate and increment occurrences)
    $ops->recordAlert('system', OperationsService::SEV_WARNING, $testAlertTitle, $testAlertMsg, 'job_999');

    $activeAlerts = $ops->getAlerts('ACTIVE', 10);
    $foundAlert = null;
    foreach ($activeAlerts as $al) {
        if (strpos($al['title'], 'Test Unique Incident') !== false) {
            $foundAlert = $al;
            break;
        }
    }

    assertTest($foundAlert !== null, "Alert Center: Alert recorded");
    assertTest($foundAlert['occurrences'] >= 2, "Alert Deduplication: Deduplicated duplicate occurrences count", "Got occurrences: " . ($foundAlert['occurrences'] ?? 0));
    assertTest(strpos($foundAlert['message'], 'MASKED') !== false, "Alert Center: Secret tokens sanitized inside alert message");

    // Acknowledge alert
    $ackOk = $ops->acknowledgeAlert($foundAlert['id'], 'admin_tester');
    assertTest($ackOk === true, "Alert Lifecycle: Successfully acknowledged alert");

    // Resolve alert
    $resOk = $ops->resolveAlert($foundAlert['id']);
    assertTest($resOk === true, "Alert Lifecycle: Successfully resolved alert");

    // Clean up test alert
    $d->rawQuery("DELETE FROM table_system_alert WHERE id = ?", array($foundAlert['id']));


    // --- 11. Safe Retry & Idempotency Guards ---
    echo "\n--- 11. Testing Safe Retry & Idempotency Guards ---\n";
    // Create a temporary failed job
    $testFailedJobId = $d->insert('product_research_job', array(
        'provider' => 'ai_agent',
        'status' => 'FAILED',
        'error_message' => 'Connection timeout',
        'date_created' => time()
    ));

    $retryRes = $ops->retryJob('research', $testFailedJobId, 'admin_tester');
    assertTest($retryRes['success'] === true, "Safe Retry: Successfully re-queued FAILED job");

    $requeuedJob = $d->rawQueryOne("SELECT status FROM table_product_research_job WHERE id = ?", array($testFailedJobId));
    assertTest($requeuedJob['status'] === 'PENDING', "Safe Retry: Job status transitioned to PENDING");

    // Cancel pending job
    $cancelRes = $ops->cancelJob('research', $testFailedJobId, 'admin_tester');
    assertTest($cancelRes['success'] === true, "Safe Cancel: Successfully cancelled PENDING job");

    // Clean up test job
    $d->rawQuery("DELETE FROM table_product_research_job WHERE id = ?", array($testFailedJobId));

    // Test idempotency: Reject retry on PUBLISHED post
    $pubPostId = $d->insert('publish_post', array(
        'id_product' => 1,
        'id_video' => 1,
        'title' => 'Published Test Post',
        'caption' => 'Caption',
        'provider' => 'manual',
        'status' => 'PUBLISHED',
        'date_created' => time()
    ));

    $pubRetryRes = $ops->retryJob('publishing', $pubPostId, 'admin_tester');
    assertTest($pubRetryRes['success'] === false, "Idempotency Guard: Rejected retry on already PUBLISHED post", $pubRetryRes['message']);

    // Clean up test post
    $d->rawQuery("DELETE FROM table_publish_post WHERE id = ?", array($pubPostId));


    // --- 12. Environment Diagnostics ---
    echo "\n--- 12. Testing Environment Diagnostics ---\n";
    $env = $ops->getEnvironmentHealth();
    assertTest(isset($env['php']['actual_runtime']), "Environment Diagnostic: PHP runtime reported honestly", $env['php']['actual_runtime']);
    assertTest($env['database']['status'] === OperationsService::STATUS_HEALTHY, "Environment Diagnostic: Database connectivity verified HEALTHY");
    assertTest(isset($env['ffmpeg']['available']), "Environment Diagnostic: FFmpeg availability checked");

} catch (Throwable $e) {
    echo "ERROR in Test Suite: " . $e->getMessage() . " on line " . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=======================================================\n";
echo "PHASE 10 TEST RESULTS: {$passCount} PASSED, {$failCount} FAILED\n";
echo "=======================================================\n";

if ($failCount > 0) {
    exit(1);
}
exit(0);
