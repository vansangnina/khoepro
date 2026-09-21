---
name: fitnado-ai-content
description: FITNADO AI Content Engine Skill - Quy trình sinh nội dung AI đa kênh, kiểm định chất lượng và áp dụng vào sản phẩm
---

# FITNADO AI CONTENT ENGINE SKILL

## 1. TỔNG QUAN HỆ THỐNG
**AI Content Engine (Phase 05)** là hệ thống sinh nội dung AI chuyên nghiệp cho FITNADO, tổng hợp dữ liệu thực tế từ:
- `table_product` (Thuộc tính, phân loại, thông số kỹ thuật)
- `table_product_research` (Dữ liệu nghiên cứu thị trường, điểm số 5 chiều, pain point giải pháp)
- `table_product_research_evidence` (Bằng chứng thực tế từ sàn/crawler)
- `table_product_affiliate` (Ưu đãi đa sàn, Best Deal, mã giảm giá)
- `table_comment` (Đánh giá người dùng thật đã được duyệt)

---

## 2. QUY TẮC BẤT DI BẤT DỊCH (NON-NEGOTIABLE CORE RULES)

1. **FACT > AI ANALYSIS**:
   - Dữ liệu thực tế sàn là chân lý tối thượng.
   - AI không bao giờ được tự bịa đặt số liệu lượt bán (`sales_count`), đánh giá sao (`rating`), số lượng review (`review_count`), tỷ lệ hoa hồng (`commission_rate`).
   - Nếu dữ liệu nguồn không có, bắt buộc để `NULL`.
2. **UNKNOWN = UNKNOWN**:
   - Thông tin chưa biết phải giữ nguyên là chưa biết, không được suy diễn thành sự thật.
3. **CẤM TỰ XƯNG TRẢI NGHIỆM CÁ NHÂN (NO FAKE REAL-TEST CLAIM)**:
   - Cấm các câu: *"Tôi đã dùng sản phẩm 30 ngày..."*, *"FITNADO đã test thực tế..."* trừ khi hệ thống có bằng chứng `REAL_TEST` trong `table_product_research_evidence`.
4. **CẤM TẠO LỜI CHỨNG THỰC GIẢ (NO FAKE REVIEWS / TESTIMONIALS)**:
   - Trích dẫn nhận xét người dùng chỉ được lấy từ `table_comment` thật đã duyệt `hienthi`.
5. **AN TOÀN Y TẾ & THỂ LỰC (FITNESS SAFETY)**:
   - Tuyệt đối không đưa ra cam kết điều trị y khoa: *"chữa đau lưng"*, *"trị dứt điểm thoát vị đĩa đệm"*, *"cam kết tăng 5kg cơ bắp"*.
   - Ưu tiên các từ ngữ chuẩn mực: *"hỗ trợ bảo vệ"*, *"phù hợp cho"*, *"giúp giảm áp lực lên cổ tay"*.
6. **CỔNG DUYỆT CON NGƯỜI (STRICT HUMAN GATE)**:
   ```text
   GENERATED → REVIEW_REQUIRED → APPROVED → APPLIED
   ```
   - Nội dung mới tạo luôn dừng ở `REVIEW_REQUIRED`.
   - Tuyệt đối không auto-publish ra ngoài website nếu chưa có phê duyệt từ Admin.
7. **PHIÊN BẢN HÓA & KHÔNG GHI ĐÈ (VERSION EVERYTHING & ZERO OVERWRITE)**:
   - Mỗi lần generate tạo ra version mới (`v1, v2, v3...`), giữ nguyên các version cũ trong lịch sử.
   - Chỉ duy nhất 1 version được kích hoạt `is_active = 1` tại một thời điểm.
8. **SAO LƯU TRƯỚC KHI ÁP DỤNG (REVERSIBLE BACKUP)**:
   - Khi áp dụng nội dung AI vào sản phẩm, hệ thống bắt buộc tự động sao lưu nội dung cũ vào `table_product_content_backup`.
9. **OUTDATED DETECTION**:
   - Sử dụng `source_hash` SHA-256 xác định tính toàn vẹn của dữ liệu đầu vào. Khi thông tin nghiên cứu thay đổi lớn, nội dung cũ tự động gắn cờ `is_outdated = 1`.

---

## 3. DANH MỤC GÓI NỘI DUNG (CONTENT PACKAGE)

| Loại nội dung | Prompt Version | Output & Mục tiêu |
|---|---|---|
| `product_analysis` | `product-analysis-v1` | Phân tích 12 khía cạnh: problem_solved, target_audience, key_benefits, limitations, pros, cons, suitable_for, not_suitable_for, buying_considerations, comparison_angles, risk_notes, confidence |
| `tiktok_hooks` | `tiktok-hooks-v1` | 7 loại hook chiến lược: Problem, Mistake, Comparison, Curiosity, Demo, Buyer Warning, Value |
| `tiktok_script` | `tiktok-script-v1` | Kịch bản 15s/30s/45s/60s kèm bảng phân cảnh Video Shot Plan chi tiết từng cảnh (Cầu nối Phase 06) |
| `seo_content` | `seo-v1` | Primary/Secondary Keywords, Search Intent, SEO Title, Meta Description, Outline, FAQ Schema |
| `review_draft` | `review-draft-v1` | Bản thảo bài viết đánh giá chuyên sâu hoàn chỉnh với các Heading H2/H3 |
| `faq` | `faq-v1` | Bộ câu hỏi thường gặp và hướng dẫn sử dụng / bảo quản |

---

## 4. QUY TRÌNH ÁP DỤNG VÀO SẢN PHẨM (APPLY TO PRODUCT WORKFLOW)

```text
Admin chọn nội dung đã APPROVED
       ↓
Màn hình So Sánh Trực Quan (Diff View: Current Content vs AI Content)
       ↓
Admin nhấn "Xác nhận Áp Dụng"
       ↓
Tạo bản sao lưu vào table_product_content_backup
       ↓
Cập nhật table_product hoặc table_seo
       ↓
Chuyển trạng thái nội dung thành APPLIED
```

---

## 5. KHOEPRO CONTENT CREATION & SEO MANDATORY RULES

### 5.1. KHOEPRO CONTENT CREATION RULE
Whenever creating a new:
- Product
- Product Category
- News Category
- News Article
- Review
- Comparison
- Buying Guide
- Static Content
- SEO Landing Page

the task is **NOT COMPLETE** until all applicable SEO and image fields are populated.

**Required Checklist:**
1. Human-readable name/title.
2. Unique slug / tenkhongdau.
3. Description.
4. Full content when applicable.
5. Main image.
6. Correct image dimensions from project config (`libraries/config-type.php`).
7. Semantic image filename based on slug (`{slug}.jpg`).
8. Image ALT (natural description, no keyword stuffing).
9. SEO Title (unique per page/item).
10. SEO Description (unique per page/item).
11. Open Graph/Social image.
12. 1200x630 Facebook share derivative (`{slug}-facebook.jpg`) when the main image is not suitable.
13. Files must physically exist inside the website's managed upload/storage directory (`upload/product/`, `upload/news/`, `upload/seopage/`, `upload/photo/`).
14. Never use temporary external image URLs.
15. Never hardcode production content into templates.
16. Data must be manageable from Admin/database.
17. No Data → No Render.
18. No fake product images.
19. No fake ratings/reviews.
20. No unsupported product or health claims.
21. Verify frontend metadata after creation (Title, Description, Canonical, OG, Twitter).

### 5.2. IMAGE NAMING RULE
- Primary image: `{slug}.{ext}`
- Gallery images: `{slug}-1.{ext}`, `{slug}-2.{ext}` or descriptive: `{slug}-mat-truoc.{ext}`, `{slug}-chi-tiet.{ext}`
- Facebook / Open Graph (1200x630): `{slug}-facebook.{ext}`
- Conventions: `lowercase`, `không dấu`, `hyphen-separated`, `semantic`. No junk filenames (`image1.jpg`, `img_1234.jpg`, `tmp.jpg`).

### 5.3. CONFIG RULE (NEVER GUESS IMAGE DIMENSIONS)
Before creating or processing an image, read the relevant configuration in `/config/` / `libraries/config-type.php`:
- Product image dimensions: from product config.
- News/review/article: from news/content config.
- Photo/banner: from photo config.
- Static: from static config.
- Open Graph / Facebook: 1200x630 social derivative (ratio 1.91:1).

### 5.4. SKILL COMPLETION RULE (SEO GATE)
AI cannot report `DONE`, `PASS`, or `COMPLETE` for any new/updated Product, Article, Category, or SEO Page if it is missing any SEO/image requirement. It MUST report `INCOMPLETE` with the exact list of missing fields.

