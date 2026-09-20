---
name: fitnado-operations
description: Kỹ năng quản lý và vận hành Trung Tâm Điều Hành & Tự Động Hóa (Operations & Automation Control Center) cho FITNADO.
---

# FITNADO OPERATIONS & AUTOMATION CONTROL CENTER SKILL

## 1. NGUYÊN TẮC BẤT DI BẤT DỊCH (NON-NEGOTIABLE CORE PRINCIPLES)

1. **OPERATIONS IS A CONTROL PLANE ONLY (Mặt Phẳng Kiểm Soát Duy Nhất)**:
   - Trung tâm Vận hành (`OperationsService`, `admin/sources/operations.php`) là bảng điều khiển và giám sát trung tâm cho toàn bộ pipeline Phase 01–09 (`Research → Product → Content → Video → Publishing → Tracking → Analytics → Optimization → Experiment`).
   - Trung tâm Vận hành KHÔNG can thiệp chỉnh sửa dữ liệu thực thể nghiệp vụ (ví dụ: không sửa giá sản phẩm, không sửa text kịch bản); việc chỉnh sửa thuộc về các module chuyên trách.

2. **AI RECOMMENDS, HUMAN DECIDES, SYSTEM EXECUTES**:
   - Phase 10 KHÔNG tạo thêm AI logic hoặc vòng lặp tự động mới.
   - Hàng đợi Hành động Con người ("CẦN BẠN XỬ LÝ") tổng hợp 8 chiều nghiệp vụ cần Admin can thiệp và sắp xếp theo thứ tự ưu tiên (`CRITICAL` > `COST_BLOCKED/WARNING` > `APPROVAL` > `NORMAL`).

3. **NO FAKE HEALTH (Không Trạng Thái Sức Khỏe Giả Lập)**:
   - Trạng thái sức khỏe worker, queue, provider được tính toán trực tiếp từ cơ sở dữ liệu thật và TTL heartbeat.
   - Tuyệt đối không hardcode nhãn `HEALTHY` hoặc giả lập trạng thái.

4. **NO PAID HEALTH CHECKS (Không Tự Động Gọi API Tính Phí Khi Kiểm Tra Sức Khỏe)**:
   - Quá trình render trang sức khỏe nhà cung cấp (`providers_tpl.php`) và chẩn đoán KHÔNG bao giờ gọi HTTP/cURL ra các API bên ngoài tính phí (Runway, Luma, Gemini, TTS).
   - Sức khỏe API dựa trên cấu hình sẵn có và lịch sử thực thi gần nhất trong cơ sở dữ liệu.

5. **SECRETS NEVER EXPOSED (Không Để Lộ Bí Mật & API Keys)**:
   - Hàm `OperationsService::sanitizeSecrets()` đệ quy quét sạch và che dấu các chuỗi token Bearer, API key, mật khẩu, authorization headers trước khi lưu vào log hoặc hiển thị lên giao diện Admin.

6. **IDEMPOTENT SAFE RETRY & SAFE CANCEL (Retry & Hủy An Toàn, Bất Biến)**:
   - Chỉ cho phép Retry các tác vụ ở trạng thái `FAILED`.
   - Tuyệt đối nghiêm cấm Retry bài đăng đã `PUBLISHED` hoặc tác vụ tính phí đã thành công.
   - Cho phép Cancel an toàn các tác vụ đang `PENDING` hoặc `RUNNING`.

7. **EMERGENCY STOP & BUDGET GUARDS (Dừng Khẩn Cấp & Hạn Mức Ngân Sách)**:
   - Nút Dừng Khẩn Cấp (`pause_paid_automation = 1`) lập tức đóng băng toàn bộ tác vụ gọi API bên ngoài tính phí, trong khi các tác vụ nội bộ (render FFmpeg ECONOMY, crawler local) vẫn hoạt động bình thường.
   - Hạn mức ngân sách ngày (`200.000 VND`) và tháng (`3.000.000 VND`) tự động khóa tác vụ tính phí khi vượt ngưỡng, trừ khi Admin bật quyền `Admin Override` (kèm ghi log kiểm toán bắt buộc vào `table_operations_override_log`).

8. **NOT_CONFIGURED != FAILED (Phân Biệt Chưa Cấu Hình và Bị Lỗi)**:
   - Các cổng tích hợp bên thứ ba (như TikTok Content Posting API, Shopee Affiliate Open API) khi chưa nạp API key được định danh chính xác là `NOT_CONFIGURED`, không được đánh dấu là `FAILED` của toàn hệ thống.

---

## 2. KIẾN TRÚC DỮ LIỆU & LỚP NGHIỆP VỤ

- `table_system_worker_status`: Theo dõi trạng thái nhịp tim (heartbeats), PID, hostname, lỗi gần nhất của 6 tiến trình nền.
- `table_system_alert`: Quản lý sự cố với cơ chế fingerprint chống trùng lặp, bộ đếm `occurrences_count`, vòng đời `ACTIVE → ACKNOWLEDGED → RESOLVED`.
- `table_operations_override_log`: Lưu vết kiểm toán toàn bộ hành động ghi đè ngân sách, dừng khẩn cấp, retry/cancel tác vụ.
- `table_analytics_setting` (group `operations`): Lưu cấu hình công tắc tự động hóa toàn cục & từng module, hạn mức ngân sách, ngưỡng timeout nhịp tim và hàng đợi kẹt.
- `OperationsService` (`libraries/class/class.OperationsService.php`): Rule-based service quản trị tiến trình, hàng đợi, chi phí, ngân sách, cảnh báo, chẩn đoán môi trường và làm sạch dữ liệu bí mật.
- Controller Admin: `admin/sources/operations.php` xử lý 9 views và các mutating POST actions bảo vệ CSRF.
- Admin Templates (`admin/templates/operations/`):
  - `overview_tpl.php`: Tổng quan sức khỏe, chỉ số tức thời, ngân sách & cảnh báo.
  - `pipeline_tpl.php`: Dòng chảy 9 giai đoạn & Hàng đợi Hành động Con người ("Cần Bạn Xử Lý").
  - `jobs_tpl.php`: Quản lý toàn diện 4 hàng đợi tác vụ, phát hiện job kẹt & thao tác Retry/Cancel.
  - `job_detail_tpl.php`: Chi tiết tác vụ, thông số payload/kết quả đã lọc bí mật.
  - `providers_tpl.php`: Chẩn đoán trạng thái nhà cung cấp không tốn phí API.
  - `costs_tpl.php`: Trung tâm phân tích chi phí thực tế vs ước tính & cơ chế Admin Override.
  - `alerts_tpl.php`: Trung tâm cảnh báo, gộp trùng sự cố & xác nhận/giải quyết.
  - `logs_tpl.php`: Nhật ký kiểm toán thao tác vận hành (`table_operations_override_log`).
  - `settings_tpl.php`: Bảng điều khiển công tắc tự động hóa và ngưỡng giám sát.

---

## 3. CHECKLIST KIỂM THỬ & BẢO TRÌ

- Kiểm tra cú pháp PHP 7.4: `php -c php.ini -l libraries/class/class.OperationsService.php`
- Chạy bộ kiểm thử tự động Phase 10: `php -c php.ini test_phase10.php`
- Chạy bộ kiểm thử HTTP & Views Admin: `php -c php.ini test_http_operations.php`
- Chạy kiểm thử hồi quy toàn hệ thống: `php -c php.ini test_regression.php`
