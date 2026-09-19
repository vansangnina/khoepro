---
name: fitnado-admin
description: Admin skill quy định quy chuẩn phát triển và mở rộng module quản trị FITNADO.
---

# FITNADO ADMIN SKILL

## 1. NGUYÊN TẮC PHÁT TRIỂN ADMIN

Mọi tính năng hoặc module quản trị mới đều phải tái sử dụng toàn bộ nền tảng có sẵn của `/admin/`:
* **Layout & Wrapper**: Tái sử dụng `admin/templates/layout/header.php`, `menu.php`, `footer.php`.
* **Xác thực & Bảo mật**: Kế thừa `$func->checkLoginAdmin()` và phân quyền theo bảng `table_permission`.
* **Xử lý Upload**: Sử dụng `$func->uploadImage()`, `$func->uploadFile()`, tự động cập nhật JSON `options` kích thước.
* **Xử lý Cache**: Tự động gọi `$cache->delete()` sau mỗi thao tác lưu (`save*`) hoặc xóa (`delete*`).
* **Đa ngôn ngữ**: Quản lý song song `namevi` / `nameen`, `descvi` / `descen`, `contentvi` / `contenten`.

---

## 2. QUY TRÌNH THỰC HIỆN TỪNG BƯỚC

```text
1. Khai báo Type trong libraries/type/
2. Thêm Menu Link vào admin/templates/layout/menu.php (nếu cần module riêng)
3. Viết Controller logic trong admin/sources/{com}.php
4. Tạo View templates trong admin/templates/{com}/
5. Kiểm tra dữ liệu insert/update vào MySQL
6. Kiểm tra xóa cache tự động
```
