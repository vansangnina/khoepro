# KẾ HOẠCH TRIỂN KHAI PHASE 08 — ANALYTICS, AFFILIATE ATTRIBUTION & WINNER DETECTION

> **Dự án**: FITNADO Gym & Fitness Affiliate Platform  
> **Giai đoạn**: Phase 08 — Đo lường hiệu quả, Phân bổ chuyển đổi (Attribution) & Phát hiện sản phẩm thắng (Winner Detection)  
> **Tương thích**: 100% PHP 7.4  
> **Nguyên tắc cốt lõi**: Observed Data First — No Fake Production Metrics — Attribution by Identity — Sample Size Gate Before Judgment — Research Score != Performance

---

## 1. MỤC TIÊU NGHIỆP VỤ & KIẾN TRÚC VÒNG ĐO LƯỜNG

Phase 08 hoàn thiện vòng tuần hoàn đo lường từ nội dung đến doanh thu thực tế:

```text
PRODUCT (Sản phẩm)
   ↓
CONTENT / HOOK / SCRIPT (Nội dung AI)
   ↓
VIDEO (Video AI Render)
   ↓
POST (Gói xuất bản kèm Tracking Code)
   ↓
FITNADO VISIT (Landing Attribution)
   ↓
PRODUCT PAGE (Product View Event)
   ↓
AFFILIATE CLICK (Click Attribution & 302 Redirect)
   ↓
CONVERSION / REVENUE (CSV Import / Đối soát đơn hàng)
   ↓
ATTRIBUTION (Gán nguồn theo Last Eligible Content Touch)
   ↓
PERFORMANCE AGGREGATION (Tổng hợp hiệu suất)
   ↓
WINNER DETECTION (Động cơ phát hiện Winner theo quy tắc)
```

---

## 2. AUDIT NGUỒN DỮ LIỆU & TÁI SỬ DỤNG HỆ THỐNG CŨ

1. **`table_product`**: Nguồn sản phẩm chính thức (`namevi`, `regular_price`, `sale_price`, `specs`, `view`).
2. **`table_product_affiliate` & `table_affiliate_click`**: 
   - Tái sử dụng bảng `table_affiliate_click` từ Phase 02 làm nguồn transaction ghi nhận lượt click chuyển hướng.
   - Không xây click tracker thứ 2 gây xung đột.
   - Bổ sung trường `tracking_code`, `session_id`, `id_post`, `id_video`, `id_content` để đồng bộ hoàn hảo với `table_analytics_event`.
3. **`table_publish_post`**:
   - Tự động sinh `tracking_code` (Unique 64 chars) cho mỗi Post.
   - Tạo landing URL: `/product-slug/?ref=<tracking-code>&utm_source=tiktok&utm_medium=organic_video&utm_campaign=<product-slug>&utm_content=<tracking-code>`.
4. **`table_ai_video` & `table_ai_content`**:
   - Trích xuất chi phí sản xuất video (`local_render_cost`, `ai_video_cost`, `tts_cost`, `total_external_api_cost`) để tính toán Content ROI thực tế: `ROI = Commission - Content Cost`.
   - Phân tích hiệu suất theo từng loại Hook chiến lược (`tiktok_hooks`).

---

## 3. THIẾT KẾ CƠ SỞ DỮ LIỆU MỚI (DATABASE EXTENSIONS)

### 3.1. `table_analytics_event` (Nhật ký Sự kiện Chuẩn hóa)
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `event_type`: VARCHAR(50) NOT NULL (`PAGE_VIEW`, `PRODUCT_VIEW`, `AFFILIATE_CLICK`, `POST_VIEW`, `VIDEO_VIEW`, `ENGAGEMENT`, `CONVERSION`, `REVENUE`)
- `id_product`: INT(11) UNSIGNED NULL
- `id_post`: INT(11) UNSIGNED NULL
- `id_video`: INT(11) UNSIGNED NULL
- `id_content`: INT(11) UNSIGNED NULL
- `id_affiliate_offer`: INT(11) UNSIGNED NULL
- `tracking_code`: VARCHAR(64) NULL
- `session_id`: VARCHAR(64) NULL
- `source`: VARCHAR(50) NULL (vd: `tiktok`, `facebook`, `direct`, `google`)
- `medium`: VARCHAR(50) NULL (vd: `organic_video`, `cpc`, `referral`)
- `campaign`: VARCHAR(100) NULL
- `content_ref`: VARCHAR(100) NULL
- `referrer`: VARCHAR(500) NULL
- `ip_hash`: VARCHAR(64) NULL (SHA-256 ẩn danh bảo vệ quyền riêng tư)
- `user_agent`: VARCHAR(255) NULL
- `device_type`: VARCHAR(20) DEFAULT 'desktop'
- `is_internal`: TINYINT(1) DEFAULT 0 (Đánh dấu lưu lượng nội bộ / admin)
- `metadata`: JSON NULL
- `event_time`: INT(11) NOT NULL
- `date_created`: INT(11) NOT NULL
- **Indexes**: `idx_event_time`, `idx_event_type`, `idx_id_product`, `idx_id_post`, `idx_tracking_code`, `idx_id_affiliate_offer`, `idx_session_id`.

### 3.2. `table_affiliate_conversion` (Bảng Chuyển Đổi & Doanh Thu)
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `platform`: VARCHAR(50) NOT NULL (`shopee`, `tiktok_shop`, `lazada`, `tiki`, `brand`, `manual`)
- `external_conversion_id`: VARCHAR(100) NOT NULL (Mã đơn hàng / Sub ID trên sàn)
- `id_product`: INT(11) UNSIGNED NULL
- `id_affiliate_offer`: INT(11) UNSIGNED NULL
- `id_post`: INT(11) UNSIGNED NULL
- `id_video`: INT(11) UNSIGNED NULL
- `id_content`: INT(11) UNSIGNED NULL
- `tracking_code`: VARCHAR(64) NULL
- `session_id`: VARCHAR(64) NULL
- `order_value`: DOUBLE DEFAULT 0 (Giá trị đơn hàng)
- `commission_value`: DOUBLE DEFAULT 0 (Hoa hồng thực nhận)
- `currency`: VARCHAR(10) DEFAULT 'VND'
- `status`: VARCHAR(30) DEFAULT 'PENDING' (`PENDING`, `CONFIRMED`, `REVERSED`, `CANCELLED`)
- `conversion_at`: INT(11) NULL (Thời điểm khách đặt hàng)
- `confirmed_at`: INT(11) NULL (Thời điểm sàn đối soát duyệt hoa hồng)
- `raw_reference`: JSON NULL (Payload gốc)
- `import_id`: INT(11) UNSIGNED NULL
- `is_manual_matched`: TINYINT(1) DEFAULT 0
- `matched_by`: VARCHAR(100) NULL
- `match_notes`: TEXT NULL
- `date_created`, `date_updated`: INT(11) NOT NULL
- **Unique Key**: `uniq_platform_ext_id` (`platform`, `external_conversion_id`) — Chống nhân đôi doanh thu khi import nhiều lần.

### 3.3. `table_conversion_import_log` (Nhật ký Import CSV Đối soát)
- `id`: INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `filename`: VARCHAR(255) NOT NULL
- `platform`: VARCHAR(50) NOT NULL
- `total_rows`: INT(11) DEFAULT 0
- `imported_count`: INT(11) DEFAULT 0
- `duplicate_count`: INT(11) DEFAULT 0
- `failed_count`: INT(11) DEFAULT 0
- `admin_user`: VARCHAR(100) NOT NULL
- `status`: VARCHAR(30) DEFAULT 'SUCCESS' (`SUCCESS`, `PARTIAL`, `FAILED`)
- `summary_json`: JSON NULL
- `date_created`: INT(11) NOT NULL

### 3.4. `table_winner_evaluation` (Lịch sử & Snapshot Đánh Giá Winner)
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `id_product`: INT(11) UNSIGNED NOT NULL
- `id_post`: INT(11) UNSIGNED NULL
- `id_video`: INT(11) UNSIGNED NULL
- `id_content`: INT(11) UNSIGNED NULL
- `winner_status`: VARCHAR(30) NOT NULL (`INSUFFICIENT_DATA`, `TESTING`, `PROMISING`, `WINNER`, `UNDERPERFORMING`)
- `signal_level`: VARCHAR(30) NOT NULL (`NONE`, `TRAFFIC_SIGNAL`, `CLICK_SIGNAL`, `CONVERSION_SIGNAL`, `REVENUE_SIGNAL`)
- `metrics_snapshot`: JSON NOT NULL (Đóng băng: sessions, clicks, ctr, cvr, commission, cost, roi)
- `rules_snapshot`: JSON NOT NULL (Đóng băng rules version & thresholds tại thời điểm đánh giá)
- `recommendation`: JSON NOT NULL (`CREATE_VARIATION`, `CREATE_NEW_HOOK`, `KEEP_TESTING`, `STOP_TESTING`, `UPGRADE_TO_HYBRID`)
- `ai_analysis`: TEXT NULL (Nhãn bắt buộc: AI_ANALYSIS)
- `evaluated_at`: INT(11) NOT NULL
- `evaluated_by`: VARCHAR(100) NOT NULL
- `date_created`: INT(11) NOT NULL

### 3.5. `table_analytics_setting` (Cấu hình Tham số Analytics & Winner)
- Quản trị ngưỡng:
  - `attribution_window_days`: 30
  - `min_landing_sessions`: 30
  - `min_affiliate_clicks`: 10
  - `min_conversions`: 2
  - `min_test_age_days`: 3
  - `promising_ctr_pct`: 5.0
  - `winner_ctr_pct`: 10.0
  - `winner_cvr_pct`: 5.0
  - `underperforming_ctr_pct`: 1.0
  - `internal_ips`: `["127.0.0.1", "::1"]`

---

## 4. CÁC LỚP NGHIỆP VỤ (CORE SERVICES)

### 4.1. `AnalyticsService` (`libraries/class/class.AnalyticsService.php`)
- **Attribution Handler**: Ghi nhận visitor từ `?ref=<tracking_code>`, lưu session/cookie ẩn danh với thời gian sống theo `attribution_window_days`.
- **Event Dispatcher**: `logEvent($eventType, $data)` với cơ chế ẩn danh IP (`hash('sha256', $ip)`) và đánh dấu `is_internal = 1` nếu là traffic từ Admin/IP nội bộ.
- **Aggregations & Metrics**:
  - `getOverviewMetrics($timeRange, $filters)`
  - `getProductMetrics($productId, $timeRange, $filters)`
  - `getPostMetrics($postId, $timeRange, $filters)`
  - `getVideoMetrics($videoId, $timeRange, $filters)`
  - `getContentHookMetrics($timeRange, $filters)`
  - `getAffiliatePlatformMetrics($timeRange)`
  - `getConversionMetrics($timeRange)`
- **Xử lý An toàn Toán học**:
  - Zero Division Guard: Mẫu số = 0 trả về 0 hoặc `'N/A'`, không crash.
  - Multi-Currency: Không cộng gộp VND với USD, phân tách theo currency.
  - Content ROI: `ROI = Confirmed Commission - External Content Cost`.

### 4.2. `WinnerDetectionEngine` (`libraries/class/class.WinnerDetectionEngine.php`)
- **Rule-Based Evaluation**:
  - Cổng kích thước mẫu (**Sample Size Gate**): Bắt buộc kiểm tra `min_landing_sessions` và `min_affiliate_clicks`. Nếu chưa đủ mẫu -> Trả về `INSUFFICIENT_DATA`, cấm gán nhãn `LOSER` hoặc `WINNER` bừa bãi.
  - Phân tầng tín hiệu (**Signal Levels**): `TRAFFIC_SIGNAL` (Lượng truy cập lớn) -> `CLICK_SIGNAL` (Tỷ lệ click cao) -> `CONVERSION_SIGNAL` (Có đơn hàng) -> `REVENUE_SIGNAL` (ROI dương, có lợi nhuận).
  - Đề xuất kinh doanh (**Actionable Recommendations**): Chỉ đưa ra gợi ý (`CREATE_VARIATION`, `CREATE_NEW_HOOK`, `KEEP_TESTING`, `STOP_TESTING`, `UPGRADE_TO_HYBRID`). Tuyệt đối không tự ý chi tiền ads hoặc tự động publish bài.
  - Snapshot & Lịch sử: Lưu lại snapshot để đối soát lý do vì sao sản phẩm từng được công nhận là Winner.
  - Tách bạch Score: Giữ nguyên vẹn `Research Score` (Phase 03) và `AI Confidence` (Phase 04/05), không bao giờ ghi đè hoặc nhầm lẫn với `Performance Score` (Phase 08).

### 4.3. `ConversionImporter` (`libraries/class/class.ConversionImporter.php`)
- Hỗ trợ format CSV chuẩn của FITNADO và các sàn thương mại điện tử.
- Xem trước (Preview): Phân loại số dòng hợp lệ, không hợp lệ, trùng lặp và không khớp mã tracking (`UNATTRIBUTED`).
- Import theo cơ chế an toàn: Chặn trùng lặp theo `platform + external_conversion_id`.
- Hỗ trợ cập nhật trạng thái đơn hoàn tiền / hủy (`REVERSED`, `CANCELLED`).
- Hỗ trợ Admin ghép nối thủ công (`Manual Match`) các đơn hàng `UNATTRIBUTED` kèm audit log.

---

## 5. TÍCH HỢP GIAO DIỆN ADMIN & FRONTEND

### 5.1. Frontend & Tracking Endpoints
- `sources/allpage.php`: Bắt `?ref=...` hoặc `?utm_content=...` khi người dùng vào bất kỳ trang nào, khởi tạo anonymous session attribution.
- `sources/product.php`: Ghi nhận sự kiện `PRODUCT_VIEW` gắn với attribution context.
- `sources/affiliate.php`: Đọc attribution context từ session/cookie, ghi nhận click vào `table_affiliate_click` và `table_analytics_event` trước khi thực hiện 302 redirect siêu tốc.

### 5.2. Admin Analytics UI (`admin/sources/analytics.php`)
- `overview_tpl.php`: Bảng điều khiển tổng quan KPI, biểu đồ phễu chuyển đổi, trạng thái kết nối conversion data.
- `products_tpl.php`: Bảng hiệu suất sản phẩm (Sessions, Clicks, CTR, Conversions, CVR, Commission, Content Cost, ROI, Winner Signal).
- `posts_tpl.php`: Bảng hiệu suất bài đăng TikTok / Mạng xã hội.
- `videos_tpl.php`: Bảng hiệu suất video theo chế độ `ECONOMY` vs `HYBRID`.
- `content_tpl.php`: Bảng hiệu suất theo phân loại Hook (`Curiosity`, `Problem-Agitate`, `Transformation`...) và phiên bản kịch bản.
- `conversions_tpl.php`: Danh sách đơn hàng chuyển đổi, hỗ trợ lọc, modal ghép nối thủ công (`Manual Match`).
- `conversion_import_tpl.php`: Giao diện tải lên CSV, xem trước validation preview, xác nhận import.
- `winner_detection_tpl.php`: Bảng xếp hạng sản phẩm thắng, nút kích hoạt đánh giá lại, xem snapshot lịch sử.
- `winner_rules_tpl.php`: Màn hình cấu hình ngưỡng mẫu tối thiểu và ngưỡng CTR/CVR/ROI (Bảo vệ CSRF).

---

## 6. KẾ HOẠCH KIỂM THỬ TOÀN DIỆN (VERIFICATION PLAN)

1. **Automated Test Suite (`test_phase08.php`)**:
   - Chạy 22 nhóm test assertions theo đúng yêu cầu đề bài.
2. **PHP Compatibility Check**:
   - `php -c php.ini -l` 100% PASS trên tất cả các file liên quan.
3. **Regression Check**:
   - Chạy toàn bộ các test suite từ Phase 01 đến Phase 07 (`test_regression.php`, `test_phase03.php` -> `test_phase07.php`).
4. **Browser Testing Subagent**:
   - Duyệt thực tế tất cả các trang Admin Analytics.
5. **Documentation Updates**:
   - Cập nhật `.ai/DATABASE.md`, `.ai/ARCHITECTURE.md`, `.ai/BUSINESS_RULES.md`, `.ai/CHANGELOG.md`.
   - Tạo kỹ năng `.ai/skills/fitnado-analytics/SKILL.md`.
   - Lập báo cáo kiểm thử `.ai/reports/PHASE-08-ANALYTICS-TEST-REPORT.md`.
