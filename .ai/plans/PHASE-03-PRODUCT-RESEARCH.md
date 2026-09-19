# FITNADO — KẾ HOẠCH TRIỂN KHAI PHASE 03
# PRODUCT RESEARCH + PRODUCT SCORING + CANDIDATE PIPELINE

---

## 1. TỔNG QUAN VÀ MỤC TIÊU (OVERVIEW & GOALS)

Phase 03 xây dựng hệ thống **Nghiên cứu sản phẩm (Product Research)**, **Chấm điểm tiềm năng (Scoring Engine)** và **Quy trình duyệt ứng viên (Candidate Pipeline)** chuyên sâu cho FITNADO.

### Luồng nghiệp vụ cốt lõi:
```text
DISCOVER PRODUCT (Manual / CSV / AI / Platform)
      ↓
RESEARCH CANDIDATE (table_product_research)
      ↓
NORMALIZE DATA & METRICS
      ↓
CALCULATE SCORE (0–100 Weighted Signal, Configurable, NULL != 0)
      ↓
ADMIN REVIEW & DUPLICATE CHECK
      ↓
APPROVE / REJECT (with Reason)
      ↓
CREATE / LINK table_product (Unpublished Draft) + table_product_affiliate
      ↓
READY FOR CONTENT (Phase 04 Ready)
```

---

## 2. NGUYÊN TẮC BẤT DI BẤT DỊCH (CORE PRINCIPLES)

1. **Phân định rõ Candidate vs Product**:
   - `table_product_research` chứa dữ liệu nghiên cứu thị trường / sản phẩm tiềm năng.
   - `table_product` là sản phẩm chính thức trên website FITNADO.
2. **Không tự động Public**:
   - Tuyệt đối không cho phép AI hay Research Engine tự động tạo sản phẩm hiển thị ra ngoài website (`hienthi`).
   - Mọi sản phẩm tạo từ candidate mặc định ở trạng thái nháp (chưa xuất bản), bắt buộc qua Admin duyệt nội dung.
3. **Điểm số là Signal, không phải Chân lý**:
   - Score (0–100) là tín hiệu ưu tiên nghiên cứu (Research Priority Signal), không phải cam kết bán chạy (Guaranteed Winner).
4. **Xử lý Missing Data chuẩn xác (`NULL != 0`)**:
   - Trường dữ liệu chưa biết (`NULL`) không bị ép về `0`, thuật toán chấm điểm bỏ qua hoặc chuẩn hóa theo trọng số các trường có sẵn.
5. **Chống Duplicate chặt chẽ**:
   - 3 tầng kiểm tra: `platform + external_product_id`, `normalized_url`, và `normalized_name + brand`.
6. **Tương thích PHP 7.4**:
   - Tuân thủ PHP 7.4, kiểm tra `php -l` cho toàn bộ file PHP.

---

## 3. THIẾT KẾ CƠ SỞ DỮ LIỆU (DATABASE DESIGN)

### Bảng `table_product_research`
| Cột | Kiểu | Mô tả |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED AUTO_INCREMENT | Khóa chính |
| `name` | VARCHAR(255) NOT NULL | Tên sản phẩm ứng viên |
| `normalized_name` | VARCHAR(255) NULL | Tên chuẩn hóa (lowercase, stripped punctuation) |
| `category_hint` | VARCHAR(255) NULL | Gợi ý danh mục |
| `brand_hint` | VARCHAR(255) NULL | Gợi ý thương hiệu |
| `platform` | VARCHAR(50) NOT NULL | Nguồn: tiktok, shopee, lazada, brand, manual, google, other |
| `source_url` | TEXT NOT NULL | URL gốc của sản phẩm |
| `normalized_url` | VARCHAR(500) NULL | URL chuẩn hóa (bỏ query tracking UTM/spm/aff) |
| `external_product_id` | VARCHAR(100) NULL | ID sản phẩm trên sàn/nền tảng ngoài |
| `image_url` | TEXT NULL | Link ảnh đại diện ứng viên |
| `price` | DOUBLE NULL | Giá bán hiện tại |
| `original_price` | DOUBLE NULL | Giá gốc |
| `currency` | VARCHAR(10) DEFAULT 'VND' | Đơn vị tiền tệ |
| `sales_count` | INT NULL | Lượt bán |
| `rating` | DOUBLE NULL | Đánh giá sao (0.0 - 5.0) |
| `review_count` | INT NULL | Số lượng đánh giá |
| `commission_rate` | DOUBLE NULL | Tỷ lệ hoa hồng (%) |
| `commission_value` | DOUBLE NULL | Giá trị hoa hồng ước tính (VND) |
| `estimated_gmv` | DOUBLE NULL | Ước tính GMV |
| `creator_count` | INT NULL | Số lượng KOC/Creator làm video |
| `video_count` | INT NULL | Số lượng video liên quan |
| `top_video_views` | BIGINT NULL | Lượt view video cao nhất |
| `problem_solved` | TEXT NULL | Vấn đề sản phẩm giải quyết (Pain point) |
| `target_audience` | TEXT NULL | Khách hàng mục tiêu |
| `research_notes` | TEXT NULL | Ghi chú nghiên cứu |
| `primary_keyword` | VARCHAR(255) NULL | Từ khóa chính SEO |
| `demand_score` | DOUBLE NULL | Điểm nhu cầu thị trường (0-100) |
| `content_score` | DOUBLE NULL | Điểm tiềm năng làm nội dung (0-100) |
| `commission_score` | DOUBLE NULL | Điểm tiềm năng hoa hồng (0-100) |
| `competition_score` | DOUBLE NULL | Điểm cơ hội cạnh tranh (0-100, điểm cao = ít bão hòa) |
| `seo_score` | DOUBLE NULL | Điểm cơ hội SEO (0-100) |
| `total_score` | DOUBLE NULL | Tổng điểm trọng số (0-100) |
| `score_breakdown` | TEXT/JSON NULL | Chi tiết phân rã điểm & lý do |
| `status` | VARCHAR(50) DEFAULT 'DISCOVERED' | DISCOVERED, RESEARCHED, APPROVED, REJECTED, PRODUCT_CREATED |
| `reject_reason` | VARCHAR(255) NULL | Lý do từ chối |
| `id_product` | INT NULL | ID sản phẩm liên kết trong `table_product` |
| `raw_data` | LONGTEXT/JSON NULL | Dữ liệu thô từ nguồn |
| `history` | LONGTEXT/JSON NULL | Lịch sử thay đổi trạng thái |
| `date_created` | INT NOT NULL | Timestamp tạo |
| `date_updated` | INT NOT NULL | Timestamp cập nhật |

---

## 4. THIẾT KẾ ĐỘNG CƠ CHẤM ĐIỂM (SCORING ENGINE DESIGN)

### Trọng số mặc định (Configurable):
- **Demand (Nhu cầu)**: 30%
- **Content Potential (Tiềm năng nội dung/TikTok)**: 25%
- **Commission (Hoa hồng & Biên lợi nhuận)**: 20%
- **Competition (Cơ hội cạnh tranh - ít bão hòa)**: 15%
- **SEO Opportunity (Tiềm năng từ khóa)**: 10%
- **Tổng**: 100%

### Công thức & Quy tắc xử lý:
1. **Demand Score (0–100)**:
   - Dựa trên `sales_count`, `rating`, `review_count`, `estimated_gmv`.
   - Nếu `sales_count` >= 5,000 → điểm trần 85-95.
2. **Content Potential Score (0–100)**:
   - Dựa trên tính trực quan (`visual demonstration`), độ rõ ràng của giải pháp (`problem_solved`), `top_video_views`, `creator_count`.
3. **Commission Score (0–100)**:
   - Dựa trên `commission_rate` (>15% = 85+, 10-15% = 70-80, 5-10% = 50-65) kết hợp giá bán để tính `commission_value`.
4. **Competition Opportunity Score (0–100)**:
   - Định nghĩa: Điểm cao = Thị trường còn nhiều đất diễn (ít đối thủ thống trị hoặc ngách ngách tiềm năng).
5. **SEO Score (0–100)**:
   - Dựa trên sự rõ ràng của `primary_keyword` và ý định tìm kiếm (Search Intent).
6. **Xử lý `NULL != 0`**:
   - Khi một chiều thiếu dữ liệu (`NULL`), trọng số được tái phân bổ đều cho các chiều có dữ liệu, tránh việc gán 0 điểm oan cho sản phẩm mới.

---

## 5. QUY TRÌNH DUYỆT & TẠO SẢN PHẨM (APPROVAL & PRODUCT MAPPING)

### Quy tắc chuyển đổi trạng thái:
- `DISCOVERED` → `RESEARCHED` (Khi đã nhập/tính toán đủ thông tin)
- `RESEARCHED` → `APPROVED` hoặc `REJECTED`
- `APPROVED` → `PRODUCT_CREATED` (Khi Admin nhấn Tạo sản phẩm)
- `REJECTED` → Lưu `reject_reason` và không xóa khỏi DB.

### Mapping sang `table_product`:
- `name` → `namevi` (Tạo `slugvi` tự động)
- `price` → `regular_price`
- `problem_solved` + `research_notes` → `descvi` / `specs`
- `category_hint` → Mapping sang `id_list` / `id_cat` theo danh mục có sẵn
- `brand_hint` → Mapping sang `id_brand`
- `type` = `'san-pham'`
- `status` = `''` (Chưa xuất bản, không có `hienthi`)
- Tự động tạo bản ghi trong `table_product_affiliate` nếu có link affiliate/nguồn
- Cập nhật `table_product_research.id_product` và chuyển trạng thái sang `PRODUCT_CREATED`.

---

## 6. DANH MỤC FILE THỰC HIỆN

### File mới:
1. `database/migrations/phase03_product_research.sql`
2. `libraries/class/class.ProductResearch.php`
3. `admin/sources/product_research.php`
4. `admin/templates/product_research/mans_tpl.php`
5. `admin/templates/product_research/man_add_tpl.php`
6. `admin/templates/product_research/create_product_tpl.php`
7. `admin/templates/product_research/weights_tpl.php`
8. `.ai/skills/fitnado-product-research/SKILL.md`
9. `.ai/reports/PHASE-03-TEST-REPORT.md`

### File chỉnh sửa:
1. `admin/templates/layout/menu.php` (Thêm menu Nghiên cứu sản phẩm)
2. `.ai/DATABASE.md`
3. `.ai/BUSINESS_RULES.md`
4. `.ai/ARCHITECTURE.md`
5. `.ai/CHANGELOG.md`
6. `.ai/knowledge/ERRORS.md`, `DECISIONS.md`, `LESSONS_LEARNED.md`

---

## 7. KẾ HOẠCH KIỂM THỬ (TEST PLAN)

1. **Syntax PHP 7.4**: Chạy `php -l` trên toàn bộ file PHP liên quan.
2. **Automated CLI Testing**:
   - Chấm điểm chuẩn xác và xử lý `NULL != 0`.
   - Chống trùng lặp theo ID ngoài, URL chuẩn hóa, và tên tương đồng.
   - Chuyển đổi trạng thái Candidate hợp lệ.
   - Tạo draft `table_product` và ngăn chặn tạo duplicate `id_product`.
3. **Manual Admin Testing**:
   - Thêm ứng viên thủ công.
   - Lọc và tìm kiếm danh sách.
   - Duyệt và từ chối kèm lý do.
   - Tạo sản phẩm từ ứng viên và kiểm tra liên kết ngược.
4. **Hồi quy (Regression Testing)**:
   - Phase 01: Trang chủ, Header, Footer, Category.
   - Phase 02: Chi tiết sản phẩm, Affiliate Offers, Redirect `/go/{id}`, Click Tracking, Reviews, Comparison.
