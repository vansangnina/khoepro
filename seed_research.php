<?php
/**
 * Seed Sample Product Research Candidates for Admin Testing
 * PHP 7.4 Compatible
 */

$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';

define('LIBRARIES', __DIR__ . '/libraries/');
require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$dbConfig = $config['database'];
$dbConfig['unix_socket'] = '/Applications/MAMP/tmp/mysql/mysql.sock';
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);
$research = new ProductResearch($d, $func);

$samples = array(
    array(
        'name' => 'Dây kháng lực ngũ sắc FITPRO 150LBS',
        'platform' => 'tiktok',
        'source_url' => 'https://www.tiktok.com/@fitshop_vn/product/172938491829',
        'external_product_id' => '172938491829',
        'category_hint' => 'Dây kháng lực & Phụ kiện',
        'brand_hint' => 'FITPRO',
        'image_url' => 'https://images.unsplash.com/photo-1598289431512-b97b0917affc?w=400&q=80',
        'price' => 189000,
        'original_price' => 280000,
        'sales_count' => 8400,
        'rating' => 4.85,
        'review_count' => 1950,
        'commission_rate' => 15.0,
        'commission_value' => 28350,
        'estimated_gmv' => 1587600000,
        'top_video_views' => 2400000,
        'creator_count' => 38,
        'video_count' => 64,
        'problem_solved' => 'Giải pháp tập luyện toàn thân tại nhà, văn phòng mà không cần máy tạ cồng kềnh. Kháng lực đa cấp độ từ 10lbs đến 150lbs.',
        'target_audience' => 'Người mới bắt đầu tập gym, nhân viên văn phòng bận rộn, người tập calisthenics',
        'research_notes' => 'Chất liệu cao su thiên nhiên 100%, móc khóa thép đúc không rỉ, kèm 2 quai cầm xốp EVA và chốt chặn cửa an toàn',
        'primary_keyword' => 'day khang luc ngu sac tap gym tai nha',
        'competition_score' => 75,
        'seo_score' => 85,
        'status' => 'RESEARCHED'
    ),
    array(
        'name' => 'Đai lưng tập Gym Aolikes da bò 3 lớp bảo vệ cột sống',
        'platform' => 'shopee',
        'source_url' => 'https://shopee.vn/product/12345/67890123',
        'external_product_id' => '67890123',
        'category_hint' => 'Đai lưng & Bảo hộ thể thao',
        'brand_hint' => 'Aolikes',
        'image_url' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=400&q=80',
        'price' => 390000,
        'original_price' => 550000,
        'sales_count' => 12500,
        'rating' => 4.9,
        'review_count' => 4200,
        'commission_rate' => 12.0,
        'commission_value' => 46800,
        'estimated_gmv' => 4875000000,
        'top_video_views' => 1800000,
        'creator_count' => 45,
        'video_count' => 82,
        'problem_solved' => 'Tăng áp lực ổ bụng (IAP), cố định và bảo vệ thắt lưng khi gánh tạ Squat và Deadlift nặng, chống cong lưng chấn thương.',
        'target_audience' => 'Gymer tập tạ trung cấp & nâng cao, người tập Powerlifting',
        'research_notes' => 'Da bò dày 10mm, khóa thép đôi chịu tải 300kg+, lớp đệm lót lưng êm ái thấm hút mồ hôi',
        'primary_keyword' => 'dai lung tap gym squat deadlift aolikes',
        'competition_score' => 85,
        'seo_score' => 90,
        'status' => 'APPROVED'
    ),
    array(
        'name' => 'Bình lắc giữ nhiệt Stainless Steel Shaker 750ml',
        'platform' => 'lazada',
        'source_url' => 'https://lazada.vn/products/binh-lac-gym-inox-750ml.html',
        'external_product_id' => 'LZD998877',
        'category_hint' => 'Bình lắc & Phụ kiện',
        'brand_hint' => 'FITNADO Gear',
        'image_url' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=400&q=80',
        'price' => 145000,
        'original_price' => 220000,
        'sales_count' => 3100,
        'rating' => 4.75,
        'review_count' => 620,
        'commission_rate' => 18.0,
        'commission_value' => 26100,
        'estimated_gmv' => 449500000,
        'top_video_views' => 450000,
        'creator_count' => 12,
        'video_count' => 20,
        'problem_solved' => 'Khử mùi hôi nhựa khi pha whey/BCAA, giữ lạnh đồ uống tập gym suốt 12 tiếng.',
        'target_audience' => 'Tất cả gymer, người tập thể thao ngoài trời',
        'research_notes' => 'Inox 304 không gỉ, nắp có gioăng silicone chống tràn 100%, có bóng lò xo đánh tan whey',
        'primary_keyword' => 'binh lac tap gym inox giu nhiet',
        'competition_score' => 70,
        'seo_score' => 75,
        'status' => 'DISCOVERED'
    )
);

foreach ($samples as $s) {
    $s['normalized_name'] = $research->normalizeName($s['name']);
    $s['normalized_url'] = $research->normalizeUrl($s['source_url']);
    $score = $research->calculateTotalScore($s);
    $s['demand_score'] = $score['demand_score'];
    $s['content_score'] = $score['content_score'];
    $s['commission_score'] = $score['commission_score'];
    $s['competition_score'] = $score['competition_score'];
    $s['seo_score'] = $score['seo_score'];
    $s['total_score'] = $score['total_score'];
    $s['score_breakdown'] = json_encode($score, JSON_UNESCAPED_UNICODE);
    $s['date_created'] = time();
    $s['date_updated'] = time();
    $s['history'] = json_encode(array(array('action' => 'CREATED', 'date' => date('Y-m-d H:i:s'), 'time' => time())), JSON_UNESCAPED_UNICODE);

    $exist = $d->rawQueryOne("select id from #_product_research where external_product_id = ?", array($s['external_product_id']));
    if (empty($exist)) {
        $id = $d->insert('product_research', $s);
        echo "Inserted Candidate: " . $s['name'] . " (ID: #$id, Score: " . $s['total_score'] . "/100)\n";
    }
}
echo "Sample seeding completed.\n";
