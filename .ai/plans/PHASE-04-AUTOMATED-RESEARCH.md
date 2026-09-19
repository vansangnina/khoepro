# FITNADO — KẾ HOẠCH TRIỂN KHAI PHASE 04
# AUTOMATED PRODUCT RESEARCH + AI RESEARCH AGENT

---

## 1. TỔNG QUAN VÀ MỤC TIÊU (OVERVIEW & GOALS)

Phase 04 nâng cấp hệ thống nghiên cứu thủ công từ Phase 03 thành **Hệ thống Khám phá & Nghiên cứu Sản phẩm Bán tự động / Tự động hóa** ứng dụng AI Agent thông minh.

### Luồng nghiệp vụ toàn diện:
```text
ADMIN RESEARCH SEEDS (Keywords, Categories, Problems, Audiences)
      ↓
RESEARCH JOB QUEUE (table_product_research_job: PENDING → RUNNING)
      ↓
BACKGROUND WORKER (CLI Cron / Safe Web Runner)
      ↓
RESEARCH PROVIDERS (Manual, AI Agent, Platform APIs, CSV Import)
      ↓
DATA NORMALIZATION & FACT EXTRACTION (Market Metrics from Source)
      ↓
AI RESEARCH AGENT (Structured JSON: Angles, Audience, Visual & Comparison Potential)
      ↓
EVIDENCE CAPTURE (table_product_research_evidence: FACT vs AI_ANALYSIS)
      ↓
DUPLICATE CHECK (Reuse Phase 03 ProductResearch::checkDuplicate)
      ├── Exact Match: Update last_seen_at & record Snapshot
      └── New Candidate: Insert in DISCOVERED status
      ↓
AUTO SCORING (Reuse Phase 03 ProductResearch::calculateTotalScore)
      ↓
CANDIDATE AUTO STATUS: RESEARCHED (Never Auto-Approve / Never Auto-Publish)
      ↓
ADMIN REVIEW QUEUE → APPROVE → CREATE DRAFT PRODUCT (table_product)
```

---

## 2. NGUYÊN TẮC BẤT DI BẤT DỊCH (CORE PRINCIPLES)

1. **Phân định rạch ròi FACT vs AI_ANALYSIS**:
   - Dữ liệu sự thật thị trường (`sales_count`, `rating`, `review_count`, `commission`, `views`) chỉ được lấy từ dữ liệu nguồn (Evidence).
   - Nếu nguồn không có, bắt buộc để `NULL`. AI tuyệt đối không được tự bịa số liệu thị trường.
2. **Không tự động Phê duyệt hay Xuất bản (Human Approval First)**:
   - Ứng viên sau khi chạy tự động và chấm điểm cao đến đâu (kể cả 100/100) cũng chỉ dừng ở trạng thái `RESEARCHED`.
   - Admin là người duy nhất quyết định `APPROVED` và tạo sản phẩm `table_product` ở chế độ nháp.
3. **Lưu vết bằng chứng nguồn (Evidence Traceability)**:
   - Toàn bộ dữ liệu tự động đều lưu vết rõ ràng trong `table_product_research_evidence`.
4. **Kiến trúc Provider & AI độc lập**:
   - Không hardcode API của từng hãng. Sử dụng `ResearchProviderInterface` và `AIProviderInterface`.
5. **Chống trùng lặp & Chống quá tải**:
   - Tái sử dụng 100% logic chống duplicate từ Phase 03.
   - Có cơ chế giới hạn số lượng (`max_results`), giới hạn ngân sách/request hàng ngày (`daily_request_limit`) và retry có kiểm soát (tối đa 3 lần).
6. **Tương thích PHP 7.4**:
   - 100% mã nguồn tuân thủ PHP 7.4, kiểm tra `php -l` cho toàn bộ file PHP.

---

## 3. THIẾT KẾ CƠ SỞ DỮ LIỆU MỚI (DATABASE DESIGN)

### 1. Bảng `table_product_research_seed` (Từ khóa & Hạt giống nghiên cứu)
| Cột | Kiểu | Mô tả |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED AUTO_INCREMENT | Khóa chính |
| `title` | VARCHAR(255) NOT NULL | Tiêu đề hạt giống nghiên cứu |
| `keyword` | VARCHAR(255) NOT NULL | Từ khóa tìm kiếm hoặc mô tả vấn đề |
| `seed_type` | VARCHAR(50) NOT NULL | keyword, category, problem, audience, product_idea |
| `category_id` | INT UNSIGNED DEFAULT 0 | ID danh mục liên kết |
| `platform` | VARCHAR(50) DEFAULT 'all' | tiktok, shopee, lazada, all |
| `priority` | INT DEFAULT 10 | Mức độ ưu tiên (1-100) |
| `depth` | VARCHAR(20) DEFAULT 'STANDARD' | QUICK, STANDARD, DEEP |
| `frequency` | VARCHAR(20) DEFAULT 'manual' | manual, daily, weekly |
| `max_results` | INT DEFAULT 10 | Giới hạn số ứng viên tối đa mỗi lượt |
| `status` | VARCHAR(20) DEFAULT 'active' | active, inactive |
| `last_run` | INT DEFAULT 0 | Timestamp lần chạy gần nhất |
| `next_run` | INT DEFAULT 0 | Timestamp dự kiến chạy tiếp theo |
| `date_created`, `date_updated` | INT NOT NULL | Timestamps |

### 2. Bảng `table_product_research_job` (Hàng đợi tác vụ)
| Cột | Kiểu | Mô tả |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED AUTO_INCREMENT | Khóa chính |
| `id_seed` | INT UNSIGNED DEFAULT 0 | ID hạt giống |
| `provider` | VARCHAR(50) NOT NULL | ai_agent, tiktok, shopee, manual, csv |
| `depth` | VARCHAR(20) DEFAULT 'STANDARD' | QUICK, STANDARD, DEEP |
| `status` | VARCHAR(20) DEFAULT 'PENDING' | PENDING, RUNNING, SUCCESS, FAILED, RETRY |
| `attempts` | INT DEFAULT 0 | Số lần đã thử |
| `max_attempts` | INT DEFAULT 3 | Số lần thử tối đa |
| `payload` | LONGTEXT NULL | Tham số đầu vào (JSON) |
| `candidates_found` | INT DEFAULT 0 | Số ứng viên phát hiện |
| `candidates_created` | INT DEFAULT 0 | Số ứng viên mới thêm vào DB |
| `duplicates_count` | INT DEFAULT 0 | Số ứng viên trùng lặp |
| `result_summary` | TEXT NULL | Tóm tắt kết quả |
| `error_message` | TEXT NULL | Lỗi chi tiết nếu thất bại |
| `started_at`, `finished_at` | INT DEFAULT 0 | Thời gian bắt đầu và kết thúc |
| `duration` | DOUBLE DEFAULT 0 | Thời gian thực thi (giây) |
| `date_created`, `date_updated` | INT NOT NULL | Timestamps |

### 3. Bảng `table_product_research_evidence` (Bằng chứng & Nguồn gốc dữ liệu)
| Cột | Kiểu | Mô tả |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED AUTO_INCREMENT | Khóa chính |
| `id_research` | INT UNSIGNED NOT NULL | ID ứng viên liên kết |
| `id_job` | INT UNSIGNED DEFAULT 0 | ID job thực thi |
| `provider` | VARCHAR(50) NOT NULL | Nguồn cung cấp dữ liệu |
| `source_url` | TEXT NULL | Liên kết nguồn bằng chứng |
| `evidence_type` | VARCHAR(50) NOT NULL | FACT, MARKET_SIGNAL, AI_ANALYSIS, RAW_PAYLOAD |
| `field_name` | VARCHAR(100) NOT NULL | Tên trường (sales_count, rating, etc.) |
| `field_value` | TEXT NULL | Giá trị ghi nhận |
| `captured_at` | INT NOT NULL | Timestamp ghi nhận |

### 4. Bảng `table_product_research_snapshot` (Lịch sử biến động chỉ số)
| Cột | Kiểu | Mô tả |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED AUTO_INCREMENT | Khóa chính |
| `id_research` | INT UNSIGNED NOT NULL | ID ứng viên liên kết |
| `id_job` | INT UNSIGNED DEFAULT 0 | ID job |
| `price`, `sales_count`, `rating`, `review_count` | ... | Chỉ số thị trường tại thời điểm snapshot |
| `commission_rate`, `creator_count`, `video_count`, `top_video_views` | ... | Chỉ số nội dung & hoa hồng |
| `captured_at` | INT NOT NULL | Timestamp |

### 5. Mở rộng bảng `table_product_research`
Bổ sung các cột:
- `discovery_source`: Nguồn phát hiện (manual, ai_agent, tiktok, shopee, csv...)
- `ai_analysis`: JSON chứa góc tiếp cận, đối tượng, tính trực quan, rủi ro.
- `ai_confidence`: Độ tin cậy của AI (0.0 - 1.0 hoặc 0 - 100)
- `first_seen_at`: Timestamp phát hiện lần đầu
- `last_seen_at`: Timestamp quét thấy gần nhất

---

## 4. DANH MỤC FILE THỰC HIỆN

### File mới (Created):
1. `database/migrations/phase04_automated_research.sql`
2. `libraries/class/class.ResearchProvider.php`
3. `libraries/class/class.AIResearchAgent.php`
4. `libraries/class/class.ResearchJobQueue.php`
5. `cron/product_research_worker.php`
6. `admin/templates/product_research/seeds_tpl.php`
7. `admin/templates/product_research/seed_add_tpl.php`
8. `admin/templates/product_research/jobs_tpl.php`
9. `admin/templates/product_research/provider_config_tpl.php`
10. `.ai/plans/PHASE-04-AUTOMATED-RESEARCH.md`
11. `.ai/skills/fitnado-automated-research/SKILL.md`
12. `.ai/reports/PHASE-04-TEST-REPORT.md`
13. `test_phase04.php`

### File chỉnh sửa (Modified):
1. `libraries/class/class.ProductResearch.php` (Tích hợp snapshot, evidence, last_seen, AI analysis ingestion)
2. `admin/sources/product_research.php` (Thêm actions seeds, jobs, provider_config, và filter nguồn tự động)
3. `admin/templates/product_research/mans_tpl.php` (Thêm bộ lọc nguồn tự động và badge nguồn)
4. `admin/templates/product_research/man_add_tpl.php` (Thêm tab xem Evidence và AI Analysis)
5. `admin/templates/layout/menu.php` (Mở rộng menu đa cấp cho Nghiên cứu sản phẩm)
6. `.ai/DATABASE.md`, `.ai/BUSINESS_RULES.md`, `.ai/ARCHITECTURE.md`, `.ai/CHANGELOG.md`, `.ai/knowledge/`

---

## 5. KẾ HOẠCH KIỂM THỬ (TEST PLAN)

1. **PHP 7.4 Syntax**: `php -l` cho toàn bộ file PHP.
2. **Automated Unit & Pipeline Tests (`test_phase04.php`)**:
   - Quản lý Seed và tạo Job không nghẽn request.
   - Cơ chế Worker xử lý Job: PENDING → RUNNING → SUCCESS / FAILED.
   - Kiểm tra AI Agent với JSON Schema và retry khi JSON sai cú pháp.
   - Kiểm tra phân định FACT vs AI_ANALYSIS (ngăn chặn AI tự bịa số liệu).
   - Kiểm tra lưu trữ Evidence và Snapshot.
   - Kiểm tra chống Duplicate cho sản phẩm đã quét qua.
   - Kiểm tra ứng viên tự động dừng ở `RESEARCHED` và không tự động `APPROVED`.
3. **Regression Tests**:
   - `test_phase03.php`: 26/26 tests PASS.
   - `test_regression.php`: Phase 01 & Phase 02 PASS.
