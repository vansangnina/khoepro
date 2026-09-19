<?php
/**
 * Automated Verification Script for FITNADO Phase 05
 * AI Content Engine Test Suite
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
if (file_exists('/Applications/MAMP/tmp/mysql/mysql.sock')) {
    $dbConfig['unix_socket'] = '/Applications/MAMP/tmp/mysql/mysql.sock';
}
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);

require_once LIBRARIES . 'class/class.ProductResearch.php';
require_once LIBRARIES . 'class/class.AIResearchAgent.php';
require_once LIBRARIES . 'class/class.AIContentEngine.php';
require_once LIBRARIES . 'class/class.AIContentJobQueue.php';

$contentEngine = new AIContentEngine($d, $func);
$contentQueue = new AIContentJobQueue($d, $func);

echo "=======================================================\n";
echo "FITNADO PHASE 05 - AUTOMATED TEST SUITE (PHP 7.4)\n";
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
// SETUP FIXTURE PRODUCT & RESEARCH
// -------------------------------------------------------------
$runSuffix = time();
$testProdName = "Dây Kéo Lưng FITNADO Pro Deadlift " . $runSuffix;
$testProdCode = "PROD_P05_" . $runSuffix;

$testProductId = $d->insert('product', array(
    'namevi' => $testProdName,
    'slugvi' => 'day-keo-lung-fitnado-pro-deadlift-' . $runSuffix,
    'code' => $testProdCode,
    'id_list' => 0,
    'id_cat' => 0,
    'id_item' => 0,
    'id_sub' => 0,
    'id_brand' => 0,
    'regular_price' => 250000,
    'sale_price' => 199000,
    'discount' => 20,
    'review_score' => 9.2,
    'review_count' => 120,
    'numb' => 1,
    'view' => 0,
    'descvi' => 'Mô tả gốc ban đầu trước khi áp dụng AI content',
    'contentvi' => '<p>Nội dung bài viết gốc của sản phẩm</p>',
    'expert_pros' => "Đệm êm\nChắc chắn",
    'expert_cons' => "Màu đen duy nhất",
    'verdict' => "Đáng mua trong tầm giá",
    'best_for' => "Gymer tập nặng",
    'specs' => '{"ChatLieu": "Cotton + Silicon", "ChieuDai": "45cm"}',
    'status' => 'hienthi',
    'type' => 'san-pham',
    'date_created' => time(),
    'date_updated' => time()
));

$testResearchId = $d->insert('product_research', array(
    'name' => $testProdName,
    'normalized_name' => 'day keo lung fitnado pro deadlift ' . $runSuffix,
    'platform' => 'tiktok',
    'source_url' => 'https://tiktok.com/@fitnado/product/' . $testProdCode,
    'normalized_url' => 'https://tiktok.com/@fitnado/product/' . $testProdCode,
    'external_product_id' => $testProdCode,
    'price' => 199000,
    'sales_count' => 3500,
    'rating' => 4.9,
    'review_count' => 620,
    'commission_rate' => 15.0,
    'problem_solved' => 'Trợ lực sức nắm cổ tay khi tập bài Deadlift và Kéo xà nặng.',
    'target_audience' => 'Người tập thể hình muốn tăng mức tạ và bảo vệ khớp cổ tay.',
    'primary_keyword' => 'day keo lung lifting straps deadlift',
    'demand_score' => 88.5,
    'content_score' => 85.0,
    'commission_score' => 80.0,
    'competition_score' => 75.0,
    'seo_score' => 82.0,
    'total_score' => 83.2,
    'status' => 'PRODUCT_CREATED',
    'id_product' => $testProductId,
    'discovery_source' => 'phase05_fixture',
    'date_created' => time(),
    'date_updated' => time()
));

// Insert Evidence Fact
$d->insert('product_research_evidence', array(
    'id_research' => $testResearchId,
    'provider' => 'tiktok',
    'source_url' => 'https://tiktok.com/@fitnado/product/' . $testProdCode,
    'evidence_type' => 'FACT',
    'field_name' => 'sales_count',
    'field_value' => '3500',
    'captured_at' => time()
));

// -------------------------------------------------------------
// TEST 1: Input Aggregation & Deterministic Source Hash
// -------------------------------------------------------------
echo "--- TEST 1: Input Aggregation & Source Hash ---\n";
$aggregated = $contentEngine->aggregateProductData($testProductId);
assertTest(!empty($aggregated['product']) && $aggregated['product']['id'] == $testProductId, "Aggregate real product record from database");
assertTest(!empty($aggregated['research']) && $aggregated['research']['id'] == $testResearchId, "Aggregate linked research candidate facts");
assertTest(!empty($aggregated['evidence']), "Aggregate research evidence provenance records");

$sourceHash1 = $contentEngine->generateSourceHash($aggregated);
$sourceHash2 = $contentEngine->generateSourceHash($aggregated);
assertTest(!empty($sourceHash1) && strlen($sourceHash1) === 64, "Generate 64-character SHA-256 source hash");
assertTest($sourceHash1 === $sourceHash2, "Source hash is strictly deterministic for identical facts");

// -------------------------------------------------------------
// TEST 2: Product Analysis Generation & 12 Dimensions
// -------------------------------------------------------------
echo "\n--- TEST 2: Product Analysis Generation ---\n";
$genAnalysis = $contentEngine->generateContent($testProductId, 'product_analysis');
assertTest($genAnalysis['status'] === true, "Generate structured Product Analysis (Content ID: #" . ($genAnalysis['content_id'] ?? 0) . ")");
assertTest($genAnalysis['version'] === 1, "Initial version is v1");

$cand1 = $d->rawQueryOne("select * from #_ai_content where id = ?", array($genAnalysis['content_id']));
assertTest($cand1['status'] === 'REVIEW_REQUIRED', "New content starts at REVIEW_REQUIRED (Human Gate enforced)");
assertTest($cand1['is_active'] == 0, "New content is not active until approved");

$struct1 = json_decode($cand1['structured_data'], true);
assertTest(!empty($struct1['problem_solved']) && !empty($struct1['target_audience']), "Analysis includes problem_solved and target_audience");
assertTest(is_array($struct1['pros']) && count($struct1['pros']) >= 2, "Analysis includes structured pros list");
assertTest(is_array($struct1['cons']) && count($struct1['cons']) >= 1, "Analysis includes structured cons list");
assertTest(is_array($struct1['suitable_for']) && is_array($struct1['not_suitable_for']), "Analysis includes suitable_for and not_suitable_for");
assertTest(!empty($struct1['buying_considerations']), "Analysis includes buying considerations");

// -------------------------------------------------------------
// TEST 3: Quality Gate & Factual Integrity Rules
// -------------------------------------------------------------
echo "\n--- TEST 3: Quality Gate & Hallucination Prevention ---\n";
// Case A: Fake personal test claim without REAL_TEST evidence
$badPersonalClaimData = array('problem_solved' => 'Tôi đã dùng sản phẩm này 30 ngày và thấy rất tốt');
$checkA = $contentEngine->validateQualityGate('product_analysis', $badPersonalClaimData, $aggregated);
assertTest($checkA['passed'] === false, "Quality Gate flags unverified personal experience claim ('Tôi đã dùng...')");

// Case B: Illegal medical claim
$badMedicalData = array('problem_solved' => 'Cam kết chữa đau lưng và điều trị thoát vị đĩa đệm');
$checkB = $contentEngine->validateQualityGate('product_analysis', $badMedicalData, $aggregated);
assertTest($checkB['passed'] === false, "Quality Gate flags illegal medical promise ('chữa đau lưng')");

// Case C: Fake customer testimonial without real reviews
$emptyAggregated = $aggregated;
$emptyAggregated['reviews'] = array();
$badReviewData = array('summary' => 'Anh Nam chia sẻ: sản phẩm rất tốt');
$checkC = $contentEngine->validateQualityGate('review_draft', $badReviewData, $emptyAggregated);
assertTest($checkC['passed'] === false, "Quality Gate flags fake customer testimonial ('Anh Nam chia sẻ...')");

// -------------------------------------------------------------
// TEST 4: 7 Strategic TikTok Hooks Generation
// -------------------------------------------------------------
echo "\n--- TEST 4: 7 Strategic TikTok Hooks ---\n";
$genHooks = $contentEngine->generateContent($testProductId, 'tiktok_hooks');
assertTest($genHooks['status'] === true, "Generate TikTok Strategic Hooks package");

$hookRecord = $d->rawQueryOne("select * from #_ai_content where id = ?", array($genHooks['content_id']));
$hookStruct = json_decode($hookRecord['structured_data'], true);
assertTest(isset($hookStruct['hooks']) && count($hookStruct['hooks']) === 7, "Generated exactly 7 strategic TikTok hook variations");

$hookTypesFound = array_column($hookStruct['hooks'], 'hook_type');
assertTest(in_array('problem', $hookTypesFound) && in_array('mistake', $hookTypesFound) && in_array('comparison', $hookTypesFound), "Contains Problem, Mistake, and Comparison hooks");
assertTest(in_array('curiosity', $hookTypesFound) && in_array('demo', $hookTypesFound) && in_array('buyer_warning', $hookTypesFound) && in_array('value', $hookTypesFound), "Contains Curiosity, Demo, Buyer Warning, and Value hooks");

// -------------------------------------------------------------
// TEST 5: TikTok Script (30s) + Scene Shot Plan
// -------------------------------------------------------------
echo "\n--- TEST 5: TikTok Script (30s) & Shot Plan ---\n";
$genScript = $contentEngine->generateContent($testProductId, 'tiktok_script', array('target_duration' => 30, 'content_angle' => 'Problem/Solution'));
assertTest($genScript['status'] === true, "Generate 30s TikTok Script + Shot Plan");

$scriptRecord = $d->rawQueryOne("select * from #_ai_content where id = ?", array($genScript['content_id']));
assertTest($scriptRecord['target_duration'] == 30, "Script target duration is 30s");

$scriptStruct = json_decode($scriptRecord['structured_data'], true);
assertTest(!empty($scriptStruct['hook_text']) && !empty($scriptStruct['cta_text']), "Script contains hook and CTA text");
assertTest(!empty($scriptStruct['shot_plan']) && is_array($scriptStruct['shot_plan']), "Script contains structured Video Shot Plan array (Bridge to Phase 06)");
assertTest(count($scriptStruct['shot_plan']) >= 5, "Shot plan contains scene-by-scene breakdown (" . count($scriptStruct['shot_plan']) . " scenes)");
assertTest(!empty($scriptStruct['shot_plan'][0]['visual_instruction']) && !empty($scriptStruct['shot_plan'][0]['voiceover']), "Scene 1 contains visual instruction and voiceover");

// -------------------------------------------------------------
// TEST 6: SEO Metadata & FAQ Generation
// -------------------------------------------------------------
echo "\n--- TEST 6: SEO Metadata Package & FAQs ---\n";
$genSeo = $contentEngine->generateContent($testProductId, 'seo_content');
assertTest($genSeo['status'] === true, "Generate SEO Metadata & FAQ package");

$seoRecord = $d->rawQueryOne("select * from #_ai_content where id = ?", array($genSeo['content_id']));
$seoStruct = json_decode($seoRecord['structured_data'], true);
assertTest(!empty($seoStruct['primary_keyword']) && !empty($seoStruct['seo_title']) && !empty($seoStruct['seo_description']), "SEO package includes primary keyword, title, and description");
assertTest(!empty($seoStruct['faqs']) && count($seoStruct['faqs']) >= 2, "SEO package includes FAQ list (" . count($seoStruct['faqs']) . " FAQs)");

// -------------------------------------------------------------
// TEST 7: Content Versioning & Zero Overwrite
// -------------------------------------------------------------
echo "\n--- TEST 7: Content Versioning & History Preservation ---\n";
$genAnalysisV2 = $contentEngine->generateContent($testProductId, 'product_analysis');
assertTest($genAnalysisV2['status'] === true, "Generate second version (v2) of Product Analysis");
assertTest($genAnalysisV2['version'] === 2, "Second generation increments to version v2");

$checkV1 = $d->rawQueryOne("select * from #_ai_content where id = ?", array($genAnalysis['content_id']));
assertTest(!empty($checkV1) && $checkV1['version'] == 1, "Original v1 record is preserved in database (Zero Overwrite)");

// -------------------------------------------------------------
// TEST 8: Human Approval & Active Version Promotion
// -------------------------------------------------------------
echo "\n--- TEST 8: Human Approval & Active Version Promotion ---\n";
$apprRes = $contentEngine->approveContent($genAnalysisV2['content_id']);
assertTest($apprRes !== false, "Approve version v2");

$checkV2After = $d->rawQueryOne("select * from #_ai_content where id = ?", array($genAnalysisV2['content_id']));
$checkV1After = $d->rawQueryOne("select * from #_ai_content where id = ?", array($genAnalysis['content_id']));
assertTest($checkV2After['status'] === 'APPROVED' && $checkV2After['is_active'] == 1, "Version v2 is now APPROVED and set to Active");
assertTest($checkV1After['is_active'] == 0, "Version v1 is deactivated");

// Approve TikTok script as well
$contentEngine->approveContent($genScript['content_id']);

// -------------------------------------------------------------
// TEST 9: Apply to Product & Reversible Content Backup
// -------------------------------------------------------------
echo "\n--- TEST 9: Apply to Product & Backup Recovery ---\n";
$applyRes = $contentEngine->applyToProduct($genAnalysisV2['content_id'], 'tester_admin');
assertTest($applyRes['status'] === true, "Apply approved Product Analysis to live table_product");

$prodAfter = $d->rawQueryOne("select * from #_product where id = ?", array($testProductId));
assertTest(!empty($prodAfter['expert_pros']) && $prodAfter['expert_pros'] !== "Đệm êm\nChắc chắn", "Product expert_pros updated with AI content");

$backups = $d->rawQuery("select * from #_product_content_backup where id_product = ?", array($testProductId));
assertTest(!empty($backups), "Previous product content backed up in table_product_content_backup (" . count($backups) . " fields backed up)");

$checkApplied = $d->rawQueryOne("select * from #_ai_content where id = ?", array($genAnalysisV2['content_id']));
assertTest($checkApplied['status'] === 'APPLIED', "AI content status updated to APPLIED");

// -------------------------------------------------------------
// TEST 10: Outdated Content Detection
// -------------------------------------------------------------
echo "\n--- TEST 10: Outdated Content Detection ---\n";
// Update research problem_solved to simulate changed input facts
$d->rawQuery("update #_product_research set problem_solved = 'Thông tin bài toán thay đổi hoàn toàn mới' where id = ?", array($testResearchId));
$outdatedCount = $contentEngine->checkOutdatedContent($testProductId);
assertTest($outdatedCount > 0, "Detect changed input facts and mark " . $outdatedCount . " content records as OUTDATED");

$checkOutdated = $d->rawQueryOne("select is_outdated from #_ai_content where id = ?", array($genAnalysisV2['content_id']));
assertTest($checkOutdated['is_outdated'] == 1, "Content record flagged with is_outdated = 1");

// -------------------------------------------------------------
// TEST 11: Content Ready & SEO Ready Logic
// -------------------------------------------------------------
echo "\n--- TEST 11: Content Ready & SEO Ready State Logic ---\n";
$isContentReady = $contentEngine->isProductContentReady($testProductId);
assertTest($isContentReady === true, "Product meets CONTENT_READY criteria (Approved Analysis + Approved TikTok Script)");

// -------------------------------------------------------------
// TEST 12: Background Content Job Queue Lifecycle
// -------------------------------------------------------------
echo "\n--- TEST 12: Background Content Job Queue ---\n";
$jobId = $contentQueue->createJob($testProductId, array('faq'), array('tone' => 'EDUCATIONAL'));
assertTest($jobId > 0, "Dispatch Content Job to Queue (Job ID: #{$jobId})");

$pendingJob = $contentQueue->getNextPendingJob();
assertTest(!empty($pendingJob) && $pendingJob['id'] == $jobId, "Pick and lock Pending Content Job");
assertTest($pendingJob['status'] === 'RUNNING', "Job transitioned to RUNNING state");

$execJobRes = $contentQueue->executeJob($jobId);
assertTest($execJobRes['status'] === true, "Background worker executes content job successfully");

$jobFinal = $d->rawQueryOne("select * from #_ai_content_job where id = ?", array($jobId));
assertTest($jobFinal['status'] === 'SUCCESS', "Job final status is SUCCESS");
assertTest($jobFinal['duration'] >= 0, "Job execution duration tracked (" . $jobFinal['duration'] . "s)");

echo "\n=======================================================\n";
echo "PHASE 05 TEST RESULTS: {$passCount} PASSED, {$failCount} FAILED\n";
echo "=======================================================\n";

if ($failCount === 0) {
    echo ">>> ALL PHASE 05 AUTOMATED TESTS PASSED SUCCESSFULLY! <<<\n";
} else {
    echo ">>> SOME TESTS FAILED! PLEASE REVIEW OUTPUT. <<<\n";
}
