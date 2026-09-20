---
name: fitnado-analytics
description: Kỹ năng quản lý và vận hành Hệ thống Đo lường, Phân bổ Chuyển đổi & Động cơ Phát hiện Winner (Analytics, Attribution & Winner Detection) cho FITNADO.
---

# FITNADO ANALYTICS, AFFILIATE ATTRIBUTION & WINNER DETECTION SKILL

## 1. NGUYÊN TẮC BẤT DI BẤT DỊCH (NON-NEGOTIABLE CORE PRINCIPLES)

1. **OBSERVED DATA FIRST (Chỉ Dùng Dữ Liệu Quan Sát Thực Tế)**:
   - Hệ thống đo lường vận hành 100% trên dữ liệu thực tế phát sinh từ lượt xem, click và chuyển đổi.
   - Tuyệt đối cấm tạo mock/fake data sản xuất (views, likes, clicks, orders, GMV, commission) để làm đẹp dashboard.
   - Không có dữ liệu: Bắt buộc hiển thị `0` hoặc `N/A` kèm thông báo rõ ràng: `"Conversion data not connected"`.

2. **ATTRIBUTION BY IDENTITY (Phân Bổ Theo Định Danh Thực Tế)**:
   - Mỗi Post Package gắn với một `tracking_code` duy nhất (ví dụ: `fp_tikt_99_...`).
   - Mọi tương tác chuyển hướng sang trang sản phẩm và click Affiliate Offer đều truy vết chính xác:
     ```text
     Post Package (ID & Tracking Code)
     → Product (ID)
     → Video Project (ID & Mode)
     → AI Content (ID & Hook Version)
     → Affiliate Offer (ID & Platform)
     ```
   - Tuyệt đối không suy luận nguồn bằng tên sản phẩm; bắt buộc dùng ID thực.

3. **SAMPLE SIZE GATE BEFORE JUDGMENT (Cổng Kích Thước Mẫu Trước Khi Kết Luận)**:
   - Không bao giờ gán nhãn `WINNER` hay `UNDERPERFORMING` (Loser) khi chưa đạt kích thước mẫu tối thiểu:
     - `min_landing_sessions` (Mặc định: 30 sessions)
     - `min_affiliate_clicks` (Mặc định: 10 clicks)
     - `min_test_age_days` (Mặc định: 3 ngày)
   - Sản phẩm ít dữ liệu BẮT BUỘC giữ trạng thái `INSUFFICIENT_DATA` (Chưa đủ dữ liệu), không được gọi là sản phẩm thất bại.

4. **RESEARCH SCORE != PERFORMANCE SCORE (Tách Bạch Điểm Cơ Hội & Hiệu Suất Thực)**:
   - `Research Score` (Phase 03) là tín hiệu cơ hội thị trường trước khi xuất bản.
   - `Performance Score` (Phase 08) là hiệu quả thực tế quan sát được sau khi xuất bản.
   - Tuyệt đối không trộn lẫn, không sửa điểm nghiên cứu hồi cứu theo hiệu suất.

5. **AI CONFIDENCE != PERFORMANCE**:
   - Độ tin cậy AI của kịch bản hay video không được tự động biến thành Winner.

6. **NO AUTOMATIC BUSINESS ACTION (Không Tự Ý Chi Tiền / Tự Đăng Bài)**:
   - Động cơ Winner Detection chỉ có quyền **DETECT** (Phát hiện) và **RECOMMEND** (Đề xuất hành động: `CREATE_VARIATION`, `CREATE_NEW_HOOK`, `KEEP_TESTING`, `STOP_TESTING`, `UPGRADE_TO_HYBRID`).
   - Quyết định chi tiền quảng cáo, đổi ngân sách hoặc xuất bản hàng loạt hoàn toàn do con người (Admin).

---

## 2. KIẾN TRÚC DỮ LIỆU & LỚP NGHIỆP VỤ

- `table_analytics_event`: Bảng sự kiện chuẩn hóa (`PAGE_VIEW`, `PRODUCT_VIEW`, `AFFILIATE_CLICK`, `POST_VIEW`, `VIDEO_VIEW`, `CONVERSION`, `REVENUE`).
- `table_affiliate_conversion`: Bảng ghi nhận đơn hàng đối soát từ sàn TMĐT (chống trùng lặp theo `platform + external_conversion_id`, hỗ trợ hoàn tiền `REVERSED`).
- `table_conversion_import_log`: Nhật ký vết kiểm toán import file CSV đối soát.
- `table_winner_evaluation`: Snapshot lịch sử và bằng chứng các lần đánh giá Winner của sản phẩm.
- `table_analytics_setting`: Cấu hình động các ngưỡng kích thước mẫu, ngưỡng CTR/CVR và thời gian lưu vết.
- `AnalyticsService` (`libraries/class/class.AnalyticsService.php`): Core service quản lý sự kiện, phân bổ landing, click tracking, tổng hợp chỉ số an toàn toán học (Zero Division Guard, tách biệt tiền tệ).
- `WinnerDetectionEngine` (`libraries/class/class.WinnerDetectionEngine.php`): Rule-based engine đánh giá và xếp hạng Winner theo các cổng điều kiện.
- `ConversionImporter` (`libraries/class/class.ConversionImporter.php`): Xử lý đọc file CSV đối soát, preview kiểm định, batch insert an toàn và ghép nối thủ công (`Manual Match`).

---

## 3. CHECKLIST KIỂM THỬ KHI THAY ĐỔI CODE

- [ ] `php -c php.ini -l` PASS 100% trên toàn bộ các file PHP (PHP 7.4).
- [ ] Chạy `php -c php.ini test_phase08.php` đạt 31/31 test assertions.
- [ ] Chạy kiểm thử hồi quy `test_regression.php`, `test_phase07.php`, `test_phase06_3.php`, `test_phase06_2.php`, `test_phase06.php`, `test_phase05.php`, `test_phase04.php`, `test_phase03.php` đạt 100% PASS.
- [ ] Xác nhận giao diện AdminLTE hoạt động đầy đủ tại `admin/index.php?com=analytics&act=overview`.
