<?php
/**
 * Script to generate crisp SVG UI screenshots for KhoePro Admin User Guide
 * PHP 7.4+ Compatible
 */

$imgDir = __DIR__ . '/images/';
if (!is_dir($imgDir)) {
    mkdir($imgDir, 0755, true);
}

$images = array(
    'dashboard-overview.svg' => array(
        'title' => 'Dashboard Tổng Quan - Các Khối KPI Thống Kê',
        'badge' => 'DASHBOARD',
        'color' => '#007bff',
        'elements' => array(
            array('box', '18 Sản phẩm trong kho', '12 Đơn hàng mới', '#28a745', '#17a2b8'),
            array('card', 'Thống kê Clicks Affiliate: 1,480 clicks hôm nay | Tỷ lệ chuyển đổi: 4.8%'),
            array('callout', 'Vị trí xem nhanh các chỉ số cốt lõi của website ngay đầu ngày')
        )
    ),
    'dashboard-header-notify.svg' => array(
        'title' => 'Thanh Header - Thông Báo & Chuyển Ngôn Ngữ',
        'badge' => 'TOP HEADER',
        'color' => '#17a2b8',
        'elements' => array(
            array('bar', 'Xin chào, Admin! | 🔔 Thông báo đơn hàng [3] | ✉️ Liên hệ [1] | 🇻🇳 VI / 🇬🇧 EN | ⚙️ Cài đặt'),
            array('callout', 'Các nút thao tác nhanh trên góc phải màn hình')
        )
    ),
    'dashboard-quick-actions.svg' => array(
        'title' => 'Lối Tắt Thao Tác Nhanh (Quick Actions)',
        'badge' => 'QUICK LINKS',
        'color' => '#6f42c1',
        'elements' => array(
            array('btn', '+ Thêm sản phẩm mới', '#28a745'),
            array('btn', '+ Viết bài AI mới', '#dc3545'),
            array('btn', 'Xem báo cáo doanh thu', '#ffc107'),
            array('callout', 'Click trực tiếp để vào ngay màn hình tác vụ mong muốn')
        )
    ),
    'research-candidates-list.svg' => array(
        'title' => 'Nghiên Cứu Sản Phẩm - Danh Sách Ứng Viên Tiềm Năng',
        'badge' => 'DISCOVERY',
        'color' => '#ffc107',
        'elements' => array(
            array('table', array(
                array('Đai lưng tập Gym Aolikes da bò 3 lớp', 'AccessTrade', '199,000 đ', 'Demand Score: 88/100 [TIỀM NĂNG CAO]', 'APPROVED'),
                array('Dây kháng lực ngũ sắc FITPRO 150LBS', 'Shopee Mall', '145,000 đ', 'Demand Score: 78/100 [TIỀM NĂNG]', 'RESEARCHED'),
                array('Bình lắc giữ nhiệt Stainless Steel 750ml', 'TikTok Shop', '220,000 đ', 'Demand Score: 65/100', 'DISCOVERED')
            )),
            array('callout', 'Sản phẩm có điểm Demand Score cao (>75) được ưu tiên chuyển đổi')
        )
    ),
    'research-candidate-detail.svg' => array(
        'title' => 'Chi Tiết Phân Tích Tiềm Năng AI & Bằng Chứng Thực Tế',
        'badge' => 'AI RESEARCH',
        'color' => '#28a745',
        'elements' => array(
            array('card', 'Vấn đề giải quyết: Hỗ trợ cố định cột sống thắt lưng khi tập Squat/Deadlift nặng'),
            array('card', 'Đối tượng mục tiêu: Gymer tập tạ từ 6 tháng trở lên, người muốn nâng mức tạ an toàn'),
            array('card', 'Bằng chứng thực tế (Evidence): Lượt bán 3,500+ trên sàn | Đánh giá 4.9/5 sao'),
            array('callout', 'Thông tin xác thực 100% từ sàn - Không bịa đặt số liệu')
        )
    ),
    'research-create-product.svg' => array(
        'title' => 'Nút Phê Duyệt & Tạo Sản Phẩm Chính Thức',
        'badge' => 'CREATE PRODUCT',
        'color' => '#28a745',
        'elements' => array(
            array('btn', '✅ TẠO SẢN PHẨM VÀO KHO (CREATE PRODUCT)', '#28a745'),
            array('callout', '1-Click chuyển toàn bộ dữ liệu ứng viên thành sản phẩm trong kho hàng')
        )
    ),
    'research-seed-add.svg' => array(
        'title' => 'Thêm Mới Từ Khóa & Seeds Cho Bot Discovery',
        'badge' => 'SEEDS CONFIG',
        'color' => '#17a2b8',
        'elements' => array(
            array('btn', '+ Thêm mới Từ khóa (Seed)', '#007bff'),
            array('callout', 'Nhấn nút Thêm mới ở góc trên bên phải màn hình')
        )
    ),
    'research-seed-form.svg' => array(
        'title' => 'Form Cấu Hình Từ Khóa & Nền Tảng Quét',
        'badge' => 'FORM SEED',
        'color' => '#6c757d',
        'elements' => array(
            array('form', 'Từ khóa tìm kiếm: [ đai lưng tập gym ] | Nền tảng: [ AccessTrade, TikTok ] | Tần suất: [ Hàng ngày ]'),
            array('callout', 'Nhập đúng từ khóa ngách Gym/Fitness để bot cào chính xác')
        )
    ),
    'research-seed-save.svg' => array(
        'title' => 'Lưu Cấu Hình Từ Khóa',
        'badge' => 'SAVE SEED',
        'color' => '#28a745',
        'elements' => array(
            array('btn', '💾 LƯU DỮ LIỆU SEED', '#28a745'),
            array('callout', 'Từ khóa sẽ được kích hoạt ngay vào hàng đợi quét tự động')
        )
    ),
    'ai-content-filter.svg' => array(
        'title' => 'Kho Nội Dung AI - Bộ Lọc Theo Sản Phẩm & Loại Nội Dung',
        'badge' => 'CONTENT FILTER',
        'color' => '#dc3545',
        'elements' => array(
            array('form', 'Sản phẩm: [ Tất cả sản phẩm ] | Loại: [ Phân tích 12 khía cạnh / Hooks / Script / SEO ] | Ngôn ngữ: [ Tiếng Việt ]'),
            array('callout', 'Dễ dàng tra cứu các phiên bản nội dung đã sinh ra')
        )
    ),
    'ai-content-status-badges.svg' => array(
        'title' => 'Các Cấp Độ Trạng Thái Kiểm Duyệt Guardrail',
        'badge' => 'COMPLIANCE BADGES',
        'color' => '#6f42c1',
        'elements' => array(
            array('badge', 'APPROVED (Đã duyệt)', '#28a745'),
            array('badge', 'REVIEW_REQUIRED (Chờ duyệt)', '#ffc107'),
            array('badge', 'REJECTED (Từ chối)', '#dc3545'),
            array('callout', 'Tuân thủ nghiêm ngặt 21 nguyên tắc an toàn nội dung & y tế')
        )
    ),
    'ai-content-approve-action.svg' => array(
        'title' => 'Phê Duyệt & Áp Dụng Nội Dung Vào Sản Phẩm',
        'badge' => 'APPROVE & APPLY',
        'color' => '#28a745',
        'elements' => array(
            array('btn', '✓ Phê duyệt phiên bản này', '#28a745'),
            array('btn', '🚀 Áp dụng vào sản phẩm (Ghi đè có Backup)', '#007bff'),
            array('callout', 'Tự động sao lưu phiên bản cũ để có thể khôi phục bất kỳ lúc nào')
        )
    ),
    'ai-content-select-product.svg' => array(
        'title' => 'Chọn Sản Phẩm Cần Tạo Nội Dung AI',
        'badge' => 'SELECT PRODUCT',
        'color' => '#007bff',
        'elements' => array(
            array('form', 'Tìm kiếm sản phẩm: [ Đai lưng tập Gym Aolikes da bò... ] 🔍'),
            array('callout', 'Hệ thống tự nạp toàn bộ thông số kỹ thuật và bằng chứng nguồn')
        )
    ),
    'ai-content-options.svg' => array(
        'title' => 'Thiết Lập Loại Nội Dung & Góc Tiếp Cận (Content Angle)',
        'badge' => 'CONTENT OPTIONS',
        'color' => '#17a2b8',
        'elements' => array(
            array('form', 'Loại nội dung: [ 7 Strategic Hooks TikTok ] | Góc tiếp cận: [ Vấn đề & Giải pháp ] | Tone: [ Chuyên gia Gym ]'),
            array('callout', 'AI sẽ tùy biến văn phong theo đúng nhóm khách hàng mục tiêu')
        )
    ),
    'ai-content-generate-btn.svg' => array(
        'title' => 'Kích Hoạt AI Sinh Nội Dung Tự Động',
        'badge' => 'GENERATE ACTION',
        'color' => '#dc3545',
        'elements' => array(
            array('btn', '✨ TẠO NỘI DUNG AI NGAY (GENERATE)', '#dc3545'),
            array('callout', 'Thời gian xử lý: 2-4 giây với Gemini AI Engine')
        )
    ),
    'ai-video-project-list.svg' => array(
        'title' => 'Danh Sách Dự Án Video AI (Projects)',
        'badge' => 'VIDEO PROJECTS',
        'color' => '#17a2b8',
        'elements' => array(
            array('table', array(
                array('Video Đai Lưng Aolikes - Hook Nỗi Đau', '30.6s', 'ECONOMY (0 VND)', 'APPROVED', '▶ Xem preview'),
                array('Video Dây Kháng Lực - 5 Bài Tập Nữ', '28.2s', 'HYBRID', 'READY', '▶ Xem preview'),
                array('Video Bình Lắc Giữ Nhiệt - Demo Test Nước', '15.0s', 'ECONOMY (0 VND)', 'RENDERING', '⏳ Đang ghép')
            )),
            array('callout', 'Quản lý toàn bộ video marketing ngắn chuẩn 9:16 dọc cho TikTok/Reels')
        )
    ),
    'ai-video-preview-player.svg' => array(
        'title' => 'Trình Phát Video Preview & Kiểm Tra Phụ Đề / Âm Thanh',
        'badge' => 'VIDEO PREVIEW',
        'color' => '#007bff',
        'elements' => array(
            array('card', '▶ Khung phát video 9:16 | Kích thước 1080x1920 | Giọng đọc AI: Onyx Nam Trầm | Phụ đề: Font Inter Vàng Nổi Bật'),
            array('callout', 'Kiểm tra khớp khẩu hình, âm lượng voiceover và độ nét trước khi duyệt')
        )
    ),
    'ai-video-approve-btn.svg' => array(
        'title' => 'Nút Phê Duyệt Video (Approve Video)',
        'badge' => 'APPROVE VIDEO',
        'color' => '#28a745',
        'elements' => array(
            array('btn', '✅ PHÊ DUYỆT VIDEO (SẴN SÀNG XUẤT BẢN)', '#28a745'),
            array('callout', 'Rào chắn Human Gate: Chỉ video ĐÃ DUYỆT mới được phép đăng lên kênh')
        )
    ),
    'ai-video-create-step1.svg' => array(
        'title' => 'Chọn Sản Phẩm & Nạp Kịch Bản Phân Cảnh (Scene Breakdown)',
        'badge' => 'VIDEO STEP 1',
        'color' => '#6f42c1',
        'elements' => array(
            array('form', 'Sản phẩm: [ Đai lưng da bò Aolikes ] | Kịch bản nguồn: [ TikTok Script v1 - 6 phân cảnh ]'),
            array('callout', 'Tự động đồng bộ các cảnh quay và lời thoại từ AI Content')
        )
    ),
    'ai-video-create-step2.svg' => array(
        'title' => 'Chọn Giọng Đọc Voiceover & Gắn Ảnh Cho Từng Cảnh',
        'badge' => 'VIDEO STEP 2',
        'color' => '#007bff',
        'elements' => array(
            array('form', 'Giọng đọc: [ Beeknoee / OpenAI TTS - Onyx ] | Cảnh 1: [ Ảnh mặt trước đai ] | Cảnh 2: [ Ảnh khóa đòn bẩy ]'),
            array('callout', 'Có thể tùy chỉnh lời đọc và chọn ảnh đại diện cho từng cảnh quay')
        )
    ),
    'ai-video-create-step3.svg' => array(
        'title' => 'Chọn Chế Độ Render Economy & Bắt Đầu Ghép Video',
        'badge' => 'VIDEO STEP 3',
        'color' => '#28a745',
        'elements' => array(
            array('btn', '🎬 BẮT ĐẦU RENDER VIDEO (CHẾ ĐỘ ECONOMY 0 VND)', '#28a745'),
            array('callout', 'Sử dụng động cơ FFmpeg server - Chi phí 0 VND')
        )
    ),
    'publishing-create-step1.svg' => array(
        'title' => 'Chọn Video Đã Duyệt & Nền Tảng Đăng Bài',
        'badge' => 'PUBLISH STEP 1',
        'color' => '#007bff',
        'elements' => array(
            array('form', 'Video nguồn: [ Video Đai Lưng Aolikes (APPROVED) ] | Nền tảng: [ TikTok / FB Reels / YT Shorts ]'),
            array('callout', 'Chỉ các video có trạng thái APPROVED mới hiển thị trong danh mục')
        )
    ),
    'publishing-create-step2.svg' => array(
        'title' => 'Tự Động Điền Caption, Hashtags & Câu Công Khai Affiliate',
        'badge' => 'PUBLISH STEP 2',
        'color' => '#17a2b8',
        'elements' => array(
            array('form', 'Caption: "Review chi tiết đai lưng da bò tập gym chuẩn form an toàn" | Hashtags: #khoepro #gym #tapgym | Disclosure: "KhoePro Affiliate Partner"'),
            array('callout', 'Đảm bảo tuân thủ 100% chính sách quảng cáo của TikTok & Meta')
        )
    ),
    'publishing-create-step3.svg' => array(
        'title' => 'Tạo Post Package Kèm Unique Tracking Code',
        'badge' => 'PUBLISH STEP 3',
        'color' => '#28a745',
        'elements' => array(
            array('btn', '🚀 TẠO POST PACKAGE (DRAFT)', '#28a745'),
            array('callout', 'Tự động tạo link Landing Page gắn mã UTM: sub1=Product, sub2=Post, sub4=TrackingCode')
        )
    ),
    'publishing-checklist-scan.svg' => array(
        'title' => 'Chạy Pre-Publish Checklist Quét 8 Tiêu Chuẩn An Toàn',
        'badge' => 'CHECKLIST SCAN',
        'color' => '#ffc107',
        'elements' => array(
            array('btn', '🔍 CHẠY PRE-PUBLISH CHECKLIST', '#ffc107'),
            array('callout', 'Quét toàn diện: Tệp video, Caption, Nền tảng, Provider, Disclosure và Guardrail')
        )
    ),
    'publishing-checklist-pass.svg' => array(
        'title' => 'Kết Quả Kiểm Định An Toàn 8/8 Tiêu Chí Đạt Chuẩn',
        'badge' => 'CHECKLIST 8/8 PASS',
        'color' => '#28a745',
        'elements' => array(
            array('badge', '✓ File video tồn tại & hợp lệ (30.6s / 5MB)', '#28a745'),
            array('badge', '✓ Caption & Hashtags đầy đủ, không vi phạm Guardrail', '#28a745'),
            array('badge', '✓ Đã gắn Affiliate Disclosure minh bạch', '#28a745'),
            array('callout', 'Đủ điều kiện 100% để chuyển sang trạng thái Sẵn Sàng (READY)')
        )
    ),
    'publishing-snapshot-lock.svg' => array(
        'title' => 'Đánh Dấu READY & Khóa Snapshot Bất Biến',
        'badge' => 'SNAPSHOT LOCK',
        'color' => '#6f42c1',
        'elements' => array(
            array('btn', '🔒 ĐÁNH DẤU READY & ĐÓNG BĂNG SNAPSHOT', '#6f42c1'),
            array('callout', 'Dữ liệu bài đăng được bảo vệ bất biến - Chống thay đổi ngoài ý muốn')
        )
    ),
    'analytics-time-filter.svg' => array(
        'title' => 'Bộ Lọc Thời Gian Báo Cáo Hiệu Suất Đo Lường',
        'badge' => 'ANALYTICS FILTER',
        'color' => '#007bff',
        'elements' => array(
            array('form', 'Khoảng thời gian: [ 7 ngày qua ▼ ] [ Hôm nay ] [ Tháng này ] [ Tùy chỉnh ] 📅'),
            array('callout', 'Tra cứu số liệu linh hoạt theo từng chiến dịch')
        )
    ),
    'analytics-chart-trend.svg' => array(
        'title' => 'Biểu Đồ Xu Hướng Tăng Trưởng Clicks & Doanh Thu',
        'badge' => 'CHART TREND',
        'color' => '#28a745',
        'elements' => array(
            array('card', '📈 Đường Clicks: Tăng 24% | Đường Doanh Thu: 12,450,000 VND | TikTok Organic: Chiếm 68%'),
            array('callout', 'Biểu đồ trực quan giúp nhận diện kênh phân phối hiệu quả nhất')
        )
    ),
    'analytics-funnel.svg' => array(
        'title' => 'Phân Tích Phễu Chuyển Đổi 4 Tầng (Conversion Funnel)',
        'badge' => 'FUNNEL 4 STAGES',
        'color' => '#6f42c1',
        'elements' => array(
            array('badge', 'Tầng 1: Lượt xem Video (125,000 views)', '#007bff'),
            array('badge', 'Tầng 2: Click Link Bio (4,200 clicks - CTR 3.3%)', '#17a2b8'),
            array('badge', 'Tầng 3: Truy cập Landing Page (3,800 visits)', '#ffc107'),
            array('badge', 'Tầng 4: Đơn hàng thành công (182 đơn - CR 4.8%)', '#28a745'),
            array('callout', 'Theo dõi điểm rơi và tối ưu từng bước trong hành trình khách hàng')
        )
    ),
    'winner-leaderboard.svg' => array(
        'title' => 'Bảng Xếp Hạng Sản Phẩm Thắng (Winner Leaderboard)',
        'badge' => 'WINNER LEADERBOARD',
        'color' => '#ffc107',
        'elements' => array(
            array('table', array(
                array('⭐ Đai lưng da bò Aolikes 10mm', '1,450 clicks', '78 đơn hàng', 'CR 5.4%', 'WINNER - TĂNG TỐC'),
                array('⭐ Dây kháng lực ngũ sắc FITPRO', '980 clicks', '42 đơn hàng', 'CR 4.3%', 'WINNER'),
                array('Bình lắc giữ nhiệt Stainless Steel', '320 clicks', '8 đơn hàng', 'CR 2.5%', 'THEO DÕI')
            )),
            array('callout', 'Sản phẩm vượt ngưỡng CR > 4% tự động được gắn cờ WINNER')
        )
    ),
    'winner-criteria-detail.svg' => array(
        'title' => 'Chi Tiết Tiêu Chí Đánh Giá Sản Phẩm Thắng',
        'badge' => 'WINNER CRITERIA',
        'color' => '#28a745',
        'elements' => array(
            array('card', 'Tiêu chí 1: Lượt click > 500 (Đạt: 1,450 clicks) ✓'),
            array('card', 'Tiêu chí 2: Tỷ lệ chuyển đổi > 3.5% (Đạt: 5.4%) ✓'),
            array('card', 'Tiêu chí 3: Tăng trưởng đơn hàng dương 3 ngày liên tiếp ✓'),
            array('callout', 'Thuật toán khách quan dựa trên dữ liệu thật từ sàn')
        )
    ),
    'winner-scale-action.svg' => array(
        'title' => 'Hành Động Tăng Tốc Sản Xuất Cho Winner',
        'badge' => 'SCALE WINNER',
        'color' => '#ffc107',
        'elements' => array(
            array('btn', '⚡ TẠO THÊM 7 HOOKS & 3 VIDEO MỚI CHO SẢN PHẨM NÀY', '#ffc107'),
            array('callout', 'Tập trung nguồn lực vào sản phẩm đang mang lại doanh thu cao nhất')
        )
    ),
    'product-list-filter.svg' => array(
        'title' => 'Danh Sách Sản Phẩm - Tra Cứu & Lọc Nhanh',
        'badge' => 'PRODUCT CATALOG',
        'color' => '#007bff',
        'elements' => array(
            array('form', 'Tìm kiếm: [ Đai lưng ] 🔍 | Danh mục: [ Phụ kiện Gym ▼ ] | Hãng: [ Aolikes ▼ ]'),
            array('callout', 'Tìm kiếm nhanh theo tên, mã sản phẩm hoặc danh mục phân loại')
        )
    ),
    'product-quick-status-toggle.svg' => array(
        'title' => 'Bật / Tắt Trạng Thái Hiển Thị Nhanh Bằng Checkbox',
        'badge' => 'QUICK TOGGLE',
        'color' => '#28a745',
        'elements' => array(
            array('form', 'Hiển thị: [☑] | Nổi bật: [☑] | Mới: [☐] (Click để đổi trạng thái AJAX tức thì)'),
            array('callout', 'Không cần mở trang chỉnh sửa, click trực tiếp trên bảng danh sách')
        )
    ),
    'product-action-buttons.svg' => array(
        'title' => 'Cột Thao Tác: Sửa, Sao Chép (Copy) & Xóa Sản Phẩm',
        'badge' => 'ACTION BUTTONS',
        'color' => '#6c757d',
        'elements' => array(
            array('btn', '✏️ Chỉnh sửa', '#007bff'),
            array('btn', '📋 Sao chép (Copy)', '#17a2b8'),
            array('btn', '🗑️ Xóa', '#dc3545'),
            array('callout', 'Thao tác thuận tiện ngay trên từng dòng sản phẩm')
        )
    ),
    'product-add-step1-image.svg' => array(
        'title' => 'Tải Lên Ảnh Đại Diện & Album Ảnh Chi Tiết',
        'badge' => 'UPLOAD IMAGES',
        'color' => '#17a2b8',
        'elements' => array(
            array('card', 'Khung chọn ảnh chính (Kích thước chuẩn: 600x600px) | Khung tải nhiều ảnh phụ Gallery'),
            array('callout', 'Hỗ trợ định dạng JPG, PNG, WebP - Tối đa 5MB')
        )
    ),
    'product-add-step2-category-price.svg' => array(
        'title' => 'Chọn Cây Danh Mục & Nhập Giá Bán / Khuyến Mãi',
        'badge' => 'PRICE & CATEGORY',
        'color' => '#007bff',
        'elements' => array(
            array('form', 'Danh mục cấp 1: [ Đồ tập Gym ] | Tên SP: [ Đai lưng Aolikes ] | Giá thường: [ 250,000 ] | Giá bán: [ 199,000 ] (Giảm 20%)'),
            array('callout', 'Hệ thống tự động tính tỷ lệ giảm giá % và hiển thị nhãn Sale')
        )
    ),
    'product-add-step3-content.svg' => array(
        'title' => 'Soạn Thảo Mô Tả, Thông Số Kỹ Thuật Specs & Bài Viết',
        'badge' => 'SPECS & CONTENT',
        'color' => '#6f42c1',
        'elements' => array(
            array('form', 'Mô tả ngắn: [ Tóm tắt ưu điểm ] | Specs: {"ChatLieu": "da bò 3 lớp", "DoDay": "10mm"} | Soạn thảo bài viết CKEditor'),
            array('callout', 'Trình soạn thảo CKEditor hỗ trợ chèn ảnh, bảng, định dạng văn bản chuyên nghiệp')
        )
    ),
    'product-add-step4-affiliate-seo.svg' => array(
        'title' => 'Gắn Link Tiếp Thị Liên Kết & Tối Ưu Thẻ SEO',
        'badge' => 'AFFILIATE & SEO',
        'color' => '#28a745',
        'elements' => array(
            array('form', 'Link Shopee: [ https://shopee.vn/... ] | Tiêu đề SEO: [ Đai Lưng Tập Gym Aolikes - KhoePro ] | Mô tả SEO: [ Đai lưng chính hãng... ]'),
            array('callout', 'Đảm bảo thẻ SEO đạt chuẩn Google và liên kết mua hàng chính xác')
        )
    ),
    'product-add-step5-save.svg' => array(
        'title' => 'Nhấn Nút "Lưu" Hoàn Tất Thêm Sản Phẩm',
        'badge' => 'SAVE PRODUCT',
        'color' => '#28a745',
        'elements' => array(
            array('btn', '💾 LƯU SẢN PHẨM (SAVE)', '#28a745'),
            array('btn', '↩ Thoát', '#6c757d'),
            array('callout', 'Sản phẩm sẽ xuất hiện ngay trên trang chủ sau khi lưu thành công')
        )
    ),
    'operations-health-badge.svg' => array(
        'title' => 'Huy Hiệu Sức Khỏe Toàn Hệ Thống (Health Status)',
        'badge' => 'HEALTH STATUS',
        'color' => '#28a745',
        'elements' => array(
            array('badge', '🟢 TRẠNG THÁI: HEALTHY (TẤT CẢ DỊCH VỤ HOẠT ĐỘNG HOÀN HẢO)', '#28a745'),
            array('callout', 'Giám sát liên tục Database, Queue Workers, API Providers và Ổ đĩa')
        )
    ),
    'operations-workers-status.svg' => array(
        'title' => 'Bảng Trạng Thái 4 Tiến Trình Nền (Background Workers)',
        'badge' => 'WORKERS STATUS',
        'color' => '#007bff',
        'elements' => array(
            array('table', array(
                array('discovery_worker', 'Tìm kiếm & phân tích ứng viên AI', 'HEALTHY (Last run: 2m ago)', '🟢 RUNNING'),
                array('content_worker', 'Sinh nội dung Hooks & Scripts AI', 'HEALTHY (Last run: 5m ago)', '🟢 RUNNING'),
                array('video_worker', 'Render video marketing FFmpeg', 'HEALTHY (Last run: 12m ago)', '🟢 IDLE'),
                array('accesstrade_sync', 'Đồng bộ đối soát đơn hàng sàn', 'HEALTHY (Last run: 15m ago)', '🟢 RUNNING')
            )),
            array('callout', 'Hỗ trợ nút "Khởi động lại (Restart/Retry)" nếu có worker bị treo')
        )
    ),
    'operations-costs-summary.svg' => array(
        'title' => 'Báo Cáo Chi Phí API & Hạn Mức Ngân Sách Ngày',
        'badge' => 'COSTS & BUDGET',
        'color' => '#ffc107',
        'elements' => array(
            array('card', 'Chi phí tiêu hao hôm nay: 45,000 VND / Ngân sách ngày: 300,000 VND (15% Đã dùng)'),
            array('card', 'Gemini AI: 12,000 VND | TTS Voice: 33,000 VND | Video Veo: 0 VND (Economy)'),
            array('callout', 'Tự động ngắt gọi API nếu chạm 100% hạn mức để bảo vệ ngân sách')
        )
    ),

    // --- PHASE 12 AUTOMATION & MISSING MODULES ---
    'research-platform-scores.svg' => array(
        'title' => 'Bảng Điểm Tiềm Năng Đa Nền Tảng (Multi-Platform Scoring)',
        'badge' => 'PLATFORM SCORES',
        'color' => '#6f42c1',
        'elements' => array(
            array('table', array(
                array('Đai lưng tập Gym da bò', 'Global: 88 | TikTok: 92 | FB: 84 | YT: 79', 'TIỀM NĂNG CAO', 'ELIGIBLE'),
                array('Dây kháng lực ngũ sắc FITPRO', 'Global: 76 | TikTok: 85 | FB: 78 | YT: 65', 'TIỀM NĂNG', 'ELIGIBLE'),
                array('Bình giữ nhiệt Inox 304', 'Global: 62 | TikTok: 55 | FB: 64 | YT: 60', 'TRUNG BÌNH', 'NOT ELIGIBLE')
            )),
            array('callout', 'Sản phẩm có TikTok Score >= 65 hoặc Global >= 65 đủ điều kiện sang Content Engine')
        )
    ),
    'research-selection-reason.svg' => array(
        'title' => 'Lý Do Lựa Chọn & Phân Tích Tiềm Năng (Selection Reason)',
        'badge' => 'SELECTION REASON',
        'color' => '#17a2b8',
        'elements' => array(
            array('card', 'Lý do: Nhu cầu bảo vệ cột sống cao trong mùa cao điểm tập gym, hoa hồng 18% hấp dẫn'),
            array('card', 'TikTok: Lực hút thị giác tốt, dễ quay video test độ bền và khả năng chịu lực'),
            array('callout', 'Lý do được ghi nhận minh bạch làm cơ sở cho AI viết kịch bản đúng trọng tâm')
        )
    ),
    'research-candidate-approve.svg' => array(
        'title' => 'Nút Phê Duyệt Đẩy Ứng Viên Sang Content Candidate Pool',
        'badge' => 'APPROVE CANDIDATE',
        'color' => '#28a745',
        'elements' => array(
            array('btn', '⚡ PHÊ DUYỆT & ĐẨY VÀO CONTENT ENGINE', '#28a745'),
            array('callout', 'Trạng thái chuyển sang APPROVED và tự động tạo hàng đợi Master Content')
        )
    ),
    'ai-master-content-facts.svg' => array(
        'title' => 'Dữ Liệu Sự Thật Cốt Lõi (Core Fact Sheet & Zero Hallucination)',
        'badge' => 'CORE FACTS',
        'color' => '#007bff',
        'elements' => array(
            array('card', 'Tên & Thương hiệu: Đai lưng Aolikes da bò 3 lớp chính hãng | Giá: 199,000 đ'),
            array('card', 'Tính năng xác thực: Da bò dày 8mm, khóa inox kép chịu lực 250kg, 6 nấc chỉnh'),
            array('card', 'Ưu đãi & Hoa hồng: Freeship toàn quốc, hoàn tiền 100% nếu nứt da | Hoa hồng 18%'),
            array('callout', 'Dữ liệu gốc được mã hóa SHA-256 source_hash đảm bảo AI không bịa đặt thông tin')
        )
    ),
    'ai-master-content-adapters.svg' => array(
        'title' => 'Bộ Chuyển Đổi Kịch Bản Đa Nền Tảng (Platform Adapters)',
        'badge' => 'PLATFORM ADAPTERS',
        'color' => '#e83e8c',
        'elements' => array(
            array('box', 'TikTok: 30s Hook + 6 Phân cảnh Shot-list', 'FB Reels: Benefit Body + CTA mua', '#e83e8c', '#17a2b8'),
            array('card', 'YouTube Shorts: SEO Title chuẩn tìm kiếm + 3 Thẻ Tags ngách + Call to Action'),
            array('callout', 'Mỗi nền tảng có định dạng tối ưu riêng biệt từ cùng một gói Master Content')
        )
    ),
    'ai-master-content-save.svg' => array(
        'title' => 'Lưu Gói Nội Dung & Kích Hoạt Policy Gate Scan',
        'badge' => 'SAVE MASTER PACKAGE',
        'color' => '#28a745',
        'elements' => array(
            array('btn', '💾 LƯU MASTER PACKAGE & QUÉT POLICY GATE', '#007bff'),
            array('callout', 'Hệ thống lưu gói nội dung và tự động chuyển sang bước kiểm duyệt an toàn')
        )
    ),
    'compliance-scan-overview.svg' => array(
        'title' => 'Tổng Quan Bảng Quét Vi Phạm Chính Sách (Compliance Scan)',
        'badge' => 'POLICY SCAN',
        'color' => '#fd7e14',
        'elements' => array(
            array('card', 'Từ cấm y tế/dược: "chữa dứt điểm", "trị khỏi 100%" → 0 vi phạm phát hiện'),
            array('card', 'Cam kết thái quá: "cam kết 100%", "tuyệt đối không đau" → 0 vi phạm'),
            array('card', 'Chính sách Affiliate sàn: Tuân thủ quy định hiển thị giá và xuất xứ sản phẩm'),
            array('callout', 'Quét tự động theo bộ quy tắc kiểm duyệt nội dung quảng cáo đa nền tảng')
        )
    ),
    'compliance-gate-badges.svg' => array(
        'title' => 'Huy Hiệu Phân Loại Policy Gate (PASS / WARNING / FAIL)',
        'badge' => 'POLICY BADGES',
        'color' => '#28a745',
        'elements' => array(
            array('badge', '🟢 PASS: Đạt 100% tiêu chuẩn - Sẵn sàng xuất bản ngay', '#28a745'),
            array('badge', '🟡 WARNING: Có từ nhạy cảm nhẹ - Tự động khử độc (Sanitize) hoặc duyệt tay', '#ffc107'),
            array('badge', '🔴 FAIL: Vi phạm nghiêm trọng từ cấm - Chặn đăng hoàn toàn', '#dc3545'),
            array('callout', 'Chỉ các nội dung đạt PASS hoặc WARNING đã sửa mới được vào Approved Pool')
        )
    ),
    'compliance-auto-sanitize.svg' => array(
        'title' => 'Tính Năng Tự Động Khử Độc Từ Ngữ (Auto-Sanitization)',
        'badge' => 'AUTO SANITIZE',
        'color' => '#17a2b8',
        'elements' => array(
            array('card', 'Gốc: "Đai lưng giúp bạn chữa dứt điểm đau lưng khi squat!"'),
            array('card', 'Đã khử độc: "Đai lưng giúp bạn hỗ trợ bảo vệ lưng và giảm nguy cơ chấn thương!"'),
            array('btn', '✨ ÁP DỤNG TỰ ĐỘNG KHỬ ĐỘC', '#28a745'),
            array('callout', '1-Click thay thế an toàn các từ nhạy cảm mà vẫn giữ nguyên thông điệp hấp dẫn')
        )
    ),
    'publishing-pool-counter.svg' => array(
        'title' => 'Bộ Đếm Kho Nội Dung Sẵn Sàng (Approved Content Pool)',
        'badge' => 'APPROVED POOL',
        'color' => '#28a745',
        'elements' => array(
            array('box', 'Approved Pool: 14 Bài Sẵn Sàng', 'Đã Lên Lịch Hôm Nay: 6 Bài', '#28a745', '#007bff'),
            array('card', 'TikTok Ready: 5 video | Facebook Reels Ready: 5 video | YouTube Ready: 4 shorts'),
            array('callout', 'Bộ lập lịch tự động lấy nội dung từ kho này theo hạn mức và khung giờ vàng')
        )
    ),
    'publishing-schedule-rules.svg' => array(
        'title' => 'Cấu Hình Quy Tắc Lập Lịch & Thời Gian Giãn Cách (Cooldown)',
        'badge' => 'SCHEDULE RULES',
        'color' => '#6f42c1',
        'elements' => array(
            array('card', 'Hạn mức kênh (Daily Quota): 3 bài/ngày/tài khoản'),
            array('card', 'Khung giờ vàng: 11:30 - 13:00 (Trưa) | 19:30 - 21:30 (Tối)'),
            array('card', 'Giãn cách (Cooldown): Tối thiểu 120 phút giữa 2 bài đăng liên tiếp cùng kênh'),
            array('callout', 'Đảm bảo đăng đều đặn, tối đa tương tác và tránh thuật toán spam của mạng xã hội')
        )
    ),
    'publishing-worker-dispatch.svg' => array(
        'title' => 'Tiến Trình Xuất Bản Tự Động (Publishing Worker & Lock Token)',
        'badge' => 'AUTO DISPATCH',
        'color' => '#007bff',
        'elements' => array(
            array('table', array(
                array('TikTok @khoepro_gym', 'Video Đai lưng Aolikes #shorts', 'Published at 11:45', 'SUCCESS'),
                array('FB Page KhoePro Store', 'Reels Đai lưng da bò tập tạ', 'Published at 12:00', 'SUCCESS'),
                array('YouTube Shorts KhoePro', 'Shorts Hướng dẫn chọn đai lưng', 'Scheduled at 19:30', 'QUEUED')
            )),
            array('callout', 'Tiến trình sử dụng Atomic Lock Token chống trùng lặp bài đăng tuyệt đối')
        )
    ),
    'analytics-social-metrics.svg' => array(
        'title' => 'Bảng Thống Kê Chỉ Số Tương Tác Mạng Xã Hội Đa Kênh',
        'badge' => 'SOCIAL METRICS',
        'color' => '#17a2b8',
        'elements' => array(
            array('box', 'Tổng Lượt Xem: 145,200 views', 'Tổng Lượt Thích & Share: 8,940', '#17a2b8', '#e83e8c'),
            array('card', 'Thời lượng xem trung bình: 18.5s/30s (Tỷ lệ giữ chân 61.6%)'),
            array('callout', 'Chỉ số đo lường được đồng bộ tự động định kỳ từ API các nền tảng')
        )
    ),
    'analytics-attribution-match.svg' => array(
        'title' => 'Đối Soát Nguồn Gốc Doanh Thu (Multi-Touch Attribution)',
        'badge' => 'ATTRIBUTION MATCH',
        'color' => '#28a745',
        'elements' => array(
            array('table', array(
                array('Đơn hàng #AT-98421', 'TikTok @khoepro_gym (utm_source=tiktok)', 'Hoa hồng: 35,820 đ', 'MATCHED'),
                array('Đơn hàng #AT-98425', 'FB Reels #post-44 (utm_source=facebook)', 'Hoa hồng: 26,100 đ', 'MATCHED'),
                array('Đơn hàng #AT-98430', 'YT Shorts #post-49 (utm_source=youtube)', 'Hoa hồng: 41,500 đ', 'MATCHED')
            )),
            array('callout', 'Truy vết chính xác từng đơn hàng về video và tài khoản tạo ra chuyển đổi')
        )
    ),
    'analytics-score-boost.svg' => array(
        'title' => 'Tự Động Tăng Điểm Tiềm Năng Sản Phẩm (Score Boost Feedback Loop)',
        'badge' => 'SCORE BOOST',
        'color' => '#ffc107',
        'elements' => array(
            array('card', 'Sản phẩm: Đai lưng tập Gym Aolikes da bò'),
            array('card', 'Điểm ban đầu: 88/100 → Phát sinh 12 đơn hàng mới → Boost: +5.0 điểm'),
            array('card', 'Điểm cập nhật mới: 93/100 [WINNER] → Ưu tiên sản xuất thêm 3 kịch bản phái sinh'),
            array('callout', 'Vòng lặp phản hồi giúp hệ thống thông minh hơn sau mỗi chiến dịch')
        )
    ),
    'ab-test-recommendations.svg' => array(
        'title' => 'Bảng Khuyến Nghị Tối Ưu AI (AI Optimization Recommendations)',
        'badge' => 'AI RECOMMENDATIONS',
        'color' => '#6f42c1',
        'elements' => array(
            array('card', 'Đề xuất 1: Video #18 có view cao nhưng CTR chỉ 1.2% → Đổi Hook 3s sang dạng "Cảnh báo sai lầm"'),
            array('card', 'Đề xuất 2: Dời CTA xuất hiện ở giây thứ 18 thay vì cuối video để đón đầu điểm rơi khán giả'),
            array('callout', 'Khuyến nghị dựa trên mô hình học máy phân tích từ 500+ bài đăng thành công')
        )
    ),
    'ab-test-create-variant.svg' => array(
        'title' => 'Tạo Thử Nghiệm A/B Mới Cho Biến Thể Kịch Bản',
        'badge' => 'CREATE A/B TEST',
        'color' => '#007bff',
        'elements' => array(
            array('form', 'Biến thể A: Hook Tò mò ("Bạn có chắc mình đang Squat đúng cách?") | CTA: Mua ngay'),
            array('form', 'Biến thể B: Hook Cảnh báo ("90% Gymer chấn thương lưng vì lỗi này!") | CTA: Nhận ưu đãi'),
            array('btn', '🧪 BẮT ĐẦU THỬ NGHIỆM A/B', '#28a745'),
            array('callout', 'Hai biến thể sẽ được đăng luân phiên để đo lường công bằng')
        )
    ),
    'ab-test-winner-report.svg' => array(
        'title' => 'Báo Cáo So Sánh & Chọn Biến Thể Chiến Thắng (Winner Variant)',
        'badge' => 'A/B WINNER REPORT',
        'color' => '#28a745',
        'elements' => array(
            array('table', array(
                array('Biến thể A (Tò mò)', '2,400 Views | CTR: 2.8%', '6 Đơn hàng', 'RUNNER UP'),
                array('Biến thể B (Cảnh báo)', '3,800 Views | CTR: 6.4%', '19 Đơn hàng', '🏆 WINNER (+128%)')
            )),
            array('btn', '⭐ CHỌN LÀM KỊCH BẢN MẶC ĐỊNH', '#28a745'),
            array('callout', 'Tự động áp dụng kịch bản chiến thắng cho các chu kỳ sản xuất tiếp theo')
        )
    ),
    'news-article-editor.svg' => array(
        'title' => 'Trình Soạn Thảo Bài Viết Tin Tức & Blog Chuẩn SEO',
        'badge' => 'BLOG EDITOR',
        'color' => '#17a2b8',
        'elements' => array(
            array('form', 'Tiêu đề bài viết: [ Hướng dẫn chọn đai lưng tập gym đúng chuẩn cho người mới ]'),
            array('form', 'Chuyên mục: [ Kiến thức tập luyện ] | Tác giả: [ HLV Thể hình KhoePro ]'),
            array('card', 'Nội dung tích hợp ảnh minh họa, bảng so sánh kích thước và link sản phẩm tiện lợi'),
            array('callout', 'Soạn thảo trực quan với đầy đủ công cụ định dạng văn bản chuyên nghiệp')
        )
    ),
    'news-article-seo.svg' => array(
        'title' => 'Cấu Hình Thẻ Meta SEO & Xem Trước Snippet Google',
        'badge' => 'SEO META CONFIG',
        'color' => '#ffc107',
        'elements' => array(
            array('card', 'Google Preview: Hướng dẫn chọn đai lưng tập gym đúng chuẩn | KhoePro Blog'),
            array('card', 'https://khoepro.com/tin-tuc/huong-dan-chon-dai-lung-tap-gym'),
            array('card', 'Meta Description: Bí quyết chọn đai lưng tập gym bảo vệ cột sống tốt nhất... (155 ký tự)'),
            array('callout', 'Tối ưu chuẩn kỹ thuật SEO On-page giúp bài viết lên top tìm kiếm tự nhiên')
        )
    ),
    'news-comments-moderation.svg' => array(
        'title' => 'Bảng Kiểm Duyệt Bình Luận & Đánh Giá Sao Của Người Dùng',
        'badge' => 'MODERATION',
        'color' => '#28a745',
        'elements' => array(
            array('table', array(
                array('Nguyễn Văn Nam (⭐⭐⭐⭐⭐)', 'Sản phẩm dùng rất chắc chắn, đỡ đau lưng hẳn khi deadlift!', '10m ago', 'ĐÃ DUYỆT'),
                array('Trần Thu Hà (⭐⭐⭐⭐⭐)', 'Giao hàng nhanh, đóng gói cẩn thận da xịn', '1h ago', 'ĐÃ DUYỆT'),
                array('Spam bot quảng cáo', 'Bấm vào link này để nhận thưởng...', '2h ago', 'TỪ CHỐI')
            )),
            array('callout', 'Duyệt hoặc ẩn nhanh bình luận chỉ với 1 click chuột')
        )
    )
);

function generateSvgContent($meta) {
    $title = htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8');
    $badge = htmlspecialchars($meta['badge'], ENT_QUOTES, 'UTF-8');
    $color = $meta['color'];

    $bodySvg = '';
    $y = 110;

    foreach ($meta['elements'] as $elem) {
        $type = $elem[0];
        if ($type === 'box') {
            $t1 = htmlspecialchars($elem[1], ENT_QUOTES, 'UTF-8');
            $t2 = htmlspecialchars($elem[2], ENT_QUOTES, 'UTF-8');
            $c1 = $elem[3];
            $c2 = $elem[4];
            $bodySvg .= "<rect x='40' y='{$y}' width='340' height='70' rx='8' fill='{$c1}' fill-opacity='0.15' stroke='{$c1}' stroke-width='1.5'/>\n";
            $bodySvg .= "<text x='60' y='" . ($y + 42) . "' font-family='Inter, Arial, sans-serif' font-size='16' font-weight='600' fill='{$c1}'>📊 {$t1}</text>\n";
            $bodySvg .= "<rect x='400' y='{$y}' width='340' height='70' rx='8' fill='{$c2}' fill-opacity='0.15' stroke='{$c2}' stroke-width='1.5'/>\n";
            $bodySvg .= "<text x='420' y='" . ($y + 42) . "' font-family='Inter, Arial, sans-serif' font-size='16' font-weight='600' fill='{$c2}'>🛒 {$t2}</text>\n";
            $y += 90;
        } elseif ($type === 'card') {
            $text = htmlspecialchars($elem[1], ENT_QUOTES, 'UTF-8');
            $bodySvg .= "<rect x='40' y='{$y}' width='700' height='54' rx='8' fill='#ffffff' stroke='#dee2e6' stroke-width='1.5'/>\n";
            $bodySvg .= "<circle cx='65' cy='" . ($y + 27) . "' r='6' fill='{$color}'/>\n";
            $bodySvg .= "<text x='85' y='" . ($y + 33) . "' font-family='Inter, Arial, sans-serif' font-size='14' font-weight='500' fill='#212529'>{$text}</text>\n";
            $y += 68;
        } elseif ($type === 'bar') {
            $text = htmlspecialchars($elem[1], ENT_QUOTES, 'UTF-8');
            $bodySvg .= "<rect x='40' y='{$y}' width='700' height='48' rx='6' fill='#343a40' stroke='#23272b' stroke-width='1'/>\n";
            $bodySvg .= "<text x='60' y='" . ($y + 30) . "' font-family='Inter, Arial, sans-serif' font-size='13' font-weight='500' fill='#f8f9fa'>{$text}</text>\n";
            $y += 65;
        } elseif ($type === 'btn') {
            $text = htmlspecialchars($elem[1], ENT_QUOTES, 'UTF-8');
            $btnCol = $elem[2];
            $bodySvg .= "<rect x='40' y='{$y}' width='320' height='46' rx='6' fill='{$btnCol}' stroke='{$btnCol}' stroke-width='1'/>\n";
            $bodySvg .= "<text x='55' y='" . ($y + 28) . "' font-family='Inter, Arial, sans-serif' font-size='13' font-weight='600' fill='#ffffff'>{$text}</text>\n";
            $y += 60;
        } elseif ($type === 'badge') {
            $text = htmlspecialchars($elem[1], ENT_QUOTES, 'UTF-8');
            $bCol = $elem[2];
            $bodySvg .= "<rect x='40' y='{$y}' width='500' height='40' rx='6' fill='{$bCol}' fill-opacity='0.15' stroke='{$bCol}' stroke-width='1.5'/>\n";
            $bodySvg .= "<text x='60' y='" . ($y + 25) . "' font-family='Inter, Arial, sans-serif' font-size='13' font-weight='600' fill='{$bCol}'>{$text}</text>\n";
            $y += 52;
        } elseif ($type === 'form') {
            $text = htmlspecialchars($elem[1], ENT_QUOTES, 'UTF-8');
            $bodySvg .= "<rect x='40' y='{$y}' width='700' height='60' rx='8' fill='#f8f9fa' stroke='#ced4da' stroke-width='1.5'/>\n";
            $bodySvg .= "<text x='55' y='" . ($y + 35) . "' font-family='Inter, Arial, sans-serif' font-size='13' font-weight='500' fill='#495057'>{$text}</text>\n";
            $y += 75;
        } elseif ($type === 'table') {
            $rows = $elem[1];
            $bodySvg .= "<rect x='40' y='{$y}' width='700' height='" . (count($rows) * 44 + 36) . "' rx='8' fill='#ffffff' stroke='#ced4da' stroke-width='1.5'/>\n";
            $bodySvg .= "<rect x='40' y='{$y}' width='700' height='36' rx='8' fill='#e9ecef'/>\n";
            $bodySvg .= "<text x='55' y='" . ($y + 23) . "' font-family='Inter, Arial, sans-serif' font-size='12' font-weight='600' fill='#495057'>TÊN HẠNG MỤC / SẢN PHẨM</text>\n";
            $bodySvg .= "<text x='360' y='" . ($y + 23) . "' font-family='Inter, Arial, sans-serif' font-size='12' font-weight='600' fill='#495057'>NGUỒN / THÔNG SỐ</text>\n";
            $bodySvg .= "<text x='560' y='" . ($y + 23) . "' font-family='Inter, Arial, sans-serif' font-size='12' font-weight='600' fill='#495057'>TRẠNG THÁI</text>\n";
            $ty = $y + 62;
            foreach ($rows as $r) {
                $c1 = htmlspecialchars($r[0], ENT_QUOTES, 'UTF-8');
                $c2 = htmlspecialchars($r[1] ?? '', ENT_QUOTES, 'UTF-8');
                $c3 = htmlspecialchars($r[3] ?? ($r[2] ?? ''), ENT_QUOTES, 'UTF-8');
                $bodySvg .= "<line x1='40' y1='" . ($ty - 22) . "' x2='740' y2='" . ($ty - 22) . "' stroke='#dee2e6' stroke-width='1'/>\n";
                $bodySvg .= "<text x='55' y='{$ty}' font-family='Inter, Arial, sans-serif' font-size='13' font-weight='500' fill='#212529'>{$c1}</text>\n";
                $bodySvg .= "<text x='360' y='{$ty}' font-family='Inter, Arial, sans-serif' font-size='12' font-weight='500' fill='#6c757d'>{$c2}</text>\n";
                $bodySvg .= "<text x='560' y='{$ty}' font-family='Inter, Arial, sans-serif' font-size='12' font-weight='600' fill='#28a745'>{$c3}</text>\n";
                $ty += 44;
            }
            $y += (count($rows) * 44 + 50);
        } elseif ($type === 'callout') {
            $text = htmlspecialchars($elem[1], ENT_QUOTES, 'UTF-8');
            $bodySvg .= "<rect x='40' y='{$y}' width='700' height='46' rx='6' fill='#e8f4fd' stroke='#bee5eb' stroke-width='1.5'/>\n";
            $bodySvg .= "<text x='60' y='" . ($y + 28) . "' font-family='Inter, Arial, sans-serif' font-size='13' font-weight='600' fill='#0c5460'>💡 {$text}</text>\n";
            $y += 58;
        }
    }

    $totalHeight = max(420, $y + 30);

    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 780 {$totalHeight}" width="100%" height="100%">
    <defs>
        <filter id="shadow" x="-5%" y="-5%" width="110%" height="110%">
            <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#000000" flood-opacity="0.08"/>
        </filter>
    </defs>
    <!-- Background Frame -->
    <rect x="10" y="10" width="760" height="{$totalHeight}" rx="12" fill="#ffffff" stroke="#e3e6ec" stroke-width="1.5" filter="url(#shadow)"/>
    
    <!-- Header Bar -->
    <rect x="10" y="10" width="760" height="64" rx="12" fill="#f8fafc" stroke="#e3e6ec" stroke-width="1.5"/>
    <rect x="10" y="60" width="760" height="14" fill="#f8fafc"/>
    
    <!-- Window Control Dots -->
    <circle cx="36" cy="38" r="6" fill="#ff5f56"/>
    <circle cx="56" cy="38" r="6" fill="#ffbd2e"/>
    <circle cx="76" cy="38" r="6" fill="#27c93f"/>
    
    <!-- Badge & Title -->
    <rect x="100" y="26" width="130" height="26" rx="6" fill="{$color}" fill-opacity="0.15"/>
    <text x="165" y="44" font-family="Inter, Arial, sans-serif" font-size="11" font-weight="600" fill="{$color}" text-anchor="middle">{$badge}</text>
    <text x="245" y="44" font-family="Inter, Arial, sans-serif" font-size="14" font-weight="600" fill="#1e293b">{$title}</text>
    
    <!-- Content Elements -->
    {$bodySvg}
</svg>
SVG;

    return $svg;
}

$count = 0;
foreach ($images as $filename => $meta) {
    $svg = generateSvgContent($meta);
    file_put_contents($imgDir . $filename, $svg);
    $count++;
}

echo "Generated {$count} SVG screenshots in {$imgDir}\n";
