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

