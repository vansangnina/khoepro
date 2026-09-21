<?php
define('LIBRARIES', './libraries/');
require_once 'libraries/config.php';
require_once 'libraries/class/class.PDODb.php';

$d = new PDODb($config['database']);

echo "=== START BATCH 5: BUYING GUIDES & KNOWLEDGE ARTICLES (16 ARTICLES) ===" . PHP_EOL;

try {
    $now = time();

    // Map news lists
    $guideList = $d->rawQueryOne("SELECT id FROM #_news_list WHERE slugvi = 'huong-dan-chon-mua' LIMIT 1");
    $idListGuide = !empty($guideList['id']) ? $guideList['id'] : 4;

    $knowList = $d->rawQueryOne("SELECT id FROM #_news_list WHERE slugvi = 'kien-thuc-tap-luyen' LIMIT 1");
    $idListKnow = !empty($knowList['id']) ? $knowList['id'] : 6;

    $articles = array(
        // GUIDE 1
        array(
            'id_list' => $idListGuide,
            'namevi' => 'Hướng dẫn chọn đai lưng tập gym: Phân biệt đai da, đai nylon và đai đòn bẩy',
            'nameen' => 'Weightlifting Belt Buying Guide',
            'slugvi' => 'huong-dan-chon-dai-lung-tap-gym',
            'slugen' => 'weightlifting-belt-buying-guide',
            'photo' => 'huong-dan-chon-dai-lung-gym.jpg',
            'descvi' => 'Cẩm nang toàn diện giúp bạn phân biệt rõ ràng giữa đai da truyền thống, đai nylon dán gai và đai khóa đòn bẩy (lever belt) để chọn đúng loại phù hợp với phong cách tập luyện.',
            'contentvi' => <<<HTML
<h2>1. Xác định mục tiêu tập luyện của bạn</h2>
<p>Một chiếc đai lưng không chỉ là phụ kiện thời trang trong phòng gym; nó là công cụ bảo vệ cột sống và tối ưu hóa lực phát. Tuy nhiên, việc chọn sai loại đai có thể gây cản trở chuyển động hoặc không mang lại sự bảo vệ cần thiết.</p>

<h2>2. So sánh 3 dòng đai lưng phổ biến</h2>
<table border="1" cellpadding="8" style="border-collapse:collapse; width:100%; margin-bottom:20px;">
    <tr style="background:#f4f4f4;">
        <th>Tiêu chí</th>
        <th>Đai da truyền thống</th>
        <th>Đai nylon dán gai</th>
        <th>Đai khóa đòn bẩy (Lever)</th>
    </tr>
    <tr>
        <td><strong>Chất liệu</strong></td>
        <td>Da bò thật hoặc da nhân tạo dày</td>
        <td>Vải nylon dệt bọc mút EVA</td>
        <td>Da nhiều lớp ép cứng (10-13mm)</td>
    </tr>
    <tr>
        <td><strong>Độ cứng vững</strong></td>
        <td>Rất cao</td>
        <td>Trung bình - Khá</td>
        <td>Cực đại</td>
    </tr>
    <tr>
        <td><strong>Độ linh hoạt</strong></td>
        <td>Trung bình</td>
        <td>Rất cao, nhẹ nhàng</td>
        <td>Thấp, chuyên cho bài nặng</td>
    </tr>
    <tr>
        <td><strong>Bài tập phù hợp</strong></td>
        <td>Squat, Deadlift, Overhead Press</td>
        <td>Crossfit, HIIT, Functional</td>
        <td>Powerlifting, Heavy Squat/Deadlift</td>
    </tr>
</table>

<h2>3. Lời khuyên chọn đai từ Khỏe Pro</h2>
<p>Nếu bạn tập thể hình phong trào, hãy chọn <a href="san-pham/dai-lung-da-harbinger-4-inch-padded-leather-belt">Đai da Harbinger 4-inch</a>. Nếu bạn hướng tới sức mạnh tối đa, hãy chọn <a href="san-pham/dai-lung-khoa-don-bay-aolikes-lever-buckle-powerlifting-belt">Đai đòn bẩy Aolikes 10mm</a>. Còn nếu bạn tập thể lực đa năng, <a href="san-pham/dai-lung-nylon-harbinger-5-inch-foam-core-belt">Đai nylon Harbinger Foam Core</a> là lựa chọn hoàn hảo.</p>
HTML
        ),

        // GUIDE 2
        array(
            'id_list' => $idListGuide,
            'namevi' => 'Cách chọn dây kháng lực phù hợp: Mini band, Super band hay bộ dây ngũ sắc?',
            'nameen' => 'Resistance Bands Selection Guide',
            'slugvi' => 'cach-chon-day-khang-luc-phu-hop',
            'slugen' => 'resistance-bands-selection-guide',
            'photo' => 'huong-dan-chon-day-khang-luc.jpg',
            'descvi' => 'Hướng dẫn phân biệt các loại dây kháng lực Mini Band, Super Band bản dài và Bộ dây ngũ sắc có móc khóa để chọn đúng phụ kiện cho mục tiêu tập mông, tập thân trên hay tập tại nhà.',
            'contentvi' => <<<HTML
<h2>1. Phân loại dây kháng lực theo mục đích sử dụng</h2>
<p>Dây kháng lực (Resistance Band) là một trong những phụ kiện thông minh và linh hoạt nhất. Tuy nhiên, mỗi loại dây lại phục vụ một nhóm bài tập riêng biệt:</p>
<ul>
    <li><strong>Dây Mini Band vải (Vòng tròn ngắn 30-76cm):</strong> Chuyên dụng cho thân dưới, tập kích hoạt cơ mông, đùi ngoài (Squat, Hip Thrust). Nên chọn <a href="san-pham/set-3-day-khang-luc-vai-aolikes-hip-resistance-band">Dây vải Aolikes</a> để chống xoắn.</li>
    <li><strong>Dây Powerband cao su bản dài (208cm):</strong> Chuyên hỗ trợ hít xà đơn, kéo giãn cơ khớp và tập kháng lực với tạ đòn như <a href="san-pham/day-khang-luc-powerband-prosourcefit-cao-su-tu-nhien">Powerband ProsourceFit</a>.</li>
    <li><strong>Bộ dây ngũ sắc có tay cầm & neo cửa:</strong> Phù hợp nhất cho người tập toàn thân tại nhà thay thế máy kéo cáp như <a href="san-pham/bo-day-khang-luc-ngu-sac-pseudois-11-mon-da-nang">Bộ ngũ sắc Pseudois 11 món</a>.</li>
</ul>
HTML
        ),

        // GUIDE 3
        array(
            'id_list' => $idListGuide,
            'namevi' => 'Cẩm nang chọn thảm tập Gym và Yoga: Độ dày, chất liệu TPE, PVC hay Cao su tự nhiên?',
            'nameen' => 'Gym & Yoga Mat Buying Guide',
            'slugvi' => 'cam-nang-chon-tham-tap-gym-va-yoga',
            'slugen' => 'gym-yoga-mat-buying-guide',
            'photo' => 'huong-dan-chon-tham-tap.jpg',
            'descvi' => 'Tiêu chí chọn thảm tập thể thao: Phân tích độ bám dính (Grip), độ đàn hồi bảo vệ khớp và so sánh ưu nhược điểm của các chất liệu PVC, TPE, Cao su tự nhiên PU.',
            'contentvi' => <<<HTML
<h2>1. Ba yếu tố quan trọng nhất khi chọn thảm tập</h2>
<ol>
    <li><strong>Độ bám dính (Grip):</strong> Quyết định bạn có bị trượt tay chân khi đổ mồ hôi hay không. Đỉnh cao bám dính là chất liệu bề mặt PU như trên <a href="san-pham/tham-tap-dinh-tuyen-liforme-yoga-mat-4-2mm">Thảm định tuyến Liforme</a>.</li>
    <li><strong>Độ dày & Mật độ đệm:</strong> Thảm 4-5mm mật độ cao như <a href="san-pham/tham-tap-the-duc-chong-truot-manduka-prolite-4-7mm">Manduka PROlite</a> bảo vệ khớp gối tốt hơn thảm 10mm xốp mềm dễ lún.</li>
    <li><strong>Độ bền & Vệ sinh:</strong> Cấu trúc ô kín (closed-cell) chống thấm mồ hôi và ngăn mùi ẩm mốc hiệu quả.</li>
</ol>
HTML
        ),

        // GUIDE 4
        array(
            'id_list' => $idListGuide,
            'namevi' => 'Hướng dẫn chọn súng massage cơ bắp: Biên độ rung và lực đẩy quan trọng như thế nào?',
            'nameen' => 'Massage Gun Buying Guide',
            'slugvi' => 'huong-dan-chon-sung-massage-co-bap',
            'slugen' => 'massage-gun-buying-guide',
            'photo' => 'huong-dan-chon-sung-massage.jpg',
            'descvi' => 'Tìm hiểu 2 thông số kỹ thuật then chốt của súng massage: Biên độ rung (Stroke Amplitude) và Lực chặn đứng máy (Stall Force) để chọn thiết bị phù hợp nhu cầu giãn cơ.',
            'contentvi' => <<<HTML
<h2>1. Hai thông số kỹ thuật quyết định chất lượng súng massage</h2>
<ul>
    <li><strong>Biên độ rung (Amplitude):</strong> Độ vươn sâu của đầu massage. Súng mini như <a href="san-pham/sung-massage-mini-xiaomi-mijia-fascia-gun-mini">Xiaomi Mijia Mini</a> đạt 6-8mm phù hợp cơ nhỏ. Súng chuyên nghiệp như <a href="san-pham/sung-massage-co-bap-cam-tay-booster-pro-3-high-power">Booster Pro 3</a> đạt 12mm tác động sâu vào mô cơ lớn.</li>
    <li><strong>Lực chặn đứng máy (Stall Force):</strong> Thể hiện sức mạnh động cơ. Động cơ công suất lớn từ 100W trở lên sẽ không bị khựng lại khi bạn tì mạnh tay vào cơ đùi hay cơ mông.</li>
</ul>
HTML
        ),

        // GUIDE 5
        array(
            'id_list' => $idListGuide,
            'namevi' => 'Cách chọn găng tay tập gym và dây kéo lưng: Đâu là giải pháp tối ưu cho bạn?',
            'nameen' => 'Gym Gloves and Lifting Straps Guide',
            'slugvi' => 'cach-chon-gang-tay-tap-gym-va-day-keo-lung',
            'slugen' => 'gym-gloves-and-lifting-straps-guide',
            'photo' => 'huong-dan-chon-gang-tay-straps.jpg',
            'descvi' => 'Phân tích sự khác biệt giữa găng tay bảo vệ lòng bàn tay và dây kéo lưng Lifting Straps trợ lực để lựa chọn đúng phụ kiện cho từng buổi tập đẩy (Push) hay kéo (Pull).',
            'contentvi' => <<<HTML
<h2>1. Khi nào nên dùng Găng tay tập gym?</h2>
<p>Găng tay như <a href="san-pham/gang-tay-the-hinh-harbinger-pro-wristwrap-gloves">Harbinger Pro WristWrap</a> phù hợp cho các bài đẩy (Bench Press, Shoulder Press) nhằm chống chai tay và trợ lực cổ tay.</p>
<h2>2. Khi nào nên dùng Dây kéo lưng (Lifting Straps)?</h2>
<p>Dây kéo lưng như <a href="san-pham/day-keo-lung-lifting-straps-harbinger-padded-cotton">Harbinger Cotton Straps</a> hoặc <a href="san-pham/day-keo-lung-so-8-figure-8-aolikes-heavy-duty-straps">Dây số 8 Aolikes</a> sinh ra cho các bài kéo (Deadlift, Lat Pulldown, Row) giúp loại bỏ giới hạn lực nắm bàn tay.</p>
HTML
        ),

        // GUIDE 6
        array(
            'id_list' => $idListGuide,
            'namevi' => 'Hướng dẫn chọn con lăn Foam Roller: Độ cứng, bề mặt gai hay bề mặt rãnh sóng?',
            'nameen' => 'Foam Roller Selection Guide',
            'slugvi' => 'huong-dan-chon-con-lan-foam-roller',
            'slugen' => 'foam-roller-selection-guide',
            'photo' => 'huong-dan-chon-con-lan-foam-roller.jpg',
            'descvi' => 'Hướng dẫn chọn mua con lăn giãn cơ Foam Roller: So sánh con lăn lõi rỗng 3D, con lăn bọt nén đặc EPP và bóng massage đôi chuyên dụng cho thắt lưng.',
            'contentvi' => <<<HTML
<h2>1. Ba dòng con lăn giãn cơ phổ biến</h2>
<ul>
    <li><strong>Con lăn rãnh 3D lõi cứng:</strong> Điển hình là <a href="san-pham/con-lan-bot-gian-co-triggerpoint-grid-1-foam-roller">TriggerPoint GRID 1.0</a> mô phỏng bàn tay trị liệu, giải tỏa điểm kích hoạt đau nhức cực sâu.</li>
    <li><strong>Con lăn bọt nén phẳng EPP:</strong> Điển hình là <a href="san-pham/con-lan-gian-co-bot-xop-eva-epp-high-density-roller">EPP High-Density</a> có bề mặt nhẵn, độ cứng cao và giá thành bình dân.</li>
    <li><strong>Bóng massage đôi hình đậu phộng:</strong> Điển hình là <a href="san-pham/bong-massage-doi-peanut-lacrosse-massage-ball">Bóng Peanut Lacrosse</a> chuyên biệt cho hai bên cột sống thắt lưng và cổ vai gáy.</li>
</ul>
HTML
        ),

        // GUIDE 7
        array(
            'id_list' => $idListGuide,
            'namevi' => 'Cẩm nang chọn size đai lưng tập gym chuẩn xác theo số đo vòng eo',
            'nameen' => 'Weightlifting Belt Sizing Guide',
            'slugvi' => 'cam-nang-chon-size-dai-lung-tap-gym',
            'slugen' => 'weightlifting-belt-sizing-guide',
            'photo' => 'huong-dan-chon-size-dai-lung.jpg',
            'descvi' => 'Cách đo vòng bụng chuẩn xác tại vị trí ngang rốn (không dùng size quần) để chọn đúng kích cỡ đai lưng vừa vặn, phát huy tối đa khả năng gồng bụng.',
            'contentvi' => <<<HTML
<h2>1. Sai lầm phổ biến: Dùng size quần để mua đai lưng</h2>
<p>Size quần thường đo ở phần hông dưới, trong khi đai lưng tập gym được đeo ở vị trí ngang rốn hoặc ngay trên mào chậu. Do đó, bạn phải dùng thước dây đo chu vi vòng bụng ngang rốn khi đang thả lỏng tự nhiên.</p>
<h2>2. Bảng size tham khảo tiêu chuẩn</h2>
<ul>
    <li>Size S: Vòng eo từ 60 - 75 cm</li>
    <li>Size M: Vòng eo từ 75 - 88 cm</li>
    <li>Size L: Vòng eo từ 88 - 100 cm</li>
    <li>Size XL: Vòng eo trên 100 cm</li>
</ul>
HTML
        ),

        // GUIDE 8
        array(
            'id_list' => $idListGuide,
            'namevi' => 'Tiêu chí chọn thiết bị phục hồi cơ bắp tại nhà cho người tập thể hình',
            'nameen' => 'Home Muscle Recovery Gear Guide',
            'slugvi' => 'tieu-chi-chon-thiet-bi-phuc-hoi-co-bap-tai-nha',
            'slugen' => 'home-muscle-recovery-gear-guide',
            'photo' => 'huong-dan-thiet-bi-phuc-hoi.jpg',
            'descvi' => 'Bộ tiêu chí xây dựng góc phục hồi cơ bắp thông minh tại nhà: Kết hợp giữa Foam Roller, Bóng massage điểm và Súng massage bộ gõ để rút ngắn thời gian hồi phục thể lực.',
            'contentvi' => <<<HTML
<h2>1. Tầm quan trọng của phục hồi trong phát triển cơ bắp</h2>
<p>Cơ bắp không phát triển trong lúc tập, chúng phát triển trong quá trình phục hồi và nghỉ ngơi sau buổi tập. Việc trang bị các công cụ hỗ trợ giải phóng mạc cơ tại nhà giúp tăng lưu thông máu, đẩy nhanh tốc độ đào thải acid lactic và giảm hiện tượng đau nhức cơ muộn (DOMS).</p>
HTML
        ),

        // KNOWLEDGE 1
        array(
            'id_list' => $idListKnow,
            'namevi' => 'Đai lưng tập gym hoạt động như thế nào? Cơ chế tạo áp lực ổ bụng (IAP)',
            'nameen' => 'How Weightlifting Belts Work Intra-Abdominal Pressure',
            'slugvi' => 'dai-lung-tap-gym-hoat-dong-nhu-the-nao',
            'slugen' => 'how-weightlifting-belts-work',
            'photo' => 'kien-thuc-co-che-dai-lung.jpg',
            'descvi' => 'Giải mã khoa học đằng sau chiếc đai lưng: Cơ chế tạo áp lực ổ bụng (Intra-Abdominal Pressure), kỹ thuật thở Valsalva và cách đai bảo vệ đốt sống thắt lưng khi nâng tạ nặng.',
            'contentvi' => <<<HTML
<h2>1. Đai lưng không tự "nâng đỡ" cột sống của bạn</h2>
<p>Một hiểu lầm phổ biến là đai lưng hoạt động như một chiếc nẹp thụ động nâng đỡ lưng. Trên thực tế, đai lưng cung cấp một bức tường cứng cáp để thành bụng ép vào khi bạn hít sâu vào bụng và nén hơi (kỹ thuật Valsalva Maneuver).</p>

<h2>2. Áp lực ổ bụng (IAP) - "Túi khí sinh học" bảo vệ đĩa đệm</h2>
<p>Khi thành bụng ép chặt vào thân đai như trên <a href="san-pham/dai-lung-khoa-don-bay-aolikes-lever-buckle-powerlifting-belt">Đai đòn bẩy Aolikes</a>, áp lực bên trong khoang bụng tăng vọt, biến khoang bụng thành một cột trụ khí nén vững chắc giúp giảm tải áp lực nén lên các đốt sống L4-L5 và S1 tới 40%.</p>
HTML
        ),

        // KNOWLEDGE 2
        array(
            'id_list' => $idListKnow,
            'namevi' => 'Khi nào nên bắt đầu dùng đai lưng khi tập Squat và Deadlift?',
            'nameen' => 'When to Start Using a Weightlifting Belt',
            'slugvi' => 'khi-nao-nen-bat-dau-dung-dai-lung',
            'slugen' => 'when-to-use-a-weightlifting-belt',
            'photo' => 'kien-thuc-khi-nao-dung-dai-lung.jpg',
            'descvi' => 'Lời khuyên từ chuyên gia thể hình: Khi nào là thời điểm thích hợp để đeo đai lưng, mức tạ bao nhiêu phần trăm trọng lượng cơ thể thì nên dùng đai và cách rèn luyện cơ Core tự nhiên.',
            'contentvi' => <<<HTML
<h2>1. Người mới tập có nên đeo đai ngay từ ngày đầu tiên?</h2>
<p>Không nên lạm dụng đai lưng quá sớm với các mức tạ nhẹ. Trong giai đoạn đầu, bạn cần học cách kích hoạt cơ bụng sâu (Transverse Abdominis) và cơ dựng sống một cách tự nhiên để xây dựng nền tảng cơ Core vững chắc.</p>
<h2>2. Nguyên tắc sử dụng đai an toàn</h2>
<ul>
    <li>Các hiệp khởi động nhẹ dưới 70% 1RM: Tập không đeo đai (Beltless) để rèn luyện cơ Core.</li>
    <li>Các hiệp tập chính nặng từ 80% 1RM trở lên: Đeo đai để bảo vệ cột sống và tối đa hóa lực phát.</li>
</ul>
HTML
        ),

        // KNOWLEDGE 3
        array(
            'id_list' => $idListKnow,
            'namevi' => 'Lifting Straps là gì? Phân biệt Straps truyền thống, Figure 8 và Versa Gripps',
            'nameen' => 'Understanding Lifting Straps Types',
            'slugvi' => 'lifting-straps-la-gi-phan-biet-cac-loai-straps',
            'slugen' => 'lifting-straps-types-explained',
            'photo' => 'kien-thuc-lifting-straps.jpg',
            'descvi' => 'Tổng quan về dây kéo lưng Lifting Straps: Cấu tạo, nguyên lý giải phóng lực nắm cẳng tay và so sánh 3 biến thể dây quấn truyền thống, dây số 8 và móc kéo Versa Gripps.',
            'contentvi' => <<<HTML
<h2>1. Định nghĩa và công dụng của Lifting Straps</h2>
<p><strong>Lifting Straps</strong> (dây kéo lưng) là phụ kiện dạng dải vải quấn quanh cổ tay và móc chặt vào đòn tạ nhằm mục đích trợ lực nắm cho bàn tay trong các bài tập kéo như Deadlift, Shrug, Pull-up.</p>
<h2>2. Phân loại 3 dòng Straps thông dụng</h2>
<ul>
    <li><strong>Dây quấn truyền thống (Lasso Straps):</strong> Tiêu biểu là <a href="san-pham/day-keo-lung-lifting-straps-harbinger-padded-cotton">Harbinger Padded Cotton</a>, linh hoạt và dễ xả tạ khẩn cấp.</li>
    <li><strong>Dây số 8 (Figure 8):</strong> Tiêu biểu là <a href="san-pham/day-keo-lung-so-8-figure-8-aolikes-heavy-duty-straps">Aolikes Figure 8</a>, khóa cứng đòn tạ chỉ sau 2 giây cho bài Deadlift siêu nặng.</li>
    <li><strong>Móc kéo tay nhanh (Versa Gripps / Grip Pad):</strong> Thao tác một chạm nhanh chóng nhưng giá thành cao.</li>
</ul>
HTML
        ),

        // KNOWLEDGE 4
        array(
            'id_list' => $idListKnow,
            'namevi' => 'Phục hồi cơ bắp (Muscle Recovery): Tầm quan trọng của Foam Rolling và Massage Gun',
            'nameen' => 'Muscle Recovery with Foam Rolling and Massage Guns',
            'slugvi' => 'phuc-hoi-co-bap-tam-quan-trong-foam-rolling-massage-gun',
            'slugen' => 'muscle-recovery-foam-rolling-massage-gun',
            'photo' => 'kien-thuc-phuc-hoi-co-bap.jpg',
            'descvi' => 'Khoa học về phục hồi cơ bắp sau tập luyện: Cơ chế giải phóng mạc cơ (SMR), cải thiện độ linh hoạt khớp và cách kết hợp Foam Roller với Súng massage để đạt hiệu quả cao nhất.',
            'contentvi' => <<<HTML
<h2>1. Mạc cơ (Fascia) là gì và tại sao chúng bị căng cứng?</h2>
<p>Mạc cơ là tấm màng liên kết bao bọc toàn bộ các sợi cơ. Sau các buổi tập cường độ cao, mạc cơ bị co rút và hình thành các điểm kích hoạt đau (Trigger Points), làm giảm lưu thông máu và hạn chế tầm vận động của khớp.</p>
<h2>2. Phối hợp nhịp nhàng giữa Foam Roller và Súng massage</h2>
<p>Sử dụng <a href="san-pham/con-lan-bot-gian-co-triggerpoint-grid-1-foam-roller">Con lăn Foam Roller</a> để quét và làm mềm các dải cơ lớn diện rộng, sau đó dùng <a href="san-pham/sung-massage-co-bap-cam-tay-booster-pro-3-high-power">Súng massage Booster Pro 3</a> để đánh sâu vào các điểm đau cục bộ.</p>
HTML
        ),

        // KNOWLEDGE 5
        array(
            'id_list' => $idListKnow,
            'namevi' => 'Dây kháng lực có thể thay thế tạ tay không? Cơ chế của lực cản biến thiên',
            'nameen' => 'Can Resistance Bands Replace Dumbbells Variable Resistance',
            'slugvi' => 'day-khang-luc-co-the-thay-the-ta-tay-khong',
            'slugen' => 'can-resistance-bands-replace-dumbbells',
            'photo' => 'kien-thuc-day-khang-luc-vs-ta.jpg',
            'descvi' => 'Phân tích cơ chế lực cản biến thiên (Variable Resistance) của dây chun so với tạ tự do cố định, và cách kết hợp dây kháng lực để kích thích phì đại cơ bắp tối đa.',
            'contentvi' => <<<HTML
<h2>1. Khái niệm lực cản biến thiên (Variable Resistance)</h2>
<p>Khác với tạ tay có trọng lượng không đổi trong suốt đường chuyển động, dây kháng lực tạo lực cản tăng dần theo độ giãn dài của sợi dây. Càng kéo căng ở điểm co cơ đỉnh (Peak Contraction), lực tác động lên sợi cơ càng lớn.</p>
<h2>2. Kết luận ứng dụng</h2>
<p>Dây kháng lực như <a href="san-pham/bo-day-khang-luc-ngu-sac-pseudois-11-mon-da-nang">Bộ ngũ sắc Pseudois</a> hoàn toàn có thể kích thích tăng cơ hiệu quả tại nhà nếu áp dụng đúng nguyên tắc quá tải lũy tiến (Progressive Overload).</p>
HTML
        ),

        // KNOWLEDGE 6
        array(
            'id_list' => $idListKnow,
            'namevi' => 'Cách bảo quản và vệ sinh phụ kiện tập gym da, cao su và vải',
            'nameen' => 'How to Clean and Maintain Gym Accessories',
            'slugvi' => 'cach-bao-quan-va-ve-sinh-phu-kien-tap-gym',
            'slugen' => 'how-to-clean-and-maintain-gym-gear',
            'photo' => 'kien-thuc-bao-quan-do-tap.jpg',
            'descvi' => 'Hướng dẫn bảo dưỡng đồ tập gym: Cách lau sạch đai da không làm nứt da, vệ sinh dây kháng lực cao su không bị khô giòn và giặt găng tay vải không bốc mùi.',
            'contentvi' => <<<HTML
<h2>1. Bảo quản đai lưng da và găng tay da thật</h2>
<ul>
    <li>Lau sạch mồ hôi bằng khăn ẩm mềm sau mỗi buổi tập.</li>
    <li>Không phơi trực tiếp dưới ánh nắng mặt trời gắt hoặc sấy nhiệt cao vì sẽ làm da bị co cứng và nứt nẻ.</li>
    <li>Thoa kem dưỡng da thuộc chuyên dụng định kỳ 3-6 tháng/lần.</li>
</ul>
<h2>2. Bảo quản dây kháng lực cao su Latex</h2>
<ul>
    <li>Tránh để dây tiếp xúc với dầu nhờn, cồn hoặc bề mặt sắc nhọn.</li>
    <li>Bảo quản nơi khô ráo, thoáng mát trong túi đựng chuyên dụng.</li>
</ul>
HTML
        ),

        // KNOWLEDGE 7
        array(
            'id_list' => $idListKnow,
            'namevi' => '5 bài tập giãn cơ hiệu quả nhất với con lăn bọt Foam Roller',
            'nameen' => '5 Best Foam Rolling Exercises for Recovery',
            'slugvi' => '5-bai-tap-gian-co-hieu-qua-nhat-voi-con-lan-foam-roller',
            'slugen' => '5-best-foam-rolling-exercises',
            'photo' => 'kien-thuc-5-bai-tap-foam-roller.jpg',
            'descvi' => 'Hướng dẫn chi tiết 5 động tác lăn giãn cơ phục hồi quan trọng nhất: Cơ đùi trước (Quads), Dải chậu chày (IT Band), Lưng trên (Thoracic Spine), Cơ bắp chân (Calves) và Cơ mông (Glutes).',
            'contentvi' => <<<HTML
<h2>1. Năm bài tập Foam Rolling kinh điển</h2>
<ol>
    <li><strong>Lăn cơ đùi trước (Quadriceps):</strong> Nằm sấp, đặt con lăn dưới đùi, lăn từ trên xương bánh chè lên đến khớp háng trong 30-60 giây.</li>
    <li><strong>Lăn cơ lưng trên (Thoracic Spine):</strong> Nằm ngửa, đặt con lăn ngang bả vai, đan tay sau gáy và lăn nhẹ nhàng vùng lưng giữa đến vai. <em>(Tránh lăn vùng thắt lưng dưới)</em>.</li>
    <li><strong>Lăn cơ bắp chân (Calves):</strong> Ngồi trên thảm, đặt bắp chân lên con lăn, nâng nhẹ mông và lăn dọc từ gân Achilles lên sát khoeo chân.</li>
    <li><strong>Lăn dải chậu chày (IT Band):</strong> Nằm nghiêng, đặt mép đùi ngoài lên con lăn để giải tỏa căng tức khớp gối.</li>
    <li><strong>Lăn cơ mông (Glutes):</strong> Ngồi bắt chéo chân chữ ngũ lên con lăn và xoay nhẹ người sang bên cơ mông cần giãn.</li>
</ol>
<p>Sử dụng <a href="san-pham/con-lan-bot-gian-co-triggerpoint-grid-1-foam-roller">Con lăn TriggerPoint GRID 1.0</a> để đạt độ êm ái và hiệu quả sâu nhất.</p>
HTML
        ),

        // KNOWLEDGE 8
        array(
            'id_list' => $idListKnow,
            'namevi' => 'Lỗi sai phổ biến khi đeo đai lưng tập gym khiến giảm hiệu quả bảo vệ',
            'nameen' => 'Common Mistakes When Wearing a Weightlifting Belt',
            'slugvi' => 'loi-sai-pho-bien-khi-deo-dai-lung-tap-gym',
            'slugen' => 'common-weightlifting-belt-mistakes',
            'photo' => 'kien-thuc-loi-sai-deo-dai-lung.jpg',
            'descvi' => 'Chỉ ra 4 sai lầm tai hại khi sử dụng đai lưng: Đeo quá chặt làm teo cơ bụng, đặt sai vị trí quá cao hoặc quá thấp, hóp bụng thay vì hít phình bụng và đeo đai suốt cả buổi tập.',
            'contentvi' => <<<HTML
<h2>1. Bốn lỗi sai gymer thường mắc phải khi dùng đai lưng</h2>
<ol>
    <li><strong>Lỗi 1: Siết đai quá nghẹt thở:</strong> Siết quá chặt khiến bạn không thể hít đủ lượng không khí vào khoang bụng để tạo áp lực Valsalva. Đai chuẩn phải để lọt vừa một bàn tay khi bạn thở bình thường.</li>
    <li><strong>Lỗi 2: Hóp bụng lại khi siết đai:</strong> Cơ chế đúng là bạn phải chủ động hít hơi và gồng phình thành bụng ép chặt vào bề mặt đai.</li>
    <li><strong>Lỗi 3: Đeo đai quá thấp như thắt lưng quần:</strong> Đai cần nằm ngang vị trí rốn để ôm trọn toàn bộ ổ bụng và thắt lưng.</li>
    <li><strong>Lỗi 4: Đeo đai liên tục không tháo:</strong> Chỉ nên siết đai trong lúc thực hiện hiệp tập chính, tháo lỏng đai giữa các hiệp nghỉ để máu lưu thông tự nhiên.</li>
</ol>
HTML
        )
    );

    foreach ($articles as $idx => $a) {
        $existArt = $d->rawQueryOne("SELECT id FROM #_news WHERE slugvi = ? AND type = 'tin-tuc' LIMIT 1", array($a['slugvi']));
        if (!empty($existArt['id'])) {
            $targetId = $existArt['id'];
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
                $a['id_list'], $a['namevi'], $a['nameen'], $a['descvi'], $a['contentvi'],
                $a['photo'], ($idx + 1), $now, $targetId
            ));
            echo "  - Updated guide/knowledge: {$a['namevi']} (ID: {$targetId})" . PHP_EOL;
        } else {
            $d->rawQuery("INSERT INTO #_news (
                id_list, namevi, nameen, slugvi, slugen, descvi, contentvi, photo,
                numb, status, type, date_created
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?,
                ?, 'hienthi,noibat', 'tin-tuc', ?
            )", array(
                $a['id_list'], $a['namevi'], $a['nameen'], $a['slugvi'], $a['slugen'], $a['descvi'], $a['contentvi'],
                $a['photo'], ($idx + 1), $now
            ));
            $inserted = $d->rawQueryOne("SELECT id FROM #_news WHERE slugvi = ? AND type = 'tin-tuc' LIMIT 1", array($a['slugvi']));
            $targetId = $inserted['id'];
            echo "  - Created guide/knowledge: {$a['namevi']} (ID: {$targetId})" . PHP_EOL;
        }

        // SEO for guide/knowledge
        $seoExist = $d->rawQueryOne("SELECT id FROM #_seo WHERE id_parent = ? AND com = 'news' AND act = 'man' AND type = 'tin-tuc' LIMIT 1", array($targetId));
        $seoTitle = "{$a['namevi']} | Khỏe Pro";
        $seoDesc = $a['descvi'];
        $seoKw = mb_strtolower($a['namevi'], 'UTF-8') . ", huong dan chon mua do tap, kien thuc tap gym, khoe pro";

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

    echo "=== COMPLETED BATCH 5: BUYING GUIDES & KNOWLEDGE ARTICLES (16 ARTICLES) ===" . PHP_EOL;

} catch (Exception $e) {
    echo "ERROR in Batch 5: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
