<?php
/**
 * Dữ liệu Hướng Dẫn Sử Dụng Admin KhoePro
 * Toàn bộ 10 nhóm chức năng và các bài hướng dẫn chi tiết từng bước
 * PHP 7.4+ Compatible
 */

if (!defined('HUONGDAN_ROOT')) {
    define('HUONGDAN_ROOT', __DIR__ . '/../');
}

class GuideRepository {
    public static function getCategories() {
        return array(
            'tong-quan' => array(
                'id' => 'tong-quan',
                'name' => 'Bắt đầu & Dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'badge' => 'badge-primary',
                'description' => 'Hướng dẫn đăng nhập, xem thống kê KPI và tổng quan bảng điều khiển Admin'
            ),
            'nghien-cuu-san-pham' => array(
                'id' => 'nghien-cuu-san-pham',
                'name' => 'Nghiên cứu Sản phẩm & AI Discovery',
                'icon' => 'fas fa-search-dollar',
                'badge' => 'badge-warning',
                'description' => 'Tìm kiếm sản phẩm tiềm năng, quản lý từ khóa ngách và cào dữ liệu tự động'
            ),
            'ai-content' => array(
                'id' => 'ai-content',
                'name' => 'AI Content Engine',
                'icon' => 'fas fa-magic',
                'badge' => 'badge-danger',
                'description' => 'Tự động tạo bài phân tích 12 khía cạnh, 7 dạng hook TikTok, kịch bản video và SEO'
            ),
            'ai-video' => array(
                'id' => 'ai-video',
                'name' => 'AI Video Engine',
                'icon' => 'fas fa-video',
                'badge' => 'badge-info',
                'description' => 'Ghép video marketing tự động, lồng tiếng voiceover AI và quản lý chi phí render'
            ),
            'xuat-ban-tiktok' => array(
                'id' => 'xuat-ban-tiktok',
                'name' => 'Xuất bản & Phân phối TikTok',
                'icon' => 'fas fa-paper-plane',
                'badge' => 'badge-success',
                'description' => 'Tạo gói bài đăng, kiểm duyệt an toàn Guardrail, lên lịch và quản lý tài khoản mạng xã hội'
            ),
            'do-luong-winner' => array(
                'id' => 'do-luong-winner',
                'name' => 'Đo lường, Chuyển đổi & Winner',
                'icon' => 'fas fa-chart-line',
                'badge' => 'badge-purple',
                'description' => 'Theo dõi Clicks, đối soát đơn hàng hoa hồng sàn và thuật toán phát hiện Winner'
            ),
            'toi-uu-ab' => array(
                'id' => 'toi-uu-ab',
                'name' => 'Tối ưu hóa & Thử nghiệm A/B',
                'icon' => 'fas fa-lightbulb',
                'badge' => 'badge-warning',
                'description' => 'Xem khuyến nghị cải thiện chuyển đổi và chạy thử nghiệm A/B nội dung'
            ),
            'trung-tam-van-hanh' => array(
                'id' => 'trung-tam-van-hanh',
                'name' => 'Trung tâm Vận hành (Operations)',
                'icon' => 'fas fa-cogs',
                'badge' => 'badge-dark',
                'description' => 'Giám sát sức khỏe hệ thống, background workers, quản lý ngân sách API và cảnh báo'
            ),
            'quan-ly-san-pham' => array(
                'id' => 'quan-ly-san-pham',
                'name' => 'Quản lý Sản phẩm & Danh mục',
                'icon' => 'fas fa-boxes',
                'badge' => 'badge-primary',
                'description' => 'Thêm mới, chỉnh sửa sản phẩm, cây danh mục đa cấp, hãng sản xuất và import/export Excel'
            ),
            'tin-tuc-bai-viet' => array(
                'id' => 'tin-tuc-bai-viet',
                'name' => 'Tin tức, Bình luận & Cấu hình Web',
                'icon' => 'fas fa-newspaper',
                'badge' => 'badge-secondary',
                'description' => 'Quản lý bài viết blog, duyệt đánh giá sao, hình ảnh banner, tài khoản và cài đặt chung'
            )
        );
    }

    public static function getArticles() {
        return array(
            // -------------------------------------------------------------
            // NHÓM 1: BẮT ĐẦU & DASHBOARD
            // -------------------------------------------------------------
            'tong-quan-dashboard' => array(
                'id' => 'tong-quan-dashboard',
                'category_id' => 'tong-quan',
                'title' => 'Tổng quan Bảng điều khiển (Dashboard)',
                'summary' => 'Hướng dẫn làm quen giao diện Admin, theo dõi nhanh chỉ số KPI và các lối tắt quan trọng',
                'purpose' => 'Giúp người quản trị nắm bắt ngay tức thì tình hình vận hành của website: số lượng đơn hàng mới, bình luận cần duyệt, lượng truy cập và trạng thái các module tự động.',
                'when_to_use' => 'Ngay sau khi đăng nhập vào hệ thống Admin hoặc khi cần xem bức tranh tổng quan đầu ngày làm việc.',
                'prerequisites' => 'Tài khoản quản trị viên đã đăng nhập thành công vào trang Admin.',
                'menu_path' => 'Thanh menu bên trái → Click chọn "Dashboard" (hoặc click vào Logo KhoePro ở góc trên bên trái).',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Xem các khối thống kê nhanh (Statistic Cards)',
                        'content' => 'Ở hàng trên cùng của Dashboard, bạn sẽ thấy các thẻ số liệu trực quan: Tổng số sản phẩm trong kho, Đơn hàng mới phát sinh, Lượt click Affiliate trong ngày và Bình luận mới cần phê duyệt.',
                        'image' => 'dashboard-overview.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Kiểm tra thông báo nhanh tại thanh Header',
                        'content' => 'Góc trên cùng bên phải có các biểu tượng chuông thông báo: Icon giỏ hàng hiển thị số đơn hàng mới, Icon lá thư hiển thị liên hệ/newsletter mới gửi, Icon cờ ngôn ngữ cho phép chuyển đổi giao diện Tiếng Việt / Tiếng Anh.',
                        'image' => 'dashboard-header-notify.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Sử dụng các lối tắt điều hướng (Quick Links)',
                        'content' => 'Dashboard cung cấp các nút bấm nhanh để bạn truy cập trực tiếp vào các tác vụ thường ngày như "Thêm sản phẩm mới", "Tạo bài viết", "Xem báo cáo chuyển đổi" mà không cần mở từng menu con.',
                        'image' => 'dashboard-quick-actions.svg'
                    )
                ),
                'result' => 'Bạn nắm được toàn bộ chỉ số chính của website và biết ngay những mục đang cần xử lý (ví dụ: có 3 đơn hàng mới hoặc 2 bình luận chờ duyệt).',
                'edit_instructions' => 'Dữ liệu trên Dashboard được tổng hợp tự động theo thời gian thực từ cơ sở dữ liệu, không cần thao tác lưu thủ công.',
                'delete_instructions' => 'Không áp dụng thao tác xóa trên trang tổng quan.',
                'warnings' => 'Nếu số liệu không thay đổi sau khi cập nhật nội dung, hãy nhấn biểu tượng "Xóa Cache" trên thanh Header để làm mới bộ nhớ đệm.',
                'troubleshooting' => 'Lỗi không tải được số liệu: Kiểm tra lại kết nối mạng hoặc xem tab Trung tâm Vận hành để đảm bảo cơ sở dữ liệu hoạt động bình thường.',
                'related_links' => array('trung-tam-van-hanh-tong-quan', 'quan-ly-san-pham-danh-sach', 'quan-ly-don-hang'),
                'keywords' => array('dashboard', 'tổng quan', 'bảng điều khiển', 'thống kê', 'kpi', 'đăng nhập', 'trang chủ admin')
            ),

            // -------------------------------------------------------------
            // NHÓM 2: NGHIÊN CỨU SẢN PHẨM & AI DISCOVERY
            // -------------------------------------------------------------
            'nghien-cuu-ung-vien' => array(
                'id' => 'nghien-cuu-ung-vien',
                'category_id' => 'nghien-cuu-san-pham',
                'title' => 'Quản lý Ứng viên Nghiên cứu Sản phẩm',
                'summary' => 'Khám phá sản phẩm ngách gym/fitness tiềm năng, xem điểm đánh giá AI và chuyển đổi thành sản phẩm',
                'purpose' => 'Hệ thống tự động quét và phân tích các sản phẩm bán chạy từ sàn thương mại điện tử (Shopee, TikTok Shop, AccessTrade). Người dùng có thể đánh giá tiềm năng và tạo sản phẩm chỉ với 1 click.',
                'when_to_use' => 'Khi bạn muốn mở rộng danh mục kinh doanh, tìm sản phẩm hot trend để làm video affiliate marketing.',
                'prerequisites' => 'Đã có từ khóa trong mục Từ khóa & Seeds hoặc bot cào dữ liệu đã chạy.',
                'menu_path' => 'Menu bên trái → Nghiên cứu sản phẩm → Ứng viên nghiên cứu.',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Xem danh sách ứng viên và điểm tiềm năng (Demand Score)',
                        'content' => 'Bảng danh sách hiển thị: Tên sản phẩm, Nguồn cào (AccessTrade/TikTok), Giá bán, Tỷ lệ hoa hồng, và Điểm tiềm năng AI (0 - 100 điểm). Các sản phẩm có màu xanh lá (>= 75 điểm) là ứng viên tiềm năng cao.',
                        'image' => 'research-candidates-list.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Xem chi tiết phân tích AI (Research Details)',
                        'content' => 'Click vào tên ứng viên hoặc nút "Chi tiết" để xem: Nỗi đau sản phẩm giải quyết (Pain Points), Khách hàng mục tiêu, Bằng chứng thực tế (Evidence) và Khuyến nghị nội dung.',
                        'image' => 'research-candidate-detail.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Phê duyệt và Tạo sản phẩm chính thức (Create Product)',
                        'content' => 'Khi quyết định kinh doanh sản phẩm, nhấn nút "Tạo sản phẩm" màu xanh lá. Hệ thống sẽ tự động điền sẵn tên, giá, ảnh đại diện, danh mục và link affiliate vào kho hàng chính thức.',
                        'image' => 'research-create-product.svg'
                    )
                ),
                'result' => 'Ứng viên được chuyển trạng thái sang "ĐÃ DUYỆT (APPROVED)" và một bản ghi sản phẩm mới xuất hiện ngay trong mục "Quản lý sản phẩm".',
                'edit_instructions' => 'Bạn có thể chỉnh sửa lại giá tham khảo, tên gợi nhớ hoặc danh mục đề xuất trước khi bấm Tạo sản phẩm.',
                'delete_instructions' => 'Chọn checkbox đầu dòng các ứng viên không tiềm năng và nhấn nút "Xóa chọn" để loại bỏ khỏi danh sách.',
                'warnings' => 'Hệ thống áp dụng bộ lọc 2 lớp (Two-Stage Relevance Filter) nên chỉ các sản phẩm thực sự thuộc ngành Thể thao / Gym mới xuất hiện.',
                'troubleshooting' => 'Không thấy ứng viên mới: Kiểm tra mục "Hàng đợi Jobs" xem tiến trình cào dữ liệu có đang chạy hoặc bị tạm dừng không.',
                'related_links' => array('nghien-cuu-tu-khoa-seeds', 'ai-content-tao-moi', 'quan-ly-san-pham-them-moi'),
                'keywords' => array('nghiên cứu', 'ứng viên', 'discovery', 'tìm sản phẩm', 'demand score', 'cào dữ liệu', 'sản phẩm hot')
            ),

            'nghien-cuu-tu-khoa-seeds' => array(
                'id' => 'nghien-cuu-tu-khoa-seeds',
                'category_id' => 'nghien-cuu-san-pham',
                'title' => 'Quản lý Từ khóa & Seeds cho Bot Discovery',
                'summary' => 'Thêm từ khóa ngách Gym, phụ kiện thể hình để định hướng cho AI tìm kiếm sản phẩm chính xác',
                'purpose' => 'Kiểm soát phạm vi quét sản phẩm tự động của hệ thống, đảm bảo AI chỉ tìm đúng các sản phẩm liên quan đến Gym, Fitness, Dinh dưỡng thể hình.',
                'when_to_use' => 'Khi muốn mở rộng tìm kiếm sang danh mục mới (ví dụ: Bổ sung từ khóa "Dây nhảy thể lực", "Con lăn tập bụng").',
                'prerequisites' => 'Quyền quản trị viên.',
                'menu_path' => 'Menu bên trái → Nghiên cứu sản phẩm → Từ khóa & Seeds.',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Nhấn nút "Thêm mới Seed" ở góc phải',
                        'content' => 'Tại giao diện danh sách từ khóa, bấm nút "Thêm mới" màu xanh dương.',
                        'image' => 'research-seed-add.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Nhập thông tin từ khóa và nền tảng quét',
                        'content' => 'Nhập "Từ khóa tìm kiếm" (VD: đai lưng tập gym), chọn "Nền tảng mục tiêu" (AccessTrade, TikTok, Shopee), và chọn "Tần suất quét" (Hàng ngày, Hàng tuần).',
                        'image' => 'research-seed-form.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Nhấn nút "Lưu dữ liệu"',
                        'content' => 'Bấm nút "Lưu" để kích hoạt từ khóa. Bot Discovery sẽ đưa từ khóa này vào hàng đợi quét tự động.',
                        'image' => 'research-seed-save.svg'
                    )
                ),
                'result' => 'Từ khóa mới hiển thị trong bảng với trạng thái "Đang hoạt động (ACTIVE)". Trong đợt quét tiếp theo, các sản phẩm mới theo từ khóa này sẽ tự động xuất hiện trong mục Ứng viên.',
                'edit_instructions' => 'Bấm icon chiếc bút chì để sửa từ khóa hoặc thay đổi tần suất quét.',
                'delete_instructions' => 'Bấm icon thùng rác để xóa từ khóa không còn muốn theo dõi.',
                'warnings' => 'Tránh nhập từ khóa quá chung chung (như "áo", "quần") để không làm lẫn các sản phẩm thời trang thông thường.',
                'troubleshooting' => 'Từ khóa không ra kết quả: Thử rút ngắn từ khóa hoặc kiểm tra lại kết nối API tại mục "Cấu hình AI & API".',
                'related_links' => array('nghien-cuu-ung-vien', 'trung-tam-van-hanh-pipeline'),
                'keywords' => array('từ khóa', 'seeds', 'quét sản phẩm', 'cấu hình cào', 'từ khóa gym')
            ),

            // -------------------------------------------------------------
            // NHÓM 3: AI CONTENT ENGINE
            // -------------------------------------------------------------
            'ai-content-kho-noi-dung' => array(
                'id' => 'ai-content-kho-noi-dung',
                'category_id' => 'ai-content',
                'title' => 'Kho nội dung AI (Content Library) & Duyệt Bài',
                'summary' => 'Quản lý toàn bộ bài phân tích 12 khía cạnh, Hooks TikTok, kịch bản video và bộ thẻ SEO do AI tạo',
                'purpose' => 'Lưu trữ có hệ thống mọi phiên bản nội dung (Version 1, Version 2,...), hỗ trợ so sánh khác biệt (Diff Viewer), phê duyệt an toàn và áp dụng trực tiếp vào sản phẩm.',
                'when_to_use' => 'Khi cần kiểm tra nội dung AI vừa viết xong, chỉnh sửa trước khi xuất bản hoặc xem lại lịch sử các bản nháp cũ.',
                'prerequisites' => 'Đã có sản phẩm trong hệ thống và đã chạy tác vụ tạo nội dung.',
                'menu_path' => 'Menu bên trái → AI Content Engine → Kho nội dung (Library).',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Lọc nội dung theo Sản phẩm và Loại nội dung (Content Type)',
                        'content' => 'Sử dụng bộ lọc ở đầu trang để chọn: Bài phân tích chuyên sâu (Product Analysis), 7 Strategic Hooks, Kịch bản video (TikTok Script) hoặc Gói SEO Metadata.',
                        'image' => 'ai-content-filter.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Kiểm tra cột Trạng thái Kiểm duyệt (Compliance Status)',
                        'content' => 'Hệ thống tự động đánh dấu: Xanh lá (APPROVED - Đã duyệt), Vàng (REVIEW_REQUIRED - Cần biên tập viên đọc duyệt), Đỏ (REJECTED/BLOCKED - Vi phạm Guardrail về y tế hoặc cam kết giả).',
                        'image' => 'ai-content-status-badges.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Xem chi tiết và bấm "Phê duyệt & Áp dụng" (Approve & Apply)',
                        'content' => 'Mở bài viết, đọc nội dung. Nếu đạt yêu cầu, bấm nút "Phê duyệt" màu xanh lá. Để đưa nội dung này lên web hiển thị cho khách hàng, bấm tiếp "Áp dụng vào sản phẩm".',
                        'image' => 'ai-content-approve-action.svg'
                    )
                ),
                'result' => 'Nội dung được cập nhật vào bảng dữ liệu sản phẩm tương ứng, đồng thời phiên bản cũ được tự động sao lưu an toàn trong bảng backup.',
                'edit_instructions' => 'Bạn có thể chỉnh sửa trực tiếp câu chữ trong ô soạn thảo trước khi nhấn nút Phê duyệt.',
                'delete_instructions' => 'Chỉ nên từ chối duyệt (Reject) thay vì xóa hoàn toàn để giữ lại lịch sử đối soát phiên bản.',
                'warnings' => 'Tuân thủ nghiêm ngặt: Tuyệt đối không duyệt các nội dung cam kết chữa khỏi bệnh 100% hoặc tự nhận "tôi đã dùng 30 ngày" nếu không có hồ sơ kiểm thử thực tế.',
                'troubleshooting' => 'Nội dung bị báo đỏ REJECTED: Đọc cột "Lý do từ chối" ở góc phải để biết từ ngữ vi phạm (VD: cụm từ "chữa đau lưng", "cam kết giảm 10kg") và chỉnh sửa lại.',
                'related_links' => array('ai-content-tao-moi', 'ai-video-du-an', 'quan-ly-san-pham-chinh-sua'),
                'keywords' => array('ai content', 'kho nội dung', 'duyệt bài', 'hooks', 'kịch bản', 'seo', 'phân tích sản phẩm')
            ),

            'ai-content-tao-moi' => array(
                'id' => 'ai-content-tao-moi',
                'category_id' => 'ai-content',
                'title' => 'Tạo gói nội dung AI mới cho Sản phẩm',
                'summary' => 'Hướng dẫn chọn sản phẩm, thiết lập tone giọng và kích hoạt AI viết bài tự động',
                'purpose' => 'Sinh ra các gói bài viết chất lượng cao chuẩn SEO và kịch bản video viral trong vòng 3-5 giây mà không cần tự viết thủ công từ đầu.',
                'when_to_use' => 'Khi vừa thêm sản phẩm mới vào kho hoặc khi muốn làm mới nội dung marketing cho sản phẩm cũ.',
                'prerequisites' => 'Sản phẩm đã có thông số cơ bản (Tên, Giá, Mô tả ngắn hoặc thông số specs).',
                'menu_path' => 'Menu bên trái → AI Content Engine → Tạo nội dung mới.',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Chọn Sản phẩm cần viết bài',
                        'content' => 'Gõ tên sản phẩm vào ô tìm kiếm hoặc chọn từ danh sách thả xuống. Hệ thống sẽ tự động tải các dữ liệu gốc liên quan lên màn hình.',
                        'image' => 'ai-content-select-product.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Chọn Loại nội dung và Góc tiếp cận (Content Angle)',
                        'content' => 'Chọn loại nội dung mong muốn (Phân tích chuyên sâu / Hooks TikTok / Kịch bản 30s / SEO). Chọn góc tiếp cận: "Vấn đề & Giải pháp", "Cảnh báo sai lầm khi tập", hoặc "So sánh đối đầu".',
                        'image' => 'ai-content-options.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Nhấn nút "Tạo nội dung ngay" (Generate)',
                        'content' => 'Bấm nút "Tạo nội dung AI" màu đỏ. Sau 2-4 giây, nội dung hoàn chỉnh kèm bảng phân tích sẽ hiển thị ngay trên màn hình để bạn xem xét.',
                        'image' => 'ai-content-generate-btn.svg'
                    )
                ),
                'result' => 'Một gói nội dung phiên bản mới được tạo và lưu vào Kho nội dung với trạng thái chờ duyệt (REVIEW_REQUIRED).',
                'edit_instructions' => 'Sau khi AI tạo xong, bạn có thể chỉnh sửa lại các câu chữ cho đúng văn phong mong muốn trước khi nhấn Phê duyệt.',
                'delete_instructions' => 'Nếu không ưng ý, có thể bấm "Tạo lại phiên bản mới" (Regenerate).',
                'warnings' => 'Hệ thống tuân thủ 21 quy tắc Guardrail: AI sẽ từ chối đưa các thông tin sai sự thật hoặc cam kết y khoa quá đà vào nội dung.',
                'troubleshooting' => 'Báo lỗi "Vượt quá giới hạn request": Kiểm tra cấu hình API Key trong Cấu hình & Prompts hoặc chờ sang ngày mới.',
                'related_links' => array('ai-content-kho-noi-dung', 'ai-video-tao-moi'),
                'keywords' => array('tạo nội dung ai', 'viết bài tự động', 'sinh kịch bản', 'prompt ai', 'gemini')
            ),

            // -------------------------------------------------------------
            // NHÓM 4: AI VIDEO ENGINE
            // -------------------------------------------------------------
            'ai-video-du-an' => array(
                'id' => 'ai-video-du-an',
                'category_id' => 'ai-video',
                'title' => 'Quản lý Dự án Video AI & Duyệt Video Thành Phẩm',
                'summary' => 'Xem trước video ngắn marketing, kiểm tra phụ đề, âm thanh và phê duyệt trước khi xuất bản',
                'purpose' => 'Quản lý toàn bộ vòng đời sản xuất video: từ bản nháp kịch bản → ghép video FFmpeg → lồng tiếng Voiceover → Video hoàn chỉnh chuẩn 9:16 cho TikTok / Reels / Shorts.',
                'when_to_use' => 'Khi cần kiểm tra các video vừa được render xong để chuẩn bị cho chiến dịch đăng bài.',
                'prerequisites' => 'Đã có dự án video được tạo từ kịch bản AI Content.',
                'menu_path' => 'Menu bên trái → AI Video Engine → Dự án Video (Projects).',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Xem danh sách dự án và trạng thái Render',
                        'content' => 'Bảng hiển thị: Tên video, Sản phẩm liên kết, Thời lượng (giây), Chế độ render (Economy 0 VND / Hybrid / Veo) và Trạng thái (READY, RENDERING, APPROVED, FAILED).',
                        'image' => 'ai-video-project-list.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Mở trình phát Video Preview',
                        'content' => 'Click icon "Play" hoặc nút "Xem chi tiết" để xem video trực tiếp trên trình duyệt. Kiểm tra: Độ rõ nét của hình ảnh, độ mượt của giọng đọc AI và độ chuẩn xác của phụ đề tiếng Việt.',
                        'image' => 'ai-video-preview-player.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Nhấn nút "Phê duyệt Video" (Approve Video)',
                        'content' => 'Nếu video đạt yêu cầu, nhấn nút "Phê duyệt Video" màu xanh lá. Video này sẽ sẵn sàng 100% để chuyển sang Trung tâm Xuất bản (Publishing Center).',
                        'image' => 'ai-video-approve-btn.svg'
                    )
                ),
                'result' => 'Video chuyển sang trạng thái "APPROVED". Lúc này hệ thống sẽ mở khóa nút "Tạo Post Xuất bản TikTok".',
                'edit_instructions' => 'Nếu cần thay đổi ảnh phân cảnh hoặc giọng đọc, bấm "Tạo bản render mới" để chỉnh sửa kịch bản.',
                'delete_instructions' => 'Bấm icon thùng rác để xóa video thử nghiệm. Hệ thống sẽ tự động dọn dẹp các tệp tạm trên ổ cứng để tiết kiệm dung lượng.',
                'warnings' => 'Chỉ video có trạng thái "APPROVED" mới được phép tạo bài đăng sang TikTok (Rào chắn Human Gate bảo vệ thương hiệu).',
                'troubleshooting' => 'Video bị báo FAILED: Kiểm tra mục "Hàng đợi Render" để xem mã lỗi FFmpeg (thường do định dạng ảnh gốc quá lớn hoặc lỗi kết nối mạng).',
                'related_links' => array('ai-video-tao-moi', 'xuat-ban-tao-post', 'ai-video-kho-tai-nguyen'),
                'keywords' => array('video ai', 'dự án video', 'duyệt video', 'tiktok video', 'reels', 'render ffmpeg')
            ),

            'ai-video-tao-moi' => array(
                'id' => 'ai-video-tao-moi',
                'category_id' => 'ai-video',
                'title' => 'Tạo Dự án Video Marketing Mới (Economy / Hybrid)',
                'summary' => 'Hướng dẫn ghép kịch bản phân cảnh, chọn giọng đọc AI và tối ưu hóa chi phí sản xuất video',
                'purpose' => 'Tạo video ngắn dọc 9:16 chuyên nghiệp với chi phí tối ưu: Chế độ Economy (0 VND - sử dụng ảnh sản phẩm thật và hiệu ứng chuyển cảnh mượt mà) hoặc Chế độ Hybrid (kết hợp phân cảnh AI tạo chuyển động).',
                'when_to_use' => 'Khi đã có kịch bản TikTok Script từ mục AI Content và muốn tạo video clip để đăng lên mạng xã hội.',
                'prerequisites' => 'Sản phẩm đã có ít nhất 2-3 ảnh chụp thực tế chất lượng cao và 1 kịch bản script đã duyệt.',
                'menu_path' => 'Menu bên trái → AI Video Engine → Tạo Video mới.',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Chọn Sản phẩm và Kịch bản nguồn (Source Script)',
                        'content' => 'Chọn sản phẩm cần làm video. Hệ thống sẽ tự động tải kịch bản phân cảnh 6 cảnh (Scene 1 đến Scene 6) đã tạo từ AI Content.',
                        'image' => 'ai-video-create-step1.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Chọn Giọng đọc AI (Voiceover TTS) và Tùy chỉnh phân cảnh',
                        'content' => 'Chọn giọng đọc nam/nữ trầm ấm hoặc năng động. Kiểm tra ảnh gắn cho từng phân cảnh, có thể thay đổi ảnh hoặc chỉnh sửa lời thoại đọc tương ứng.',
                        'image' => 'ai-video-create-step2.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Chọn Chế độ Render và Bấm "Tạo Video"',
                        'content' => 'Chọn chế độ "ECONOMY (Mặc định - Chi phí 0 VND)". Bấm nút "Bắt đầu Render Video". Hệ thống sẽ đưa tác vụ vào hàng đợi và xử lý nền trong 10-20 giây.',
                        'image' => 'ai-video-create-step3.svg'
                    )
                ),
                'result' => 'Tác vụ render được khởi tạo. Bạn có thể theo dõi tiến độ thanh phần trăm tại mục "Hàng đợi Render". Khi xong, video sẽ xuất hiện trong danh mục Dự án Video.',
                'edit_instructions' => 'Có thể điều chỉnh tốc độ đọc của voiceover (0.9x đến 1.1x) và cỡ chữ phụ đề (Caption Size) trong mục Cấu hình Providers.',
                'delete_instructions' => 'Dự án đang render có thể hủy bất kỳ lúc nào từ màn hình Hàng đợi Render.',
                'warnings' => 'Luôn ưu tiên chế độ Economy để tiết kiệm 100% ngân sách API trong giai đoạn thử nghiệm.',
                'troubleshooting' => 'Video không có tiếng: Kiểm tra xem nhà cung cấp giọng đọc (TTS Provider) trong Cấu hình có đang bật hay không.',
                'related_links' => array('ai-video-du-an', 'ai-content-kho-noi-dung'),
                'keywords' => array('tạo video', 'ghép video', 'voiceover', 'phụ đề tự động', 'economy mode', 'hybrid video')
            ),

            // -------------------------------------------------------------
            // NHÓM 5: XUẤT BẢN & PHÂN PHỐI TIKTOK
            // -------------------------------------------------------------
            'xuat-ban-tao-post' => array(
                'id' => 'xuat-ban-tao-post',
                'category_id' => 'xuat-ban-tiktok',
                'title' => 'Tạo Post Package Xuất bản & Gắn Link Affiliate UTM',
                'summary' => 'Quy trình đóng gói bài đăng video, tự động sinh mã theo dõi UTM Tracking và gắn link tiếp thị liên kết',
                'purpose' => 'Đảm bảo mỗi bài đăng trên TikTok, Facebook Reels hay YouTube Shorts đều có kèm mã định danh duy nhất (Unique Tracking Code) để theo dõi chính xác từng click và đơn hàng phát sinh.',
                'when_to_use' => 'Khi video đã được duyệt (APPROVED) và bạn muốn đăng lên mạng xã hội ngay hoặc lên lịch hẹn giờ.',
                'prerequisites' => 'Video liên kết phải có trạng thái APPROVED.',
                'menu_path' => 'Menu bên trái → Xuất bản & TikTok → Tạo Post Package.',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Chọn Video đã duyệt và Nền tảng đăng bài (Platform)',
                        'content' => 'Chọn video từ danh mục thả xuống. Chọn nền tảng mục tiêu: TikTok, Facebook Reels, Instagram Reels hoặc YouTube Shorts.',
                        'image' => 'publishing-create-step1.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Kiểm tra Caption, Hashtags và Câu công khai Affiliate (Disclosure)',
                        'content' => 'Hệ thống tự động điền Caption tối ưu, 5 hashtag ngành gym và câu công khai minh bạch quan hệ đối tác theo đúng luật quảng cáo và tiêu chuẩn Guardrail.',
                        'image' => 'publishing-create-step2.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Nhận Link Landing Page kèm mã UTM và Bấm "Tạo Post Package"',
                        'content' => 'Hệ thống tự động tạo URL đích gắn mã theo dõi sub1-sub4 (VD: `https://khoepro.com/san-pham/dai-lung?ref=KP_TRK_xxx`). Bấm "Tạo Post Package" để lưu bản ghi.',
                        'image' => 'publishing-create-step3.svg'
                    )
                ),
                'result' => 'Tạo thành công Post Package ở trạng thái DRAFT (Bản nháp) với đầy đủ thông tin chuẩn hóa và mã định danh theo dõi.',
                'edit_instructions' => 'Bạn có thể chỉnh sửa lại tiêu đề, nội dung caption trước khi chuyển sang trạng thái Sẵn sàng xuất bản.',
                'delete_instructions' => 'Bấm "Hủy bài đăng" nếu không còn nhu cầu xuất bản gói bài này.',
                'warnings' => 'Không được xóa câu công khai đối tác liên kết (Affiliate Disclosure) để bảo vệ tài khoản mạng xã hội không bị vi phạm chính sách nền tảng.',
                'troubleshooting' => 'Báo lỗi "Video chưa được phê duyệt": Quay lại mục AI Video Engine và bấm Duyệt video trước.',
                'related_links' => array('xuat-ban-kiem-duyet-ready', 'ai-video-du-an', 'do-luong-clicks-conversions'),
                'keywords' => array('xuất bản', 'đăng bài tiktok', 'post package', 'utm tracking', 'affiliate link', 'caption tiktok')
            ),

            'xuat-ban-kiem-duyet-ready' => array(
                'id' => 'xuat-ban-kiem-duyet-ready',
                'category_id' => 'xuat-ban-tiktok',
                'title' => 'Kiểm duyệt Pre-publish Checklist & Khóa Snapshot Bất Biến',
                'summary' => 'Kiểm tra 8 tiêu chí an toàn trước khi đăng bài và cơ chế đóng băng dữ liệu chống thay đổi ngoài ý muốn',
                'purpose' => 'Ngăn chặn 100% rủi ro đăng nhầm video lỗi, link hỏng, thiếu caption hoặc nội dung bị chỉnh sửa trái phép sau khi đã lên lịch.',
                'when_to_use' => 'Sau khi tạo Post Package và muốn chuyển sang trạng thái READY (Sẵn sàng) hoặc SCHEDULED (Đã lên lịch).',
                'prerequisites' => 'Post Package ở trạng thái DRAFT.',
                'menu_path' => 'Xuất bản & TikTok → Danh sách bài đăng → Click vào bài cần duyệt.',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Nhấn nút "Chạy Pre-publish Checklist"',
                        'content' => 'Hệ thống sẽ tự động quét 8 tiêu chuẩn an toàn: File video tồn tại và dung lượng chuẩn, Caption hợp lệ, Nền tảng hợp lệ, Không vi phạm Guardrail, Link landing page khả dụng.',
                        'image' => 'publishing-checklist-scan.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Xem kết quả kiểm định',
                        'content' => 'Nếu toàn bộ 8 tiêu chí đạt dấu tích xanh lá (PASS), hệ thống sẽ kích hoạt nút "Đánh dấu READY (Sẵn sàng đăng)".',
                        'image' => 'publishing-checklist-pass.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Bấm "Đánh dấu READY" (Khóa Snapshot)',
                        'content' => 'Khi bấm READY, hệ thống tự động chụp một bản Snapshot bất biến (đóng băng vĩnh viễn toàn bộ text, link, video của bài đăng). Kể cả sau này sản phẩm có đổi giá, bài đăng vẫn giữ nguyên dữ liệu tại thời điểm duyệt.',
                        'image' => 'publishing-snapshot-lock.svg'
                    )
                ),
                'result' => 'Bài đăng chuyển sang trạng thái READY hoặc SCHEDULED (nếu có hẹn giờ). Quản trị viên có thể đăng thủ công lên kênh hoặc để bot tự động đăng theo lịch.',
                'edit_instructions' => 'Nếu bạn cố tình sửa nội dung của một bài đã READY, hệ thống sẽ tự động hủy cờ READY và chuyển về DRAFT để bắt buộc kiểm duyệt lại (Cơ chế Edit Invalidation an toàn).',
                'delete_instructions' => 'Bài đã PUBLISHED (Đã đăng) sẽ bị khóa chỉnh sửa vĩnh viễn để bảo vệ tính toàn vẹn của lịch sử đối soát.',
                'warnings' => 'Tuyệt đối không tìm cách lách các cảnh báo màu đỏ của Checklist.',
                'troubleshooting' => 'Checklist báo lỗi Caption: Kiểm tra xem bạn có bỏ trống caption hoặc dùng từ ngữ nhạy cảm bị Guardrail chặn không.',
                'related_links' => array('xuat-ban-tao-post', 'trung-tam-van-hanh-nhat-ky'),
                'keywords' => array('pre-publish checklist', 'kiểm duyệt', 'snapshot bất biến', 'ready', 'scheduled', 'an toàn xuất bản')
            ),

            // -------------------------------------------------------------
            // NHÓM 6: ĐO LƯỜNG, CHUYỂN ĐỔI & WINNER DETECTION
            // -------------------------------------------------------------
            'do-luong-tong-quan' => array(
                'id' => 'do-luong-tong-quan',
                'category_id' => 'do-luong-winner',
                'title' => 'Tổng quan Hiệu suất Đo lường & Báo cáo Chuyển đổi',
                'summary' => 'Phân tích lượt click, tỷ lệ chuyển đổi đơn hàng (CR) và doanh thu hoa hồng theo thời gian thực',
                'purpose' => 'Cung cấp báo cáo tài chính minh bạch: Biết chính xác mỗi video, mỗi sản phẩm đem về bao nhiêu lượt xem, bao nhiêu click vào link mua hàng và bao nhiêu tiền hoa hồng thực nhận.',
                'when_to_use' => 'Hàng ngày hoặc hàng tuần để đánh giá hiệu quả kinh doanh của hệ thống affiliate.',
                'prerequisites' => 'Đã có bài đăng phát sinh lượt xem hoặc người dùng click vào link tiếp thị.',
                'menu_path' => 'Menu bên trái → Đo lường & Winner → Tổng quan hiệu suất.',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Chọn khoảng thời gian báo cáo',
                        'content' => 'Chọn bộ lọc thời gian: Hôm nay, 7 ngày qua, 30 ngày qua hoặc tùy chỉnh khoảng ngày mong muốn.',
                        'image' => 'analytics-time-filter.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Xem biểu đồ xu hướng Clicks và Doanh thu',
                        'content' => 'Biểu đồ đường trực quan thể hiện sự tăng trưởng lượng click theo từng ngày, so sánh giữa các kênh TikTok, Facebook và Website.',
                        'image' => 'analytics-chart-trend.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Xem bảng phân tích phễu chuyển đổi (Conversion Funnel)',
                        'content' => 'Theo dõi phễu 4 tầng: Lượt xem Video → Click vào Link Bio → Truy cập Landing Page → Đơn hàng thành công trên sàn thương mại điện tử.',
                        'image' => 'analytics-funnel.svg'
                    )
                ),
                'result' => 'Bạn nắm được doanh thu hoa hồng ước tính và biết kênh nào đang mang lại chuyển đổi cao nhất để tập trung nguồn lực.',
                'edit_instructions' => 'Dữ liệu đo lường được đồng bộ tự động từ pixel theo dõi và API sàn, không chỉnh sửa thủ công để đảm bảo tính khách quan.',
                'delete_instructions' => 'Không áp dụng thao tác xóa trên dữ liệu phân tích.',
                'warnings' => 'Đơn hàng từ sàn thường có độ trễ đối soát 15-30 phút từ hệ thống AccessTrade / Shopee.',
                'troubleshooting' => 'Không thấy đơn hàng mới: Vào mục "Đơn hàng & Chuyển đổi" bấm nút "Đồng bộ giao dịch ngay" (Sync Now).',
                'related_links' => array('do-luong-winner-detection', 'do-luong-doi-soat-csv'),
                'keywords' => array('đo lường', 'analytics', 'báo cáo hoa hồng', 'doanh thu affiliate', 'tỷ lệ chuyển đổi', 'clicks')
            ),

            'do-luong-winner-detection' => array(
                'id' => 'do-luong-winner-detection',
                'category_id' => 'do-luong-winner',
                'title' => 'Phát hiện Sản phẩm Thắng (Winner Detection)',
                'summary' => 'Thuật toán tự động tìm ra sản phẩm có tỷ lệ chuyển đổi đột phá và gợi ý tăng tốc độ phủ sóng',
                'purpose' => 'Giúp nhà kinh doanh không bỏ lỡ "sóng" sản phẩm hot: Khi một sản phẩm đạt tiêu chuẩn Winner (Click cao, Chuyển đổi tốt, Tỷ lệ hoa hồng cao), hệ thống sẽ gắn nhãn WINNER và đề xuất tạo thêm nhiều biến thể video mới.',
                'when_to_use' => 'Khi muốn lọc nhanh danh sách các sản phẩm đang có doanh thu tốt nhất để đẩy mạnh sản xuất nội dung.',
                'prerequisites' => 'Hệ thống đã ghi nhận tối thiểu 50-100 clicks cho sản phẩm.',
                'menu_path' => 'Menu bên trái → Đo lường & Winner → Winner Detection.',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Xem bảng xếp hạng Sản phẩm Thắng (Winner Leaderboard)',
                        'content' => 'Danh sách hiển thị các sản phẩm được gắn nhãn sao vàng "WINNER" kèm điểm hiệu suất tổng hợp (Performance Score).',
                        'image' => 'winner-leaderboard.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Xem lý do sản phẩm được chọn làm Winner',
                        'content' => 'Click vào sản phẩm để xem phân tích: Tỷ lệ click-to-lead > 5%, Doanh số đơn hàng tăng trưởng 3 ngày liên tiếp, Video TikTok có lượt giữ chân cao.',
                        'image' => 'winner-criteria-detail.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Bấm "Tạo thêm biến thể Video cho Winner"',
                        'content' => 'Nhấn nút hành động màu vàng để tự động kích hoạt AI viết thêm 7 hooks mới và dựng thêm các video biến thể khác để tối đa hóa doanh thu từ sản phẩm này.',
                        'image' => 'winner-scale-action.svg'
                    )
                ),
                'result' => 'Tự động tạo ra các gói nội dung mới tập trung vào đúng sản phẩm đang có tỷ lệ sinh lời cao nhất.',
                'edit_instructions' => 'Bạn có thể tùy chỉnh ngưỡng điểm Winner tại mục "Cấu hình quy tắc".',
                'delete_instructions' => 'Không áp dụng xóa.',
                'warnings' => 'Một sản phẩm có thể mất nhãn Winner nếu trong 14 ngày không phát sinh thêm đơn hàng mới (Tránh dồn lực vào sản phẩm đã hết trend).',
                'troubleshooting' => 'Chưa có sản phẩm nào lên Winner: Cần duy trì đăng bài đều đặn để tích lũy đủ lượng click thống kê tối thiểu.',
                'related_links' => array('do-luong-tong-quan', 'toi-uu-khuyen-nghi', 'ai-video-tao-moi'),
                'keywords' => array('winner detection', 'sản phẩm thắng', 'hot trend', 'tối ưu doanh thu', 'scale video')
            ),

            // -------------------------------------------------------------
            // NHÓM 7: QUẢN LÝ SẢN PHẨM & DANH MỤC
            // -------------------------------------------------------------
            'quan-ly-san-pham-danh-sach' => array(
                'id' => 'quan-ly-san-pham-danh-sach',
                'category_id' => 'quan-ly-san-pham',
                'title' => 'Quản lý Danh sách Sản phẩm (Product Catalog)',
                'summary' => 'Tra cứu, lọc sản phẩm theo danh mục, bật/tắt hiển thị, sao chép và quản lý giá bán',
                'purpose' => 'Trung tâm quản lý toàn bộ kho hàng và danh mục sản phẩm hiển thị trên website KhoePro.',
                'when_to_use' => 'Khi cần tìm kiếm sản phẩm để sửa thông tin, đổi giá, ẩn sản phẩm tạm hết hàng hoặc kiểm tra kho.',
                'prerequisites' => 'Quyền quản trị viên hoặc biên tập viên.',
                'menu_path' => 'Menu bên trái → Quản lý sản phẩm → Sản phẩm.',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Tìm kiếm và Lọc theo Danh mục',
                        'content' => 'Nhập tên hoặc mã sản phẩm vào ô tìm kiếm ở góc trên. Sử dụng các menu thả xuống để lọc theo: Danh mục cấp 1, Cấp 2, Hãng sản xuất hoặc Trạng thái hiển thị.',
                        'image' => 'product-list-filter.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Bật / Tắt trạng thái hiển thị nhanh bằng Checkbox',
                        'content' => 'Tại các cột "Hiển thị", "Nổi bật", "Mới": Bạn có thể click trực tiếp vào ô checkbox để bật/tắt hiển thị sản phẩm ra ngoài trang chủ mà không cần mở trang chỉnh sửa.',
                        'image' => 'product-quick-status-toggle.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Các thao tác Sao chép (Copy), Chỉnh sửa, Xóa',
                        'content' => 'Ở cột Thao tác cuối mỗi dòng: Icon Chiếc bút chì (Chỉnh sửa), Icon Hai trang giấy (Nhân bản sản phẩm tương tự), Icon Thùng rác (Xóa sản phẩm).',
                        'image' => 'product-action-buttons.svg'
                    )
                ),
                'result' => 'Trạng thái sản phẩm ngoài website được cập nhật ngay lập tức tương ứng với thao tác trong Admin.',
                'edit_instructions' => 'Nhấp đúp vào tên sản phẩm hoặc icon cây bút chì để vào màn hình soạn thảo chi tiết.',
                'delete_instructions' => 'Muốn xóa nhiều sản phẩm cùng lúc: Tích chọn các ô vuông đầu dòng và nhấn nút "Xóa chọn" màu đỏ trên thanh công cụ.',
                'warnings' => '⚠️ Thao tác Xóa sản phẩm sẽ xóa vĩnh viễn khỏi database và không thể hoàn tác nếu không có bản backup.',
                'troubleshooting' => 'Sản phẩm đã bật "Hiển thị" nhưng ngoài web không thấy: Bấm icon "Xóa Cache" trên thanh Header để xóa bộ nhớ đệm.',
                'related_links' => array('quan-ly-san-pham-them-moi', 'quan-ly-san-pham-chinh-sua', 'quan-ly-san-pham-danh-muc-cap'),
                'keywords' => array('quản lý sản phẩm', 'danh sách sản phẩm', 'bật tắt hiển thị', 'sản phẩm nổi bật', 'tìm kiếm sản phẩm')
            ),

            'quan-ly-san-pham-them-moi' => array(
                'id' => 'quan-ly-san-pham-them-moi',
                'category_id' => 'quan-ly-san-pham',
                'title' => 'Thêm Mới Sản Phẩm & Nhập Thông Số Kỹ Thuật',
                'summary' => 'Hướng dẫn chi tiết từng bước tạo sản phẩm hoàn chỉnh: Ảnh, Giá, Danh mục, Bài viết và Thẻ SEO',
                'purpose' => 'Đưa một sản phẩm mới lên website chuẩn cấu trúc SEO E-commerce, hiển thị đẹp mắt và sẵn sàng nhận đơn đặt hàng hoặc click tiếp thị liên kết.',
                'when_to_use' => 'Khi nhập hàng mới về hoặc thêm sản phẩm affiliate mới vào website.',
                'prerequisites' => 'Đã tạo trước Danh mục cấp 1 tương ứng (VD: Danh mục Đồ tập Gym).',
                'menu_path' => 'Quản lý sản phẩm → Sản phẩm → Nhấn nút "Thêm mới" màu xanh dương.',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Tải lên Hình ảnh Sản phẩm (Product Images)',
                        'content' => 'Tại khung "Hình ảnh", bấm chọn ảnh đại diện chính (kích thước tối ưu: 600x600px hoặc 800x800px). Có thể tải thêm nhiều ảnh phụ vào khung Album Gallery phía dưới.',
                        'image' => 'product-add-step1-image.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Chọn Cây Danh mục và Điền Thông tin Cơ bản',
                        'content' => 'Chọn "Danh mục cấp 1", "Danh mục cấp 2", "Hãng sản xuất". Nhập "Tên sản phẩm", "Mã sản phẩm", "Giá bán thường (regular_price)", "Giá khuyến mãi (sale_price)". Hệ thống sẽ tự tính tỷ lệ giảm giá %.',
                        'image' => 'product-add-step2-category-price.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Soạn thảo Mô tả, Thông số Specs và Nội dung chi tiết',
                        'content' => 'Nhập "Mô tả ngắn" tóm tắt ưu điểm. Nhập bảng "Thông số kỹ thuật (Specs)" dạng JSON hoặc danh sách (Chất liệu, Kích thước, Xuất xứ). Soạn bài viết đánh giá chi tiết trong khung soạn thảo CKEditor.',
                        'image' => 'product-add-step3-content.svg'
                    ),
                    array(
                        'step_num' => 4,
                        'title' => 'Gắn Link Tiếp thị liên kết (Affiliate Offers) & Thẻ SEO',
                        'content' => 'Tại mục Affiliate: Chọn sàn (Shopee/TikTok Shop), dán đường dẫn mua hàng. Kiểm tra khung "SEO Page" bên dưới: Tối ưu Tiêu đề SEO (dưới 65 ký tự) và Mô tả SEO (dưới 160 ký tự).',
                        'image' => 'product-add-step4-affiliate-seo.svg'
                    ),
                    array(
                        'step_num' => 5,
                        'title' => 'Nhấn nút "Lưu" để hoàn tất',
                        'content' => 'Kiểm tra lại toàn bộ thông tin và nhấn nút "Lưu" màu xanh lá ở góc trên bên phải màn hình.',
                        'image' => 'product-add-step5-save.svg'
                    )
                ),
                'result' => 'Sản phẩm được tạo thành công, xuất hiện trên website với đầy đủ đường dẫn thân thiện (slug), ảnh đại diện, giá bán và link mua hàng.',
                'edit_instructions' => 'Sau khi lưu, bạn có thể quay lại chỉnh sửa bất kỳ lúc nào từ bảng Danh sách sản phẩm.',
                'delete_instructions' => 'Nếu tạo nhầm, có thể xóa từ danh sách sản phẩm.',
                'warnings' => 'Ảnh tải lên phải có dung lượng dưới 5MB, ưu tiên định dạng JPG/PNG/WebP rõ nét.',
                'troubleshooting' => 'Không lưu được bài: Kiểm tra xem đã điền đầy đủ "Tên sản phẩm" hay chưa (Tên sản phẩm là trường bắt buộc).',
                'related_links' => array('quan-ly-san-pham-danh-sach', 'quan-ly-san-pham-chinh-sua', 'ai-content-tao-moi'),
                'keywords' => array('thêm sản phẩm', 'tạo sản phẩm mới', 'nhập giá', 'upload ảnh sản phẩm', 'seo sản phẩm', 'ckeditor')
            ),

            // -------------------------------------------------------------
            // NHÓM 8: TRUNG TÂM VẬN HÀNH (OPERATIONS)
            // -------------------------------------------------------------
            'trung-tam-van-hanh-tong-quan' => array(
                'id' => 'trung-tam-van-hanh-tong-quan',
                'category_id' => 'trung-tam-van-hanh',
                'title' => 'Trung tâm Vận hành & Giám sát Hệ thống (Operations Control Center)',
                'summary' => 'Theo dõi tình trạng kết nối API, hàng đợi tác vụ nền, ngân sách tiêu hao và cảnh báo sự cố',
                'purpose' => 'Giúp bộ phận kỹ thuật và quản lý cấp cao kiểm soát 100% sự ổn định của hệ sinh thái tự động hóa KhoePro.',
                'when_to_use' => 'Đầu ngày làm việc, khi nhận được thông báo lỗi qua Telegram hoặc khi cần đối soát chi phí API.',
                'prerequisites' => 'Quyền quản trị viên cấp cao (Admin / Superadmin).',
                'menu_path' => 'Menu bên trái → Trung tâm Vận hành → Tổng quan hệ thống.',
                'steps' => array(
                    array(
                        'step_num' => 1,
                        'title' => 'Xem Huy hiệu Sức khỏe Hệ thống (Health Badge)',
                        'content' => 'Góc trái màn hình hiển thị trạng thái tổng hợp: "HEALTHY" (Màu xanh - Mọi dịch vụ hoạt động hoàn hảo), "DEGRADED" (Màu vàng - Có dịch vụ phản hồi chậm) hoặc "CRITICAL" (Màu đỏ - Cần xử lý ngay).',
                        'image' => 'operations-health-badge.svg'
                    ),
                    array(
                        'step_num' => 2,
                        'title' => 'Kiểm tra trạng thái 4 Worker tự động hóa',
                        'content' => 'Bảng Workers hiển thị tình trạng của 4 tiến trình nền: Discovery Worker (Tìm sản phẩm), Content Worker (Viết bài AI), Video Worker (Render video) và AccessTrade Sync Worker (Đồng bộ đơn hàng).',
                        'image' => 'operations-workers-status.svg'
                    ),
                    array(
                        'step_num' => 3,
                        'title' => 'Kiểm tra Chi phí API & Ngân sách ngày',
                        'content' => 'Khung bên phải thống kê chi phí API đã sử dụng hôm nay (VND) so với hạn mức ngân sách ngày (Daily Budget Limit). Nếu gần chạm ngưỡng, hệ thống sẽ tự động bật cảnh báo.',
                        'image' => 'operations-costs-summary.svg'
                    )
                ),
                'result' => 'Quản trị viên biết chắc chắn hệ thống đang chạy trơn tru, không có tác vụ nào bị nghẽn (Stalled Jobs) hay gặp sự cố đứt gãy kết nối.',
                'edit_instructions' => 'Có thể điều chỉnh ngân sách tối đa hoặc bật chế độ Safe Mode tại mục Cấu hình Vận hành.',
                'delete_instructions' => 'Không áp dụng xóa trên màn hình tổng quan vận hành.',
                'warnings' => 'Nếu huy hiệu chuyển sang màu ĐỎ (CRITICAL), hãy mở ngay mục "Cảnh báo & Sự cố" để đọc chi tiết lỗi.',
                'troubleshooting' => 'Một Worker hiển thị FAILED: Vào mục "Quản lý Tác vụ" bấm nút "Chạy lại (Retry)" để khởi động lại tiến trình.',
                'related_links' => array('trung-tam-van-hanh-pipeline', 'trung-tam-van-hanh-nhat-ky', 'tong-quan-dashboard'),
                'keywords' => array('vận hành', 'operations', 'sức khỏe hệ thống', 'healthy', 'chi phí api', 'worker cron', 'quản trị hệ thống')
            )
        );
    }

    public static function getArticle($id) {
        $articles = self::getArticles();
        return isset($articles[$id]) ? $articles[$id] : null;
    }

    public static function getArticlesByCategory($categoryId) {
        $articles = self::getArticles();
        $result = array();
        foreach ($articles as $art) {
            if ($art['category_id'] === $categoryId) {
                $result[] = $art;
            }
        }
        return $result;
    }

    public static function searchArticles($keyword) {
        $keyword = trim(mb_strtolower($keyword, 'UTF-8'));
        if (empty($keyword)) return self::getArticles();

        $articles = self::getArticles();
        $matches = array();

        foreach ($articles as $id => $art) {
            $score = 0;
            $title = mb_strtolower($art['title'], 'UTF-8');
            $summary = mb_strtolower($art['summary'], 'UTF-8');
            $purpose = mb_strtolower($art['purpose'], 'UTF-8');

            if (mb_strpos($title, $keyword) !== false) $score += 10;
            if (mb_strpos($summary, $keyword) !== false) $score += 5;
            if (mb_strpos($purpose, $keyword) !== false) $score += 3;

            if (!empty($art['keywords'])) {
                foreach ($art['keywords'] as $kw) {
                    if (mb_strpos(mb_strtolower($kw, 'UTF-8'), $keyword) !== false) {
                        $score += 8;
                    }
                }
            }

            if ($score > 0) {
                $matches[$id] = array('article' => $art, 'score' => $score);
            }
        }

        uasort($matches, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        $final = array();
        foreach ($matches as $item) {
            $final[] = $item['article'];
        }
        return $final;
    }
}
