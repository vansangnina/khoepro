---
name: fitnado-core
description: Core skill bắt buộc phải nạp và tuân thủ cho tất cả các tác vụ lập trình trên dự án FITNADO.
---

# FITNADO CORE SKILL

Skill này định hình toàn bộ quy trình làm việc, tư duy và nguyên tắc nền tảng khi can thiệp vào mã nguồn FITNADO.

## 1. TÀI LIỆU BẮT BUỘC PHẢI ĐỌC TRƯỚC KHI LÀM VIỆC

Khi thực thi bất kỳ task nào, AI Agent **PHẢI** đọc qua các tài liệu sau trong thư mục `.ai/`:
1. `.ai/README.md`
2. `.ai/PROJECT.md`
3. `.ai/ARCHITECTURE.md`
4. `.ai/DATABASE.md`
5. `.ai/BUSINESS_RULES.md`
6. `.ai/CODING_RULES.md`
7. `.ai/knowledge/ERRORS.md`
8. `.ai/knowledge/DECISIONS.md`
9. `.ai/knowledge/LESSONS_LEARNED.md`

---

## 2. QUY TRÌNH BẮT BUỘC 8 BƯỚC (THE MANDATORY WORKFLOW)

```text
1. UNDERSTAND       -> Hiểu rõ yêu cầu, phạm vi và mục tiêu kinh doanh
2. LOCATE           -> Xác định chính xác file source, config, database table và template liên quan
3. PLAN             -> Tạo kế hoạch thực thi rõ ràng (Implementation Plan)
4. MINIMUM CHANGE   -> Chỉ chỉnh sửa đúng phạm vi cần thiết, tương thích 100% PHP 7.4
5. TEST             -> Chạy kiểm tra cú pháp php -l và kiểm tra chức năng
6. REGRESSION       -> Kiểm tra các module phụ thuộc xung quanh
7. UPDATE KNOWLEDGE -> Ghi nhận bài học hoặc lỗi mới vào .ai/knowledge/
8. CHANGELOG        -> Cập nhật nhật ký công việc vào .ai/CHANGELOG.md
9. GIT SYNC         -> Tự động commit và push toàn bộ thay đổi lên GitHub (origin/main)
```

---

## 3. RÀNG BUỘC KỸ THUẬT KHÔNG THỂ THƯƠNG LƯỢNG

* **PHP 7.4 Hard Requirement**: Không dùng cú pháp PHP 8+ (`match`, `str_contains`, `str_starts_with`, `?->`, `union types`, `named arguments`...).
* **Không làm gãy kiến trúc cũ**: Sử dụng `$d` (PDODb), `$func` (Functions), `$cache` (Cache), AltoRouter và template includes.
* **No Mockup in PHP**: Không bao giờ hardcode dữ liệu giả trên Frontend.

---

## 4. KHOEPRO CONTENT CREATION & SEO MANDATORY RULES

### 4.1. KHOEPRO CONTENT CREATION RULE
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
7. Semantic image filename based on slug.
8. Image ALT.
9. SEO Title.
10. SEO Description.
11. Open Graph/Social image.
12. 1200x630 Facebook share derivative when the main image is not suitable.
13. Files must physically exist inside the website's managed upload/storage directory (`upload/product/`, `upload/news/`, `upload/seopage/`, `upload/photo/`).
14. Never use temporary external image URLs.
15. Never hardcode production content into templates.
16. Data must be manageable from Admin/database.
17. No Data → No Render.
18. No fake product images.
19. No fake ratings/reviews.
20. No unsupported product or health claims.
21. Verify frontend metadata after creation (Title, Description, Canonical, OG, Twitter).

### 4.2. IMAGE NAMING RULE
- Primary image: `{slug}.{ext}`
- Gallery images: `{slug}-1.{ext}`, `{slug}-2.{ext}` or descriptive: `{slug}-mat-truoc.{ext}`, `{slug}-chi-tiet.{ext}`
- Facebook / Open Graph (1200x630): `{slug}-facebook.{ext}`
- Conventions: `lowercase`, `không dấu`, `hyphen-separated`, `semantic`. No junk filenames (`image1.jpg`, `img_1234.jpg`, `tmp.jpg`).

### 4.3. CONFIG RULE (NEVER GUESS IMAGE DIMENSIONS)
Before creating or processing an image, read the relevant configuration in `/config/` / `libraries/config-type.php`:
- Product image dimensions: from product config.
- News/review/article: from news/content config.
- Photo/banner: from photo config.
- Static: from static config.
- Open Graph / Facebook: 1200x630 social derivative (ratio 1.91:1).

### 4.4. SKILL COMPLETION RULE (SEO GATE)
AI cannot report `DONE`, `PASS`, or `COMPLETE` for any new/updated Product, Article, Category, or SEO Page if it is missing any SEO/image requirement. It MUST report `INCOMPLETE` with the exact list of missing fields.

---

## 5. QUY TẮC TỰ ĐỘNG ĐỒNG BỘ GITHUB (MANDATORY GIT AUTO-PUSH)

* **Bắt buộc đồng bộ**: Sau khi hoàn thành bất kỳ tác vụ nào (tính năng mới, sửa lỗi, cập nhật tài liệu, test...), Agent **BẮT BUỘC** phải tự động chạy lệnh commit và đẩy lên GitHub remote:
  ```bash
  git add .
  git commit -m "<type>: <mô tả thay đổi súc tích>"
  git push origin main
  ```
* **Repository đích**: `https://github.com/vansangnina/khoepro` (branch `main`).
* **Không để đọng thay đổi (Zero Uncommitted Changes)**: Kết thúc mỗi phiên làm việc, working tree phải hoàn toàn sạch (`working tree clean`).

