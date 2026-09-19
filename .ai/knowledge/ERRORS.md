# FITNADO KNOWLEDGE: SỔ TAY GHI NHẬN LỖI (ERRORS.MD)

Tài liệu này lưu trữ các lỗi đã phát hiện hoặc có nguy cơ phát sinh trong quá trình AI thực hiện tác vụ, cùng nguyên nhân gốc rễ và biện pháp phòng ngừa triệt để.

---

## MẪU GHI NHẬN LỖI (ERROR ENTRY FORMAT)

```markdown
### [ERR-xxx] Tên ngắn gọn của lỗi
* **Vấn đề (Problem)**: Mô tả hiện tượng lỗi quan sát được.
* **Nguyên nhân (Cause)**: Lý do kỹ thuật gây ra lỗi (ví dụ: dùng hàm PHP 8 trên PHP 7.4, sai tên cột...).
* **Cách khắc phục (Fix)**: Code hoặc giải pháp đã áp dụng.
* **Biện pháp phòng ngừa (Prevention)**: Quy tắc kiểm tra để không lặp lại.
* **Phạm vi ảnh hưởng (Affected area)**: Module, file hoặc chức năng bị tác động.
```

---

## CÁC LỖI TIÊU BIỂU & BIỆN PHÁP PHÒNG NGỪA

### [ERR-001] Sử dụng cú pháp hoặc hàm của PHP 8+ trên môi trường PHP 7.4
* **Vấn đề**: Gặp lỗi `Fatal error: Uncaught Error: Call to undefined function str_contains()` hoặc `Parse error: syntax error, unexpected '?'` trên máy chủ production.
* **Nguyên nhân**: AI sử dụng theo thói quen các hàm hiện đại như `str_contains()`, `str_starts_with()`, nullsafe operator `?->`, hoặc biểu thức `match()`.
* **Cách khắc phục**: Thay thế bằng `strpos() !== false`, `strncmp() === 0`, kiểm tra `!empty()` truyền thống, và cấu trúc `switch-case`.
* **Biện pháp phòng ngừa**: Luôn chạy `php -l <file>` và kiểm tra đối chiếu bảng quy chuẩn trong `.ai/CODING_RULES.md`.
* **Phạm vi ảnh hưởng**: Toàn bộ các file PHP backend và template.

### [ERR-002] Hardcode dữ liệu mẫu tĩnh (Mock Data) vào Template PHP
* **Vấn đề**: Giao diện hiển thị các thẻ sản phẩm hoặc bài viết demo không thể cập nhật từ Admin, gây sai lệch dữ liệu thực tế của khách hàng.
* **Nguyên nhân**: Sao chép nguyên khối mã HTML từ file prototype (`fitnado-desktop.html`) vào template PHP mà không bóc tách vòng lặp `foreach` query từ database.
* **Cách khắc phục**: Tách biệt luồng query trong `sources/*.php` và chỉ duyệt lặp qua mảng dữ liệu thật trong `templates/*.php`.
* **Biện pháp phòng ngừa**: Tuân thủ quy tắc "No Data -> No Render", luôn kiểm tra `if (!empty($items))` trước khi xuất HTML.
* **Phạm vi ảnh hưởng**: `templates/index/index_tpl.php`, `templates/product/product_tpl.php`...

### [ERR-003] Lỗi SQL Incorrect decimal/integer value khi insert bản ghi thiếu trường số
* **Vấn đề**: Lỗi SQL `Incorrect decimal value: '' for column 'review_score' at row 1` khi tạo sản phẩm vào `table_product`.
* **Nguyên nhân**: Schema loader của `PDODb` tự động điền giá trị rỗng `''` cho các trường có trong bảng nhưng không có trong mảng dữ liệu insert, gây lỗi strict mode MySQL với các cột kiểu `decimal` hoặc `int`.
* **Cách khắc phục**: Luôn khai báo tường minh các trường số (`review_score`, `review_count`, `numb`, `view`, `discount`) với giá trị số cụ thể `0` hoặc `(float)$val`.
* **Biện pháp phòng ngừa**: Kiểm tra toàn bộ schema của bảng đích trước khi map dữ liệu insert.
* **Phạm vi ảnh hưởng**: `libraries/class/class.ProductResearch.php` (hàm `createProductFromCandidate`).

### [ERR-004] Lỗi PDO Connection Refused trên môi trường CLI cục bộ
* **Vấn đề**: Script PHP CLI không kết nối được MySQL khi chỉ dùng `host => localhost` do MySQL MAMP chỉ lắng nghe qua unix socket.
* **Nguyên nhân**: Lớp `PDODb` chưa hỗ trợ tham số `unix_socket` trong danh sách `$connectionParams`.
* **Cách khắc phục**: Bổ sung `'unix_socket'` vào `$connectionParams` của `PDODb::connect()`.
* **Biện pháp phòng ngừa**: Hỗ trợ đầy đủ các tham số kết nối PDO chuẩn.
* **Phạm vi ảnh hưởng**: `libraries/class/class.PDODb.php`.

### [ERR-005] Lỗi AutoLoad khi tên class không khớp tên file 1:1 trong thư mục `libraries/class/`
* **Vấn đề**: `Fatal error: Class 'ResearchProviderFactory' not found` khi gọi Factory từ class khác mà chưa include file `class.ResearchProvider.php`.
* **Nguyên nhân**: `AutoLoad` mặc định của MasterPDO map tên class `FooBar` sang `libraries/class/class.FooBar.php`. Khi nhiều class liên quan nằm trong cùng một file (như `ResearchCandidateDTO`, `ResearchProviderFactory` trong `class.ResearchProvider.php`), Autoload không tự tìm thấy.
* **Cách khắc phục**: Thêm wrapper alias `class ResearchProvider` có phương thức tĩnh `factory()`, đồng thời thêm các khối `require_once` kiểm tra `class_exists()` trong các file consumer.
* **Biện pháp phòng ngừa**: Luôn kiểm tra cơ chế autoload và khai báo `require_once` tường minh hoặc đặt tên file khớp với class chính.
* **Phạm vi ảnh hưởng**: `libraries/class/class.ResearchProvider.php`, `libraries/class/class.ResearchJobQueue.php`.

### [ERR-006] Ghi đè trực tiếp dữ liệu sản phẩm gốc mà không lưu lịch sử hoàn nguyên
* **Vấn đề**: Khi người dùng áp dụng nội dung AI vào sản phẩm, nếu không hài lòng không thể khôi phục lại mô tả/bài viết cũ.
* **Nguyên nhân**: Thiết kế ban đầu update thẳng vào `table_product` mà không có cơ chế snapshot/backup.
* **Cách khắc phục**: Tạo bảng `table_product_content_backup` và xây dựng hàm `applyToProduct()` tự động backup toàn bộ các trường `descvi`, `contentvi`, `expert_pros`, `expert_cons`, `verdict`, `best_for`, `specs` cũ trước khi thực hiện UPDATE.
* **Biện pháp phòng ngừa**: Mọi luồng áp dụng nội dung AI hàng loạt vào dữ liệu sản phẩm chính phải có bước sao lưu tự động và cho phép Diff/Rollback.
* **Phạm vi ảnh hưởng**: `libraries/class/class.AIContentEngine.php`, `admin/sources/ai_content.php`.


