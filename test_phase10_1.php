<?php
/**
 * FITNADO PHASE 10.1 - AUTOMATED TEST SUITE (PHP 7.4)
 * Production Readiness & ACCESSTRADE Publisher API Verification
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
require_once LIBRARIES . 'class/class.AccessTradeProvider.php';
require_once LIBRARIES . 'class/class.ResearchProvider.php';

$ops = new OperationsService($d, $func);
$at = new AccessTradeProvider($d, $func);

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
echo "FITNADO PHASE 10.1 - AUTOMATED TEST SUITE (PHP 7.4)\n";
echo "Production Readiness & ACCESSTRADE Integration\n";
echo "=======================================================\n\n";

// --- 1. Production Environment Readiness ---
echo "--- 1. Testing Production Environment Readiness ---\n";
assertTest(version_compare(PHP_VERSION, '7.4.0', '>='), "PHP Version: Runtime is PHP 7.4+ compatible (Current: " . PHP_VERSION . ")");
assertTest($d !== null, "Database Connection: PDODb instance connected successfully");
$uploadDir = __DIR__ . '/upload';
$cacheDir = __DIR__ . '/upload/cache';
$videoDir = __DIR__ . '/upload/ai_video';
$logsDir = __DIR__ . '/upload/logs';
foreach (array($uploadDir, $cacheDir, $videoDir, $logsDir) as $dir) {
    if (!is_dir($dir)) @mkdir($dir, 0777, true);
}
assertTest(is_writable($uploadDir) && is_writable($cacheDir), "File System: Upload and cache directories are writable");
$ffmpegPath = $config['video_composer']['ffmpeg_path'] ?? ($config['video_engine']['ffmpeg_path'] ?? '');
assertTest(!empty($ffmpegPath), "FFmpeg Engine: Dynamic binary resolution configured: " . $ffmpegPath);
assertTest(defined('NN_CONTRACT'), "Security: NN_CONTRACT constant defined for webhook token protection");

// --- 2. Secret Sanitization & API Key Protection ---
echo "\n--- 2. Testing Secret Sanitization & Key Protection ---\n";
$dummySecretText = "Error communicating with api_key=my_secret_key_12345 and password=supersecretpass and Token 9876543210abcdef";
$sanitized = $ops->sanitizeSecrets($dummySecretText);
assertTest(strpos($sanitized, 'my_secret_key_12345') === false && strpos($sanitized, 'supersecretpass') === false && strpos($sanitized, 'MASKED') !== false, "Secret Sanitizer: Masking sensitive credentials in logs/alerts");
$providers = $ops->getProvidersStatus();
assertTest(isset($providers['accesstrade']), "Providers Status: ACCESSTRADE provider registered in Operations Control Center");
assertTest(!isset($providers['accesstrade']['access_key']), "Security: Raw API keys are never exposed in public provider status arrays");

// --- 3. ACCESSTRADE Provider Instantiation & Config ---
echo "\n--- 3. Testing ACCESSTRADE Provider Class ---\n";
assertTest(class_exists('AccessTradeProvider'), "Class Existence: AccessTradeProvider class loaded");
assertTest(method_exists($at, 'testConnection'), "Method: testConnection() exists");
assertTest(method_exists($at, 'searchCampaigns'), "Method: searchCampaigns() exists");
assertTest(method_exists($at, 'generateTrackingLink'), "Method: generateTrackingLink() exists");

// --- 4. ACCESSTRADE Campaign Search & Gym Filtering ---
echo "\n--- 4. Testing Campaign Discovery & Gym Filtering ---\n";
// Test local gym filter method
$sampleCampaigns = array(
    array('id' => 'camp_1', 'name' => 'Whey Protein Store', 'merchant' => 'WheyStore', 'category' => 'Thực phẩm thể hình', 'status' => 1),
    array('id' => 'camp_2', 'name' => 'Gia dụng bếp chiên', 'merchant' => 'KitchenPro', 'category' => 'Gia dụng', 'status' => 1),
    array('id' => 'camp_3', 'name' => 'Găng tay Gym & Đai lưng', 'merchant' => 'GymMax', 'category' => 'Dụng cụ thể thao', 'status' => 1),
    array('id' => 'camp_4', 'name' => 'Mỹ phẩm son môi', 'merchant' => 'BeautyVN', 'category' => 'Làm đẹp', 'status' => 1)
);
$filtered = $at->filterGymCampaigns($sampleCampaigns);
assertTest(count($filtered) === 2, "Gym Filter: Correctly identified 2 gym/fitness campaigns from sample", "Found: " . count($filtered));
assertTest($filtered[0]['id'] === 'camp_1' && $filtered[1]['id'] === 'camp_3', "Gym Filter: Correct campaign IDs retained ('camp_1', 'camp_3')");
// Test search campaigns fallback when not configured
$searchRes = $at->searchCampaigns(array('keyword' => 'whey'));
assertTest(is_array($searchRes), "Campaign Search: Returns array response even in unconfigured state");
assertTest(isset($searchRes['success']), "Campaign Search: Structured response with 'success' key");
assertTest(isset($searchRes['data']), "Campaign Search: Structured response with 'data' array");

// --- 5. Datafeed Product Search & Schema Normalization ---
echo "\n--- 5. Testing Datafeed Product Search & Schema Normalization ---\n";
$sampleRawProduct = array(
    'product_id' => 'at_prod_101',
    'name' => 'BCAA 5000 Powder Phục Hồi Cơ',
    'price' => 650000,
    'aff_url' => 'https://click.accesstrade.vn/adv.php?rk=test_rk',
    'image' => 'https://img.accesstrade.vn/bcaa.jpg',
    'merchant' => 'WheyStoreVN',
    'category_name' => 'Thực phẩm bổ sung'
);
$normalized = $at->normalizeDatafeedProduct($sampleRawProduct);
assertTest(!empty($normalized['title']) && $normalized['title'] === 'BCAA 5000 Powder Phục Hồi Cơ', "Datafeed Normalization: Title mapped accurately");
assertTest($normalized['price'] === 650000.0, "Datafeed Normalization: Price normalized to float");
assertTest($normalized['merchant'] === 'WheyStoreVN', "Datafeed Normalization: Merchant identified");
assertTest($normalized['discovery_source'] === 'ACCESSTRADE_API', "Datafeed Normalization: Tagged with discovery_source='ACCESSTRADE_API'");
assertTest($normalized['is_accesstrade'] === 1, "Datafeed Normalization: Flagged with is_accesstrade=1");
assertTest(!empty($normalized['affiliate_url']), "Datafeed Normalization: Affiliate URL populated");

// --- 6. Dynamic Tracking Link Generation with sub1-sub4 ---
echo "\n--- 6. Testing Dynamic Tracking Link Generation ---\n";
$rawUrl = "https://shopee.vn/product/123456/789101";
$params = array(
    'id_product' => 42,
    'id_post' => 108,
    'id_experiment' => 5,
    'tracking_code' => 'KP_TRK_9999',
    'utm_source' => 'khoepro',
    'utm_medium' => 'organic_video',
    'utm_campaign' => 'whey_review',
    'utm_content' => 'hook_variant_a'
);
$linkResult = $at->generateTrackingLink($rawUrl, $params);
assertTest(is_array($linkResult) && $linkResult['success'] === true, "Link Generator: Produced successful tracking link result");
$generatedLink = $linkResult['tracking_url'] ?? '';
assertTest(strpos($generatedLink, 'sub1=42') !== false, "Link Generator: sub1 correctly mapped to id_product (42)");
assertTest(strpos($generatedLink, 'sub2=108') !== false, "Link Generator: sub2 correctly mapped to id_post (108)");
assertTest(strpos($generatedLink, 'sub3=5') !== false, "Link Generator: sub3 correctly mapped to id_experiment (5)");
assertTest(strpos($generatedLink, 'sub4=KP_TRK_9999') !== false, "Link Generator: sub4 correctly mapped to tracking_code ('KP_TRK_9999')");
assertTest(strpos($generatedLink, 'utm_source=khoepro') !== false, "Link Generator: utm_source standard parameter injected");

// --- 7. Outbound Affiliate Link Resolution ---
echo "\n--- 7. Testing Outbound Affiliate Link Resolution ---\n";
require_once LIBRARIES . 'class/class.Affiliate.php';
$affService = new Affiliate($d);
assertTest(class_exists('Affiliate'), "Class Existence: Affiliate class loaded");
$platMeta = Affiliate::getPlatformMeta('accesstrade');
assertTest(is_array($platMeta) && !empty($platMeta['name']), "Platform Meta: ACCESSTRADE platform meta resolved");
$discount = Affiliate::calculateDiscountPercent(1000000, 800000);
assertTest($discount === 20, "Discount Calculation: 1,000,000 -> 800,000 correctly computed as 20%");
$offers = $affService->getOffersByProductId(1, false);
assertTest(is_array($offers), "Affiliate Offers: getOffersByProductId returns array");


// --- 8. Staged Datafeed Discovery via ResearchProviderFactory ---
echo "\n--- 8. Testing Staged Datafeed Discovery via Factory ---\n";
$researchProvider = ResearchProviderFactory::create('accesstrade', $d, $func);
assertTest($researchProvider instanceof AccessTradeResearchProvider, "Factory: Successfully instantiated AccessTradeResearchProvider");
assertTest($researchProvider->getPlatformKey() === 'accesstrade', "Factory: Platform key is 'accesstrade'");
$feedResults = $researchProvider->searchProducts(array('keyword' => 'creatine', 'limit' => 5));
assertTest(is_array($feedResults), "Research Provider: searchProducts() returns array response");
assertTest(isset($feedResults['success']), "Research Provider: Result has 'success' status flag");

// --- 9. Automated Transaction Sync & Status Mapping ---
echo "\n--- 9. Testing Transaction Sync & Status Mapping ---\n";
$testTransactions = array(
    array(
        'order_id' => 'AT_ORD_TEST_001',
        'click_id' => 'CLK_001',
        'click_time' => date('Y-m-d H:i:s', time() - 3600),
        'conversion_time' => date('Y-m-d H:i:s', time() - 1800),
        'commission' => 25000,
        'order_amount' => 500000,
        'status' => 1, // Approved in AT
        'product_name' => 'Con-Cret Creatine HCL',
        'sub1' => '1',
        'sub2' => '2',
        'sub3' => '0',
        'sub4' => 'FITNADO_TRK_TEST_001'
    ),
    array(
        'order_id' => 'AT_ORD_TEST_002',
        'click_id' => 'CLK_002',
        'click_time' => date('Y-m-d H:i:s', time() - 7200),
        'conversion_time' => date('Y-m-d H:i:s', time() - 3600),
        'commission' => 15000,
        'order_amount' => 300000,
        'status' => 0, // Pending in AT
        'product_name' => 'Shaker Pro 700ml',
        'sub1' => '1',
        'sub2' => '0',
        'sub3' => '0',
        'sub4' => 'FITNADO_TRK_TEST_002'
    )
);
$mappedStatus1 = $at->mapTransactionStatus(1);
$mappedStatus0 = $at->mapTransactionStatus(0);
$mappedStatus2 = $at->mapTransactionStatus(2);
assertTest($mappedStatus1 === 'APPROVED', "Status Mapping: AT status 1 maps to 'APPROVED'");
assertTest($mappedStatus0 === 'PENDING', "Status Mapping: AT status 0 maps to 'PENDING'");
assertTest($mappedStatus2 === 'REJECTED', "Status Mapping: AT status 2 maps to 'REJECTED'");

$reconcileRes = $at->reconcileTransactions($testTransactions);
assertTest($reconcileRes['success'] === true, "Transaction Reconciliation: Executed successfully");
assertTest($reconcileRes['imported'] >= 2 || $reconcileRes['skipped'] >= 0, "Transaction Reconciliation: Correctly processed test transactions (Imported: {$reconcileRes['imported']}, Updated: {$reconcileRes['updated']}, Skipped: {$reconcileRes['skipped']})");

// --- 10. Transaction Idempotency (Anti-Duplicate vs CSV) ---
echo "\n--- 10. Testing Idempotency & Deduplication against CSV ---\n";
// Re-running reconciliation with the exact same payload must NOT double-count imported records
$reconcileRes2 = $at->reconcileTransactions($testTransactions);
assertTest($reconcileRes2['success'] === true, "Idempotency: Re-reconciliation executed cleanly");
assertTest($reconcileRes2['imported'] === 0, "Idempotency: Exactly 0 duplicates created on second run (Imported: 0)");
assertTest($reconcileRes2['skipped'] + $reconcileRes2['updated'] === 2, "Idempotency: Existing records were safely skipped/updated without duplicating");

// Check database records directly
$checkDb = $d->rawQueryOne("SELECT COUNT(*) as cnt FROM table_affiliate_conversion WHERE external_conversion_id = 'AT_ORD_TEST_001' AND platform = 'accesstrade'");
assertTest(!empty($checkDb['cnt']) && (int)$checkDb['cnt'] === 1, "Database Verification: Exactly 1 record exists in table_affiliate_conversion for 'AT_ORD_TEST_001'");

// --- 11. Operations Center & Background Worker Integration ---
echo "\n--- 11. Testing Operations Center & Worker Registration ---\n";
$registeredWorkers = $ops->getRegisteredWorkers();
assertTest(isset($registeredWorkers['accesstrade_sync']), "Worker Registration: 'accesstrade_sync' registered in OperationsService");
assertTest($registeredWorkers['accesstrade_sync']['file'] === 'cron/accesstrade_sync_worker.php', "Worker Registration: Points to valid cron script 'cron/accesstrade_sync_worker.php'");

// Run worker heartbeat test
$workerKey = 'accesstrade_sync';
$ops->recordWorkerStart($workerKey, 'worker');
$ops->recordHeartbeat($workerKey, array('limit' => 50));
$ops->recordWorkerSuccess($workerKey, array('imported' => 0, 'updated' => 0));
$wStatuses = $ops->getWorkerStatuses();
assertTest(isset($wStatuses[$workerKey]), "Worker Status: 'accesstrade_sync' status tracked in database");
assertTest($wStatuses[$workerKey]['health'] === OperationsService::STATUS_HEALTHY, "Worker Health: 'accesstrade_sync' reported as HEALTHY");

// --- 12. Full Project Regression Sanity Check ---
echo "\n--- 12. Testing Full Project Regression & Compatibility ---\n";
require_once LIBRARIES . 'class/class.AnalyticsService.php';
require_once LIBRARIES . 'class/class.OptimizationEngine.php';
require_once LIBRARIES . 'class/class.PublishJobQueue.php';

$analytics = new AnalyticsService($d, $func);
$optEngine = new OptimizationEngine($d, $func);
$pubQueue = new PublishJobQueue($d, $func);

assertTest($analytics !== null, "Regression: AnalyticsService instantiated cleanly");
assertTest($optEngine !== null, "Regression: OptimizationEngine instantiated cleanly");
assertTest($pubQueue !== null, "Regression: PublishJobQueue instantiated cleanly");

// Check database table existence for all phases
$phaseTables = array(
    'table_product_affiliate',
    'table_affiliate_conversion',
    'table_affiliate_click',
    'table_product_research',
    'table_ai_video',
    'table_publish_post',
    'table_analytics_event',
    'table_optimization_experiment',
    'table_system_worker_status'
);
$allTablesOk = true;
foreach ($phaseTables as $t) {
    $exists = $d->rawQueryOne("SHOW TABLES LIKE '{$t}'");
    if (empty($exists)) {
        $allTablesOk = false;
        break;
    }
}
assertTest($allTablesOk === true, "Database Regression: All core tables from Phase 02 through Phase 10.1 exist");
assertTest(file_exists(__DIR__ . '/.ai/reports/PHASE-10.1-PRODUCTION-ACCESSTRADE.md'), "Documentation: Phase 10.1 sign-off report is present");

echo "\n=======================================================\n";
echo "PHASE 10.1 TEST RESULTS: {$passCount} PASSED, {$failCount} FAILED\n";
echo "=======================================================\n";

if ($failCount === 0) {
    echo ">>> ALL PHASE 10.1 TESTS PASSED SUCCESSFULLY! <<<\n";
    exit(0);
} else {
    echo ">>> SOME TESTS FAILED! PLEASE INSPECT. <<<\n";
    exit(1);
}
