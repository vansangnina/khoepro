# QUY CHUẨN PHÁT TRIỂN ADMIN (ADMIN RULES)

Tài liệu này hướng dẫn cách thức mở rộng và phát triển các tính năng trong hệ thống quản trị `/admin/` tuân thủ kiến trúc Nina MasterPDO.

---

## 1. QUY ƯỚC ĐIỀU HƯỚNG ADMIN (ADMIN ROUTING CONVENTIONS)

Admin sử dụng query params chuẩn qua `libraries/requick.php`:
* `com`: Tên module quản trị (tương ứng với file `admin/sources/{$com}.php` và thư mục `admin/templates/{$com}/`).
* `act`: Hành động thực thi (`man`, `add`, `edit`, `save`, `delete`, `man_list`, `add_list`, `edit_list`, `save_list`, `delete_list`...).
* `type`: Phân loại cấu hình (`san-pham`, `tin-tuc`, `slide`, `video`, `gioi-thieu`...).
* `curPage` (`p`): Trang hiện tại trong danh sách phân trang.
* `id`: ID của bản ghi đang sửa hoặc xóa.
* `id_parent`: ID bản ghi cha (khi thao tác với gallery hoặc comment).

Ví dụ URL chuẩn:
* Danh sách sản phẩm: `index.php?com=product&act=man&type=san-pham`
* Thêm mới bài viết: `index.php?com=news&act=add&type=tin-tuc`
* Thư viện ảnh sản phẩm: `index.php?com=product&act=man_photo&id_parent=1&kind=man&type=san-pham`

---

## 2. QUY TRÌNH TẠO / MỞ RỘNG MỘT MODULE ADMIN

Khi cần thêm tính năng mới trong Admin (ví dụ: quản lý Affiliate Links, Video Review, hoặc Thông số so sánh):

1. **Bước 1: Khai báo cấu hình Type (`libraries/type/config-type-*.php`)**:
   * Khai báo các field được phép nhập (`name`, `desc`, `content`, `slug`, `seo`, `images`, `gallery`...).
   * Định nghĩa kích thước ảnh thumbnail chuẩn (`width`, `height`, `thumb`, `img_type`).
   * Định nghĩa checkbox trạng thái (`check` => `hienthi`, `noibat`...).
2. **Bước 2: Viết Controller Source (`admin/sources/{$com}.php`)**:
   * Xử lý switch-case theo `$act` (`man`, `add`, `edit`, `save`, `delete`).
   * Sử dụng các hàm xử lý ảnh `$func->uploadImage()`, upload file `$func->uploadFile()`.
   * Thực hiện validation dữ liệu trước khi insert/update qua `$d`.
   * Gán biến `$template` trỏ đến view tương ứng (ví dụ `$template = "product/man/item_add";`).
3. **Bước 3: Tạo Template View (`admin/templates/{$com}/..._tpl.php`)**:
   * Sử dụng cấu trúc HTML Form chuẩn AdminLTE với các class form group, card, breadcrumb.
   * Tích hợp khung soạn thảo CKEditor (`class="form-control-ckeditor"`).
   * Tích hợp CKFinder cho bộ chọn media.
   * Hiển thị danh sách ảnh gallery kéo thả sắp xếp thứ tự.
4. **Bước 4: Tự động dọn dẹp Cache**:
   * Mọi hành động `save*`, `update*`, `delete*` đều phải gọi `$cache->delete()` để xóa cache frontend.

---

## 3. QUY ƯỚC UPLOAD & QUẢN LÝ TẬP TIN

* Thư mục đích: Khai báo hằng số trong `libraries/constant.php` (ví dụ: `UPLOAD_PRODUCT`, `UPLOAD_NEWS`, `UPLOAD_PHOTO`).
* Xử lý ảnh đại diện:
  * Kiểm tra định dạng file theo whitelist trong config.
  * Đổi tên file duy nhất theo hàm `$func->uploadName($file['name'])`.
  * Tự động tạo metadata `options` lưu kích thước (`w`, `h`, `m`) vào database.
* Xử lý đa ảnh (Gallery):
  * Sử dụng bảng `table_gallery` với `com = $com`, `type = $type`, `kind = $kind`, `id_parent = $id`.

---

## 4. BẢO MẬT & PHÂN QUYỀN (SECURITY & PERMISSIONS)

* **Kiểm tra đăng nhập**: Bắt buộc mọi request admin (trừ `act=login`) phải qua `$func->checkLoginAdmin()`.
* **Phân quyền thao tác**: Hệ thống tự động so khớp quyền truy cập chuỗi `$com . '_' . $act . '_' . $type` với danh sách quyền trong session `$_SESSION[$loginAdmin]['permissions']`.
* **Chống SQL Injection**: Sử dụng prepared statements với tham số bind qua `$d->rawQuery()` hoặc `$d->insert()`, `$d->update()`.
