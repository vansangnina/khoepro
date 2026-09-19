<?php
/**
 * Automated Verification Script for FITNADO Phase 03
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
$dbConfig['unix_socket'] = '/Applications/MAMP/tmp/mysql/mysql.sock';
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);
$research = new ProductResearch($d, $func);

echo "=======================================================\n";
echo "FITNADO PHASE 03 - AUTOMATED TEST SUITE (PHP 7.4)\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $testName, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . " - " . $details . "\n";
        $failCount++;
    }
}

// -------------------------------------------------------------
// TEST 1: URL Normalization
// -------------------------------------------------------------
echo "--- TEST 1: URL Normalization ---\n";
$dirtyUrl1 = "https://www.tiktok.com/@shop/product/172938491829?utm_source=tiktok_aff&utm_medium=video&spm=123.456&aff_trace_key=abc12345&fbclid=IwAR09";
$cleanUrl1 = $research->normalizeUrl($dirtyUrl1);
$expectedUrl1 = "https://tiktok.com/@shop/product/172938491829";
assertTest($cleanUrl1 === $expectedUrl1, "Normalize dirty TikTok URL (strip UTM/spm/aff/fbclid)", "Got: " . $cleanUrl1);

$dirtyUrl2 = "https://shopee.vn/Dây-kháng-lực-ngũ-sắc-i.12345.67890?utm_campaign=gym_sale&ref=affiliate";
$cleanUrl2 = $research->normalizeUrl($dirtyUrl2);
$expectedUrl2 = "https://shopee.vn/Dây-kháng-lực-ngũ-sắc-i.12345.67890";
assertTest($cleanUrl2 === $expectedUrl2, "Normalize Shopee product URL", "Got: " . $cleanUrl2);

// -------------------------------------------------------------
// TEST 2: Name Normalization
// -------------------------------------------------------------
echo "\n--- TEST 2: Name Normalization ---\n";
$name1 = "  Đai Lưng Tập Gym Cực Xịn Aolikes (Màu Đỏ)  ";
$norm1 = $research->normalizeName($name1);
assertTest($norm1 === "dai lung tap gym cuc xin aolikes mau do", "Normalize Vietnamese product name", "Got: " . $norm1);

// -------------------------------------------------------------
// TEST 3: Scoring Engine - Full Data
// -------------------------------------------------------------
echo "\n--- TEST 3: Scoring Engine (Full Data) ---\n";
$fullData = array(
    'sales_count' => 5500,       // Demand: ~90
    'rating' => 4.8,             // Rating: ~90
    'review_count' => 1200,      // Reviews: ~90
    'estimated_gmv' => 150000000,// GMV: 70
    'top_video_views' => 1500000,// Content views: 90
    'creator_count' => 25,       // Creators: 85
    'problem_solved' => 'Hỗ trợ bảo vệ cột sống và tăng lực gồng core khi thực hiện các bài squat, deadlift nặng',
    'target_audience' => 'Người tập gym nặng, powerlifter, người bị đau lưng nhẹ khi tập tạ',
    'price' => 350000,
    'commission_rate' => 15,     // Commission: ~90
    'commission_value' => 52500,
    'competition_score' => 80,   // Competition: 80
    'primary_keyword' => 'dai lung tap gym squat deadlift' // SEO: 80
);

$scoreFull = $research->calculateTotalScore($fullData);
assertTest($scoreFull['demand_score'] >= 80, "Demand Score calculation (>80)", "Got: " . $scoreFull['demand_score']);
assertTest($scoreFull['content_score'] >= 80, "Content Score calculation (>80)", "Got: " . $scoreFull['content_score']);
assertTest($scoreFull['commission_score'] >= 75, "Commission Score calculation (>=75)", "Got: " . $scoreFull['commission_score']);
assertTest($scoreFull['total_score'] >= 80 && $scoreFull['total_score'] <= 95, "Total Weighted Score (80-95)", "Got: " . $scoreFull['total_score']);
assertTest(count($scoreFull['reasons']) >= 5, "Score Explanations Generated", "Reasons count: " . count($scoreFull['reasons']));

// -------------------------------------------------------------
// TEST 4: Scoring Engine - Missing Data (NULL != 0)
// -------------------------------------------------------------
echo "\n--- TEST 4: Scoring Engine (Missing Data NULL != 0) ---\n";
$sparseData = array(
    'sales_count' => 2500,
    'rating' => 4.7,
    'review_count' => null,
    'estimated_gmv' => null,
    'top_video_views' => null,
    'creator_count' => null,
    'problem_solved' => null,
    'target_audience' => null,
    'price' => null,
    'commission_rate' => null,
    'commission_value' => null,
    'competition_score' => null,
    'seo_score' => null,
    'primary_keyword' => null
);

$scoreSparse = $research->calculateTotalScore($sparseData);
assertTest($scoreSparse['demand_score'] !== null && $scoreSparse['demand_score'] > 0, "Sparse data calculates demand score smoothly", "Got: " . $scoreSparse['demand_score']);
assertTest($scoreSparse['content_score'] === null, "Missing content data is NULL (not 0)", "Got: " . var_export($scoreSparse['content_score'], true));
assertTest($scoreSparse['commission_score'] === null, "Missing commission data is NULL (not 0)", "Got: " . var_export($scoreSparse['commission_score'], true));
assertTest($scoreSparse['total_score'] !== null && !is_nan($scoreSparse['total_score']), "Total score handles missing dimensions proportionally", "Got: " . $scoreSparse['total_score']);

// -------------------------------------------------------------
// TEST 5: Weight Configuration
// -------------------------------------------------------------
echo "\n--- TEST 5: Weight Configuration ---\n";
$defaultWeights = $research->getWeights();
assertTest(array_sum($defaultWeights) == 100, "Default weights sum to 100%", "Sum: " . array_sum($defaultWeights));

$invalidWeights = array('demand' => 50, 'content' => 30, 'commission' => 20, 'competition' => 10, 'seo' => 10);
$resInvalid = $research->setWeights($invalidWeights);
assertTest($resInvalid['status'] === false, "Reject invalid weights sum != 100% (120%)", "Msg: " . $resInvalid['message']);

// -------------------------------------------------------------
// TEST 6: Candidate Lifecycle & Database CRUD
// -------------------------------------------------------------
echo "\n--- TEST 6: Candidate Lifecycle & Duplicate Detection ---\n";

// Clean any previous test records
$d->rawQuery("delete from #_product_research where external_product_id = 'TEST_CANDIDATE_001'");

$candidateData = array(
    'name' => 'Con Lăn Tập Bụng 4 Bánh FITNADO Power Roller',
    'normalized_name' => $research->normalizeName('Con Lăn Tập Bụng 4 Bánh FITNADO Power Roller'),
    'platform' => 'tiktok',
    'source_url' => 'https://www.tiktok.com/@fitshop/product/TEST_CANDIDATE_001?utm_source=tiktok',
    'normalized_url' => $research->normalizeUrl('https://www.tiktok.com/@fitshop/product/TEST_CANDIDATE_001?utm_source=tiktok'),
    'external_product_id' => 'TEST_CANDIDATE_001',
    'category_hint' => 'Dụng cụ tập bụng',
    'brand_hint' => 'FITNADO Core',
    'price' => 249000,
    'original_price' => 399000,
    'sales_count' => 3200,
    'rating' => 4.8,
    'review_count' => 540,
    'commission_rate' => 14.5,
    'commission_value' => 36105,
    'top_video_views' => 850000,
    'creator_count' => 15,
    'problem_solved' => 'Tập cơ bụng 6 múi tại nhà với hệ thống trợ lực đàn hồi an toàn',
    'target_audience' => 'Nam nữ muốn giảm mỡ bụng tại nhà',
    'research_notes' => 'Bánh xe bọc cao su chống ồn, có đệm lót đầu gối',
    'primary_keyword' => 'con lan tap bung 4 banh co tro luc',
    'status' => 'DISCOVERED',
    'date_created' => time(),
    'date_updated' => time()
);

$scoreCalc = $research->calculateTotalScore($candidateData);
$candidateData['demand_score'] = $scoreCalc['demand_score'];
$candidateData['content_score'] = $scoreCalc['content_score'];
$candidateData['commission_score'] = $scoreCalc['commission_score'];
$candidateData['competition_score'] = $scoreCalc['competition_score'];
$candidateData['seo_score'] = $scoreCalc['seo_score'];
$candidateData['total_score'] = $scoreCalc['total_score'];
$candidateData['score_breakdown'] = json_encode($scoreCalc, JSON_UNESCAPED_UNICODE);

$candidateId = $d->insert('product_research', $candidateData);
assertTest($candidateId > 0, "Insert research candidate into table_product_research (ID: #$candidateId)");

// Test Duplicate Detection
$dupCheck = $research->checkDuplicate('tiktok', 'TEST_CANDIDATE_001', 'https://tiktok.com/@another/TEST_CANDIDATE_001', 'Con Lăn Khác');
assertTest($dupCheck['is_duplicate'] === true && $dupCheck['type'] === 'EXACT_EXTERNAL_ID', "Duplicate Detection: Matches EXACT_EXTERNAL_ID", "Type: " . $dupCheck['type']);

// Test Status Transition: DISCOVERED -> APPROVED
$d->rawQuery("update #_product_research set status = 'APPROVED' where id = ?", array($candidateId));
$checkStatus = $d->rawQueryOne("select status from #_product_research where id = ?", array($candidateId));
assertTest($checkStatus['status'] === 'APPROVED', "Transition candidate status to APPROVED");

// -------------------------------------------------------------
// TEST 7: Candidate -> table_product Mapping
// -------------------------------------------------------------
echo "\n--- TEST 7: Candidate -> table_product Mapping ---\n";

// Get valid category list id
$catList = $d->rawQueryOne("select id from #_product_list limit 0,1");
$targetListId = !empty($catList['id']) ? (int)$catList['id'] : 0;

$mappingData = array(
    'namevi' => 'Con Lăn Tập Bụng 4 Bánh FITNADO Power Roller',
    'id_list' => $targetListId,
    'regular_price' => 249000,
    'sale_price' => 0,
    'descvi' => 'Tập cơ bụng 6 múi tại nhà với hệ thống trợ lực đàn hồi an toàn',
    'specs' => 'Bánh xe bọc cao su chống ồn, có đệm lót đầu gối',
    'affiliate_url' => 'https://tiktok.com/@fitshop/product/TEST_CANDIDATE_001'
);

$createRes = $research->createProductFromCandidate($candidateId, $mappingData);
assertTest($createRes['status'] === true && !empty($createRes['product_id']), "Map Candidate to table_product (New Product ID: #" . ($createRes['product_id'] ?? 0) . ")", $createRes['message'] ?? '');

$createdProdId = $createRes['product_id'] ?? 0;
if ($createdProdId > 0) {
    // Verify product draft status (NO 'hienthi')
    $prodRow = $d->rawQueryOne("select id, namevi, status, regular_price, type from #_product where id = ?", array($createdProdId));
    assertTest(strpos($prodRow['status'] ?? '', 'hienthi') === false, "Product created in UNPUBLISHED draft state (no 'hienthi')");
    assertTest($prodRow['type'] === 'san-pham', "Product type is 'san-pham'");
    assertTest($prodRow['regular_price'] == 249000, "Product price mapped correctly (249,000 VND)");

    // Verify affiliate offer created in table_product_affiliate
    $affRow = $d->rawQueryOne("select id, id_product, platform, price, affiliate_url from #_product_affiliate where id_product = ?", array($createdProdId));
    assertTest(!empty($affRow['id']), "Initial Affiliate Offer seeded in table_product_affiliate (Affiliate ID: #" . ($affRow['id'] ?? 0) . ")");
    assertTest($affRow['platform'] === 'tiktok', "Affiliate platform matches candidate ('tiktok')");

    // Verify candidate updated with id_product and status PRODUCT_CREATED
    $candUpdated = $d->rawQueryOne("select id_product, status from #_product_research where id = ?", array($candidateId));
    assertTest($candUpdated['id_product'] == $createdProdId, "Candidate linked with id_product (#$createdProdId)");
    assertTest($candUpdated['status'] === 'PRODUCT_CREATED', "Candidate status updated to PRODUCT_CREATED");

    // Guard Test: Try creating product AGAIN from same candidate
    $secondAttempt = $research->createProductFromCandidate($candidateId, $mappingData);
    assertTest($secondAttempt['status'] === false, "Guard: Prevent duplicate product creation from already linked candidate", "Msg: " . $secondAttempt['message']);
}

// -------------------------------------------------------------
// SUMMARY
// -------------------------------------------------------------
echo "\n=======================================================\n";
echo "TEST RESULTS: " . $passCount . " PASSED, " . $failCount . " FAILED\n";
echo "=======================================================\n";

if ($failCount === 0) {
    echo ">>> ALL PHASE 03 AUTOMATED TESTS PASSED SUCCESSFULLY! <<<\n";
} else {
    echo ">>> SOME TESTS FAILED! PLEASE REVIEW OUTPUT. <<<\n";
}
