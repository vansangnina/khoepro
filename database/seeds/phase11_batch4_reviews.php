<?php
define('LIBRARIES', './libraries/');
require_once 'libraries/config.php';
require_once 'libraries/class/class.PDODb.php';

$d = new PDODb($config['database']);

echo "=== START BATCH 4: EDITORIAL REVIEWS (16 IN-DEPTH ARTICLES) ===" . PHP_EOL;

try {
    $now = time();

    // Get id_list for 'danh-gia-review'
    $reviewList = $d->rawQueryOne("SELECT id FROM #_news_list WHERE slugvi = 'danh-gia-review' LIMIT 1");
    $idListReview = !empty($reviewList['id']) ? $reviewList['id'] : 3;

    $reviews = array(
        // 1. Harbinger 4-inch Leather Belt Review
        array(
            'namevi' => 'Đánh giá đai lưng da Harbinger 4-Inch: Lựa chọn tiêu chuẩn cho bài tập tạ vừa và nặng',
            'nameen' => 'Harbinger 4-Inch Padded Leather Belt Review',
            'slugvi' => 'danh-gia-dai-lung-da-harbinger-4-inch',
            'slugen' => 'harbinger-4-inch-padded-leather-belt-review',
            'photo' => 'review-dai-lung-da-harbinger-4-inch.jpg',
            'descvi' => 'Đánh giá chi tiết đai lưng da Harbinger 4-inch Padded Leather: Phân tích chất liệu da thuộc, lớp đệm lưng êm ái, cơ chế khóa 2 chốt thép và trải nghiệm thực tế trong các bài Squat, Deadlift.',
            'contentvi' => <<<HTML
<h2>1. Đặt vấn đề: Tại sao Harbinger 4-inch vẫn là tượng đài đai tập tạ?</h2>
<p>Khi nhắc đến đai lưng tập gym bằng da, <strong>Harbinger 4-inch Padded Leather Belt</strong> là cái tên xuất hiện đầu tiên trong danh sách khuyến nghị của nhiều huấn luyện viên thể hình. Không chạy theo xu hướng đai đòn bẩy cồng kềnh, Harbinger trung thành với thiết kế công thái học truyền thống: bản lưng 4 inch cong nhẹ ôm khít hõm lưng dưới và đệm mút giảm chấn.</p>

<h2>2. Đánh giá thiết kế và Chất liệu da thuộc</h2>
<p>Thân đai được làm từ da bò thuộc nguyên miếng dày dặn, các đường may viền kép chỉ dù chịu lực chạy song song giúp kết cấu đai không bị bong tách sau hàng nghìn giờ tập luyện. Điểm đặc biệt của Harbinger là lớp đệm xốp lót trong (Foam Padding) bọc vải nhung mềm mại, giúp phân tán lực nén và loại bỏ cảm giác da cấn rát vào thắt lưng khi siết chặt.</p>
<p>Khóa cài làm từ thép carbon mạ chrome chống gỉ với cơ chế 2 chốt cài (double prong) kết hợp con lăn trượt êm ái, giúp người tập dễ dàng kéo căng và khóa chặt nấc đai chỉ bằng một lực vừa phải.</p>

<h2>3. Trải nghiệm thực tế trong các bài tập</h2>
<ul>
    <li><strong>Barbell Squat:</strong> Bản rộng 4 inch (10cm) ở phần lưng cung cấp điểm tựa gồng áp lực ổ bụng (IAP) rất tốt, trong khi phần hông vát cong không hề cấn vào xương sườn hay xương chậu khi xuống sâu quá song song (deep squat).</li>
    <li><strong>Deadlift & Bent-over Row:</strong> Đai giữ cột sống thắt lưng ở vị trí trung tính (neutral spine) ổn định, tạo cảm giác an tâm tuyệt đối khi kéo tạ rời khỏi mặt sàn.</li>
</ul>

<h2>4. Ưu điểm và Hạn chế (Pros & Cons)</h2>
<p><strong>Ưu điểm:</strong></p>
<ul>
    <li>Chất liệu da thật kết hợp đệm mút mang lại độ êm ái vượt trội so với đai da thông thường.</li>
    <li>Khóa 2 chốt thép dày dặn, con lăn mượt mà dễ thao tác.</li>
    <li>Thiết kế công thái học không cản trở biên độ chuyển động.</li>
</ul>
<p><strong>Hạn chế:</strong></p>
<ul>
    <li>Da mới còn hơi cứng, cần khoảng 2-3 tuần tập luyện để da mềm và ôm sát form cơ thể.</li>
    <li>Không cứng cáp 360 độ như đai Powerlifting bản thẳng 10mm khi thử sức với mức tạ tối đa 1RM.</li>
</ul>

<h2>5. So sánh nhanh với các lựa chọn thay thế</h2>
<p>So với các loại đai nylon dán gai, Harbinger 4-inch cho độ cứng và cảm giác gồng bụng vững chắc hơn hẳn. Tuy nhiên, nếu bạn là vận động viên Powerlifting chuyên nghiệp chỉ tập Squat/Deadlift cực nặng, một chiếc đai đòn bẩy 10mm bản thẳng sẽ là lựa chọn phù hợp hơn.</p>

<h2>6. Kết luận từ Ban biên tập Khỏe Pro</h2>
<p>Harbinger 4-inch Padded Leather Belt là sự đầu tư xứng đáng cho bất kỳ gymer nào muốn tập luyện an toàn, nâng cao sức mạnh mà vẫn đề cao sự thoải mái và độ bền bỉ dài lâu.</p>
<p>👉 <em>Xem thông tin chi tiết và giá bán cập nhật tại:</em> <a href="san-pham/dai-lung-da-harbinger-4-inch-padded-leather-belt"><strong>Đai lưng da Harbinger 4-inch Padded Leather Belt</strong></a></p>
HTML
        ),

        // 2. Aolikes Lever Belt Review
        array(
            'namevi' => 'Review đai lưng đòn bẩy Aolikes Lever Belt: Đạt chuẩn Powerlifting ở mức chi phí hợp lý?',
            'nameen' => 'Aolikes 10mm Lever Belt Review',
            'slugvi' => 'review-dai-lung-don-bay-aolikes-lever-belt',
            'slugen' => 'aolikes-10mm-lever-belt-review',
            'photo' => 'review-dai-lung-don-bay-aolikes.jpg',
            'descvi' => 'Đánh giá chi tiết đai đòn bẩy Aolikes Lever Belt 10mm: Cơ chế khóa gạt siêu nhanh 1 giây, độ cứng cáp tạo áp lực ổ bụng và sự khác biệt đối với người tập Squat, Deadlift tải trọng lớn.',
            'contentvi' => <<<HTML
<h2>1. Cơn sốt đai lưng khóa đòn bẩy (Lever Belt)</h2>
<p>Đai lưng đòn bẩy đang trở thành trào lưu mạnh mẽ trong giới tập tạ nặng. Với khả năng đóng/mở chỉ bằng một cú gạt tay trong vòng 1 giây, <strong>Aolikes Lever Belt 10mm</strong> mang cấu trúc tiêu chuẩn của các dòng đai thi đấu Powerlifting quốc tế nhưng với mức giá chỉ bằng 1/3 các thương hiệu nhập khẩu.</p>

<h2>2. Phân tích cấu trúc 10mm bản thẳng</h2>
<p>Đai có bản rộng đều 10cm từ trước ra sau cùng độ dày 10mm bằng sợi da Microfiber mật độ cao ép nhiệt 4 lớp. Cấu trúc này hoạt động như một "vòng đai thép" cố định toàn bộ khoang bụng, giúp người tập tạo áp lực ổ bụng cực đại khi hít thở bằng kỹ thuật Valsalva Maneuver.</p>
<p>Bộ khóa đòn bẩy (Lever Buckle) đúc từ hợp kim kẽm-thép chắc chắn, chịu lực căng kéo cực lớn mà không bị cong vênh hay lỏng lẻo.</p>

<h2>3. Ưu điểm và Hạn chế</h2>
<p><strong>Ưu điểm:</strong></p>
<ul>
    <li>Khóa gạt siêu tiện lợi: sau hiệp tập mệt thở dốc, chỉ cần gạt nhẹ là đai bung lỏng tức thì.</li>
    <li>Độ cứng 10mm tạo điểm tựa gồng cơ bụng và lưng dưới cực kỳ vững chãi.</li>
    <li>Đường chỉ may 4 lớp gia cường tăng tuổi thọ và khả năng chịu lực.</li>
</ul>
<p><strong>Hạn chế:</strong></p>
<ul>
    <li>Độ cứng lớn có thể gây cảm giác cấn đau mạn sườn hoặc bầm nhẹ vùng xương chậu trong 1-2 tuần đầu.</li>
    <li>Muốn điều chỉnh size rộng/hẹp khi tăng giảm cân nặng cần dùng tua vít vặn lại vị trí ốc khóa.</li>
</ul>

<h2>4. Kết luận từ Khỏe Pro</h2>
<p>Aolikes Lever Belt 10mm là lựa chọn số một cho những ai muốn bước chân vào con đường tập sức mạnh chuyên sâu (Powerlifting/Strength Training) mà không muốn chi trả mức giá quá cao.</p>
<p>👉 <em>Xem thông tin sản phẩm tại:</em> <a href="san-pham/dai-lung-khoa-don-bay-aolikes-lever-buckle-powerlifting-belt"><strong>Đai lưng khóa đòn bẩy Aolikes Lever Buckle</strong></a></p>
HTML
        ),

        // 3. Harbinger Foam Core 5-inch Review
        array(
            'namevi' => 'Đánh giá đai lưng nylon Harbinger Foam Core: Có phù hợp cho tập Functional và Crossfit?',
            'nameen' => 'Harbinger 5-Inch Foam Core Belt Review',
            'slugvi' => 'danh-gia-dai-lung-nylon-harbinger-foam-core',
            'slugen' => 'harbinger-5-inch-foam-core-belt-review',
            'photo' => 'review-dai-lung-nylon-harbinger.jpg',
            'descvi' => 'Trải nghiệm đai lưng vải nylon Harbinger 5-inch Foam Core: Trọng lượng siêu nhẹ dưới 300g, độ thoáng khí, tính linh hoạt và khả năng bảo vệ trong các bài tập năng động.',
            'contentvi' => <<<HTML
<h2>1. Tổng quan về đai lưng vải nylon</h2>
<p>Không phải ai tập gym cũng nâng mức tạ 200kg. Với những người tập thể hình tổng hợp, tập Crossfit hay tập các bài nhảy hộp, cử giật (Clean & Jerk), một chiếc đai da nặng nề có thể trở thành gánh nặng cản trở sự nhanh nhẹn. <strong>Harbinger 5-inch Foam Core</strong> sinh ra để phục vụ nhóm nhu cầu này.</p>

<h2>2. Thiết kế lõi bọt xốp EVA ép nhiệt</h2>
<p>Đai sử dụng lõi xốp EVA đàn hồi bọc bên ngoài bởi lớp vải nylon dệt mật độ cao. Bản lưng mở rộng 12.7cm (5 inch) ôm khít vùng thắt lưng, trong khi hai bên hông được vát hẹp để cơ thể xoay chuyển tự nhiên.</p>
<p>Khóa cài kết hợp giữa thanh trượt thép và dải băng dán gai Velcro bản lớn giúp việc điều chỉnh độ siết diễn ra nhanh chóng và chính xác đến từng milimet.</p>

<h2>3. Kết luận từ Khỏe Pro</h2>
<p>Một chiếc đai hoàn hảo cho gymer phong trào, phụ nữ, người tập Crossfit và các bài tập tạ vừa cần sự cơ động tối đa.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/dai-lung-nylon-harbinger-5-inch-foam-core-belt"><strong>Đai lưng nylon Harbinger 5-inch Foam Core</strong></a></p>
HTML
        ),

        // 4. Pseudois 11-piece Resistance Bands Review
        array(
            'namevi' => 'Review bộ dây kháng lực Pseudois 11 chi tiết: Trải nghiệm phòng gym di động tại nhà',
            'nameen' => 'Pseudois 11-Piece Resistance Band Set Review',
            'slugvi' => 'review-bo-day-khang-luc-pseudois-11-chi-tiet',
            'slugen' => 'pseudois-11-piece-resistance-band-set-review',
            'photo' => 'review-bo-day-khang-luc-pseudois.jpg',
            'descvi' => 'Đánh giá bộ dây kháng lực ngũ sắc Pseudois 11 món: Chất liệu cao su Latex tự nhiên, khả năng gộp dây lên tới 100lbs và cách thiết lập các bài tập toàn thân tại nhà.',
            'contentvi' => <<<HTML
<h2>1. Giải pháp tập luyện toàn diện không cần tạ nặng</h2>
<p><strong>Bộ dây kháng lực ngũ sắc Pseudois 11 món</strong> là một trong những bộ dụng cụ tập luyện tại nhà bán chạy nhất hiện nay. Với 5 sợi dây ống cao su có màu sắc và mức kháng lực khác nhau cùng hệ thống tay cầm, chốt neo cửa và quấn cổ chân, bạn có thể thực hiện hầu hết các bài tập tương tự như trên giàn kéo cáp (Cable Machine) ở phòng gym.</p>

<h2>2. Chất liệu cao su Latex và các mức kháng lực</h2>
<ul>
    <li>Dây Vàng: 10 lbs (~4.5 kg) - Thích hợp khởi động, tập vai sau và phục hồi khớp.</li>
    <li>Dây Đỏ: 15 lbs (~6.8 kg) - Tập tay trước, tay sau.</li>
    <li>Dây Xanh lá: 20 lbs (~9.1 kg) - Tập ngực, ép ngực.</li>
    <li>Dây Xanh dương: 25 lbs (~11.3 kg) - Tập kéo lưng xô, chèo thuyền (Row).</li>
    <li>Dây Đen: 30 lbs (~13.6 kg) - Tập Squat, Deadlift.</li>
    <li><em>Có thể gộp cả 5 dây vào 1 tay cầm để đạt mức kháng lực tối đa 100 lbs (~45.4 kg).</em></li>
</ul>

<h2>3. Kết luận từ Khỏe Pro</h2>
<p>Bộ dây ngũ sắc Pseudois là sự lựa chọn số một cho những ai bận rộn không thể đến phòng gym, người thích tập luyện tại nhà hoặc muốn duy trì cơ bắp trong các chuyến công tác xa.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/bo-day-khang-luc-ngu-sac-pseudois-11-mon-da-nang"><strong>Bộ dây kháng lực ngũ sắc Pseudois 11 món</strong></a></p>
HTML
        ),

        // 5. Aolikes Fabric Hip Band Review
        array(
            'namevi' => 'Đánh giá set dây kháng lực vải Aolikes: Giải pháp kích hoạt cơ mông đùi chống trơn trượt',
            'nameen' => 'Aolikes Fabric Hip Resistance Band Review',
            'slugvi' => 'danh-gia-set-day-khang-luc-vai-aolikes',
            'slugen' => 'aolikes-fabric-hip-resistance-band-review',
            'photo' => 'review-set-day-vai-aolikes.jpg',
            'descvi' => 'Trải nghiệm set 3 dây kháng lực vải dệt Aolikes Hip Band: Phân tích khả năng chống cuộn gập, dải cao su bám đùi và hiệu quả kích hoạt nhóm cơ mông đùi trong bài Squat, Hip Thrust.',
            'contentvi' => <<<HTML
<h2>1. Vấn đề của dây cao su miniband truyền thống</h2>
<p>Dây cao su mỏng thường bị cuộn tròn, xoắn lại như sợi dây thừng và kẹp bứt lông chân hoặc gây lằn da rát buốt khi tập các bài dang chân. <strong>Dây kháng lực vải Aolikes</strong> khắc phục triệt để nhược điểm này nhờ bề mặt vải dệt cotton dày dặn bản rộng 8cm.</p>

<h2>2. Thiết kế dải cao su chống trượt mặt trong</h2>
<p>Mặt trong của mỗi chiếc dây Aolikes đều được may 2 dải cao su nổi chạy song song, giúp dây bám chắc chắn vào bề mặt quần tập thun/legging mà không bị trượt lên trượt xuống trong suốt set tập.</p>

<h2>3. Kết luận từ Khỏe Pro</h2>
<p>Một phụ kiện không thể thiếu trong túi tập của các bạn nữ và những ai muốn tối ưu hóa sự phát triển của cơ mông đùi.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/set-3-day-khang-luc-vai-aolikes-hip-resistance-band"><strong>Set 3 dây kháng lực vải Aolikes Hip Band</strong></a></p>
HTML
        ),

        // 6. TriggerPoint GRID 1.0 Review
        array(
            'namevi' => 'Đánh giá con lăn TriggerPoint GRID 1.0: Tại sao được coi là tiêu chuẩn vàng giãn cơ?',
            'nameen' => 'TriggerPoint GRID 1.0 Foam Roller Review',
            'slugvi' => 'danh-gia-con-lan-triggerpoint-grid-1-0',
            'slugen' => 'triggerpoint-grid-1-foam-roller-review',
            'photo' => 'review-con-lan-triggerpoint-grid-1.jpg',
            'descvi' => 'Phân tích chi tiết con lăn bọt TriggerPoint GRID 1.0: Cấu trúc rãnh 3D mô phỏng bàn tay trị liệu, độ bền lõi nhựa ABS chịu tải 225kg và hiệu quả giải phóng nút thắt mạc cơ sau tập.',
            'contentvi' => <<<HTML
<h2>1. Tiêu chuẩn vàng trong làng Foam Rolling</h2>
<p><strong>TriggerPoint GRID 1.0</strong> không đơn thuần là một ống xốp lăn lưng, đây là thiết bị phục hồi cơ bắp được nghiên cứu công phu với bề mặt rãnh 3D độc quyền mô phỏng chính xác các thao tác xoa bóp trị liệu chuyên nghiệp.</p>

<h2>2. Cấu trúc bề mặt 3 vùng Distrodensity</h2>
<ul>
    <li><strong>Vùng mặt phẳng lớn (High & Flat):</strong> Mô phỏng lòng bàn tay, giúp xoa dịu và làm ấm các nhóm cơ lớn.</li>
    <li><strong>Vùng rãnh dài (Tubular):</strong> Mô phỏng ngón tay, giúp kéo giãn các thớ cơ dọc.</li>
    <li><strong>Vùng gai nhỏ (Low & Flat):</strong> Mô phỏng đầu ngón tay, tác động sâu vào các điểm kích hoạt đau nhức (Trigger Points).</li>
</ul>

<h2>3. Kết luận từ Khỏe Pro</h2>
<p>Mặc dù có giá cao hơn con lăn thông thường, độ bền bỉ qua 5-10 năm cùng hiệu quả giải tỏa căng cứng cơ bắp vượt trội khiến TriggerPoint GRID 1.0 trở thành khoản đầu tư hoàn toàn xứng đáng.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/con-lan-bot-gian-co-triggerpoint-grid-1-foam-roller"><strong>Con lăn bọt giãn cơ TriggerPoint GRID 1.0</strong></a></p>
HTML
        ),

        // 7. Booster Pro 3 Massage Gun Review
        array(
            'namevi' => 'Review súng massage Booster Pro 3: Động cơ lực đẩy mạnh mẽ cho người tập nặng',
            'nameen' => 'Booster Pro 3 Deep Tissue Massage Gun Review',
            'slugvi' => 'review-sung-massage-booster-pro-3',
            'slugen' => 'booster-pro-3-massage-gun-review',
            'photo' => 'review-sung-massage-booster-pro-3.jpg',
            'descvi' => 'Đánh giá sức mạnh của súng massage cơ bắp Booster Pro 3: Động cơ không chổi than 126W, biên độ 12mm, 6 đầu massage chuyên dụng và thời lượng pin 2400mAh.',
            'contentvi' => <<<HTML
<h2>1. Súng massage chuyên nghiệp cho vận động viên</h2>
<p>Khác với các dòng súng massage mini chỉ rung nhẹ trên bề mặt da, <strong>Booster Pro 3</strong> là một "con quái vật" thực sự với động cơ không chổi than 126W và biên độ rung 12mm tác động trực tiếp vào các thớ cơ sâu nhất.</p>

<h2>2. Trải nghiệm lực đẩy và triệt tiêu tiếng ồn</h2>
<p>Khi tì đè mạnh vào các khối cơ dày như cơ đùi trước hay cơ mông, máy vẫn duy trì nhịp gõ đầm chắc ở tần số lên tới 3400 RPM mà không hề bị khựng hay đứng máy. Công nghệ giảm chấn khí nén thế hệ mới giữ độ ồn của máy dưới ngưỡng 45dB, rất êm ái khi sử dụng tại nhà.</p>

<h2>3. Kết luận từ Khỏe Pro</h2>
<p>Thiết bị phục hồi cơ bắp đỉnh cao dành cho gymer tập nặng, vận động viên thể hình và các huấn luyện viên chuyên nghiệp.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/sung-massage-co-bap-cam-tay-booster-pro-3-high-power"><strong>Súng massage cơ bắp Booster Pro 3</strong></a></p>
HTML
        ),

        // 8. Xiaomi Mijia Mini Gun Review
        array(
            'namevi' => 'Đánh giá súng massage mini Xiaomi Mijia: Thiết bị giãn cơ bỏ túi tiện dụng',
            'nameen' => 'Xiaomi Mijia Mini Fascia Gun Review',
            'slugvi' => 'danh-gia-sung-massage-mini-xiaomi-mijia',
            'slugen' => 'xiaomi-mijia-mini-fascia-gun-review',
            'photo' => 'review-sung-massage-xiaomi-mini.jpg',
            'descvi' => 'Đánh giá súng massage mini Xiaomi Mijia Fascia Gun: Trọng lượng siêu nhẹ 375g, vòng đèn LED cảnh báo lực ấn thông minh, cổng sạc Type-C và độ ồn dưới 40dB.',
            'contentvi' => <<<HTML
<h2>1. Súng massage nhỏ gọn trong lòng bàn tay</h2>
<p>Với trọng lượng chỉ 375 gram, <strong>Xiaomi Mijia Mini Fascia Gun</strong> là người bạn đồng hành lý tưởng cho dân văn phòng và các bạn nữ tập thể thao cần giải tỏa mỏi cơ cổ vai gáy và bắp chân mọi lúc mọi nơi.</p>

<h2>2. Tính năng đèn LED báo lực ấn thông minh</h2>
<p>Vòng đèn LED 3 màu quanh thân máy đổi màu theo lực ấn của người dùng, giúp bạn biết chính xác khi nào đang ấn đúng lực và cảnh báo khi lực ấn quá mạnh lên các vùng cơ gần xương khớp.</p>

<h2>3. Kết luận từ Khỏe Pro</h2>
<p>Thiết bị massage thông minh, đẹp mắt, tiện dụng và an toàn tuyệt đối cho nhu cầu thư giãn hàng ngày.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/sung-massage-mini-xiaomi-mijia-fascia-gun-mini"><strong>Súng massage mini Xiaomi Mijia</strong></a></p>
HTML
        ),

        // 9. Harbinger Cotton Straps Review
        array(
            'namevi' => 'Review dây kéo lưng Harbinger Cotton Padded Straps: Trợ thủ đắc lực cho bài Deadlift',
            'nameen' => 'Harbinger Padded Cotton Lifting Straps Review',
            'slugvi' => 'review-day-keo-lung-harbinger-cotton-padded-straps',
            'slugen' => 'harbinger-padded-cotton-straps-review',
            'photo' => 'review-day-keo-lung-harbinger.jpg',
            'descvi' => 'Đánh giá dây kéo lưng Deadlift Harbinger Padded Cotton: Đệm Neoprene 5mm êm cổ tay, sợi cotton dày bám chặt đòn tạ và cách quấn dây chuẩn kỹ thuật.',
            'contentvi' => <<<HTML
<h2>1. Giải phóng sức mạnh cơ lưng xô</h2>
<p>Trong các bài Deadlift hay Shrugs, lực nắm của bàn tay thường kiệt sức trước khi cơ lưng đạt ngưỡng kích thích tối đa. <strong>Harbinger Padded Cotton Straps</strong> với đệm lót cổ tay Neoprene 5mm giúp bạn tập trung 100% tinh thần vào việc kéo tạ mà không lo tuột tay hay đau rát cổ tay.</p>
<h2>2. Kết luận từ Khỏe Pro</h2>
<p>Phụ kiện giá trị cao với chi phí nhỏ mà mọi gymer nghiêm túc với bài tập lưng đều nên sở hữu trong túi đồ tập.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/day-keo-lung-lifting-straps-harbinger-padded-cotton"><strong>Dây kéo lưng Harbinger Padded Cotton</strong></a></p>
HTML
        ),

        // 10. Aolikes Figure 8 Straps Review
        array(
            'namevi' => 'Đánh giá dây kéo lưng Figure 8 Aolikes: Khóa cổ tay siêu chắc cho bài kéo nặng',
            'nameen' => 'Aolikes Figure 8 Heavy Duty Straps Review',
            'slugvi' => 'danh-gia-day-keo-lung-figure-8-aolikes',
            'slugen' => 'aolikes-figure-8-straps-review',
            'photo' => 'review-day-so-8-aolikes.jpg',
            'descvi' => 'Đánh giá dây kéo lưng số 8 Figure 8 Aolikes: Thao tác khóa nhanh 2 giây, khả năng chịu tải 300kg và so sánh với dây strap truyền thống.',
            'contentvi' => <<<HTML
<h2>1. Thiết kế số 8 khóa chặt thanh đòn</h2>
<p><strong>Dây số 8 Aolikes</strong> là vũ khí bí mật của các vận động viên Powerlifting và Strongman. Thiết kế 2 vòng lồng chéo giúp cố định đòn tạ vào cổ tay chỉ sau một thao tác xỏ tay đơn giản, không cần tốn thời gian quấn nhiều vòng.</p>
<h2>2. Kết luận từ Khỏe Pro</h2>
<p>Lựa chọn số một cho những buổi tập Deadlift tạ cực nặng hoặc bài Shrugs cầu vai tải trọng lớn.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/day-keo-lung-so-8-figure-8-aolikes-heavy-duty-straps"><strong>Dây kéo lưng số 8 Figure 8 Aolikes</strong></a></p>
HTML
        ),

        // 11. Harbinger Pro WristWrap Gloves Review
        array(
            'namevi' => 'Review găng tay Harbinger Pro WristWrap: Bảo vệ lòng bàn tay và hỗ trợ khớp cổ tay',
            'nameen' => 'Harbinger Pro WristWrap Gloves Review',
            'slugvi' => 'review-gang-tay-harbinger-pro-wristwrap',
            'slugen' => 'harbinger-pro-wristwrap-gloves-review',
            'photo' => 'review-gang-tay-harbinger-pro.jpg',
            'descvi' => 'Đánh giá găng tay da thể hình Harbinger Pro WristWrap: Chất liệu da thật nguyên bản, đệm bọt kép công thái học và đai quấn cổ tay WristWrap độc quyền.',
            'contentvi' => <<<HTML
<h2>1. Đẳng cấp bảo vệ toàn diện từ Harbinger</h2>
<p>Nếu bạn muốn đôi bàn tay không bị chai sần thô ráp trong khi khớp cổ tay vẫn được gia cố vững chắc trong các bài đẩy tạ nặng, <strong>Harbinger Pro WristWrap</strong> là câu trả lời toàn diện nhất nhờ chất liệu da thật và đai quấn cổ tay chuyên dụng.</p>
<h2>2. Kết luận từ Khỏe Pro</h2>
<p>Một đôi găng tay thể hình cao cấp bền bỉ qua nhiều năm tập luyện cường độ cao.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/gang-tay-the-hinh-harbinger-pro-wristwrap-gloves"><strong>Găng tay thể hình Harbinger Pro WristWrap</strong></a></p>
HTML
        ),

        // 12. Aolikes Crossfit Gloves Review
        array(
            'namevi' => 'Đánh giá găng tay thoáng khí Aolikes Crossfit: Cảm giác cầm nắm tự nhiên khi tập xà',
            'nameen' => 'Aolikes Breathable Crossfit Gloves Review',
            'slugvi' => 'danh-gia-gang-tay-thoang-khi-aolikes-crossfit',
            'slugen' => 'aolikes-crossfit-gloves-review',
            'photo' => 'review-gang-tay-aolikes-crossfit.jpg',
            'descvi' => 'Trải nghiệm găng tay tập gym hở lưng Aolikes Crossfit: Đệm hạt silicon tổ ong chống trượt, mu bàn tay thoáng mát và dải quấn cổ tay 45cm.',
            'contentvi' => <<<HTML
<h2>1. Thoáng khí tối đa cho buổi tập năng động</h2>
<p><strong>Găng tay Aolikes Crossfit</strong> mang lại cảm giác nhẹ nhàng, thông thoáng tuyệt đối nhờ thiết kế khoét hở mu bàn tay, giải phóng mồ hôi nhanh chóng trong các bài tập xà đơn và tạ ấm.</p>
<h2>2. Kết luận từ Khỏe Pro</h2>
<p>Đôi găng tay đa năng tuyệt vời cho người thích cảm giác thật tay và không muốn bị bí mồ hôi.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/gang-tay-tap-gym-co-quan-co-tay-aolikes-crossfit-gloves"><strong>Găng tay tập gym Aolikes Crossfit</strong></a></p>
HTML
        ),

        // 13. Liforme Yoga Mat Review
        array(
            'namevi' => 'Review thảm định tuyến Liforme Yoga Mat: Đắt đỏ nhưng có thực sự đáng tiền?',
            'nameen' => 'Liforme Alignment Yoga Mat Review',
            'slugvi' => 'review-tham-dinh-tuyen-liforme-yoga-mat',
            'slugen' => 'liforme-alignment-yoga-mat-review',
            'photo' => 'review-tham-liforme-yoga.jpg',
            'descvi' => 'Đánh giá chuyên sâu thảm yoga cao cấp Liforme 4.2mm: Bề mặt PU GripForMe chống trượt vô địch, hệ thống vạch định tuyến laser và độ bền thực tế.',
            'contentvi' => <<<HTML
<h2>1. "Chiếc Rolls-Royce" trong thế giới thảm tập Yoga</h2>
<p>Với mức giá hơn 3 triệu đồng, <strong>Liforme Yoga Mat</strong> luôn là tâm điểm bàn luận của cộng đồng yoga. Liệu độ bám dính và hệ thống định tuyến AlignForMe có thực sự tạo nên sự khác biệt xứng đáng với số tiền đầu tư?</p>

<h2>2. Bề mặt GripForMe chống trượt huyền thoại</h2>
<p>Khác biệt lớn nhất của Liforme là khi lòng bàn tay bạn càng đổ mồ hôi, bề mặt thảm lại càng bám dính chặt chẽ hơn, loại bỏ hoàn toàn hiện tượng trượt tay nguy hiểm trong các tư thế thăng bằng phức tạp.</p>

<h2>3. Kết luận từ Khỏe Pro</h2>
<p>Một chiếc thảm đỉnh cao cho những ai đam mê Yoga nghiêm túc và yêu cầu sự an toàn, chính xác tuyệt đối trong từng chuyển động.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/tham-tap-dinh-tuyen-liforme-yoga-mat-4-2mm"><strong>Thảm tập định tuyến Liforme Yoga Mat 4.2mm</strong></a></p>
HTML
        ),

        // 14. Manduka PROlite Review
        array(
            'namevi' => 'Đánh giá thảm tập Manduka PROlite: Độ bền vô song và độ êm ái bảo vệ khớp',
            'nameen' => 'Manduka PROlite Yoga Mat Review',
            'slugvi' => 'danh-gia-tham-tap-manduka-prolite',
            'slugen' => 'manduka-prolite-yoga-mat-review',
            'photo' => 'review-tham-manduka-prolite.jpg',
            'descvi' => 'Đánh giá thảm tập thể thao Manduka PROlite 4.7mm: Cấu trúc ô kín chống thấm mồ hôi, bảo vệ khớp gối hoàn hảo và chính sách bảo hành trọn đời.',
            'contentvi' => <<<HTML
<h2>1. Huyền thoại độ bền bảo hành trọn đời</h2>
<p><strong>Manduka PROlite</strong> được biết đến như một chiếc thảm "mua một lần dùng cả đời". Với cấu trúc ô kín mật độ cao, mồ hôi và bụi bẩn không thể ngấm vào bên trong, giúp thảm luôn sạch sẽ và không bị mục rách qua hàng chục năm sử dụng.</p>
<h2>2. Kết luận từ Khỏe Pro</h2>
<p>Sự lựa chọn số một cho gymer tập Bodyweight, HIIT và Yoga cần chiếc thảm siêu bền và bảo vệ khớp gối tối ưu.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/tham-tap-the-duc-chong-truot-manduka-prolite-4-7mm"><strong>Thảm tập chống trượt Manduka PROlite 4.7mm</strong></a></p>
HTML
        ),

        // 15. EPP Roller Review
        array(
            'namevi' => 'Review con lăn giãn cơ EPP High-Density: Độ cứng cao phù hợp cho nhóm cơ lớn',
            'nameen' => 'EPP High-Density Foam Roller Review',
            'slugvi' => 'review-con-lan-gian-co-epp-high-density',
            'slugen' => 'epp-high-density-roller-review',
            'photo' => 'review-con-lan-epp.jpg',
            'descvi' => 'Đánh giá con lăn bọt xốp nén EPP 45cm: Độ cứng vững chắc không lún xẹp, trọng lượng siêu nhẹ 250g và hiệu quả giãn cơ lưng đùi với giá bình dân.',
            'contentvi' => <<<HTML
<h2>1. Giải pháp giãn cơ tiết kiệm và hiệu quả</h2>
<p><strong>Con lăn xốp nén EPP High-Density</strong> mang lại độ cứng cáp chắc nịch, chiều dài 45cm thoải mái cho vùng lưng với mức giá chưa đến 200 nghìn đồng, phù hợp cho bất kỳ ai muốn tự giãn cơ tại nhà mỗi ngày.</p>
<h2>2. Kết luận từ Khỏe Pro</h2>
<p>Lựa chọn kinh tế và thực dụng cho người cần con lăn cứng cáp, bền bỉ.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/con-lan-gian-co-bot-xop-eva-epp-high-density-roller"><strong>Con lăn giãn cơ bọt xốp EPP High-Density</strong></a></p>
HTML
        ),

        // 16. Valeo Eva Foam Belt Review
        array(
            'namevi' => 'Đánh giá đai lưng Valeo Eva Foam: Lựa chọn cơ bản cho người mới bắt đầu tập Gym',
            'nameen' => 'Valeo Eva Foam Weightlifting Belt Review',
            'slugvi' => 'danh-gia-dai-lung-valeo-eva-foam',
            'slugen' => 'valeo-eva-foam-belt-review',
            'photo' => 'review-dai-lung-valeo-eva.jpg',
            'descvi' => 'Đánh giá đai lưng Valeo Eva Foam: Bản lưng mở rộng 15cm nâng đỡ tốt thắt lưng, trọng lượng nhẹ và mức giá cực kỳ phải chăng cho người mới tập.',
            'contentvi' => <<<HTML
<h2>1. Chiếc đai lưng "quốc dân" cho người mới bắt đầu</h2>
<p><strong>Valeo Eva Foam</strong> là chiếc đai lưng quen thuộc nhất tại hầu hết các phòng gym nhờ thiết kế bản lưng rộng 15cm nâng đỡ trọn vẹn thắt lưng và mức giá cực kỳ dễ tiếp cận cho học sinh, sinh viên.</p>
<h2>2. Kết luận từ Khỏe Pro</h2>
<p>Giải pháp cơ bản, an toàn và tiết kiệm cho những ai mới bước vào hành trình tập luyện thể hình.</p>
<p>👉 <em>Xem chi tiết sản phẩm:</em> <a href="san-pham/dai-lung-valeo-eva-foam-weightlifting-belt"><strong>Đai lưng Valeo Eva Foam</strong></a></p>
HTML
        )
    );

    foreach ($reviews as $idx => $r) {
        $existRev = $d->rawQueryOne("SELECT id FROM #_news WHERE slugvi = ? AND type = 'tin-tuc' LIMIT 1", array($r['slugvi']));
        if (!empty($existRev['id'])) {
            $targetId = $existRev['id'];
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
                $idListReview, $r['namevi'], $r['nameen'], $r['descvi'], $r['contentvi'],
                $r['photo'], ($idx + 1), $now, $targetId
            ));
            echo "  - Updated review: {$r['namevi']} (ID: {$targetId})" . PHP_EOL;
        } else {
            $d->rawQuery("INSERT INTO #_news (
                id_list, namevi, nameen, slugvi, slugen, descvi, contentvi, photo,
                numb, status, type, date_created
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?,
                ?, 'hienthi,noibat', 'tin-tuc', ?
            )", array(
                $idListReview, $r['namevi'], $r['nameen'], $r['slugvi'], $r['slugen'], $r['descvi'], $r['contentvi'],
                $r['photo'], ($idx + 1), $now
            ));
            $inserted = $d->rawQueryOne("SELECT id FROM #_news WHERE slugvi = ? AND type = 'tin-tuc' LIMIT 1", array($r['slugvi']));
            $targetId = $inserted['id'];
            echo "  - Created review: {$r['namevi']} (ID: {$targetId})" . PHP_EOL;
        }

        // SEO for review
        $seoExist = $d->rawQueryOne("SELECT id FROM #_seo WHERE id_parent = ? AND com = 'news' AND act = 'man' AND type = 'tin-tuc' LIMIT 1", array($targetId));
        $seoTitle = "{$r['namevi']} | Khỏe Pro";
        $seoDesc = $r['descvi'];
        $seoKw = mb_strtolower($r['namevi'], 'UTF-8') . ", danh gia do tap gym, review do tap, khoe pro";

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

    echo "=== COMPLETED BATCH 4: EDITORIAL REVIEWS (16 IN-DEPTH ARTICLES) ===" . PHP_EOL;

} catch (Exception $e) {
    echo "ERROR in Batch 4: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
