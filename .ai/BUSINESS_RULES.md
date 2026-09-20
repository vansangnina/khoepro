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

---

## 10. QUY TẮC LOW-COST HYBRID VIDEO COMPOSER (AI VIDEO RULES - PHASE 06.2)

* **Ba Chế độ Video (Three Video Modes)**:
  * **ECONOMY (Mặc định)**: 100% kết xuất cục bộ từ ảnh sản phẩm, gallery, chuyển động Ken Burns pan/zoom, voiceover TTS và phụ đề TikTok safe-area. **Chi phí API Video bên ngoài = 0 VND**.
  * **HYBRID**: Sử dụng toàn bộ hạ tầng Economy, cho phép tối đa 1–2 phân cảnh AI Video sinh chuyển động chân thực (giới hạn $\le$ 8s) cho các cảnh thực sự cần thiết (vd: Demo khóa / hành động).
  * **PREMIUM**: Cho phép nhiều phân cảnh AI Video. Chỉ dành cho sản phẩm trọng điểm đã được Admin xác nhận đầu tư.
* **Cấu trúc Phân cảnh Tiếp thị Bắt buộc (Purpose-Driven Flow)**:
  * Mọi phân cảnh trong Shot Plan phải có `purpose` thuộc danh mục hợp lệ: `HOOK`, `PROBLEM`, `PRODUCT_INTRO`, `DEMO`, `BENEFIT`, `PROOF`, `LIMITATION`, `COMPARISON`, `BEST_FOR`, `CTA`.
  * **0–3 giây đầu BẮT BUỘC có purpose là `HOOK`** lấy từ kịch bản đã được phê duyệt ở Phase 05. AI Video không được tự ý bịa đặt hook mới.
  * Phân cảnh cuối cùng bắt buộc là `CTA` dẫn dắt xem giỏ hàng.
* **Quy tắc Gọi AI Scene Provider (Beeknoee Rule)**:
  * Chỉ gọi API Beeknoee khi phân cảnh có `render_method = 'AI_VIDEO'`.
  * Beeknoee đóng vai trò là **Optional AI Scene Generator**, không phải Full Final Video Generator.
* **Hạn mức Chi phí & Cost Guard**:
  * Trước khi render, hệ thống tự động tính toán: số cảnh AI, tổng số giây AI và chi phí ước tính (`Estimated AI Video Cost`).
  * Cấu hình `max_ai_video_cost_per_video` (mặc định 60.000 VND). Nếu chi phí ước tính vượt quá hạn mức, hệ thống TỰ ĐỘNG CHẶN (Block) trừ khi có quyền Admin Override.
* **Hạ tầng FFmpeg & Fallback**:
  * Hệ thống kiểm định `ffmpeg -version` và `ffprobe -version`.
  * Nếu chưa có FFmpeg, báo cáo chẩn đoán trạng thái trên Admin Settings và kích hoạt bộ sinh Local Media Container fallback, tuyệt đối không silently fail.

---

## 11. QUY TẮC TRUNG TÂM XUẤT BẢN & PHÂN PHỐI (PUBLISHING CENTER RULES - PHASE 07)

* **Approved Video Only (Chỉ Xuất Bản Video Đã Phê Duyệt)**:
  * Bài đăng (`table_publish_post`) chỉ được phép khởi tạo từ các dự án video (`table_video_project`) đã có trạng thái `APPROVED`.
  * Nghiêm cấm tạo gói xuất bản từ các video nháp (`DRAFT`), đang render (`RENDERING`), hoặc đang chờ duyệt (`REVIEW_REQUIRED`).
* **Rào chắn Phê duyệt của Con người (Human Gate & Manual First)**:
  * Trọng tâm vận hành của FITNADO là quy trình **Manual Publishing Provider**: Admin xem gói bài đăng, tải video MP4, copy 1-click Caption/Hashtags, đăng trực tiếp lên TikTok Creator Center hoặc Mobile App, sau đó dán TikTok Video URL / Post ID vào hệ thống để xác nhận.
  * Mọi bài đăng mới tạo bắt đầu ở trạng thái `DRAFT`.
* **Tiêu chuẩn Sẵn sàng & Kiểm tra Trước Xuất bản (Pre-publish Checklist)**:
  * Bài đăng chỉ được chuyển từ `DRAFT` sang `READY` khi thỏa mãn 100% Pre-publish Checklist:
    1. Video MP4 cục bộ tồn tại trên đĩa và dung lượng > 0 byte.
    2. Độ dài caption hợp lệ (1 – 2.200 ký tự).
    3. Có ít nhất 1 hashtag hợp lệ (bắt đầu bằng `#`).
    4. Kênh/Tài khoản xuất bản (`id_account`) đang hoạt động (`status = 'ACTIVE'`).
    5. Video gốc không bị gắn cờ lỗi thời hoặc bị từ chối.
* **Cơ chế Đóng băng Snapshot khi Sẵn sàng (Immutable Snapshot on READY)**:
  * Khi bài đăng chuyển sang `READY`, hệ thống tạo một bản sao lưu bất biến (`post_snapshot` JSON) gồm: file video URL, caption, hashtags, thông tin kênh, và mã băm `source_hash`.
  * Snapshot này đảm bảo nội dung xuất bản không bị sai lệch nếu dữ liệu nguồn bị thay đổi sau đó.
* **Hủy bỏ Snapshot khi Chỉnh sửa (Edit Invalidation to DRAFT)**:
  * Nếu người dùng chỉnh sửa caption, hashtags, hoặc kênh xuất bản của một bài đăng đang ở trạng thái `READY` hoặc `SCHEDULED`, hệ thống **bắt buộc hủy bỏ snapshot** và chuyển trạng thái trở lại `DRAFT` để yêu cầu kiểm tra checklist lại từ đầu.
* **Bất biến Tuyệt đối với Bài Đã Xuất bản (Published Post Immutability)**:
  * Bài đăng đã ở trạng thái `PUBLISHED` tuyệt đối không cho phép chỉnh sửa nội dung trực tiếp.
  * Nếu muốn tái sử dụng hoặc đăng lại phiên bản mới, bắt buộc sử dụng tính năng **Nhân bản (Duplicate Post)** để tạo một bản nháp `DRAFT` hoàn toàn mới.
* **Khóa Chống Đăng Trùng Lặp (Double Publish Concurrency Lock)**:
  * Khi bắt đầu tiến trình xuất bản (ngay lập tức hoặc qua background worker), hệ thống đặt cờ `publish_lock = 1` và `locked_at = NOW()`.
  * Mọi yêu cầu xuất bản song song trên cùng bài đăng sẽ bị từ chối ngay lập tức.
  * Hệ thống tự động giải phóng stale lock nếu tiến trình bị treo quá 10 phút (600 giây).
* **Quy chuẩn Xác thực URL & TikTok Post ID (Validation Standards)**:
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

---

## 10. QUY TẮC LOW-COST HYBRID VIDEO COMPOSER (AI VIDEO RULES - PHASE 06.2)

* **Ba Chế độ Video (Three Video Modes)**:
  * **ECONOMY (Mặc định)**: 100% kết xuất cục bộ từ ảnh sản phẩm, gallery, chuyển động Ken Burns pan/zoom, voiceover TTS và phụ đề TikTok safe-area. **Chi phí API Video bên ngoài = 0 VND**.
  * **HYBRID**: Sử dụng toàn bộ hạ tầng Economy, cho phép tối đa 1–2 phân cảnh AI Video sinh chuyển động chân thực (giới hạn $\le$ 8s) cho các cảnh thực sự cần thiết (vd: Demo khóa / hành động).
  * **PREMIUM**: Cho phép nhiều phân cảnh AI Video. Chỉ dành cho sản phẩm trọng điểm đã được Admin xác nhận đầu tư.
* **Cấu trúc Phân cảnh Tiếp thị Bắt buộc (Purpose-Driven Flow)**:
  * Mọi phân cảnh trong Shot Plan phải có `purpose` thuộc danh mục hợp lệ: `HOOK`, `PROBLEM`, `PRODUCT_INTRO`, `DEMO`, `BENEFIT`, `PROOF`, `LIMITATION`, `COMPARISON`, `BEST_FOR`, `CTA`.
  * **0–3 giây đầu BẮT BUỘC có purpose là `HOOK`** lấy từ kịch bản đã được phê duyệt ở Phase 05. AI Video không được tự ý bịa đặt hook mới.
  * Phân cảnh cuối cùng bắt buộc là `CTA` dẫn dắt xem giỏ hàng.
* **Quy tắc Gọi AI Scene Provider (Beeknoee Rule)**:
  * Chỉ gọi API Beeknoee khi phân cảnh có `render_method = 'AI_VIDEO'`.
  * Beeknoee đóng vai trò là **Optional AI Scene Generator**, không phải Full Final Video Generator.
* **Hạn mức Chi phí & Cost Guard**:
  * Trước khi render, hệ thống tự động tính toán: số cảnh AI, tổng số giây AI và chi phí ước tính (`Estimated AI Video Cost`).
  * Cấu hình `max_ai_video_cost_per_video` (mặc định 60.000 VND). Nếu chi phí ước tính vượt quá hạn mức, hệ thống TỰ ĐỘNG CHẶN (Block) trừ khi có quyền Admin Override.
* **Hạ tầng FFmpeg & Fallback**:
  * Hệ thống kiểm định `ffmpeg -version` và `ffprobe -version`.
  * Nếu chưa có FFmpeg, báo cáo chẩn đoán trạng thái trên Admin Settings và kích hoạt bộ sinh Local Media Container fallback, tuyệt đối không silently fail.

---

## 11. QUY TẮC TRUNG TÂM XUẤT BẢN & PHÂN PHỐI (PUBLISHING CENTER RULES - PHASE 07)

* **Approved Video Only (Chỉ Xuất Bản Video Đã Phê Duyệt)**:
  * Bài đăng (`table_publish_post`) chỉ được phép khởi tạo từ các dự án video (`table_video_project`) đã có trạng thái `APPROVED`.
  * Nghiêm cấm tạo gói xuất bản từ các video nháp (`DRAFT`), đang render (`RENDERING`), hoặc đang chờ duyệt (`REVIEW_REQUIRED`).
* **Rào chắn Phê duyệt của Con người (Human Gate & Manual First)**:
  * Trọng tâm vận hành của FITNADO là quy trình **Manual Publishing Provider**: Admin xem gói bài đăng, tải video MP4, copy 1-click Caption/Hashtags, đăng trực tiếp lên TikTok Creator Center hoặc Mobile App, sau đó dán TikTok Video URL / Post ID vào hệ thống để xác nhận.
  * Mọi bài đăng mới tạo bắt đầu ở trạng thái `DRAFT`.
* **Tiêu chuẩn Sẵn sàng & Kiểm tra Trước Xuất bản (Pre-publish Checklist)**:
  * Bài đăng chỉ được chuyển từ `DRAFT` sang `READY` khi thỏa mãn 100% Pre-publish Checklist:
    1. Video MP4 cục bộ tồn tại trên đĩa và dung lượng > 0 byte.
    2. Độ dài caption hợp lệ (1 – 2.200 ký tự).
    3. Có ít nhất 1 hashtag hợp lệ (bắt đầu bằng `#`).
    4. Kênh/Tài khoản xuất bản (`id_account`) đang hoạt động (`status = 'ACTIVE'`).
    5. Video gốc không bị gắn cờ lỗi thời hoặc bị từ chối.
* **Cơ chế Đóng băng Snapshot khi Sẵn sàng (Immutable Snapshot on READY)**:
  * Khi bài đăng chuyển sang `READY`, hệ thống tạo một bản sao lưu bất biến (`post_snapshot` JSON) gồm: file video URL, caption, hashtags, thông tin kênh, và mã băm `source_hash`.
  * Snapshot này đảm bảo nội dung xuất bản không bị sai lệch nếu dữ liệu nguồn bị thay đổi sau đó.
* **Hủy bỏ Snapshot khi Chỉnh sửa (Edit Invalidation to DRAFT)**:
  * Nếu người dùng chỉnh sửa caption, hashtags, hoặc kênh xuất bản của một bài đăng đang ở trạng thái `READY` hoặc `SCHEDULED`, hệ thống **bắt buộc hủy bỏ snapshot** và chuyển trạng thái trở lại `DRAFT` để yêu cầu kiểm tra checklist lại từ đầu.
* **Bất biến Tuyệt đối với Bài Đã Xuất bản (Published Post Immutability)**:
  * Bài đăng đã ở trạng thái `PUBLISHED` tuyệt đối không cho phép chỉnh sửa nội dung trực tiếp.
  * Nếu muốn tái sử dụng hoặc đăng lại phiên bản mới, bắt buộc sử dụng tính năng **Nhân bản (Duplicate Post)** để tạo một bản nháp `DRAFT` hoàn toàn mới.
* **Khóa Chống Đăng Trùng Lặp (Double Publish Concurrency Lock)**:
  * Khi bắt đầu tiến trình xuất bản (ngay lập tức hoặc qua background worker), hệ thống đặt cờ `publish_lock = 1` và `locked_at = NOW()`.
  * Mọi yêu cầu xuất bản song song trên cùng bài đăng sẽ bị từ chối ngay lập tức.
  * Hệ thống tự động giải phóng stale lock nếu tiến trình bị treo quá 10 phút (600 giây).
* **Quy chuẩn Xác thực URL & TikTok Post ID (Validation Standards)**:
  * Khi xác nhận xuất bản thủ công (`markManualPublished`), URL bài đăng bắt buộc phải tuân theo chuẩn RFC URL và thuộc tên miền hợp lệ của TikTok (`tiktok.com`, `vm.tiktok.com`, `vt.tiktok.com`).
  * Mã bài đăng (`tiktok_post_id`) phải là chuỗi số nguyên dương hợp lệ (numeric string).
* **Nguyên tắc Trung thực Tuyệt đối về API (No Fake API Success)**:
  * Provider `TikTokPublishProvider` khi chưa có thông tin cấu hình OAuth (Client Key / Secret / Access Token) bắt buộc trả về trạng thái `NOT CONFIGURED` và cờ `isConfigured() === false`.
  * Tuyệt đối không giả lập (fake/mock) thông báo "Xuất bản API thành công" khi chưa có kết nối thật với TikTok Content Posting API.
* **Bảo mật Thông tin Kênh & Masking Bí mật (Secret Masking & Security)**:
  * Toàn bộ API Client Secret, Access Token, Refresh Token của các kênh mạng xã hội lưu trong `table_publish_account` và `table_setting` bắt buộc phải được che giấu (masking `••••••••`) trên giao diện Admin và không bao giờ xuất hiện trong public log.

---

## 12. QUY TẮC ANALYTICS, ATTRIBUTION & PHÁT HIỆN WINNER (PHASE 08)

* **Nguyên tắc Dữ liệu Quan sát Thực tế (Observed Data Only)**:
  * Toàn bộ chỉ số hiệu suất (`sessions`, `clicks`, `conversions`, `revenue`, `commission`, `cost`, `ROI`) BẮT BUỘC chỉ được tính từ dữ liệu ghi nhận thực tế từ sự kiện web và tệp đối soát đơn hàng.
  * Tuyệt đối nghiêm cấm giả lập số liệu chuyển đổi (no fake production analytics). Nếu chưa nhập CSV đối soát đơn hàng, giao diện phải hiển thị rõ thông báo `CONVERSION DATA: CHƯA KẾT NỐI`.
* **Phân định Nguồn Attribution bằng Định danh Chiến dịch (Campaign Identity Attribution)**:
  * Mọi bài đăng xuất bản ở Phase 07 có một `tracking_code` độc nhất (ví dụ: `fp_tikt_99_25b4470f`).
  * Khi người xem bấm vào landing link `?ref=fp_...` hoặc `utm_content=fp_...`, hệ thống phân giải chính xác `id_post`, `id_video`, `id_content`, `id_product`.
  * Mô hình phân bổ chuẩn: **Last Eligible Fitnado Content Touch** với cửa sổ quy kết mặc định là 30 ngày (Attribution Window = 30 days).
* **Cô lập Traffic Trực tiếp & Nhiều Bài đăng (Traffic Isolation)**:
  * Người dùng truy cập trực tiếp website (không qua link video) có `post_id = NULL` và `is_direct = true`, không được gán nhầm cho bất kỳ video TikTok nào.
  * Nhiều bài đăng khác nhau cho cùng một sản phẩm giữ danh tính tracking riêng biệt để so sánh hiệu quả giữa các góc nội dung/Hook.
* **Quy tắc Kiểm soát Kích thước Mẫu Nghiêm ngặt (Strict Sample Size Gating)**:
  * Để tránh kết luận sai lầm do mẫu quá nhỏ (Small Sample Fallacy), sản phẩm hoặc bài đăng CHƯA ĐẠT ngưỡng tối thiểu (`min_landing_sessions >= 30` và `min_affiliate_clicks >= 10`):
    * BẮT BUỘC giữ trạng thái `INSUFFICIENT_DATA` (Chưa đủ dữ liệu).
    * TUYỆT ĐỐI KHÔNG BAO GIỜ được tuyên bố sớm là `WINNER` hay `UNDERPERFORMING` / `LOSER`.
* **Phân cấp Tín hiệu Hiệu suất (Signal Level Hierarchy)**:
  * `REVENUE`: Đã tạo ra hoa hồng thực tế vượt chi phí sản xuất video (Net ROI > 0).
  * `CONVERSION`: Đã có đơn hàng chuyển đổi thành công trên sàn TMĐT.
  * `CLICK`: Chưa có đơn nhưng CTR affiliate cao ($\ge 5\%$), người xem có ý định mua hàng rõ rệt.
  * `TRAFFIC`: Có lượt truy cập landing page nhưng tỷ lệ click affiliate còn thấp.
  * `NONE`: Chưa có lượt truy cập nào.
* **Quy chuẩn Trạng thái Đánh giá 6 Cấp độ (6-Level Performance Maturity Standards)**:
  * `INSUFFICIENT_DATA`: Chưa đạt đủ số sessions (<30) hoặc clicks (<10) tối thiểu. Bắt buộc giữ trạng thái này, không kết luận sớm.
  * `TRAFFIC_PROMISING`: Lưu lượng xem cao ($\ge 30$ sessions) nhưng ít click (<10 clicks). Đề xuất tối ưu trang sản phẩm và nút CTA (`REVIEW_PRODUCT_PAGE`).
  * `CLICK_PROMISING`: Lượt click cao ($\ge 10$ clicks) và CTR $\ge 5\%$, nhưng chưa có dữ liệu đối soát đơn hàng (`conversion_source_connected = false`). TUYỆT ĐỐI CẤM gán nhãn `WINNER` hay `REVENUE_WINNER`.
  * `CONVERSION_PROMISING`: Đã có đơn hàng đối soát từ sàn TMĐT nhưng Net ROI chưa vượt chi phí sản xuất (đang hoàn vốn).
  * `REVENUE_WINNER`: Đã có đơn hàng đối soát và Net ROI dương (Hoa hồng thực nhận > Chi phí sản xuất). Đủ điều kiện đề xuất nâng cấp video (`UPGRADE_TO_HYBRID`).
  * `UNDERPERFORMING`: Đủ mẫu ($\ge 30$ sessions), CTR $< 1\%$ và 0 đơn hàng sau ít nhất 3 ngày kiểm thử. Đề xuất tạm dừng hoặc đổi Hook mới (`CREATE_NEW_HOOK`).
* **Tách biệt Tuyệt đối giữa Điểm Nghiên cứu & Chỉ số Hiệu suất (Research Score != Performance Score)**:
  * Điểm nghiên cứu thị trường ở Phase 03/04 (`research_score` / `total_score`) là giả thuyết tiềm năng ban đầu.
  * Chỉ số hiệu suất Phase 08 (`CTR`, `CVR`, `EPC`, `ROI`) là thực tế quan sát được trên thị trường.
  * Hai nhóm chỉ số này lưu ở hai bảng riêng biệt và không ghi đè lẫn nhau.
* **Nguyên tắc Chỉ Đề xuất — Không Tự ý Hành động (No Auto-Spend / No Auto-Publish Actions)**:
  * Khi phát hiện tín hiệu hiệu suất, hệ thống sinh đề xuất hành động rõ ràng (`recommendation`: `UPGRADE_TO_HYBRID`, `CREATE_VARIATION`, `CREATE_NEW_HOOK`, `KEEP_TESTING`).
  * Hệ thống KHÔNG BAO GIỜ tự động chi tiền gọi API AI video, tự động tăng ngân sách, hay tự động xuất bản thêm bài đăng mà không có sự kiểm duyệt và bấm nút từ Admin con người.
* **Toàn vẹn Đối soát & Chống Trùng Đơn (Conversion Idempotency & Duplicate Protection)**:
  * Mỗi đơn hàng nhập từ CSV có mã định danh duy nhất trên sàn.
  * Việc tải lại cùng một file CSV nhiều lần không làm nhân đôi số lượng đơn hay doanh thu hoa hồng.
  * Khi đơn hàng bị hoàn trả hoặc hủy, hệ thống cập nhật trạng thái sang `REVERSED` hoặc `CANCELLED` mà không làm mất lịch sử đối soát.
* **Toán học An toàn & Bảo vệ Chia cho 0 (Safe Math & Zero Division Guard)**:
  * Toàn bộ hàm tính toán tỷ lệ (`CTR`, `CVR`, `EPC`, `ROI`) có cơ chế kiểm tra mẫu số. Nếu mẫu số $= 0$, hàm trả về `0.0` an toàn, không bao giờ gây lỗi PHP Fatal Error hoặc Warning.

---

## 13. QUY TẮC DATA-DRIVEN OPTIMIZATION LOOP & THỬ NGHIỆM A/B (PHASE 09)

* **Nguyên tắc Tam giác Vận hành Khép kín (AI Recommends, Human Decides, System Executes)**:
  * **AI Recommends**: Động cơ tối ưu hóa (`OptimizationEngine`) quét dữ liệu hiệu suất và đưa ra các đề xuất cải thiện kèm mã máy (`reason_code`), giải thích tiếng Việt (`reason_summary`), giả thuyết kiểm chứng (`hypothesis`), và chi phí ước tính (`estimated_cost_vnd`).
  * **Human Decides**: Mọi khuyến nghị bắt buộc phải chờ phê duyệt của Admin (`status = 'PENDING'`). Admin có quyền phê duyệt thông thường, phê duyệt kèm Admin Override (vượt hạn mức), hoặc từ chối kèm lý do.
  * **System Executes**: Sau khi Admin duyệt, hệ thống tự động sinh biến thể kịch bản (Phase 05), video project (Phase 06), và bài đăng phân phối có mã tracking cô lập (Phase 07).
* **Nguyên tắc Đơn Biến Số (One-Variable Principle)**:
  * Mỗi thử nghiệm A/B chỉ được phép thay đổi duy nhất 1 biến số trong danh mục:
    * `HOOK`: Thay đổi 0–3s đầu tiên (câu giật tít mở màn).
    * `CTA`: Thay đổi câu kêu gọi hành động cuối video/bài đăng.
    * `SCRIPT`: Thay đổi góc tiếp cận nội dung (Content Angle).
    * `VIDEO_STYLE`: Thay đổi phong cách sản xuất (Nâng cấp từ ECONOMY lên HYBRID).
    * `VOICE`: Thay đổi giọng đọc thuyết minh TTS.
    * `OFFER`: Thay đổi ưu đãi tiếp thị liên kết nổi bật.
  * Thử nghiệm đa biến số bắt buộc phải được gắn nhãn minh bạch là `MULTIVARIATE`.
* **Ưu tiên Tiết kiệm & Rào chắn Ngân sách Cứng (Economy First & Hard Cost Gate)**:
  * Mặc định mọi biến thể thử nghiệm sử dụng chế độ ECONOMY (Chi phí API Video = 0 VND).
  * Nâng cấp lên HYBRID chỉ được đề xuất khi sản phẩm có bằng chứng doanh thu thực tế (`REVENUE_WINNER`).
  * Hạn mức chi phí tối đa cho một thử nghiệm được khống chế bởi `max_cost_per_experiment` (mặc định: 60.000 VND). Nếu chi phí ước tính vượt hạn mức, hệ thống tự động chặn phê duyệt thường và yêu cầu bật cờ `Admin Override`.
* **Giãn cách Đề xuất & Chống Trùng lặp (Cooldown & Deduplication Gate)**:
  * Sau khi sinh khuyến nghị cho một sản phẩm/bài đăng, hệ thống áp dụng thời gian giãn cách `recommendation_cooldown_hours` (mặc định: 24 giờ).
  * Trong thời gian cooldown, hệ thống tuyệt đối không sinh thêm khuyến nghị trùng lặp cho cùng một sản phẩm/biến số.
* **Cổng Kích thước Mẫu Đánh giá Thử nghiệm (Experiment Sample Size Gate)**:
  * Tiến trình đánh giá đối soát (`evaluateExperiment`) chỉ đưa ra kết luận người chiến thắng khi bài đăng biến thể đạt đủ kích thước mẫu tối thiểu:
    * `experiment_min_sessions >= 30`
    * `experiment_min_clicks >= 10`
  * Nếu chưa đủ mẫu, kết luận bắt buộc là `INSUFFICIENT_DATA` và thử nghiệm tiếp tục duy trì trạng thái `RUNNING`.
* **Bộ Quy tắc Đánh giá Kết luận Thử nghiệm (Experiment Conclusion Rules)**:
  * `VARIATION_BETTER`: Biến thể mới vượt trội về doanh thu hoa hồng (`varCommission > baseCommission` kèm `varConversions >= baseConversions`) hoặc CTR cao hơn rõ rệt ($\Delta \text{CTR} \ge 2.0\%$).
  * `BASELINE_BETTER`: Bài đăng gốc tạo ra doanh thu tốt hơn hoặc CTR vượt trội so với biến thể mới ($\Delta \text{CTR} \le -2.0\%$).
  * `NO_MEANINGFUL_DIFFERENCE`: Hai phiên bản có hiệu suất tương đương nhau trong phạm vi sai số $(-2.0\% < \Delta \text{CTR} < 2.0\%$).
* **Chống Vòng lặp Vô hạn (No Infinite Loop Protection)**:
  * Khi thử nghiệm A/B hoàn tất (`COMPLETED`), hệ thống ghi nhận kết luận và snapshot đối soát.
  * Hệ thống KHÔNG tự động kích hoạt thử nghiệm tiếp theo.
  * Mỗi sản phẩm bị giới hạn tối đa `max_active_experiments_per_product` (mặc định: 2) thử nghiệm đang chạy đồng thời.

