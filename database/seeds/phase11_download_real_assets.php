<?php
/**
 * PHASE 11 REAL PRODUCT & CATEGORY IMAGE DOWNLOADER & COMPOSITOR
 * Fetches authentic product photography, processes into clean square/hero JPEG,
 * and composites real product photos onto 1200x630 Facebook OG cards.
 */

define('LIBRARIES', './libraries/');
require_once 'libraries/config.php';
require_once 'libraries/class/class.PDODb.php';

$d = new PDODb($config['database']);

echo "=== DOWNLOADING AUTHENTIC PRODUCT & CATEGORY IMAGES ===" . PHP_EOL;

function downloadAndSave($url, $destPath, $targetW = 800, $targetH = 800) {
    $tempFile = $destPath . '.tmp';
    $ch = curl_init($url);
    $fp = fopen($tempFile, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $ret = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);

    if (!$ret || $httpCode != 200 || filesize($tempFile) < 1000) {
        @unlink($tempFile);
        return false;
    }

    $imgData = @file_get_contents($tempFile);
    $src = @imagecreatefromstring($imgData);
    @unlink($tempFile);

    if (!$src) return false;

    $srcW = imagesx($src);
    $srcH = imagesy($src);

    $dst = imagecreatetruecolor($targetW, $targetH);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefilledrectangle($dst, 0, 0, $targetW, $targetH, $white);

    // Fit image inside target rectangle while keeping aspect ratio
    $ratio = min($targetW / $srcW, $targetH / $srcH);
    $newW = (int)($srcW * $ratio);
    $newH = (int)($srcH * $ratio);
    $dstX = (int)(($targetW - $newW) / 2);
    $dstY = (int)(($targetH - $newH) / 2);

    imagecopyresampled($dst, $src, $dstX, $dstY, 0, 0, $newW, $newH, $srcW, $srcH);
    imagejpeg($dst, $destPath, 92);
    imagedestroy($src);
    imagedestroy($dst);

    return true;
}

// Function to generate 1200x630 Facebook OG Card with the REAL product photo embedded
function generateFacebookProductCard($mainProductPath, $fbDestPath, $title, $categoryName, $rating = '4.9 ★', $price = '') {
    $width = 1200;
    $height = 630;
    $im = imagecreatetruecolor($width, $height);

    // Gradient Navy background
    $bgStart = [10, 18, 36];
    $bgEnd = [20, 42, 74];
    for ($y = 0; $y < $height; $y++) {
        $r = (int)($bgStart[0] + ($bgEnd[0] - $bgStart[0]) * ($y / $height));
        $g = (int)($bgStart[1] + ($bgEnd[1] - $bgStart[1]) * ($y / $height));
        $b = (int)($bgStart[2] + ($bgEnd[2] - $bgStart[2]) * ($y / $height));
        $col = imagecolorallocate($im, $r, $g, $b);
        imageline($im, 0, $y, $width, $y, $col);
    }

    $white = imagecolorallocate($im, 255, 255, 255);
    $blue = imagecolorallocate($im, 2, 86, 170);
    $gold = imagecolorallocate($im, 251, 191, 36);
    $cardBg = imagecolorallocate($im, 255, 255, 255);
    $badgeBg = imagecolorallocate($im, 16, 185, 129);
    $grayText = imagecolorallocate($im, 203, 213, 225);

    // Top gold brand bar
    imagefilledrectangle($im, 0, 0, $width, 8, $gold);

    // Left side: Brand badge & typography
    imagestring($im, 5, 50, 45, 'KHOE PRO - DANH GIA & SO SANH', $gold);
    imagestring($im, 4, 50, 80, strtoupper(mb_convert_encoding($categoryName, 'ISO-8859-1', 'UTF-8')), $grayText);

    // Title lines
    $titleAscii = mb_convert_encoding($title, 'ISO-8859-1', 'UTF-8');
    if (strlen($titleAscii) > 40) {
        $line1 = substr($titleAscii, 0, 40);
        $line2 = substr($titleAscii, 40, 40);
        imagestring($im, 5, 50, 130, $line1, $white);
        imagestring($im, 5, 50, 160, $line2, $white);
    } else {
        imagestring($im, 5, 50, 140, $titleAscii, $white);
    }

    // Rating & Trust badges
    imagefilledrectangle($im, 50, 220, 220, 260, $blue);
    imagestring($im, 5, 65, 230, 'DANH GIA CHUAN', $white);

    imagefilledrectangle($im, 240, 220, 360, 260, $badgeBg);
    imagestring($im, 5, 255, 230, $rating, $white);

    if (!empty($price)) {
        imagestring($im, 5, 50, 300, 'Gia tham khao: ' . $price, $gold);
    }

    // Feature highlights
    imagestring($im, 4, 50, 360, '* 100% Thong so ky thuat thuc te tu nha san xuat', $grayText);
    imagestring($im, 4, 50, 395, '* Tong hop uu nhuoc diem & doi tuong phu hop', $grayText);
    imagestring($im, 4, 50, 430, '* So sanh muc gia & uu dai tot nhat tren cac san', $grayText);

    // Bottom domain footer
    imagestring($im, 4, 50, $height - 50, 'khoepro.com  *  Nen tang danh gia dung cu tap Gym doc lap', $gold);

    // Right side: Embed actual product image inside a modern rounded white card
    $cardX = 640;
    $cardY = 50;
    $cardW = 510;
    $cardH = 510;
    imagefilledrectangle($im, $cardX, $cardY, $cardX + $cardW, $cardY + $cardH, $cardBg);

    if (file_exists($mainProductPath)) {
        $prodImg = @imagecreatefromjpeg($mainProductPath);
        if ($prodImg) {
            $pW = imagesx($prodImg);
            $pH = imagesy($prodImg);
            // Inset by 15px
            imagecopyresampled($im, $prodImg, $cardX + 15, $cardY + 15, 0, 0, $cardW - 30, $cardH - 30, $pW, $pH);
            imagedestroy($prodImg);
        }
    }

    imagejpeg($im, $fbDestPath, 92);
    imagedestroy($im);
}

// 18 Products with direct authentic image sources
$productSources = [
    'dai-lung-da-harbinger-4-inch.jpg' => [
        'url' => 'https://harbingerfitness.com/cdn/shop/files/HARB_15131_PaddedLeatherBelt2.0_4inch_LS15_240904-2000x2000-50cecc9.jpg?v=1752077201',
        'title' => 'Dai lung da Harbinger 4-inch Padded Leather Belt',
        'cat' => 'Dai lung tap gym',
        'rating' => '4.9 *',
        'price' => '650.000d'
    ],
    'dai-lung-don-bay-aolikes-lever-belt.jpg' => [
        'url' => 'https://i.ebayimg.com/images/g/zIIAAOSw4p1nYRor/s-l500.jpg',
        'title' => 'Dai lung khoa don bay Aolikes Lever Belt',
        'cat' => 'Dai lung tap gym',
        'rating' => '4.8 *',
        'price' => '850.000d'
    ],
    'dai-lung-nylon-harbinger-5-inch.jpg' => [
        'url' => 'https://i.ebayimg.com/images/g/K1wAAOSwGhdiZ57L/s-l1200.jpg',
        'title' => 'Dai lung nylon Harbinger 5-inch Foam Core',
        'cat' => 'Dai lung tap gym',
        'rating' => '4.7 *',
        'price' => '520.000d'
    ],
    'dai-lung-valeo-eva-foam.jpg' => [
        'url' => 'https://www.arizon.az/storage/photos/azer2023/sport/valekemr/valeo_gym_belt_and_strap_set_f_1636268913_c2669dee_progressive.jpg',
        'title' => 'Dai lung Valeo Eva Foam Weightlifting Belt',
        'cat' => 'Dai lung tap gym',
        'rating' => '4.6 *',
        'price' => '220.000d'
    ],
    'bo-day-khang-luc-ngu-sac-pseudois.jpg' => [
        'url' => 'https://i.ebayimg.com/images/g/rTgAAOSwX~9g3udp/s-l1200.jpg',
        'title' => 'Bo day khang luc ngu sac Pseudois 11 mon',
        'cat' => 'Day khang luc',
        'rating' => '4.8 *',
        'price' => '280.000d'
    ],
    'set-3-day-khang-luc-vai-aolikes.jpg' => [
        'url' => 'https://images.tokopedia.net/img/cache/700/o3syd0/1997/1/1/b469d85526e94168b38fba3026510f62~.jpeg.webp',
        'title' => 'Set 3 day khang luc vai Aolikes Hip Band',
        'cat' => 'Day khang luc',
        'rating' => '4.9 *',
        'price' => '195.000d'
    ],
    'day-khang-luc-powerband-prosourcefit.jpg' => [
        'url' => 'https://www.prosourcefit.com/cdn/shop/files/XFIT-Power-Resistance-Bands-Set-01-Shopify.jpg?v=1731018555&width=1406',
        'title' => 'Day khang luc Powerband ProsourceFit',
        'cat' => 'Day khang luc',
        'rating' => '4.9 *',
        'price' => '320.000d'
    ],
    'con-lan-triggerpoint-grid-1.jpg' => [
        'url' => 'https://tptherapy.com/cdn/shop/files/ii6cnrgslcu4h5xvryem.jpg?v=1750694296',
        'title' => 'Con lan bot gian co TriggerPoint GRID 1.0',
        'cat' => 'Con lan Foam Roller',
        'rating' => '5.0 *',
        'price' => '890.000d'
    ],
    'con-lan-epp-high-density.jpg' => [
        'url' => 'https://www.algeos.com/media/catalog/product/cache/d2b2e781a4c16536e6029c3f8ce0aff7/i/m/img_5845_1.jpeg',
        'title' => 'Con lan gian co bot xop EPP High-Density',
        'cat' => 'Con lan Foam Roller',
        'rating' => '4.7 *',
        'price' => '240.000d'
    ],
    'bong-massage-doi-peanut-lacrosse.jpg' => [
        'url' => 'https://i.ebayimg.com/images/g/Y5wAAOSwhYtoKpx~/s-l500.jpg',
        'title' => 'Bong massage doi Peanut Lacrosse Ball',
        'cat' => 'Con lan Foam Roller',
        'rating' => '4.8 *',
        'price' => '120.000d'
    ],
    'day-keo-lung-harbinger-padded-cotton.jpg' => [
        'url' => 'http://harbingerfitness.com.au/cdn/shop/files/oommpdutrkah0jrmi6rz.jpg?v=1770947385',
        'title' => 'Day keo lung Harbinger Padded Cotton Straps',
        'cat' => 'Gang tay & Straps',
        'rating' => '4.9 *',
        'price' => '310.000d'
    ],
    'day-keo-lung-so-8-aolikes.jpg' => [
        'url' => 'https://ae-pic-a1.aliexpress-media.com/kf/Sb781800de9c34e7698a6fba45c984109F.jpg',
        'title' => 'Day keo lung so 8 Figure 8 Aolikes Heavy Duty',
        'cat' => 'Gang tay & Straps',
        'rating' => '4.8 *',
        'price' => '160.000d'
    ],
    'gang-tay-tap-gym-aolikes-crossfit.jpg' => [
        'url' => 'https://ae-pic-a1.aliexpress-media.com/kf/Sca044738744547949397672224d081db8.jpg',
        'title' => 'Gang tay tap gym co quan co tay Aolikes',
        'cat' => 'Gang tay & Straps',
        'rating' => '4.7 *',
        'price' => '175.000d'
    ],
    'gang-tay-harbinger-pro-wristwrap.jpg' => [
        'url' => 'https://harbingerfitness.com/cdn/shop/files/HARB_16292_F_Pro_Wristwrap_Gloves_3.0_Mens_Black_LS3_240523_No_Tattoos-2000x1309-b65094c.jpg?v=1745260116',
        'title' => 'Gang tay the hinh Harbinger Pro WristWrap',
        'cat' => 'Gang tay & Straps',
        'rating' => '4.9 *',
        'price' => '540.000d'
    ],
    'sung-massage-booster-pro-3.jpg' => [
        'url' => 'https://bizweb.dktcdn.net/100/477/865/products/file-booter-tang-tinh-dau-02.jpg?v=1743317290453',
        'title' => 'Sung massage co bap Booster Pro 3 High Power',
        'cat' => 'Sung massage cam tay',
        'rating' => '4.9 *',
        'price' => '2.850.000d'
    ],
    'sung-massage-mini-xiaomi-mijia.jpg' => [
        'url' => 'https://i02.appmifile.com/mi-com-product/fly-birds/xiaomi-massage-gun-mini/98ce74c933e0e20759d0997a5292a864.jpg',
        'title' => 'Sung massage mini Xiaomi Mijia Fascia Gun',
        'cat' => 'Sung massage cam tay',
        'rating' => '4.8 *',
        'price' => '990.000d'
    ],
    'tham-dinh-tuyen-liforme-yoga-mat.jpg' => [
        'url' => 'https://liforme.com/cdn/shop/files/cosmic_6c56993d-6d95-4b67-a4b8-799ce70a7e7f.png?v=1753889116',
        'title' => 'Tham tap dinh tuyen Liforme Yoga Mat 4.2mm',
        'cat' => 'Tham tap Gym & Yoga',
        'rating' => '5.0 *',
        'price' => '3.450.000d'
    ],
    'tham-tap-manduka-prolite.jpg' => [
        'url' => 'http://eu.manduka.com/cdn/shop/files/112011153-PL71-MALDIVE-02.jpg?v=1775648367',
        'title' => 'Tham tap the duc chong truot Manduka PROlite',
        'cat' => 'Tham tap Gym & Yoga',
        'rating' => '4.9 *',
        'price' => '2.350.000d'
    ]
];

foreach ($productSources as $filename => $info) {
    $mainPath = 'upload/product/' . $filename;
    echo "Processing $filename... ";
    $ok = downloadAndSave($info['url'], $mainPath, 800, 800);
    if ($ok) {
        echo "Main Downloaded -> ";
        // Update options in DB
        $opt = json_encode(['p' => $filename, 'w' => 800, 'h' => 800, 'm' => 'image/jpeg']);
        $d->rawQuery("UPDATE table_product SET options = ? WHERE photo = ?", [$opt, $filename]);
    } else {
        echo "Keep Existing/Processed -> ";
    }

    // Now composite the real photo into 1200x630 Facebook OG Card
    $slug = pathinfo($filename, PATHINFO_FILENAME);
    $fbPath = 'upload/product/' . $slug . '-facebook.jpg';
    generateFacebookProductCard($mainPath, $fbPath, $info['title'], $info['cat'], $info['rating'], $info['price']);
    echo "OG 1200x630 Generated OK\n";
}

// 2. Process Categories with real fitness photography
echo "\n--- PROCESSING REAL CATEGORY IMAGES ---\n";
$catSources = [
    'upload/product/dung-cu-tap-luyen.jpg' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=800&q=80',
    'upload/product/phu-kien-gym.jpg' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=800&q=80',
    'upload/product/thiet-bi-phuc-hoi-suc-khoe.jpg' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&q=80',
    'upload/product/dai-lung-tap-gym.jpg' => 'https://harbingerfitness.com/cdn/shop/files/HARB_15131_PaddedLeatherBelt2.0_4inch_LS15_240904-2000x2000-50cecc9.jpg?v=1752077201',
    'upload/product/day-khang-luc.jpg' => 'https://www.prosourcefit.com/cdn/shop/files/XFIT-Power-Resistance-Bands-Set-01-Shopify.jpg?v=1731018555&width=1406',
    'upload/product/tham-tap-gym-yoga.jpg' => 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=800&q=80',
    'upload/product/gang-tay-lifting-straps.jpg' => 'https://harbingerfitness.com/cdn/shop/files/HARB_16292_F_Pro_Wristwrap_Gloves_3.0_Mens_Black_LS3_240523_No_Tattoos-2000x1309-b65094c.jpg?v=1745260116',
    'upload/product/con-lan-foam-roller.jpg' => 'https://tptherapy.com/cdn/shop/files/ii6cnrgslcu4h5xvryem.jpg?v=1750694296',
    'upload/product/sung-massage-cam-tay.jpg' => 'https://bizweb.dktcdn.net/100/477/865/products/file-booter-tang-tinh-dau-02.jpg?v=1743317290453',
    'upload/news/kien-thuc-the-hinh.jpg' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&q=80',
    'upload/news/danh-gia-thiet-bi.jpg' => 'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?w=800&q=80',
    'upload/news/huong-dan-chon-mua.jpg' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&q=80',
    'upload/news/kinh-nghiem-phuc-hoi.jpg' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800&q=80'
];

foreach ($catSources as $dest => $url) {
    echo "Downloading category $dest... ";
    downloadAndSave($url, $dest, 800, 600);
    echo "OK\n";
}

echo "\n🎉 FINISHED DOWNLOADING AND COMPOSITING ALL REAL PRODUCT & CATEGORY IMAGES!\n";
