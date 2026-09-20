---
name: fitnado-optimization
description: Kỹ năng quản lý và vận hành Vòng Lặp Tối Ưu Hóa Dựa Trên Dữ Liệu (Data-Driven Optimization Loop & A/B Experimentation) cho FITNADO.
---

# FITNADO DATA-DRIVEN OPTIMIZATION & A/B EXPERIMENTATION SKILL

## 1. NGUYÊN TẮC BẤT DI BẤT DỊCH (NON-NEGOTIABLE CORE PRINCIPLES)

1. **AI RECOMMENDS, HUMAN DECIDES, SYSTEM EXECUTES (Tam Giác Vận Hành Khép Kín)**:
   - **AI Recommends**: Động cơ tối ưu hóa (`OptimizationEngine`) phân tích tín hiệu hiệu suất từ Phase 08 và đưa ra khuyến nghị kèm lý do rõ ràng, giả thuyết có thể kiểm chứng và chi phí ước tính.
   - **Human Decides**: Tuyệt đối không tự động duyệt chi tiền, tự tạo biến thể hoặc tự xuất bản. Toàn bộ khuyến nghị phải qua Admin phê duyệt hoặc từ chối tại Admin UI.
   - **System Executes**: Khi Admin phê duyệt, hệ thống tự động sinh biến thể kịch bản (Phase 05), video project (Phase 06), và bài đăng phân phối có mã tracking cô lập (Phase 07).

2. **ONE-VARIABLE PRINCIPLE (Nguyên Tắc Đơn Biến Số Trong Thử Nghiệm)**:
   - Mỗi thử nghiệm A/B chỉ được thay đổi duy nhất một biến số:
     - `HOOK`: Đổi góc mở đầu / câu giật tít.
     - `CTA`: Đổi lời kêu gọi hành động cuối video.
     - `SCRIPT`: Đổi kịch bản / góc tiếp cận nội dung (Angle).
     - `VIDEO_STYLE`: Đổi phong cách (ví dụ: nâng cấp ECONOMY lên HYBRID).
     - `VOICE`: Đổi giọng đọc thuyết minh TTS.
     - `OFFER`: Đổi ưu đãi affiliate nổi bật.
   - Không được phép thay đổi đồng thời nhiều biến số trừ khi được gắn nhãn rõ ràng là `MULTIVARIATE`.

3. **ECONOMY FIRST & HARD COST GATE (Tiết Kiệm Chi Phí & Hàng Rào Ngân Sách)**:
   - Mặc định mọi biến thể thử nghiệm sử dụng chế độ ECONOMY (Chi phí API Video = 0 VND).
   - Nâng cấp lên HYBRID chỉ được đề xuất khi sản phẩm có bằng chứng doanh thu thực tế (`REVENUE_WINNER`).
   - Mọi thử nghiệm có chi phí vượt ngưỡng `max_cost_per_experiment` (mặc định 60.000 VND) đều bị Hard Cost Gate chặn tự động, chỉ được thông qua khi Admin bật quyền `Admin Override`.

4. **6-LEVEL PERFORMANCE MATURITY (Thang Phân Hạng Hiệu Suất Chuẩn Xác)**:
   - `INSUFFICIENT_DATA`: Mẫu nhỏ (<30 sessions hoặc <10 clicks).
   - `TRAFFIC_PROMISING`: Lưu lượng xem cao nhưng CTR thấp (>=30 sessions, <10 clicks).
   - `CLICK_PROMISING`: CTR cao nhưng chưa có dữ liệu đối soát đơn hàng (>=30 sessions, >=10 clicks).
   - `CONVERSION_PROMISING`: Đã có đơn hàng đối soát nhưng Net ROI chưa vượt chi phí.
   - `REVENUE_WINNER`: Đã có đơn hàng đối soát và Net ROI dương (Doanh thu hoa hồng > Chi phí sản xuất).
   - `UNDERPERFORMING`: Lưu lượng cao (>=30 sessions) nhưng CTR rất thấp (<1%) và 0 đơn hàng.

5. **TRACKING ISOLATION & SAMPLE SIZE GATING**:
   - Biến thể thử nghiệm mới được cấp một `tracking_code` riêng biệt (gắn liền ID thử nghiệm).
   - Đánh giá thử nghiệm (`evaluateExperiment`) chỉ đưa ra kết luận khi biến thể tích lũy đủ kích thước mẫu (`experiment_min_sessions >= 30`, `experiment_min_clicks >= 10`).

6. **NO INFINITE LOOPS (Chống Vòng Lặp Vô Hạn)**:
   - Khi một thử nghiệm hoàn tất (`COMPLETED`), hệ thống ghi nhận kết luận và snapshot kết quả.
   - Hệ thống KHÔNG tự động kích hoạt thử nghiệm tiếp theo. Mỗi sản phẩm bị giới hạn tối đa `max_active_experiments_per_product` (mặc định: 2) và thời gian giãn cách `recommendation_cooldown_hours` (mặc định: 24h).

---

## 2. KIẾN TRÚC DỮ LIỆU & LỚP NGHIỆP VỤ

- `table_optimization_recommendation`: Lưu trữ khuyến nghị tối ưu hóa, mã lý do (`reason_code`), giả thuyết (`hypothesis`), biến số (`proposed_variable`), chi phí ước tính, snapshot dữ liệu và trạng thái phê duyệt.
- `table_optimization_experiment`: Lưu trữ hồ sơ thử nghiệm A/B, đối soát liên kết ID Baseline vs ID Variation (Post, Video, Content), chỉ số đo lường thực tế và kết luận (`result_conclusion`).
- `OptimizationEngine` (`libraries/class/class.OptimizationEngine.php`): Rule-based core engine phụ trách sinh khuyến nghị, kiểm tra cooldown, thực thi Hard Cost Gate, sinh biến thể và đánh giá đối soát A/B.
- `WinnerDetectionEngine` (`libraries/class/class.WinnerDetectionEngine.php`): Cung cấp thang đo 6 cấp độ hiệu suất và cổng kiểm tra nguồn chuyển đổi `isConversionSourceConnected`.
- Controller Admin: `admin/sources/optimization.php` quản lý màn hình Khuyến nghị, Đối soát Thử nghiệm A/B và Cấu hình Quy tắc.
- Admin Templates:
  - `admin/templates/optimization/recommendations_tpl.php`
  - `admin/templates/optimization/recommendation_detail_tpl.php`
  - `admin/templates/optimization/experiments_tpl.php`
  - `admin/templates/optimization/experiment_detail_tpl.php`
  - `admin/templates/optimization/rules_tpl.php`

---

## 3. CHECKLIST KIỂM ĐỊNH VẬN HÀNH

- [ ] Sản phẩm chưa kết nối đơn hàng không bao giờ được gán nhãn `WINNER` hay `REVENUE_WINNER`.
- [ ] Chạy sinh khuyến nghị 2 lần liên tiếp không tạo ra khuyến nghị trùng lặp trong thời gian cooldown 24h.
- [ ] Phê duyệt đề xuất chi phí cao bị chặn nếu không có Admin Override.
- [ ] Biến thể Post thử nghiệm mang `tracking_code` duy nhất và cô lập.
- [ ] Đánh giá thử nghiệm khi chưa đủ mẫu trả về `INSUFFICIENT_DATA` và giữ trạng thái `RUNNING`.
- [ ] Hoàn tất thử nghiệm không tự động kích hoạt thử nghiệm mới.
