# TIÊU CHUẨN KIỂM THỬ FITNADO (TESTING & VERIFICATION)

Mọi thay đổi source code trong các phase phát triển của dự án FITNADO đều phải vượt qua đầy đủ các bước kiểm thử dưới đây trước khi bàn giao.

---

## 1. KIỂM TRA CÚ PHÁP & PHP COMPATIBILITY (SYNTAX CHECK)

* Chạy lệnh kiểm tra cú pháp cho tất cả các file PHP được tạo mới hoặc chỉnh sửa:
  ```bash
  php -l <filepath>
  ```
* Đảm bảo không có bất kỳ cú pháp PHP 8+ nào lọt vào codebase.

---

## 2. KIỂM THỬ CHỨC NĂNG (FUNCTIONAL TESTING)

* **Luồng dữ liệu Admin -> Database -> Frontend**:
  1. Thêm/sửa dữ liệu trong giao diện Admin.
  2. Kiểm tra dữ liệu được ghi đúng vào bảng cơ sở dữ liệu (`table_*`).
  3. Kiểm tra frontend hiển thị chính xác dữ liệu vừa nhập mà không bị lỗi layout hay rò rỉ dữ liệu.
* **Xử lý ngoại lệ**:
  * Khi danh sách sản phẩm hoặc bài viết rỗng -> Giao diện hiển thị thông báo thân thiện hoặc ẩn khối hợp lý, không phát sinh warning/notice PHP.

---

## 3. KIỂM THỬ GIAO DIỆN & ĐÁP ỨNG ĐA THIẾT BỊ (RESPONSIVE TESTING)

Mọi component giao diện mới hoặc chỉnh sửa phải được kiểm thử hiển thị mượt mà trên bộ kích thước màn hình tiêu chuẩn:

| Thiết bị | Viewport Width | Yêu cầu hiển thị chính |
| :--- | :--- | :--- |
| **Desktop 4K / Ultrawide** | `1920px` | Khung wrap tối đa 1440px căn giữa, không bị vỡ grid |
| **Desktop Standard** | `1440px`, `1366px` | Hiển thị trọn vẹn theo thiết kế `fitnado-desktop.html` |
| **Tablet Landscape** | `1024px` | Menu và grid tự động thu nhỏ hợp lý |
| **Tablet Portrait** | `768px` | Chuyển sang bố cục linh hoạt, ẩn bớt cột phụ |
| **Mobile Pro Max** | `430px` | Hiển thị theo chuẩn `fitnado-mobile.html`, bottom nav cố định |
| **Mobile Standard** | `390px`, `375px` | Card sản phẩm và video scroll ngang mượt mà |
| **Mobile Small** | `360px` | Không tràn viền ngang (no horizontal overflow scroll) |

---

## 4. KIỂM THỬ HỒI QUY (REGRESSION TESTING)

Trước khi xác nhận hoàn thành bất kỳ task nào:
* Kiểm tra các trang liên quan trực tiếp và gián tiếp:
  * Trang chủ (`/`)
  * Trang danh sách sản phẩm & chi tiết sản phẩm
  * Trang tin tức & bài viết chi tiết
  * Trang tĩnh (Giới thiệu, Liên hệ, Chính sách)
  * Bộ máy tạo thumbnail tự động (`thumbs/`)
  * Hệ thống SEO meta tags
