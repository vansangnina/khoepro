# FITNADO KNOWLEDGE: QUYẾT ĐỊNH KIẾN TRÚC (DECISIONS.MD)

Tài liệu này lưu trữ các quyết định kỹ thuật và kiến trúc cốt lõi đã được đưa ra, nhằm đảm bảo tính nhất quán và không đảo ngược thiết kế một cách tùy tiện.

---

## 1. QUYẾT ĐỊNH 01: TÁI SỬ DỤNG VÀ MỞ RỘNG KIẾN TRÚC HIỆN TẠI (MASTERPDO)

* **Quyết định**: Giữ nguyên nền tảng kiến trúc Nina MasterPDO (AltoRouter + PDODb + Sources/Templates) thay vì chuyển đổi sang Laravel, Vue hoặc Next.js.
* **Lý do**:
  1. Toàn bộ hệ thống quản trị Admin (`admin/`), hệ thống cache, upload, xử lý thumbnail (`thumbs/`), SEO và bảo mật đã được kiểm chứng và hoạt động ổn định trên production.
  2. Môi trường server khách hàng yêu cầu chạy ổn định trên PHP 7.4 + MySQL.
  3. Việc duy trì kiến trúc hiện tại giúp đảm bảo tiến độ, tính tương thích và bảo toàn 100% dữ liệu đang có.

---

## 2. QUYẾT ĐỊNH 02: TÁI SỬ DỤNG BẢNG `table_product` CHO KHÁM PHÁ & ĐÁNH GIÁ SẢN PHẨM

* **Quyết định**: Không tạo bảng mới dạng `table_fitnado_product`, mà tận dụng và mở rộng trường dữ liệu trên `table_product` thông qua cấu hình `libraries/type/config-type-product.php` và trường JSON `options` hoặc bảng liên kết `table_gallery` / `table_comment`.
* **Lý do**:
  1. `table_product` đã tích hợp sẵn hệ thống phân cấp 4 cấp danh mục (`list`, `cat`, `item`, `sub`), phân loại thương hiệu (`brand`), giá bán, giá khuyến mãi, hình ảnh, gallery và SEO meta tags.
  2. Tận dụng được toàn bộ module CRUD trong `admin/sources/product.php`.

---

## 3. QUYẾT ĐỊNH 03: TÁI SỬ DỤNG `table_gallery` VÀ `table_photo` CHO VIDEO REVIEW & AI CONTENT

* **Quyết định**: Quản lý các video ngắn 30s review trực quan thông qua module `table_photo` (với `type = 'video'`) hoặc `table_gallery` (với `type = 'san-pham'` và link_video).
* **Lý do**:
  1. Module photo và gallery đã có sẵn luồng nhập link video YouTube/MP4 và quản lý trạng thái `hienthi,noibat` trong Admin.
  2. Dễ dàng query trên frontend cho khối "30 Giây Review".

---

## 4. QUYẾT ĐỊNH 04: TÁCH BIỆT `table_product_research` VÀ QUY TRÌNH DUYỆT TẠO DRAFT (PHASE 03)

* **Quyết định**: Tạo bảng riêng `table_product_research` cho toàn bộ quá trình khám phá, chấm điểm và nghiên cứu sản phẩm. Không nhồi nhét hàng chục cột nghiên cứu vào `table_product`.
* **Lý do**:
  1. **Candidate != Live Product**: Chỉ những sản phẩm tiềm năng sau khi được Admin phê duyệt (`APPROVED`) mới được tạo sang `table_product` ở trạng thái nháp (`status` không có `hienthi`).
  2. **Ngăn chặn rác dữ liệu**: Tránh làm phình to bảng sản phẩm chính với các sản phẩm bị từ chối hoặc đang trong giai đoạn nháp thử nghiệm.
  3. **Độc lập và an toàn**: Đảm bảo an toàn tuyệt đối cho frontend, ngăn chặn mọi rủi ro tự động xuất bản (auto-publish) sản phẩm chưa được kiểm chứng.

---

## 5. QUYẾT ĐỊNH 05: HÀNG ĐỢI XỬ LÝ NỀN (BACKGROUND JOB QUEUE) & TÁCH BẠCH FACT VS AI_ANALYSIS (PHASE 04)

* **Quyết định**:
  1. Tác vụ quét dữ liệu và gọi AI được đưa vào hàng đợi `table_product_research_job` và xử lý bất đồng bộ qua Background Worker (`cron/product_research_worker.php`) thay vì chạy đồng bộ trên HTTP Request.
  2. Tách bạch tuyệt đối: AI không được tự bịa số liệu sự thật thị trường (`sales_count`, `rating`, `review_count`, `commission`). Nếu nguồn không có, bắt buộc để `NULL`.
  3. Lưu nhật ký bằng chứng `table_product_research_evidence` và ảnh chụp biến động thị trường `table_product_research_snapshot` khi quét lại duplicate.
* **Lý do**:
  1. **Tránh Timeout HTTP**: Quá trình gọi API LLM và crawler có thể mất 3–15 giây/sản phẩm; đưa vào hàng đợi nền đảm bảo trải nghiệm quản trị Admin luôn mượt mà.
  2. **Chính xác & Minh bạch**: Ngăn chặn hallucination của LLM trong các số liệu tài chính/doanh số quan trọng.
  3. **Giám sát xu hướng**: Snapshots cho phép FITNADO theo dõi tốc độ tăng trưởng doanh số và độ nóng của sản phẩm theo thời gian thực.

