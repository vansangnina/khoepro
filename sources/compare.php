<?php
if (!defined('SOURCES')) die("Error");

require_once LIBRARIES . "config-affiliate.php";

$id1 = (!empty($_GET['id1'])) ? (int)$_GET['id1'] : 0;
$id2 = (!empty($_GET['id2'])) ? (int)$_GET['id2'] : 0;

/* Phân tích slug dạng so-sanh/slug1-vs-slug2 */
if (!empty($_GET['slugs'])) {
    $parts = explode('-vs-', $_GET['slugs']);
    if (count($parts) == 2) {
        $slug1 = htmlspecialchars(trim($parts[0]));
        $slug2 = htmlspecialchars(trim($parts[1]));
        $p1 = $d->rawQueryOne("select id from #_product where $sluglang = ? and type = 'san-pham' and find_in_set('hienthi', status) limit 0,1", array($slug1));
        $p2 = $d->rawQueryOne("select id from #_product where $sluglang = ? and type = 'san-pham' and find_in_set('hienthi', status) limit 0,1", array($slug2));
        if (!empty($p1['id'])) $id1 = (int)$p1['id'];
        if (!empty($p2['id'])) $id2 = (int)$p2['id'];
    }
}

/* Fallback nếu chưa chọn đủ 2 sản phẩm */
if (empty($id1) || empty($id2) || $id1 == $id2) {
    $fallbackProducts = $d->rawQuery("select id from #_product where type = 'san-pham' and find_in_set('hienthi', status) order by find_in_set('noibat', status) desc, numb, id desc limit 0,2");
    if (count($fallbackProducts) >= 2) {
        if (empty($id1)) $id1 = (int)$fallbackProducts[0]['id'];
        if (empty($id2) || $id1 == $id2) $id2 = (int)$fallbackProducts[1]['id'];
    }
}

/* Lấy danh sách sản phẩm để người dùng chọn so sánh */
$allProductsForCompare = $d->rawQuery("select id, name$lang, slugvi, slugen, sale_price, regular_price from #_product where type = 'san-pham' and find_in_set('hienthi', status) order by numb, id desc limit 0, 50");

/* Helper lấy chi tiết sản phẩm so sánh */
function getCompareProductDetail($productId)
{
    global $d, $lang, $func, $affiliate;
    if (empty($productId)) return null;

    $row = $d->rawQueryOne("select id, name$lang as name, slugvi, slugen, photo, options, code, regular_price, sale_price, discount, desc$lang as `desc`, review_score, review_count, expert_pros, expert_cons, verdict, specs, best_for, affiliate_note, pros_vi, pros_en, cons_vi, cons_en, suitable_vi, suitable_en, specifications_vi, specifications_en, review_type, is_real_test from #_product where id = ? and type = 'san-pham' and find_in_set('hienthi', status) limit 0,1", array($productId));

    if (empty($row)) return null;

    // Fill compatibility fields
    if (empty($row['pros_vi']) && !empty($row['expert_pros'])) $row['pros_vi'] = $row['expert_pros'];
    if (empty($row['cons_vi']) && !empty($row['expert_cons'])) $row['cons_vi'] = $row['expert_cons'];
    if (empty($row['suitable_vi']) && !empty($row['best_for'])) $row['suitable_vi'] = $row['best_for'];

    // Parse specs
    $parsedSpecs = !empty($row['specs']) ? json_decode($row['specs'], true) : [];
    if (is_array($parsedSpecs) && !empty($parsedSpecs)) {
        $specsHtml = "<table class='table table-sm table-bordered mb-0'>";
        foreach ($parsedSpecs as $k => $v) {
            $specsHtml .= "<tr><th style='width:40%; background:#f8fafc;'>" . htmlspecialchars($k) . "</th><td>" . htmlspecialchars($v) . "</td></tr>";
        }
        $specsHtml .= "</table>";
        $row['specifications_vi'] = $specsHtml;
    }

    /* Lấy comment rating */
    $comment = new Comments($d, $func, $row['id'], 'san-pham');
    $row['total_reviews'] = $row['review_count'] > 0 ? $row['review_count'] : $comment->total;
    $row['avg_rating'] = $row['review_score'] > 0 ? number_format($row['review_score'], 1) : $comment->avgPoint();
    $row['avg_star_percent'] = $comment->avgStar();

    /* Lấy affiliate offers */
    $row['offers'] = $affiliate->getOffersByProductId($row['id']);
    $row['best_offer'] = $affiliate->getBestOffer($row['id']);

    return $row;
}

$product1 = getCompareProductDetail($id1);
$product2 = getCompareProductDetail($id2);

/* Breadcrumbs & SEO */
$breadcr->set('so-sanh', 'So sánh sản phẩm');
if (!empty($product1) && !empty($product2)) {
    $seoTitle = "So sánh " . $product1['name'] . " vs " . $product2['name'] . " | Khỏe Pro";
    $seoDesc = "Đặt lên bàn cân so sánh chi tiết giữa " . $product1['name'] . " và " . $product2['name'] . ": Giá bán, thông số kỹ thuật, ưu nhược điểm, đánh giá thực tế và nơi mua tốt nhất.";
} else {
    $seoTitle = "So sánh sản phẩm dụng cụ tập Gym & Thể thao | Khỏe Pro";
    $seoDesc = "Công cụ so sánh trực quan thông số, tính năng và giá bán các phụ kiện, dụng cụ tập thể hình tại Khỏe Pro.";
}

$seo->set('title', $seoTitle);
$seo->set('keywords', 'so sanh san pham, khoepro compare, danh gia do tap gym');
$seo->set('description', $seoDesc);
$seo->set('url', $func->getPageURL());
$seo->set('h1', 'So sánh sản phẩm');
$breadcrumbs = $breadcr->get();
