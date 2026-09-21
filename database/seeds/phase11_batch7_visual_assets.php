<?php
define('LIBRARIES', './libraries/');
require_once 'libraries/config.php';
require_once 'libraries/class/class.PDODb.php';

$d = new PDODb($config['database']);

echo "=== START BATCH 7: VISUAL ASSETS & HOMEPAGE CURATION ===" . PHP_EOL;

// Helper function to create stylish card images with gradients and clean typography
function createEditorialImage($filePath, $width, $height, $title, $category, $themeColor = 'dark') {
    $im = imagecreatetruecolor($width, $height);
    
    // Theme colors
    $themes = array(
        'dark' => array(array(18, 24, 38), array(30, 41, 59), array(255, 255, 255), array(59, 130, 246)),
        'gym' => array(array(15, 23, 42), array(30, 58, 138), array(255, 255, 255), array(245, 158, 11)),
        'green' => array(array(6, 78, 59), array(4, 120, 87), array(255, 255, 255), array(52, 211, 153)),
        'red' => array(array(127, 29, 29), array(185, 28, 28), array(255, 255, 255), array(252, 165, 165)),
        'purple' => array(array(88, 28, 135), array(126, 34, 206), array(255, 255, 255), array(216, 180, 254)),
        'amber' => array(array(120, 53, 15), array(180, 83, 9), array(255, 255, 255), array(253, 230, 138))
    );

    $theme = isset($themes[$themeColor]) ? $themes[$themeColor] : $themes['dark'];
    $bgStart = $theme[0];
    $bgEnd = $theme[1];

    // Create subtle vertical gradient
    for ($y = 0; $y < $height; $y++) {
        $r = (int)($bgStart[0] + ($bgEnd[0] - $bgStart[0]) * ($y / $height));
        $g = (int)($bgStart[1] + ($bgEnd[1] - $bgStart[1]) * ($y / $height));
        $b = (int)($bgStart[2] + ($bgEnd[2] - $bgStart[2]) * ($y / $height));
        $color = imagecolorallocate($im, $r, $g, $b);
        imageline($im, 0, $y, $width, $y, $color);
    }

    // Border / Accent glow
    $accentColor = imagecolorallocate($im, $theme[3][0], $theme[3][1], $theme[3][2]);
    $textColor = imagecolorallocate($im, $theme[2][0], $theme[2][1], $theme[2][2]);
    $subColor = imagecolorallocate($im, 148, 163, 184);
    $badgeBg = imagecolorallocatealpha($im, $theme[3][0], $theme[3][1], $theme[3][2], 80);

    // Decorative geometric accents
    imagefilledrectangle($im, 20, 20, $width - 20, 24, $accentColor);
    imagefilledellipse($im, (int)($width * 0.85), (int)($height * 0.25), 180, 180, $badgeBg);

    // Brand mark top left
    imagestring($im, 5, 30, 40, "KHOE PRO | " . mb_strtoupper($category, 'UTF-8'), $accentColor);
    
    // Title word wrapping (simple 5-font builtin)
    $words = explode(' ', $title);
    $lines = array();
    $currentLine = '';
    $maxCharsPerLine = (int)($width / 11);

    foreach ($words as $w) {
        if (strlen($currentLine . ' ' . $w) <= $maxCharsPerLine) {
            $currentLine .= ($currentLine ? ' ' : '') . $w;
        } else {
            if ($currentLine) $lines[] = $currentLine;
            $currentLine = $w;
        }
    }
    if ($currentLine) $lines[] = $currentLine;

    $startY = (int)($height * 0.45);
    foreach ($lines as $i => $line) {
        if ($i > 3) break;
        imagestring($im, 5, 30, $startY + ($i * 26), $line, $textColor);
    }

    // Footer mark
    imagestring($im, 3, 30, $height - 40, "khoepro.com  *  Chuan Thong So & Khach Quan", $subColor);

    // Save image
    $dir = dirname($filePath);
    if (!file_exists($dir)) mkdir($dir, 0755, true);

    imagejpeg($im, $filePath, 90);
    imagedestroy($im);
}

// 1. Generate Product Images
$products = $d->rawQuery("SELECT id, namevi, photo, code, id_cat FROM #_product WHERE type = 'san-pham'");
$themeCycle = array('gym', 'dark', 'green', 'amber', 'purple', 'red');

foreach ($products as $idx => $p) {
    if (!empty($p['photo'])) {
        $filePath = "upload/product/" . $p['photo'];
        $theme = $themeCycle[$idx % count($themeCycle)];
        createEditorialImage($filePath, 540, 540, $p['namevi'], $p['code'] ? $p['code'] : 'SẢN PHẨM', $theme);
        echo "  - Generated Product image: {$filePath}" . PHP_EOL;
    }
}

// 2. Generate News & Review & Guide & Comparison Images
$news = $d->rawQuery("SELECT id, namevi, photo, id_list FROM #_news WHERE type = 'tin-tuc'");
foreach ($news as $idx => $n) {
    if (!empty($n['photo'])) {
        $filePath = "upload/news/" . $n['photo'];
        $theme = $themeCycle[($idx + 2) % count($themeCycle)];
        createEditorialImage($filePath, 540, 360, $n['namevi'], 'KHỎE PRO EDITORIAL', $theme);
        echo "  - Generated News image: {$filePath}" . PHP_EOL;
    }
}

// 3. Generate Brand Assets: Logo & Favicon
$logoPath = "upload/photo/logo-khoepro.png";
$imLogo = imagecreatetruecolor(180, 60);
imagealphablending($imLogo, false);
imagesavealpha($imLogo, true);
$transparent = imagecolorallocatealpha($imLogo, 0, 0, 0, 127);
imagefilledrectangle($imLogo, 0, 0, 180, 60, $transparent);

$primaryColor = imagecolorallocate($imLogo, 16, 185, 129); // Emerald 500
$whiteColor = imagecolorallocate($imLogo, 255, 255, 255);
$subColor = imagecolorallocate($imLogo, 148, 163, 184);

// Draw stylized logo text
imagestring($imLogo, 5, 10, 12, "KHOE PRO", $primaryColor);
imagestring($imLogo, 2, 10, 36, "FITNESS & GEAR", $subColor);
imagepng($imLogo, $logoPath);
imagedestroy($imLogo);
echo "✓ Generated Logo: {$logoPath}" . PHP_EOL;

// Favicon
$favPath = "upload/photo/favicon-khoepro.png";
$imFav = imagecreatetruecolor(48, 48);
$bgFav = imagecolorallocate($imFav, 15, 23, 42);
$textFav = imagecolorallocate($imFav, 16, 185, 129);
imagefilledrectangle($imFav, 0, 0, 48, 48, $bgFav);
imagestring($imFav, 5, 12, 16, "KP", $textFav);
imagepng($imFav, $favPath);
imagepng($imFav, "favicon.ico");
imagedestroy($imFav);
echo "✓ Generated Favicon: {$favPath} and favicon.ico" . PHP_EOL;

// 4. Generate Slideshow Banners (1366 x 600)
$slide1Path = "upload/photo/slide-khoepro-1.jpg";
createEditorialImage($slide1Path, 1366, 600, "KHOE PRO - NEN TANG DANH GIA & SO SANH DUNG CU GYM", "TOP 2026", "gym");

$slide2Path = "upload/photo/slide-khoepro-2.jpg";
createEditorialImage($slide2Path, 1366, 600, "LUA CHON THONG MINH HON. TAP LUYEN AN TOAN VA KHOE HON.", "EDITORIAL REVIEWS", "dark");
echo "✓ Generated Slideshow banners: {$slide1Path} & {$slide2Path}" . PHP_EOL;

// 5. Update table_photo for logo, favicon, slide
$now = time();

// Logo
$existLogo = $d->rawQueryOne("SELECT id FROM #_photo WHERE type = 'logo' AND act = 'photo_static' LIMIT 1");
if (!empty($existLogo['id'])) {
    $d->rawQuery("UPDATE #_photo SET photo = 'logo-khoepro.png', status = 'hienthi', date_updated = ? WHERE id = ?", array($now, $existLogo['id']));
} else {
    $d->rawQuery("INSERT INTO #_photo (photo, namevi, type, act, status, date_created) VALUES ('logo-khoepro.png', 'Logo Khỏe Pro', 'logo', 'photo_static', 'hienthi', ?)", array($now));
}

// Favicon
$existFav = $d->rawQueryOne("SELECT id FROM #_photo WHERE type = 'favicon' AND act = 'photo_static' LIMIT 1");
if (!empty($existFav['id'])) {
    $d->rawQuery("UPDATE #_photo SET photo = 'favicon-khoepro.png', status = 'hienthi', date_updated = ? WHERE id = ?", array($now, $existFav['id']));
} else {
    $d->rawQuery("INSERT INTO #_photo (photo, namevi, type, act, status, date_created) VALUES ('favicon-khoepro.png', 'Favicon Khỏe Pro', 'favicon', 'photo_static', 'hienthi', ?)", array($now));
}

// Slideshow
$d->rawQuery("DELETE FROM #_photo WHERE type = 'slide' AND act = 'photo_multi'");
$d->rawQuery("INSERT INTO #_photo (photo, namevi, descvi, link, type, act, numb, status, date_created) VALUES 
    ('slide-khoepro-1.jpg', 'Khỏe Pro - Đánh giá đồ tập gym chuyên sâu', 'Cung cấp góc nhìn khách quan, phân tích thông số và so sánh chi tiết.', 'san-pham', 'slide', 'photo_multi', 1, 'hienthi', ?),
    ('slide-khoepro-2.jpg', 'Lựa chọn thông minh hơn. Tập luyện khỏe hơn.', 'Khám phá cẩm nang chọn mua và các bài review thiết bị phục hồi.', 'tin-tuc', 'slide', 'photo_multi', 2, 'hienthi', ?)",
    array($now, $now)
);

echo "✓ Updated table_photo for logo, favicon and slideshow" . PHP_EOL;
echo "=== COMPLETED BATCH 7: VISUAL ASSETS & HOMEPAGE CURATION ===" . PHP_EOL;
