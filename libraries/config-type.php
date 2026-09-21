<?php
/* Config type - Group */
$config['group'] = array(
    groupsanpham => array(
        "product" => array("san-pham"),
        "tags" => array("san-pham"),
        "photo_static" => array("watermark"),
    ),
    grouptintuc => array(
        "news" => array("tin-tuc", "tuyen-dung"),
        "tags" => array("tin-tuc"),
        "photo_static" => array("watermark-news"),
    )
);

/* Config type - Product */
require_once LIBRARIES . 'type/config-type-product.php';

/* Config type - Tags */
require_once LIBRARIES . 'type/config-type-tags.php';

/* Config type - Newsletter */
require_once LIBRARIES . 'type/config-type-newsletter.php';

/* Config type - News */
require_once LIBRARIES . 'type/config-type-news.php';

/* Config type - Static */
require_once LIBRARIES . 'type/config-type-static.php';

/* Config type - Photo */
require_once LIBRARIES . 'type/config-type-photo.php';

/* Seo page */
$config['seopage']['page'] = array(
    "trang-chu" => "Trang chủ",
    "san-pham" => "Sản phẩm",
    "tin-tuc" => "Tin tức & Review",
    "danh-gia-review" => "Đánh giá & Review",
    "huong-dan-chon-mua" => "Hướng dẫn chọn mua",
    "so-sanh-san-pham" => "So sánh sản phẩm",
    "kien-thuc-tap-luyen" => "Kiến thức tập luyện",
    "video" => "Video 30s Review",
    "gioi-thieu" => "Giới thiệu",
    "lien-he" => "Liên hệ"
);
$config['seopage']['width'] = 1200;
$config['seopage']['height'] = 630;
$config['seopage']['thumb'] = '1200x630x1';
$config['seopage']['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Setting */
$config['setting']['address'] = true;
$config['setting']['phone'] = true;
$config['setting']['hotline'] = true;
$config['setting']['zalo'] = true;
$config['setting']['oaidzalo'] = false;
$config['setting']['email'] = true;
$config['setting']['website'] = true;
$config['setting']['fanpage'] = true;
$config['setting']['coords'] = true;
$config['setting']['coords_iframe'] = true;
$config['setting']['worktime'] = true;
$config['setting']['link_googlemaps'] = true;

/* Quản lý import */
$config['import']['images'] = true;
$config['import']['thumb'] = '100x100x1';
$config['import']['img_type'] = ".jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP";

/* Quản lý export */
$config['export']['category'] = true;

/* Quản lý tài khoản */
$config['user']['active'] = true;
$config['user']['admin'] = false;
$config['user']['check_admin'] = array("hienthi" => kichhoat);
$config['user']['member'] = true;
$config['user']['check_member'] = array("hienthi" => kichhoat);

/* Quản lý phân quyền */
$config['permission']['active'] = true;
$config['permission']['check'] = array("hienthi" => kichhoat);

/* Quản lý liên lệ */
$config['contact']['check'] = array("hienthi" => xacnhan);

/* Quản lý địa điểm */
$config['places']['active'] = true;
$config['places']['check_city'] = array("hienthi" => hienthi);
$config['places']['check_district'] = array("hienthi" => hienthi);
$config['places']['check_ward'] = array("hienthi" => hienthi);
$config['places']['placesship'] = true;

/* Quản lý giỏ hàng */
$config['order']['active'] = true;
$config['order']['search'] = true;
$config['order']['excel'] = true;
$config['order']['word'] = true;
$config['order']['excelall'] = false;
$config['order']['wordall'] = false;
$config['order']['thumb'] = '100x100x1';

/* Quản lý thông báo đẩy */
$config['onesignal'] = false;

if($config['website']['linkredirect'] == true){
    $config['photo']['man_photo']['dieuhuonglink']['title_main_photo'] = linkredirect;
    $config['photo']['man_photo']['dieuhuonglink']['check_photo'] = array("hienthi" => hienthi);
    $config['photo']['man_photo']['dieuhuonglink']['number_photo'] = 5;
    $config['photo']['man_photo']['dieuhuonglink']['loaidieuhuong_photo'] = true;
    $config['photo']['man_photo']['dieuhuonglink']['link_photo'] = true;
    $config['photo']['man_photo']['dieuhuonglink']['link2_photo'] = true;
}