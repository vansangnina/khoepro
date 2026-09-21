<!-- Basehref -->
<base href="<?= $configBase ?>" />

<!-- UTF-8 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Title, Keywords, Description -->
<title><?= $seo->get('title') ?></title>
<meta name="keywords" content="<?= $seo->get('keywords') ?>" />
<meta name="description" content="<?= $seo->get('description') ?>" />

<!-- Robots -->
<?php /* <meta name="robots" content="<?=(in_array($source,$config['website']['noseo']))?'noindex,nofollow':'index,follow,noodp'?>" /> */?>
<meta name="robots" content="<?php if($source=='search' or $source=='user' or $source=='order') { echo 'nofollow, noindex'; } elseif($metaseo!='') { echo $metaseo; } else { echo 'index,follow'; } ?>" />
<!-- Favicon -->
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />

<!-- Google Fonts: Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

<!-- Webmaster Tool -->
<?= $func->decodeHtmlChars($setting['mastertool']) ?>

<!-- GEO -->
<meta name="geo.region" content="VN" />
<meta name="geo.placename" content="Hồ Chí Minh" />
<meta name="geo.position" content="10.823099;106.629664" />
<meta name="ICBM" content="10.823099, 106.629664" />

<!-- Author - Copyright -->
<meta name='revisit-after' content='1 days' />
<meta name="author" content="<?= $setting['name' . $lang] ?>" />
<meta name="copyright" content="<?= $setting['name' . $lang] . " - [" . $optsetting['email'] . "]" ?>" />

<!-- Facebook -->
<meta property="og:type" content="<?= $seo->get('type') ?>" />
<meta property="og:site_name" content="<?= $setting['name' . $lang] ?>" />
<meta property="og:title" content="<?= $seo->get('title') ?>" />
<meta property="og:description" content="<?= $seo->get('description') ?>" />
<meta property="og:url" content="<?= $seo->get('url') ?>" />
<meta property="og:image" content="<?= $seo->get('photo') ?>" />
<meta property="og:image:alt" content="<?= $seo->get('title') ?>" />
<meta property="og:image:type" content="<?= $seo->get('photo:type') ?>" />
<meta property="og:image:width" content="<?= $seo->get('photo:width') ?>" />
<meta property="og:image:height" content="<?= $seo->get('photo:height') ?>" />

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="<?= $optsetting['email'] ?>" />
<meta name="twitter:creator" content="<?= $setting['name' . $lang] ?>" />
<meta name="twitter:url" content="<?= $seo->get('url') ?>" />
<meta name="twitter:title" content="<?= $seo->get('title') ?>" />
<meta name="twitter:description" content="<?= $seo->get('description') ?>" />
<meta name="twitter:image" content="<?= $seo->get('photo') ?>" />

<!-- Canonical -->
<link rel="canonical" href="<?= $func->getCurrentPageURL() ?>" />

<!-- Chống đổi màu trên IOS -->
<meta name="format-detection" content="telephone=no">

<!-- Viewport -->
<meta name="viewport" content="width=device-width, initial-scale=1">