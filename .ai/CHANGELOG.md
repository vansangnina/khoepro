# FITNADO AI CHANGELOG

Tài liệu ghi nhận toàn bộ các thay đổi được thực hiện bởi AI Agents trong suốt quá trình phát triển dự án FITNADO.

---

## [2026-09-19] - PHASE 06: AI VIDEO PRODUCTION ENGINE (APPROVED CONTENT -> VIDEO PROJECT -> ASSET PREP -> RENDER -> QC -> PREVIEW -> HUMAN APPROVAL)

### CREATED
* `database/migrations/phase06_ai_video.sql`: Migration tạo 3 bảng mới (`table_ai_video`, `table_ai_video_job`, `table_ai_video_asset`).
* `libraries/class/class.VideoProvider.php`: Lớp trừu tượng hóa nhà cung cấp video (`VideoProviderInterface`, `MockVideoProvider`, `ExternalVideoProvider`, `ManualVideoProvider`, `VideoProviderFactory`).
* `libraries/class/class.AIVideoEngine.php`: Lớp động cơ sản xuất video AI cốt lõi: khởi tạo dự án từ Approved TikTok Script Phase 05, bộ giải quyết tài nguyên phân cảnh (`resolveProjectAssets`), phát hiện thiếu asset (`WAITING_ASSET`), tính toán `script_hash` phát hiện video lỗi thời (`is_outdated`), bộ kiểm định chất lượng Media QC (`validateRenderedMedia`), quản lý phiên bản (`version`, `is_active`) và xử lý duyệt/từ chối từ Admin.
* `libraries/class/class.AIVideoJobQueue.php`: Lớp quản trị hàng đợi render bất đồng bộ, concurrency locking, polling, stale recovery và retry.
* `cron/video_render_worker.php`: Background CLI / HTTP Token worker xử lý hàng đợi render video theo lô.
* `admin/sources/ai_video.php`: Controller quản trị dự án video, xem chi tiết & preview HTML5 `<video controls>`, tạo dự án mới, render ngay/hàng loạt, phê duyệt/từ chối, quản lý kho Assets, giám sát Job Queue và cấu hình Providers.
* `admin/templates/ai_video/mans_tpl.php`: Thư viện dự án video với bộ lọc đa tiêu chí và widget thống kê.
* `admin/templates/ai_video/view_tpl.php`: Màn hình chi tiết dự án với trình phát HTML5 `<video controls>`, bảng phân cảnh Shot Plan chi tiết, danh sách tài nguyên, báo cáo Media QC và nút Phê duyệt / Từ chối (kèm modal lý do).
* `admin/templates/ai_video/create_tpl.php`: Giao diện khởi tạo dự án video từ kịch bản TikTok đã duyệt.
* `admin/templates/ai_video/jobs_tpl.php`: Màn hình giám sát hàng đợi tác vụ render.
* `admin/templates/ai_video/assets_tpl.php`: Màn hình quản lý kho tài nguyên trực quan video.
* `admin/templates/ai_video/settings_tpl.php`: Giao diện cấu hình API keys và hạn mức render hàng ngày.
* `.ai/plans/PHASE-06-AI-VIDEO.md`: Kế hoạch triển khai chi tiết Phase 06.
* `.ai/skills/fitnado-ai-video/SKILL.md`: Kỹ năng chuyên môn về quy trình sản xuất video AI, kiểm định chất lượng và rào chắn phê duyệt của con người.
* `test_phase06.php`: Bộ kiểm thử toàn diện Phase 06 gồm 28 test cases.

### MODIFIED & ENHANCED
* `admin/templates/layout/menu.php`: Bổ sung menu điều hướng "AI Video Engine" với đầy đủ các phân mục (Dự án Video, Tạo Video mới, Hàng đợi Render, Kho tài nguyên, Cấu hình Providers).
* `.ai/DATABASE.md`, `.ai/BUSINESS_RULES.md`, `.ai/ARCHITECTURE.md`: Cập nhật schema Mục 12, Quy tắc Mục 9 và Kiến trúc Mục 10 cho Phase 06.

### RESULT
* Hoàn thành toàn diện Phase 06: Chuyển đổi thành công Approved TikTok Script & Shot Plan thành Video Projects có thể render, kiểm định chất lượng nghiêm ngặt và preview trực tiếp trên Admin; kiểm soát 100% bằng Human Gate (không auto-publish); đạt 28/28 Phase 06 test cases (100% PASS), vượt qua 115 test cases hồi quy của các Phase 01–05 và 0 lỗi PHP 7.4.

---

## [2026-09-19] - PHASE 05: AI CONTENT ENGINE (PRODUCT ANALYSIS, HOOKS, TIKTOK SCRIPTS, SHOT PLANS & SEO PACKS)

### CREATED
* `database/migrations/phase05_ai_content.sql`: Migration tạo 3 bảng mới (`table_ai_content`, `table_ai_content_job`, `table_product_content_backup`).
* `libraries/class/class.AIContentEngine.php`: Lớp động cơ nội dung AI lõi: tổng hợp dữ liệu sản phẩm & evidence facts, tính toán `source_hash` phát hiện nội dung lỗi thời, kiểm tra cổng chất lượng (`validateQualityGate`), quản lý phiên bản (`version`, `is_current`), bộ so sánh và áp dụng có thể hoàn nguyên (`applyToProduct`) kèm backup.
* `libraries/class/class.AIContentJobQueue.php`: Lớp quản lý hàng đợi tác vụ sinh nội dung AI bất đồng bộ (Single / Batch queue, concurrency lock, retry, stale recovery).
* `cron/ai_content_worker.php`: Background CLI / HTTP Token worker xử lý hàng đợi theo lô.
* `admin/sources/ai_content.php`: Controller quản trị thư viện nội dung AI, xem chi tiết gói nội dung, trình so sánh Diff Apply, tạo nội dung đơn/hàng loạt, giám sát Job Queue và cấu hình Prompts.
* `admin/templates/ai_content/mans_tpl.php`: Thư viện nội dung lọc theo sản phẩm, loại nội dung, trạng thái và cảnh báo outdated.
* `admin/templates/ai_content/view_tpl.php`: Màn hình xem chi tiết gói nội dung (7 Hooks, phân cảnh kịch bản, bảng Shot Plan, SEO meta, bài review và kết quả Quality Gate).
* `admin/templates/ai_content/diff_apply_tpl.php`: Trình so sánh Diff trực quan song song (Side-by-Side Diff) giữa nội dung hiện tại và nội dung AI được duyệt.
* `admin/templates/ai_content/generate_tpl.php`: Giao diện tạo nội dung AI cho sản phẩm hoặc tạo hàng loạt.
* `admin/templates/ai_content/jobs_tpl.php`: Màn hình giám sát hàng đợi job AI Content.
* `admin/templates/ai_content/settings_tpl.php`: Màn hình xem thông tin prompt templates và cài đặt.
* `.ai/plans/PHASE-05-AI-CONTENT.md`: Kế hoạch triển khai chi tiết Phase 05.
* `.ai/skills/fitnado-ai-content/SKILL.md`: Tài liệu kỹ năng về quy trình AI Content Engine, Quality Gate và Shot Plan bridge.
* `test_phase05.php`: Bộ kiểm thử toàn diện Phase 05 gồm 49 test cases.

### MODIFIED & ENHANCED
* `libraries/class/class.AIResearchAgent.php`: Mở rộng MockAIProvider với các cấu trúc sinh nội dung chuyên sâu (Hooks 7 loại, kịch bản phân cảnh TikTok, Shot Plan 5 scene, SEO metadata, Review draft, FAQ, Tone guide) hỗ trợ kiểm thử và fallback ngoại tuyến.
* `admin/templates/layout/menu.php`: Thêm menu điều hướng "AI Content Engine" với đầy đủ sub-menus (Thư viện nội dung, Tạo nội dung, Hàng đợi xử lý, Cấu hình Prompts).
* `.ai/DATABASE.md`, `.ai/BUSINESS_RULES.md`, `.ai/ARCHITECTURE.md`: Cập nhật schema Mục 11, Quy tắc Mục 8 và Kiến trúc Mục 9 cho Phase 05.

### RESULT
* Hoàn thành toàn diện Phase 05: Hệ thống AI Content Engine tự động tạo ra nội dung phong phú, giàu tính thương mại và chuẩn xác dựa trên facts thật; tuân thủ nghiêm ngặt Quality Gate và Human Approval; kịch bản TikTok kèm Shot Plan phân đoạn sẵn sàng làm cầu nối cho Phase 06; hoàn thành 49/49 test cases (100% PASS) và 0 lỗi PHP 7.4.

---

## [2026-09-19] - PHASE 04: AUTOMATED PRODUCT RESEARCH + AI RESEARCH AGENT

### CREATED
* `database/migrations/phase04_automated_research.sql`: Migration tạo 4 bảng mới (`table_product_research_seed`, `table_product_research_job`, `table_product_research_evidence`, `table_product_research_snapshot`) và mở rộng `table_product_research` (`discovery_source`, `ai_analysis`, `ai_confidence`, `first_seen_at`, `last_seen_at`).
* `libraries/class/class.ResearchProvider.php`: Kiến trúc Provider đa nguồn (`ResearchProviderInterface`, `AiResearchProvider`, `MockPlatformProvider` cho TikTok/Shopee/Lazada, `CsvProvider`, `ManualProvider`, DTO `ResearchCandidateDTO`, Factory `ResearchProviderFactory`).
* `libraries/class/class.AIResearchAgent.php`: Lớp tích hợp AI LLM (Gemini 1.5 Flash, OpenAI-compatible, MockAIProvider fallback), chuẩn hóa prompt (`research-v1.0`), kiểm thực cấu trúc JSON, giới hạn tần suất gọi và tuân thủ nguyên tắc FACT vs AI_ANALYSIS.
* `libraries/class/class.ResearchJobQueue.php`: Động cơ quản trị hàng đợi tác vụ nền, khóa xử lý đồng thời (concurrency locking), tự động phục hồi job treo, chấm điểm tự động, kiểm tra trùng lặp và ghi nhận Evidence / Snapshot.
* `cron/product_research_worker.php`: Background CLI / Token-protected worker xử lý hàng đợi theo lô.
* `admin/templates/product_research/seeds_tpl.php` & `seed_add_tpl.php`: Giao diện quản trị hạt giống nghiên cứu và nút kích hoạt quét ngay ("Quét ngay").
* `admin/templates/product_research/jobs_tpl.php`: Bảng điều khiển giám sát hàng đợi tác vụ nền với bộ lọc trạng thái và nút thử lại job lỗi ("Thử lại").
* `admin/templates/product_research/provider_config_tpl.php`: Giao diện cấu hình API keys bảo mật (masking), chọn nhà cung cấp AI và hạn mức request/ngày.
* `.ai/skills/fitnado-automated-research/SKILL.md`: Tài liệu kỹ năng quy trình nghiên cứu tự động và tích hợp AI Agent.
* `.ai/reports/PHASE-04-TEST-REPORT.md`: Báo cáo kiểm thử toàn diện 35/35 test cases đạt 100%.

### MODIFIED
* `admin/sources/product_research.php`: Mở rộng controller xử lý các action hạt giống (`seeds`, `seed_add`, `seed_save`, `seed_run_now`), hàng đợi (`jobs`, `job_retry`, `job_delete`), cấu hình AI (`provider_config`, `save_provider_config`) và bộ lọc nguồn `discovery_source`.
* `admin/templates/product_research/mans_tpl.php`: Bổ sung badge nguồn phát hiện (`discovery_source`) và bộ lọc theo nguồn.
* `admin/templates/product_research/man_add_tpl.php`: Tích hợp thẻ trực quan hóa phân tích AI (AI Insights Card) và bảng lịch sử bằng chứng dữ liệu (Evidence Provenance Table).
* `admin/templates/layout/menu.php`: Mở rộng menu đa cấp cho module "Nghiên cứu sản phẩm" (Tổng quan, Hạt giống, Hàng đợi, Cấu hình AI, Trọng số).
* `.ai/DATABASE.md`, `.ai/BUSINESS_RULES.md`, `.ai/ARCHITECTURE.md`: Cập nhật schema chi tiết, quy tắc Fact vs AI Analysis, Strict Human Gate và kiến trúc hệ thống Phase 04.

### RESULT
* Hoàn thành toàn diện Phase 04: Biến quy trình nghiên cứu thủ công thành hệ thống tự động hóa / bán tự động với AI Research Agent, duy trì kiểm soát nghiêm ngặt của con người (Human Gate), 0 lỗi cú pháp PHP 7.4 và vượt qua toàn bộ 35 unit/integration tests + kiểm thử hồi quy Phase 01, 02, 03.

---

## [2026-09-19] - PHASE 03: PRODUCT RESEARCH + PRODUCT SCORING + CANDIDATE PIPELINE

### CREATED
* `database/migrations/phase03_product_research.sql`: File migration tạo bảng `table_product_research` non-destructive với đầy đủ index.
* `libraries/class/class.ProductResearch.php`: Lớp xử lý nghiên cứu sản phẩm, chuẩn hóa URL/tên, động cơ chấm điểm 5 chiều (Demand, Content, Commission, Competition, SEO), xử lý `NULL != 0`, kiểm tra trùng lặp 3 cấp và ánh xạ tạo sản phẩm draft trong `table_product`.
* `admin/sources/product_research.php`: Controller quản trị nghiên cứu sản phẩm (CRUD, lọc, sắp xếp, duyệt, từ chối kèm lý do, tính lại điểm, tạo sản phẩm và cấu hình trọng số).
* `admin/templates/product_research/mans_tpl.php`: Giao diện danh sách ứng viên với widget thống kê, bộ lọc, bảng điểm chi tiết và màu sắc theo khoảng điểm.
* `admin/templates/product_research/man_add_tpl.php`: Giao diện form ứng viên với visualizer phân rã điểm 5 chiều, thanh tiến trình, lý do chấm điểm và cảnh báo duplicate.
* `admin/templates/product_research/create_product_tpl.php`: Màn hình ánh xạ ứng viên đã duyệt sang danh mục/thương hiệu thật của `table_product` và khởi tạo ưu đãi affiliate.
* `admin/templates/product_research/weights_tpl.php`: Màn hình cấu hình trọng số chấm điểm trực quan với thanh trượt và xác thực tổng 100%.
* `.ai/plans/PHASE-03-PRODUCT-RESEARCH.md`: Bản kế hoạch triển khai Phase 03.
* `.ai/skills/fitnado-product-research/SKILL.md`: Agent skill về quy trình nghiên cứu, chấm điểm và tạo sản phẩm.
* `.ai/reports/PHASE-03-TEST-REPORT.md`: Báo cáo kiểm thử toàn diện Phase 03.

### MODIFIED
* `admin/templates/layout/menu.php`: Thêm menu "Nghiên cứu sản phẩm" trong thanh điều hướng AdminLTE.
* `libraries/class/class.PDODb.php`: Bổ sung tham số `unix_socket` vào kết nối PDO giúp chạy mượt mà trên cả môi trường web và CLI.
* `.ai/DATABASE.md`, `.ai/BUSINESS_RULES.md`, `.ai/ARCHITECTURE.md`: Cập nhật schema, quy tắc nghiệp vụ và kiến trúc module nghiên cứu sản phẩm.

### RESULT
* Hoàn thành toàn diện Phase 03: Pipeline nghiên cứu và chấm điểm sản phẩm Gym/Fitness hoạt động chuẩn xác trên PHP 7.4, ngăn chặn auto-publish, chống trùng lặp dữ liệu và sẵn sàng cho Phase 04.

---

## [2026-09-19] - PHASE 02: PRODUCT DETAIL + REVIEW + COMPARISON + AFFILIATE FOUNDATION

### CREATED & EXTENDED
* `libraries/class/class.Affiliate.php`: Helper class quản lý nghiệp vụ tiếp thị liên kết đa sàn (Shopee, Lazada, TikTok Shop, Tiki, Brand...), thuật toán tính % giảm giá, chọn Best Offer thông minh, đồng bộ rating tự động và log click tracking.
* `table_product_affiliate`: Bảng cơ sở dữ liệu lưu các ưu đãi affiliate theo từng sản phẩm.
* `table_affiliate_click`: Bảng lưu vết chuyển hướng click với mã băm IP (SHA-256), loại thiết bị, nguồn truy cập và referer.
* `table_product`: Bổ sung các cột review & specs (`review_score`, `review_count`, `expert_pros`, `expert_cons`, `verdict`, `specs`, `best_for`, `affiliate_note`).
* `.ai/plans/PHASE-02-PRODUCT-AFFILIATE.md`: Bản kế hoạch triển khai chi tiết cho Phase 02.
* `.ai/reports/PHASE-02-TEST-REPORT.md`: Báo cáo nghiệm thu kiểm thử chi tiết.

### IMPLEMENTED & OPTIMIZED
* `sources/product.php`: Tối ưu hóa truy vấn chi tiết sản phẩm, nạp danh sách ưu đãi affiliate, parse bảng thông số kỹ thuật JSON, lấy sản phẩm so sánh đối đầu cùng danh mục, cập nhật lượt xem và tạo cấu trúc JSON-LD Schema (Product, AggregateRating, Offers).
* `templates/product/product_detail_tpl.php`: Giao diện chi tiết sản phẩm cao cấp, conversion-driven với:
  - Hero Gallery tương tác có badge "ĐÃ TEST THỰC TẾ", discount badge và Fitnado Score Pill (thang điểm 10).
  - Khối Best Deal CTA nổi bật với mã giảm giá 1-click copy và mức tiết kiệm hiển thị rõ ràng.
  - Khối so sánh giá đa sàn (Shopee, Lazada, TikTok Shop, Tiki...) với badge nhận diện thương hiệu.
  - Đánh giá chuyên gia Fitnado với Ưu điểm (Pros), Nhược điểm (Cons) và Đối tượng phù hợp nhất (Best For).
  - Bảng thông số kỹ thuật dạng bảng zebra rõ ràng.
  - Ma trận so sánh đối đầu trực tiếp (Head-to-head Comparison) với 2 sản phẩm tương đương.
  - Sticky Mobile Buy Bar cố định chân trang trên thiết bị di động.
* `sources/affiliate.php` & Route `go/{id}`: Xử lý click tracking an toàn với HTTP headers `X-Robots-Tag: noindex, nofollow`, bảo mật link và 302 redirect trực tiếp sang sàn.
* `sources/compare.php` & `templates/product/compare_tpl.php`: Nâng cấp công cụ so sánh sản phẩm độc lập (`/so-sanh`) với ma trận so sánh đầy đủ thông số, ưu nhược điểm và nút mua affiliate.
* `admin/sources/product.php` & `admin/templates/product/man/man_add_tpl.php`: Tích hợp form quản trị điểm đánh giá, đối tượng phù hợp, bảng thông số và repeater quản lý danh sách ưu đãi affiliate đa sàn kèm cờ Best Deal và Coupon Code.
* `libraries/class/class.Comments.php`: Sửa lỗi buffer nesting trong hàm `markdown()` giúp tích hợp form bình luận không bị xóa output bộ đệm ngoài.

### RESULT
* Hoàn thành toàn diện Phase 02: Xây dựng nền tảng kiếm doanh thu affiliate và trang chi tiết sản phẩm chuẩn SEO & CRO cho FITNADO.

---

## [2026-09-18] - PHASE 01: FRONTEND FOUNDATION + HOMEPAGE + REAL DATABASE INTEGRATION

### CREATED
* `.ai/plans/PHASE-01-FRONTEND.md`: Bản kế hoạch triển khai chi tiết cho Phase 01.
* `.ai/reports/PHASE_01_TEST_REPORT.md`: Báo cáo kiểm thử toàn diện cú pháp PHP 7.4, luồng dữ liệu thực tế và responsive đa thiết bị.

### VERIFIED & INTEGRATED
* `assets/css/fitnado.css`: Hệ thống CSS hoàn chỉnh với đầy đủ design tokens (`#0256AA`), component card, video, comparison table, goal box, và responsive breakpoints từ 360px đến 1920px.
* `templates/layout/header.php`: Tích hợp logo động, thanh tìm kiếm thông minh, nút giỏ hàng và đăng nhập thành viên.
* `templates/layout/menu.php`: Menu điều hướng chính với dropdown danh mục đồ tập và bài review từ database.
* `templates/layout/slide.php`: Hero section với số liệu dynamic thống kê và banner visual gradient.
* `templates/layout/footer.php`: Footer 5 cột đầy đủ thông tin giới thiệu, danh mục, chính sách, mạng xã hội và thông báo affiliate.
* `templates/layout/phone.php`: Bottom Navigation Bar cố định 5 tab chức năng cho Mobile.
* `templates/index/index_tpl.php`: Homepage hoàn chỉnh với 7 section động: Categories, Featured Products, 30s Video Reviews, Goals, Comparison, Knowledge & Guides, Newsletter Form.
* `sources/index.php` & `sources/allpage.php`: Kết nối dữ liệu thực tế từ MySQL qua PDODb và Cache helper.

### TESTING
* Đạt 100% cú pháp qua `php -l` cho toàn bộ các file PHP.
* Đạt Visual Parity với `fitnado-desktop.html` và `fitnado-mobile.html`.

### RESULT
* Hoàn thành Phase 01: Homepage FITNADO hoạt động thực tế trên cả Desktop và Mobile với dữ liệu thật từ database.

---

## [2026-09-18] - TASK 00: KHỞI TẠO BỘ NÃO `.ai/` CHO DỰ ÁN FITNADO

### CREATED
* `.ai/README.md`, `.ai/PROJECT.md`, `.ai/ARCHITECTURE.md`, `.ai/DATABASE.md`, `.ai/BUSINESS_RULES.md`, `.ai/DESIGN_SYSTEM.md`, `.ai/ADMIN_RULES.md`, `.ai/CODING_RULES.md`, `.ai/TESTING.md`, `.ai/CHANGELOG.md`
* `.ai/knowledge/ERRORS.md`, `.ai/knowledge/DECISIONS.md`, `.ai/knowledge/LESSONS_LEARNED.md`
* `.ai/skills/` (fitnado-core, fitnado-frontend, fitnado-admin, fitnado-affiliate, fitnado-ai-content, fitnado-regression)
* `.ai/reports/` (SOURCE_AUDIT.md, DATABASE_AUDIT.md)

### RESULT
* Hoàn thành audit kiến trúc và thiết lập hệ thống tri thức `.ai/`.
