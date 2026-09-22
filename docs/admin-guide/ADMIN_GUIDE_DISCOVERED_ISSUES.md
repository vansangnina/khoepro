# NHẬT KÝ VẤN ĐỀ PHÁT HIỆN & LƯU Ý KỸ THUẬT (DISCOVERED ISSUES)

## 1. NGUYÊN TẮC
Theo đúng chỉ đạo tại Mục 5 của quy trình xây dựng tài liệu:
> "Nếu phát hiện vấn đề trong lúc audit/test: Không âm thầm sửa ngoài phạm vi mà ghi nhận đầy đủ vào tài liệu `ADMIN_GUIDE_DISCOVERED_ISSUES.md`."

---

## 2. DANH SÁCH VẤN ĐỀ & KHUYẾN NGHỊ VẬN HÀNH

### Vấn đề 1: Trễ hiển thị ngoài Frontend do bộ nhớ đệm (Cache Delay)
- **Chức năng liên quan**: Thêm mới / Sửa sản phẩm (`com=product`), Thay đổi trạng thái bài viết (`com=news`).
- **Hiện tượng**: Sau khi người dùng lưu sản phẩm mới hoặc đổi giá trong Admin, khi ra ngoài trang chủ xem lại có thể vẫn thấy giá cũ nếu trình duyệt đang lưu cache.
- **Nguyên nhân**: Cơ chế Cache HTML tĩnh của Nina Framework nhằm tăng tốc độ tải trang cho khách hàng.
- **Giải pháp & Khuyến nghị người dùng**:
  - Đã tích hợp nút "Xóa Cache" trên thanh Header của Admin.
  - Đã đưa vào mục **"⚠️ Lưu ý quan trọng"** và **"Xử lý lỗi thường gặp"** trong tài liệu hướng dẫn: Sau khi sửa giá hoặc đăng bài mới, người dùng chỉ cần click icon "Xóa Cache" 1 lần là website sẽ cập nhật tức thì.
- **Mức độ ảnh hưởng**: Thấp (Đã có hướng dẫn chi tiết).

---

### Vấn đề 2: Giới hạn dung lượng và định dạng ảnh tải lên
- **Chức năng liên quan**: Upload ảnh sản phẩm (`com=product&act=add`), Banner (`com=photo`).
- **Hiện tượng**: Một số quản trị viên tải ảnh chụp máy cơ chưa nén (>15MB) hoặc định dạng HEIC/RAW khiến quá trình upload bị từ chối.
- **Nguyên nhân**: Cấu hình an toàn máy chủ giới hạn file upload tối đa 5MB/ảnh và chỉ nhận định dạng web (JPG, PNG, WebP).
- **Giải pháp & Khuyến nghị người dùng**:
  - Tài liệu hướng dẫn đã ghi rõ tiêu chuẩn kích thước ảnh (600x600px đối với sản phẩm, 1920x600px đối với banner) và định dạng khuyến nghị (JPG/WebP).
- **Mức độ ảnh hưởng**: Thấp.

---

### Vấn đề 3: Thời gian trễ đối soát đơn hàng từ sàn Affiliate (AccessTrade / Shopee)
- **Chức năng liên quan**: Đo lường & Chuyển đổi (`com=analytics&act=conversions`).
- **Hiện tượng**: Khách hàng vừa đặt hàng thành công trên Shopee nhưng chưa thấy xuất hiện ngay trong Admin KhoePro.
- **Nguyên nhân**: API AccessTrade và Shopee đồng bộ dữ liệu theo chu kỳ 15-30 phút/lần thay vì real-time từng giây.
- **Giải pháp & Khuyến nghị người dùng**:
  - Đã bổ sung ghi chú giải thích cơ chế đối soát và nút "Đồng bộ giao dịch ngay (Sync Now)" trong hướng dẫn đo lường chuyển đổi.
- **Mức độ ảnh hưởng**: Thấp (Hành vi bình thường của hệ thống Affiliate).

---

## 3. TỔNG KẾT
- Toàn bộ các module Admin hoạt động ổn định, an toàn, không có lỗi nghiêm trọng (Critical/Blocker).
- Tất cả các trường hợp đặc thù đã được đưa vào phần **"Lưu ý quan trọng"** và **"Troubleshooting"** của từng bài hướng dẫn để người dùng dễ dàng xử lý.
