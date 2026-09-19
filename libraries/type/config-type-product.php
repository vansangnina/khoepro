<?php
require_once LIBRARIES . 'config-affiliate.php';

/* Sản phẩm */
$nametype = "san-pham";
$config['product'][$nametype]['title_main'] = sanpham;
$config['product'][$nametype]['dropdown'] = true;
$config['product'][$nametype]['list'] = true;
$config['product'][$nametype]['cat'] = true;
$config['product'][$nametype]['item'] = true;
$config['product'][$nametype]['sub'] = true;
$config['product'][$nametype]['brand'] = true;
$config['product'][$nametype]['color'] = true;
$config['product'][$nametype]['size'] = true;
$config['product'][$nametype]['tags'] = true;
$config['product'][$nametype]['import'] = false;
$config['product'][$nametype]['export'] = true;
$config['product'][$nametype]['view'] = true;
$config['product'][$nametype]['copy'] = true;
$config['product'][$nametype]['copy_image'] = true;
$config['product'][$nametype]['comment'] = true;
$config['product'][$nametype]['affiliate'] = true;
$config['product'][$nametype]['pros_cons'] = true;
$config['product'][$nametype]['specifications'] = true;
$config['product'][$nametype]['review_meta'] = true;
$config['product'][$nametype]['slug'] = true;
$config['product'][$nametype]['check'] = array("noibat" => noibat, "hienthi" => hienthi);
$config['product'][$nametype]['images'] = true;
$config['product'][$nametype]['icon'] = true;
$config['product'][$nametype]['show_images'] = true;
$config['product'][$nametype]['gallery'] = array(
    $nametype => array(
        "title_main_photo" => hinhanhsanpham,
        "title_sub_photo" => hinhanh,
        "check_photo" => array("hienthi" => hienthi),
        "number_photo" => 3,
        "images_photo" => true,
        "cart_photo" => true,
        "avatar_photo" => true,
        "name_photo" => true,
        "width_photo" => 540,
        "height_photo" => 540,
        "thumb_photo" => '100x100x1',
        "img_type_photo" => '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP'
    ),
    "video" => array(
        "title_main_photo" => videosanpham,
        "title_sub_photo" => "Video",
        "check_photo" => array("hienthi" => hienthi),
        "number_photo" => 2,
        "video_photo" => true,
        "name_photo" => true
    ),
    "taptin" => array(
        "title_main_photo" => taptinsanpham,
        "title_sub_photo" => taptin,
        "check_photo" => array("hienthi" => hienthi),
        "number_photo" => 2,
        "file_photo" => true,
        "name_photo" => true,
        "file_type_photo" => '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.xls|.xlsx|.jpg|.png|.gif|.webp|.WEBP'
    )
);
$config['product'][$nametype]['code'] = true;
$config['product'][$nametype]['regular_price'] = true;
$config['product'][$nametype]['sale_price'] = true;
$config['product'][$nametype]['discount'] = true;
$config['product'][$nametype]['desc'] = true;
$config['product'][$nametype]['desc_cke'] = true;
$config['product'][$nametype]['content'] = true;
$config['product'][$nametype]['content_cke'] = true;
$config['product'][$nametype]['schema'] = true;
$config['product'][$nametype]['seo'] = true;
$config['product'][$nametype]['width'] = 270;
$config['product'][$nametype]['height'] = 270;
$config['product'][$nametype]['thumb'] = '100x100x1';
$config['product'][$nametype]['width_icon'] = 30;
$config['product'][$nametype]['height_icon'] = 30;
$config['product'][$nametype]['thumb_icon'] = '100x100x1';
$config['product'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Sản phẩm (Size) */
$config['product'][$nametype]['check_size'] = array("hienthi" => hienthi);

/* Sản phẩm (Color) */
$config['product'][$nametype]['check_color'] = array("hienthi" => hienthi);
$config['product'][$nametype]['color_images'] = true;
$config['product'][$nametype]['color_code'] = true;
$config['product'][$nametype]['color_type'] = true;
$config['product'][$nametype]['width_color'] = 30;
$config['product'][$nametype]['height_color'] = 30;
$config['product'][$nametype]['thumb_color'] = '100x100x1';
$config['product'][$nametype]['img_type_color'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Sản phẩm (List) */
$config['product'][$nametype]['title_main_list'] = danhmuccap1;
$config['product'][$nametype]['images_list'] = true;
$config['product'][$nametype]['show_images_list'] = true;
$config['product'][$nametype]['slug_list'] = true;
$config['product'][$nametype]['check_list'] = array("noibat" => noibat, "hienthi" => hienthi);
$config['product'][$nametype]['gallery_list'] = array(
    $nametype => array(
        "title_main_photo" => hinhanhsanphamcap1,
        "title_sub_photo" => hinhanh,
        "check_photo" => array("hienthi" => hienthi),
        "number_photo" => 2,
        "images_photo" => true,
        "avatar_photo" => true,
        "name_photo" => true,
        "width_photo" => 300,
        "height_photo" => 200,
        "thumb_photo" => '100x100x1',
        "img_type_photo" => '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP',
    ),
    "video" => array(
        "title_main_photo" => videosanphamcap1,
        "title_sub_photo" => "Video",
        "check_photo" => array("hienthi" => hienthi),
        "number_photo" => 2,
        "video_photo" => true,
        "name_photo" => true
    )
);
$config['product'][$nametype]['desc_list'] = true;
$config['product'][$nametype]['seo_list'] = true;
$config['product'][$nametype]['width_list'] = 300;
$config['product'][$nametype]['height_list'] = 200;
$config['product'][$nametype]['thumb_list'] = '100x100x1';
$config['product'][$nametype]['img_type_list'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Sản phẩm (Cat) */
$config['product'][$nametype]['title_main_cat'] = danhmuccap2;
$config['product'][$nametype]['images_cat'] = true;
$config['product'][$nametype]['show_images_cat'] = true;
$config['product'][$nametype]['slug_cat'] = true;
$config['product'][$nametype]['check_cat'] = array("noibat" => noibat, "hienthi" => hienthi);
$config['product'][$nametype]['desc_cat'] = true;
$config['product'][$nametype]['seo_cat'] = true;
$config['product'][$nametype]['width_cat'] = 300;
$config['product'][$nametype]['height_cat'] = 200;
$config['product'][$nametype]['thumb_cat'] = '100x100x1';
$config['product'][$nametype]['img_type_cat'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Sản phẩm (Item) */
$config['product'][$nametype]['title_main_item'] = danhmuccap3;
$config['product'][$nametype]['images_item'] = true;
$config['product'][$nametype]['show_images_item'] = true;
$config['product'][$nametype]['slug_item'] = true;
$config['product'][$nametype]['check_item'] = array("noibat" => noibat, "hienthi" => hienthi);
$config['product'][$nametype]['desc_item'] = true;
$config['product'][$nametype]['seo_item'] = true;
$config['product'][$nametype]['width_item'] = 300;
$config['product'][$nametype]['height_item'] = 200;
$config['product'][$nametype]['thumb_item'] = '100x100x1';
$config['product'][$nametype]['img_type_item'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Sản phẩm (Sub) */
$config['product'][$nametype]['title_main_sub'] = danhmuccap4;
$config['product'][$nametype]['images_sub'] = true;
$config['product'][$nametype]['show_images_sub'] = true;
$config['product'][$nametype]['slug_sub'] = true;
$config['product'][$nametype]['check_sub'] = array("noibat" => noibat, "hienthi" => hienthi);
$config['product'][$nametype]['desc_sub'] = true;
$config['product'][$nametype]['seo_sub'] = true;
$config['product'][$nametype]['width_sub'] = 300;
$config['product'][$nametype]['height_sub'] = 200;
$config['product'][$nametype]['thumb_sub'] = '100x100x1';
$config['product'][$nametype]['img_type_sub'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Sản phẩm (Hãng) */
$config['product'][$nametype]['title_main_brand'] = danhmuchang;
$config['product'][$nametype]['images_brand'] = true;
$config['product'][$nametype]['show_images_brand'] = true;
$config['product'][$nametype]['slug_brand'] = true;
$config['product'][$nametype]['check_brand'] = array("noibat" => noibat, "hienthi" => hienthi);
$config['product'][$nametype]['seo_brand'] = true;
$config['product'][$nametype]['width_brand'] = 150;
$config['product'][$nametype]['height_brand'] = 150;
$config['product'][$nametype]['thumb_brand'] = '100x100x1';
$config['product'][$nametype]['img_type_brand'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Thư viện ảnh */
$nametype = "thu-vien-anh";
$config['product'][$nametype]['title_main'] = thuvienanh;
$config['product'][$nametype]['check'] = array("hienthi" => hienthi);
$config['product'][$nametype]['view'] = true;
$config['product'][$nametype]['copy'] = true;
$config['product'][$nametype]['slug'] = true;
$config['product'][$nametype]['images'] = true;
$config['product'][$nametype]['show_images'] = true;
$config['product'][$nametype]['gallery'] = array(
    $nametype => array(
        "title_main_photo" => hinhanhthuvienanh,
        "title_sub_photo" => hinhanh,
        "check_photo" => array("hienthi" => hienthi),
        "number_photo" => 2,
        "images_photo" => true,
        "avatar_photo" => true,
        "name_photo" => true,
        "width_photo" => 540,
        "height_photo" => 540,
        "thumb_photo" => '100x100x1',
        "img_type_photo" => '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP'
    )
);
$config['product'][$nametype]['seo'] = true;
$config['product'][$nametype]['width'] = 270;
$config['product'][$nametype]['height'] = 270;
$config['product'][$nametype]['thumb'] = '100x100x1';
$config['product'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';
