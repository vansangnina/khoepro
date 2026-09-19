<?php
// /* Background */
// $nametype = "background";
// $config['photo']['photo_static'][$nametype]['title_main'] = "Background";
// $config['photo']['photo_static'][$nametype]['check'] = array("hienthi" => hienthi);
// $config['photo']['photo_static'][$nametype]['images'] = true;
// $config['photo']['photo_static'][$nametype]['background'] = true;
// $config['photo']['photo_static'][$nametype]['width'] = 900;
// $config['photo']['photo_static'][$nametype]['height'] = 300;
// $config['photo']['photo_static'][$nametype]['thumb'] = '900x300x1';
// $config['photo']['photo_static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';


/* Banner */
$nametype = "banner";
$config['photo']['photo_static'][$nametype]['title_main'] = "Banner";
$config['photo']['photo_static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['photo']['photo_static'][$nametype]['images'] = true;
$config['photo']['photo_static'][$nametype]['width'] = 730;
$config['photo']['photo_static'][$nametype]['height'] = 120;
$config['photo']['photo_static'][$nametype]['thumb'] = '730x120x1';
$config['photo']['photo_static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Logo */
$nametype = "logo";
$config['photo']['photo_static'][$nametype]['title_main'] = "Logo";
$config['photo']['photo_static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['photo']['photo_static'][$nametype]['images'] = true;
$config['photo']['photo_static'][$nametype]['width'] = 120;
$config['photo']['photo_static'][$nametype]['height'] = 100;
$config['photo']['photo_static'][$nametype]['thumb'] = '120x100x1';
$config['photo']['photo_static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Favicon */
$nametype = "favicon";
$config['photo']['photo_static'][$nametype]['title_main'] = "Favicon";
$config['photo']['photo_static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['photo']['photo_static'][$nametype]['images'] = true;
$config['photo']['photo_static'][$nametype]['width'] = 48;
$config['photo']['photo_static'][$nametype]['height'] = 48;
$config['photo']['photo_static'][$nametype]['thumb'] = '48x48x1';
$config['photo']['photo_static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Watermark */
$nametype = "watermark";
$config['photo']['photo_static'][$nametype]['title_main'] = "Watermark";
$config['photo']['photo_static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['photo']['photo_static'][$nametype]['images'] = true;
$config['photo']['photo_static'][$nametype]['watermark'] = true;
$config['photo']['photo_static'][$nametype]['watermark-advanced'] = true;
$config['photo']['photo_static'][$nametype]['width'] = 100;
$config['photo']['photo_static'][$nametype]['height'] = 70;
$config['photo']['photo_static'][$nametype]['thumb'] = '100x70x1';
$config['photo']['photo_static'][$nametype]['img_type'] = '.png|.PNG|.Png';

/* Watermark tin tức */
$nametype = "watermark-news";
$config['photo']['photo_static'][$nametype]['title_main'] = watermarktintuc;
$config['photo']['photo_static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['photo']['photo_static'][$nametype]['images'] = true;
$config['photo']['photo_static'][$nametype]['watermark'] = true;
$config['photo']['photo_static'][$nametype]['watermark-advanced'] = true;
$config['photo']['photo_static'][$nametype]['width'] = 50;
$config['photo']['photo_static'][$nametype]['height'] = 50;
$config['photo']['photo_static'][$nametype]['thumb'] = '50x50x1';
$config['photo']['photo_static'][$nametype]['img_type'] = '.png|.PNG|.Png';

/* Video */
$nametype = "video";
$config['photo']['photo_static'][$nametype]['title_main'] = "Video";
$config['photo']['photo_static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['photo']['photo_static'][$nametype]['images'] = true;
$config['photo']['photo_static'][$nametype]['video'] = true;
$config['photo']['photo_static'][$nametype]['name'] = true;
$config['photo']['photo_static'][$nametype]['desc'] = true;
$config['photo']['photo_static'][$nametype]['content'] = true;
$config['photo']['photo_static'][$nametype]['width'] = 250;
$config['photo']['photo_static'][$nametype]['height'] = 150;
$config['photo']['photo_static'][$nametype]['thumb'] = '250x150x1';
$config['photo']['photo_static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Popup */
$nametype = "popup";
$config['photo']['photo_static'][$nametype]['title_main'] = "Popup";
$config['photo']['photo_static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['photo']['photo_static'][$nametype]['images'] = true;
$config['photo']['photo_static'][$nametype]['name'] = true;
$config['photo']['photo_static'][$nametype]['link'] = true;
$config['photo']['photo_static'][$nametype]['width'] = 800;
$config['photo']['photo_static'][$nametype]['height'] = 530;
$config['photo']['photo_static'][$nametype]['thumb'] = '800x530x1';
$config['photo']['photo_static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Slideshow */
$nametype = "slide";
$config['photo']['man_photo'][$nametype]['title_main_photo'] = "Slideshow";
$config['photo']['man_photo'][$nametype]['check_photo'] = array("hienthi" => hienthi);
$config['photo']['man_photo'][$nametype]['number_photo'] = 2;
$config['photo']['man_photo'][$nametype]['images_photo'] = true;
$config['photo']['man_photo'][$nametype]['avatar_photo'] = true;
$config['photo']['man_photo'][$nametype]['link_photo'] = true;
$config['photo']['man_photo'][$nametype]['name_photo'] = true;
$config['photo']['man_photo'][$nametype]['desc_photo'] = true;
$config['photo']['man_photo'][$nametype]['width_photo'] = 1366;
$config['photo']['man_photo'][$nametype]['height_photo'] = 600;
$config['photo']['man_photo'][$nametype]['thumb_photo'] = '200x100x1';
$config['photo']['man_photo'][$nametype]['img_type_photo'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Social */
$nametype = "social";
$config['photo']['man_photo'][$nametype]['title_main_photo'] = "Social";
$config['photo']['man_photo'][$nametype]['check_photo'] = array("hienthi" => hienthi);
$config['photo']['man_photo'][$nametype]['number_photo'] = 2;
$config['photo']['man_photo'][$nametype]['images_photo'] = true;
$config['photo']['man_photo'][$nametype]['avatar_photo'] = true;
$config['photo']['man_photo'][$nametype]['link_photo'] = true;
$config['photo']['man_photo'][$nametype]['width_photo'] = 30;
$config['photo']['man_photo'][$nametype]['height_photo'] = 30;
$config['photo']['man_photo'][$nametype]['thumb_photo'] = '30x30x2';
$config['photo']['man_photo'][$nametype]['img_type_photo'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Video */
$nametype = "video";
$config['photo']['man_photo'][$nametype]['title_main_photo'] = "Video";
$config['photo']['man_photo'][$nametype]['check_photo'] = array("noibat" => "Nổi bật", "hienthi" => hienthi);
$config['photo']['man_photo'][$nametype]['number_photo'] = 2;
$config['photo']['man_photo'][$nametype]['video_photo'] = true;
$config['photo']['man_photo'][$nametype]['name_photo'] = true;

/* Đối tác */
$nametype = "doitac";
$config['photo']['man_photo'][$nametype]['title_main_photo'] = doitac;
$config['photo']['man_photo'][$nametype]['check_photo'] = array("hienthi" => hienthi);
$config['photo']['man_photo'][$nametype]['number_photo'] = 5;
$config['photo']['man_photo'][$nametype]['images_photo'] = true;
$config['photo']['man_photo'][$nametype]['avatar_photo'] = true;
$config['photo']['man_photo'][$nametype]['link_photo'] = true;
$config['photo']['man_photo'][$nametype]['name_photo'] = true;
$config['photo']['man_photo'][$nametype]['width_photo'] = 175;
$config['photo']['man_photo'][$nametype]['height_photo'] = 95;
$config['photo']['man_photo'][$nametype]['thumb_photo'] = '175x95x2';
$config['photo']['man_photo'][$nametype]['img_type_photo'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

