# QUY TẮC NGHIỆP VỤ FITNADO (BUSINESS RULES)

---

## 1. ĐỊNH VỊ CỐT LÕI (CORE BUSINESS MODEL)

* **Mô hình chính**: FITNADO là nền tảng **Product Discovery, In-depth Review & Affiliate Marketing** chuyên sâu cho ngành Gym & Fitness.
* **Không phải Ecommerce Checkout truyền thống**: Hệ thống ưu tiên dẫn dắt người dùng từ nhu cầu tập luyện -> tìm kiếm sản phẩm phù hợp -> đọc review so sánh đa chiều -> chuyển đổi mua hàng qua link Affiliate chính hãng (Shopee, Lazada, Tiki, TikTok Shop, Brand Website).
* **Nghiêm cấm giả lập thanh toán khi không có luồng**: Mọi nút "Mua ngay" hoặc "Xem nơi bán tốt nhất" phải trỏ về affiliate link hợp lệ hoặc trang chi tiết sản phẩm.

---

## 2. LUỒNG XỬ LÝ NỘI DUNG (CONTENT LIFECYCLE)

```text
[1] Nghiên cứu Sản phẩm (Product Research)
      │
      ▼
[2] Tạo & Quản lý Sản phẩm (Product Entry trong Admin/DB)
      │
      ▼
[3] Đánh giá & So sánh (Content & Comparison Matrix)
      │
      ▼
[4] Tạo Video Ngắn Trực quan (AI / Real 30s Video)
      │
      ▼
[5] Xuất bản & Phân phối (Web SEO + TikTok Distribution)
      │
      ▼
[6] Điều hướng Affiliate (Affiliate Traffic & Outbound Clicks)
      │
      ▼
[7] Đo lường & Tối ưu (Analytics & Winner Detection)
```

---

## 3. TÍNH MINH BẠCH & TRUNG THỰC CỦA ĐÁNH GIÁ (REVIEW INTEGRITY)

Hệ thống phân định rõ ràng các cấp độ nội dung đánh giá:
1. `AI_ANALYSIS`: Phân tích tổng hợp thông số kỹ thuật, ưu/nhược điểm từ dữ liệu nhà sản xuất và cộng đồng.
2. `EDITOR_REVIEW`: Bài đánh giá chuyên sâu từ ban biên tập/chuyên gia thể hình của FITNADO.
3. `REAL_TEST`: Bài test thực tế tại phòng gym với hình ảnh/video trực tiếp (có đính kèm album/video test).
4. `USER_REVIEW`: Đánh giá từ người dùng thực tế (`table_comment`).

> [!CAUTION]
> **Quy tắc đạo đức AI**: AI không được tự nhận mình là người trực tiếp cầm/nắm/thử sản phẩm nếu đó chỉ là bài phân tích dữ liệu tổng hợp (`AI_ANALYSIS`). Phải dùng đại từ và văn phong phù hợp ("Theo phân tích của FITNADO", "Tổng hợp đánh giá từ người dùng", "Dữ liệu đo lường kỹ thuật cho thấy...").

---

## 4. NGUYÊN TẮC DỮ LIỆU ĐỘNG (DATA-DRIVEN RULES)

* **Không có dữ liệu thật -> Không hiển thị (No Data -> No Render)**:
  * Nếu một mục tiêu tập luyện (Goal), video review hay sản phẩm so sánh chưa có dữ liệu trong Database, frontend không hiển thị khối rỗng hoặc khối lỗi.
  * Phải có điều kiện `if (!empty($data)) { ... }` trước khi render bất kỳ component nào.
* **Không chèn dữ liệu tĩnh giả mạo (No Fake Mockup Data in PHP)**:
  * Tuyệt đối không hardcode text/giá/link sản phẩm giả vào template PHP để "cho đẹp giao diện".
  * Mọi nội dung hiển thị phải query từ database hoặc nạp từ file config.

---

## 5. NGUYÊN TẮC QUẢN TRỊ ADMIN (ADMIN FIRST RULE)

* Bất kỳ trường thông tin mới nào xuất hiện trên Frontend (ví dụ: Link Affiliate Shopee/TikTok, Tiêu chí so sánh, Đánh giá chuyên gia, Video ID ngắn) **BẮT BUỘC** phải có nơi quản lý, cấu hình và chỉnh sửa trong Admin hoặc bảng Config tương ứng.

---

## 6. QUY TẮC NGHIÊN CỨU & DUYỆT SẢN PHẨM (PRODUCT RESEARCH RULES - PHASE 03)

* **Candidate != Live Product**:
  * `table_product_research` là môi trường nghiên cứu nội bộ, không có route hay URL công khai trên frontend.
  * Chỉ có sản phẩm trong `table_product` có cờ `hienthi` mới xuất hiện trên website.
* **Human Approval First**:
  * Nghiêm cấm mọi luồng AI/Scraper tự động tạo và xuất bản sản phẩm ra ngoài.
  * Mọi sản phẩm tạo từ ứng viên phải qua nút "Tạo sản phẩm" của Admin và mặc định ở chế độ **Bản nháp (Draft / Chưa có cờ hienthi)**.
* **Score là tín hiệu ưu tiên (Research Priority Signal)**:
  * Điểm số (0–100) tổng hợp từ 5 chiều (Demand 30%, Content 25%, Commission 20%, Competition 15%, SEO 10%).
  * Không bao giờ gọi score là "Cam kết thắng lớn" (Guaranteed Winner).
* **Missing Data Rule (`NULL != 0`)**:
  * Dữ liệu chưa biết (`NULL`) không bị ép về `0`, thuật toán chuẩn hóa điểm theo các chiều có dữ liệu.
* **Anti-Duplicate Triad**:
  * Hệ thống cảnh báo trùng lặp 3 cấp: (1) `platform + external_product_id`, (2) `normalized_url`, (3) `normalized_name + brand`.
  * Không cho phép tạo duplicate `table_product` nếu ứng viên đã có `id_product`.
* **Chuyển đổi trạng thái hợp lệ**:
  * `DISCOVERED` → `RESEARCHED` → `APPROVED` / `REJECTED` → `PRODUCT_CREATED`.
  * Từ chối (`REJECTED`) bắt buộc có lý do và không xóa dữ liệu để phục vụ đối soát lịch sử.

---

## 7. QUY TẮC NGHIÊN CỨU TỰ ĐỘNG & AI RESEARCH AGENT (PHASE 04)

* **Tách biệt tuyệt đối giữa FACT vs AI_ANALYSIS**:
  * Các trường số liệu thị trường thực tế (`sales_count`, `rating`, `review_count`, `commission_rate`, `creator_count`, `video_count`) BẮT BUỘC chỉ được lấy từ dữ liệu nguồn xác thực (crawler, sàn, API, CSV).
  * AI LLM TUYỆT ĐỐI KHÔNG ĐƯỢC TỰ BỊA ĐẶT các số liệu trên. Nếu không có dữ liệu nguồn, các trường này PHẢI LÀ `NULL`.
  * Nhận định AI (`content_angles`, `problem_solved`, `visual_demo_potential`, `comparison_potential`, `risk_notes`, `confidence`) được gắn nhãn `AI_ANALYSIS` và lưu riêng trong `ai_analysis` JSON và `table_product_research_evidence`.
* **Human Gate Bắt Buộc (Strict Human Gate)**:
  * Toàn bộ ứng viên do hệ thống tự động/AI quét về chỉ được phép dừng ở trạng thái `RESEARCHED`.
  * Hệ thống ngầm và cron worker KHÔNG BAO GIỜ tự động chuyển trạng thái sang `APPROVED` hoặc tự động tạo `table_product`.
  * Quyết định tạo sản phẩm và xuất bản ra website FITNADO luôn thuộc về Admin con người.
* **Minh bạch xuất xứ dữ liệu (Data Provenance)**:
  * Mỗi dữ liệu thu thập được lưu vết trong `table_product_research_evidence` kèm `evidence_type` (`FACT` hoặc `AI_ANALYSIS`), URL nguồn và timestamp.
* **Xử lý quét lại & Lịch sử biến động (Re-scanning & Snapshots)**:
  * Khi quét lại một sản phẩm đã tồn tại (dựa trên Duplicate Triad), hệ thống KHÔNG tạo bản ghi candidate mới.
  * Hệ thống cập nhật `last_seen_at` và ghi nhận một bản ghi `table_product_research_snapshot` để theo dõi biến động thị trường theo thời gian.
* **Bảo mật thông tin & API Keys**:
  * API key của các nhà cung cấp AI (Gemini, OpenAI) được lưu trong bảng cấu hình `table_setting`, che giấu (masking) trên giao diện Admin và không bao giờ commit vào Git.

---

## 8. QUY TẮC NỘI DUNG AI & DUYỆT BẢN THẢO (AI CONTENT RULES - PHASE 05)

* **Nguyên tắc Factual Integrity (Sự thật là tối thượng)**:
  * Fact > AI Analysis: AI Content chỉ được tạo dựa trên dữ liệu sản phẩm, thông số kỹ thuật thực tế và evidence facts đã duyệt.
  * Nghiêm cấm bịa đặt trải nghiệm cá nhân giả mạo ("Tôi đã tập thử", "Mình dùng 3 tháng...") nếu không có cờ `REAL_TEST` kèm tư liệu test phòng gym.
  * Nghiêm cấm bịa đặt lời nhận xét của khách hàng giả mạo (Fake Testimonials / Fake User Quotes).
  * Nghiêm cấm cam kết y tế hoặc chữa bệnh trái luật ("chữa khỏi đau lưng", "cam kết giảm 10kg sau 1 tuần").
* **Chất lượng nội dung & Quality Gate Bắt Buộc**:
  * Mọi gói nội dung AI sinh ra phải vượt qua bộ lọc `validateQualityGate()` trước khi chuyển sang `REVIEW_REQUIRED`.
  * Nếu phát hiện từ khóa cấm hoặc vi phạm claim sự thật, hệ thống hạ `quality_score` và đánh dấu cờ vi phạm để Admin đối soát.
* **Quy tắc Kiểm duyệt & Trạng thái (Human Content Gate)**:
  * Trạng thái mặc định sau khi sinh: `REVIEW_REQUIRED`.
  * Tuyệt đối không tự động publish lên website hoặc auto-apply vào `table_product`.
  * Chuyển đổi trạng thái hợp lệ: `DRAFT` → `REVIEW_REQUIRED` → `APPROVED` / `REJECTED` → `APPLIED_TO_PRODUCT`.
  * Khi bị `REJECTED`, bắt buộc lưu `review_notes` để phục vụ tinh chỉnh prompt và audit.
* **Cơ chế Snapshot Hoàn nguyên (Reversible Apply & Backup)**:
  * Khi Admin bấm "Áp dụng vào sản phẩm" (Apply to Product), hệ thống bắt buộc tự động sao lưu toàn bộ giá trị cũ của các trường sản phẩm liên quan (`descvi`, `contentvi`, `expert_pros`, `expert_cons`, `verdict`, `best_for`, `specs`) vào `table_product_content_backup`.
  * Cho phép Admin xem Diff đối soát trước khi áp dụng và khôi phục (Rollback) nội dung cũ bất cứ lúc nào.
* **Định dạng Kịch bản TikTok & Cầu nối Shot Plan (Bridge to Phase 06)**:
  * Mỗi kịch bản TikTok kèm danh sách 7 loại Hooks chiến lược.
  * Kịch bản bắt buộc có mảng `shot_plan` chi tiết từng phân đoạn (Scene #, duration, visual instruction, voiceover, on-screen text, asset requirement).
  * Cấu trúc `shot_plan` này là dữ liệu đầu vào chuẩn hóa sẵn sàng cho Phase 06 (AI Video Rendering & Video Scene Assembler).
* **Phát hiện Nội dung Lỗi thời (Deterministic Source Hash)**:
  * Sử dụng mã băm SHA-256 (`source_hash`) của toàn bộ thông tin sản phẩm và nghiên cứu tại thời điểm sinh nội dung.
  * Nếu sản phẩm hoặc dữ liệu research bị thay đổi sau khi sinh, hệ thống hiển thị nhãn cảnh báo "Nội dung cũ hơn dữ liệu nghiên cứu (Outdated)" để Admin cân nhắc chạy lại.

---

## 9. QUY TẮC SẢN XUẤT VIDEO AI & KIỂM DUYỆT (AI VIDEO RULES - PHASE 06)

* **Nguyên tắc Approved Content Only (Chỉ dùng Kịch bản Đã Duyệt)**:
  * Video Project chỉ được phép khởi tạo từ các kịch bản TikTok trong `table_ai_content` đã có trạng thái `APPROVED` hoặc `APPLIED`.
  * Nghiêm cấm tạo video từ các bản nháp `DRAFT` hoặc `REVIEW_REQUIRED`.
* **Shot Plan là Nguồn Chân lý Duy nhất (Source of Truth)**:
  * Video Engine tiêu thụ trực tiếp các trường `scene_number`, `duration`, `visual_instruction`, `voiceover`, `on_screen_text`, `asset_requirement` từ Phase 05.
  * AI Video không được tự ý sáng chế thông số sản phẩm hoặc thêm testimonial không có thật.
* **Quy chuẩn Bản quyền & An toàn Tài nguyên (Asset Safety & Rights)**:
  * Ưu tiên tái sử dụng ảnh sản phẩm `table_product.photo` và thư viện ảnh `table_gallery`.
  * Nếu bất kỳ phân cảnh nào thiếu tài nguyên bắt buộc, trạng thái dự án chuyển sang `WAITING_ASSET` và bị chặn không cho đưa vào hàng đợi render.
  * Tuyệt đối không tự ý tải video lậu từ TikTok/Shopee để tránh vi phạm bản quyền thương mại.
* **Cơ chế Quản lý Phiên bản & Lỗi thời (Versioning & Outdated)**:
  * Khi tạo lại video cho cùng sản phẩm, hệ thống tạo bản ghi mới với `version = v+1`, giữ nguyên vẹn file video và lịch sử của các version cũ.
  * Nếu kịch bản TikTok gốc bị sửa đổi sau thời điểm tạo dự án, hệ thống tự động gắn cờ `is_outdated = 1` và hiển thị cảnh báo trực quan trên Admin.
* **Kiểm định Chất lượng Media (Media QC Validation)**:
  * Mọi video sau khi tải về lưu trữ cục bộ phải qua kiểm định: không được là file rỗng (zero-byte), định dạng MP4 hợp lệ, độ dài khớp kịch bản và tỷ lệ khung hình dọc 9:16 chuẩn TikTok (1080x1920).
* **Rào chắn Kiểm duyệt của Con người (Strict Human Approval Gate)**:
  * Video sau khi render thành công bắt buộc dừng ở trạng thái `REVIEW_REQUIRED`.
  * Admin xem trực tiếp video qua trình phát HTML5 `<video controls>` để duyệt (`APPROVED`) hoặc từ chối (`REJECTED`) kèm lý do cụ thể.
* **Tuyệt đối Không Tự Động Xuất bản (No Auto-Publishing / No Auto-Post)**:
  * Phase 06 dừng lại ở video đã được Admin phê duyệt (`APPROVED`). Tuyệt đối không tự động kết nối API xuất bản lên TikTok, YouTube Shorts hay Instagram Reels.
* **Bảo vệ Ngân sách & Hạn mức Render (Cost Guard & Daily Limit)**:
  * Áp dụng hạn mức sinh video hàng ngày (`daily_video_limit`) trong cấu hình để tránh chi phí ngoài ý muốn khi chạy hàng loạt.



