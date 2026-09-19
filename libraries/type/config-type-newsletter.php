<?php
/* Đăng ký nhận tin */
$nametype = "dangkynhantin";
$config['newsletter'][$nametype]['title_main'] = dangkynhantin;
$config['newsletter'][$nametype]['file'] = true;
$config['newsletter'][$nametype]['email'] = true;
$config['newsletter'][$nametype]['is_send'] = true;
$config['newsletter'][$nametype]['fullname'] = true;
$config['newsletter'][$nametype]['phone'] = true;
$config['newsletter'][$nametype]['address'] = true;
$config['newsletter'][$nametype]['subject'] = true;
$config['newsletter'][$nametype]['content'] = true;
$config['newsletter'][$nametype]['notes'] = true;
$config['newsletter'][$nametype]['confirm_status'] = array("1" => daxem, "2" => dalienhe, "3" => dathongbao);
$config['newsletter'][$nametype]['show_name'] = true;
$config['newsletter'][$nametype]['show_phone'] = true;
$config['newsletter'][$nametype]['show_date'] = true;
$config['newsletter'][$nametype]['file_type'] = '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.xls|.xlsx|.jpg|.png|.gif|.webp|.WEBP';
