# KIẾN TRÚC HỆ THỐNG FITNADO (ARCHITECTURE.MD)

Tài liệu này được trích xuất và phân tích trực tiếp từ **Source Code Thật** của dự án `masterpdo`.

---

## 1. VÒNG ĐỜI REQUEST (REQUEST LIFECYCLE)

### Luồng xử lý Frontend (`/index.php`)

```text
[1] Client HTTP Request (Browser)
      │
      ▼
[2] index.php (Entry Point)
      ├── Khởi tạo Session & Định nghĩa hằng số: LIBRARIES, SOURCES, LAYOUT, THUMBS, WATERMARK
      ├── Load config: libraries/config.php & libraries/constant.php
      ├── AutoLoad: libraries/autoload.php
      ├── Khởi tạo Core Services:
      │     ├── AntiSQLInjection ($injection)
      │     ├── PDODb ($d)
      │     ├── Flash ($flash)
      │     ├── Seo ($seo)
      │     ├── AltoRouter ($router)
      │     ├── Cache ($cache)
      │     ├── Functions ($func)
      │     ├── BreadCrumbs ($breadcr)
      │     ├── MobileDetect ($detect)
      │     ├── CssMinify ($css) & JsMinify ($js)
      │
      ▼
[3] Routing Layer (libraries/router.php)
      ├── Validate URL: $func->checkUrl() & checkRedirect()
      ├── Mobile/Desktop Detect: định nghĩa hằng TEMPLATE ('./templates/')
      ├── Định nghĩa AltoRouter routes: '', 'index.php', 'sitemap.xml', '[a:com]', '[a:com]/[a:lang]/', 'thumbs/...'
      ├── Router Match ($match) & Phân giải $com, $getPage
      ├── Nạp cấu hình chung Setting ($setting, $optsetting) từ cache/database
      ├── Xác định Ngôn ngữ ($lang = 'vi'/'en') & SEO Lang ($seolang)
      ├── Tối ưu liên kết Friendly URLs qua mảng `$requick`:
      │     Tra cứu bảng tương ứng (`table_product*`, `table_news*`, `table_tags`...) dựa trên `$sluglang`
      │     Xác định id, gán `$_GET['id']`, `$_GET['idl']`, `$_GET['idc']`...
      │
      ▼
[4] Source Execution (Controller Layer)
      ├── Nạp dữ liệu toàn trang: sources/allpage.php (logo, favicon, footer, tags, menus, counter)
      ├── Nạp Source chuyên biệt: sources/{$source}.php (ví dụ: sources/index.php, sources/product.php...)
      │     Thực thi truy vấn Database qua `$d` / `$cache`, tính toán SEO meta, gán biến cho Template
      │
      ▼
[5] Template Rendering (View Layer)
      └── Nạp templates/index.php
            ├── templates/layout/head.php & css.php
            ├── templates/layout/seo.php, header.php, menu.php
            ├── templates/layout/slide.php (nếu homepage) hoặc breadcrumb.php
            ├── Nội dung chính: templates/{$template}_tpl.php
            └── templates/layout/footer.php, modal.php, js.php, phone.php
```

---

## 2. KIẾN TRÚC ADMIN (`/admin/`)

### Luồng xử lý Admin (`admin/index.php`)

```text
[1] Request to /admin/
      │
      ▼
[2] admin/index.php
      ├── Load config & Core classes (PDODb, Flash, Seo, Cache, Functions)
      ├── Load Admin Language (libraries/lang/admin/vi.php)
      ├── Load Type Configuration (libraries/config-type.php)
      ├── Tra cứu Setting chung
      ├── Nạp libraries/requick.php:
      │     ├── Nhận tham số: $com, $act, $type, $kind, $val, $id_parent, $id, $curPage
      │     ├── Kiểm tra Single Sign-on / Login session token (#_user)
      │     ├── Kiểm tra Phân quyền Admin (#_permission, #_permission_group)
      │     ├── Kiểm tra Authentication ($func->checkLoginAdmin())
      │     ├── Xóa Cache tự động khi thực hiện các hành động: save*, update, delete*
      │     └── Include admin/sources/{$com}.php (ví dụ: admin/sources/product.php)
      │
      ▼
[3] Admin Template Render
      ├── Nếu chưa login: render admin/templates/user/login_tpl.php
      └── Nếu đã login: render Layout Wrapper:
            ├── admin/templates/layout/header.php & menu.php
            ├── Content: admin/templates/{$template}_tpl.php (ví dụ: product/man/items_tpl.php)
            └── admin/templates/layout/footer.php & js.php
```

---

## 3. LỚP TRUY VẤN DỮ LIỆU (DATABASE ACCESS LAYER)

Dự án sử dụng class wrapper **`PDODb`** (`libraries/class/class.PDODb.php`) kết hợp PDO:

* **Prefix tự động**: Mặc định là `table_` (hoặc cấu hình trong `config['database']['prefix']`). Trong câu query raw có thể dùng `#_` để tự động thay thế bằng prefix.
* **Prepared Statements & Raw Queries**:
  * `$d->rawQuery($sql, $params)`: Trả về mảng danh sách bản ghi.
  * `$d->rawQueryOne($sql, $params)`: Trả về 1 bản ghi duy nhất.
  * `$d->rawQueryValue($sql, $params)`: Lấy giá trị của 1 cột.
* **Query Builder Helpers**:
  * `$d->where($column, $value, $operator)`: Điều kiện WHERE.
  * `$d->orderBy($column, $direction)`: Sắp xếp.
  * `$d->get($table, $limit, $columns)` / `$d->getOne($table, $columns)`.
  * `$d->insert($table, $data)`: Thêm mới bản ghi, trả về lastInsertId.
  * `$d->update($table, $data)`: Cập nhật bản ghi theo điều kiện WHERE.
  * `$d->delete($table)`: Xóa bản ghi theo điều kiện WHERE.
* **Cache Helper (`Cache` class)**:
  * `$cache->get($sql, $params, $type = 'result'|'fetch', $time = 7200)`: Tự động lưu và đọc kết quả query từ file cache trong thư mục `caches/`.
  * `$cache->delete()`: Xóa toàn bộ file cache khi có thay đổi dữ liệu từ Admin.

---

## 4. HỆ THỐNG CẤU HÌNH (CONFIGURATION ARCHITECTURE)

Hệ thống cấu hình chia làm 2 lớp:
1. **Cấu hình chung (`libraries/config.php`)**:
   * Database credentials (`host`, `username`, `password`, `dbname`, `prefix`, `charset`).
   * Website debug mode, secret salt, login token names.
   * Ngôn ngữ hỗ trợ (`vi`, `en`), slug keys, video config.
2. **Cấu hình động Type (`libraries/config-type.php` & `libraries/type/`)**:
   * `config-type-product.php`: Quản lý cấp danh mục sản phẩm (List, Cat, Item, Sub, Brand), thư viện ảnh Gallery, giá bán (`regular_price`, `sale_price`, `discount`), thông số kỹ thuật, màu sắc (`color`), kích thước (`size`), đánh giá comment.
   * `config-type-news.php`: Quản lý các loại bài viết (`tin-tuc`, `tuyen-dung`, `chinh-sach`, `hinh-thuc-thanh-toan`), phân cấp danh mục, gallery, schema, SEO.
   * `config-type-photo.php`: Quản lý ảnh tĩnh & đa ảnh (`logo`, `favicon`, `banner`, `slide`, `social`, `video`, `doitac`, `popup`, `watermark`).
   * `config-type-static.php`: Quản lý trang tĩnh một bài duy nhất (`gioi-thieu`, `slogan`, `copyright`, `lienhe`, `footer`).

---

## 5. XỬ LÝ HÌNH ẢNH & MEDIA (IMAGE & UPLOAD ARCHITECTURE)

* **Upload Directory**: `upload/` chứa các thư mục con theo module (`upload/product/`, `upload/news/`, `upload/photo/`, `upload/static/`, `upload/filemanager/`...).
* **On-the-fly Image Resizing (`thumbs/`)**:
  * Route: `thumbs/[w]x[h]x[z]/[src]`
  * Khi client gọi URL ảnh dạng `thumbs/540x540x1/upload/product/abc.jpg`, router chuyển tiếp đến hàm `createThumb()` của `Functions` class để xử lý crop, nén WebP/JPG và lưu file cache trong thư mục `thumbs/`.
* **Watermark Engine**: Route `watermark/product/...` hoặc `watermark/news/...` tự động đóng dấu logo watermark dựa trên cấu hình trong database `#_photo`.

---

## 6. HỆ THỐNG BÌNH LUẬN & ĐÁNH GIÁ (COMMENTS & REVIEWS)

* Được đóng gói trong class **`Comments`** (`libraries/class/class.Comments.php`).
* Hỗ trợ phân cấp cha/con (Reply), đánh giá số sao (1-5 sao), đính kèm album hình ảnh (`table_comment_photo`) và video thực tế (`table_comment_video`).
* Quản lý trạng thái duyệt `hienthi` và kiểm duyệt nội dung trực tiếp trong Admin.

---

## 7. HỆ THỐNG NGHIÊN CỨU SẢN PHẨM (PRODUCT RESEARCH ARCHITECTURE - PHASE 03)

### Core Engine & Class:
* **`ProductResearch`** (`libraries/class/class.ProductResearch.php`):
  * **Scoring Engine**: Đánh giá 5 chiều (Demand, Content, Commission, Competition, SEO) với trọng số động (Default: 30/25/20/15/10) và xử lý chuẩn xác `NULL != 0`.
  * **Normalization Engine**: Hàm `normalizeUrl()` (loại bỏ toàn bộ tracking query params như `utm_*`, `spm`, `aff_*`, `fbclid`...) và `normalizeName()` (chuẩn hóa tên không dấu/lowercase).
  * **Duplicate Detection Engine**: Hàm `checkDuplicate()` phát hiện trùng lặp 3 cấp: (1) `platform + external_product_id`, (2) `normalized_url`, (3) `normalized_name + brand`.
  * **Mapping Engine**: Hàm `createProductFromCandidate()` ánh xạ ứng viên được duyệt sang `table_product` ở trạng thái nháp (`status` không có `hienthi`), khởi tạo `table_product_affiliate` và cập nhật `id_product` ngược lại candidate.

### Admin Controller & Templates:
* **Controller**: `admin/sources/product_research.php` xử lý các action:
  * `man`: Danh sách với bộ lọc đa tiêu chí, sắp xếp, tìm kiếm, phân trang và widget thống kê.
  * `add` / `edit` / `save`: Form ứng viên với visualizer phân rã điểm số và cảnh báo trùng lặp.
  * `approve` / `reject`: Chuyển đổi trạng thái quy trình kèm lý do từ chối.
  * `recalculate`: Tính toán lại điểm số tự động khi thay đổi chỉ số thị trường.
  * `create_product` / `save_product`: Màn hình ánh xạ sang danh mục/thương hiệu thật của website.
  * `weights` / `save_weights`: Cấu hình trọng số chấm điểm với validation tổng 100%.
* **Views**: `admin/templates/product_research/` (`mans_tpl.php`, `man_add_tpl.php`, `create_product_tpl.php`, `weights_tpl.php`).

---

## 8. HỆ THỐNG TỰ ĐỘNG HÓA NGHIÊN CỨU & AI RESEARCH AGENT (PHASE 04)

### Cấu trúc đa tầng (Multi-Layer Discovery Engine):

```text
[Discovery Seed]  ──▶  [Job Queue]  ──▶  [Background Worker]  ──▶  [Research Providers]
                                                                           │
                                                                           ▼
                                                                  [AI Research Agent]
                                                                           │
                                                                           ▼
                                                                [Candidate Normalizer]
                                                                           │
                                                                           ▼
                                                                  [Duplicate Check]
                                                            (Match? Update last_seen + Snapshot)
                                                                           │ (No match)
                                                                           ▼
                                                                [Scoring Engine (Ph 03)]
                                                                           │
                                                                           ▼
                                                              [Evidence Provenance Logger]
                                                                           │
                                                                           ▼
                                                                 [table_product_research]
                                                                (Status = 'RESEARCHED')
                                                                           │
                                                                           ▼
                                                                   [STRICT HUMAN GATE]
                                                                (Admin Reviews & Approves)
```

### Thành phần lớp nghiệp vụ:
1. **`ResearchCandidateDTO`** (`libraries/class/class.ResearchProvider.php`):
   * DTO chuẩn hóa thống nhất toàn bộ thuộc tính sản phẩm và mảng `evidence` trước khi lưu vào DB.
2. **`ResearchProviderInterface` & `ResearchProviderFactory`** (`libraries/class/class.ResearchProvider.php`):
   * `AiResearchProvider`: Gọi `AIResearchAgent` để khám phá và phân tích tiềm năng từ từ khóa seed.
   * `MockPlatformProvider`: Trình giả lập crawler đa nền tảng (`tiktok`, `shopee`, `lazada`) trả về DTO và Fact Evidence.
   * `CsvProvider`: Nạp tập tin CSV danh sách sản phẩm.
   * `ManualProvider`: Hỗ trợ nhập thủ công.
3. **`AIResearchAgent`** (`libraries/class/class.AIResearchAgent.php`):
   * Hỗ trợ đa LLM (Google Gemini 1.5 Flash, OpenAI-compatible, MockAIProvider fallback).
   * Phiên bản prompt chuẩn hóa (`research-v1.0`) kèm system instruction và structured JSON schema validation.
   * Cơ chế tự động retry tối đa 3 lần và tuân thủ hạn mức gọi API ngày (Daily Rate Limit).
4. **`ResearchJobQueue`** (`libraries/class/class.ResearchJobQueue.php`):
   * Quản lý trạng thái hàng đợi `table_product_research_job` (`PENDING`, `RUNNING`, `SUCCESS`, `FAILED`).
   * Khóa xử lý đồng thời (Concurrency lock) và tự động phục hồi job bị treo quá 10 phút.
   * Tích hợp trọn gói: Thu thập -> Phân tích AI -> Kiểm tra trùng lặp -> Chấm điểm 5 chiều -> Lưu Evidence & Snapshot.
5. **`product_research_worker.php`** (`cron/product_research_worker.php`):
   * CLI worker chạy định kỳ qua Cronjob hoặc bảo vệ bằng secret token khi trigger qua Webhook/HTTP.
6. **Admin Dashboard mở rộng** (`admin/sources/product_research.php`):
   * Quản lý hạt giống (`seeds`, `seed_add`, `seed_edit`, `seed_save`, `seed_run_now`).
   * Giám sát hàng đợi (`jobs`, `job_retry`, `job_delete`).
   * Cấu hình nhà cung cấp AI & API Keys (`provider_config`, `save_provider_config`).
   * Hiển thị chi tiết bảng Evidence và thẻ AI Insights Visualizer trên form Candidate.

---

## 9. KIẾN TRÚC AI CONTENT ENGINE & CONTENT LIFECYCLE (PHASE 05)

### Luồng xử lý sinh nội dung (Content Generation & Review Flow):

```text
[table_product] + [table_product_research] + [table_product_research_evidence]
                                    │
                                    ▼
                          [Input Data Aggregator]
                       (Calculate SHA-256 source_hash)
                                    │
                                    ▼
                        [AI Content Job Queue]
                       (table_ai_content_job)
                                    │
                                    ▼
                         [AI Content Worker]
                      (cron/ai_content_worker.php)
                                    │
                                    ▼
                         [AI Provider Service]
                      (Gemini / OpenAI / Mock)
                                    │
                                    ▼
                         [Quality Gate Filter]
                   (validateQualityGate & Factual Rules)
                                    │
                                    ▼
                          [table_ai_content]
                      (Status: REVIEW_REQUIRED)
                                    │
                                    ▼
                          [HUMAN ADMIN GATE]
                   (Review / Edit / Approve / Reject)
                                    │ (When Approved)
                                    ▼
                       [Side-by-Side Diff Apply]
                                    ├── Step 1: Backup current product fields to table_product_content_backup
                                    └── Step 2: Overwrite table_product with approved AI content
```

### Thành phần lớp nghiệp vụ Phase 05:
1. **`AIContentEngine`** (`libraries/class/class.AIContentEngine.php`):
   * Tổng hợp dữ liệu đầu vào sản phẩm & trích xuất các facts từ `table_product_research_evidence` (`buildProductInputContext`).
   * Tính toán chữ ký dữ liệu `source_hash` để phát hiện nội dung bị lỗi thời khi thông tin sản phẩm thay đổi (`computeSourceHash`, `isContentOutdated`).
   * Quản lý các mẫu prompt có phiên bản (`PROMPTS` registry: `all-pack-v1`, `tiktok-script-v1`, `seo-pack-v1`, `review-draft-v1`).
   * Bộ kiểm tra cổng chất lượng (`validateQualityGate`): Kiểm tra độ dài, cấu trúc, phát hiện từ khóa cấm, cam kết y tế sai lệch, và claims cá nhân giả mạo.
   * Quản lý vòng đời phiên bản: Tự động tăng `version` khi sinh lại và đánh dấu `is_current = 1`.
   * Áp dụng có thể hoàn nguyên (`applyToProduct`): Tự động sao lưu dữ liệu cũ vào `table_product_content_backup` trước khi cập nhật `table_product`.
2. **`AIContentJobQueue`** (`libraries/class/class.AIContentJobQueue.php`):
   * Quản lý hàng đợi tác vụ sinh nội dung AI đơn lẻ hoặc hàng loạt (`table_ai_content_job`).
   * Cơ chế khóa concurrency lock (`worker_ai_content_lock`), xử lý timeout và tự động retry tác vụ thất bại.
3. **`ai_content_worker.php`** (`cron/ai_content_worker.php`):
   * Background CLI worker xử lý job theo lô (mặc định 5 jobs/lần) hoặc kích hoạt qua HTTP với access token bí mật.
4. **Cầu nối Kịch bản TikTok & Shot Plan (Bridge to Phase 06)**:
   * Kịch bản TikTok được cấu trúc mảng phân cảnh (`shot_plan`): bao gồm `scene_number`, `duration`, `visual_instruction`, `voiceover`, `on_screen_text`, `asset_requirement`.
   * Cung cấp dữ liệu đầu vào sẵn sàng cho việc dựng video và render AI cảnh quay trong Phase 06.
5. **Giao diện Quản trị AI Content (`admin/sources/ai_content.php`)**:
   * Thư viện nội dung (`mans_tpl.php`): Lọc theo sản phẩm, loại nội dung, trạng thái và cảnh báo outdated.
   * Chi tiết nội dung (`view_tpl.php`): Hiển thị trực quan 7 Hooks chiến lược, kịch bản phân cảnh, bảng Shot Plan, SEO meta, bài review và kết quả Quality Gate.
   * Đối soát & Áp dụng (`diff_apply_tpl.php`): Trình so sánh Diff trực quan song song (Side-by-Side Diff) giữa nội dung hiện tại của sản phẩm và nội dung AI đã duyệt.
   * Giám sát Job (`jobs_tpl.php`) & Cấu hình Prompts (`settings_tpl.php`).

---

## 10. KIẾN TRÚC AI VIDEO PRODUCTION ENGINE & LIFECYCLE (PHASE 06)

### Luồng sản xuất Video hoàn chỉnh (Video Lifecycle Flow):

```text
APPROVED TIKTOK SCRIPT + SHOT PLAN (table_ai_content)
                         │
                         ▼
        [VIDEO PROJECT MANAGER] (table_ai_video)
        (Hooks, Scene Setup, 9:16 Aspect Ratio)
                         │
                         ▼
             [ASSET RESOLUTION PIPELINE]
  (Reuse table_product.photo, table_gallery, Uploads)
     ├── All Assets Ready ──▶ [READY]
     └── Missing Assets   ──▶ [WAITING_ASSET]
                         │
                         ▼
         [VIDEO JOB QUEUE] (table_ai_video_job)
        (Concurrency Lock, Timeout, Stale Recovery)
                         │
                         ▼
          [VIDEO BACKGROUND WORKER]
          (cron/video_render_worker.php)
                         │
                         ▼
          [VIDEO PROVIDER ABSTRACTION]
     ├── MockVideoProvider (Internal Simulation)
     ├── ExternalVideoProvider (Creatify / Arcads / HeyGen)
     └── ManualVideoProvider (Admin DIY Editor)
                         │
                         ▼
         [DOWNLOAD & OUTPUT STORAGE]
     (SSRF Protection, Safe Filename, /upload/video/)
                         │
                         ▼
           [MEDIA VALIDATION & QC]
  (Zero-byte Check, MIME Type, Duration, Safe Area Check)
                         │
                         ▼
          [STRICT HUMAN APPROVAL GATE]
             (Status: REVIEW_REQUIRED)
     ├── Admin HTML5 <video> Preview
     ├── Review Notes / Reject Reason
     └── APPROVED -> READY_FOR_PUBLISHING (Phase 07)
```

### Thành phần lớp nghiệp vụ Phase 06:
1. **`AIVideoEngine`** (`libraries/class/class.AIVideoEngine.php`):
   * Khởi tạo dự án video từ kịch bản TikTok đã duyệt trong `table_ai_content` (`createProjectFromApprovedContent`).
   * Tự động ánh xạ tài nguyên cho từng Scene từ ảnh sản phẩm và gallery (`resolveProjectAssets`).
   * Kiểm tra tính toàn vẹn và phát hiện kịch bản gốc bị sửa đổi (`computeScriptHash`, `checkVideoOutdated`).
   * Kiểm định chất lượng media video sau render (`validateRenderedMedia`): kiểm tra kích thước file, định dạng MP4, độ dài và tỷ lệ khung hình 9:16.
   * Xử lý phê duyệt (`approveVideo`) và từ chối (`rejectVideo`) của Admin con người.
2. **`VideoProviderInterface` & `VideoProviderFactory`** (`libraries/class/class.VideoProvider.php`):
   * Lớp trừu tượng hóa cho mọi nhà cung cấp video (Mock, Creatify, Arcads, HeyGen, Manual).
   * `MockVideoProvider`: Giả lập render cục bộ, tạo file MP4 mẫu và thumbnail hợp lệ để test offline.
   * `ExternalVideoProvider`: Kết nối REST API video thương mại ngoài kèm bảo mật API keys và SSRF download guard.
3. **`AIVideoJobQueue`** (`libraries/class/class.AIVideoJobQueue.php`):
   * Quản lý hàng đợi tác vụ render `table_ai_video_job`.
   * Khóa concurrency lock, cơ chế polling bất đồng bộ (`next_poll_at`), tự động phục hồi job treo quá 10 phút và retry lỗi mạng.
4. **`video_render_worker.php`** (`cron/video_render_worker.php`):
   * Background CLI / HTTP Token worker xử lý render video theo lô.
5. **Giao diện Quản trị AI Video (`admin/sources/ai_video.php`)**:
   * Quản lý dự án (`mans_tpl.php`), xem chi tiết & preview HTML5 `<video controls>` (`view_tpl.php`).
   * Tạo dự án mới (`create_tpl.php`), giám sát hàng đợi (`jobs_tpl.php`), quản lý kho tài nguyên (`assets_tpl.php`) và cấu hình API / hạn mức render (`settings_tpl.php`).

---

## 11. KIẾN TRÚC LOW-COST HYBRID VIDEO COMPOSER (PHASE 06.2)

### Luồng xử lý Video Composer (Low-Cost Hybrid Pipeline):

```text
APPROVED SCRIPT + SHOT PLAN (Phase 05)
                │
                ▼
      [PURPOSE-DRIVEN FLOW]
  (0-3s HOOK -> PROBLEM -> INTRO -> DEMO -> BENEFIT -> LIMITATION -> CTA)
                │
                ▼
     [SELECT PRODUCTION MODE]
  ├── ECONOMY (Default: 0 VND API Cost - 100% Local Motion)
  ├── HYBRID (Max 1-2 AI Scenes - Motion Highlights)
  └── PREMIUM (High Investment Multi-scene AI)
                │
                ▼
     [COST ESTIMATOR & GUARD]
  (Estimate AI Sec & Cost -> Block if > max_ai_video_cost_per_video)
                │
                ▼
        [VIDEO COMPOSER]
  ├── Local Scene Renderer (Ken Burns zoom_in/out, pan, push, fade, slide)
  ├── Optional AI Scene Collector (Beeknoee Veo 3.1 - only if AI_VIDEO)
  ├── Voiceover Mixer (TTS Audio Track)
  ├── Safe-Area Captions Overlay (Vietnamese Unicode)
  └── Branding & CTA Watermark (FITNADO)
                │
                ▼
  [FINAL COMPOSED 9:16 MP4] (1080x1920 H.264 / AAC)
                │
                ▼
   [MEDIA QC & COST BREAKDOWN LOG]
  (local_render_cost, ai_video_cost, tts_cost, total_api_cost)
                │
                ▼
  [STRICT HUMAN APPROVAL GATE] (Admin Preview -> Approve / Reject)
```

### Thành phần lớp nghiệp vụ Phase 06.2:
1. **`VideoComposer`** (`libraries/class/class.VideoComposer.php`):
   * Quản lý 3 modes: `ECONOMY`, `HYBRID`, `PREMIUM`.
   * Khởi tạo và kiểm tra hạ tầng `auditFFmpeg()`.
   * Ước tính chi phí trước render `estimateCost()` và áp dụng rào chắn ngân sách `validateCostGuard()`.
   * Chuẩn hóa cấu trúc phân cảnh `normalizeScenes()` với quy tắc 0–3s bắt buộc là `HOOK`.
   * Xử lý kết xuất toàn diện `composeVideo()`: ghép cảnh, hiệu ứng chuyển động, lồng tiếng, phụ đề safe-area và xuất file MP4.
2. **`BeeknoeeVideoProvider`** (`libraries/class/class.VideoProvider.php`):
   * Định vị lại thành **Optional AI Scene Generator**, chỉ được kích hoạt khi phân cảnh cụ thể yêu cầu `render_method = 'AI_VIDEO'`.

---

## 12. KIẾN TRÚC PUBLISHING CENTER & TIKTOK PUBLISHING FOUNDATION (PHASE 07)

### Luồng Quản lý Xuất bản Đa Kênh (Publishing Lifecycle Flow):

```text
APPROVED VIDEO (table_ai_video, status = 'APPROVED')
                    │
                    ▼
          [STRICT HUMAN GATE]
         (Chặn video DRAFT/REJECTED)
                    │
                    ▼
     [POST PACKAGE INITIALIZATION] (table_publish_post)
  ├── Video MP4 + Thumbnail + Duration
  ├── Product Info + Specifications + Fitnado Score
  ├── Caption + 7-Hook Content Snapshot
  ├── Hashtags Snapshot (#fitnado #daicung #reviewgym)
  ├── Affiliate Destination + Disclosure Text
  └── Platform Target (TikTok / Facebook / Instagram / YouTube / Web)
                    │
                    ▼
       [PRE-PUBLISH CHECKLIST GATE]
  ├── 1. Video Approved & MP4 Valid (>1KB)
  ├── 2. Product Exists & Active
  ├── 3. Caption Non-empty
  ├── 4. Platform & Channel Selected
  └── 5. Fact Evidence Traceability Confirmed
                    │ (Pass Checklist)
                    ▼
      [IMMUTABLE SNAPSHOT ON READY]
  (Đóng băng snapshot_data JSON - Chống biến động lịch sử)
                    │
         ┌──────────┴──────────┐
         ▼                     ▼
    [PUBLISH NOW]       [SCHEDULE QUEUE]
         │              (table_publish_post.scheduled_at)
         │                     │
         │                     ▼
         │            [PUBLISH JOB QUEUE & WORKER]
         │            (cron/publish_worker.php - Idempotency Lock)
         │                     │ (When Due: scheduled_at <= now)
         └──────────┬──────────┘
                    │
                    ▼
      [PUBLISH PROVIDER FACTORY]
         ├── ManualPublishProvider (100% Production Usable Fallback)
         │     ├── Download Video MP4
         │     ├── 1-Click Copy Caption
         │     ├── 1-Click Copy Hashtags
         │     ├── Open TikTok Creator Center
         │     └── Mark as Published (Validate URL & Extract Post ID)
         │
         └── TikTokPublishProvider (API Foundation)
               ├── Status: NOT CONFIGURED (Báo cáo trung thực khi chưa có Secret)
               └── Content Posting API Ready (Khi có Authorization thật)
                    │
                    ▼
         [PUBLISHED / POST ID / URL]
  (table_publish_post.status = 'PUBLISHED', published_at, external_post_url)
                    │
                    ▼
         [PUBLISH AUDIT LOG TRAIL]
  (table_publish_log: CREATED, EDIT, READY, SCHEDULED, PUBLISHED, RETRIED)
```

### Thành phần lớp nghiệp vụ Phase 07:
1. **`PublishingCenter`** (`libraries/class/class.PublishingCenter.php`):
   * Quản lý toàn diện vòng đời Post Package (`createPostFromApprovedVideo`, `updatePost`, `markPostReady`, `schedulePost`, `publishNow`, `markManualPublished`, `duplicatePost`).
   * Rào chắn Human Gate: Chỉ video có `status = 'APPROVED'` mới được khởi tạo bài đăng.
   * Rào chắn Outdated Gate: Tự động cảnh báo khi dữ liệu video hoặc sản phẩm gốc thay đổi.
   * Rào chắn Immutability: Đóng băng toàn bộ nội dung thành `snapshot_data` khi chuyển sang `READY`.
   * Rào chắn Phục hồi DRAFT: Tự động hoàn nguyên về `DRAFT` nếu bài đăng `READY` bị chỉnh sửa.
   * Rào chắn Toàn vẹn Lịch sử: Không cho phép sửa trực tiếp bài đã `PUBLISHED` (bắt buộc `duplicatePost`).
   * Khóa Idempotency (`publish_lock`): Ngăn chặn đăng trùng lặp khi có 2 request gửi đồng thời.
2. **`PublishProviderInterface`, `ManualPublishProvider`, `TikTokPublishProvider`** (`libraries/class/class.PublishProvider.php`):
   * Lớp trừu tượng hóa cho các kênh và phương thức xuất bản.
   * `ManualPublishProvider`: Động cơ xuất bản thủ công hoàn chỉnh, xác thực URL bài đăng TikTok (chống URL độc hại, kiểm tra domain hợp lệ, trích xuất Video ID).
   * `TikTokPublishProvider`: Cung cấp nền tảng API, báo cáo trung thực trạng thái `NOT CONFIGURED` nếu chưa có OAuth credentials.
3. **`PublishJobQueue`** (`libraries/class/class.PublishJobQueue.php`):
   * Quản lý hàng đợi tác vụ lên lịch xuất bản `table_publish_post`.
   * Tự động phục hồi khóa bị treo quá 10 phút (`recoverStaleLocks`).
   * Cơ chế thử lại bài lỗi (`retryFailedPost`).
4. **`publish_worker.php`** (`cron/publish_worker.php`):
   * Background CLI / Token-protected HTTP worker xử lý quét bài đăng đến hạn theo lô.
5. **Giao diện Quản trị Publishing Center (`admin/sources/publishing.php`)**:

---

## 13. KIẾN TRÚC ANALYTICS, AFFILIATE ATTRIBUTION & WINNER DETECTION (PHASE 08)

### Sơ đồ Vòng đời Đo lường & Phát hiện Winner (Closed Loop Measurement):

```text
       [PUBLISHED POST] (Phase 07)
   (Unique tracking_code: fp_tikt_99_25b4470f)
              │
              ▼
   [TIKTOK / SOCIAL TRAFFIC]
   (Click Landing URL: /product-slug?ref=fp_tikt_99_...&utm_source=tiktok...)
              │
              ▼
   [FITNADO WEB VISIT / SOURCES/ALLPAGE.PHP]
   ├── 1. Parse & Resolve Landing Attribution (AnalyticsService::resolveLandingAttribution)
   ├── 2. Persist Anonymous Session & Cookie Attribution Window (Default: 30 days)
   └── 3. Log Normalized Event (table_analytics_event: PAGE_VIEW / PRODUCT_VIEW)
              │
              ▼
   [PRODUCT DETAIL / SOURCES/PRODUCT.PHP]
   ├── Hiển thị bài đánh giá chuyên sâu + Ưu đãi tiếp thị
   └── Log Normalized Event (table_analytics_event: PRODUCT_VIEW)
              │
              ▼
   [AFFILIATE CTA CLICK / SOURCES/AFFILIATE.PHP]
   ├── 1. Retrieve Active Session Attribution Context
   ├── 2. Record Transactional Log (table_affiliate_click with tracking_code, id_post, id_video, id_content)
   ├── 3. Project Normalized Event (table_analytics_event: AFFILIATE_CLICK)
   └── 4. 302 Instant Redirect to Shopee / TikTok Shop / Brand Store with Sub-Tracking Parameter
              │
              ▼
   [COMMERCE PLATFORM / CONVERSION EVENT]
   (Shopee / TikTok Shop / Lazada ghi nhận đơn hàng phát sinh)
              │
              ▼
   [CONVERSION DATA INGESTION]
   ├── Method A: Periodic CSV Report Upload (Shopee / TikTok Affiliate CSV)
   │     ├── ConversionImporter::previewCsv (Kiểm tra định dạng, xem trước dữ liệu)
   │     ├── Duplicate Protection (Tránh tính trùng doanh thu)
   │     ├── Tracking Identity Resolution (Khớp đơn hàng qua sub_id tracking_code)
   │     └── Reversal / Refund Handling (Cập nhật trạng thái REVERSED)
   │
   └── Method B: Manual Attribution Match (Admin gán nguồn cho đơn chưa khớp sub_id)
              │
              ▼
   [CLOSED-LOOP AGGREGATION & ATTRIBUTION ENGINE]
   (AnalyticsService::getOverviewMetrics, getProductMetrics, getPostMetrics, getVideoMetrics, getContentHookMetrics)
   ├── Last Eligible Content Touch Model
   ├── Direct Traffic Isolation (Không gán nhầm cho TikTok)
   ├── Multiple Posts Isolation (Phân định rạch ròi giữa các video cùng sản phẩm)
   ├── Safe Math & Zero Division Guard (CTR, CVR, EPC, ROI luôn an toàn khi mẫu = 0)
   ├── Multi-Currency Isolation (Tách biệt VND và USD)
   └── Internal Traffic Filtering (Lọc IP Admin / Dev khỏi thống kê)
              │
              ▼
   [WINNER DETECTION ENGINE] (WinnerDetectionEngine)
   ├── 1. Sample Size Gate (min_landing_sessions >= 30, min_affiliate_clicks >= 10)
   │     └── Nếu chưa đủ mẫu: Giữ trạng thái INSUFFICIENT_DATA (Chống kết luận sớm)
   │
   ├── 2. Signal Level Hierarchy:
   │     REVENUE (Doanh thu > Chi phí) > CONVERSION (Có đơn) > CLICK (CTR cao) > TRAFFIC > NONE
   │
   ├── 3. Performance Status Evaluation:
   │     ├── WINNER (CTR >= 10% + CVR >= 5% hoặc High ROI)
   │     ├── PROMISING (CTR >= 5%, Ý định mua cao)
   │     ├── TESTING (Đang kiểm thử, tiệm cận ngưỡng)
   │     └── UNDERPERFORMING (CTR < 1% sau thời gian tối thiểu)
   │
   ├── 4. Actionable Next-Step Recommendations (Không tự ý chi tiền / tự ý đăng bài):
   │     ├── UPGRADE_TO_HYBRID: Nâng cấp sản xuất video HYBRID cho sản phẩm Thắng
   │     ├── CREATE_VARIATION: Tạo thêm biến thể kịch bản Hook cho sản phẩm Tiềm năng
   │     ├── CREATE_NEW_HOOK: Đổi Hook mở đầu cho nội dung kém hiệu quả
   │     └── KEEP_TESTING: Tiếp tục theo dõi đủ độ lớn mẫu
   │
   └── 5. Frozen Evaluation Snapshot (table_winner_evaluation: Metrics + Rules + Recommendation)
```

### Thành phần Lớp Dịch vụ Phase 08:
1. **`AnalyticsService`** (`libraries/class/class.AnalyticsService.php`):
   * Định danh và ghi nhận sự kiện chuẩn hóa vào `table_analytics_event`.
   * Phân giải nguồn chuyển tiếp `resolveLandingAttribution` theo mô hình Last Eligible Content Touch và thời gian Attribution Window cấu hình được.
   * Làm giàu dữ liệu click chuyển tiếp `recordAffiliateClick` với đầy đủ `id_post`, `id_video`, `id_content`, `tracking_code`.
   * Tổng hợp báo cáo đa chiều: Tổng quan (`getOverviewMetrics`), Sản phẩm (`getProductMetrics`), Bài đăng (`getPostMetrics`), Video (`getVideoMetrics`), Nội dung & Hook (`getContentHookMetrics`).
   * Phân tách độc lập điểm nghiên cứu thị trường (Research Score) và chỉ số hiệu suất quan sát thực tế (Observed Performance Metrics).
2. **`WinnerDetectionEngine`** (`libraries/class/class.WinnerDetectionEngine.php`):
   * Động cơ đánh giá dựa trên tập quy tắc minh bạch (Rule-Based Evaluation).
   * Rào chắn kiểm soát kích thước mẫu nghiêm ngặt (Strict Sample Size Gating).
   * Phân cấp tín hiệu hiệu suất và sinh snapshot lịch sử đóng băng.
3. **`ConversionImporter`** (`libraries/class/class.ConversionImporter.php`):
   * Phân tích và nạp tệp CSV đối soát từ các sàn TMĐT.
   * Cơ chế phòng chống trùng lặp dữ liệu (Idempotent Ingestion).
   * Hỗ trợ xử lý hoàn trả/hủy đơn và gán nguồn thủ công có vết kiểm toán (Audit Trail).
4. **Bộ điều khiển & Giao diện Quản trị Analytics (`admin/sources/analytics.php` & `admin/templates/analytics/`)**:
   * Tổng quan hiệu suất (`overview_tpl.php`), Bảng chỉ số sản phẩm (`products_tpl.php`), Bài đăng (`posts_tpl.php`), Video (`videos_tpl.php`), Phân tích Hook & Content (`content_tpl.php`), Danh sách đối soát đơn hàng (`conversions_tpl.php`), Hộp công cụ nhập CSV (`conversion_import_tpl.php`), Bảng điều khiển Winner Detection (`winner_detection_tpl.php`), Cấu hình quy tắc & ngưỡng (`winner_rules_tpl.php`).

---

## 14. KIẾN TRÚC DATA-DRIVEN OPTIMIZATION LOOP & A/B EXPERIMENTATION (PHASE 09)

### Sơ đồ Vòng Lặp Vận Hành Tối Ưu Hóa Khép Kín:

```text
       [PHASE 08 OBSERVED PERFORMANCE SIGNAL]
(Traffic / Click / Conversion / Revenue / 6-Level Maturity)
                         │
                         ▼
             [OPTIMIZATION ENGINE] (Rule-based Core)
  ├── 1. Cooldown & Deduplication Gate (24h Window)
  ├── 2. Machine Reason Code Generator (POSITIVE_ROI, HIGH_TRAFFIC_LOW_CTR...)
  ├── 3. Falsifiable Hypothesis Builder
  ├── 4. One-Variable Selector (HOOK / CTA / SCRIPT / VIDEO_STYLE / VOICE / OFFER)
  └── 5. Cost Estimation & Hard Cost Gate Check
                         │
                         ▼
      [OPTIMIZATION RECOMMENDATION] (table_optimization_recommendation, status = 'PENDING')
                         │
                         ▼
        [STRICT HUMAN APPROVAL GATE] (Admin Decides)
  ├── Phê duyệt thông thường (Chi phí <= 60.000 đ)
  ├── Phê duyệt kèm Admin Override (Nếu vượt hạn mức ngân sách)
  └── Từ chối kèm lý do phản hồi (REJECTED)
                         │ (Approved)
                         ▼
     [A/B EXPERIMENT INITIALIZATION] (table_optimization_experiment, status = 'RUNNING')
  ├── Đóng băng Baseline Snapshot (Sessions, Clicks, CTR, Revenue, Post ID)
  ├── Sinh Biến thể Kịch bản (Phase 05 - nếu đổi HOOK/CTA/SCRIPT)
  ├── Sinh Biến thể Video (Phase 06 - ECONOMY default / HYBRID if approved)
  └── Khởi tạo Post Package Biến thể (Phase 07) kèm Unique Tracking Code
                         │
                         ▼
    [PUBLISH & SEPARATE TRACKING ATTRIBUTION] (Phase 07 + Phase 08)
  (Visitor tương tác qua mã tracking riêng biệt: fp_tikt_<exp_id>_<hash>)
                         │
                         ▼
          [A/B EXPERIMENT EVALUATION ENGINE]
  ├── Cổng kiểm tra kích thước mẫu (Sample Gate: sessions >= 30, clicks >= 10)
  │     └── Nếu chưa đủ: Giữ trạng thái RUNNING, kết luận INSUFFICIENT_DATA
  │
  ├── Đối soát chỉ số Baseline vs Variation (CTR, CVR, Commission, Net ROI)
  └── Đưa ra kết luận chính thức:
        ├── VARIATION_BETTER: Biến thể mới vượt trội về doanh thu/CTR
        ├── BASELINE_BETTER: Nội dung gốc hiệu quả hơn
        └── NO_MEANINGFUL_DIFFERENCE: Không có sự khác biệt rõ rệt
                         │
                         ▼
  [EXPERIMENT COMPLETED] (Status = 'COMPLETED', Ghi nhận kết luận & Snapshot)
  └── Chống vòng lặp vô hạn: KHÔNG tự động kích hoạt thử nghiệm tiếp theo.
```

### Thành phần Lớp Dịch vụ Phase 09:
1. **`OptimizationEngine`** (`libraries/class/class.OptimizationEngine.php`):
   * Quản lý tạo khuyến nghị tối ưu hóa từ dữ liệu hiệu suất Phase 08.
   * Rào chắn thời gian giãn cách `hasRecentRecommendation` chống spam khuyến nghị.
   * Thực thi Hard Cost Gate và xử lý Admin Override.
   * Khởi tạo và liên kết thử nghiệm A/B, tự động phối hợp sinh kịch bản Phase 05, video Phase 06, và post Phase 07.
   * Đánh giá đối soát thử nghiệm theo cổng kích thước mẫu và đưa ra kết luận định lượng.
2. **Bộ điều khiển & Giao diện Quản trị Optimization (`admin/sources/optimization.php` & `admin/templates/optimization/`)**:
   * Danh sách khuyến nghị (`recommendations_tpl.php`), Chi tiết khuyến nghị & Cổng phê duyệt ngân sách (`recommendation_detail_tpl.php`), Danh sách thử nghiệm A/B (`experiments_tpl.php`), Màn hình đối soát trực quan Baseline vs Variation (`experiment_detail_tpl.php`), Cấu hình hạn mức ngân sách & kích thước mẫu (`rules_tpl.php`).

---

## 15. KIẾN TRÚC OPERATIONS & AUTOMATION CONTROL CENTER (PHASE 10)

### Sơ đồ Mặt Phẳng Điều Hành & Giám Sát Trung Tâm:

```text
+---------------------------------------------------------------------------------------------------+
|                           FITNADO OPERATIONS & AUTOMATION CONTROL CENTER                         |
+---------------------------------------------------------------------------------------------------+
|                                                                                                   |
|  [HEALTH MONITORING]          [QUEUE CONTROL]           [COST & BUDGET]          [ALERT CENTER]   |
|  ├── Worker Heartbeat TTL     ├── Research Queue        ├── Actual vs Estimated  ├── Fingerprint  |
|  ├── Real DB Ping & Stats     ├── Content Queue         ├── Daily & Monthly Cap  ├── Deduplication|
|  ├── FFmpeg/FFprobe Detect    ├── Video Render Queue    ├── Admin Override Log   ├── Acknowledge  |
|  └── Zero-Cost Provider Scan  ├── Publishing Queue      └── Emergency Pause Paid └── Resolve Flow |
|                               └── Stuck Job Detection                                             |
|                                                                                                   |
|                                [HUMAN ACTION QUEUE]                                               |
|                    ("CẦN BẠN XỬ LÝ" - Multi-Dimension Aggregation)                                |
|  ├── 1. CRITICAL: Failed Jobs, Degraded Workers, Stale Trackers                                   |
|  ├── 2. COST_BLOCKED / WARNING: Budget Exceeded, Over-budget Experiments                          |
|  ├── 3. APPROVAL: Video QC, Post Packages, Optimization Recommendations                           |
|  └── 4. NORMAL: Researched Candidates, Running Experiments, Unattributed Conversions              |
|                                                                                                   |
|                                [GLOBAL CONTROL PLANE]                                             |
|  ├── Master Automation Switch & Per-Module Toggles (Research / Content / Video / Publish / Opt)  |
|  ├── Safe Retry (FAILED only) & Safe Cancel (PENDING/RUNNING only)                                |
|  ├── Recursive Secret Sanitizer (Bearer tokens, API keys, Passwords masked everywhere)            |
|  └── Data Freshness Trackers (Distinguishes STALE from NOT_CONFIGURED)                            |
+---------------------------------------------------------------------------------------------------+
```

### Thành phần Lớp Dịch vụ Phase 10:
1. **`OperationsService`** (`libraries/class/class.OperationsService.php`):
   * **Process Registry & Worker Heartbeat**: Theo dõi 6 tiến trình nền qua TTL, xác định trạng thái thực tế (`HEALTHY`, `WARNING`, `DEGRADED`, `FAILED`, `DISABLED`, `UNKNOWN`).
   * **Queue Metrics & Stuck Job Detection**: Tổng hợp thời gian thực của 4 hàng đợi tác vụ, cảnh báo các job bị treo vượt ngưỡng.
   * **Zero-Cost Provider Diagnostics**: Chẩn đoán tính sẵn sàng của API bên thứ ba dựa trên DB mà không kích hoạt gọi cURL tính phí.
   * **Cost Center & Budget Guards**: Phân tách chi phí thực tế vs ước tính, giám sát hạn mức ngân sách ngày/tháng, cơ chế Dừng Khẩn Cấp (`pause_paid_automation`).
   * **Human Action Queue**: Tập trung toàn bộ 8 chiều tác vụ cần Admin xử lý, phân hạng ưu tiên rõ ràng.
   * **Secret Sanitizer**: Loại bỏ bí mật nhạy cảm (API key, token Bearer) trước khi xuất ra view/log.
   * **Idempotent Safe Retry & Safe Cancel**: Bảo vệ an toàn chống lặp lại tác vụ đã xuất bản hoặc đã tính phí.
2. **Bộ điều khiển & Giao diện Quản trị Operations (`admin/sources/operations.php` & `admin/templates/operations/`)**:
   * Quản lý 9 màn hình chuyên trách: `overview_tpl.php`, `pipeline_tpl.php`, `jobs_tpl.php`, `job_detail_tpl.php`, `providers_tpl.php`, `costs_tpl.php`, `alerts_tpl.php`, `logs_tpl.php`, `settings_tpl.php`.
