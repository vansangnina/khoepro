<?php
if (!defined('SOURCES')) die("Error");
/* static */
$popup = $cache->get("select name$lang, photo, link from #_photo where type = ? and act = ? and find_in_set('hienthi',status) limit 0,1", array('popup', 'photo_static'), 'fetch', 7200);

/* multi */
$slider = $cache->get("select name$lang, desc$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('slide'), 'result', 7200);
$brand = $cache->get("select name$lang, slugvi, slugen, id, photo from #_product_brand where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('san-pham'), 'result', 7200);
$productHot = $d->rawQuery("select id, name$lang, slugvi, slugen, photo, regular_price, sale_price, discount, code, desc$lang from #_product where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) order by numb,id desc", array('san-pham'));
if (empty($productHot)) {
    $productHot = $d->rawQuery("select id, name$lang, slugvi, slugen, photo, regular_price, sale_price, discount, code, desc$lang from #_product where type = ? and find_in_set('hienthi',status) order by numb,id desc limit 0,10", array('san-pham'));
}
$proListHot = $d->rawQuery("select name$lang, slugvi, slugen, id, photo from #_product_list where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('san-pham'));
$newsHot = $d->rawQuery("select name$lang, slugvi, slugen, desc$lang, date_created, id, photo from #_news where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) order by numb,id desc limit 0,16", array('tin-tuc'));
if (empty($newsHot)) {
    $newsHot = $d->rawQuery("select name$lang, slugvi, slugen, desc$lang, date_created, id, photo from #_news where type = ? and find_in_set('hienthi',status) order by numb,id desc limit 0,16", array('tin-tuc'));
}
$videoHot = $d->rawQuery("select id, name$lang, link_video, photo from #_photo where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('video'));
$partner = $d->rawQuery("select name$lang, link, photo from #_photo where type = ? and find_in_set('hienthi',status) order by numb, id desc", array('doitac'));
$countProduct = $d->rawQueryOne("select count(id) as total from #_product where type = ? and find_in_set('hienthi',status)", array('san-pham'));
$countNews = $d->rawQueryOne("select count(id) as total from #_news where type = ? and find_in_set('hienthi',status)", array('tin-tuc'));

$gioithieu = $d->rawQueryOne("select id, type, name$lang, content$lang, desc$lang, photo, date_created, date_updated, options from #_static where type = ? limit 0,1", array('gioi-thieu'));
/* SEO */
$seopage = $d->rawQueryOne("select * from #_seopage where type = ? limit 0,1", array('trang-chu'));
$seo->set('h1', $seopage['title' . $seolang]);
if (!empty($seopage['title' . $seolang])) $seo->set('title', $seopage['title' . $seolang]);
else $seo->set('title', $titleMain);
if (!empty($seopage['keywords' . $seolang])) $seo->set('keywords', $seopage['keywords' . $seolang]);
if (!empty($seopage['description' . $seolang])) $seo->set('description', $seopage['description' . $seolang]);
$seo->set('url', $func->getPageURL());
$imgJson = (!empty($seopage['options'])) ? json_decode($seopage['options'], true) : null;
if (!empty($seopage['photo'])) {
    if (empty($imgJson) || ($imgJson['p'] != $seopage['photo'])) {
        $imgJson = $func->getImgSize($seopage['photo'], UPLOAD_SEOPAGE_L . $seopage['photo']);
        $seo->updateSeoDB(json_encode($imgJson), 'seopage', $seopage['id']);
    }
    if (!empty($imgJson)) {
        $seo->set('photo', $configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . UPLOAD_SEOPAGE_L . $seopage['photo']);
        $seo->set('photo:width', $imgJson['w']);
        $seo->set('photo:height', $imgJson['h']);
        $seo->set('photo:type', $imgJson['m']);
    }
}