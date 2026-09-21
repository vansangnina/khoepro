<?php
/**
 * PHASE 11: APPLY AUTHENTIC REAL PHOTOS FOR PRODUCTS, CATEGORIES & ARTICLES
 * - Pure, real product & gear photos (clean, high resolution, no text overlay)
 * - Real gym/lifestyle/recovery photos for all articles
 * - Cleans thumbs/ cache so all admin & frontend views update immediately
 */

define('LIBRARIES', './libraries/');
require_once 'libraries/config.php';
require_once 'libraries/class/class.PDODb.php';

$d = new PDODb($config['database']);

echo "========================================================\n";
echo "📸 APPLYING AUTHENTIC REAL PHOTOS TO ENTIRE KHOEPRO\n";
echo "========================================================\n\n";

function fetchAndSaveImage($url, $destPath, $targetW = 800, $targetH = 600, $fitSquare = false) {
    $tempFile = $destPath . '.tmp';
    $ch = curl_init($url);
    $fp = fopen($tempFile, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
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

    if ($fitSquare) {
        // Fit square product image with white background
        $dst = imagecreatetruecolor($targetW, $targetH);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $targetW, $targetH, $white);

        $ratio = min($targetW / $srcW, $targetH / $srcH);
        $newW = (int)($srcW * $ratio);
        $newH = (int)($srcH * $ratio);
        $dstX = (int)(($targetW - $newW) / 2);
        $dstY = (int)(($targetH - $newH) / 2);

        imagecopyresampled($dst, $src, $dstX, $dstY, 0, 0, $newW, $newH, $srcW, $srcH);
    } else {
        // Center-crop / cover image
        $dst = imagecreatetruecolor($targetW, $targetH);
        $srcRatio = $srcW / $srcH;
        $targetRatio = $targetW / $targetH;

        if ($srcRatio > $targetRatio) {
            $tempH = $srcH;
            $tempW = (int)($srcH * $targetRatio);
            $srcX = (int)(($srcW - $tempW) / 2);
            $srcY = 0;
        } else {
            $tempW = $srcW;
            $tempH = (int)($srcW / $targetRatio);
            $srcX = 0;
            $srcY = (int)(($srcH - $tempH) / 2);
        }

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $targetW, $targetH, $tempW, $tempH);
    }

    imagejpeg($dst, $destPath, 92);
    imagedestroy($src);
    imagedestroy($dst);

    return true;
}

// 1. ALL 18 PRODUCTS (Square 800x800, pure authentic product photos)
echo "1️⃣ Updating 18 Product Real Photos...\n";
$productPhotos = [
    'dai-lung-da-harbinger-4-inch.jpg' => 'https://harbingerfitness.com/cdn/shop/files/HARB_15131_PaddedLeatherBelt2.0_4inch_LS15_240904-2000x2000-50cecc9.jpg?v=1752077201',
    'dai-lung-don-bay-aolikes-lever-belt.jpg' => 'https://i.ebayimg.com/images/g/zIIAAOSw4p1nYRor/s-l500.jpg',
    'dai-lung-nylon-harbinger-5-inch.jpg' => 'https://i.ebayimg.com/images/g/K1wAAOSwGhdiZ57L/s-l1200.jpg',
    'dai-lung-valeo-eva-foam.jpg' => 'https://www.arizon.az/storage/photos/azer2023/sport/valekemr/valeo_gym_belt_and_strap_set_f_1636268913_c2669dee_progressive.jpg',
    'bo-day-khang-luc-ngu-sac-pseudois.jpg' => 'https://i.ebayimg.com/images/g/rTgAAOSwX~9g3udp/s-l1200.jpg',
    'set-3-day-khang-luc-vai-aolikes.jpg' => 'https://images.tokopedia.net/img/cache/700/o3syd0/1997/1/1/b469d85526e94168b38fba3026510f62~.jpeg.webp',
    'day-khang-luc-powerband-prosourcefit.jpg' => 'https://www.prosourcefit.com/cdn/shop/files/XFIT-Power-Resistance-Bands-Set-01-Shopify.jpg?v=1731018555&width=1406',
    'con-lan-triggerpoint-grid-1.jpg' => 'https://tptherapy.com/cdn/shop/files/ii6cnrgslcu4h5xvryem.jpg?v=1750694296',
    'con-lan-epp-high-density.jpg' => 'https://www.algeos.com/media/catalog/product/cache/d2b2e781a4c16536e6029c3f8ce0aff7/i/m/img_5845_1.jpeg',
    'bong-massage-doi-peanut-lacrosse.jpg' => 'https://i.ebayimg.com/images/g/Y5wAAOSwhYtoKpx~/s-l500.jpg',
    'day-keo-lung-harbinger-padded-cotton.jpg' => 'https://cdn.shopify.com/s/files/1/0024/9803/5810/products/21300_Cotton_Padded_Lifting_Straps_Black_1080x.jpg',
    'day-keo-lung-so-8-aolikes.jpg' => 'https://ae-pic-a1.aliexpress-media.com/kf/Sb781800de9c34e7698a6fba45c984109F.jpg',
    'gang-tay-tap-gym-aolikes-crossfit.jpg' => 'https://i.ebayimg.com/images/g/H04AAOSwZtlduU-h/s-l1200.jpg',
    'gang-tay-harbinger-pro-wristwrap.jpg' => 'https://harbingerfitness.com/cdn/shop/files/HARB_16292_F_Pro_Wristwrap_Gloves_3.0_Mens_Black_LS3_240523_No_Tattoos-2000x1309-b65094c.jpg?v=1745260116',
    'sung-massage-booster-pro-3.jpg' => 'https://bizweb.dktcdn.net/100/477/865/products/file-booter-tang-tinh-dau-02.jpg?v=1743317290453',
    'sung-massage-mini-xiaomi-mijia.jpg' => 'https://i02.appmifile.com/mi-com-product/fly-birds/xiaomi-massage-gun-mini/98ce74c933e0e20759d0997a5292a864.jpg',
    'tham-dinh-tuyen-liforme-yoga-mat.jpg' => 'https://liforme.com/cdn/shop/files/cosmic_6c56993d-6d95-4b67-a4b8-799ce70a7e7f.png?v=1753889116',
    'tham-tap-manduka-prolite.jpg' => 'http://eu.manduka.com/cdn/shop/files/112011153-PL71-MALDIVE-02.jpg?v=1775648367'
];

foreach ($productPhotos as $filename => $url) {
    $path = 'upload/product/' . $filename;
    echo "  -> Product $filename: ";
    $ok = fetchAndSaveImage($url, $path, 800, 800, true);
    echo ($ok ? "OK" : "SKIP/KEPT") . "\n";
}

// 2. ALL 13 CATEGORIES
echo "\n2️⃣ Updating 13 Category Real Photos...\n";
$categoryPhotos = [
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

foreach ($categoryPhotos as $path => $url) {
    echo "  -> Category $path: ";
    $ok = fetchAndSaveImage($url, $path, 800, 600, false);
    echo ($ok ? "OK" : "SKIP/KEPT") . "\n";
}

// 3. ALL 42 ARTICLES (Real fitness, exercise, equipment & review photos)
echo "\n3️⃣ Updating 42 News/Review/Guide Real Photos...\n";
$articlePhotos = [
    // 16 Review articles: Use the exact real product photo or lifestyle testing photo
    'upload/news/review-dai-lung-da-harbinger-4-inch.jpg' => 'https://harbingerfitness.com/cdn/shop/files/HARB_15131_PaddedLeatherBelt2.0_4inch_LS15_240904-2000x2000-50cecc9.jpg?v=1752077201',
    'upload/news/review-dai-lung-don-bay-aolikes.jpg' => 'https://i.ebayimg.com/images/g/zIIAAOSw4p1nYRor/s-l500.jpg',
    'upload/news/review-dai-lung-nylon-harbinger.jpg' => 'https://i.ebayimg.com/images/g/K1wAAOSwGhdiZ57L/s-l1200.jpg',
    'upload/news/review-bo-day-khang-luc-pseudois.jpg' => 'https://i.ebayimg.com/images/g/rTgAAOSwX~9g3udp/s-l1200.jpg',
    'upload/news/review-set-day-vai-aolikes.jpg' => 'https://images.tokopedia.net/img/cache/700/o3syd0/1997/1/1/b469d85526e94168b38fba3026510f62~.jpeg.webp',
    'upload/news/review-con-lan-triggerpoint-grid-1.jpg' => 'https://tptherapy.com/cdn/shop/files/ii6cnrgslcu4h5xvryem.jpg?v=1750694296',
    'upload/news/review-sung-massage-booster-pro-3.jpg' => 'https://bizweb.dktcdn.net/100/477/865/products/file-booter-tang-tinh-dau-02.jpg?v=1743317290453',
    'upload/news/review-sung-massage-xiaomi-mini.jpg' => 'https://i02.appmifile.com/mi-com-product/fly-birds/xiaomi-massage-gun-mini/98ce74c933e0e20759d0997a5292a864.jpg',
    'upload/news/review-day-keo-lung-harbinger.jpg' => 'https://cdn.shopify.com/s/files/1/0024/9803/5810/products/21300_Cotton_Padded_Lifting_Straps_Black_1080x.jpg',
    'upload/news/review-day-so-8-aolikes.jpg' => 'https://ae-pic-a1.aliexpress-media.com/kf/Sb781800de9c34e7698a6fba45c984109F.jpg',
    'upload/news/review-gang-tay-harbinger-pro.jpg' => 'https://harbingerfitness.com/cdn/shop/files/HARB_16292_F_Pro_Wristwrap_Gloves_3.0_Mens_Black_LS3_240523_No_Tattoos-2000x1309-b65094c.jpg?v=1745260116',
    'upload/news/review-gang-tay-aolikes-crossfit.jpg' => 'https://i.ebayimg.com/images/g/H04AAOSwZtlduU-h/s-l1200.jpg',
    'upload/news/review-tham-liforme-yoga.jpg' => 'https://liforme.com/cdn/shop/files/cosmic_6c56993d-6d95-4b67-a4b8-799ce70a7e7f.png?v=1753889116',
    'upload/news/review-tham-manduka-prolite.jpg' => 'http://eu.manduka.com/cdn/shop/files/112011153-PL71-MALDIVE-02.jpg?v=1775648367',
    'upload/news/review-con-lan-epp.jpg' => 'https://www.algeos.com/media/catalog/product/cache/d2b2e781a4c16536e6029c3f8ce0aff7/i/m/img_5845_1.jpeg',
    'upload/news/review-dai-lung-valeo-eva.jpg' => 'https://www.arizon.az/storage/photos/azer2023/sport/valekemr/valeo_gym_belt_and_strap_set_f_1636268913_c2669dee_progressive.jpg',

    // 8 Buying Guides
    'upload/news/huong-dan-chon-dai-lung-gym.jpg' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=800&q=80',
    'upload/news/huong-dan-chon-day-khang-luc.jpg' => 'https://images.unsplash.com/photo-1598289431512-b97b0917affc?w=800&q=80',
    'upload/news/huong-dan-chon-tham-tap.jpg' => 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=800&q=80',
    'upload/news/huong-dan-chon-sung-massage.jpg' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&q=80',
    'upload/news/huong-dan-chon-gang-tay-straps.jpg' => 'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=800&q=80',
    'upload/news/huong-dan-chon-con-lan-foam-roller.jpg' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800&q=80',
    'upload/news/huong-dan-chon-size-dai-lung.jpg' => 'https://images.unsplash.com/photo-1526506118085-60ce8714f8c5?w=800&q=80',
    'upload/news/huong-dan-thiet-bi-phuc-hoi.jpg' => 'https://images.unsplash.com/photo-1574680096145-d05b474e2155?w=800&q=80',

    // 8 Knowledge articles
    'upload/news/kien-thuc-co-che-dai-lung.jpg' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&q=80',
    'upload/news/kien-thuc-khi-nao-dung-dai-lung.jpg' => 'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?w=800&q=80',
    'upload/news/kien-thuc-lifting-straps.jpg' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=800&q=80',
    'upload/news/kien-thuc-phuc-hoi-co-bap.jpg' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800&q=80',
    'upload/news/kien-thuc-day-khang-luc-vs-ta.jpg' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&q=80',
    'upload/news/kien-thuc-bao-quan-do-tap.jpg' => 'https://images.unsplash.com/photo-1550345332-09e3ac987658?w=800&q=80',
    'upload/news/kien-thuc-5-bai-tap-foam-roller.jpg' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800&q=80',
    'upload/news/kien-thuc-loi-sai-deo-dai-lung.jpg' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=800&q=80',

    // 6 Comparisons
    'upload/news/so-sanh-lever-belt-vs-prong-belt.jpg' => 'https://images.unsplash.com/photo-1526506118085-60ce8714f8c5?w=800&q=80',
    'upload/news/so-sanh-dai-ban-thang-vs-ban-cong.jpg' => 'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=800&q=80',
    'upload/news/so-sanh-day-vai-vs-day-cao-su.jpg' => 'https://images.unsplash.com/photo-1598289431512-b97b0917affc?w=800&q=80',
    'upload/news/so-sanh-sung-massage-vs-foam-roller.jpg' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&q=80',
    'upload/news/so-sanh-tham-cao-su-vs-tpe.jpg' => 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=800&q=80',
    'upload/news/so-sanh-gang-tay-vs-lifting-straps.jpg' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=800&q=80',

    // 4 Policies
    'upload/news/phuong-phap-danh-gia.jpg' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&q=80',
    'upload/news/minh-bach-lien-ket-affiliate.jpg' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=800&q=80',
    'upload/news/chinh-sach-bao-mat.jpg' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&q=80',
    'upload/news/dieu-khoan-su-dung.jpg' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=800&q=80'
];

foreach ($articlePhotos as $path => $url) {
    echo "  -> Article $path: ";
    $ok = fetchAndSaveImage($url, $path, 800, 533, false);
    echo ($ok ? "OK" : "SKIP/KEPT") . "\n";
}

// 4. PURGE THUMBS CACHE
echo "\n4️⃣ Purging all cached thumbnails in thumbs/...\n";
function cleanDirectory($dir) {
    if (!is_dir($dir)) return;
    $files = array_diff(scandir($dir), ['.', '..', '.htaccess']);
    foreach ($files as $file) {
        $full = "$dir/$file";
        if (is_dir($full)) {
            cleanDirectory($full);
            @rmdir($full);
        } else {
            @unlink($full);
        }
    }
}
cleanDirectory('thumbs');
echo "  -> Cleaned all cached thumbs. New requests will generate fresh thumbnails from authentic photos.\n";

echo "\n🎉 ALL REAL PHOTOS APPLIED AND THUMBNAIL CACHE PURGED SUCCESSFULLY!\n";
