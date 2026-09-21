<?php
define('LIBRARIES', './libraries/');
require_once 'libraries/config.php';
require_once 'libraries/class/class.PDODb.php';

$d = new PDODb($config['database']);

echo "=== START BATCH 6: PRODUCT COMPARISONS (6 HEAD-TO-HEAD ARTICLES) ===" . PHP_EOL;

try {
    $now = time();

    // Get id_list for 'so-sanh-san-pham'
    $compList = $d->rawQueryOne("SELECT id FROM #_news_list WHERE slugvi = 'so-sanh-san-pham' LIMIT 1");
    $idListComp = !empty($compList['id']) ? $compList['id'] : 5;

    $comparisons = array(
        // 1. Lever Belt vs Prong Belt
        array(
            'namevi' => 'So sánh đai lưng đòn bẩy (Lever Belt) và đai cài chốt (Prong Belt): Đâu là chân ái?',
            'nameen' => 'Lever Belt vs Prong Belt Comparison',
            'slugvi' => 'so-sanh-dai-lung-don-bay-va-dai-cai-chot',
            'slugen' => 'lever-belt-vs-prong-belt-comparison',
            'photo' => 'so-sanh-lever-belt-vs-prong-belt.jpg',
            'descvi' => 'Đặt lên bàn cân đai lưng khóa đòn bẩy (Lever Belt) và đai chốt cài kim loại (Prong Belt): Phân tích tốc độ khóa mở, độ ổn định siết áp lực, tính tiện dụng và độ bền khóa qua thời gian.',
            'contentvi' => <<<HTML
<h2>1. Tổng quan hai cơ chế khóa phổ biến nhất</h2>
<p>Khi chọn mua đai lưng da tập tạ nặng, hai kiểu khóa phổ biến nhất khiến gymer đắn đo là <strong>Khóa đòn bẩy (Lever Buckle)</strong> và <strong>Khóa chốt cài kim loại (Prong Buckle)</strong>. Mỗi loại đều mang những ưu điểm riêng biệt tùy theo thói quen tập luyện.</p>

<h2>2. Bảng so sánh trực diện tiêu chí</h2>
<table border="1" cellpadding="8" style="border-collapse:collapse; width:100%; margin-bottom:20px;">
    <tr style="background:#f4f4f4;">
        <th>Tiêu chí</th>
        <th>Đai khóa đòn bẩy (Lever Belt)</th>
        <th>Đai khóa chốt cài (Prong Belt)</th>
    </tr>
    <tr>
        <td><strong>Tốc độ đóng/mở</strong></td>
        <td>⚡ 1 giây (gạt cần bẩy cực nhanh)</td>
        <td>⏳ 10-15 giây (cần kéo căng để rút chốt)</td>
    </tr>
    <tr>
        <td><strong>Độ siết áp lực</strong></td>
        <td>Cực kỳ chặt, đạt áp lực tối đa lập tức</td>
        <td>Tùy thuộc lực kéo tay của người đeo</td>
    </tr>
    <tr>
        <td><strong>Thay đổi kích cỡ</strong></td>
        <td>Phải dùng tua vít chỉnh vị trí ốc</td>
        <td>Chuyển đổi nấc lỗ cài linh hoạt ngay</td>
    </tr>
    <tr>
        <td><strong>Độ bền khóa</strong></td>
        <td>Hợp kim chịu lực (cần tránh làm rơi mạnh)</td>
        <td>Khóa thép kim loại gần như không thể hỏng</td>
    </tr>
    <tr>
        <td><strong>Đại diện tiêu biểu</strong></td>
        <td><a href="san-pham/dai-lung-khoa-don-bay-aolikes-lever-buckle-powerlifting-belt">Đai đòn bẩy Aolikes 10mm</a></td>
        <td><a href="san-pham/dai-lung-da-harbinger-4-inch-padded-leather-belt">Đai da Harbinger 4-inch 2 chốt</a></td>
    </tr>
</table>

<h2>3. Kết luận từ Khỏe Pro: Bạn nên chọn loại nào?</h2>
<ul>
    <li><strong>Chọn Lever Belt nếu:</strong> Bạn tập Powerlifting, Squat/Deadlift tải trọng lớn, muốn tiết kiệm sức sau hiệp tập mệt và chỉ một mình bạn sử dụng đai (số đo vòng eo ổn định).</li>
    <li><strong>Chọn Prong Belt nếu:</strong> Bạn muốn chia sẻ đai dùng chung với bạn tập, vòng bụng có sự chênh lệch lớn giữa các mùa xả cơ/siết cơ, hoặc thích sự cổ điển bền bỉ bất chấp va đập.</li>
</ul>
HTML
        ),

        // 2. 10mm Straight Belt vs 4-inch Contoured Belt
        array(
            'namevi' => 'Đai lưng da bản thẳng 10mm vs Đai da bản cong 4-inch: Khác biệt khi Squat và Deadlift',
            'nameen' => '10mm Straight Belt vs 4-Inch Contoured Belt',
            'slugvi' => 'dai-da-ban-thang-10mm-vs-dai-da-ban-cong-4-inch',
            'slugen' => '10mm-straight-vs-4-inch-contoured-belt',
            'photo' => 'so-sanh-dai-ban-thang-vs-ban-cong.jpg',
            'descvi' => 'So sánh chi tiết sự khác biệt về cơ sinh học chuyển động giữa đai lưng bản thẳng 10cm x dày 10mm và đai lưng vát cong công thái học 4 inch khi thực hiện Squat sâu và kéo Deadlift.',
            'contentvi' => <<<HTML
<h2>1. Khác biệt về hình học và phân bổ áp lực</h2>
<p>Thiết kế hình dáng đai ảnh hưởng trực tiếp đến cảm giác tiếp xúc giữa đai và khung xương sườn, mào chậu của bạn:</p>
<ul>
    <li><strong>Đai bản thẳng 10cm đều (Straight Belt):</strong> Cung cấp diện tích tiếp xúc lớn và áp lực gồng bụng đồng đều 360 độ quanh bụng và lưng. Thích hợp tối đa cho việc phát lực tạ cực đại.</li>
    <li><strong>Đai bản cong vát hông 4-inch (Contoured Belt):</strong> Phần lưng mở rộng 10cm nhưng hai bên hông được khoét lượn sóng, giúp tránh cấn vào xương sườn khi gập người sâu.</li>
</ul>

<h2>2. Lựa chọn khuyên dùng</h2>
<p>Nếu bạn cao trên 1m70 hoặc tập Powerlifting, hãy chọn đai bản thẳng như <a href="san-pham/dai-lung-khoa-don-bay-aolikes-lever-buckle-powerlifting-belt">Aolikes Lever Belt 10mm</a>. Nếu bạn có thân người ngắn hoặc ưu tiên sự êm ái khi tập Hypertrophy, hãy chọn đai bản cong như <a href="san-pham/dai-lung-da-harbinger-4-inch-padded-leather-belt">Harbinger 4-inch Padded Leather</a>.</p>
HTML
        ),

        // 3. Fabric Band vs Latex Band
        array(
            'namevi' => 'Dây kháng lực vải (Fabric Band) vs Dây kháng lực cao su (Latex Band): Ưu và nhược điểm',
            'nameen' => 'Fabric Resistance Bands vs Latex Rubber Bands',
            'slugvi' => 'day-khang-luc-vai-vs-day-khang-luc-cao-su',
            'slugen' => 'fabric-vs-latex-resistance-bands',
            'photo' => 'so-sanh-day-vai-vs-day-cao-su.jpg',
            'descvi' => 'Đối chiếu trực diện dây kháng lực vải dệt cotton và dây cao su thiên nhiên Latex: Đánh giá độ co giãn, khả năng chống cuộn xoắn, độ bám dính trên da và độ bền qua thời gian.',
            'contentvi' => <<<HTML
<h2>1. Bảng so sánh dây vải và dây cao su</h2>
<table border="1" cellpadding="8" style="border-collapse:collapse; width:100%; margin-bottom:20px;">
    <tr style="background:#f4f4f4;">
        <th>Đặc tính</th>
        <th>Dây kháng lực vải dệt</th>
        <th>Dây kháng lực cao su Latex</th>
    </tr>
    <tr>
        <td><strong>Hiện tượng cuộn xoắn</strong></td>
        <td>❌ Hoàn toàn KHÔNG cuộn gập</td>
        <td>⚠️ Dễ bị cuộn xoắn khi tập mông đùi</td>
    </tr>
    <tr>
        <td><strong>Cảm giác tiếp xúc da</strong></td>
        <td>Mềm mại, không rát, không kẹp lông chân</td>
        <td>Có thể rít da nếu ra nhiều mồ hôi</td>
    </tr>
    <tr>
        <td><strong>Độ co giãn biên độ</strong></td>
        <td>Giới hạn (khoảng 2x chiều dài)</td>
        <td>Rất cao (giãn 3-4x chiều dài)</td>
    </tr>
    <tr>
        <td><strong>Bài tập tối ưu</strong></td>
        <td>Tập mông đùi (Squat, Hip Thrust)</td>
        <td>Tập toàn thân, hỗ trợ xà, kéo giãn</td>
    </tr>
    <tr>
        <td><strong>Sản phẩm đề xuất</strong></td>
        <td><a href="san-pham/set-3-day-khang-luc-vai-aolikes-hip-resistance-band">Set dây vải Aolikes</a></td>
        <td><a href="san-pham/day-khang-luc-powerband-prosourcefit-cao-su-tu-nhien">Powerband ProsourceFit</a></td>
    </tr>
</table>
HTML
        ),

        // 4. Massage Gun vs Foam Roller
        array(
            'namevi' => 'Súng massage cơ bắp vs Con lăn Foam Roller: Nên đầu tư thiết bị nào để phục hồi?',
            'nameen' => 'Massage Gun vs Foam Roller Comparison',
            'slugvi' => 'sung-massage-co-bap-vs-con-lan-foam-roller',
            'slugen' => 'massage-gun-vs-foam-roller-comparison',
            'photo' => 'so-sanh-sung-massage-vs-foam-roller.jpg',
            'descvi' => 'So sánh thiết bị phục hồi cơ bắp: Đặt súng massage bộ gõ (Percussion Therapy) và con lăn bọt (Foam Rolling) lên bàn cân về hiệu quả giãn cơ, tính tiện dụng và ngân sách đầu tư.',
            'contentvi' => <<<HTML
<h2>1. Cơ chế tác động khác nhau như thế nào?</h2>
<ul>
    <li><strong>Con lăn Foam Roller:</strong> Tác động diện rộng nhờ trọng lượng cơ thể ép lên con lăn, giúp làm mềm toàn bộ dải cơ lớn và tăng độ linh hoạt khớp. Chi phí thấp, độ bền vĩnh cửu. Tham khảo: <a href="san-pham/con-lan-bot-gian-co-triggerpoint-grid-1-foam-roller">TriggerPoint GRID 1.0</a>.</li>
    <li><strong>Súng massage cơ bắp:</strong> Tác động cục bộ cực sâu bằng sóng xung kích lực đẩy, giúp đánh tan điểm kích hoạt đau (Trigger Points) chỉ trong vài giây mà bạn không cần phải nằm lăn lộn tốn sức. Tham khảo: <a href="san-pham/sung-massage-co-bap-cam-tay-booster-pro-3-high-power">Booster Pro 3</a> hoặc <a href="san-pham/sung-massage-mini-xiaomi-mijia-fascia-gun-mini">Xiaomi Mijia Mini</a>.</li>
</ul>

<h2>2. Lời khuyên đầu tư</h2>
<p>Nếu ngân sách dưới 1 triệu, Foam Roller là bước khởi đầu hoàn hảo. Nếu bạn có ngân sách dư dả và muốn sự tiện lợi tối đa khi ngồi ghế xem TV cũng có thể giãn cơ, hãy đầu tư thêm một chiếc súng massage.</p>
HTML
        ),

        // 5. Natural Rubber Mat vs TPE Mat
        array(
            'namevi' => 'Thảm tập cao su tự nhiên (Natural Rubber) vs Thảm xốp TPE: Đâu là lựa chọn tối ưu?',
            'nameen' => 'Natural Rubber vs TPE Yoga Mat Comparison',
            'slugvi' => 'tham-cao-su-tu-nhien-vs-tham-xop-tpe',
            'slugen' => 'natural-rubber-vs-tpe-yoga-mat',
            'photo' => 'so-sanh-tham-cao-su-vs-tpe.jpg',
            'descvi' => 'So sánh hai chất liệu thảm tập phổ biến nhất hiện nay: Cao su thiên nhiên phủ PU cao cấp và Xốp nhân tạo TPE về độ bám dính mồ hôi, trọng lượng di động và độ bền sử dụng.',
            'contentvi' => <<<HTML
<h2>1. So sánh chi tiết chất liệu</h2>
<ul>
    <li><strong>Thảm Cao su thiên nhiên phủ PU:</strong> Tiêu biểu là <a href="san-pham/tham-tap-dinh-tuyen-liforme-yoga-mat-4-2mm">Liforme Yoga Mat</a>. Độ bám dính mồ hôi là số 1 thế giới, không mùi nhựa độc hại, đầm chắc bảo vệ khớp. Tuy nhiên trọng lượng nặng (2.5kg) và giá thành cao.</li>
    <li><strong>Thảm Xốp nhân tạo TPE / PVC mật độ cao:</strong> Tiêu biểu là <a href="san-pham/tham-tap-the-duc-chong-truot-manduka-prolite-4-7mm">Manduka PROlite</a>. Trọng lượng nhẹ, chống thấm mồ hôi tốt, độ bền cao, giá thành hợp lý cho nhu cầu tập luyện đa năng hàng ngày.</li>
</ul>
HTML
        ),

        // 6. Gloves vs Lifting Straps
        array(
            'namevi' => 'Găng tay tập gym vs Dây kéo lưng (Lifting Straps): Khi nào nên dùng từng loại?',
            'nameen' => 'Gym Gloves vs Lifting Straps Comparison',
            'slugvi' => 'gang-tay-tap-gym-vs-day-keo-lung-lifting-straps',
            'slugen' => 'gym-gloves-vs-lifting-straps',
            'photo' => 'so-sanh-gang-tay-vs-lifting-straps.jpg',
            'descvi' => 'Làm rõ sự khác biệt và công năng của Găng tay thể hình so với Dây kéo lưng Lifting Straps: Đâu là phụ kiện chân ái cho buổi tập Push (Đẩy) và buổi tập Pull (Kéo)?',
            'contentvi' => <<<HTML
<h2>1. Hiểu đúng công năng để không dùng sai phụ kiện</h2>
<p>Rất nhiều người mới tập gym lầm tưởng găng tay có thể thay thế dây kéo lưng hoặc ngược lại. Đây là hai phụ kiện có nguyên lý phục vụ hoàn toàn khác nhau:</p>
<ul>
    <li><strong>Găng tay tập gym:</strong> Thiết kế để bảo vệ lòng bàn tay khỏi chai sần, đệm êm khi tì đè và quấn bảo vệ khớp cổ tay trong các bài <strong>ĐẨY (Push)</strong> như Bench Press, Dumbbell Shoulder Press. Tham khảo: <a href="san-pham/gang-tay-the-hinh-harbinger-pro-wristwrap-gloves">Harbinger Pro WristWrap</a>.</li>
    <li><strong>Dây kéo lưng Lifting Straps:</strong> Thiết kế chuyên biệt để khóa thanh đòn vào cổ tay trong các bài <strong>KÉO (Pull)</strong> như Deadlift, Row, Pulldown giúp loại bỏ giới hạn lực nắm của ngón tay. Tham khảo: <a href="san-pham/day-keo-lung-lifting-straps-harbinger-padded-cotton">Harbinger Cotton Straps</a> hoặc <a href="san-pham/day-keo-lung-so-8-figure-8-aolikes-heavy-duty-straps">Dây số 8 Aolikes</a>.</li>
</ul>
HTML
        )
    );

    foreach ($comparisons as $idx => $c) {
        $existComp = $d->rawQueryOne("SELECT id FROM #_news WHERE slugvi = ? AND type = 'tin-tuc' LIMIT 1", array($c['slugvi']));
        if (!empty($existComp['id'])) {
            $targetId = $existComp['id'];
            $d->rawQuery("UPDATE #_news SET 
                id_list = ?, 
                namevi = ?, 
                nameen = ?, 
                descvi = ?, 
                contentvi = ?, 
                photo = ?, 
                numb = ?, 
                status = 'hienthi,noibat', 
                date_updated = ? 
                WHERE id = ?", array(
                $idListComp, $c['namevi'], $c['nameen'], $c['descvi'], $c['contentvi'],
                $c['photo'], ($idx + 1), $now, $targetId
            ));
            echo "  - Updated comparison: {$c['namevi']} (ID: {$targetId})" . PHP_EOL;
        } else {
            $d->rawQuery("INSERT INTO #_news (
                id_list, namevi, nameen, slugvi, slugen, descvi, contentvi, photo,
                numb, status, type, date_created
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?,
                ?, 'hienthi,noibat', 'tin-tuc', ?
            )", array(
                $idListComp, $c['namevi'], $c['nameen'], $c['slugvi'], $c['slugen'], $c['descvi'], $c['contentvi'],
                $c['photo'], ($idx + 1), $now
            ));
            $inserted = $d->rawQueryOne("SELECT id FROM #_news WHERE slugvi = ? AND type = 'tin-tuc' LIMIT 1", array($c['slugvi']));
            $targetId = $inserted['id'];
            echo "  - Created comparison: {$c['namevi']} (ID: {$targetId})" . PHP_EOL;
        }

        // SEO for comparison
        $seoExist = $d->rawQueryOne("SELECT id FROM #_seo WHERE id_parent = ? AND com = 'news' AND act = 'man' AND type = 'tin-tuc' LIMIT 1", array($targetId));
        $seoTitle = "{$c['namevi']} | Khỏe Pro";
        $seoDesc = $c['descvi'];
        $seoKw = mb_strtolower($c['namevi'], 'UTF-8') . ", so sanh do tap gym, so sanh dung cu the thao, khoe pro";

        if (!empty($seoExist['id'])) {
            $d->rawQuery("UPDATE #_seo SET titlevi = ?, descriptionvi = ?, keywordsvi = ? WHERE id = ?", array(
                $seoTitle, $seoDesc, $seoKw, $seoExist['id']
            ));
        } else {
            $d->rawQuery("INSERT INTO #_seo (id_parent, com, act, type, titlevi, descriptionvi, keywordsvi, main_keywordsvi, main_keywordsen) VALUES (?, 'news', 'man', 'tin-tuc', ?, ?, ?, '', '')", array(
                $targetId, $seoTitle, $seoDesc, $seoKw
            ));
        }
    }

    echo "=== COMPLETED BATCH 6: PRODUCT COMPARISONS (6 HEAD-TO-HEAD ARTICLES) ===" . PHP_EOL;

} catch (Exception $e) {
    echo "ERROR in Batch 6: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
