<?php
/**
 * PHASE 11 SUPPLEMENT: BACKFILL SEO PAGES, CATEGORY IMAGES & 1200x630 OG DERIVATIVES
 */

define('LIBRARIES', './libraries/');
require_once 'libraries/config.php';
require_once 'libraries/class/class.PDODb.php';

$d = new PDODb($config['database']);

echo "=== START PHASE 11 SEO & SOCIAL IMAGE UPGRADE ===" . PHP_EOL;

// Helper to generate 1200x630 Open Graph banner
function generateSocialImage($filePath, $title, $category = 'KHỎE PRO', $tagline = 'khoepro.com  *  Đánh giá & So sánh dụng cụ tập gym', $themeColor = 'dark') {
    $width = 1200;
    $height = 630;
    $im = imagecreatetruecolor($width, $height);

    $themes = array(
        'dark' => array(array(15, 23, 42), array(30, 41, 59), array(255, 255, 255), array(16, 185, 129)),
        'gym' => array(array(10, 15, 30), array(24, 49, 83), array(255, 255, 255), array(2, 86, 170)), // #0256aa
        'emerald' => array(array(4, 47, 46), array(13, 148, 136), array(255, 255, 255), array(52, 211, 153)),
        'amber' => array(array(69, 26, 3), array(180, 83, 9), array(255, 255, 255), array(251, 191, 36)),
        'red' => array(array(69, 10, 10), array(185, 28, 28), array(255, 255, 255), array(248, 113, 113)),
        'purple' => array(array(59, 7, 100), array(126, 34, 206), array(255, 255, 255), array(192, 132, 252))
    );

    $theme = isset($themes[$themeColor]) ? $themes[$themeColor] : $themes['gym'];
    $bgStart = $theme[0];
    $bgEnd = $theme[1];

    for ($y = 0; $y < $height; $y++) {
        $r = (int)($bgStart[0] + ($bgEnd[0] - $bgStart[0]) * ($y / $height));
        $g = (int)($bgStart[1] + ($bgEnd[1] - $bgStart[1]) * ($y / $height));
        $b = (int)($bgStart[2] + ($bgEnd[2] - $bgStart[2]) * ($y / $height));
        $color = imagecolorallocate($im, $r, $g, $b);
        imageline($im, 0, $y, $width, $y, $color);
    }

    $accentColor = imagecolorallocate($im, $theme[3][0], $theme[3][1], $theme[3][2]);
    $whiteColor = imagecolorallocate($im, 255, 255, 255);
    $subColor = imagecolorallocate($im, 203, 213, 225);
    $badgeBg = imagecolorallocatealpha($im, $theme[3][0], $theme[3][1], $theme[3][2], 85);

    // Decorative graphics
    imagefilledrectangle($im, 60, 60, $width - 60, 68, $accentColor);
    imagefilledellipse($im, 1050, 160, 260, 260, $badgeBg);
    imagefilledellipse($im, 1120, 480, 180, 180, $badgeBg);

    // Top Category badge
    imagestring($im, 5, 80, 95, "KHÓE PRO  |  " . mb_strtoupper($category, 'UTF-8'), $accentColor);

    // Main Title Word Wrap
    $words = explode(' ', $title);
    $lines = array();
    $currentLine = '';
    $maxChars = 40;

    foreach ($words as $w) {
        if (strlen($currentLine . ' ' . $w) <= $maxChars) {
            $currentLine .= ($currentLine ? ' ' : '') . $w;
        } else {
            if ($currentLine) $lines[] = $currentLine;
            $currentLine = $w;
        }
    }
    if ($currentLine) $lines[] = $currentLine;

    $startY = 220;
    foreach ($lines as $i => $line) {
        if ($i > 4) break;
        imagestring($im, 5, 80, $startY + ($i * 45), $line, $whiteColor);
    }

    // Bottom brand bar
    imagestring($im, 4, 80, $height - 90, $tagline, $subColor);
    imagestring($im, 5, $width - 240, $height - 90, "https://khoepro.com", $accentColor);

    $dir = dirname($filePath);
    if (!file_exists($dir)) mkdir($dir, 0755, true);

    imagejpeg($im, $filePath, 92);
    imagedestroy($im);
}

// -------------------------------------------------------------------
// 1. POPULATE & BACKFILL TABLE_SEOPAGE
// -------------------------------------------------------------------
echo PHP_EOL . "--- 1. BACKFILLING TABLE_SEOPAGE (10 LANDING PAGES) ---" . PHP_EOL;

$seoPages = array(
    array(
        'type' => 'trang-chu',
        'titlevi' => 'Khỏe Pro - Nền tảng Đánh giá & So sánh Dụng cụ Tập Gym, Thể thao',
        'descriptionvi' => 'Khỏe Pro cung cấp các bài đánh giá chuyên sâu, so sánh khách quan và cẩm nang chọn mua dụng cụ tập gym, thể thao, thiết bị phục hồi chuẩn xác.',
        'keywordsvi' => 'khoe pro, danh gia do tap gym, so sanh dai lung, day khang luc, sung massage, tham yoga',
        'titleen' => 'Khoe Pro - Fitness Gear Reviews & Comparisons',
        'descriptionen' => 'In-depth reviews, objective comparisons, and buying guides for workout gear, fitness tech, and recovery equipment.',
        'keywordsen' => 'khoe pro, gym gear reviews, lifting belts, resistance bands, massage guns',
        'photo' => 'trang-chu-facebook.jpg',
        'theme' => 'gym'
    ),
    array(
        'type' => 'san-pham',
        'titlevi' => 'Sản phẩm Dụng Cụ Tập Gym & Phụ Kiện Thể Thao Chính Hãng | Khỏe Pro',
        'descriptionvi' => 'Khám phá danh mục đầy đủ các dụng cụ tập gym, đai lưng thể hình, dây kháng lực, thảm tập và thiết bị phục hồi chính hãng được phân tích chi tiết.',
        'keywordsvi' => 'san pham gym, do tap the hinh, dai lung gym, day khang luc, tham yoga, khoe pro',
        'titleen' => 'Fitness & Workout Equipment Catalog | Khoe Pro',
        'descriptionen' => 'Explore authentic gym accessories, weightlifting belts, resistance bands, and recovery tools reviewed by Khoe Pro.',
        'keywordsen' => 'fitness gear, workout accessories, weightlifting equipment',
        'photo' => 'san-pham-facebook.jpg',
        'theme' => 'dark'
    ),
    array(
        'type' => 'tin-tuc',
        'titlevi' => 'Tin tức, Cẩm Nang & Đánh Giá Đồ Tập Gym Mới Nhất | Khỏe Pro',
        'descriptionvi' => 'Tổng hợp các bài viết review thực tế, cẩm nang chọn mua và kiến thức tập luyện thể thao hữu ích từ ban biên tập Khỏe Pro.',
        'keywordsvi' => 'tin tuc gym, review do tap, cam nang the thao, kien thuc tap luyen, khoe pro',
        'titleen' => 'Fitness News, Reviews & Guides | Khoe Pro',
        'descriptionen' => 'Latest workout gear reviews, buying guides, and fitness science articles.',
        'keywordsen' => 'fitness news, workout gear reviews, gym guides',
        'photo' => 'tin-tuc-facebook.jpg',
        'theme' => 'emerald'
    ),
    array(
        'type' => 'danh-gia-review',
        'titlevi' => 'Đánh Giá & Review Dụng Cụ Tập Gym Chuyên Sâu | Khỏe Pro',
        'descriptionvi' => 'Chuyên mục đánh giá chi tiết chất liệu, công thái học, ưu nhược điểm và đối tượng phù hợp của từng sản phẩm thể hình.',
        'keywordsvi' => 'danh gia do tap, review dai lung, review sung massage, review day khang luc, khoe pro',
        'titleen' => 'In-depth Fitness Gear Reviews | Khoe Pro',
        'descriptionen' => 'Unbiased editorial evaluations of weightlifting belts, straps, mats, and recovery tools.',
        'keywordsen' => 'gear reviews, belt review, massage gun review',
        'photo' => 'danh-gia-review-facebook.jpg',
        'theme' => 'gym'
    ),
    array(
        'type' => 'huong-dan-chon-mua',
        'titlevi' => 'Hướng Dẫn & Cẩm Nang Chọn Mua Đồ Tập Gym Chuẩn | Khỏe Pro',
        'descriptionvi' => 'Bộ tiêu chí rõ ràng giúp bạn chọn đúng kích cỡ, chất liệu và phân khúc giá cho đai lưng, dây kháng lực, thảm tập và súng massage.',
        'keywordsvi' => 'huong dan chon mua do gym, cach chon dai lung, cach chon tham yoga, khoe pro',
        'titleen' => 'Fitness Gear Buying Guides | Khoe Pro',
        'descriptionen' => 'Practical buying criteria for gym belts, resistance bands, and workout mats.',
        'keywordsen' => 'buying guides, fitness gear criteria',
        'photo' => 'huong-dan-chon-mua-facebook.jpg',
        'theme' => 'amber'
    ),
    array(
        'type' => 'so-sanh-san-pham',
        'titlevi' => 'So Sánh Đối Đầu Dụng Cụ & Thiết Bị Thể Thao | Khỏe Pro',
        'descriptionvi' => 'Đặt lên bàn cân các dòng sản phẩm cùng phân khúc để làm rõ sự khác biệt về vật liệu, độ bền, độ bám và tính kinh tế.',
        'keywordsvi' => 'so sanh do tap gym, lever belt vs prong belt, day vai vs day cao su, khoe pro',
        'titleen' => 'Head-to-Head Fitness Gear Comparisons | Khoe Pro',
        'descriptionen' => 'Direct comparisons between weightlifting belts, resistance bands, and recovery gear.',
        'keywordsen' => 'gear comparisons, lever vs prong, fabric vs latex',
        'photo' => 'so-sanh-san-pham-facebook.jpg',
        'theme' => 'purple'
    ),
    array(
        'type' => 'kien-thuc-tap-luyen',
        'titlevi' => 'Kiến Thức Tập Luyện, Cơ Sinh Học & Phục Hồi Cơ Bắp | Khỏe Pro',
        'descriptionvi' => 'Chia sẻ kiến thức chuẩn khoa học về kỹ thuật gồng áp lực ổ bụng, phục hồi mạc cơ (SMR) và phòng ngừa chấn thương khi tập tạ.',
        'keywordsvi' => 'kien thuc tap gym, co sinh hoc the hinh, phuc hoi co bap, foam rolling, khoe pro',
        'titleen' => 'Fitness Science & Muscle Recovery | Khoe Pro',
        'descriptionen' => 'Science-backed insights on intra-abdominal pressure, myofascial release, and safe training.',
        'keywordsen' => 'fitness science, muscle recovery, injury prevention',
        'photo' => 'kien-thuc-tap-luyen-facebook.jpg',
        'theme' => 'emerald'
    ),
    array(
        'type' => 'video',
        'titlevi' => 'Video 30s Review & Hướng Dẫn Trực Quan | Khỏe Pro',
        'descriptionvi' => 'Xem video review ngắn gọn, trực quan về chuyển động, cách sử dụng và chi tiết các dòng đồ tập gym phổ biến.',
        'keywordsvi' => 'video review do tap, video gym 30s, huong dan su dung phu kien, khoe pro',
        'titleen' => '30-Second Fitness Video Reviews | Khoe Pro',
        'descriptionen' => 'Short visual video overviews of gym gear and workout accessories.',
        'keywordsen' => 'gym videos, gear video review',
        'photo' => 'video-facebook.jpg',
        'theme' => 'red'
    ),
    array(
        'type' => 'gioi-thieu',
        'titlevi' => 'Giới Thiệu Khỏe Pro - Tôn Chỉ & Sứ Mệnh Vì Người Tập Luyện',
        'descriptionvi' => 'Tìm hiểu về đội ngũ Khỏe Pro, tôn chỉ hoạt động độc lập, phương pháp đánh giá khách quan và cam kết bảo vệ người tiêu dùng thể thao.',
        'keywordsvi' => 'gioi thieu khoe pro, ve chung toi, su menh khoe pro, ban bien tap',
        'titleen' => 'About Khoe Pro - Mission & Editorial Standards',
        'descriptionen' => 'Learn about Khoe Pro, our independent mission, and editorial evaluation principles.',
        'keywordsen' => 'about khoe pro, mission, editorial policy',
        'photo' => 'gioi-thieu-facebook.jpg',
        'theme' => 'gym'
    ),
    array(
        'type' => 'lien-he',
        'titlevi' => 'Liên Hệ Ban Biên Tập Khỏe Pro | Tiếp Nhận Phản Hồi & Đóng Góp',
        'descriptionvi' => 'Thông tin liên hệ chính thức của Khỏe Pro. Chúng tôi luôn lắng nghe phản hồi của bạn đọc và tiếp nhận sản phẩm thẩm định.',
        'keywordsvi' => 'lien he khoe pro, van phong khoe pro, email contact khoepro',
        'titleen' => 'Contact Khoe Pro Editorial Team',
        'descriptionen' => 'Official contact information and feedback channels for Khoe Pro.',
        'keywordsen' => 'contact khoe pro, support email',
        'photo' => 'lien-he-facebook.jpg',
        'theme' => 'dark'
    )
);

foreach ($seoPages as $sp) {
    $ogPath = "upload/seopage/" . $sp['photo'];
    generateSocialImage($ogPath, $sp['titlevi'], 'LANDING PAGE', 'khoepro.com  *  Nền tảng đánh giá đồ tập gym số 1', $sp['theme']);
    echo "  - Generated SEO Page OG Image: {$ogPath}" . PHP_EOL;

    $optionsJson = json_encode(array(
        'p' => $sp['photo'],
        'w' => 1200,
        'h' => 630,
        'm' => 'image/jpeg'
    ));

    $exist = $d->rawQueryOne("SELECT id FROM #_seopage WHERE type = ? LIMIT 1", array($sp['type']));
    if (!empty($exist['id'])) {
        $d->rawQuery("UPDATE #_seopage SET 
            titlevi = ?, descriptionvi = ?, keywordsvi = ?,
            titleen = ?, descriptionen = ?, keywordsen = ?,
            photo = ?, options = ?
            WHERE id = ?", array(
            $sp['titlevi'], $sp['descriptionvi'], $sp['keywordsvi'],
            $sp['titleen'], $sp['descriptionen'], $sp['keywordsen'],
            $sp['photo'], $optionsJson, $exist['id']
        ));
        echo "  - Updated seopage: {$sp['type']} (ID: {$exist['id']})" . PHP_EOL;
    } else {
        $d->rawQuery("INSERT INTO #_seopage (
            type, titlevi, descriptionvi, keywordsvi,
            titleen, descriptionen, keywordsen,
            photo, options
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array(
            $sp['type'], $sp['titlevi'], $sp['descriptionvi'], $sp['keywordsvi'],
            $sp['titleen'], $sp['descriptionen'], $sp['keywordsen'],
            $sp['photo'], $optionsJson
        ));
        echo "  - Created seopage: {$sp['type']}" . PHP_EOL;
    }
}

// -------------------------------------------------------------------
// 2. CATEGORY MAIN IMAGES & 1200x630 OG DERIVATIVES
// -------------------------------------------------------------------
echo PHP_EOL . "--- 2. CATEGORY IMAGES & SOCIAL DERIVATIVES ---" . PHP_EOL;

// Product Lists (Level 1)
$plists = $d->rawQuery("SELECT id, namevi, slugvi FROM #_product_list WHERE type = 'san-pham'");
foreach ($plists as $pl) {
    $mainImg = $pl['slugvi'] . ".jpg";
    $ogImg = $pl['slugvi'] . "-facebook.jpg";
    
    // Main 576x432
    generateSocialImage("upload/product/" . $mainImg, $pl['namevi'], 'DANH MỤC SẢN PHẨM', 'khoepro.com  *  Dụng cụ tập gym chính hãng', 'dark');
    // OG 1200x630
    generateSocialImage("upload/product/" . $ogImg, $pl['namevi'], 'DANH MỤC SẢN PHẨM', 'khoepro.com  *  Dụng cụ tập gym chính hãng', 'dark');

    $d->rawQuery("UPDATE #_product_list SET photo = ? WHERE id = ?", array($mainImg, $pl['id']));
    echo "  - Updated product_list image: {$pl['namevi']} -> {$mainImg}" . PHP_EOL;
}

// Product Cats (Level 2)
$pcats = $d->rawQuery("SELECT id, namevi, slugvi FROM #_product_cat WHERE type = 'san-pham'");
foreach ($pcats as $pc) {
    $mainImg = $pc['slugvi'] . ".jpg";
    $ogImg = $pc['slugvi'] . "-facebook.jpg";

    generateSocialImage("upload/product/" . $mainImg, $pc['namevi'], 'CHUYÊN MỤC DỤNG CỤ', 'khoepro.com  *  Đánh giá chi tiết & thông số', 'gym');
    generateSocialImage("upload/product/" . $ogImg, $pc['namevi'], 'CHUYÊN MỤC DỤNG CỤ', 'khoepro.com  *  Đánh giá chi tiết & thông số', 'gym');

    $d->rawQuery("UPDATE #_product_cat SET photo = ? WHERE id = ?", array($mainImg, $pc['id']));
    echo "  - Updated product_cat image: {$pc['namevi']} -> {$mainImg}" . PHP_EOL;
}

// News Lists
$nlists = $d->rawQuery("SELECT id, namevi, slugvi FROM #_news_list WHERE type = 'tin-tuc'");
foreach ($nlists as $nl) {
    $mainImg = $nl['slugvi'] . ".jpg";
    $ogImg = $nl['slugvi'] . "-facebook.jpg";

    generateSocialImage("upload/news/" . $mainImg, $nl['namevi'], 'CHUYÊN MỤC BÀI VIẾT', 'khoepro.com  *  Nội dung biên tập chuẩn mực', 'emerald');
    generateSocialImage("upload/news/" . $ogImg, $nl['namevi'], 'CHUYÊN MỤC BÀI VIẾT', 'khoepro.com  *  Nội dung biên tập chuẩn mực', 'emerald');

    $d->rawQuery("UPDATE #_news_list SET photo = ? WHERE id = ?", array($mainImg, $nl['id']));
    echo "  - Updated news_list image: {$nl['namevi']} -> {$mainImg}" . PHP_EOL;
}

// -------------------------------------------------------------------
// 3. PRODUCT 1200x630 FACEBOOK DERIVATIVE IMAGES
// -------------------------------------------------------------------
echo PHP_EOL . "--- 3. PRODUCT 1200x630 SOCIAL DERIVATIVES ---" . PHP_EOL;

$products = $d->rawQuery("SELECT id, namevi, slugvi, code, regular_price, sale_price FROM #_product WHERE type = 'san-pham'");
foreach ($products as $p) {
    $ogImg = $p['slugvi'] . "-facebook.jpg";
    $ogPath = "upload/product/" . $ogImg;
    $priceTag = $p['sale_price'] > 0 ? number_format($p['sale_price']) . 'đ' : 'Chính hãng';
    
    generateSocialImage($ogPath, $p['namevi'], 'SẢN PHẨM: ' . $p['code'], "khoepro.com  *  Giá tốt: {$priceTag}  *  Đánh giá chuyên sâu", 'gym');
    echo "  - Generated Product OG Image: {$ogPath}" . PHP_EOL;
}

// -------------------------------------------------------------------
// 4. NEWS & ARTICLE 1200x630 FACEBOOK DERIVATIVE IMAGES
// -------------------------------------------------------------------
echo PHP_EOL . "--- 4. ARTICLE 1200x630 SOCIAL DERIVATIVES ---" . PHP_EOL;

$articles = $d->rawQuery("SELECT id, namevi, slugvi, type FROM #_news");
foreach ($articles as $a) {
    $ogImg = $a['slugvi'] . "-facebook.jpg";
    $ogPath = "upload/news/" . $ogImg;
    
    generateSocialImage($ogPath, $a['namevi'], 'BÀI VIẾT BIÊN TẬP', 'khoepro.com  *  Kiến thức & Đánh giá khách quan', 'dark');
    echo "  - Generated Article OG Image: {$ogPath}" . PHP_EOL;
}

echo PHP_EOL . "=== COMPLETED PHASE 11 SEO & SOCIAL IMAGE UPGRADE ===" . PHP_EOL;
