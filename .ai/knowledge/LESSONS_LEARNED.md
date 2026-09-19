# FITNADO KNOWLEDGE: BÀI HỌC KINH NGHIỆM THỰC TẾ (LESSONS LEARNED)

Tài liệu này tổng hợp các bài học kinh nghiệm rút ra sau từng giai đoạn thực thi dự án để các AI Agents kế tiếp không lặp lại sai lầm.

---

## 1. BÀI HỌC TỪ PHASE 01 (HOMEPAGE & REAL DATABASE INTEGRATION)

* **Bài học 01: Quy tắc "No Data -> No Render" và cơ chế Fallback Query an toàn**:
  * Khi query các sản phẩm có `find_in_set('noibat', status)`, nếu quản trị viên chưa đánh dấu "Nổi bật" cho sản phẩm nào, query sẽ trả về mảng rỗng.
  * *Giải pháp chuẩn*: Trong `sources/index.php`, cần có cơ chế fallback lấy danh sách sản phẩm mới nhất (`find_in_set('hienthi', status) limit 0,10`) để trang chủ không bị trống dữ liệu mà vẫn đảm bảo tính tự động.
* **Bài học 02: Visual Parity và tối ưu CSS Mobile Horizontal Scroll**:
  * Trên thiết bị di động (<= 991px), các khối Danh mục (Categories), Sản phẩm (Products), và Video (Videos) hiển thị dạng cuộn ngang (Horizontal Scroll với `-webkit-overflow-scrolling: touch`). Cần ẩn thanh cuộn mặc định (`scrollbar-width: none; ::-webkit-scrollbar { display: none; }`) để giao diện trông hiện đại và mượt mà như một ứng dụng Native App.
* **Bài học 03: Tích hợp Video YouTube & MP4 linh hoạt**:
  * Với section "30 Giây Review", dữ liệu có thể là URL YouTube hoặc file MP4 tải lên. Bóc tách YouTube ID qua `$func->getYoutube($link)` để lấy thumbnail chất lượng cao (`hqdefault.jpg`) và kích hoạt modal video qua Fancybox (`data-fancybox="video-gallery"`).

---

## 2. BÀI HỌC TỪ TASK 00 (AUDIT SOURCE & PROTOTYPE)

* **Bài học 01: Prototype chỉ là nguồn chân lý về mặt thị giác (Visual Truth), không phải nguồn dữ liệu (Data Truth)**:
  * Không sao chép các số liệu demo tĩnh ("2.450+ sản phẩm", "150+ bài review"). Thay vào đó tính toán từ cơ sở dữ liệu thật qua `$countProduct`, `$countNews`, `$counter`.
* **Bài học 02: Đồng bộ tỉ lệ Thumbnail giữa Config và Prototype**:
  * Tận dụng cơ chế `thumbs/{w}x{h}x{z}/` để render kích thước tối ưu cho từng loại thẻ (Card 285x285, Video 320x220, Article 280x180).

---

## 3. BÀI HỌC TỪ PHASE 03 (PRODUCT RESEARCH & SCORING PIPELINE)

* **Bài học 01: Chấm điểm phải xử lý chuẩn Missing Data (`NULL != 0`)**:
  * Sản phẩm mới phát hiện thường chưa có đủ dữ liệu về doanh số hoặc từ khóa SEO. Việc gán giá trị 0 cho trường chưa biết sẽ làm sai lệch nghiêm trọng điểm số tổng hợp và loại bỏ oan các sản phẩm tiềm năng. Giải pháp chuẩn là tái phân bổ tỷ lệ trọng số cho các chiều đã có dữ liệu thực.
* **Bài học 02: Chống trùng lặp 3 tầng (URL, Platform ID, Tên chuẩn hóa)**:
  * Người dùng thường copy link có chứa tham số tracking (`utm_source`, `aff_platform`, `spm`). Cần hàm chuẩn hóa URL loại bỏ toàn bộ query rác trước khi kiểm tra trùng lặp trong cơ sở dữ liệu.
* **Bài học 03: Tuyệt đối không auto-publish sản phẩm ra frontend**:
  * Mọi sản phẩm sinh ra từ ứng viên nghiên cứu bắt buộc phải ở trạng thái Bản nháp (Unpublished Draft / không có cờ `hienthi`) để người quản trị kiểm tra hình ảnh, nội dung và danh mục trước khi công khai.

---

## 4. BÀI HỌC TỪ PHASE 04 (AUTOMATED RESEARCH & AI RESEARCH AGENT)

* **Bài học 01: Thiết kế Background Job Queue để cô lập tác vụ nặng (Decoupled Long-Running Jobs)**:
  * Quá trình phân tích AI và crawler mất nhiều thời gian. Đưa vào hàng đợi `table_product_research_job` và chạy qua cron worker giúp giao diện quản trị Admin phản hồi tức thì dưới 100ms, đồng thời có thể retry tự động khi mạng chập chờn.
* **Bài học 02: Tách biệt nghiêm ngặt FACT vs AI_ANALYSIS chống ảo giác (Hallucination Guardrails)**:
  * LLM rất giỏi trong việc phân tích góc độ nội dung (Content Angles) và giải pháp bài toán (Problem Solved), nhưng không được phép tự bịa số liệu lượt bán, đánh giá sao hoặc tỷ lệ hoa hồng. Đặt schema validation bắt buộc `null` cho các trường thiếu số liệu sàn thực tế.
* **Bài học 03: Lưu Snapshot để theo dõi tín hiệu thị trường theo thời gian**:
  * Khi quét lại một sản phẩm đã có, thay vì tạo ứng viên trùng lặp, việc cập nhật `last_seen_at` và lưu một snapshot biến động giá/lượt bán/creator count mang lại giá trị vô cùng lớn cho việc phát hiện các sản phẩm đang có xu hướng tăng trưởng nóng (Trending Velocity).

