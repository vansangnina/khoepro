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

---

## 6. QUYẾT ĐỊNH 06: KIỂM DUYỆT CHẤT LƯỢNG (QUALITY GATE), SOURCE HASH VÀ ÁP DỤNG HOÀN NGUYÊN (PHASE 05)

* **Quyết định**:
  1. **Strict Quality Gate**: Áp dụng bộ lọc kiểm duyệt `validateQualityGate()` để chặn các claim sai sự thật ("Tôi đã dùng", "chữa bệnh"), fake testimonials và hạ điểm chất lượng trước khi cho phép vào vòng duyệt `REVIEW_REQUIRED`.
  2. **Deterministic Source Hash**: Tính toán SHA-256 trên dữ liệu nguồn của sản phẩm và research facts để tự động gắn cờ Outdated nếu sản phẩm thay đổi sau khi sinh nội dung.
  3. **Shot Plan Bridge**: Định dạng kịch bản TikTok có phân cảnh chi tiết (`shot_plan`) để sẵn sàng tích hợp thẳng vào AI Video Rendering (Phase 06) mà không cần refactor cấu trúc.
  4. **Reversible Apply & Backup**: Tự động lưu bản sao lưu các trường sản phẩm bị ghi đè vào `table_product_content_backup` khi Admin bấm "Áp dụng", cho phép xem Diff và Rollback an toàn.
* **Lý do**:
  1. Đảm bảo uy tín thương hiệu FITNADO, tuân thủ pháp luật quảng cáo thực phẩm/dụng cụ thể thao và bảo vệ dữ liệu sản phẩm gốc khi ứng dụng AI quy mô lớn.

---

## 7. QUYẾT ĐỊNH 07: TRỪU TƯỢNG HÓA VIDEO PROVIDER, ASSET READINESS VÀ HUMAN VIDEO GATE (PHASE 06)

* **Quyết định**:
  1. **Video Provider Abstraction**: Không phụ thuộc vào một vendor AI Video duy nhất. Tạo interface `VideoProviderInterface` hỗ trợ Mock, Creatify, Arcads, HeyGen, Manual.
  2. **Asset Readiness Gating**: Tự động ánh xạ từ ảnh sản phẩm và gallery; nếu thiếu tài nguyên chuyển sang `WAITING_ASSET` và chặn không cho queue render.
  3. **Media Validation & Safe Storage**: Kiểm tra tính toàn vẹn của video (zero-byte check, MP4 format, 9:16 aspect ratio, duration) và lưu trữ cục bộ bảo vệ SSRF.
  4. **Strict Human Gate & No Auto-Publish**: Video bắt buộc dừng ở `REVIEW_REQUIRED` để Admin xem preview HTML5 trước khi duyệt (`APPROVED`); tuyệt đối không tự động đẩy lên API TikTok/YouTube.
* **Lý do**:
  1. Đảm bảo tính linh hoạt khi thị trường video AI thay đổi nhanh, bảo vệ chi phí API và ngăn chặn rủi ro video rác hoặc sai lệch thông tin xuất hiện trên mạng xã hội.



