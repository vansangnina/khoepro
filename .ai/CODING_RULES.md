# QUY TẮC VIẾT CODE BẮT BUỘC (CODING RULES)

---

## 1. TƯƠNG THÍCH PHP 7.4 (HARD REQUIREMENT)

Môi trường Production chạy trên **PHP 7.4**. Tuyệt đối không sử dụng bất kỳ tính năng hoặc cú pháp nào từ PHP 8.0+:

| Cú pháp BỊ CẤM (PHP 8+) | Giải pháp thay thế CHUẨN (PHP 7.4) |
| :--- | :--- |
| `match ($x) { ... }` | Dùng `switch ($x) { ... }` hoặc `if / elseif / else` |
| `str_contains($haystack, $needle)` | Dùng `strpos($haystack, $needle) !== false` |
| `str_starts_with($haystack, $needle)` | Dùng `strncmp($haystack, $needle, strlen($needle)) === 0` |
| `str_ends_with($haystack, $needle)` | Dùng `substr($haystack, -strlen($needle)) === $needle` |
| Nullsafe operator: `$obj?->prop` | Dùng `!empty($obj) && isset($obj->prop) ? $obj->prop : null` |
| Union types: `int|string $x` | Dùng Docblock `@param int|string $x` |
| Constructor property promotion | Khai báo thuộc tính trong class và gán giá trị trong constructor |
| Named arguments: `func(param: $val)` | Truyền tham số theo đúng thứ tự vị trí |
| Attributes `#[Route(...)]` | Dùng comment PHPDoc tiêu chuẩn |

---

## 2. NGUYÊN TẮC TÁI SỬ DỤNG KIẾN TRÚC HIỆN CÓ (REUSE EXISTING CODE)

1. **Class Helper**: Luôn tận dụng các helper đã có trong `libraries/class/`:
   * `$d` (`PDODb`): Xử lý cơ sở dữ liệu.
   * `$func` (`Functions`): Format tiền tệ (`$func->formatMoney()`), chuyển hướng URL (`$func->redirect()`, `$func->transfer()`), kiểm tra URL, tạo thumbnail, xử lý chuỗi.
   * `$seo` (`Seo`): Quản lý thẻ meta, canonical, OpenGraph, Twitter card.
   * `$cache` (`Cache`): Cache dữ liệu truy vấn nặng.
   * `$flash` (`Flash`): Thông báo session flash messages.
2. **Cấu hình Type**: Không hardcode kích thước ảnh hoặc field option trong source PHP; phải đọc từ mảng `$config` (`libraries/config-type.php`).

---

## 3. CẤM HARDCODE DỮ LIỆU FRONTEND (NO HARDCODED DATA)

Nghiêm cấm hardcode các thành phần sau vào template PHP:
* Danh sách sản phẩm, danh mục, giá bán, giảm giá.
* Nội dung bài viết, tác giả, ngày đăng.
* Video review, link affiliate, đánh giá số sao.
* Banner, hotline, thông tin liên hệ trong footer.

Tất cả dữ liệu phải được truyền qua biến từ tầng `sources/*.php` sau khi query từ database hoặc nạp từ file cấu hình ngôn ngữ/setting.

---

## 4. BẢO TỒN NỘI DUNG VÀ TRÁNH THAY ĐỔI NGOÀI PHẠM VI

1. **Không tự ý refactor**: Không sửa các file không liên quan đến phạm vi của task hiện tại.
2. **Không thêm comment rác**: Tuyệt đối không chèn các chú thích như `// AI GENERATED`, `// GEMINI CODE`, `// NEW CODE 2026`, `// TODO BY AGENT` vào source code.
3. **Bảo tồn comment gốc**: Giữ nguyên toàn bộ comment và chú thích kiến trúc ban đầu của hệ thống.
