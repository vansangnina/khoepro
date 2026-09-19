# BÁO CÁO KIỂM TOÁN MÃ NGUỒN (SOURCE CODE AUDIT REPORT)

* **Dự án**: FITNADO (`masterpdo`)
* **Ngày kiểm toán**: 18/09/2026
* **Môi trường**: PHP 7.4 / Apache / Nginx / MySQL

---

## 1. CÁC ĐIỂM VÀO HỆ THỐNG (ENTRY POINTS)

* **Frontend Entry Point**: `/index.php`
  * Khởi tạo session, nạp `libraries/config.php`, `libraries/autoload.php`.
  * Khởi tạo các instance dịch vụ: `$d` (PDODb), `$func` (Functions), `$cache` (Cache), `$seo` (Seo), `$router` (AltoRouter), `$detect` (MobileDetect).
  * Chuyển quyền điều khiển cho `libraries/router.php` và render qua `templates/index.php`.
* **Admin Entry Point**: `/admin/index.php`
  * Nạp `libraries/requick.php` để xử lý điều phối module quản trị qua query params `com`, `act`, `type`.
  * Kiểm tra authentication qua `$func->checkLoginAdmin()` và phân quyền theo `table_permission`.

---

## 2. KIỂM TOÁN ĐIỀU HƯỚNG & ĐỊNH TUYẾN (ROUTING AUDIT)

* **Router**: Sử dụng thư viện `AltoRouter` kết hợp bảng map tối ưu `$requick` trong `libraries/router.php`.
* **Luồng phân giải Slug**:
  * Router duyệt mảng `$requick` để tra cứu slug tiếng Việt (`slugvi`) trong các bảng:
    * `table_product_list`, `table_product_cat`, `table_product_item`, `table_product_sub`, `table_product_brand`, `table_product`
    * `table_news_list`, `table_news_cat`, `table_news_item`, `table_news_sub`, `table_news`
    * `table_tags`, `table_static`
  * Khi tìm thấy bản ghi tương ứng, router tự động gán ID vào biến `$_GET` (`id`, `idl`, `idc`, `idi`, `ids`, `idb`) và chuyển tiếp đến controller source tương ứng.
* **Controller / Source Mapping**:
  * Trang chủ: `sources/index.php` -> Template `templates/index/index_tpl.php`
  * Sản phẩm: `sources/product.php` -> Template `templates/product/product_tpl.php` hoặc `templates/product/product_detail_tpl.php`
  * Tin tức / Review: `sources/news.php` -> Template `templates/news/news_tpl.php` hoặc `templates/news/news_detail_tpl.php`
  * Video: `sources/video.php` -> Template `templates/video/video_tpl.php`
  * Trang tĩnh: `sources/static.php` -> Template `templates/static/static_tpl.php`

---

## 3. KIỂM TOÁN HỆ THỐNG CẤU HÌNH (CONFIG AUDIT)

* `libraries/config.php`: Chứa database credentials, server URL, debug flags, language config (`vi`, `en`).
* `libraries/config-type.php`: Điểm nạp tập trung toàn bộ cấu hình module.
* `libraries/type/config-type-product.php`: Cấu hình danh mục 4 cấp, brand, color, size, tags, gallery (ảnh/video/tập tin), giá bán, giảm giá, SEO, schema.
* `libraries/type/config-type-news.php`: Cấu hình bài viết tin tức, tuyển dụng, chính sách, hình thức thanh toán.
* `libraries/type/config-type-photo.php`: Cấu hình logo, favicon, banner, slideshow, video, mạng xã hội, watermark.
* `libraries/type/config-type-static.php`: Cấu hình giới thiệu, slogan, copyright, footer, liên hệ.

---

## 4. KIỂM TOÁN GIAO DIỆN & PROTOTYPE (PROTOTYPE AUDIT)

* **Desktop Prototype (`fitnado-desktop.html`)**:
  * Màu chủ đạo: `#0256AA`.
  * Bố cục: Top bar với tìm kiếm -> Navigation bar -> Hero section với stats & CTA -> Danh mục 8 icon -> Sản phẩm quan tâm (5 cột) -> 30 giây review video (6 cột) -> Tìm theo mục tiêu (6 cột) -> So sánh sản phẩm 3 cột -> Kiến thức & hướng dẫn (5 cột) -> Newsletter form -> Footer 5 cột.
* **Mobile Prototype (`fitnado-mobile.html`)**:
  * Bố cục tối ưu màn hình cảm ứng: Header sticky -> Search bar -> Hero box -> Danh mục scroll ngang -> Sản phẩm scroll ngang -> Video scroll ngang -> Mục tiêu grid 2 cột -> So sánh rút gọn -> Danh sách bài viết dạng list dọc -> Newsletter -> Bottom navigation cố định 5 tab.
