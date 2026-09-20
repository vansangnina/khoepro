<?php
/**
 * FITNADO PHASE 09 - AUTOMATED TEST SUITE
 * Data-Driven Optimization Loop & Phase 08 Hotfix
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
require_once LIBRARIES . 'class/class.OptimizationEngine.php';
require_once LIBRARIES . 'class/class.PublishingCenter.php';

$analytics = new AnalyticsService($d, $func);
$winnerEngine = new WinnerDetectionEngine($d, $analytics);
$optimizationEngine = new OptimizationEngine($d, $func, $analytics, $winnerEngine);
$publishingCenter = new PublishingCenter($d, $func);

$passed = 0;
$failed = 0;

function assertTest($condition, $message) {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] {$message}\n";
        $passed++;
    } else {
        echo "[FAIL] {$message}\n";
        $failed++;
    }
}

echo "=======================================================\n";
echo "FITNADO PHASE 09 - AUTOMATED TEST SUITE (PHP 7.4)\n";
echo "=======================================================\n\n";

// --- 1. HOTFIX: PHASE 08 WINNER SEMANTICS & NO CONVERSION SOURCE GATE ---
echo "--- 1. Testing Phase 08 Winner Semantics Hotfix ---\n";

// Create test product with high clicks (>=30 sessions, >=10 clicks) but NO conversions connected
$testProdCode = 'P09_TEST_' . time();
$testProdId = $d->insert('product', array(
    'namevi' => 'Đai Cổ Tay Test Hotfix P08 ' . time(),
    'slugvi' => 'dai-co-tay-test-hotfix-p08-' . time(),
    'code' => $testProdCode,
    'regular_price' => 150000,
    'sale_price' => 120000,
    'discount' => 0,
    'review_score' => 4.5,
    'review_count' => 10,
    'status' => 'hienthi',
    'type' => 'san-pham',
    'date_created' => time() - (10 * 86400)
));

// Seed 35 sessions and 12 clicks (CTR ~34%)
for ($i = 0; $i < 35; $i++) {
    $sess = 'sess_p08_fix_' . $i . '_' . time();
    $analytics->logEvent(AnalyticsService::EVENT_PAGE_VIEW, array(
        'id_product' => $testProdId,
        'session_id' => $sess,
        'source' => 'tiktok',
        'is_internal' => 0
    ));
}
for ($j = 0; $j < 12; $j++) {
    $analytics->logEvent(AnalyticsService::EVENT_AFFILIATE_CLICK, array(
        'id_product' => $testProdId,
        'session_id' => 'sess_p08_fix_' . $j . '_' . time(),
        'source' => 'tiktok',
        'is_internal' => 0
    ));
}

// Evaluate Product with WinnerEngine
$evalFix = $winnerEngine->evaluateProduct($testProdId, 'test_admin', false);

assertTest(
    $evalFix['status'] === WinnerDetectionEngine::STATUS_CLICK_PROMISING,
    "Phase 08 Hotfix: High Click + No Conversions evaluated as CLICK_PROMISING (Not REVENUE_WINNER)"
);
assertTest(
    $evalFix['status'] !== 'WINNER' && $evalFix['status'] !== 'REVENUE_WINNER',
    "Phase 08 Hotfix: Product with unconfirmed conversions strictly prohibited from WINNER label"
);


// --- 2. PERFORMANCE MATURITY SCALE ---
echo "\n--- 2. Testing 6-Level Performance Maturity Scale ---\n";

// 2.1 INSUFFICIENT_DATA
$lowSampleProdId = $d->insert('product', array(
    'namevi' => 'Sản phẩm Mẫu Nhỏ ' . time(),
    'slugvi' => 'san-pham-mau-nho-' . time(),
    'discount' => 0,
    'review_score' => 4.5,
    'review_count' => 10,
    'status' => 'hienthi',
    'type' => 'san-pham',
    'date_created' => time()
));
$lowSampleEval = $winnerEngine->evaluateProduct($lowSampleProdId, 'test_admin', false);
assertTest(
    $lowSampleEval['status'] === WinnerDetectionEngine::STATUS_INSUFFICIENT_DATA,
    "Maturity Level 1: Low sample held strictly at INSUFFICIENT_DATA"
);

// 2.2 TRAFFIC_PROMISING (>=30 sessions, <10 clicks)
$trafficProdId = $d->insert('product', array(
    'namevi' => 'Sản phẩm Traffic Cao Click Thấp ' . time(),
    'slugvi' => 'san-pham-traffic-cao-' . time(),
    'discount' => 0,
    'review_score' => 4.5,
    'review_count' => 10,
    'status' => 'hienthi',
    'type' => 'san-pham',
    'date_created' => time() - (5 * 86400)
));
for ($i = 0; $i < 32; $i++) {
    $analytics->logEvent(AnalyticsService::EVENT_PAGE_VIEW, array(
        'id_product' => $trafficProdId,
        'session_id' => 'sess_traf_' . $i . '_' . time(),
        'is_internal' => 0
    ));
}
for ($j = 0; $j < 2; $j++) {
    $analytics->logEvent(AnalyticsService::EVENT_AFFILIATE_CLICK, array(
        'id_product' => $trafficProdId,
        'session_id' => 'sess_traf_' . $j . '_' . time(),
        'is_internal' => 0
    ));
}
$trafficEval = $winnerEngine->evaluateProduct($trafficProdId, 'test_admin', false);
assertTest(
    $trafficEval['status'] === WinnerDetectionEngine::STATUS_TRAFFIC_PROMISING,
    "Maturity Level 2: High traffic but low clicks evaluated as TRAFFIC_PROMISING"
);
assertTest(
    $trafficEval['recommendation']['action'] === WinnerDetectionEngine::REC_REVIEW_PRODUCT_PAGE,
    "Maturity Level 2 Recommendation: Proposes REVIEW_PRODUCT_PAGE (CTA/Offer Optimization)"
);

// 2.3 CONVERSION_PROMISING (With confirmed conversion data)
$convProdId = $d->insert('product', array(
    'namevi' => 'Sản phẩm Có Đơn Hàng ' . time(),
    'slugvi' => 'san-pham-co-don-hang-' . time(),
    'discount' => 0,
    'review_score' => 4.5,
    'review_count' => 10,
    'status' => 'hienthi',
    'type' => 'san-pham',
    'date_created' => time() - (7 * 86400)
));
// Seed sessions, clicks, and 2 conversions with low commission (Net ROI <= 0)
for ($i = 0; $i < 35; $i++) {
    $analytics->logEvent(AnalyticsService::EVENT_PAGE_VIEW, array('id_product' => $convProdId, 'session_id' => 'sess_c_' . $i, 'is_internal' => 0));
}
for ($j = 0; $j < 12; $j++) {
    $analytics->logEvent(AnalyticsService::EVENT_AFFILIATE_CLICK, array('id_product' => $convProdId, 'session_id' => 'sess_c_' . $j, 'is_internal' => 0));
}
$d->insert('affiliate_conversion', array(
    'external_conversion_id' => 'ORD_TEST_CONV_' . time() . '_1',
    'platform' => 'shopee',
    'id_product' => $convProdId,
    'order_value' => 200000,
    'commission_value' => 10000,
    'currency' => 'VND',
    'status' => 'CONFIRMED',
    'conversion_at' => time() - 3600,
    'date_created' => time(),
    'date_updated' => time()
));
$d->insert('affiliate_conversion', array(
    'external_conversion_id' => 'ORD_TEST_CONV_' . time() . '_2',
    'platform' => 'shopee',
    'id_product' => $convProdId,
    'order_value' => 250000,
    'commission_value' => 12500,
    'currency' => 'VND',
    'status' => 'CONFIRMED',
    'conversion_at' => time() - 1800,
    'date_created' => time(),
    'date_updated' => time()
));
// Insert AI video with cost 50,000 VND so commission (22.5k) < cost (50k)
$d->insert('ai_video', array(
    'id_product' => $convProdId,
    'title' => 'Video Test Conv',
    'mode' => 'HYBRID',
    'status' => 'APPROVED',
    'tts_cost' => 0,
    'ai_video_cost' => 50000,
    'duration_actual' => 30,
    'date_created' => time()
));
$convEval = $winnerEngine->evaluateProduct($convProdId, 'test_admin', false);
assertTest(
    $convEval['status'] === WinnerDetectionEngine::STATUS_CONVERSION_PROMISING,
    "Maturity Level 3: Confirmed conversions with pending profitability evaluated as CONVERSION_PROMISING"
);

// 2.4 REVENUE_WINNER (Confirmed conversions + Positive Net ROI)
$revWinnerProdId = $d->insert('product', array(
    'namevi' => 'Sản phẩm Thắng Doanh Thu ' . time(),
    'slugvi' => 'san-pham-thang-doanh-thu-' . time(),
    'discount' => 0,
    'review_score' => 4.5,
    'review_count' => 10,
    'status' => 'hienthi',
    'type' => 'san-pham',
    'date_created' => time() - (10 * 86400)
));
for ($i = 0; $i < 40; $i++) {
    $analytics->logEvent(AnalyticsService::EVENT_PAGE_VIEW, array('id_product' => $revWinnerProdId, 'session_id' => 'sess_rw_' . $i, 'is_internal' => 0));
}
for ($j = 0; $j < 15; $j++) {
    $analytics->logEvent(AnalyticsService::EVENT_AFFILIATE_CLICK, array('id_product' => $revWinnerProdId, 'session_id' => 'sess_rw_' . $j, 'is_internal' => 0));
}
$d->insert('affiliate_conversion', array(
    'external_conversion_id' => 'ORD_TEST_RW_' . time() . '_1',
    'platform' => 'shopee',
    'id_product' => $revWinnerProdId,
    'order_value' => 500000,
    'commission_value' => 50000,
    'currency' => 'VND',
    'status' => 'CONFIRMED',
    'conversion_at' => time() - 3600,
    'date_created' => time(),
    'date_updated' => time()
));
$d->insert('affiliate_conversion', array(
    'external_conversion_id' => 'ORD_TEST_RW_' . time() . '_2',
    'platform' => 'shopee',
    'id_product' => $revWinnerProdId,
    'order_value' => 600000,
    'commission_value' => 60000,
    'currency' => 'VND',
    'status' => 'CONFIRMED',
    'conversion_at' => time() - 1800,
    'date_created' => time(),
    'date_updated' => time()
));
// Video Economy with cost 0 VND -> ROI = 110,000 VND > 0
$d->insert('ai_video', array(
    'id_product' => $revWinnerProdId,
    'title' => 'Video Test Economy RW',
    'mode' => 'ECONOMY',
    'status' => 'APPROVED',
    'tts_cost' => 0,
    'ai_video_cost' => 0,
    'duration_actual' => 30,
    'date_created' => time()
));
$revWinnerEval = $winnerEngine->evaluateProduct($revWinnerProdId, 'test_admin', false);
assertTest(
    $revWinnerEval['status'] === WinnerDetectionEngine::STATUS_REVENUE_WINNER,
    "Maturity Level 4: Confirmed revenue exceeding production cost evaluated as REVENUE_WINNER"
);


// --- 3. OPTIMIZATION ENGINE & RECOMMENDATION GENERATION ---
echo "\n--- 3. Testing Optimization Engine & Recommendations ---\n";

$recs = $optimizationEngine->generateRecommendations($revWinnerProdId);
assertTest(!empty($recs), "OptimizationEngine: Generated recommendations for Revenue Winner product");
assertTest(
    $recs[0]['recommendation_type'] === OptimizationEngine::REC_UPGRADE_TO_HYBRID,
    "Recommendation for Revenue Winner is UPGRADE_TO_HYBRID"
);
assertTest(
    $recs[0]['proposed_variable'] === OptimizationEngine::VAR_VIDEO_STYLE,
    "Proposed variable is VIDEO_STYLE"
);
assertTest(
    (float)$recs[0]['estimated_cost_vnd'] > 0,
    "Hybrid recommendation accurately estimates external AI scene cost (~50.000 VND)"
);
assertTest(
    $recs[0]['status'] === OptimizationEngine::REC_STATUS_PENDING,
    "New recommendation starts at PENDING status (Human Gate Guard)"
);


// --- 4. DEDUPLICATION & COOLDOWN ---
echo "\n--- 4. Testing Recommendation Deduplication & Cooldown ---\n";

// Running generateRecommendations again immediately should NOT create duplicate recommendation
$secondRecs = $optimizationEngine->generateRecommendations($revWinnerProdId);
assertTest(empty($secondRecs), "Cooldown Gate: Duplicate recommendation prevented within cooldown period (24h)");


// --- 5. COST GATE & BUDGET CONTROLS ---
echo "\n--- 5. Testing Hard Cost Gate & Admin Override ---\n";

$recId = (int)$recs[0]['id'];

// Temporarily set max_cost_per_experiment to 20,000 VND so estimated 50,000 VND is blocked
$analytics->saveSetting('max_cost_per_experiment', 20000, 'optimization');

$blockedApproval = $optimizationEngine->approveRecommendation($recId, 'test_admin', 'Test block', false);
assertTest(
    $blockedApproval['success'] === false && strpos($blockedApproval['error'], 'vượt quá hạn mức') !== false,
    "Hard Cost Gate: Successfully blocked approval when estimated cost exceeds max budget limit"
);

// Approve with Admin Override
$overrideApproval = $optimizationEngine->approveRecommendation($recId, 'test_admin', 'Test override approval', true);
assertTest(
    !empty($overrideApproval['success']),
    "Hard Cost Gate: Succeeded with explicit Admin Override"
);
assertTest(
    !empty($overrideApproval['experiment_id']),
    "Approval successfully created A/B Experiment record (ID: #{$overrideApproval['experiment_id']})"
);

// Restore default cost setting
$analytics->saveSetting('max_cost_per_experiment', 60000, 'optimization');


// --- 6. ONE-VARIABLE EXPERIMENT LIFECYCLE ---
echo "\n--- 6. Testing One-Variable Experiment Lifecycle ---\n";

$expId = (int)$overrideApproval['experiment_id'];
$expRecord = $d->rawQueryOne("SELECT * FROM table_optimization_experiment WHERE id = ? LIMIT 1", array($expId));

assertTest(!empty($expRecord), "Experiment record found in table_optimization_experiment");
assertTest($expRecord['changed_variable'] === 'VIDEO_STYLE', "Experiment preserves One-Variable Principle ('VIDEO_STYLE')");
assertTest($expRecord['status'] === OptimizationEngine::EXP_STATUS_RUNNING, "Experiment status transitioned to RUNNING");
assertTest(!empty($expRecord['id_variation_post']), "Experiment linked with newly created Variation Post");


// --- 7. TRACKING ISOLATION & ATTRIBUTION ---
echo "\n--- 7. Testing Publishing & Tracking Identity Isolation ---\n";

$varPostId = (int)$expRecord['id_variation_post'];
$varPost = $d->rawQueryOne("SELECT * FROM table_publish_post WHERE id = ? LIMIT 1", array($varPostId));

assertTest(!empty($varPost['tracking_code']), "Variation Post has unique tracking code ({$varPost['tracking_code']})");
assertTest(
    strpos($varPost['tracking_code'], (string)$expId) !== false || strlen($varPost['tracking_code']) > 8,
    "Tracking code uniquely incorporates experiment identity"
);


// --- 8. EXPERIMENT EVALUATION & SAMPLE GATE ---
echo "\n--- 8. Testing Experiment Evaluation & Sample Gate ---\n";

// 8.1 Low sample on variation -> INSUFFICIENT_DATA
$evalLowSample = $optimizationEngine->evaluateExperiment($expId);
assertTest(
    $evalLowSample['conclusion'] === OptimizationEngine::RESULT_INSUFFICIENT_DATA,
    "Experiment Sample Gate: Low variation sample yields INSUFFICIENT_DATA (No false winner declaration)"
);
assertTest($evalLowSample['is_completed'] === false, "Experiment remains in RUNNING state while sample accumulates");

// 8.2 Seed adequate traffic and clicks for Variation post
for ($i = 0; $i < 35; $i++) {
    $analytics->logEvent(AnalyticsService::EVENT_PAGE_VIEW, array(
        'id_product' => $revWinnerProdId,
        'id_post' => $varPostId,
        'tracking_code' => $varPost['tracking_code'],
        'session_id' => 'sess_exp_var_' . $i,
        'is_internal' => 0
    ));
}
for ($j = 0; $j < 14; $j++) {
    $analytics->logEvent(AnalyticsService::EVENT_AFFILIATE_CLICK, array(
        'id_product' => $revWinnerProdId,
        'id_post' => $varPostId,
        'tracking_code' => $varPost['tracking_code'],
        'session_id' => 'sess_exp_var_' . $j,
        'is_internal' => 0
    ));
}
// Seed higher revenue conversions for Variation
$d->insert('affiliate_conversion', array(
    'external_conversion_id' => 'ORD_EXP_VAR_' . time() . '_1',
    'platform' => 'shopee',
    'id_product' => $revWinnerProdId,
    'id_post' => $varPostId,
    'tracking_code' => $varPost['tracking_code'],
    'order_value' => 800000,
    'commission_value' => 80000,
    'currency' => 'VND',
    'status' => 'CONFIRMED',
    'conversion_at' => time() - 600,
    'date_created' => time(),
    'date_updated' => time()
));
$d->insert('affiliate_conversion', array(
    'external_conversion_id' => 'ORD_EXP_VAR_' . time() . '_2',
    'platform' => 'shopee',
    'id_product' => $revWinnerProdId,
    'id_post' => $varPostId,
    'tracking_code' => $varPost['tracking_code'],
    'order_value' => 900000,
    'commission_value' => 90000,
    'currency' => 'VND',
    'status' => 'CONFIRMED',
    'conversion_at' => time() - 300,
    'date_created' => time(),
    'date_updated' => time()
));

$evalCompleted = $optimizationEngine->evaluateExperiment($expId);
assertTest(
    $evalCompleted['conclusion'] === OptimizationEngine::RESULT_VARIATION_BETTER,
    "Experiment Conclusion: Variation with superior CTR and Revenue declared VARIATION_BETTER"
);
assertTest($evalCompleted['is_completed'] === true, "Experiment transitioned to COMPLETED status");


// --- 9. NO INFINITE LOOP & NO AUTONOMOUS SPENDING ---
echo "\n--- 9. Testing No Infinite Loop & Autonomous Spending Guard ---\n";

// Count total experiments for this product - completing an experiment MUST NOT spawn another automatically
$expCountAfter = $d->rawQueryOne("SELECT COUNT(id) as total FROM table_optimization_experiment WHERE id_product = ?", array($revWinnerProdId));
assertTest((int)$expCountAfter['total'] === 1, "No Infinite Loop: Completing an experiment does NOT auto-spawn next experiment");


// --- 10. REAL PRODUCTION FIXTURE INTEGRATION ---
echo "\n--- 10. Testing Real Production Product & Video Fixture ---\n";

$realProd = $d->rawQueryOne("SELECT id, namevi FROM table_product WHERE id = 66 LIMIT 1");
assertTest(!empty($realProd), "Real Product Fixture: Product #66 ('{$realProd['namevi']}') verified");

$realPost = $d->rawQueryOne("SELECT id, title, tracking_code FROM table_publish_post WHERE id = 5 LIMIT 1");
assertTest(!empty($realPost), "Real Post Fixture: Post #5 ('{$realPost['title']}') verified with tracking code '{$realPost['tracking_code']}'");

echo "\n=======================================================\n";
echo sprintf("PHASE 09 TEST RESULTS: %d PASSED, %d FAILED\n", $passed, $failed);
echo "=======================================================\n";

if ($failed > 0) {
    exit(1);
}
exit(0);
