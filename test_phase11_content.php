<?php
/**
 * KHOEPRO.COM — PHASE 11 CONTENT FOUNDATION QA SUITE
 * Validates 100% content integrity, taxonomy, image existence, SEO completeness, and brand fidelity.
 */

define('LIBRARIES', './libraries/');
require_once 'libraries/config.php';
require_once 'libraries/class/class.PDODb.php';

$d = new PDODb($config['database']);

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$errors = array();

function testAssert($condition, $testName, &$totalTests, &$passedTests, &$failedTests, &$errors) {
    $totalTests++;
    if ($condition) {
        $passedTests++;
        echo "  [PASS] {$testName}" . PHP_EOL;
    } else {
        $failedTests++;
        $errors[] = $testName;
        echo "  [FAIL] {$testName}" . PHP_EOL;
    }
}

echo "=================================================================" . PHP_EOL;
echo "   KHOEPRO.COM — PHASE 11 PRODUCTION CONTENT FOUNDATION QA      " . PHP_EOL;
echo "=================================================================" . PHP_EOL . PHP_EOL;

// 1. BRAND & DOMAIN CHECK
echo "--- 1. BRAND & DOMAIN CONFIGURATION ---" . PHP_EOL;
$setting = $d->rawQueryOne("SELECT * FROM #_setting WHERE id = 1");
$settingOpt = json_decode($setting['options'], true);

testAssert(strpos($setting['namevi'], 'Khỏe Pro') !== false, "Setting Brand Name contains 'Khỏe Pro'", $totalTests, $passedTests, $failedTests, $errors);
testAssert($settingOpt['website'] === 'https://khoepro.com', "Setting Website is 'https://khoepro.com'", $totalTests, $passedTests, $failedTests, $errors);
testAssert($config['video_composer']['brand_name'] === 'Khỏe Pro', "Config video_composer brand_name is 'Khỏe Pro'", $totalTests, $passedTests, $failedTests, $errors);

// 2. TAXONOMY CHECKS
echo PHP_EOL . "--- 2. TAXONOMY COMPLETENESS ---" . PHP_EOL;
$plists = $d->rawQuery("SELECT * FROM #_product_list WHERE type = 'san-pham'");
testAssert(count($plists) >= 3, "Product Lists (Cấp 1) >= 3 (Found: " . count($plists) . ")", $totalTests, $passedTests, $failedTests, $errors);

$pcats = $d->rawQuery("SELECT * FROM #_product_cat WHERE type = 'san-pham'");
testAssert(count($pcats) >= 6, "Product Categories (Cấp 2) >= 6 (Found: " . count($pcats) . ")", $totalTests, $passedTests, $failedTests, $errors);

$nlists = $d->rawQuery("SELECT * FROM #_news_list WHERE type = 'tin-tuc'");
testAssert(count($nlists) >= 4, "News Lists >= 4 (Found: " . count($nlists) . ")", $totalTests, $passedTests, $failedTests, $errors);

// 3. STATIC PAGES & POLICIES
echo PHP_EOL . "--- 3. STATIC PAGES & EDITORIAL POLICIES ---" . PHP_EOL;
$about = $d->rawQueryOne("SELECT * FROM #_static WHERE type = 'gioi-thieu'");
testAssert(!empty($about['contentvi']) && strpos($about['contentvi'], 'Khỏe Pro') !== false, "Static 'gioi-thieu' exists with rich content", $totalTests, $passedTests, $failedTests, $errors);

$contact = $d->rawQueryOne("SELECT * FROM #_static WHERE type = 'lienhe'");
testAssert(!empty($contact['contentvi']) && strpos($contact['contentvi'], 'contact@khoepro.com') !== false, "Static 'lienhe' contains official contact info", $totalTests, $passedTests, $failedTests, $errors);

$policies = $d->rawQuery("SELECT * FROM #_news WHERE type = 'chinh-sach'");
testAssert(count($policies) >= 4, "Editorial Policies >= 4 (Found: " . count($policies) . ")", $totalTests, $passedTests, $failedTests, $errors);

$polSlugs = array_map(function($p) { return $p['slugvi']; }, $policies);
testAssert(in_array('phuong-phap-danh-gia', $polSlugs), "Policy 'phuong-phap-danh-gia' exists", $totalTests, $passedTests, $failedTests, $errors);
testAssert(in_array('minh-bach-lien-ket-affiliate', $polSlugs), "Policy 'minh-bach-lien-ket-affiliate' (Affiliate Disclosure) exists", $totalTests, $passedTests, $failedTests, $errors);

// 4. REAL PRODUCTS CATALOG
echo PHP_EOL . "--- 4. REAL PRODUCTS CATALOG (Target >= 15) ---" . PHP_EOL;
$products = $d->rawQuery("SELECT * FROM #_product WHERE type = 'san-pham'");
testAssert(count($products) >= 15, "Total Products >= 15 (Found: " . count($products) . ")", $totalTests, $passedTests, $failedTests, $errors);

$proPhotoMissing = 0;
$proMissingSpecs = 0;
$proMissingProsCons = 0;
$proSlugs = array();
$duplicateSlugs = 0;

foreach ($products as $p) {
    if (in_array($p['slugvi'], $proSlugs)) {
        $duplicateSlugs++;
    }
    $proSlugs[] = $p['slugvi'];

    if (empty($p['photo']) || !file_exists("upload/product/" . $p['photo'])) {
        $proPhotoMissing++;
    }
    if (empty($p['specifications_vi'])) {
        $proMissingSpecs++;
    }
    if (empty($p['pros_vi']) || empty($p['cons_vi'])) {
        $proMissingProsCons++;
    }
}

testAssert($duplicateSlugs === 0, "Zero duplicate product slugs", $totalTests, $passedTests, $failedTests, $errors);
testAssert($proPhotoMissing === 0, "Zero broken product images (Missing: {$proPhotoMissing})", $totalTests, $passedTests, $failedTests, $errors);
testAssert($proMissingSpecs === 0, "100% products have structured specifications JSON", $totalTests, $passedTests, $failedTests, $errors);
testAssert($proMissingProsCons === 0, "100% products have pros and cons", $totalTests, $passedTests, $failedTests, $errors);

// 5. EDITORIAL REVIEWS
echo PHP_EOL . "--- 5. EDITORIAL REVIEWS (Target >= 15) ---" . PHP_EOL;
$reviewList = $d->rawQueryOne("SELECT id FROM #_news_list WHERE slugvi = 'danh-gia-review' LIMIT 1");
$reviews = $d->rawQuery("SELECT * FROM #_news WHERE id_list = ? AND type = 'tin-tuc'", array($reviewList['id']));
testAssert(count($reviews) >= 15, "Total Review Articles >= 15 (Found: " . count($reviews) . ")", $totalTests, $passedTests, $failedTests, $errors);

$revPhotoMissing = 0;
$revMissingLink = 0;
foreach ($reviews as $r) {
    if (empty($r['photo']) || !file_exists("upload/news/" . $r['photo'])) {
        $revPhotoMissing++;
    }
    if (strpos($r['contentvi'], 'san-pham/') === false) {
        $revMissingLink++;
    }
}
testAssert($revPhotoMissing === 0, "Zero broken review images (Missing: {$revPhotoMissing})", $totalTests, $passedTests, $failedTests, $errors);
testAssert($revMissingLink === 0, "100% review articles contain internal links to products", $totalTests, $passedTests, $failedTests, $errors);

// 6. BUYING GUIDES & KNOWLEDGE
echo PHP_EOL . "--- 6. BUYING GUIDES & KNOWLEDGE ARTICLES (Target >= 15) ---" . PHP_EOL;
$guideList = $d->rawQueryOne("SELECT id FROM #_news_list WHERE slugvi = 'huong-dan-chon-mua' LIMIT 1");
$knowList = $d->rawQueryOne("SELECT id FROM #_news_list WHERE slugvi = 'kien-thuc-tap-luyen' LIMIT 1");

$guides = $d->rawQuery("SELECT * FROM #_news WHERE id_list = ? AND type = 'tin-tuc'", array($guideList['id']));
$knows = $d->rawQuery("SELECT * FROM #_news WHERE id_list = ? AND type = 'tin-tuc'", array($knowList['id']));
$totalGuidesKnow = count($guides) + count($knows);

testAssert(count($guides) >= 8, "Buying Guides >= 8 (Found: " . count($guides) . ")", $totalTests, $passedTests, $failedTests, $errors);
testAssert(count($knows) >= 8, "Fitness Knowledge Articles >= 8 (Found: " . count($knows) . ")", $totalTests, $passedTests, $failedTests, $errors);
testAssert($totalGuidesKnow >= 15, "Total Guides & Knowledge >= 15 (Found: {$totalGuidesKnow})", $totalTests, $passedTests, $failedTests, $errors);

// 7. PRODUCT COMPARISONS
echo PHP_EOL . "--- 7. PRODUCT COMPARISONS (Target >= 5) ---" . PHP_EOL;
$compList = $d->rawQueryOne("SELECT id FROM #_news_list WHERE slugvi = 'so-sanh-san-pham' LIMIT 1");
$comps = $d->rawQuery("SELECT * FROM #_news WHERE id_list = ? AND type = 'tin-tuc'", array($compList['id']));
testAssert(count($comps) >= 5, "Product Comparison Articles >= 5 (Found: " . count($comps) . ")", $totalTests, $passedTests, $failedTests, $errors);

// 8. SEO METADATA COVERAGE
echo PHP_EOL . "--- 8. SEO METADATA COVERAGE ---" . PHP_EOL;
$totalSeoRecords = $d->rawQueryOne("SELECT count(id) as total FROM #_seo");
testAssert($totalSeoRecords['total'] >= 45, "Total SEO Metadata Records >= 45 (Found: " . $totalSeoRecords['total'] . ")", $totalTests, $passedTests, $failedTests, $errors);

$emptyTitles = $d->rawQuery("SELECT id FROM #_seo WHERE titlevi IS NULL OR titlevi = ''");
testAssert(count($emptyTitles) === 0, "Zero empty SEO titles across all records", $totalTests, $passedTests, $failedTests, $errors);

$emptyDescs = $d->rawQuery("SELECT id FROM #_seo WHERE descriptionvi IS NULL OR descriptionvi = ''");
testAssert(count($emptyDescs) === 0, "Zero empty SEO descriptions across all records", $totalTests, $passedTests, $failedTests, $errors);

// 9. BRAND VISUAL ASSETS
echo PHP_EOL . "--- 9. BRAND VISUAL ASSETS (Logo, Favicon, Slideshow) ---" . PHP_EOL;
$logo = $d->rawQueryOne("SELECT photo FROM #_photo WHERE type = 'logo' AND act = 'photo_static' LIMIT 1");
testAssert(!empty($logo['photo']) && file_exists("upload/photo/" . $logo['photo']), "Logo photo exists in upload/photo/", $totalTests, $passedTests, $failedTests, $errors);

$fav = $d->rawQueryOne("SELECT photo FROM #_photo WHERE type = 'favicon' AND act = 'photo_static' LIMIT 1");
testAssert(!empty($fav['photo']) && file_exists("upload/photo/" . $fav['photo']), "Favicon photo exists in upload/photo/", $totalTests, $passedTests, $failedTests, $errors);
testAssert(file_exists("favicon.ico"), "Root favicon.ico exists", $totalTests, $passedTests, $failedTests, $errors);

$slides = $d->rawQuery("SELECT photo FROM #_photo WHERE type = 'slide' AND act = 'photo_multi'");
testAssert(count($slides) >= 2, "Slideshow banners >= 2 (Found: " . count($slides) . ")", $totalTests, $passedTests, $failedTests, $errors);

// SUMMARY
echo PHP_EOL . "=================================================================" . PHP_EOL;
echo "QA RESULTS: Total: {$totalTests} | Passed: {$passedTests} | Failed: {$failedTests}" . PHP_EOL;
echo "SUCCESS RATE: " . round(($passedTests / $totalTests) * 100, 2) . "%" . PHP_EOL;
echo "=================================================================" . PHP_EOL;

if ($failedTests > 0) {
    echo PHP_EOL . "FAILURES ENCOUNTERED:" . PHP_EOL;
    foreach ($errors as $e) {
        echo "  - " . $e . PHP_EOL;
    }
    exit(1);
} else {
    echo PHP_EOL . "ALL TESTS PASSED! CONTENT FOUNDATION IS 100% PRODUCTION-READY." . PHP_EOL;
    exit(0);
}
