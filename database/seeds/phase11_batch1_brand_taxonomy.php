<?php
define('LIBRARIES', './libraries/');
require_once 'libraries/config.php';
require_once 'libraries/class/class.PDODb.php';

$d = new PDODb($config['database']);

echo "=== START BATCH 1: BRAND CONFIGURATION & TAXONOMY ===" . PHP_EOL;

try {
    // 1. Update table_setting
    $settingOpt = array(
        'mailertype' => '1',
        'ip_host' => '1.1.1.1',
        'port_host' => '25',
        'secure_host' => 'tls',
        'email_host' => 'contact@khoepro.com',
        'password_host' => '',
        'host_gmail' => 'smtp.gmail.com',
        'port_gmail' => '587',
        'secure_gmail' => 'tls',
        'email_gmail' => 'contact@khoepro.com',
        'password_gmail' => '',
        'lang_default' => 'vi',
        'address' => '123 Huỳnh Thúc Kháng, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
        'email' => 'contact@khoepro.com',
        'hotline' => '0988.123.456',
        'phone' => '0988.123.456',
        'zalo' => '0988.123.456',
        'oaidzalo' => '',
        'website' => 'https://khoepro.com',
        'fanpage' => 'https://facebook.com/khoepro',
        'worktime' => '08:00 - 18:00 (Thứ 2 - Thứ 7)'
    );

    $d->rawQuery("UPDATE #_setting SET 
        namevi = ?, 
        nameen = ?, 
        addressvi = ?, 
        addressen = ?, 
        options = ? 
        WHERE id = 1", array(
        'Khỏe Pro - Đánh giá & So sánh Dụng cụ Tập luyện',
        'Khoe Pro - Fitness Gear Reviews & Comparisons',
        '123 Huỳnh Thúc Kháng, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
        '123 Huynh Thuc Khang, Ben Nghe Ward, District 1, Ho Chi Minh City',
        json_encode($settingOpt, JSON_UNESCAPED_UNICODE)
    ));

    // Update Setting SEO
    $checkSeoSetting = $d->rawQueryOne("SELECT id FROM #_seo WHERE com = 'setting' AND act = 'update' AND type = 'setting' LIMIT 1");
    if (!empty($checkSeoSetting['id'])) {
        $d->rawQuery("UPDATE #_seo SET 
            titlevi = ?, 
            keywordsvi = ?, 
            descriptionvi = ? 
            WHERE id = ?", array(
            'Khỏe Pro - Nền tảng Đánh giá & So sánh Dụng cụ Tập Gym, Thể thao',
            'đánh giá đồ tập gym, so sánh đai lưng, dây kháng lực, súng massage, thảm yoga, khỏe pro',
            'Khỏe Pro cung cấp bài đánh giá chuyên sâu, so sánh khách quan và hướng dẫn chọn mua dụng cụ tập gym, thể thao, thiết bị phục hồi chuẩn xác.',
            $checkSeoSetting['id']
        ));
    } else {
        $d->rawQuery("INSERT INTO #_seo (id_parent, com, act, type, titlevi, keywordsvi, descriptionvi, main_keywordsvi, main_keywordsen) VALUES (?, ?, ?, ?, ?, ?, ?, '', '')", array(
            0, 'setting', 'update', 'setting',
            'Khỏe Pro - Nền tảng Đánh giá & So sánh Dụng cụ Tập Gym, Thể thao',
            'đánh giá đồ tập gym, so sánh đai lưng, dây kháng lực, súng massage, thảm yoga, khỏe pro',
            'Khỏe Pro cung cấp bài đánh giá chuyên sâu, so sánh khách quan và hướng dẫn chọn mua dụng cụ tập gym, thể thao, thiết bị phục hồi chuẩn xác.'
        ));
    }
    echo "✓ Updated table_setting and table_seo (setting)" . PHP_EOL;

    // 2. Product Level 1 (table_product_list)
    $productLists = array(
        array(
            'namevi' => 'Dụng cụ tập luyện',
            'nameen' => 'Workout Equipment',
            'slugvi' => 'dung-cu-tap-luyen',
            'slugen' => 'workout-equipment',
            'descvi' => 'Tổng hợp các loại dụng cụ tập thể hình, gym, yoga và phụ kiện hỗ trợ chuyển động kháng lực tại nhà và phòng tập.',
            'numb' => 1,
            'status' => 'hienthi,noibat'
        ),
        array(
            'namevi' => 'Phụ kiện Gym & Thể hình',
            'nameen' => 'Gym Accessories',
            'slugvi' => 'phu-kien-gym',
            'slugen' => 'gym-accessories',
            'descvi' => 'Dây kéo lưng, găng tay, đai cuốn cổ tay, bảo hộ khớp gối giúp gia tăng hiệu suất và hỗ trợ an toàn khi tập nặng.',
            'numb' => 2,
            'status' => 'hienthi,noibat'
        ),
        array(
            'namevi' => 'Thiết bị phục hồi & Sức khỏe',
            'nameen' => 'Recovery & Health Tech',
            'slugvi' => 'thiet-bi-phuc-hoi-suc-khoe',
            'slugen' => 'recovery-health-tech',
            'descvi' => 'Con lăn bọt giãn cơ, bóng massage, súng massage cơ bắp chuyên sâu và dụng cụ giải tỏa căng cứng mô cơ sau tập.',
            'numb' => 3,
            'status' => 'hienthi,noibat'
        )
    );

    $now = time();
    $listIdMap = array();

    foreach ($productLists as $item) {
        $exist = $d->rawQueryOne("SELECT id FROM #_product_list WHERE slugvi = ? LIMIT 1", array($item['slugvi']));
        if (!empty($exist['id'])) {
            $d->rawQuery("UPDATE #_product_list SET namevi = ?, nameen = ?, descvi = ?, numb = ?, status = ?, date_updated = ? WHERE id = ?", array(
                $item['namevi'], $item['nameen'], $item['descvi'], $item['numb'], $item['status'], $now, $exist['id']
            ));
            $targetId = $exist['id'];
            $listIdMap[$item['slugvi']] = $targetId;
            echo "  - Updated product_list: {$item['namevi']} (ID: {$targetId})" . PHP_EOL;
        } else {
            $d->rawQuery("INSERT INTO #_product_list (namevi, nameen, slugvi, slugen, descvi, numb, status, type, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, 'san-pham', ?)", array(
                $item['namevi'], $item['nameen'], $item['slugvi'], $item['slugen'], $item['descvi'], $item['numb'], $item['status'], $now
            ));
            $inserted = $d->rawQueryOne("SELECT id FROM #_product_list WHERE slugvi = ? LIMIT 1", array($item['slugvi']));
            $targetId = $inserted['id'];
            $listIdMap[$item['slugvi']] = $targetId;
            echo "  - Created product_list: {$item['namevi']} (ID: {$targetId})" . PHP_EOL;
        }

        // SEO for product_list
        $seoExist = $d->rawQueryOne("SELECT id FROM #_seo WHERE id_parent = ? AND com = 'product' AND act = 'man_list' AND type = 'san-pham' LIMIT 1", array($targetId));
        $seoTitle = $item['namevi'] . " - Đánh giá & Hướng dẫn Chọn Mua | Khỏe Pro";
        $seoDesc = "Khám phá danh sách " . mb_strtolower($item['namevi'], 'UTF-8') . " chính hãng, phân tích thông số kỹ thuật, đánh giá ưu nhược điểm khách quan từ Khỏe Pro.";
        if (!empty($seoExist['id'])) {
            $d->rawQuery("UPDATE #_seo SET titlevi = ?, descriptionvi = ?, keywordsvi = ? WHERE id = ?", array(
                $seoTitle, $seoDesc, mb_strtolower($item['namevi'], 'UTF-8') . ", khoe pro", $seoExist['id']
            ));
        } else {
            $d->rawQuery("INSERT INTO #_seo (id_parent, com, act, type, titlevi, descriptionvi, keywordsvi, main_keywordsvi, main_keywordsen) VALUES (?, 'product', 'man_list', 'san-pham', ?, ?, ?, '', '')", array(
                $targetId, $seoTitle, $seoDesc, mb_strtolower($item['namevi'], 'UTF-8') . ", khoe pro"
            ));
        }
    }

    // 3. Product Level 2 (table_product_cat)
    $productCats = array(
        array(
            'list_slug' => 'dung-cu-tap-luyen',
            'namevi' => 'Đai lưng tập gym',
            'nameen' => 'Weightlifting Belts',
            'slugvi' => 'dai-lung-tap-gym',
            'slugen' => 'weightlifting-belts',
            'descvi' => 'Các mẫu đai lưng da, đai nylon, đai khóa đòn bẩy (lever belt) hỗ trợ gồng áp lực ổ bụng khi Squat và Deadlift.',
            'numb' => 1,
            'status' => 'hienthi,noibat'
        ),
        array(
            'list_slug' => 'dung-cu-tap-luyen',
            'namevi' => 'Dây kháng lực',
            'nameen' => 'Resistance Bands',
            'slugvi' => 'day-khang-luc',
            'slugen' => 'resistance-bands',
            'descvi' => 'Bộ dây ngũ sắc, mini band và super band hỗ trợ kích hoạt cơ bắp, tập kháng lực tại nhà và tập bổ trợ chuyển động.',
            'numb' => 2,
            'status' => 'hienthi,noibat'
        ),
        array(
            'list_slug' => 'dung-cu-tap-luyen',
            'namevi' => 'Thảm tập Gym & Yoga',
            'nameen' => 'Gym & Yoga Mats',
            'slugvi' => 'tham-tap-gym-yoga',
            'slugen' => 'gym-yoga-mats',
            'descvi' => 'Thảm tập cao su tự nhiên, thảm TPE chống trơn trượt, định tuyến chuẩn xác giúp bảo vệ khớp cổ tay và đầu gối.',
            'numb' => 3,
            'status' => 'hienthi,noibat'
        ),
        array(
            'list_slug' => 'phu-kien-gym',
            'namevi' => 'Găng tay tập Gym & Lifting Straps',
            'nameen' => 'Gym Gloves & Straps',
            'slugvi' => 'gang-tay-lifting-straps',
            'slugen' => 'gym-gloves-straps',
            'descvi' => 'Dây kéo lưng Deadlift, găng tay thoáng khí có quấn cổ tay giúp tăng cường độ bám và bảo vệ lòng bàn tay.',
            'numb' => 1,
            'status' => 'hienthi,noibat'
        ),
        array(
            'list_slug' => 'thiet-bi-phuc-hoi-suc-khoe',
            'namevi' => 'Con lăn Foam Roller & Bóng massage',
            'nameen' => 'Foam Rollers & Massage Balls',
            'slugvi' => 'con-lan-foam-roller',
            'slugen' => 'foam-rollers-massage-balls',
            'descvi' => 'Dụng cụ giải phóng mạc cơ (Myofascial Release), con lăn bọt gai, con lăn rãnh sóng giảm đau nhức cơ bắp.',
            'numb' => 1,
            'status' => 'hienthi,noibat'
        ),
        array(
            'list_slug' => 'thiet-bi-phuc-hoi-suc-khoe',
            'namevi' => 'Súng massage cầm tay',
            'nameen' => 'Massage Guns',
            'slugvi' => 'sung-massage-cam-tay',
            'slugen' => 'massage-guns',
            'descvi' => 'Súng massage bộ gõ chuyên sâu với biên độ rung mạnh mẽ, hỗ trợ tăng lưu thông máu và phục hồi cơ bắp cấp tốc.',
            'numb' => 2,
            'status' => 'hienthi,noibat'
        )
    );

    $catIdMap = array();
    foreach ($productCats as $item) {
        $parentListId = isset($listIdMap[$item['list_slug']]) ? $listIdMap[$item['list_slug']] : 0;
        $exist = $d->rawQueryOne("SELECT id FROM #_product_cat WHERE slugvi = ? LIMIT 1", array($item['slugvi']));
        if (!empty($exist['id'])) {
            $d->rawQuery("UPDATE #_product_cat SET id_list = ?, namevi = ?, nameen = ?, descvi = ?, numb = ?, status = ?, date_updated = ? WHERE id = ?", array(
                $parentListId, $item['namevi'], $item['nameen'], $item['descvi'], $item['numb'], $item['status'], $now, $exist['id']
            ));
            $targetId = $exist['id'];
            $catIdMap[$item['slugvi']] = $targetId;
            echo "  - Updated product_cat: {$item['namevi']} (ID: {$targetId})" . PHP_EOL;
        } else {
            $d->rawQuery("INSERT INTO #_product_cat (id_list, namevi, nameen, slugvi, slugen, descvi, numb, status, type, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'san-pham', ?)", array(
                $parentListId, $item['namevi'], $item['nameen'], $item['slugvi'], $item['slugen'], $item['descvi'], $item['numb'], $item['status'], $now
            ));
            $inserted = $d->rawQueryOne("SELECT id FROM #_product_cat WHERE slugvi = ? LIMIT 1", array($item['slugvi']));
            $targetId = $inserted['id'];
            $catIdMap[$item['slugvi']] = $targetId;
            echo "  - Created product_cat: {$item['namevi']} (ID: {$targetId})" . PHP_EOL;
        }

        // SEO for product_cat
        $seoExist = $d->rawQueryOne("SELECT id FROM #_seo WHERE id_parent = ? AND com = 'product' AND act = 'man_cat' AND type = 'san-pham' LIMIT 1", array($targetId));
        $seoTitle = "Top {$item['namevi']} Tốt Nhất - Đánh Giá Chi Tiết | Khỏe Pro";
        $seoDesc = "Tổng hợp và đánh giá chi tiết các dòng " . mb_strtolower($item['namevi'], 'UTF-8') . " phổ biến, phân tích ưu nhược điểm và thông số kỹ thuật thực tế.";
        if (!empty($seoExist['id'])) {
            $d->rawQuery("UPDATE #_seo SET titlevi = ?, descriptionvi = ?, keywordsvi = ? WHERE id = ?", array(
                $seoTitle, $seoDesc, mb_strtolower($item['namevi'], 'UTF-8') . ", khoe pro", $seoExist['id']
            ));
        } else {
            $d->rawQuery("INSERT INTO #_seo (id_parent, com, act, type, titlevi, descriptionvi, keywordsvi, main_keywordsvi, main_keywordsen) VALUES (?, 'product', 'man_cat', 'san-pham', ?, ?, ?, '', '')", array(
                $targetId, $seoTitle, $seoDesc, mb_strtolower($item['namevi'], 'UTF-8') . ", khoe pro"
            ));
        }
    }

    // 4. News Level 1 (table_news_list)
    $newsLists = array(
        array(
            'namevi' => 'Đánh giá & Review',
            'nameen' => 'Product Reviews',
            'slugvi' => 'danh-gia-review',
            'slugen' => 'product-reviews',
            'descvi' => 'Các bài đánh giá chuyên sâu, phân tích ưu nhược điểm, thông số kỹ thuật và đối tượng phù hợp của từng sản phẩm thể thao.',
            'numb' => 1,
            'status' => 'hienthi,noibat'
        ),
        array(
            'namevi' => 'Hướng dẫn chọn mua',
            'nameen' => 'Buying Guides',
            'slugvi' => 'huong-dan-chon-mua',
            'slugen' => 'buying-guides',
            'descvi' => 'Cẩm nang và tiêu chí giúp bạn lựa chọn thiết bị, dụng cụ tập luyện phù hợp với thể trạng, ngân sách và mục tiêu cá nhân.',
            'numb' => 2,
            'status' => 'hienthi,noibat'
        ),
        array(
            'namevi' => 'So sánh sản phẩm',
            'nameen' => 'Product Comparisons',
            'slugvi' => 'so-sanh-san-pham',
            'slugen' => 'product-comparisons',
            'descvi' => 'Đặt lên bàn cân các dòng sản phẩm cùng phân khúc để làm rõ sự khác biệt về chất liệu, tính năng và giá trị sử dụng.',
            'numb' => 3,
            'status' => 'hienthi,noibat'
        ),
        array(
            'namevi' => 'Kiến thức tập luyện',
            'nameen' => 'Fitness Knowledge',
            'slugvi' => 'kien-thuc-tap-luyen',
            'slugen' => 'fitness-knowledge',
            'descvi' => 'Kiến thức khoa học về phục hồi cơ bắp, kỹ thuật sử dụng phụ kiện an toàn và phương pháp rèn luyện lối sống khỏe mạnh.',
            'numb' => 4,
            'status' => 'hienthi,noibat'
        )
    );

    $newsListIdMap = array();
    foreach ($newsLists as $item) {
        $exist = $d->rawQueryOne("SELECT id FROM #_news_list WHERE slugvi = ? LIMIT 1", array($item['slugvi']));
        if (!empty($exist['id'])) {
            $d->rawQuery("UPDATE #_news_list SET namevi = ?, nameen = ?, descvi = ?, numb = ?, status = ?, date_updated = ? WHERE id = ?", array(
                $item['namevi'], $item['nameen'], $item['descvi'], $item['numb'], $item['status'], $now, $exist['id']
            ));
            $targetId = $exist['id'];
            $newsListIdMap[$item['slugvi']] = $targetId;
            echo "  - Updated news_list: {$item['namevi']} (ID: {$targetId})" . PHP_EOL;
        } else {
            $d->rawQuery("INSERT INTO #_news_list (namevi, nameen, slugvi, slugen, descvi, numb, status, type, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, 'tin-tuc', ?)", array(
                $item['namevi'], $item['nameen'], $item['slugvi'], $item['slugen'], $item['descvi'], $item['numb'], $item['status'], $now
            ));
            $inserted = $d->rawQueryOne("SELECT id FROM #_news_list WHERE slugvi = ? LIMIT 1", array($item['slugvi']));
            $targetId = $inserted['id'];
            $newsListIdMap[$item['slugvi']] = $targetId;
            echo "  - Created news_list: {$item['namevi']} (ID: {$targetId})" . PHP_EOL;
        }

        // SEO for news_list
        $targetId = $newsListIdMap[$item['slugvi']];
        $seoExist = $d->rawQueryOne("SELECT id FROM #_seo WHERE id_parent = ? AND com = 'news' AND act = 'man_list' AND type = 'tin-tuc' LIMIT 1", array($targetId));
        $seoTitle = "{$item['namevi']} Dụng Cụ Thể Thao & Gym | Khỏe Pro";
        $seoDesc = "Tổng hợp bài viết {$item['namevi']} chất lượng cao từ ban biên tập Khỏe Pro. Thông tin trung thực, khách quan và hữu ích.";
        if (!empty($seoExist['id'])) {
            $d->rawQuery("UPDATE #_seo SET titlevi = ?, descriptionvi = ?, keywordsvi = ? WHERE id = ?", array(
                $seoTitle, $seoDesc, mb_strtolower($item['namevi'], 'UTF-8') . ", khoe pro", $seoExist['id']
            ));
        } else {
            $d->rawQuery("INSERT INTO #_seo (id_parent, com, act, type, titlevi, descriptionvi, keywordsvi, main_keywordsvi, main_keywordsen) VALUES (?, 'news', 'man_list', 'tin-tuc', ?, ?, ?, '', '')", array(
                $targetId, $seoTitle, $seoDesc, mb_strtolower($item['namevi'], 'UTF-8') . ", khoe pro"
            ));
        }
    }

    echo "=== COMPLETED BATCH 1: BRAND CONFIGURATION & TAXONOMY ===" . PHP_EOL;

} catch (Exception $e) {
    echo "ERROR in Batch 1: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
