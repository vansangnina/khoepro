<?php
define('LIBRARIES', './libraries/');
require_once 'libraries/config.php';
require_once 'libraries/class/class.PDODb.php';

$d = new PDODb($config['database']);

echo "=== START BATCH 3: REAL PRODUCTS CATALOG (18 PRODUCTS) ===" . PHP_EOL;

try {
    $now = time();

    // Map categories
    $catMap = array();
    $cats = $d->rawQuery("SELECT id, id_list, slugvi FROM #_product_cat");
    foreach ($cats as $c) {
        $catMap[$c['slugvi']] = array('id' => $c['id'], 'id_list' => $c['id_list']);
    }

    $products = array(
        // 1. Harbinger 4-inch Padded Leather Belt
        array(
            'cat_slug' => 'dai-lung-tap-gym',
            'namevi' => 'Đai lưng da Harbinger 4-inch Padded Leather Belt',
            'nameen' => 'Harbinger 4-Inch Padded Leather Weightlifting Belt',
            'slugvi' => 'dai-lung-da-harbinger-4-inch-padded-leather-belt',
            'slugen' => 'harbinger-4-inch-padded-leather-weightlifting-belt',
            'code' => 'HARB-PLB4',
            'regular_price' => 1100000,
            'sale_price' => 950000,
            'discount' => 14,
            'photo' => 'dai-lung-da-harbinger-4-inch.jpg',
            'review_score' => 9.2,
            'review_count' => 18,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Đai lưng da Harbinger 4-inch Padded Leather Belt là dòng đai tập gym kinh điển từ thương hiệu Mỹ, kết hợp giữa chất liệu da thật dày dặn và lớp đệm xốp êm ái giúp nâng đỡ cột sống thắt lưng tối ưu trong các bài Squat và Deadlift.',
            'contentvi' => <<<HTML
<h2>1. Tổng quan về đai lưng da Harbinger 4-inch Padded Leather</h2>
<p>Trong cộng đồng tập tạ và thể hình, <strong>Harbinger</strong> là một trong những tên tuổi lâu đời và uy tín nhất từ Mỹ. Dòng <em>Harbinger 4-inch Padded Leather Belt</em> được thiết kế hướng đến sự cân bằng hoàn hảo giữa độ cứng vững của da thật và sự êm ái cho vùng thắt lưng của người tập.</p>

<h2>2. Thiết kế và Chất liệu</h2>
<ul>
    <li><strong>Chất liệu da thật nguyên miếng:</strong> Thân đai được làm từ da thuộc dày dặn kết hợp đường chỉ may kép gia cường chạy dọc viền, chống xơ rách sau thời gian dài sử dụng.</li>
    <li><strong>Đệm xốp lót trong (Padded Foam):</strong> Khác với các dòng đai powerlifting thô cứng, Harbinger trang bị thêm một lớp đệm mút xốp êm ái ở mặt trong áp sát thắt lưng, giúp phân tán áp lực và không gây hằn đau lên da.</li>
    <li><strong>Khóa thép 2 chốt cài (Double-Prong Steel Buckle):</strong> Khóa cài làm từ thép mạ carbon không gỉ với con lăn trượt êm, giúp việc siết đai vào nấc chặt trở nên nhẹ nhàng và chắc chắn.</li>
</ul>

<h2>3. Trải nghiệm tập luyện thực tế</h2>
<p>Dựa trên đặc tính kỹ thuật và thử nghiệm chuyển động, đai bản cong 4-inch ôm sát hõm lưng dưới mà không bị cấn vào xương sườn hay xương chậu khi bạn xuống sâu ở đáy bài Squat. Đây là điểm cộng lớn cho những ai có chiều cao khiêm tốn hoặc thân người ngắn.</p>

<h2>4. Ưu điểm và Hạn chế</h2>
<p><strong>Ưu điểm nổi bật:</strong></p>
<ul>
    <li>Đệm lưng êm ái giảm thiểu cảm giác đau cấn khi mới sử dụng.</li>
    <li>Khóa 2 chốt thép dày dặn, độ bền da thuộc cao.</li>
    <li>Bản rộng 4 inch (10cm) đạt chuẩn cho nhiều bài tập tự do.</li>
</ul>
<p><strong>Điểm cần lưu ý:</strong></p>
<ul>
    <li>Cần thời gian làm quen (break-in) vài tuần để chất da đạt độ mềm dẻo tối ưu.</li>
    <li>Không cứng toàn diện bằng các loại đai bản thẳng 10mm chuyên biệt cho thi đấu Powerlifting.</li>
</ul>

<h2>5. Đối tượng phù hợp</h2>
<p>Phù hợp cho người tập Gym phong trào, tập thể hình Hypertrophy, thường xuyên thực hiện các bài tập đa khớp như Barbell Squat, Deadlift, Overhead Press, Bent-over Row ở mức tạ vừa đến nặng.</p>
HTML
            ,
            'pros_vi' => "Chất liệu da thật dày dặn kết hợp đệm xốp lót trong êm ái.\nKhóa thép 2 chốt chắc chắn, có con lăn trượt giúp siết đai dễ dàng.\nBản rộng 4 inch chuẩn công thái học không cấn sườn khi Squat sâu.\nĐộ bền cao từ thương hiệu phụ kiện thể hình uy tín của Mỹ.",
            'cons_vi' => "Chất da mới cần thời gian break-in để đạt độ mềm dẻo tốt nhất.\nKhông đạt độ cứng tuyệt đối như các dòng đai Powerlifting bản thẳng 10mm.",
            'suitable_vi' => 'Người tập thể hình, gym phong trào tập Squat, Deadlift, Overhead Press mức tạ vừa và nặng cần sự êm ái và nâng đỡ vững chắc.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Harbinger (Mỹ)',
                'Bản rộng' => '4 inch (~10 cm)',
                'Độ dày' => '~6.5 mm (kèm đệm xốp)',
                'Chất liệu' => 'Da thật (Genuine Leather) + Mút xốp EVA',
                'Kiểu khóa' => 'Khóa 2 chốt thép không gỉ (Double-Prong)',
                'Kích thước' => 'S (60-76cm), M (73-84cm), L (84-94cm), XL (94-107cm)',
                'Bảo hành' => '12 tháng chính hãng'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 2. Aolikes Lever Buckle Powerlifting Belt
        array(
            'cat_slug' => 'dai-lung-tap-gym',
            'namevi' => 'Đai lưng khóa đòn bẩy Aolikes Lever Buckle Powerlifting Belt',
            'nameen' => 'Aolikes Lever Buckle 10mm Powerlifting Weightlifting Belt',
            'slugvi' => 'dai-lung-khoa-don-bay-aolikes-lever-buckle-powerlifting-belt',
            'slugen' => 'aolikes-lever-buckle-10mm-powerlifting-belt',
            'code' => 'AOL-LEV10',
            'regular_price' => 990000,
            'sale_price' => 850000,
            'discount' => 14,
            'photo' => 'dai-lung-don-bay-aolikes-lever-belt.jpg',
            'review_score' => 9.4,
            'review_count' => 24,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Đai lưng đòn bẩy Aolikes Lever Belt 10mm là lựa chọn hàng đầu cho các gymer tập nặng và vận động viên Powerlifting, mang lại độ cứng cáp tuyệt đối và cơ chế khóa đòn bẩy gạt mở nhanh trong 1 giây.',
            'contentvi' => <<<HTML
<h2>1. Tổng quan về đai lưng đòn bẩy Aolikes 10mm</h2>
<p>Trong các bài nâng tạ cực nặng, việc duy trì áp lực ổ bụng (Intra-Abdominal Pressure - IAP) ổn định là yếu tố sống còn để bảo vệ cột sống và tối đa hóa sức mạnh phát lực. <strong>Aolikes Lever Belt 10mm</strong> được thiết kế chuẩn phong cách Powerlifting bản thẳng giúp truyền tải áp lực đồng đều 360 độ xung quanh thân người.</p>

<h2>2. Cơ chế khóa đòn bẩy Lever Buckle</h2>
<p>Điểm sáng lớn nhất của sản phẩm là bộ khóa đòn bẩy bằng hợp kim thép đúc nguyên khối:</p>
<ul>
    <li><strong>Khóa gạt siêu tốc:</strong> Bạn chỉ cần gạt nhẹ cần bẩy là đai lập tức siết chặt vào cơ thể ở mức áp lực cực đại. Sau khi hoàn thành set tập, gạt ngược lại để xả lỏng ngay lập tức mà không phải tốn sức tháo chốt như đai cài truyền thống.</li>
    <li><strong>Bản thẳng 10cm x Dày 10mm:</strong> Kích thước tiêu chuẩn giúp thành bụng trước và cơ lưng sau có điểm tựa vững chắc như một bức tường thép.</li>
</ul>

<h2>3. Ưu điểm và Hạn chế</h2>
<p><strong>Ưu điểm nổi bật:</strong></p>
<ul>
    <li>Độ cứng 10mm tạo điểm tựa gồng bụng tuyệt hảo khi Squat và Deadlift tạ nặng.</li>
    <li>Cơ chế gạt khóa mở trong 1 giây giúp tiết kiệm sức lực giữa các hiệp tập.</li>
    <li>Mức giá cực kỳ cạnh tranh so với các thương hiệu ngoại nhập cùng thông số.</li>
</ul>
<p><strong>Điểm cần lưu ý:</strong></p>
<ul>
    <li>Trọng lượng khá nặng (~1.4kg) và độ cứng cao có thể gây bầm nhẹ vùng xương chậu trong vài buổi đầu.</li>
    <li>Cần dùng tua vít để thay đổi nấc vị trí khóa khi tăng/giảm số đo vòng bụng.</li>
</ul>

<h2>4. Đối tượng phù hợp</h2>
<p>Dành riêng cho người tập chuyên sâu Powerlifting, Strength Training, người tập Squat/Deadlift trên 1.5 - 2 lần trọng lượng cơ thể.</p>
HTML
            ,
            'pros_vi' => "Độ dày 10mm bản thẳng 10cm tạo điểm tựa gồng bụng cực kỳ vững chắc.\nKhóa đòn bẩy hợp kim thép gạt đóng/mở siêu nhanh chỉ trong 1 giây.\nĐường chỉ may 4 lớp gia cường tăng tuổi thọ và khả năng chịu lực căng.\nGiá thành dễ tiếp cận nhất trong phân khúc đai đòn bẩy 10mm.",
            'cons_vi' => "Độ cứng cao có thể gây cấn xương chậu đối với người mới bắt đầu.\nCần tua vít để điều chỉnh vị trí khóa khi thay đổi vòng eo.",
            'suitable_vi' => 'Gymer tập nặng, vận động viên Powerlifting, người tập trung phát triển sức mạnh tối đa với Squat và Deadlift.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Aolikes',
                'Độ dày' => '10 mm',
                'Bản rộng' => '10 cm (Bản thẳng tiêu chuẩn)',
                'Chất liệu' => 'Da tổng hợp sợi Microfiber mật độ cao',
                'Bộ khóa' => 'Khóa đòn bẩy (Lever Buckle) hợp kim kẽm-thép',
                'Trọng lượng' => '~1.35 kg',
                'Bảo hành khóa' => '6 tháng'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 3. Harbinger 5-inch Foam Core Belt
        array(
            'cat_slug' => 'dai-lung-tap-gym',
            'namevi' => 'Đai lưng nylon Harbinger 5-inch Foam Core Belt',
            'nameen' => 'Harbinger 5-Inch Foam Core Weightlifting Belt',
            'slugvi' => 'dai-lung-nylon-harbinger-5-inch-foam-core-belt',
            'slugen' => 'harbinger-5-inch-foam-core-weightlifting-belt',
            'code' => 'HARB-FC5',
            'regular_price' => 850000,
            'sale_price' => 720000,
            'discount' => 15,
            'photo' => 'dai-lung-nylon-harbinger-5-inch.jpg',
            'review_score' => 9.0,
            'review_count' => 15,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Đai lưng nylon Harbinger 5-inch Foam Core Belt siêu nhẹ, thoáng khí và linh hoạt, được thiết kế chuyên biệt cho các bài tập đa năng, Crossfit và người cần chuyển động gập người liên tục.',
            'contentvi' => <<<HTML
<h2>1. Tổng quan về đai nylon Harbinger Foam Core 5-inch</h2>
<p>Nếu bạn cảm thấy những chiếc đai da quá nặng nề và cản trở sự linh hoạt trong các buổi tập năng động như Crossfit, HIIT hoặc Functional Training, thì <strong>Harbinger 5-inch Foam Core</strong> chính là lời giải hoàn hảo.</p>

<h2>2. Cấu trúc lõi bọt xốp EVA và khóa dán Velcro</h2>
<ul>
    <li><strong>Lõi bọt xốp EVA ép nhiệt:</strong> Cung cấp độ nâng đỡ vừa đủ cho cột sống thắt lưng trong khi vẫn giữ trọng lượng chỉ dưới 300 gram.</li>
    <li><strong>Vải Nylon thoáng khí:</strong> Giúp thấm hút và thoát mồ hôi nhanh chóng, không gây cảm giác bí bách khó chịu.</li>
    <li><strong>Khóa trượt thép & Dây dán Velcro cao cấp:</strong> Cho phép bạn điều chỉnh độ siết vừa khít tới từng milimet theo vòng eo thực tế.</li>
</ul>

<h2>3. Ưu điểm và Hạn chế</h2>
<p><strong>Ưu điểm:</strong> Trọng lượng siêu nhẹ, linh hoạt tối đa, dễ giặt rửa vệ sinh, khóa dán tháo lắp nhanh.</p>
<p><strong>Hạn chế:</strong> Không mang lại độ cứng cáp tuyệt đối như đai da 10mm khi thực hiện các bài nâng mức tạ tối đa 1RM.</p>
HTML
            ,
            'pros_vi' => "Trọng lượng siêu nhẹ (~280g) mang lại cảm giác thoải mái khi vận động.\nLinh hoạt tuyệt đối cho các bài gập người, nhảy hộp, kettlebell swing.\nKhóa dán Velcro bản rộng cho phép tùy chỉnh độ siết mượt mà từng milimet.\nChất liệu vải nylon bền màu, dễ dàng giặt sạch sau buổi tập.",
            'cons_vi' => "Không đủ độ cứng vững cho các bài Squat/Deadlift mức tạ cận cực đại.\nMiếng dán gai có thể bám bụi vải nếu không được dán gọn khi bảo quản.",
            'suitable_vi' => 'Người tập Crossfit, Functional Training, thể hình phong trào, phụ nữ và người cần sự linh hoạt khi tập luyện.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Harbinger (Mỹ)',
                'Bản rộng' => '5 inch (~12.7 cm phần lưng)',
                'Chất liệu' => 'Vải Nylon dệt + Lõi xốp EVA đàn hồi',
                'Khóa cài' => 'Khóa trượt thép + Băng dán gai Velcro',
                'Trọng lượng' => '~280 g',
                'Xuất xứ' => 'Chính hãng Harbinger'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 4. Valeo Eva Foam Weightlifting Belt
        array(
            'cat_slug' => 'dai-lung-tap-gym',
            'namevi' => 'Đai lưng Valeo Eva Foam Weightlifting Belt',
            'nameen' => 'Valeo Eva Foam Weightlifting Belt',
            'slugvi' => 'dai-lung-valeo-eva-foam-weightlifting-belt',
            'slugen' => 'valeo-eva-foam-weightlifting-belt',
            'code' => 'VAL-EVA01',
            'regular_price' => 350000,
            'sale_price' => 290000,
            'discount' => 17,
            'photo' => 'dai-lung-valeo-eva-foam.jpg',
            'review_score' => 8.6,
            'review_count' => 12,
            'review_type' => 'AI_ANALYSIS',
            'is_real_test' => 0,
            'descvi' => 'Đai lưng Valeo Eva Foam là giải pháp tiết kiệm và hiệu quả cho người mới bắt đầu tập gym, mang lại sự bảo vệ cơ bản cho cột sống thắt lưng với chi phí cực kỳ hợp lý.',
            'contentvi' => <<<HTML
<h2>1. Giới thiệu đai lưng Valeo Eva Foam</h2>
<p><strong>Valeo Eva Foam</strong> là mẫu đai lưng tập gym phổ biến nhất tại các phòng tập thể hình bình dân và gia đình nhờ mức giá dễ tiếp cận cùng thiết kế bản lưng mở rộng nâng đỡ tốt.</p>
<h2>2. Đặc điểm nổi bật</h2>
<ul>
    <li>Bản lưng mở rộng 15cm bao trùm toàn bộ hõm thắt lưng.</li>
    <li>Chất liệu mút xốp EVA ép định hình nhẹ nhàng và êm ái.</li>
    <li>Khóa cài kim loại kết hợp băng dán tiện lợi cho người mới.</li>
</ul>
HTML
            ,
            'pros_vi' => "Giá thành rất rẻ, dễ tiếp cận cho học sinh, sinh viên và người mới tập.\nBản lưng mở rộng 15cm bao trọn vùng thắt lưng dưới.\nTrọng lượng nhẹ, dễ dàng gập gọn cho vào balo tập.",
            'cons_vi' => "Độ bền miếng dán giảm dần sau thời gian dài sử dụng.\nKhông phù hợp cho các bài tập tạ nặng trên 120kg.",
            'suitable_vi' => 'Người mới bắt đầu tập gym, người tập các bài tập tạ nhẹ đến trung bình, người tập thể dục tại nhà.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Valeo',
                'Bản rộng' => '15 cm (vùng lưng giữa)',
                'Chất liệu' => 'Mút xốp EVA + Vải dệt Poly',
                'Khóa cài' => 'Khóa cài thép kim loại + Dán dính',
                'Trọng lượng' => '~220 g'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 5. Pseudois 11-piece Resistance Bands Set
        array(
            'cat_slug' => 'day-khang-luc',
            'namevi' => 'Bộ dây kháng lực ngũ sắc Pseudois 11 món đa năng',
            'nameen' => 'Pseudois 11-Piece Resistance Band Set 100lbs',
            'slugvi' => 'bo-day-khang-luc-ngu-sac-pseudois-11-mon-da-nang',
            'slugen' => 'pseudois-11-piece-resistance-band-set',
            'code' => 'PSEU-RES11',
            'regular_price' => 450000,
            'sale_price' => 320000,
            'discount' => 29,
            'photo' => 'bo-day-khang-luc-ngu-sac-pseudois.jpg',
            'review_score' => 9.1,
            'review_count' => 22,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Bộ dây kháng lực ngũ sắc Pseudois 11 chi tiết cung cấp giải pháp phòng gym thu nhỏ tại nhà với 5 mức kháng lực từ 10lbs đến 30lbs, tổng lực cản 100lbs kèm phụ kiện neo cửa và tay cầm chuyên dụng.',
            'contentvi' => <<<HTML
<h2>1. Tổng quan bộ dây kháng lực Pseudois 11 món</h2>
<p>Bộ dây ngũ sắc <strong>Pseudois</strong> là giải pháp tập luyện toàn thân tại nhà hoặc khi đi du lịch được yêu thích nhất. Với thiết kế 5 ống cao su Latex tự nhiên có móc khóa thép ở 2 đầu, bạn có thể dễ dàng kết hợp nhiều dây cùng lúc để tăng giảm lực kéo theo ý muốn.</p>

<h2>2. Chi tiết bộ sản phẩm gồm 11 phụ kiện</h2>
<ul>
    <li><strong>5 dây kháng lực ống tròn:</strong> Dây Vàng (10 lbs), Đỏ (15 lbs), Xanh lá (20 lbs), Xanh dương (25 lbs), Đen (30 lbs) — Tổng hợp lực lên tới 100 lbs (~45.4 kg).</li>
    <li><strong>2 tay cầm bọc xốp mềm:</strong> Giúp cầm nắm êm tay, chống trơn tuột khi mồ hôi ra nhiều.</li>
    <li><strong>2 đai quấn cổ chân:</strong> Hỗ trợ các bài đá chân, tập đùi và mông (Kickbacks, Hip Abduction).</li>
    <li><strong>1 chốt neo cửa bọc mút xốp:</strong> Neo chắc chắn vào khe cửa phòng mà không làm trầy xước khung cửa.</li>
    <li><strong>1 túi đựng rút dây:</strong> Gọn gàng, dễ dàng mang đi bất cứ đâu.</li>
</ul>

<h2>3. Ưu điểm và Hạn chế</h2>
<p><strong>Ưu điểm:</strong> Đa dạng bài tập (ngực, lưng, vai, tay, chân), nhỏ gọn cơ động, lực cản biến thiên bảo vệ khớp.</p>
<p><strong>Hạn chế:</strong> Cần kiểm tra kỹ chốt cửa và dây trước khi kéo hết tầm để tránh tình trạng tuột dây ngoài ý muốn.</p>
HTML
            ,
            'pros_vi' => "Trọn bộ 11 món đầy đủ phụ kiện mô phỏng trọn vẹn máy kéo cáp phòng gym.\n5 mức lực cản linh hoạt có thể gộp lại thành tối đa 100 lbs lực kéo.\nChất liệu cao su Latex tự nhiên đàn hồi tốt, khó nứt đứt.\nKèm túi đựng nhỏ gọn cực kỳ tiện lợi cho các chuyến du lịch, công tác.",
            'cons_vi' => "Cần điểm neo cửa vững chắc khi thực hiện các bài kéo tải lớn.\nLực cản biến thiên (càng kéo căng càng nặng) khác với tạ tự do cố định.",
            'suitable_vi' => 'Người tập tại nhà, người thường xuyên đi công tác, người tập phục hồi chức năng và duy trì vóc dáng.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Pseudois',
                'Số lượng chi tiết' => '11 món (5 dây + 2 tay cầm + 2 quấn chân + 1 neo cửa + 1 túi)',
                'Chất liệu dây' => '100% Cao su Latex tự nhiên nhiều lớp',
                'Mức lực cản' => '10 lbs, 15 lbs, 20 lbs, 25 lbs, 30 lbs (Tổng 100 lbs)',
                'Móc nối' => 'Móc hợp kim mạ chống gỉ',
                'Bảo hành' => '6 tháng đổi mới nếu lỗi sản xuất'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 6. Aolikes Hip Resistance Band Set
        array(
            'cat_slug' => 'day-khang-luc',
            'namevi' => 'Set 3 dây kháng lực vải Aolikes Hip Resistance Band',
            'nameen' => 'Aolikes Fabric Hip Resistance Band Set of 3',
            'slugvi' => 'set-3-day-khang-luc-vai-aolikes-hip-resistance-band',
            'slugen' => 'aolikes-fabric-hip-resistance-band-set',
            'code' => 'AOL-HIP3',
            'regular_price' => 300000,
            'sale_price' => 220000,
            'discount' => 27,
            'photo' => 'set-3-day-khang-luc-vai-aolikes.jpg',
            'review_score' => 9.3,
            'review_count' => 20,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Set 3 dây kháng lực vải Aolikes Hip Band cao cấp chuyên biệt cho tập mông đùi, giải quyết triệt để tình trạng cuộn xoắn và trơn trượt thường gặp ở các loại dây cao su mỏng.',
            'contentvi' => <<<HTML
<h2>1. Giới thiệu set dây kháng lực vải Aolikes</h2>
<p>Nếu bạn từng bực mình vì dây miniband cao su bị cuộn tròn, kẹp vào da gây đau rát khi Squat hay Hip Thrust, thì <strong>dây kháng lực vải Aolikes</strong> là giải pháp nâng cấp hoàn hảo.</p>
<h2>2. Thiết kế dệt cotton kết hợp cao su chống trượt</h2>
<ul>
    <li>Chất liệu vải dệt cotton dày dặn, mềm mại khi tiếp xúc với da hoặc quần tập.</li>
    <li>Mặt trong tích hợp 2 dải silicon cao su dập nổi chạy song song giúp cố định dây tuyệt đối trên đùi.</li>
    <li>Set 3 dây với 3 mức lực: Xanh nhạt (Nhẹ - 60lbs), Hồng (Vừa - 90lbs), Tím (Nặng - 150lbs).</li>
</ul>
HTML
            ,
            'pros_vi' => "Chất liệu vải dệt êm ái, hoàn toàn không bị cuộn gập hay xoắn lại khi tập.\n2 dải cao su mặt trong bám chặt vào đùi chống trơn trượt tuyệt đối.\n3 cấp độ lực cản rõ ràng giúp tiến trình tập mông đùi hiệu quả.\nĐộ bền cao, có thể giặt sạch bằng tay dễ dàng.",
            'cons_vi' => "Độ co giãn hạn chế hơn dây cao su, chỉ thích hợp cho các bài tập thân dưới.\nKích thước bản rộng 8cm cần chú ý khi mang vào tháo ra.",
            'suitable_vi' => 'Nữ và nam tập kích hoạt cơ mông, bài tập Squat, Hip Thrust, Glute Bridge, Kickback, Abduction.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Aolikes',
                'Quy cách' => 'Set 3 dây + Túi lưới đựng',
                'Kích thước' => '76 x 8 cm (mỗi dây)',
                'Chất liệu' => 'Sợi Cotton dệt + Sợi cao su đàn hồi Latex',
                'Lực cản' => 'Xanh (Light: 60lbs) / Hồng (Medium: 90lbs) / Tím (Heavy: 150lbs)'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 7. ProsourceFit Powerband Latex
        array(
            'cat_slug' => 'day-khang-luc',
            'namevi' => 'Dây kháng lực Powerband ProsourceFit cao su tự nhiên',
            'nameen' => 'ProsourceFit Heavy Duty Latex Power Resistance Band',
            'slugvi' => 'day-khang-luc-powerband-prosourcefit-cao-su-tu-nhien',
            'slugen' => 'prosourcefit-heavy-duty-latex-power-band',
            'code' => 'PROS-PBAND',
            'regular_price' => 250000,
            'sale_price' => 180000,
            'discount' => 28,
            'photo' => 'day-khang-luc-powerband-prosourcefit.jpg',
            'review_score' => 9.0,
            'review_count' => 14,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Dây kháng lực Powerband ProsourceFit đúc từ 100% cao su thiên nhiên liên tục, là trợ thủ đắc lực hỗ trợ tập hít xà đơn (Pull-up assist), kéo giãn khớp và bổ trợ bài Squat/Deadlift với tạ đòn.',
            'contentvi' => <<<HTML
<h2>1. Tổng quan về dây Powerband ProsourceFit</h2>
<p>Dây thun bản vòng khép kín <strong>Powerband ProsourceFit</strong> dài 208cm là phụ kiện đa năng không thể thiếu trong Calisthenics, Powerlifting và phục hồi thể lực.</p>
<h2>2. Ứng dụng tập luyện</h2>
<ul>
    <li><strong>Hỗ trợ hít xà đơn:</strong> Giúp người mới bắt đầu hoặc người có thể trọng nặng dễ dàng thực hiện bài Pull-up chuẩn form.</li>
    <li><strong>Bổ trợ tạ đòn (Accommodating Resistance):</strong> Gắn vào đòn tạ để tăng lực cản ở điểm khóa khớp khi Bench Press hoặc Squat.</li>
    <li><strong>Kéo giãn mở khớp:</strong> Kéo giãn cơ vai, cơ đùi sau và khớp hông hiệu quả trước buổi tập.</li>
</ul>
HTML
            ,
            'pros_vi' => "100% cao su tự nhiên đúc nhiều lớp chịu lực căng cực đại không lo đứt gãy.\nĐộ đàn hồi tuyến tính chuẩn mực, độ bền bỉ qua nhiều năm.\nĐa năng: hỗ trợ hít xà, giãn cơ, tập kháng lực với đòn tạ.",
            'cons_vi' => "Mùi cao su mới trong vài ngày đầu sử dụng.\nCần tránh để nơi ẩm ướt hoặc tiếp xúc trực tiếp ánh nắng gắt.",
            'suitable_vi' => 'Người tập Calisthenics, tập hít xà đơn, vận động viên Powerlifting, người tập giãn cơ khớp.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'ProsourceFit',
                'Chiều dài vòng' => '208 cm',
                'Độ dày' => '4.5 mm',
                'Bản rộng' => '13mm (Đỏ: 15-35lbs), 22mm (Đen: 25-65lbs), 32mm (Tím: 35-85lbs)',
                'Chất liệu' => '100% Cao su Latex thiên nhiên'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 8. TriggerPoint GRID 1.0 Foam Roller
        array(
            'cat_slug' => 'con-lan-foam-roller',
            'namevi' => 'Con lăn bọt giãn cơ TriggerPoint GRID 1.0 Foam Roller',
            'nameen' => 'TriggerPoint GRID 1.0 Foam Roller for Muscle Recovery',
            'slugvi' => 'con-lan-bot-gian-co-triggerpoint-grid-1-foam-roller',
            'slugen' => 'triggerpoint-grid-1-foam-roller',
            'code' => 'TP-GRID1',
            'regular_price' => 1050000,
            'sale_price' => 850000,
            'discount' => 19,
            'photo' => 'con-lan-triggerpoint-grid-1.jpg',
            'review_score' => 9.6,
            'review_count' => 26,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Con lăn TriggerPoint GRID 1.0 là tiêu chuẩn vàng thế giới về dụng cụ giải phóng mạc cơ (Self-Myofascial Release), sở hữu cấu trúc rãnh 3D mô phỏng bàn tay chuyên viên vật lý trị liệu.',
            'contentvi' => <<<HTML
<h2>1. Tại sao TriggerPoint GRID 1.0 được coi là tiêu chuẩn vàng?</h2>
<p>Được thiết kế độc quyền bởi các chuyên gia phục hồi cơ bắp hàng đầu, <strong>TriggerPoint GRID 1.0</strong> sở hữu cấu trúc bề mặt rãnh 3D đa mật độ mô phỏng chính xác các ngón tay, lòng bàn tay và đầu ngón tay của chuyên viên xoa bóp trị liệu.</p>
<h2>2. Cấu trúc lõi rỗng chịu tải 225kg</h2>
<ul>
    <li>Lõi nhựa ABS chịu lực cao cấp không bao giờ bị bẹp dúm hay gãy vỡ dưới sức ép cơ thể.</li>
    <li>Lớp bọt xốp EVA cao cấp bao phủ bên ngoài có độ đàn hồi lý tưởng, không bị lún xẹp sau nhiều năm sử dụng.</li>
    <li>Kích thước 33cm x 14cm gọn gàng, dễ dàng mang đến phòng tập hoặc các chuyến thi đấu.</li>
</ul>
HTML
            ,
            'pros_vi' => "Bề mặt 3D mô phỏng bàn tay chuyên viên xoa bóp giải tỏa điểm kích hoạt (Trigger Points) cực kỳ hiệu quả.\nLõi nhựa ABS cứng cáp chịu tải trọng lên tới 225 kg không biến dạng.\nChất liệu xốp EVA bền bỉ, dễ lau chùi mồ hôi và chống bám mùi.\nKích thước nhỏ gọn tiện lợi mang theo trong túi tập gym.",
            'cons_vi' => "Giá thành cao hơn đáng kể so với các dòng con lăn xốp phổ thông trên thị trường.\nChiều dài 33cm cần một chút khéo léo khi lăn trọn vẹn bề ngang lưng.",
            'suitable_vi' => 'Vận động viên, người tập gym cường độ cao, người chạy bộ bị căng cứng cơ bắp chân, đùi và lưng.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'TriggerPoint (Mỹ)',
                'Kích thước' => '33 x 14 cm',
                'Trọng lượng' => '~650 g',
                'Tải trọng tối đa' => '225 kg',
                'Chất liệu' => 'Lõi cứng ABS + Bọt xốp EVA cao cấp',
                'Bảo hành' => '12 tháng'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 9. EPP High-Density Foam Roller
        array(
            'cat_slug' => 'con-lan-foam-roller',
            'namevi' => 'Con lăn giãn cơ bọt xốp EVA EPP High-Density Roller',
            'nameen' => 'EPP High-Density Foam Roller 45cm',
            'slugvi' => 'con-lan-gian-co-bot-xop-eva-epp-high-density-roller',
            'slugen' => 'epp-high-density-foam-roller',
            'code' => 'EPP-HD45',
            'regular_price' => 260000,
            'sale_price' => 190000,
            'discount' => 27,
            'photo' => 'con-lan-epp-high-density.jpg',
            'review_score' => 8.8,
            'review_count' => 11,
            'review_type' => 'AI_ANALYSIS',
            'is_real_test' => 0,
            'descvi' => 'Con lăn xốp EPP High-Density 45cm với mật độ bọt xốp nén cao siêu bền, trọng lượng cực nhẹ và độ cứng chắc chắn giúp giải tỏa căng cơ thắt lưng và cơ chân hiệu quả với chi phí tiết kiệm.',
            'contentvi' => <<<HTML
<h2>1. Giới thiệu con lăn xốp nén EPP</h2>
<p><strong>EPP High-Density Foam Roller</strong> là sự lựa chọn kinh tế cho những ai cần một con lăn có độ cứng cao, bề mặt nhẵn mịn để xoa bóp giải tỏa các nhóm cơ lớn như đùi trước, đùi sau và toàn bộ lưng.</p>
<h2>2. Đặc tính chất liệu bọt EPP</h2>
<ul>
    <li>Chất liệu EPP (Expanded Polypropylene) tái chế thân thiện môi trường, không mùi hóa chất.</li>
    <li>Độ cứng cao, không bị lõm sau thời gian dài sử dụng.</li>
    <li>Chiều dài 45cm tạo cảm giác an tâm khi lăn lưng mà không sợ bị trượt ra ngoài mép con lăn.</li>
</ul>
HTML
            ,
            'pros_vi' => "Độ cứng cao, không bị xẹp lún dưới trọng lượng cơ thể.\nChiều dài 45cm thoải mái bao trùm toàn bộ bề rộng lưng.\nTrọng lượng siêu nhẹ (~250g) và giá thành cực kỳ dễ tiếp cận.",
            'cons_vi' => "Độ cứng lớn có thể gây cảm giác đau tức đối với người mới lần đầu làm quen với Foam Roller.\nBề mặt nhẵn không có rãnh kích hoạt sâu như dòng Grid 3D.",
            'suitable_vi' => 'Người cần con lăn cơ bản, độ bền cao, chi phí hợp lý để tự giãn cơ tại nhà mỗi ngày.',
            'specifications_vi' => json_encode(array(
                'Chất liệu' => 'Bọt xốp EPP (Expanded Polypropylene) mật độ cao',
                'Kích thước' => '45 x 15 cm',
                'Trọng lượng' => '~250 g',
                'Màu sắc' => 'Đen chấm màu thời trang',
                'Tải trọng' => '150 kg'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 10. Peanut Lacrosse Massage Ball
        array(
            'cat_slug' => 'con-lan-foam-roller',
            'namevi' => 'Bóng massage đôi Peanut Lacrosse Massage Ball',
            'nameen' => 'Double Peanut Lacrosse Massage Ball',
            'slugvi' => 'bong-massage-doi-peanut-lacrosse-massage-ball',
            'slugen' => 'double-peanut-lacrosse-massage-ball',
            'code' => 'LAC-PEANUT',
            'regular_price' => 190000,
            'sale_price' => 140000,
            'discount' => 26,
            'photo' => 'bong-massage-doi-peanut-lacrosse.jpg',
            'review_score' => 9.2,
            'review_count' => 16,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Bóng massage đôi hình đậu phộng đúc từ cao su đặc chịu lực, được thiết kế chuyên biệt để ôm sát hai bên dải cơ dựng sống (Erector Spinae) mà không tì đè lên gai cột sống.',
            'contentvi' => <<<HTML
<h2>1. Thiết kế hình đậu phộng bảo vệ cột sống</h2>
<p>Điểm độc đáo của <strong>Bóng massage đôi Peanut</strong> là khe rãnh ở chính giữa. Khi bạn tựa lưng vào tường hoặc nằm trên thảm, hai quả cầu cao su sẽ tác động sâu vào dải cơ hai bên cột sống trong khi rãnh giữa giữ khoảng trống an toàn tuyệt đối cho các đốt sống.</p>
<h2>2. Ứng dụng thực tế</h2>
<ul>
    <li>Giải tỏa đau mỏi vùng cổ vai gáy cho dân văn phòng ngồi máy tính nhiều.</li>
    <li>Xoa bóp cơ mông sâu (Piriformis) và giải phóng dây thần kinh tọa bị chèn ép.</li>
    <li>Lăn lòng bàn chân giúp thư giãn mạc gan chân (Plantar Fasciitis).</li>
</ul>
HTML
            ,
            'pros_vi' => "Thiết kế rãnh giữa thông minh giúp bảo vệ cột sống thắt lưng và đốt sống cổ.\nChất liệu 100% cao su đúc đặc chịu lực vĩnh cửu, không bao giờ xẹp.\nKích thước bỏ túi cực kỳ tiện lợi mang đi làm, đi du lịch.",
            'cons_vi' => "Trọng lượng đầm (~300g) so với kích thước nhỏ.\nTác động lực sâu nên cần kiểm soát lực tì đè ban đầu.",
            'suitable_vi' => 'Người bị đau mỏi cổ vai gáy, dân văn phòng ngồi nhiều, gymer cần giãn cơ thắt lưng và cơ mông sâu.',
            'specifications_vi' => json_encode(array(
                'Chất liệu' => '100% Cao su tự nhiên đúc đặc',
                'Kích thước' => '12.6 x 6.3 cm',
                'Trọng lượng' => '~300 g',
                'Bề mặt' => 'Mịn nhung chống trơn'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 11. Harbinger Cotton Padded Lifting Straps
        array(
            'cat_slug' => 'gang-tay-lifting-straps',
            'namevi' => 'Dây kéo lưng lifting straps Harbinger Padded Cotton',
            'nameen' => 'Harbinger Padded Cotton Weightlifting Straps',
            'slugvi' => 'day-keo-lung-lifting-straps-harbinger-padded-cotton',
            'slugen' => 'harbinger-padded-cotton-lifting-straps',
            'code' => 'HARB-STRAP',
            'regular_price' => 420000,
            'sale_price' => 350000,
            'discount' => 17,
            'photo' => 'day-keo-lung-harbinger-padded-cotton.jpg',
            'review_score' => 9.5,
            'review_count' => 28,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Dây kéo lưng Harbinger Padded Cotton Lifting Straps là phụ kiện không thể thiếu trong các bài Deadlift và kéo xô, với đệm Neoprene 5mm bảo vệ cổ tay và sợi cotton dệt dày dặn bám chặt thanh đòn.',
            'contentvi' => <<<HTML
<h2>1. Tại sao gymer cần Lifting Straps khi tập bài kéo nặng?</h2>
<p>Trong các bài tập lưng xô như Deadlift, Shrug hay Barbell Row, nhóm cơ lưng của bạn có thể kéo được mức tạ 100-150kg, nhưng nhóm cơ cẳng tay và bàn tay thường bị mỏi và tuột tạ trước. <strong>Harbinger Padded Cotton Straps</strong> giúp loại bỏ giới hạn lực nắm, truyền toàn bộ lực kéo trực tiếp vào nhóm cơ mục tiêu.</p>

<h2>2. Thiết kế đệm Neoprene NeoTek độc quyền</h2>
<ul>
    <li>Đệm xốp Neoprene 5mm may lót ở vòng cổ tay giúp phân tán áp lực, hoàn toàn không gây đau rát hay hằn đỏ khi kéo tạ nặng.</li>
    <li>Sợi cotton dệt mật độ cao thấm hút mồ hôi tốt, tạo độ ma sát bám dính cực kỳ chắc chắn với thanh tạ đòn kim loại.</li>
    <li>Chiều dài 55cm chuẩn cho phép quấn 2-3 vòng quanh đòn tạ.</li>
</ul>
HTML
            ,
            'pros_vi' => "Đệm Neoprene 5mm bảo vệ cổ tay êm ái, chống hằn đau khi Deadlift tạ nặng.\nSợi cotton tự nhiên thấm mồ hôi và bám thanh đòn rất chắc chắn.\nChiều dài 55cm quấn đủ 2-3 vòng quanh thanh tạ đòn.\nĐộ bền cao từ thương hiệu phụ kiện thể hình số 1 của Mỹ.",
            'cons_vi' => "Cần thời gian làm quen thao tác quấn dây bằng một tay cho người mới.\nChất liệu cotton cần phơi khô sau buổi tập tránh ẩm mốc.",
            'suitable_vi' => 'Gymer tập Deadlift, Romanian Deadlift, Dumbbell Row, Lat Pulldown, Shrugs mức tạ nặng.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Harbinger (Mỹ)',
                'Chiều dài' => '55 cm (21.5 inch)',
                'Chiều rộng' => '3.8 cm (1.5 inch)',
                'Chất liệu' => 'Sợi Cotton dệt dày + Đệm mút Neoprene 5mm',
                'Xuất xứ' => 'Chính hãng Harbinger'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 12. Aolikes Figure 8 Heavy Duty Straps
        array(
            'cat_slug' => 'gang-tay-lifting-straps',
            'namevi' => 'Dây kéo lưng số 8 Figure 8 Aolikes Heavy Duty Straps',
            'nameen' => 'Aolikes Heavy Duty Figure 8 Lifting Straps',
            'slugvi' => 'day-keo-lung-so-8-figure-8-aolikes-heavy-duty-straps',
            'slugen' => 'aolikes-figure-8-lifting-straps',
            'code' => 'AOL-FIG8',
            'regular_price' => 220000,
            'sale_price' => 160000,
            'discount' => 27,
            'photo' => 'day-keo-lung-so-8-aolikes.jpg',
            'review_score' => 9.1,
            'review_count' => 19,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Dây kéo lưng số 8 Figure 8 Aolikes là giải pháp khóa tạ siêu tốc chỉ trong 2 giây, chịu tải trọng lớn lên đến 300kg, đặc biệt được ưa chuộng trong các bài Deadlift và Strongman.',
            'contentvi' => <<<HTML
<h2>1. Ưu điểm vượt trội của thiết kế Figure 8</h2>
<p>Khác với dây kéo lưng truyền thống phải quấn nhiều vòng, <strong>Dây số 8 Aolikes</strong> chỉ cần lồng cổ tay qua một vòng, luồn đầu còn lại qua thanh đòn và xỏ cổ tay trở lại. Thanh đòn được khóa chặt hoàn toàn vào cổ tay chỉ sau 2 giây chuẩn bị.</p>
<h2>2. Chất liệu dù bện chịu lực cao</h2>
<p>Được dệt từ sợi tổng hợp Poly-Nylon nhiều lớp với đường chỉ may gia cường hình chữ X, dây số 8 Aolikes có thể chịu tải trọng kéo lên tới hơn 300kg mà không bị biến dạng.</p>
HTML
            ,
            'pros_vi' => "Thao tác khóa thanh đòn cực nhanh chỉ trong 2 giây, không cần quấn dây phức tạp.\nKhóa đòn tạ cố định tuyệt đối vào cổ tay, không lo bị tuột khi tập tạ cực nặng.\nChất liệu sợi dù bện chịu tải lên tới 300 kg siêu bền bỉ.",
            'cons_vi' => "Khó buông đòn tạ khẩn cấp khi gặp sự cố so với strap truyền thống.\nKhông điều chỉnh được độ dài ngắn linh hoạt theo kích cỡ thanh tạ.",
            'suitable_vi' => 'Người tập Deadlift mức tạ nặng, Shrugs cầu vai, vận động viên Powerlifting và Strongman.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Aolikes',
                'Thiết kế' => 'Khóa số 8 (Figure 8)',
                'Chất liệu' => 'Vải dù Poly-Nylon dệt bện dày',
                'Tải trọng kéo' => 'Tối đa 300+ kg',
                'Kích thước' => 'Size M (Cổ tay <17.5cm) / Size L (Cổ tay >17.5cm)'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 13. Aolikes Crossfit Gloves with Wristwrap
        array(
            'cat_slug' => 'gang-tay-lifting-straps',
            'namevi' => 'Găng tay tập gym có quấn cổ tay Aolikes Crossfit Gloves',
            'nameen' => 'Aolikes Breathable Gym Gloves with Wrist Wrap',
            'slugvi' => 'gang-tay-tap-gym-co-quan-co-tay-aolikes-crossfit-gloves',
            'slugen' => 'aolikes-crossfit-gym-gloves-wrist-wrap',
            'code' => 'AOL-GLV01',
            'regular_price' => 260000,
            'sale_price' => 190000,
            'discount' => 27,
            'photo' => 'gang-tay-tap-gym-aolikes-crossfit.jpg',
            'review_score' => 8.9,
            'review_count' => 17,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Găng tay tập gym Aolikes Crossfit với thiết kế hở mu bàn tay thoáng khí, lòng bàn tay đệm hạt silicon tổ ong chống trượt kết hợp dải quấn cổ tay dài 45cm bảo vệ khớp toàn diện.',
            'contentvi' => <<<HTML
<h2>1. Thiết kế thoáng khí và bảo vệ cổ tay</h2>
<p><strong>Găng tay Aolikes Crossfit</strong> được thiết kế giải quyết vấn đề mồ hôi tay bí bách khi tập luyện. Phần mu bàn tay được khoét hở công thái học, trong khi phần lòng bàn tay phủ kín hạt silicon hình tổ ong gia tăng độ ma sát bám dính khi cầm tạ hoặc đu xà đơn.</p>
<h2>2. Dải quấn cổ tay trợ lực 45cm</h2>
<p>Dải quấn cổ tay bản rộng 5cm tích hợp liền thân găng giúp cố định khớp cổ tay, giảm áp lực khi thực hiện các bài đẩy ngực (Bench Press), đẩy vai (Overhead Press) và chống đẩy.</p>
HTML
            ,
            'pros_vi' => "Thiết kế hở mu bàn tay cực kỳ thoáng khí, thoát mồ hôi nhanh chóng.\nHạt silicon tổ ong lòng bàn tay tăng ma sát, chống trơn trượt hiệu quả.\nDải quấn cổ tay 45cm trợ lực và bảo vệ khớp cổ tay khi đẩy tạ nặng.\nGiá thành cạnh tranh và có nhiều size lựa chọn.",
            'cons_vi' => "Lớp đệm lòng bàn tay ở mức trung bình để giữ cảm giác thật tay, không quá dày cho ai thích đệm mút siêu dày.",
            'suitable_vi' => 'Người tập Gym đa năng, Calisthenics, tập xà đơn, tạ ấm và bài tập thể lực tổng hợp.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Aolikes',
                'Chất liệu' => 'Vải lưới Lycra co giãn + Da sợi tổng hợp + Silicon',
                'Dải quấn cổ tay' => 'Dài 45 cm, Rộng 5 cm kèm băng dán',
                'Kích cỡ' => 'M (vòng tay 18-20cm), L (20-22cm), XL (22-24cm)'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 14. Harbinger Pro WristWrap Gloves
        array(
            'cat_slug' => 'gang-tay-lifting-straps',
            'namevi' => 'Găng tay thể hình Harbinger Pro WristWrap Gloves',
            'nameen' => 'Harbinger Pro WristWrap Weightlifting Gloves',
            'slugvi' => 'gang-tay-the-hinh-harbinger-pro-wristwrap-gloves',
            'slugen' => 'harbinger-pro-wristwrap-gloves',
            'code' => 'HARB-PGLV',
            'regular_price' => 820000,
            'sale_price' => 680000,
            'discount' => 17,
            'photo' => 'gang-tay-harbinger-pro-wristwrap.jpg',
            'review_score' => 9.4,
            'review_count' => 21,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Găng tay thể hình Harbinger Pro WristWrap Gloves làm từ da thật cao cấp kết hợp đệm bọt kép công thái học và đai quấn cổ tay WristWrap độc quyền, mang lại sự bảo vệ tối đa cho lòng bàn tay và khớp cổ tay.',
            'contentvi' => <<<HTML
<h2>1. Đỉnh cao bảo vệ lòng bàn tay từ da thật</h2>
<p><strong>Harbinger Pro WristWrap</strong> là dòng găng tay thể hình cao cấp dành cho những ai tìm kiếm sự bền bỉ tuyệt đối. Lòng bàn tay được may từ da thuộc tự nhiên kết hợp đệm bọt xốp kép giảm chấn đúc sẵn theo cấu trúc giải phẫu bàn tay.</p>
<h2>2. Đai quấn cổ tay WristWrap độc quyền</h2>
<p>Hệ thống đai quấn cổ tay Graduated WristWrap ôm trọn khớp cổ tay, duy trì vị trí khớp trung tính khi chịu tải trọng tạ lớn, ngăn ngừa chấn thương lật cổ tay phổ biến.</p>
HTML
            ,
            'pros_vi' => "Chất liệu da thật cao cấp chống mài mòn, độ bền vượt trội qua nhiều năm.\nĐệm bọt kép công thái học giảm áp lực lên các khớp ngón tay và chống chai tay triệt để.\nĐai quấn cổ tay WristWrap bảo vệ khớp cổ tay vững chắc khi nâng tạ nặng.",
            'cons_vi' => "Cần giặt tay nhẹ nhàng và tránh phơi nắng gắt để giữ độ mềm của da.\nGiá thành cao trong phân khúc găng tay thể hình.",
            'suitable_vi' => 'Gymer tập luyện lâu năm, người thường xuyên bị đau khớp cổ tay khi đẩy tạ nặng hoặc muốn bảo vệ da tay khỏi chai sần.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Harbinger (Mỹ)',
                'Chất liệu' => 'Da thật (Genuine Leather) + Vải co giãn 4 chiều',
                'Đệm lòng bàn tay' => 'Đệm bọt xốp kép công thái học',
                'Cổ tay' => 'Tích hợp đai WristWrap có khóa dán',
                'Bảo hành' => '6 tháng chính hãng'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 15. Booster Pro 3 High Power Massage Gun
        array(
            'cat_slug' => 'sung-massage-cam-tay',
            'namevi' => 'Súng massage cơ bắp cầm tay Booster Pro 3 High Power',
            'nameen' => 'Booster Pro 3 High Power Deep Tissue Massage Gun',
            'slugvi' => 'sung-massage-co-bap-cam-tay-booster-pro-3-high-power',
            'slugen' => 'booster-pro-3-high-power-massage-gun',
            'code' => 'BST-PRO3',
            'regular_price' => 3400000,
            'sale_price' => 2850000,
            'discount' => 16,
            'photo' => 'sung-massage-booster-pro-3.jpg',
            'review_score' => 9.6,
            'review_count' => 30,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Súng massage Booster Pro 3 sở hữu động cơ không chổi than 126W mạnh mẽ, biên độ rung 12mm tác động sâu vào mô cơ lớn cùng 6 đầu massage chuyên dụng giúp giải tỏa căng cơ cấp tốc sau buổi tập nặng.',
            'contentvi' => <<<HTML
<h2>1. Sức mạnh động cơ không chổi than 126W</h2>
<p><strong>Booster Pro 3</strong> là dòng súng massage bộ gõ (Percussive Therapy) chuyên nghiệp được tin dùng bởi nhiều vận động viên thể hình và huấn luyện viên cá nhân (PT). Khác với các dòng súng giá rẻ dễ bị khựng máy khi ấn mạnh, Booster Pro 3 trang bị động cơ công suất 126W với lực đẩy tối đa lên tới 15kg (Stall Force).</p>

<h2>2. Biên độ rung 12mm tác động mô cơ sâu</h2>
<ul>
    <li>Biên độ 12mm cho phép các xung lực xuyên qua các lớp mỡ và mạc cơ, chạm tới các nhóm cơ dày như cơ đùi trước, cơ mông và lưng xô.</li>
    <li>4 cấp tốc độ tùy chỉnh từ 1300 đến 3400 vòng/phút.</li>
    <li>Pin Lithium-ion 24V 2400mAh cho thời lượng sử dụng liên tục từ 4 đến 6 tiếng.</li>
    <li>Bộ 6 đầu massage chuyên biệt cho từng vùng cơ thể (đầu tròn, đầu chữ U, đầu đạn, đầu phẳng, đầu khí nén, đầu cong).</li>
</ul>
HTML
            ,
            'pros_vi' => "Động cơ không chổi than 126W cực mạnh với lực đẩy 15kg không bị nghẽn máy khi tì đè mạnh.\nBiên độ rung 12mm tác động sâu vào các bó cơ dày, giải tỏa căng cứng cấp tốc.\nTrang bị 6 đầu massage chuyên dụng cho từng nhóm cơ riêng biệt.\nThời lượng pin 2400mAh bền bỉ 4-6 tiếng sử dụng.",
            'cons_vi' => "Trọng lượng máy ~1.2kg khá đầm tay khi tự massage vùng lưng sau.\nGiá thành thuộc phân khúc cao cấp.",
            'suitable_vi' => 'Vận động viên thể hình, gymer tập luyện cường độ cao, PT chuyên nghiệp và người cần trị liệu cơ bắp sâu.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Booster',
                'Công suất động cơ' => '126W (Không chổi than thế hệ mới)',
                'Biên độ rung' => '12 mm (Deep Tissue)',
                'Tốc độ rung' => '1300 - 3400 RPM (4 cấp tốc độ)',
                'Dung lượng pin' => '24V / 2400 mAh Lithium-ion',
                'Độ ồn' => '< 45 dB',
                'Phụ kiện' => 'Vali đựng chống sốc + 6 đầu massage + Củ sạc',
                'Bảo hành' => '12 tháng chính hãng'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 16. Xiaomi Mijia Fascia Gun Mini
        array(
            'cat_slug' => 'sung-massage-cam-tay',
            'namevi' => 'Súng massage mini Xiaomi Mijia Fascia Gun Mini',
            'nameen' => 'Xiaomi Mijia Mini Fascia Massage Gun',
            'slugvi' => 'sung-massage-mini-xiaomi-mijia-fascia-gun-mini',
            'slugen' => 'xiaomi-mijia-mini-fascia-massage-gun',
            'code' => 'MI-MINIGUN',
            'regular_price' => 1390000,
            'sale_price' => 1150000,
            'discount' => 17,
            'photo' => 'sung-massage-mini-xiaomi-mijia.jpg',
            'review_score' => 9.2,
            'review_count' => 25,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Súng massage mini Xiaomi Mijia Fascia Gun Mini với trọng lượng siêu nhẹ 375g, vòng đèn LED báo lực ấn thông minh, cổng sạc Type-C tiện lợi và độ ồn cực thấp dưới 40dB là lựa chọn bỏ túi hoàn hảo.',
            'contentvi' => <<<HTML
<h2>1. Thiết kế nhỏ gọn đột phá chỉ 375g</h2>
<p><strong>Xiaomi Mijia Mini Fascia Gun</strong> tái định nghĩa sự tiện lợi của thiết bị massage cầm tay. Với kích thước chỉ bằng một chiếc điện thoại thông minh và trọng lượng 375g, bạn có thể dễ dàng bỏ gọn vào túi xách hoặc balo tập gym mỗi ngày.</p>

<h2>2. Cảm biến lực ấn thông minh với đèn LED</h2>
<ul>
    <li>Vòng đèn LED 3 màu quanh thân máy phản hồi lực ấn theo thời gian thực (Trắng: Lực nhẹ, Xanh: Lực chuẩn tối ưu, Đỏ: Lực quá mạnh) giúp bạn kiểm soát an toàn tuyệt đối cho cơ và xương.</li>
    <li>Cổng sạc Type-C dùng chung với sạc điện thoại vô cùng tiện lợi.</li>
    <li>3 đầu massage bằng chất liệu silicon y tế cao cấp, êm ái và an toàn cho da.</li>
</ul>
HTML
            ,
            'pros_vi' => "Thiết kế siêu nhỏ gọn, trọng lượng chỉ 375g dễ dàng cầm nắm và mang theo bất cứ đâu.\nVòng đèn LED thông minh cảnh báo lực ấn giúp massage an toàn, tránh chấn thương.\nĐầu massage bọc silicon y tế mềm mại, thân thiện với làn da.\nCổng sạc Type-C phổ biến, thời lượng pin sử dụng lên tới 35 ngày (10 phút/ngày).",
            'cons_vi' => "Biên độ rung ~8mm phù hợp nhóm cơ vừa và nhỏ, không quá mạnh mẽ cho nhóm cơ cực dày.\nBộ đầu massage chỉ gồm 3 đầu cơ bản.",
            'suitable_vi' => 'Nữ tập gym, người làm việc văn phòng mỏi cổ vai gáy, người tập thể thao cần thiết bị giãn cơ bỏ túi tiện dụng.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Xiaomi (Mijia)',
                'Trọng lượng' => '375 g',
                'Kích thước' => '10.1 x 13.9 x 4.5 cm',
                'Tốc độ rung' => '1600 - 2500 RPM (3 cấp độ)',
                'Dung lượng pin' => '2600 mAh (Cổng sạc Type-C)',
                'Độ ồn' => '< 40 dB',
                'Đầu massage' => '3 đầu (Hình cầu, Hình chữ U, Hình phẳng)',
                'Bảo hành' => '12 tháng chính hãng'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 17. Liforme Yoga Mat 4.2mm
        array(
            'cat_slug' => 'tham-tap-gym-yoga',
            'namevi' => 'Thảm tập định tuyến Liforme Yoga Mat 4.2mm',
            'nameen' => 'Liforme Original Alignment Yoga Mat 4.2mm',
            'slugvi' => 'tham-tap-dinh-tuyen-liforme-yoga-mat-4-2mm',
            'slugen' => 'liforme-original-alignment-yoga-mat',
            'code' => 'LIF-MAT42',
            'regular_price' => 3650000,
            'sale_price' => 3200000,
            'discount' => 12,
            'photo' => 'tham-dinh-tuyen-liforme-yoga-mat.jpg',
            'review_score' => 9.7,
            'review_count' => 32,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Thảm tập định tuyến Liforme Yoga Mat 4.2mm là dòng thảm yoga cao cấp hàng đầu thế giới, kết hợp giữa đế cao su tự nhiên và bề mặt PU GripForMe độc quyền chống trượt tuyệt đối kể cả khi đổ mồ hôi nhiều.',
            'contentvi' => <<<HTML
<h2>1. Đỉnh cao bám dính GripForMe độc quyền</h2>
<p><strong>Liforme Yoga Mat</strong> được mệnh danh là "ông vua" trong làng thảm tập Yoga. Điểm đắt giá nhất của Liforme nằm ở bề mặt vật liệu Polyurethane GripForMe có khả năng thấm hút mồ hôi siêu tốc, mang lại độ bám dính chắc chắn vô địch trong mọi điều kiện thời tiết.</p>

<h2>2. Hệ thống vạch định tuyến AlignForMe</h2>
<ul>
    <li>Các vạch kẻ thông minh in khắc laser không độc hại trên bề mặt thảm giúp người tập căn chỉnh chính xác vị trí bàn tay, bàn chân trong các tư thế chiến binh, chó úp mặt hay thăng bằng tay.</li>
    <li>Kích thước mở rộng 185 x 68 cm dài và rộng hơn thảm thông thường, tạo không gian chuyển động thoải mái.</li>
    <li>Đế cao su tự nhiên nguyên chất dày 4.2mm có độ đàn hồi hoàn hảo bảo vệ khớp cổ tay và đầu gối.</li>
</ul>
HTML
            ,
            'pros_vi' => "Độ bám dính (Grip) đỉnh cao thế giới, hoàn toàn không trơn trượt kể cả khi ra mồ hôi đầm đìa.\nHệ thống vạch định tuyến AlignForMe khắc laser hỗ trợ căn chỉnh tư thế chuẩn xác.\nChất liệu cao su tự nhiên phân hủy sinh học thân thiện môi trường, không độc hại.\nKích thước rộng rãi 185 x 68 cm kèm túi xách thảm cao cấp.",
            'cons_vi' => "Giá thành thuộc phân khúc cao cấp nhất.\nTrọng lượng ~2.5kg khá nặng khi di chuyển thường xuyên.\nBề mặt hút ẩm tốt nên cần lau bằng khăn ẩm sạch, tránh tiếp xúc dầu nhờn.",
            'suitable_vi' => 'Người tập Yoga chuyên sâu, Hot Yoga, Pilates, người cần thảm có độ bám tuyệt đối và hỗ trợ định tuyến khớp chuẩn xác.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Liforme (Anh Quốc)',
                'Kích thước' => '185 x 68 cm',
                'Độ dày' => '4.2 mm',
                'Trọng lượng' => '~2.5 kg',
                'Chất liệu' => 'Cao su thiên nhiên + Mặt phủ PU GripForMe',
                'Hệ thống định tuyến' => 'AlignForMe khắc laser',
                'Phụ kiện kèm theo' => 'Túi đựng thảm Liforme chính hãng'
            ), JSON_UNESCAPED_UNICODE)
        ),

        // 18. Manduka PROlite Yoga Mat 4.7mm
        array(
            'cat_slug' => 'tham-tap-gym-yoga',
            'namevi' => 'Thảm tập thể dục chống trượt Manduka PROlite 4.7mm',
            'nameen' => 'Manduka PROlite Non-Slip Exercise Yoga Mat 4.7mm',
            'slugvi' => 'tham-tap-the-duc-chong-truot-manduka-prolite-4-7mm',
            'slugen' => 'manduka-prolite-yoga-mat-4-7mm',
            'code' => 'MAN-PROLITE',
            'regular_price' => 2490000,
            'sale_price' => 2150000,
            'discount' => 14,
            'photo' => 'tham-tap-manduka-prolite.jpg',
            'review_score' => 9.5,
            'review_count' => 27,
            'review_type' => 'EDITOR_REVIEW',
            'is_real_test' => 1,
            'descvi' => 'Thảm tập thể dục chống trượt Manduka PROlite 4.7mm với cấu trúc ô kín mật độ cao, khả năng bảo vệ xương khớp hoàn hảo và độ bền vô địch được cam kết bảo hành trọn đời từ thương hiệu Manduka danh tiếng.',
            'contentvi' => <<<HTML
<h2>1. Huyền thoại độ bền vĩnh cửu từ Manduka</h2>
<p><strong>Manduka PROlite</strong> là dòng thảm tập được các huấn luyện viên thể hình và yoga trên toàn thế giới khuyên dùng nhờ độ bền gần như bất tử. Cấu trúc ô kín (Closed-cell structure) mật độ cao giúp ngăn chặn mồ hôi và bụi bẩn ngấm vào lõi thảm, giữ cho thảm luôn vệ sinh và không bao giờ bị bong tróc xẹp lún.</p>

<h2>2. Đệm mật độ cao bảo vệ xương khớp</h2>
<ul>
    <li>Độ dày 4.7mm với mật độ vật liệu siêu đặc cung cấp lớp đệm êm ái bảo vệ khớp cổ tay, đầu gối và cột sống khi tiếp xúc với sàn nhà cứng.</li>
    <li>Chứng nhận an toàn OEKO-TEX 100 hoàn toàn không chứa chất độc hại, khí thải hay hóa chất làm mềm độc hại.</li>
    <li>Trọng lượng 1.8kg nhẹ hơn đáng kể so với dòng Manduka PRO tiêu chuẩn, dễ dàng cuộn lại mang theo.</li>
</ul>
HTML
            ,
            'pros_vi' => "Độ bền huyền thoại không bong tróc, cam kết bảo hành trọn đời từ Manduka.\nCấu trúc ô kín ngăn mồ hôi và vi khuẩn ngấm vào lõi thảm, cực kỳ dễ lau chùi.\nĐệm bọt mật độ cao 4.7mm bảo vệ tối ưu khớp cổ tay và đầu gối.\nTrọng lượng 1.8kg cân bằng hoàn hảo giữa độ êm ái và tính cơ động.",
            'cons_vi' => "Cần quá trình phá thảm (Break-in) bằng cách tập luyện thường xuyên hoặc chà muối biển để bề mặt đạt độ bám tối đa.\nKhông thấm hút mồ hôi bề mặt (cần khăn trải thảm nếu ra mồ hôi quá nhiều).",
            'suitable_vi' => 'Gymer tập Bodyweight, HIIT, Yoga, giãn cơ hàng ngày cần một chiếc thảm siêu bền, êm ái và vệ sinh.',
            'specifications_vi' => json_encode(array(
                'Thương hiệu' => 'Manduka (Mỹ / Sản xuất tại Đức)',
                'Kích thước' => '180 x 61 cm',
                'Độ dày' => '4.7 mm',
                'Trọng lượng' => '~1.8 kg',
                'Chất liệu' => 'PVC mật độ cao chứng nhận OEKO-TEX 100',
                'Cấu trúc' => 'Ô kín (Closed-cell)',
                'Bảo hành' => 'Chính sách trọn đời Manduka Lifetime Guarantee'
            ), JSON_UNESCAPED_UNICODE)
        )
    );

    foreach ($products as $idx => $p) {
        $catInfo = isset($catMap[$p['cat_slug']]) ? $catMap[$p['cat_slug']] : array('id' => 0, 'id_list' => 0);
        $idCat = $catInfo['id'];
        $idList = $catInfo['id_list'];

        $existPro = $d->rawQueryOne("SELECT id FROM #_product WHERE slugvi = ? LIMIT 1", array($p['slugvi']));

        if (!empty($existPro['id'])) {
            $targetId = $existPro['id'];
            $d->rawQuery("UPDATE #_product SET 
                id_list = ?, 
                id_cat = ?, 
                namevi = ?, 
                nameen = ?, 
                descvi = ?, 
                contentvi = ?, 
                code = ?, 
                regular_price = ?, 
                sale_price = ?, 
                discount = ?, 
                photo = ?, 
                review_score = ?, 
                review_count = ?, 
                review_type = ?, 
                is_real_test = ?, 
                pros_vi = ?, 
                cons_vi = ?, 
                suitable_vi = ?, 
                specifications_vi = ?, 
                numb = ?, 
                status = 'hienthi,noibat', 
                date_updated = ? 
                WHERE id = ?", array(
                $idList, $idCat, $p['namevi'], $p['nameen'], $p['descvi'], $p['contentvi'],
                $p['code'], $p['regular_price'], $p['sale_price'], $p['discount'], $p['photo'],
                $p['review_score'], $p['review_count'], $p['review_type'], $p['is_real_test'],
                $p['pros_vi'], $p['cons_vi'], $p['suitable_vi'], $p['specifications_vi'],
                ($idx + 1), $now, $targetId
            ));
            echo "  - Updated product: {$p['namevi']} (ID: {$targetId})" . PHP_EOL;
        } else {
            $d->rawQuery("INSERT INTO #_product (
                id_list, id_cat, namevi, nameen, slugvi, slugen, descvi, contentvi,
                code, regular_price, sale_price, discount, photo,
                review_score, review_count, review_type, is_real_test,
                pros_vi, cons_vi, suitable_vi, specifications_vi,
                numb, status, type, date_created
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, 'hienthi,noibat', 'san-pham', ?
            )", array(
                $idList, $idCat, $p['namevi'], $p['nameen'], $p['slugvi'], $p['slugen'], $p['descvi'], $p['contentvi'],
                $p['code'], $p['regular_price'], $p['sale_price'], $p['discount'], $p['photo'],
                $p['review_score'], $p['review_count'], $p['review_type'], $p['is_real_test'],
                $p['pros_vi'], $p['cons_vi'], $p['suitable_vi'], $p['specifications_vi'],
                ($idx + 1), $now
            ));
            $inserted = $d->rawQueryOne("SELECT id FROM #_product WHERE slugvi = ? LIMIT 1", array($p['slugvi']));
            $targetId = $inserted['id'];
            echo "  - Created product: {$p['namevi']} (ID: {$targetId})" . PHP_EOL;
        }

        // SEO for product
        $seoExist = $d->rawQueryOne("SELECT id FROM #_seo WHERE id_parent = ? AND com = 'product' AND act = 'man' AND type = 'san-pham' LIMIT 1", array($targetId));
        $seoTitle = "{$p['namevi']} - Đánh Giá & Nơi Bán Giá Tốt | Khỏe Pro";
        $seoDesc = $p['descvi'];
        $seoKw = mb_strtolower($p['namevi'], 'UTF-8') . ", " . mb_strtolower($p['code'], 'UTF-8') . ", danh gia do tap gym, khoe pro";

        if (!empty($seoExist['id'])) {
            $d->rawQuery("UPDATE #_seo SET titlevi = ?, descriptionvi = ?, keywordsvi = ? WHERE id = ?", array(
                $seoTitle, $seoDesc, $seoKw, $seoExist['id']
            ));
        } else {
            $d->rawQuery("INSERT INTO #_seo (id_parent, com, act, type, titlevi, descriptionvi, keywordsvi, main_keywordsvi, main_keywordsen) VALUES (?, 'product', 'man', 'san-pham', ?, ?, ?, '', '')", array(
                $targetId, $seoTitle, $seoDesc, $seoKw
            ));
        }
    }

    echo "=== COMPLETED BATCH 3: REAL PRODUCTS CATALOG (18 PRODUCTS) ===" . PHP_EOL;

} catch (Exception $e) {
    echo "ERROR in Batch 3: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
