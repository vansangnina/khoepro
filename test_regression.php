<?php
/**
 * Regression Test Suite for Phase 01 & Phase 02
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

echo "=======================================================\n";
echo "FITNADO PHASE 01 & PHASE 02 REGRESSION VERIFICATION\n";
echo "=======================================================\n\n";

$pass = 0;
$fail = 0;

function check($cond, $title, $err = '') {
    global $pass, $fail;
    if ($cond) {
        echo "[PASS] $title\n";
        $pass++;
    } else {
        echo "[FAIL] $title: $err\n";
        $fail++;
    }
}

// 1. Phase 01: Product Categories & Live Products in table_product
$cats = $d->rawQuery("select id, namevi from #_product_list where find_in_set('hienthi',status)");
check(!empty($cats), "Phase 01: Category taxonomy loaded (" . count($cats) . " active categories)");

$prods = $d->rawQuery("select id, namevi, slugvi, regular_price, status from #_product where find_in_set('hienthi',status) limit 0,5");
check(!empty($prods), "Phase 01: Live products loaded (" . count($prods) . " samples)");

// 2. Phase 02: Affiliate Offers & Comparisons
$affOffers = $d->rawQuery("select a.*, p.namevi from #_product_affiliate a inner join #_product p on a.id_product = p.id where find_in_set('hienthi',a.status) limit 0,5");
check(!empty($affOffers), "Phase 02: Affiliate offers linked to active products (" . count($affOffers) . " offers verified)");

// 3. Phase 02: Click Tracking Table
$clickCount = $d->rawQueryOne("select count(id) as total from #_affiliate_click");
check(isset($clickCount['total']), "Phase 02: Affiliate click tracking table intact (" . $clickCount['total'] . " clicks logged)");

// 4. Phase 03: Product Research Candidate Table
$candCount = $d->rawQueryOne("select count(id) as total from #_product_research");
check(!empty($candCount['total']) && $candCount['total'] >= 3, "Phase 03: Research candidate pipeline table active (" . $candCount['total'] . " candidates)");

echo "\n=======================================================\n";
echo "REGRESSION RESULTS: $pass PASSED, $fail FAILED\n";
echo "=======================================================\n";
