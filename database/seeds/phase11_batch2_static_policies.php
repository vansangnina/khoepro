<?php
define('LIBRARIES', './libraries/');
require_once 'libraries/config.php';
require_once 'libraries/class/class.PDODb.php';

$d = new PDODb($config['database']);

echo "=== START BATCH 2: STATIC CONTENT & EDITORIAL POLICIES ===" . PHP_EOL;

try {
    $now = time();

    // 1. Static: Giới thiệu (gioi-thieu)
    $aboutDesc = "Khỏe Pro (khoepro.com) là nền tảng nội dung chuyên sâu, đánh giá độc lập và so sánh khách quan các dụng cụ tập gym, thiết bị thể thao và giải pháp phục hồi cơ bắp.";
    $aboutContent = <<<HTML
<h2>1. Khỏe Pro là ai?</h2>
<p><strong>Khỏe Pro</strong> (trực thuộc hệ sinh thái <a href="https://khoepro.com">khoepro.com</a>) được xây dựng với sứ mệnh trở thành điểm tựa thông tin đáng tin cậy cho cộng đồng người tập gym, thể hình, thể thao đa năng và những ai đang theo đuổi lối sống khỏe mạnh tại Việt Nam.</p>
<p>Chúng tôi hiểu rằng giữa ma trận hàng nghìn sản phẩm dụng cụ tập luyện trên thị trường — từ đai lưng tập tạ, dây kháng lực, thảm yoga cho đến các thiết bị công nghệ cao như súng massage — người tập rất dễ rơi vào bẫy quảng cáo thổi phồng hoặc mua phải sản phẩm không phù hợp với nhu cầu và thể trạng của mình.</p>

<h2>2. Định vị và Tôn chỉ hoạt động</h2>
<ul>
    <li><strong>Khách quan & Độc lập:</strong> Mọi bài viết đánh giá và so sánh đều dựa trên nghiên cứu thông số kỹ thuật thực tế, tài liệu nhà sản xuất và tiêu chuẩn an toàn chuyển động.</li>
    <li><strong>Không thổi phồng công dụng:</strong> Chúng tôi nói rõ ưu điểm lẫn hạn chế của từng sản phẩm. Không có sản phẩm nào là "hoàn hảo cho tất cả mọi người".</li>
    <li><strong>An toàn tập luyện là ưu tiên hàng đầu:</strong> Khỏe Pro luôn khuyến khích người tập ưu tiên kỹ thuật chuẩn xác, bảo vệ hệ xương khớp trước khi tăng mức tạ.</li>
</ul>

<h2>3. Cơ cấu nội dung chính trên Khỏe Pro</h2>
<p>Nội dung trên website được chia thành 4 trụ cột chính:</p>
<ol>
    <li><strong>Đánh giá sản phẩm (Reviews):</strong> Phân tích chi tiết chất liệu, công thái học, độ hoàn thiện, ưu và nhược điểm của từng món đồ tập.</li>
    <li><strong>Hướng dẫn chọn mua (Buying Guides):</strong> Cung cấp bộ tiêu chí rõ ràng (độ dày, chất liệu, size số, ngân sách) giúp bạn tự tin đưa ra quyết định mua sắm thông minh.</li>
    <li><strong>So sánh đối đầu (Comparisons):</strong> Đặt các sản phẩm hoặc loại vật liệu lên bàn cân (như đai da vs đai nylon, thảm cao su vs thảm TPE) để làm rõ sự khác biệt.</li>
    <li><strong>Kiến thức tập luyện & Phục hồi:</strong> Hướng dẫn sử dụng phụ kiện an toàn, kỹ thuật giãn cơ và tối ưu hóa thời gian hồi phục thể lực.</li>
</ol>

<h2>4. Cam kết biên tập</h2>
<p>Chúng tôi cam kết duy trì tính minh bạch tuyệt đối. Toàn bộ nội dung đều được kiểm duyệt kỹ lưỡng trước khi xuất bản nhằm đảm bảo mang lại giá trị thực tế cao nhất cho bạn đọc.</p>
HTML;

    $existAbout = $d->rawQueryOne("SELECT id FROM #_static WHERE type = 'gioi-thieu' LIMIT 1");
    if (!empty($existAbout['id'])) {
        $d->rawQuery("UPDATE #_static SET namevi = 'Giới thiệu về Khỏe Pro', nameen = 'About Khoe Pro', descvi = ?, contentvi = ?, status = 'hienthi', date_updated = ? WHERE id = ?", array(
            $aboutDesc, $aboutContent, $now, $existAbout['id']
        ));
        echo "✓ Updated static: gioi-thieu" . PHP_EOL;
    } else {
        $d->rawQuery("INSERT INTO #_static (namevi, nameen, descvi, contentvi, type, status, date_created) VALUES ('Giới thiệu về Khỏe Pro', 'About Khoe Pro', ?, ?, 'gioi-thieu', 'hienthi', ?)", array(
            $aboutDesc, $aboutContent, $now
        ));
        echo "✓ Created static: gioi-thieu" . PHP_EOL;
    }

    // SEO for gioi-thieu
    $seoAbout = $d->rawQueryOne("SELECT id FROM #_seo WHERE com = 'static' AND type = 'gioi-thieu' LIMIT 1");
    if (!empty($seoAbout['id'])) {
        $d->rawQuery("UPDATE #_seo SET titlevi = 'Giới thiệu Khỏe Pro - Nền tảng Đánh giá & So sánh Dụng cụ Thể thao', descriptionvi = ?, keywordsvi = 'gioi thieu khoe pro, ve chung toi, danh gia do tap gym' WHERE id = ?", array(
            $aboutDesc, $seoAbout['id']
        ));
    } else {
        $d->rawQuery("INSERT INTO #_seo (id_parent, com, act, type, titlevi, descriptionvi, keywordsvi, main_keywordsvi, main_keywordsen) VALUES (0, 'static', 'update', 'gioi-thieu', 'Giới thiệu Khỏe Pro - Nền tảng Đánh giá & So sánh Dụng cụ Thể thao', ?, 'gioi thieu khoe pro, ve chung toi, danh gia do tap gym', '', '')", array(
            $aboutDesc
        ));
    }

    // 2. Static: Slogan, Copyright, Footer, Lienhe
    $d->rawQuery("UPDATE #_static SET namevi = 'Lựa chọn thông minh hơn. Tập luyện khỏe hơn.', status = 'hienthi', date_updated = ? WHERE type = 'slogan'", array($now));
    $d->rawQuery("UPDATE #_static SET namevi = 'Copyright © 2026 Khỏe Pro (khoepro.com). All rights reserved.', status = 'hienthi', date_updated = ? WHERE type = 'copyright'", array($now));
    
    $footerContent = "<p><strong>Khỏe Pro (khoepro.com)</strong> là nền tảng đánh giá, so sánh dụng cụ tập gym, thể thao và chia sẻ kiến thức thể hình chuyên sâu. Chúng tôi giúp bạn lựa chọn đúng thiết bị, nâng cao hiệu quả tập luyện và phòng ngừa chấn thương.</p>";
    $d->rawQuery("UPDATE #_static SET contentvi = ?, status = 'hienthi', date_updated = ? WHERE type = 'footer'", array($footerContent, $now));

    $contactContent = <<<HTML
<h2>Thông tin liên hệ Ban biên tập Khỏe Pro</h2>
<p>Nếu bạn có bất kỳ câu hỏi, phản hồi về nội dung bài viết, góp ý cải tiến hoặc muốn gửi thông tin sản phẩm để thẩm định, xin vui lòng liên hệ với chúng tôi qua các kênh sau:</p>
<ul>
    <li><strong>Tên thương hiệu:</strong> Khỏe Pro</li>
    <li><strong>Website chính thức:</strong> <a href="https://khoepro.com">https://khoepro.com</a></li>
    <li><strong>Địa chỉ văn phòng:</strong> 123 Huỳnh Thúc Kháng, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</li>
    <li><strong>Email tiếp nhận thông tin & phản hồi:</strong> <a href="mailto:contact@khoepro.com">contact@khoepro.com</a></li>
    <li><strong>Hotline hỗ trợ:</strong> 0988.123.456</li>
    <li><strong>Giờ làm việc:</strong> 08:00 - 18:00 từ Thứ Hai đến Thứ Bảy</li>
</ul>
<p><em>Lưu ý: Khỏe Pro là đơn vị biên tập nội dung độc lập, không trực tiếp xử lý các khiếu nại về giao nhận hàng hóa của các sàn thương mại điện tử bên thứ ba.</em></p>
HTML;
    $d->rawQuery("UPDATE #_static SET namevi = 'Liên hệ Ban biên tập Khỏe Pro', contentvi = ?, status = 'hienthi', date_updated = ? WHERE type = 'lienhe'", array($contactContent, $now));
    echo "✓ Updated static: slogan, copyright, footer, lienhe" . PHP_EOL;

    // 3. Editorial Policies (table_news: type = 'chinh-sach')
    $policies = array(
        array(
            'namevi' => 'Phương pháp đánh giá & Tiêu chuẩn biên tập',
            'nameen' => 'Review Methodology & Editorial Policy',
            'slugvi' => 'phuong-phap-danh-gia',
            'slugen' => 'review-methodology',
            'descvi' => 'Tìm hiểu quy trình nghiên cứu, thẩm định thông số kỹ thuật và các tiêu chuẩn đánh giá nghiêm ngặt của đội ngũ Khỏe Pro.',
            'contentvi' => <<<HTML
<h2>1. Nguyên tắc cốt lõi của Khỏe Pro</h2>
<p>Tại <strong>Khỏe Pro</strong>, chúng tôi tin rằng một bài review có giá trị phải giúp người đọc giải quyết được vấn đề thực tế: <em>Sản phẩm này có đáng tiền không? Có an toàn cho bài tập của tôi không? Và ai là người nên mua?</em></p>

<h2>2. Phân định rõ ràng các cấp độ đánh giá</h2>
<p>Chúng tôi minh bạch về nguồn gốc dữ liệu đằng sau từng bài viết:</p>
<ul>
    <li><strong>Phân tích dữ liệu & Thông số (Data & Spec Analysis):</strong> Đối chiếu thông số kỹ thuật do nhà sản xuất công bố, tiêu chuẩn vật liệu (da bò, nylon 1000D, cao su Latex tự nhiên, mút bọt EPP) và phản hồi tổng hợp từ cộng đồng tập luyện.</li>
    <li><strong>Đánh giá của Biên tập viên (Editor Review):</strong> Biên tập viên am hiểu bộ môn phân tích cấu trúc công thái học, độ an toàn và so sánh trực quan với các đối thủ cùng phân khúc.</li>
    <li><strong>Kiểm nghiệm thực tế (Real Test):</strong> Khi bài viết được gắn nhãn này, sản phẩm đã được đưa vào các buổi tập thực tế để kiểm tra độ bám, độ bền khóa đòn bẩy hoặc độ êm ái khi chịu tải nặng.</li>
</ul>

<h2>3. Tiêu chí đánh giá sản phẩm</h2>
<ol>
    <li><strong>Độ bền & Chất liệu (Durability & Materials):</strong> Khả năng chịu lực kéo, chống mài mòn, đường may chịu lực và độ ổn định qua thời gian.</li>
    <li><strong>Công năng & Độ an toàn (Functionality & Safety):</strong> Hỗ trợ đúng vị trí chuyển động, giảm áp lực lên khớp, không gây cấn đau hay trơn tuột khi đổ mồ hôi.</li>
    <li><strong>Độ thoải mái & Công thái học (Comfort & Ergonomics):</strong> Sự vừa vặn, dễ thao tác đeo tháo, độ thoáng khí của vật liệu tiếp xúc với da.</li>
    <li><strong>Giá trị trên giá thành (Value for Money):</strong> Mức độ xứng đáng của sản phẩm so với số tiền bỏ ra trong phân khúc giá.</li>
</ol>

<h2>4. Cam kết không giả mạo trải nghiệm</h2>
<p>Khỏe Pro tuyệt đối không tạo ra các đánh giá giả mạo (fake review), không bịa đặt số sao đánh giá (4.9/5 không căn cứ), và không đưa ra các tuyên bố y tế chưa được kiểm chứng.</p>
HTML
        ),
        array(
            'namevi' => 'Minh bạch liên kết hoa hồng (Affiliate Disclosure)',
            'nameen' => 'Affiliate Disclosure',
            'slugvi' => 'minh-bach-lien-ket-affiliate',
            'slugen' => 'affiliate-disclosure',
            'descvi' => 'Chính sách minh bạch về liên kết tiếp thị liên kết (Affiliate Links) và cam kết độc lập của ban biên tập Khỏe Pro.',
            'contentvi' => <<<HTML
<h2>1. Khỏe Pro duy trì hoạt động như thế nào?</h2>
<p>Khỏe Pro là nền tảng nội dung miễn phí cho tất cả độc giả. Để trang trải chi phí vận hành máy chủ, nghiên cứu nội dung và duy trì đội ngũ biên tập, chúng tôi tham gia vào các chương trình tiếp thị liên kết (Affiliate Marketing) từ các sàn thương mại điện tử và đối tác uy tín như ACCESSTRADE, Shopee, Lazada, Tiki.</p>

<h2>2. Tiếp thị liên kết hoạt động ra sao?</h2>
<p>Khi bạn nhấp vào một liên kết giới thiệu nơi bán trên website Khỏe Pro và hoàn tất mua hàng, chúng tôi có thể nhận được một khoản hoa hồng nhỏ từ nhà bán hàng.</p>
<p><strong>Điều này hoàn toàn KHÔNG làm tăng giá bán sản phẩm đối với bạn.</strong> Bạn vẫn được hưởng mức giá niêm yết, các chương trình khuyến mãi, voucher giảm giá và chính sách bảo hành chuẩn từ sàn thương mại điện tử.</p>

<h2>3. Cam kết tính độc lập trong đánh giá</h2>
<ul>
    <li>Hoa hồng affiliate <strong>không bao giờ</strong> là yếu tố quyết định việc sản phẩm được khen hay bị chê trên Khỏe Pro.</li>
    <li>Chúng tôi luôn nêu rõ các nhược điểm (Cons) và đối tượng không phù hợp của sản phẩm ngay cả khi sản phẩm đó có mức hoa hồng hấp dẫn.</li>
    <li>Các vị trí Top khuyến nghị được sắp xếp dựa trên sự phù hợp thực tế với nhu cầu của người tập, không dựa vào đơn vị tài trợ.</li>
</ul>
HTML
        ),
        array(
            'namevi' => 'Chính sách bảo mật thông tin',
            'nameen' => 'Privacy Policy',
            'slugvi' => 'chinh-sach-bao-mat',
            'slugen' => 'privacy-policy',
            'descvi' => 'Cam kết bảo mật thông tin cá nhân và quyền riêng tư của độc giả khi truy cập website khoepro.com.',
            'contentvi' => <<<HTML
<h2>1. Thu thập thông tin</h2>
<p>Khỏe Pro tôn trọng quyền riêng tư của người dùng. Chúng tôi chỉ thu thập thông tin cơ bản khi bạn chủ động đăng ký nhận bản tin (email) hoặc gửi câu hỏi qua biểu mẫu liên hệ.</p>

<h2>2. Sử dụng thông tin</h2>
<p>Dữ liệu được thu thập chỉ nhằm mục đích:</p>
<ul>
    <li>Gửi các bài viết đánh giá mới, cẩm nang tập luyện và thông báo cập nhật nội dung hữu ích (nếu bạn đã đăng ký).</li>
    <li>Giải đáp thắc mắc và hỗ trợ phản hồi từ người đọc.</li>
    <li>Phân tích lưu lượng truy cập ẩn danh (qua Google Analytics) nhằm tối ưu tốc độ tải trang và trải nghiệm người dùng.</li>
</ul>

<h2>3. Bảo mật và Chia sẻ dữ liệu</h2>
<p>Khỏe Pro cam kết <strong>không bán, không trao đổi hoặc chia sẻ thông tin cá nhân</strong> của bạn cho bất kỳ bên thứ ba nào vì mục đích thương mại riêng biệt, ngoại trừ các trường hợp theo yêu cầu của pháp luật hiện hành.</p>
HTML
        ),
        array(
            'namevi' => 'Điều khoản sử dụng',
            'nameen' => 'Terms of Service',
            'slugvi' => 'dieu-khoan-su-dung',
            'slugen' => 'terms-of-service',
            'descvi' => 'Các điều khoản, quyền và trách nhiệm của người dùng khi tiếp cận và sử dụng nội dung trên nền tảng Khỏe Pro.',
            'contentvi' => <<<HTML
<h2>1. Chấp thuận điều khoản</h2>
<p>Khi truy cập và sử dụng website <strong>khoepro.com</strong>, bạn đồng ý tuân thủ các điều khoản sử dụng được quy định dưới đây.</p>

<h2>2. Tuyên bố miễn trừ trách nhiệm y tế (Health & Medical Disclaimer)</h2>
<p>Toàn bộ nội dung trên Khỏe Pro — bao gồm bài viết review, hướng dẫn tập luyện và gợi ý phụ kiện — được biên soạn với mục đích cung cấp thông tin tham khảo chung.</p>
<p><em>Nội dung này không thể thay thế cho các chẩn đoán, tư vấn chuyên môn từ bác sĩ chuyên khoa hoặc huấn luyện viên thể hình có chứng chỉ hành nghề. Người tập có tiền sử bệnh lý xương khớp, tim mạch nên tham khảo ý kiến chuyên gia y tế trước khi thực hiện các bài tập tải trọng nặng.</em></p>

<h2>3. Bản quyền nội dung</h2>
<p>Toàn bộ bài viết, cấu trúc tổng hợp dữ liệu, hình ảnh đồ họa biên tập thuộc quyền sở hữu trí tuệ của Khỏe Pro. Mọi hành vi sao chép nội dung nhằm mục đích thương mại phải được sự đồng ý bằng văn bản hoặc ghi rõ nguồn liên kết trỏ về <a href="https://khoepro.com">khoepro.com</a>.</p>
HTML
        )
    );

    foreach ($policies as $idx => $pol) {
        $existPol = $d->rawQueryOne("SELECT id FROM #_news WHERE slugvi = ? AND type = 'chinh-sach' LIMIT 1", array($pol['slugvi']));
        if (!empty($existPol['id'])) {
            $d->rawQuery("UPDATE #_news SET namevi = ?, nameen = ?, descvi = ?, contentvi = ?, numb = ?, status = 'hienthi', date_updated = ? WHERE id = ?", array(
                $pol['namevi'], $pol['nameen'], $pol['descvi'], $pol['contentvi'], ($idx + 1), $now, $existPol['id']
            ));
            $targetId = $existPol['id'];
            echo "  - Updated policy: {$pol['namevi']} (ID: {$targetId})" . PHP_EOL;
        } else {
            $d->rawQuery("INSERT INTO #_news (namevi, nameen, slugvi, slugen, descvi, contentvi, numb, status, type, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, 'hienthi', 'chinh-sach', ?)", array(
                $pol['namevi'], $pol['nameen'], $pol['slugvi'], $pol['slugen'], $pol['descvi'], $pol['contentvi'], ($idx + 1), $now
            ));
            $inserted = $d->rawQueryOne("SELECT id FROM #_news WHERE slugvi = ? AND type = 'chinh-sach' LIMIT 1", array($pol['slugvi']));
            $targetId = $inserted['id'];
            echo "  - Created policy: {$pol['namevi']} (ID: {$targetId})" . PHP_EOL;
        }

        // SEO for policy
        $seoExist = $d->rawQueryOne("SELECT id FROM #_seo WHERE id_parent = ? AND com = 'news' AND act = 'man' AND type = 'chinh-sach' LIMIT 1", array($targetId));
        $seoTitle = "{$pol['namevi']} | Khỏe Pro";
        $seoDesc = $pol['descvi'];
        if (!empty($seoExist['id'])) {
            $d->rawQuery("UPDATE #_seo SET titlevi = ?, descriptionvi = ?, keywordsvi = ? WHERE id = ?", array(
                $seoTitle, $seoDesc, mb_strtolower($pol['namevi'], 'UTF-8') . ", khoe pro", $seoExist['id']
            ));
        } else {
            $d->rawQuery("INSERT INTO #_seo (id_parent, com, act, type, titlevi, descriptionvi, keywordsvi, main_keywordsvi, main_keywordsen) VALUES (?, 'news', 'man', 'chinh-sach', ?, ?, ?, '', '')", array(
                $targetId, $seoTitle, $seoDesc, mb_strtolower($pol['namevi'], 'UTF-8') . ", khoe pro"
            ));
        }
    }

    echo "=== COMPLETED BATCH 2: STATIC CONTENT & EDITORIAL POLICIES ===" . PHP_EOL;

} catch (Exception $e) {
    echo "ERROR in Batch 2: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
