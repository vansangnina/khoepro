# FITNADO IMPLEMENTATION PLAN: PHASE 02 — PRODUCT DETAIL + REVIEW + COMPARISON + AFFILIATE FOUNDATION

* **Dự án**: FITNADO — *Find Better Gear. Train Better.*
* **Giai đoạn**: Phase 02 — Product Detail + Review + Comparison + Affiliate Foundation
* **Ngày lập**: 19/09/2026
* **Trạng thái**: PLANNING & READY FOR EXECUTION
* **Hard Requirement**: PHP 7.4, Nina MasterPDO Architecture, MySQL, Non-destructive Database Migration, No Mockup Data.

---

## 1. MỤC TIÊU & PHẠM VI TRIỂN KHAI

Xây dựng nền tảng chuyển đổi doanh thu Affiliate và trải nghiệm chi tiết sản phẩm toàn diện cho FITNADO:
```text
PRODUCT (table_product)
   ↓
PRODUCT DETAIL (Gallery, Real Rating, Specifications, Pros/Cons, Real Test)
   ↓
AFFILIATE OFFERS (Shopee, TikTok Shop, Lazada, Brand Web - Multi-offer per product)
   ↓
CLICK TRACKING & REDIRECT (/go/[id] -> Log Click -> 302 Redirect)
   ↓
PRODUCT COMPARISON (/so-sanh/ -> Product A vs Product B Matrix)
   ↓
USER REVIEWS (table_comment & Comments Class integration)
```

---

## 2. KIẾN TRÚC & MAPPING DỮ LIỆU

### 2.1. Affiliate Foundation & Data Model
* **Bảng mới 1**: `table_product_affiliate`
  * `id` (int unsigned, PK, AI)
  * `id_product` (int unsigned, FK logic to `table_product.id`)
  * `platform` (varchar 50): `tiktok_shop`, `shopee`, `lazada`, `brand`, `other`
  * `seller_name` (varchar 255): Tên gian hàng / shop chính hãng
  * `affiliate_url` (mediumtext): URL affiliate tracking (có UTM / partner tags)
  * `original_url` (mediumtext): Link gốc sản phẩm
  * `price` (double): Giá bán tham khảo tại sàn
  * `commission_rate` (double): % hoa hồng dự kiến
  * `commission_value` (double): Giá trị hoa hồng dự kiến (VNĐ)
  * `priority` (int): Thứ tự ưu tiên (ưu tiên cao nhất hiển thị Best Offer)
  * `status` (varchar 50): `hienthi`, `tamngung`
  * `date_created`, `date_updated` (int)

* **Bảng mới 2**: `table_affiliate_click`
  * `id` (int unsigned, PK, AI)
  * `id_product` (int unsigned)
  * `id_affiliate` (int unsigned)
  * `platform` (varchar 50)
  * `source_page` (varchar 50): `product_detail`, `homepage`, `comparison`, `article`
  * `device_type` (varchar 20): `mobile`, `tablet`, `desktop`
  * `referer` (varchar 500): Nguồn truy cập
  * `ip_hash` (varchar 64): Mã hóa SHA256 IP phục vụ chống click fraud tối thiểu
  * `user_agent` (varchar 255)
  * `date_created` (int)

* **Central Platform Config**: `libraries/config-affiliate.php`
  * Định nghĩa danh sách các nền tảng tập trung:
    * `tiktok_shop` => 'TikTok Shop' (icon / badge)
    * `shopee` => 'Shopee'
    * `lazada` => 'Lazada'
    * `brand` => 'Website thương hiệu'
    * `other` => 'Khác'

### 2.2. Product Review & Metadata Fields (Mở rộng `table_product`)
Để hỗ trợ `PROS / CONS`, `SUITABLE_FOR`, và phân loại đánh giá (`AI_ANALYSIS`, `EDITOR_REVIEW`, `REAL_TEST`, `USER_REVIEW`):
* Bổ sung các cột vào `table_product` (Non-destructive):
  * `pros_vi`, `pros_en` (text): Điểm mạnh (mỗi dòng 1 điểm)
  * `cons_vi`, `cons_en` (text): Điểm yếu
  * `suitable_vi`, `suitable_en` (text): Phù hợp cho ai (Đối tượng khuyên dùng)
  * `specifications_vi`, `specifications_en` (mediumtext): Bảng thông số kỹ thuật chi tiết
  * `review_type` (varchar 50): Phân loại đánh giá (`AI_ANALYSIS`, `EDITOR_REVIEW`, `REAL_TEST`)
  * `is_real_test` (tinyint 1): Cờ xác nhận đã test thực tế tại phòng gym (kèm video/ảnh)

### 2.3. Click Tracking & Outbound Redirect Router
* Route AltoRouter: `$router->map('GET', 'go/[i:id]', 'affiliate_redirect', 'affiliate_go');`
* Luồng xử lý (`sources/affiliate.php`):
  1. Kiểm tra `$id` tồn tại trong `table_product_affiliate` và có `find_in_set('hienthi', status)`.
  2. Validate `affiliate_url` (bắt buộc `http://` hoặc `https://`, loại bỏ `javascript:`, `data:`).
  3. Ghi nhận click vào `table_affiliate_click` (source_page, device_type, referer, timestamp).
  4. Header redirect: `HTTP/1.1 302 Found` -> chuyển hướng an toàn tới sàn thương mại điện tử.

### 2.4. Product Detail Architecture (`templates/product/product_detail_tpl.php`)
* **Above The Fold**:
  * Desktop: Cột trái (Gallery ảnh trượt + thumbnails) | Cột phải (Tên SP, Brand, Real Rating từ `table_comment`, Giá tham khảo, Mô tả ngắn, Best Offer CTA nổi bật với rel="nofollow sponsored", danh sách nơi mua).
  * Mobile: Gallery -> Tiêu đề -> Rating -> Giá -> Best Offer CTA chạm nhanh -> Danh sách sàn TMĐT.
* **Middle & Below Sections**:
  * Tab / Blocks:
    * Đánh giá chi tiết (Editorial Content từ `contentvi`).
    * Điểm mạnh / Điểm yếu (Pros & Cons Card trực quan).
    * Đối tượng phù hợp (Suitable For).
    * Thông số kỹ thuật (Specifications Table).
    * Video Review thực tế (nếu có trong `table_gallery` hoặc `table_photo`).
    * Real User Reviews (Tích hợp `Comments` class, điểm TB, tỷ lệ sao, gửi đánh giá mới).
    * Sản phẩm tương tự cùng danh mục (Related Products).
    * Bài viết kiến thức / cẩm nang liên quan (`table_news`).
* **Quy tắc No Data -> No Render**: Chỉ render các section khi có dữ liệu thật từ DB.

### 2.5. Comparison Foundation (`/so-sanh`)
* **Routing**:
  * Thêm route `/so-sanh` vào `libraries/router.php`.
  * Hỗ trợ so sánh nhanh 2 sản phẩm: `/so-sanh?id1=1&id2=2`.
* **Data Matrix**:
  * Tự động lấy trực tiếp thông tin từ 2 sản phẩm (`table_product`): Tên, Ảnh, Giá, Rating thật, Điểm mạnh/yếu, Thông số kỹ thuật, Best Affiliate Offer.
  * Không tạo winner giả (không tự ý ghi "Winner/Best" nếu không có tiêu chí khách quan).
* **Responsive**:
  * Desktop: Bảng so sánh 3 cột (Sản phẩm 1 - Tiêu chí - Sản phẩm 2).
  * Mobile: Stacked view hoặc 2 cột cuộn bảng độc lập, không tràn viền ngang toàn trang.

### 2.6. Admin Product & Affiliate Management
* **Trong Product Edit (`admin/sources/product.php` & `admin/templates/product/man/man_add_tpl.php`)**:
  * Thêm Card / Tab **"Nơi mua / Affiliate Offers"**:
    * Quản lý danh sách offer của sản phẩm (Thêm, Sửa, Xóa, Bật/Tắt, Thứ tự ưu tiên, Nền tảng, Tên Shop, Link Affiliate, Giá, Commission).
  * Thêm Card / Tab **"Đánh giá & Thông số"**:
    * Nhập Pros (Ưu điểm), Cons (Nhược điểm), Suitable For (Đối tượng phù hợp), Thông số kỹ thuật.
    * Checkbox xác nhận "Đã kiểm định thực tế" (`is_real_test`) và Review Type.
* **Module Báo cáo Click Affiliate Admin (`admin/sources/affiliate.php`)**:
  * Menu Admin "Tiếp thị liên kết" -> Xem danh sách lượt click theo Sản phẩm, Nền tảng, Nguồn click, Thiết bị, Thời gian.

---

## 3. DANH SÁCH FILE THAY ĐỔI & TẠO MỚI

### Files to Create:
1. `database/migrations/phase_02_affiliate_product.sql`
2. `libraries/config-affiliate.php`
3. `sources/affiliate.php`
4. `sources/compare.php`
5. `templates/product/compare_tpl.php`
6. `admin/sources/affiliate.php`
7. `admin/templates/affiliate/mans_tpl.php`
8. `.ai/plans/PHASE-02-PRODUCT-AFFILIATE.md`
9. `.ai/reports/PHASE-02-TEST-REPORT.md`

### Files to Modify:
1. `libraries/router.php`
2. `libraries/type/config-type-product.php`
3. `sources/product.php`
4. `templates/product/product_detail_tpl.php`
5. `assets/css/fitnado.css`
6. `admin/sources/product.php`
7. `admin/templates/product/man/man_add_tpl.php`
8. `admin/templates/layout/menu.php`
9. `.ai/DATABASE.md`
10. `.ai/BUSINESS_RULES.md`
11. `.ai/CHANGELOG.md`
