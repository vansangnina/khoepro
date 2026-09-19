# THÔNG TIN DỰ ÁN FITNADO (PROJECT CONTEXT)

## 1. TỔNG QUAN DỰ ÁN

* **Tên dự án**: FITNADO
* **Slogan định vị**: *FIND BETTER GEAR. TRAIN BETTER.* / *GEAR BETTER. A STRONGER YOU.*
* **Lĩnh vực hoạt động**: Khám phá, Đánh giá, So sánh sản phẩm Gym/Fitness & Tiếp thị liên kết (Product Discovery, Real Review, Product Comparison, Affiliate Marketing, TikTok Content, AI Video & SEO).
* **Mục tiêu kinh doanh**: Giúp người tập gym (Gymmer, Fitness Enthusiasts) tìm đúng phụ kiện/thiết bị tập luyện chất lượng cao, tránh mua nhầm hàng kém chất lượng, tiết kiệm thời gian và tiền bạc thông qua đánh giá đa chiều, video trực quan và liên kết mua hàng tin cậy.

---

## 2. ĐỊNH HƯỚNG CÁC GIAI ĐOẠN PHÁT TRIỂN (ROADMAP)

Hệ sinh thái FITNADO phát triển theo chu trình khép kín:
```text
Product Research (Nghiên cứu SP)
      ↓
Product Curation (Chọn lọc & Kiểm định)
      ↓
Content & In-depth Review (Phân tích, So sánh, Đánh giá)
      ↓
AI Video Creation (Video ngắn 30s trực quan cho TikTok/Web)
      ↓
TikTok & Multi-channel Distribution (Phân phối đa kênh)
      ↓
Affiliate Traffic & Conversion (Điều hướng mua sắm Affiliate)
      ↓
Analytics & Winner Detection (Đo lường & Xác định sản phẩm đột phá)
```

> **Lưu ý**: Kênh TikTok đã được tạo sẵn bên ngoài. Hệ thống không cần xây dựng module tạo kênh, mà tập trung vào tối ưu trải nghiệm khám phá sản phẩm, bài viết review, video embed/player, affiliate link management và tracking.

---

## 3. NỀN TẢNG KỸ THUẬT HIỆN TẠI (SYSTEM STACK)

* **Ngôn ngữ lập trình**: PHP 7.4 (**Hard Requirement** - không dùng cú pháp PHP 8+).
* **Hệ quản trị cơ sở dữ liệu**: MySQL / MariaDB (Engine MyISAM/InnoDB, collation `utf8mb4_unicode_ci`, prefix `table_`).
* **Framework / Kiến trúc gốc**: Nina MasterPDO Architecture (tùy biến trên nền tảng AltoRouter, PDODb wrapper, template include).
* **Quản lý thư viện**: Composer (`vendor/` chứa alto-router, mobile-detect, phpmailer, php-image-magician...).
* **Frontend**: Vanilla CSS / Scss (`assets/css/fitnado.css`, `style.css`), Vanilla JavaScript / jQuery, LightGallery, Slick Carousel.
* **Giao diện quản trị (Admin)**: AdminLTE dựa trên Nina MasterPDO (`/admin/`), phân quyền group permission, CKEditor, CKFinder, quản lý đa ngôn ngữ (vi/en).

---

## 4. RÀNG BUỘC KỸ THUẬT NGHIÊM NGẶT

1. **Tuân thủ source hiện tại**: Mọi tính năng mới (Affiliate, Video Review, Product Spec Compare, Goal-based Discovery...) phải tích hợp trực tiếp vào codebase hiện tại thông qua các config type, sources, templates và admin module chuẩn.
2. **Không phân tách project**: Không tạo sub-project độc lập (no separate Next.js/Laravel app song song).
3. **Bảo toàn tính năng hiện hữu**: Không làm gãy luồng xử lý của các module tin tức, album, static page, SEO, cache và routing đã ổn định.
