---
name: fitnado-regression
description: Regression skill quy định các bước kiểm tra toàn diện nhằm tránh làm gãy các tính năng hiện có sau mỗi lần thay đổi code.
---

# FITNADO REGRESSION SKILL

## 1. NGUYÊN TẮC KIỂM THỬ HỒI QUY (REGRESSION PRINCIPLES)

Trước khi thực hiện bất kỳ sửa đổi nào:
1. **Xác định bản đồ ảnh hưởng (Impact Matrix)**: Liệt kê tất cả các trang, module và API phụ thuộc vào file/bảng sắp sửa.
2. **Sau khi sửa đổi**: Bắt buộc kiểm tra đồng thời cả tính năng mới VÀ các tính năng cũ có liên quan.

---

## 2. BẢNG CHECKLIST KIỂM TRA HỒI QUY THEO MODULE

| Khi sửa đổi module | Các trang/chức năng BẮT BUỘC phải kiểm tra lại |
| :--- | :--- |
| **Sản phẩm (Product Detail)** | - Trang chủ (khối Hot Products)<br>- Danh mục sản phẩm (List, Cat)<br>- Chi tiết sản phẩm<br>- Admin quản lý sản phẩm<br>- SEO tags & Schema sản phẩm |
| **Bài viết (News/Review)** | - Trang chủ (khối Kiến thức & Hướng dẫn)<br>- Danh mục tin tức<br>- Chi tiết bài viết<br>- Admin quản lý bài viết<br>- Breadcrumb & SEO |
| **Ảnh / Slider / Video** | - Trang chủ (khối Hero Slide, 30s Video)<br>- Bộ máy thumbnail (`thumbs/`)<br>- Watermark ảnh |
| **Header / Menu / Footer** | - Kiểm tra trên toàn bộ các trang (Desktop & Mobile)<br>- Thanh tìm kiếm (Search form)<br>- Modal form & Bottom navigation bar |
| **Config / Type** | - Admin CRUD tương ứng<br>- Luồng hiển thị Frontend<br>- Bộ nhớ cache (`caches/`) |
