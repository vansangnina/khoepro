# FITNADO IMPLEMENTATION PLAN: PHASE 01 — FRONTEND FOUNDATION + HOMEPAGE + REAL DATABASE INTEGRATION

* **Ngày lập**: 18/09/2026
* **Trạng thái**: APPROVED & EXECUTING
* **Mục tiêu**: Tích hợp hoàn chỉnh thiết kế FITNADO (Desktop & Mobile Prototypes) vào hệ thống mã nguồn hiện tại, kết nối trực tiếp với dữ liệu thật trong MySQL database, đảm bảo tương thích 100% PHP 7.4.

---

## 1. MAPPING TỔNG THỂ (BACKEND & DATABASE & PROTOTYPE MAPPING)

| Khối giao diện (Section) | Nguồn Prototype | Bảng Database thật | Controller Source & Logic | Template đích |
| :--- | :--- | :--- | :--- | :--- |
| **Top Header & Search** | `top`, `search`, action buttons | `table_photo` (logo), `table_static` (slogan), `table_setting` | `sources/allpage.php` | `templates/layout/header.php` |
| **Navigation Bar** | `nav` bar | `table_product_list`, `table_news_list` | `sources/allpage.php` (`$productListMenu`, `$newsListMenu`) | `templates/layout/menu.php` |
| **Hero Section** | `hero`, `heroGrid`, `visual`, `stats` | `table_photo` (`type='slide'`), `table_product` (`count`), `table_news` (`count`), `table_counter` | `sources/index.php` (`$slider`, `$countProduct`, `$countNews`, `$counter`) | `templates/layout/slide.php` |
| **Categories Grid** | `cats`, `cat` (8 items) | `table_product_list` (`type='san-pham'`, `status contains 'hienthi'`) | `sources/allpage.php` (`$productListMenu`) / `sources/index.php` (`$proListHot`) | `templates/index/index_tpl.php` |
| **Featured Products** | `products`, `card`, rating, price, CTA | `table_product` (`type='san-pham'`, `status contains 'hienthi,noibat'`) | `sources/index.php` (`$productHot`) | `templates/index/index_tpl.php` |
| **30s Review Videos** | `videos`, `video`, play badge | `table_photo` (`type='video'`, `status contains 'hienthi'`) | `sources/index.php` (`$videoHot`) | `templates/index/index_tpl.php` |
| **Training Goals** | `goals`, `goal` (Tăng cơ, Giảm mỡ...) | `table_tags` (`type='san-pham'`) / `table_product_list` | `sources/allpage.php` (`$tagsProduct`) / `sources/index.php` | `templates/index/index_tpl.php` |
| **Product Comparison** | `compare`, tiêu chí so sánh | `table_product` (So sánh 2 sản phẩm nổi bật) | `sources/index.php` (`$productHot`) | `templates/index/index_tpl.php` |
| **Knowledge & Guides** | `articles`, `article` | `table_news` (`type='tin-tuc'`, `status contains 'hienthi'`) | `sources/index.php` (`$newsHot`) | `templates/index/index_tpl.php` |
| **Newsletter Box** | `newsletter` banner gradient + form | `table_newsletter` | `sources/allpage.php` (POST `submit-newsletter`) | `templates/index/index_tpl.php` |
| **Footer & Social** | `footer`, `foot`, 5 cột | `table_static` (`footer`, `copyright`), `table_photo` (`social`), `table_news` (`chinh-sach`) | `sources/allpage.php` (`$footer`, `$social`, `$policy`) | `templates/layout/footer.php` |
| **Mobile Bottom Bar** | `bottom` fixed 5 tabs | Cố định route: Home, Category, Video, Cart, Account | `templates/layout/phone.php` | `templates/layout/phone.php` |

---

## 2. DANH SÁCH FILE LIÊN QUAN (AFFECTED FILES)

### Files to Inspect & Validate:
1. `assets/css/fitnado.css`: Hệ thống CSS tokens, cards, components, grid, responsive.
2. `templates/layout/css.php`: Nạp `css/fitnado.css`.
3. `templates/layout/header.php`: Header logo, search bar, login/cart quick buttons.
4. `templates/layout/menu.php`: Menu dropdown động cấp 1/cấp 2.
5. `templates/layout/slide.php`: Hero section với số liệu dynamic stats & hero banner.
6. `templates/layout/footer.php`: Footer 5 cột, link chính sách, mạng xã hội, copyright.
7. `templates/layout/phone.php`: Bottom Navigation Bar cố định trên mobile.
8. `templates/index/index_tpl.php`: Homepage với toàn bộ 7 section nội dung chuẩn.
9. `sources/index.php`: Query dữ liệu trang chủ từ Database thật.
10. `sources/allpage.php`: Query dữ liệu toàn trang.

---

## 3. CHI TIẾT CÁC BƯỚC THỰC HIỆN

### Bước 1: Kiểm tra tính toàn vẹn của Data Layer (`sources/index.php` & `sources/allpage.php`)
* Đảm bảo mọi câu truy vấn dùng hàm chuẩn của `$d` hoặc `$cache`.
* Bổ sung fallback query an toàn khi `find_in_set('noibat', status)` rỗng để homepage luôn có dữ liệu hiển thị mượt mà.

### Bước 2: Tối ưu và hoàn thiện Template Components
* Đảm bảo nguyên tắc "No Data -> No Render" cho tất cả các khối trong `templates/index/index_tpl.php`.
* Tối ưu Lazy loading hình ảnh thông qua `class="lazy"` và thumbnail URL `thumbs/{w}x{h}x{z}/upload/...`.
* Đồng bộ class và layout giữa Desktop và Mobile.

### Bước 3: Kiểm thử Cú pháp & Tương thích PHP 7.4
* Chạy lệnh `php -l` đối với tất cả các file PHP liên quan.

### Bước 4: Kiểm thử Trình duyệt & Đa kích thước màn hình (Browser & Responsive Testing)
* Kiểm tra layout trên các độ phân giải: `1920px`, `1440px`, `1366px`, `1024px`, `768px`, `430px`, `390px`, `375px`, `360px`.

### Bước 5: Báo cáo & Cập nhật Tri thức
* Cập nhật `.ai/CHANGELOG.md`, `.ai/knowledge/LESSONS_LEARNED.md` và tạo `.ai/reports/PHASE_01_TEST_REPORT.md`.
