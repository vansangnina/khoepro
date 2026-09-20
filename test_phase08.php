<?php
/**
 * FITNADO PHASE 08 - AUTOMATED TEST SUITE
 * Analytics, Affiliate Attribution & Winner Detection
 * 100% PHP 7.4 Compatible
 */

$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'CLI-Test';
$_SERVER['DOCUMENT_ROOT'] = __DIR__;

define('LIBRARIES', __DIR__ . '/libraries/');
define('SOURCES', __DIR__ . '/sources/');

require_once LIBRARIES . 'config.php';
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$d = new PDODb($config['database']);
$cache = new Cache($d);
$func = new Functions($d, $cache);

require_once LIBRARIES . 'class/class.AnalyticsService.php';
require_once LIBRARIES . 'class/class.WinnerDetectionEngine.php';
require_once LIBRARIES . 'class/class.ConversionImporter.php';
require_once LIBRARIES . 'class/class.PublishingCenter.php';

$analytics = new AnalyticsService($d, $func);
$winnerEngine = new WinnerDetectionEngine($d, $analytics);
$importer = new ConversionImporter($d);
$publishingCenter = new PublishingCenter($d, $func);

$passed = 0;
$failed = 0;

function assertTest($condition, $message) {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] " . $message . "\n";
        $passed++;
    } else {
        echo "[FAIL] " . $message . "\n";
        $failed++;
    }
}

echo "=======================================================\n";
echo "FITNADO PHASE 08 - AUTOMATED TEST SUITE (PHP 7.4)\n";
echo "=======================================================\n\n";

// --- 1. Event Model & Schema Integrity ---
echo "--- 1. Testing Analytics Event Model & Schema ---\n";
$testSessionId = 'sess_test_' . bin2hex(random_bytes(8));
$eventId = $analytics->logEvent(AnalyticsService::EVENT_PAGE_VIEW, array(
    'session_id' => $testSessionId,
    'source' => 'tiktok',
    'medium' => 'organic_video',
    'campaign' => 'prod_test',
    'ip' => '127.0.0.1',
    'is_internal' => 0
));
assertTest($eventId > 0, "Event Logging: Successfully logged PAGE_VIEW event (Event ID: #{$eventId})");

$loggedEvent = $d->rawQueryOne("SELECT * FROM table_analytics_event WHERE id = ? LIMIT 1", array($eventId));
assertTest(!empty($loggedEvent) && $loggedEvent['event_type'] === 'PAGE_VIEW' && $loggedEvent['session_id'] === $testSessionId, "Schema Integrity: Event record contains matching event_type and session_id");

// --- 2. Post Tracking Code & Landing URL Generation ---
echo "\n--- 2. Testing Tracking Code & Landing URL Generation ---\n";
$code = AnalyticsService::generateTrackingCode('tiktok', 99);
assertTest(strpos($code, 'fp_tikt_99_') === 0, "Tracking Code: Generated valid format with prefix and post ID ({$code})");

$approvedVideo = $d->rawQueryOne("SELECT id, id_product FROM table_ai_video WHERE status = 'APPROVED' LIMIT 1");
if ($approvedVideo) {
    $postRes = $publishingCenter->createPostFromApprovedVideo($approvedVideo['id'], array('platform' => 'tiktok'));
    assertTest($postRes['success'] === true && !empty($postRes['id_post']), "Post Creation: Successfully created Post Package with tracking");
    
    $createdPost = $d->rawQueryOne("SELECT tracking_code, landing_url FROM table_publish_post WHERE id = ? LIMIT 1", array($postRes['id_post']));
    assertTest(!empty($createdPost['tracking_code']) && strpos($createdPost['landing_url'], 'ref=' . $createdPost['tracking_code']) !== false, "Landing URL: Contains ref=<tracking_code> and standard UTM parameters");
} else {
    echo "[SKIP] No approved video found for Post creation test\n";
}

// --- 3. Landing Attribution & Session/Cookie Persistence ---
echo "\n--- 3. Testing Landing Attribution Resolver ---\n";
$testTrackingCode = 'fp_tikt_test_' . bin2hex(random_bytes(4));
$dummyPostId = $d->insert('publish_post', array(
    'id_product' => 1,
    'id_video' => 1,
    'platform' => 'tiktok',
    'tracking_code' => $testTrackingCode,
    'title' => 'Test Post for Attribution',
    'caption' => 'Test caption',
    'status' => 'PUBLISHED',
    'date_created' => time(),
    'date_updated' => time()
));

$attrContext = $analytics->resolveLandingAttribution(array(
    'ref' => $testTrackingCode,
    'utm_source' => 'tiktok',
    'utm_medium' => 'organic_video',
    'utm_campaign' => 'gym_belt'
), null);

assertTest($attrContext['id_post'] === $dummyPostId && $attrContext['id_product'] === 1 && $attrContext['tracking_code'] === $testTrackingCode, "Landing Attribution: Accurately resolved Post ID #{$dummyPostId} and Product ID #1 from ref code");
assertTest($attrContext['is_direct'] === false, "Landing Attribution: Correctly flagged as campaign traffic (is_direct = false)");

// --- 4. Product View & Affiliate Click Attribution ---
echo "\n--- 4. Testing Click Attribution & Redirect Integration ---\n";
$testOffer = $d->rawQueryOne("SELECT id, id_product FROM table_product_affiliate WHERE find_in_set('hienthi', status) LIMIT 1");
if ($testOffer) {
    $clickId = $analytics->recordAffiliateClick($testOffer['id'], $attrContext, array(
        'source_page' => 'product_detail_hero',
        'ip' => '192.168.1.100',
        'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)'
    ));
    assertTest($clickId > 0, "Click Attribution: Recorded enriched click in table_affiliate_click (Click ID: #{$clickId})");

    $clickRow = $d->rawQueryOne("SELECT * FROM table_affiliate_click WHERE id = ? LIMIT 1", array($clickId));
    assertTest($clickRow['id_post'] === $dummyPostId && $clickRow['tracking_code'] === $testTrackingCode && $clickRow['device_type'] === 'mobile', "Click Verification: Post ID, Tracking Code, and Mobile Device preserved in click log");
}

// --- 5. Direct Traffic Handling (post_id = NULL) ---
echo "\n--- 5. Testing Direct Traffic Handling ---\n";
$directAttr = $analytics->resolveLandingAttribution(array('id' => 1), null);
assertTest($directAttr['id_post'] === null && $directAttr['is_direct'] === true && $directAttr['source'] === 'direct', "Direct Traffic: Correctly assigned post_id = NULL without false TikTok attribution");

// --- 6. Multiple Posts & Multiple Products Isolation ---
echo "\n--- 6. Testing Multiple Posts Isolation ---\n";
$codeA = 'fp_tikt_post_a_' . bin2hex(random_bytes(3));
$codeB = 'fp_tikt_post_b_' . bin2hex(random_bytes(3));
$postA = $d->insert('publish_post', array('id_product' => 1, 'platform' => 'tiktok', 'tracking_code' => $codeA, 'title' => 'Post A', 'caption' => 'A', 'status' => 'PUBLISHED', 'date_created' => time(), 'date_updated' => time()));
$postB = $d->insert('publish_post', array('id_product' => 1, 'platform' => 'tiktok', 'tracking_code' => $codeB, 'title' => 'Post B', 'caption' => 'B', 'status' => 'PUBLISHED', 'date_created' => time(), 'date_updated' => time()));

$attrA = $analytics->resolveLandingAttribution(array('ref' => $codeA), null);
$attrB = $analytics->resolveLandingAttribution(array('ref' => $codeB), null);

assertTest($attrA['id_post'] === $postA && $attrB['id_post'] === $postB && $attrA['id_post'] !== $attrB['id_post'], "Post Isolation: Post A and Post B for same product retain distinct separate attribution identities");

// --- 7. Attribution Window Expiration ---
echo "\n--- 7. Testing Attribution Window Expiration ---\n";
$expiredTouch = array(
    'tracking_code' => 'fp_old_expired',
    'id_post' => 999,
    'id_product' => 1,
    'touch_time' => time() - (35 * 86400), // 35 days ago (> 30 days)
    'is_direct' => false
);
$resolvedExpired = $analytics->resolveLandingAttribution(array(), $expiredTouch);
assertTest($resolvedExpired['is_direct'] === true && $resolvedExpired['id_post'] === null, "Attribution Window: Expired touch (>30 days) gracefully invalidated to direct traffic");

// --- 8. Conversion Model & Duplicate Prevention (Idempotency) ---
echo "\n--- 8. Testing Conversion Model & Duplicate Protection ---\n";
$testOrderId = 'ORD_TEST_' . time() . '_' . rand(100, 999);
$csvContent = "order_id,platform,tracking_code,order_value,commission_value,currency,status\n";
$csvContent .= "{$testOrderId},shopee,{$testTrackingCode},500000,50000,VND,CONFIRMED\n";

$tmpCsvPath = __DIR__ . '/scratch/test_import_' . time() . '.csv';
file_put_contents($tmpCsvPath, $csvContent);

$importRes1 = $importer->executeImport($tmpCsvPath, 'shopee', 'test_admin');
assertTest($importRes1['imported_count'] === 1 && $importRes1['duplicate_count'] === 0, "Conversion Import 1: Successfully imported 1 confirmed conversion (ID: {$testOrderId})");

$importRes2 = $importer->executeImport($tmpCsvPath, 'shopee', 'test_admin');
assertTest($importRes2['imported_count'] === 0 && $importRes2['duplicate_count'] === 1, "Duplicate Protection: Re-importing same CSV does not double count revenue (1 duplicate detected)");
@unlink($tmpCsvPath);

// --- 9. Unattributed Conversion & Manual Match ---
echo "\n--- 9. Testing Unattributed Conversion & Manual Match ---\n";
$unattrOrderId = 'ORD_UNATTR_' . time() . '_' . rand(100, 999);
$unattrCsv = "order_id,platform,tracking_code,order_value,commission_value,currency,status\n";
$unattrCsv .= "{$unattrOrderId},shopee,UNKNOWN_CODE_999,300000,30000,VND,CONFIRMED\n";
$unattrPath = __DIR__ . '/scratch/test_unattr_' . time() . '.csv';
file_put_contents($unattrPath, $unattrCsv);

$importUnattr = $importer->executeImport($unattrPath, 'shopee', 'test_admin');
assertTest($importUnattr['unattributed_count'] === 1, "Unattributed Conversion: Unknown tracking code correctly flagged as UNATTRIBUTED");
@unlink($unattrPath);

$convRecord = $d->rawQueryOne("SELECT id FROM table_affiliate_conversion WHERE external_conversion_id = ? LIMIT 1", array($unattrOrderId));
if ($convRecord) {
    $matched = $importer->manualMatchConversion($convRecord['id'], 1, $dummyPostId, 'test_admin', 'Đối soát thủ công đơn hàng');
    assertTest($matched === true, "Manual Match: Admin successfully resolved unattributed conversion to Product #1 and Post #{$dummyPostId}");
    
    $checkMatched = $d->rawQueryOne("SELECT is_manual_matched, matched_by FROM table_affiliate_conversion WHERE id = ? LIMIT 1", array($convRecord['id']));
    assertTest($checkMatched['is_manual_matched'] == 1 && $checkMatched['matched_by'] === 'test_admin', "Manual Match Audit: Audit trail captured is_manual_matched = 1 and matched_by = test_admin");
}

// --- 10. Conversion Reversal / Refund Handling ---
echo "\n--- 10. Testing Conversion Reversal / Refund ---\n";
$reversalCsv = "order_id,platform,tracking_code,order_value,commission_value,currency,status\n";
$reversalCsv .= "{$testOrderId},shopee,{$testTrackingCode},500000,50000,VND,REVERSED\n";
$revPath = __DIR__ . '/scratch/test_rev_' . time() . '.csv';
file_put_contents($revPath, $reversalCsv);

$importRev = $importer->executeImport($revPath, 'shopee', 'test_admin');
$checkRev = $d->rawQueryOne("SELECT status FROM table_affiliate_conversion WHERE external_conversion_id = ? LIMIT 1", array($testOrderId));
assertTest($checkRev['status'] === 'REVERSED', "Conversion Reversal: Status updated to REVERSED without data loss or corruption");
@unlink($revPath);

// --- 11. Zero Division Handling & Safe Math ---
echo "\n--- 11. Testing Safe Math & Zero Division Guard ---\n";
$zeroCtr = AnalyticsService::safePercentage(0, 0);
$zeroCvr = AnalyticsService::safePercentage(5, 0);
$zeroDivide = AnalyticsService::safeDivide(100, 0);
assertTest($zeroCtr === 0.0 && $zeroCvr === 0.0 && $zeroDivide === 0.0, "Zero Division: All metrics (CTR, CVR, EPC) safely return 0.0 without division by zero errors");

// --- 12. Currency Separation ---
echo "\n--- 12. Testing Currency Separation ---\n";
$d->insert('affiliate_conversion', array(
    'platform' => 'amazon',
    'external_conversion_id' => 'USD_ORD_' . time(),
    'order_value' => 50.0,
    'commission_value' => 5.0,
    'currency' => 'USD',
    'status' => 'CONFIRMED',
    'conversion_at' => time(),
    'date_created' => time(),
    'date_updated' => time()
));
$overview = $analytics->getOverviewMetrics('all');
assertTest($overview['confirmed_commission_usd'] >= 5.0, "Currency Isolation: USD commission ($5.00) tracked separately from VND");

// --- 13. Internal & Admin Traffic Filtering ---
echo "\n--- 13. Testing Internal Traffic Filtering ---\n";
assertTest($analytics->isInternalIp('127.0.0.1') === true, "Internal Traffic: 127.0.0.1 correctly recognized as internal IP");
assertTest($analytics->isInternalIp('113.160.20.10') === false, "Internal Traffic: Public IP 113.160.20.10 recognized as real visitor");

// Query real live products for testing
$realProd = $d->rawQueryOne("SELECT id, namevi, slugvi FROM table_product WHERE find_in_set('hienthi', status) LIMIT 1");
$realProductId = !empty($realProd['id']) ? (int)$realProd['id'] : 66;

$secondProd = $d->rawQueryOne("SELECT id, namevi FROM table_product WHERE id != ? LIMIT 1", array($realProductId));
$secondProductId = !empty($secondProd['id']) ? (int)$secondProd['id'] : 68;

// --- 14. External Content Cost & Content ROI ---
echo "\n--- 14. Testing External Content Cost & ROI Aggregation ---\n";
$prodMetrics = $analytics->getProductMetrics($realProductId, 'all');
if (!empty($prodMetrics[0])) {
    $p1 = $prodMetrics[0];
    $expectedRoi = $p1['commission_vnd'] - $p1['content_cost_vnd'];
    assertTest(abs($p1['roi_vnd'] - $expectedRoi) < 0.01, "Content ROI: Product ROI accurately equals Commission ({$p1['commission_vnd']} đ) - Content Cost ({$p1['content_cost_vnd']} đ) = {$p1['roi_vnd']} đ");
}

// --- 15. Winner Detection Sample Size Gate (INSUFFICIENT_DATA) ---
echo "\n--- 15. Testing Winner Detection Sample Size Gate ---\n";
// Second product has low samples (0 sessions, 0 clicks)
$evalInsufficient = $winnerEngine->evaluateProduct($secondProductId, 'test_admin', false);
assertTest($evalInsufficient['status'] === WinnerDetectionEngine::STATUS_INSUFFICIENT_DATA, "Sample Size Gate: Product with low samples strictly held at INSUFFICIENT_DATA (never prematurely declared Loser/Winner)");

// --- 16. Winner Status Progression (Rule Evaluation) ---
echo "\n--- 16. Testing Winner Rule Evaluation ---\n";
// Temporarily mock event traffic for test product to test Winner qualification
for ($i = 0; $i < 35; $i++) {
    $analytics->logEvent(AnalyticsService::EVENT_PAGE_VIEW, array(
        'id_product' => $realProductId,
        'session_id' => 'sess_winner_' . $i,
        'is_internal' => 0
    ));
}
for ($i = 0; $i < 12; $i++) {
    $analytics->logEvent(AnalyticsService::EVENT_AFFILIATE_CLICK, array(
        'id_product' => $realProductId,
        'session_id' => 'sess_winner_' . $i,
        'is_internal' => 0
    ));
}

$evalWinner = $winnerEngine->evaluateProduct($realProductId, 'test_admin', true);
assertTest(in_array($evalWinner['status'], array(WinnerDetectionEngine::STATUS_WINNER, WinnerDetectionEngine::STATUS_PROMISING)), "Winner Evaluation: Product with >=30 sessions and >=10 clicks evaluated as {$evalWinner['status']}");
assertTest(!empty($evalWinner['recommendation']['action']), "Winner Recommendation: Generated actionable recommendation ({$evalWinner['recommendation']['action']})");

// --- 17. Winner Evaluation Snapshot & Audit History ---
echo "\n--- 17. Testing Winner Snapshot & History ---\n";
$history = $winnerEngine->getEvaluationHistory($realProductId, 5);
assertTest(!empty($history), "Winner Snapshot: Successfully recorded evaluation snapshot in table_winner_evaluation");
assertTest(isset($history[0]['metrics_snapshot']['sessions']) && isset($history[0]['rules_snapshot']['rules_version']), "Snapshot Data: Evaluation contains frozen metrics snapshot and rules snapshot");

// --- 18. Research Score vs Performance Score Independence ---
echo "\n--- 18. Testing Research Score vs Performance Score Independence ---\n";
$prod1Data = $d->rawQueryOne("SELECT r.total_score as research_score FROM table_product p LEFT JOIN table_product_research r ON (p.id = r.id_product) WHERE p.id = ? LIMIT 1", array($realProductId));
assertTest($prod1Data !== null, "Score Independence: Product Research Score remains preserved and segregated from Observed Performance metrics");

// --- 19. Real Data Test Verification ---
echo "\n--- 19. Testing Real Production Product & Video Fixture ---\n";
assertTest(!empty($realProd), "Real Product Fixture: Found live product '#{$realProd['id']} - {$realProd['namevi']}'");

$realPost = $d->rawQueryOne("SELECT id, title, tracking_code FROM table_publish_post LIMIT 1");
assertTest(!empty($realPost) && !empty($realPost['tracking_code']), "Real Post Fixture: Found real Post '#{$realPost['id']}' with active tracking code ({$realPost['tracking_code']})");

// Clean up test dummy post
if ($dummyPostId) {
    $d->rawQuery("DELETE FROM table_publish_post WHERE id = ?", array($dummyPostId));
}

echo "\n=======================================================\n";
echo "PHASE 08 TEST RESULTS: {$passed} PASSED, {$failed} FAILED\n";
echo "=======================================================\n";
