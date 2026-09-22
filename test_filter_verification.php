<?php
/**
 * KHOEPRO - ACCESSTRADE Relevance Filter Verification Test Suite
 * PHP 7.4 Compatible
 */

define('LIBRARIES', __DIR__ . '/libraries/');
require_once 'libraries/config.php';
require_once LIBRARIES . 'class/class.PDODb.php';
require_once LIBRARIES . 'class/class.AccessTradeProvider.php';
require_once LIBRARIES . 'class/class.ProductRelevanceFilter.php';
require_once LIBRARIES . 'class/class.ResearchProvider.php';
require_once LIBRARIES . 'class/class.Affiliate.php';
require_once LIBRARIES . 'class/class.AnalyticsService.php';

$d = new PDODb($config['database']);
$func = null;

$passed = 0;
$failed = 0;

function assertTest($condition, $description) {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  [PASS] {$description}\n";
    } else {
        $failed++;
        echo "  [FAIL] {$description}\n";
    }
}

echo "========================================================\n";
echo "KHOEPRO ACCESSTRADE FILTER VERIFICATION & REGRESSION\n";
echo "========================================================\n\n";

// --- 1. Provider Connection & Authentication ---
echo "--- 1. Testing Live Connection & Credentials ---\n";
$atProvider = new AccessTradeProvider($d, $func);
assertTest($atProvider->isConfigured(), "Provider Config: Access Key is configured");
$conn = $atProvider->testConnection();
assertTest($conn['success'] === true, "Live Connection: API Ping returned 200 OK ({$conn['latency_ms']}ms)");

// --- 2. Live Campaign Discovery ---
echo "\n--- 2. Testing Live Campaign Discovery ---\n";
$camps = $atProvider->getCampaigns(array('limit' => 50));
assertTest($camps['success'] === true && count($camps['campaigns']) > 0, "Campaigns API: Fetched " . count($camps['campaigns']) . " real campaigns");

// --- 3. Dynamic Tracking Link Generation (utm_source=khoepro) ---
echo "\n--- 3. Testing Tracking Link with utm_source=khoepro ---\n";
$testDestUrl = "https://shopee.vn/product/123/456";
$trkResult = $atProvider->generateTrackingLink($testDestUrl, '', array(
    'id_product' => 10,
    'id_post' => 20,
    'id_experiment' => 3,
    'tracking_code' => 'KP_TRK_TEST_FILTER_001'
));
assertTest($trkResult['success'] === true, "Link Generation: Result returned success");
$trkUrl = $trkResult['tracking_url'] ?? '';
assertTest(strpos($trkUrl, 'utm_source=khoepro') !== false, "Attribution: Default utm_source is 'khoepro' (verified in {$trkUrl})");
assertTest(strpos($trkUrl, 'sub1=10') !== false, "Attribution: sub1=10 (Product ID)");
assertTest(strpos($trkUrl, 'sub2=20') !== false, "Attribution: sub2=20 (Post ID)");
assertTest(strpos($trkUrl, 'sub3=3') !== false, "Attribution: sub3=3 (Experiment ID)");
assertTest(strpos($trkUrl, 'sub4=KP_TRK_TEST_FILTER_001') !== false, "Attribution: sub4=KP_TRK_TEST_FILTER_001 (Tracking Fingerprint)");

// --- 4. Two-Stage Filter Verification on Real AccessTrade Datafeed ---
echo "\n--- 4. Fetching Real AccessTrade Datafeed Products & Running Two-Stage Filter ---\n";
$filter = new ProductRelevanceFilter($d, $func);

$totalReceived = 0;
$relevantCount = 0;
$notRelevantCount = 0;
$needsReviewCount = 0;

$evaluatedLiveProducts = array();

// Fetch up to 100 products (4 pages of 25)
for ($page = 1; $page <= 4; $page++) {
    $feedRes = $atProvider->searchProducts('', array('page' => $page, 'limit' => 25));
    if ($feedRes['success'] && !empty($feedRes['products'])) {
        foreach ($feedRes['products'] as $p) {
            $totalReceived++;
            $eval = $filter->evaluateProduct($p);
            $evaluatedLiveProducts[] = array(
                'product' => $p,
                'evaluation' => $eval
            );

            if ($eval['verdict'] === ProductRelevanceFilter::VERDICT_RELEVANT) {
                $relevantCount++;
            } elseif ($eval['verdict'] === ProductRelevanceFilter::VERDICT_NOT_RELEVANT) {
                $notRelevantCount++;
            } else {
                $needsReviewCount++;
            }
        }
    }
}

echo "Total Live Feed Products Received: {$totalReceived}\n";
echo "RELEVANT: {$relevantCount}\n";
echo "NOT_RELEVANT: {$notRelevantCount}\n";
echo "NEEDS_REVIEW: {$needsReviewCount}\n";

assertTest($totalReceived >= 20, "Datafeed Ingestion: Successfully received {$totalReceived} live products from AccessTrade");
assertTest($notRelevantCount === $totalReceived, "Relevance Gate: All {$totalReceived} 30Shine cosmetic/grooming products correctly classified as NOT_RELEVANT");
assertTest($relevantCount === 0, "Safety Gate: Zero cosmetics/grooming leaked into RELEVANT pool (0 leakage)");

// --- 5. Benchmark Taxonomy Test Suite (Positive & Negative Test Cases) ---
echo "\n--- 5. Testing Benchmark Taxonomy & Semantic Context ---\n";

$benchmarkItems = array(
    // Positive Core Fitness & Gym Gear
    array('name' => 'Đai lưng tập gym Valeo da bò 3 lớp khóa inox', 'merchant' => 'WheyStore', 'category' => 'Phụ kiện tập gym', 'expected' => 'RELEVANT'),
    array('name' => 'Dây kéo lưng lifting straps trợ lực deadlift FITNADO Pro', 'merchant' => 'GymGearVN', 'category' => 'Bảo hộ thể thao', 'expected' => 'RELEVANT'),
    array('name' => 'Găng tay tập Gym quấn cổ tay đệm silicone chống chai', 'merchant' => 'AolikesMall', 'category' => 'Găng tay', 'expected' => 'RELEVANT'),
    array('name' => 'Dây kháng lực ngũ sắc 150lbs tập gym tại nhà', 'merchant' => 'SportPro', 'category' => 'Dây kháng lực', 'expected' => 'RELEVANT'),
    array('name' => 'Con lăn tập bụng 4 bánh trợ lực tự hồi kèm thảm quỳ', 'merchant' => 'FitnessMall', 'category' => 'Dụng cụ tập bụng', 'expected' => 'RELEVANT'),
    array('name' => 'Thảm tập Yoga PU định tuyến chống trượt cao cấp 6mm', 'merchant' => 'YogaHouse', 'category' => 'Thảm yoga', 'expected' => 'RELEVANT'),
    array('name' => 'Con lăn massage bọt xốp Foam Roller giãn cơ bắp', 'merchant' => 'RecoveryVN', 'category' => 'Phục hồi cơ', 'expected' => 'RELEVANT'),
    array('name' => 'Súng massage cầm tay 6 cấp độ giãn cơ chuyên sâu', 'merchant' => 'TechSport', 'category' => 'Thiết bị massage', 'expected' => 'RELEVANT'),
    array('name' => 'Cân sức khỏe điện tử thông minh đo 18 chỉ số mỡ cơ InBody', 'merchant' => 'SmartScaleVN', 'category' => 'Thiết bị sức khỏe', 'expected' => 'RELEVANT'),
    array('name' => 'Bình lắc thể thao giữ nhiệt Stainless Steel Shaker 750ml', 'merchant' => 'ShakerVN', 'category' => 'Bình lắc', 'expected' => 'RELEVANT'),
    array('name' => 'Whey Gold Standard 5lbs Optimum Nutrition', 'merchant' => 'WheyStore', 'category' => 'Thực phẩm bổ sung', 'expected' => 'RELEVANT'),
    array('name' => 'Bó gối thể thao Knee Sleeve 7mm tập Squat nặng', 'merchant' => 'PowerliftingVN', 'category' => 'Bó gối', 'expected' => 'RELEVANT'),

    // Negative Irrelevant Categories
    array('name' => 'Gôm xịt tóc Lady Killer - Tóc đẹp thách thức thời gian', 'merchant' => '30shine_store', 'category' => '', 'expected' => 'NOT_RELEVANT'),
    array('name' => 'Sữa Rửa Mặt DaBo For Men', 'merchant' => '30shine_store', 'category' => '', 'expected' => 'NOT_RELEVANT'),
    array('name' => 'Mở thẻ tín dụng hoàn tiền SHB TAPTAP', 'merchant' => 'shb_taptap', 'category' => 'Banking', 'expected' => 'NOT_RELEVANT'),
    array('name' => 'Tuyển dụng tài xế GSM Philippines thu nhập hấp dẫn', 'merchant' => 'gsm_ph', 'category' => 'Dịch vụ', 'expected' => 'NOT_RELEVANT'),
    array('name' => 'CellphoneS - Preorder iPhone 18 Pro Max 256GB', 'merchant' => 'cellphones_ambassador', 'category' => 'Công nghệ', 'expected' => 'NOT_RELEVANT'),
    array('name' => 'Vay tiêu dùng Viet Credit hạn mức 50 triệu', 'merchant' => 'vcredit2026', 'category' => 'Tài chính', 'expected' => 'NOT_RELEVANT'),
    array('name' => 'Sim Vinaphone 4G trọn gói cước trả sau', 'merchant' => 'vnpt_sim', 'category' => 'Viễn thông', 'expected' => 'NOT_RELEVANT'),
    array('name' => 'Kem Trị Mụn Dưỡng Trắng Da Skinlosophy', 'merchant' => 'skinlosophy_shopee', 'category' => 'Mỹ phẩm', 'expected' => 'NOT_RELEVANT')
);

$benchmarkPassed = 0;
foreach ($benchmarkItems as $b) {
    $evalB = $filter->evaluateProduct($b);
    $matches = ($evalB['verdict'] === $b['expected']);
    if ($matches) $benchmarkPassed++;
    assertTest($matches, "Benchmark [{$b['expected']}]: '{$b['name']}' -> {$evalB['verdict']} ({$evalB['confidence']}%)");
}
assertTest($benchmarkPassed === count($benchmarkItems), "Benchmark Suite: {$benchmarkPassed}/" . count($benchmarkItems) . " test cases passed 100%");

// --- 6. AccessTradeResearchProvider Staging Gate Test ---
echo "\n--- 6. Testing AccessTradeResearchProvider Staging Gate ---\n";
$atResearch = ResearchProviderFactory::create('accesstrade', $d, $func);
assertTest($atResearch instanceof AccessTradeResearchProvider, "Research Factory: Instantiated AccessTradeResearchProvider");

// Querying live datafeed through AccessTradeResearchProvider:
// Should return 0 candidates because live feed only contains 30Shine cosmetics!
$discoveredCandidates = $atResearch->discoverCandidates(array('limit' => 20));
echo "Discovered candidates through gate: " . count($discoveredCandidates) . "\n";
assertTest(count($discoveredCandidates) === 0, "Staging Gate Defense: Blocked all 30Shine cosmetic products from entering research staging");

// --- 7. Audit Existing Records in table_product_research ---
echo "\n--- 7. Auditing Existing Records in table_product_research ---\n";
$existingRecords = $d->rawQuery("SELECT id, name, platform, discovery_source, status, category_hint FROM table_product_research ORDER BY id DESC LIMIT 10");
assertTest(count($existingRecords) <= 10 && count($existingRecords) > 0, "Audit: Retrieved " . count($existingRecords) . " records for audit");

$auditedRelevant = 0;
$auditedNotRelevant = 0;
foreach ($existingRecords as $rec) {
    $auditEval = $filter->evaluateProduct(array(
        'name' => $rec['name'],
        'merchant' => $rec['platform'],
        'category' => $rec['category_hint']
    ));
    echo "  - Record #{$rec['id']}: '{$rec['name']}' -> {$auditEval['verdict']} (Current DB Status: {$rec['status']})\n";
    if ($auditEval['verdict'] === ProductRelevanceFilter::VERDICT_RELEVANT) {
        $auditedRelevant++;
    } else {
        $auditedNotRelevant++;
    }
}
assertTest($auditedRelevant === count($existingRecords), "Audit Result: All " . count($existingRecords) . " audited historical records are RELEVANT fitness products");

// --- 8. Regression Checks (Click, Conversion, Analytics) ---
echo "\n--- 8. Testing Regression on Analytics, Click Tracking & Transactions ---\n";
$analytics = new AnalyticsService($d, $func);
$testCode = 'KP_REG_TEST_' . time();
$eventId = $analytics->logEvent(AnalyticsService::EVENT_AFFILIATE_CLICK, array(
    'id_product' => 1,
    'tracking_code' => $testCode,
    'source' => 'accesstrade',
    'medium' => 'website'
));
assertTest(!empty($eventId) && $eventId > 0, "Click Tracking: Recorded click event in table_analytics_event (ID #{$eventId})");

// Test Transaction Read
$transRes = $atProvider->fetchTransactions(time() - (30 * 86400), time(), null, 1, 10);
assertTest($transRes['success'] === true, "Transaction API: fetchTransactions() returned 200 OK (Found: " . count($transRes['orders']) . ")");

// --- 9. PHP Version & Compatibility ---
echo "\n--- 9. Checking PHP Version & Compatibility ---\n";
echo "Current PHP Version: " . PHP_VERSION . "\n";
assertTest(version_compare(PHP_VERSION, '7.4.0', '>='), "PHP Version Check: Environment supports PHP 7.4+ syntax");

echo "\n========================================================\n";
echo "SUMMARY: {$passed} PASSED / {$failed} FAILED\n";
echo "========================================================\n";
