# KẾ HOẠCH TRIỂN KHAI: FITNADO — PHASE 05
## AI CONTENT ENGINE
### Research → Approved Product → Content Package → Quality Gate → Human Approval → Website / SEO / TikTok Script

---

## 1. TỔNG QUAN KIẾN TRÚC & MỤC TIÊU

Phase 05 xây dựng hệ sinh thái sinh nội dung AI chuyên nghiệp cho FITNADO, lấy dữ liệu thực tế từ **Product Research & Evidence** (Phase 03, Phase 04), **Affiliate Offers** (Phase 02), và **Product Taxonomy/Specs** (Phase 01) để tạo ra các gói nội dung đa kênh:
- **Product Analysis**: Phân tích chuyên sâu 12 khía cạnh (vấn đề, đối tượng, lợi ích cốt lõi, hạn chế, pros/cons, đối tượng phù hợp/không phù hợp, lưu ý mua hàng, góc so sánh, rủi ro).
- **TikTok Strategic Hooks**: 7 loại hook bán hàng (Problem, Mistake, Comparison, Curiosity, Demo, Buyer Warning, Value).
- **TikTok Scripts & Video Shot Plan (Cầu nối Phase 06)**: Kịch bản video ngắn (15s, 30s, 45s, 60s) phân cảnh chi tiết (scene_number, duration, visual_instruction, voiceover, on_screen_text, asset_requirement).
- **SEO Metadata & FAQ Package**: Từ khóa chính/phụ, Search Intent, SEO Title, Meta Description, Dàn ý & FAQ.
- **Editorial Review Draft**: Bản thảo bài đánh giá sản phẩm chuyên sâu dựa trên số liệu thực tế.

---

## 2. NGUYÊN TẮC BẤT DI BẤT DỊCH (NON-NEGOTIABLE PRINCIPLES)

1. **PHP 7.4 Compatibility**: 100% mã nguồn chạy chuẩn trên PHP 7.4 (`php -l` PASS 100%).
2. **Factual Integrity & Source Priority**:
   ```text
   VERIFIED PRODUCT DATA > RESEARCH FACT > APPROVED EDITORIAL DATA > AI ANALYSIS
   ```
   - AI không tự bịa thông số kỹ thuật, lượt bán, đánh giá sao, giấy chứng nhận y tế.
   - Cấm tự xưng "Tôi đã dùng..." khi chưa có bằng chứng `REAL_TEST`.
   - Cấm tạo fake review / fake testimonial.
   - Cấm cam kết điều trị y khoa ("chữa đau lưng", "trị thoát vị").
3. **Cổng duyệt Con người (Strict Human Gate)**:
   ```text
   GENERATING → GENERATED → REVIEW_REQUIRED → APPROVED / REJECTED → APPLIED
   ```
   Nội dung do AI tạo không bao giờ tự động ghi đè lên `table_product` hoặc tự động công khai.
4. **Versioning & Quản lý Biến thể (Zero Overwrite)**:
   Mỗi lần generate tạo ra version mới (`v1, v2, v3...`). Không ghi đè các version cũ. Duy nhất 1 version được chọn làm `is_active = 1` cho từng loại nội dung.
5. **Cơ chế Source Hash & Outdated Detection**:
   Tạo mã băm SHA-256 xác định tính toàn vẹn của dữ liệu đầu vào. Khi thông tin nghiên cứu hoặc giá thay đổi lớn, nội dung cũ được gắn nhãn `is_outdated = 1` để nhắc nhở Admin rà soát lại.
6. **Sao lưu trước khi Áp dụng (Apply to Product & Reversible Backup)**:
   Khi Admin chọn "Áp dụng vào sản phẩm", hệ thống lưu bản sao lưu nội dung cũ vào `table_product_content_backup` trước khi cập nhật `table_product` hoặc `table_seo`.
7. **Tái sử dụng AI Provider Abstraction**:
   Tận dụng toàn bộ `AIProviderInterface`, `GeminiAIProvider`, `OpenAICompatibleProvider`, `MockAIProvider` đã xây dựng ở Phase 04.

---

## 3. CƠ SỞ DỮ LIỆU MỚI (DATABASE SCHEMA)

### 3.1. `table_ai_content`
- Quản lý các gói nội dung được sinh ra theo từng version, trạng thái, điểm chất lượng và liên kết sản phẩm.

### 3.2. `table_ai_content_job`
- Hàng đợi tác vụ sinh nội dung nền (Background Content Jobs), hỗ trợ xử lý theo lô (batch generation) không lo timeout.

### 3.3. `table_product_content_backup`
- Lưu vết lịch sử thay đổi nội dung trên `table_product` và `table_seo` để có thể hoàn tác (revert) bất kỳ lúc nào.

---

## 4. CÁC MODULE CODE CẦN XÂY DỰNG

1. **`libraries/class/class.AIContentEngine.php`**:
   - Tổng hợp dữ liệu đầu vào đa nguồn (Product, Research, Evidence, Affiliate, Specs).
   - Tạo `source_hash` xác thực.
   - Quản lý các Prompt Templates có versioning (`product-analysis-v1`, `tiktok-hook-v1`, `tiktok-script-v1`, `seo-v1`, `review-draft-v1`, `faq-v1`).
   - Rule-based Quality Gate Validator (kiểm tra fake reviews, fake test claims, fake metrics, medical claims, empty output).
   - Quản lý Versioning (`getNextVersion`, `setActiveVersion`).
   - Áp dụng vào Sản phẩm (`applyToProduct`) kèm sao lưu (`backupCurrentContent`).
2. **`libraries/class/class.AIContentJobQueue.php`**:
   - Quản trị vòng đời hàng đợi content job (`createJob`, `createBatchJobs`, `getNextPendingJob`, `executeJob`, `recoverStaleJobs`).
3. **`cron/ai_content_worker.php`**:
   - CLI background worker xử lý hàng đợi theo lô.
4. **Admin Module (`admin/sources/ai_content.php` & templates)**:
   - **Kho nội dung (Content Library)**: Lọc theo Product, Content Type, Status, Language, Version.
   - **Xem chi tiết & Kiểm duyệt (Review & Quality Check View)**: Xem structured data, shot plan, quality check status, nút Duyệt/Từ chối.
   - **So sánh & Áp dụng (Diff & Apply to Product View)**: So sánh trực quan Current Product Content vs Approved AI Content trước khi áp dụng.
   - **Tạo nội dung (Generate Form / Batch Generation)**: Chọn sản phẩm, chọn loại nội dung, tone giọng, thời lượng video, góc tiếp cận.
   - **Hàng đợi Content Jobs (Jobs Monitor)**: Theo dõi tiến độ, thời gian chạy, token usage, nút thử lại.
   - **Cấu hình & Prompts**: Xem danh mục prompt versions, daily quota, default model.

---

## 5. KẾ HOẠCH KIỂM THỬ (TEST PLAN)

1. **`test_phase05.php`**: Test suite tự động bao phủ 12 nhóm kiểm thử:
   - Input Aggregation & Source Hash
   - Content Versioning (v1, v2 không ghi đè)
   - Quality Gate: Fake review flag
   - Quality Gate: Fake personal test claim flag
   - Quality Gate: Fake metrics / missing data preservation
   - Product Analysis generation
   - TikTok Hooks generation (7 strategic variations)
   - TikTok Script (30s) + Shot Plan structure
   - SEO metadata & FAQ generation
   - Approval workflow & Active version selection
   - Apply to Product with Backup & Reversible state
   - Outdated detection when source facts change
   - Background Job Queue execution & worker
   - XSS protection & Prompt injection isolation
2. **Kiểm thử Hồi quy (Regression Suite)**:
   - `test_phase04.php` (35 tests PASS)
   - `test_phase03.php` (26 tests PASS)
   - `test_regression.php` (5 tests PASS)
   - `php -l` 100% PASS trên tất cả các file PHP mới và sửa đổi.
3. **Git Auto-Push**:
   - Đẩy toàn bộ code lên `https://github.com/vansangnina/khoepro`.
