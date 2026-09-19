<?php
/* Giới thiệu */
$nametype = "gioi-thieu";
$config['static'][$nametype]['title_main'] = gioithieu;
$config['static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['static'][$nametype]['images'] = true;
$config['static'][$nametype]['images1'] = true;
$config['static'][$nametype]['file'] = true;
$config['static'][$nametype]['name'] = true;
$config['static'][$nametype]['desc'] = true;
$config['static'][$nametype]['desc_cke'] = true;
$config['static'][$nametype]['content'] = true;
$config['static'][$nametype]['content_cke'] = true;
$config['static'][$nametype]['seo'] = true;
$config['static'][$nametype]['video'] = true;
$config['static'][$nametype]['width'] = 300;
$config['static'][$nametype]['height'] = 200;
$config['static'][$nametype]['width1'] = 300;
$config['static'][$nametype]['height1'] = 200;
$config['static'][$nametype]['gallery'] = array(
    $nametype => array(
        "title_main_photo" => hinhanh,
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
$config['static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif';
$config['static'][$nametype]['video_type'] = '.mp4';
$config['static'][$nametype]['file_type'] = '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.xls|.xlsx|.jpg|.png|.gif|.webp|.WEBP';

/* Slogan */
$nametype = "slogan";
$config['static'][$nametype]['title_main'] = "Slogan";
$config['static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['static'][$nametype]['name'] = true;

/* copyright */
$nametype = "copyright";
$config['static'][$nametype]['title_main'] = "Copyright";
$config['static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['static'][$nametype]['name'] = true;

/* Liên hệ */
$nametype = "lienhe";
$config['static'][$nametype]['title_main'] = lienhe;
$config['static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['static'][$nametype]['content'] = true;
$config['static'][$nametype]['content_cke'] = true;

/* Footer */
$nametype = "footer";
$config['static'][$nametype]['title_main'] = "Footer";
$config['static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['static'][$nametype]['name'] = true;
$config['static'][$nametype]['content'] = true;
$config['static'][$nametype]['content_cke'] = true;
