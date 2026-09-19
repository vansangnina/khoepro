# KẾ HOẠCH TRIỂN KHAI: PHASE 06 — AI VIDEO PRODUCTION ENGINE

**Dự án**: FITNADO (Hệ sinh thái Product Discovery, Review & Affiliate Gym/Fitness)  
**Phân hệ**: Phase 06 — Động cơ Sản xuất Video AI (AI Video Production Engine)  
**Mục tiêu cốt lõi**: Chuyển đổi Kịch bản TikTok đã duyệt (Approved TikTok Script) và Bảng phân cảnh (Approved Shot Plan) thành Video Project -> Video Job -> Render -> Validation -> Preview -> Phê duyệt của Admin (Human Approval).

---

## 1. KHẢO SÁT HIỆN TRẠNG & ĐẦU VÀO TỪ PHASE 05

### 1.1. Cấu trúc dữ liệu đầu vào Phase 05 (Input Source of Truth)
Từ `table_ai_content` với `content_type = 'tiktok_script'` và `status = 'APPROVED'`:
* `structured_data` chứa mảng `shot_plan`:
  - `scene_number`: Số thứ tự phân cảnh (1, 2, 3...)
  - `duration`: Thời lượng cảnh (giây)
  - `visual_instruction`: Chỉ dẫn khung hình / góc quay
  - `voiceover`: Lời lồng tiếng / thuyết minh
  - `on_screen_text`: Chữ hiển thị trên màn hình (Captions / Hooks)
  - `asset_requirement`: Yêu cầu tài nguyên trực quan (Ảnh sản phẩm, B-roll, Icon giỏ hàng...)
* `source_hash`: Băm SHA-256 đối soát tính toàn vẹn của kịch bản, phát hiện khi nào video bị `OUTDATED`.

### 1.2. Khảo sát Hệ thống & API Credentials Thực tế
* **Hạ tầng Binary**: `ffmpeg` / `ffprobe` không khả dụng trực tiếp trên PATH hệ thống server cục bộ -> Hệ thống Video Engine thiết kế cơ chế fallback và validation an toàn dựa trên PHP MIME/File size/Stream info mà không crash.
* **Cấu hình AI API**:
  - `table_setting` hiện tại sử dụng `active_provider = 'mock'`.
  - Chưa có API Key thật cho nhà cung cấp Video ngoài (Creatify / Arcads / HeyGen / Runway).
  - -> **Định nghĩa bàn giao**: Đạt trạng thái **ARCHITECTURE READY & MOCK RENDER TESTED**. Báo cáo minh bạch: **REAL VIDEO PROVIDER NOT CONFIGURED** theo đúng nguyên tắc không suy đoán / không fake PASS.

---

## 2. KIẾN TRÚC TỔNG THỂ HỆ THỐNG VIDEO (PHASE 06)

```text
APPROVED TIKTOK SCRIPT + SHOT PLAN (table_ai_content)
                         │
                         ▼
        [VIDEO PROJECT MANAGER] (table_ai_video)
        (Hooks, Scene Setup, 9:16 Aspect Ratio)
                         │
                         ▼
             [ASSET RESOLUTION PIPELINE]
  (Reuse table_product.photo, table_gallery, Uploads)
     ├── All Assets Ready ──▶ [READY]
     └── Missing Assets   ──▶ [WAITING_ASSET]
                         │
                         ▼
         [VIDEO JOB QUEUE] (table_ai_video_job)
        (Concurrency Lock, Timeout, Stale Recovery)
                         │
                         ▼
          [VIDEO BACKGROUND WORKER]
          (cron/video_render_worker.php)
                         │
                         ▼
          [VIDEO PROVIDER ABSTRACTION]
     ├── MockVideoProvider (Internal Simulation)
     ├── ExternalVideoProvider (Creatify / Arcads / HeyGen)
     └── ManualVideoProvider (Admin DIY Editor)
                         │
                         ▼
         [DOWNLOAD & OUTPUT STORAGE]
     (SSRF Protection, Safe Filename, /upload/video/)
                         │
                         ▼
           [MEDIA VALIDATION & QC]
  (Zero-byte Check, MIME Type, Duration, Safe Area Check)
                         │
                         ▼
          [STRICT HUMAN APPROVAL GATE]
             (Status: REVIEW_REQUIRED)
     ├── Admin HTML5 <video> Preview
     ├── Review Notes / Reject Reason
     └── APPROVED -> READY_FOR_PUBLISHING (Phase 07)
```

---

## 3. THIẾT KẾ CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)

### 3.1. `table_ai_video` (Video Project & Thành phẩm)
* `id`: Khóa chính INT(11) UNSIGNED AUTO_INCREMENT
* `id_product`: ID sản phẩm liên kết `table_product.id` (bắt buộc)
* `id_content`: ID kịch bản liên kết `table_ai_content.id` (bắt buộc)
* `title`: Tiêu đề video / Concept dự án VARCHAR(255)
* `video_type`: Phân loại (`TIKTOK_9_16`, `YOUTUBE_SHORTS`, `PRODUCT_SHOWCASE`)
* `aspect_ratio`: Tỷ lệ khung hình (`9:16`, `1:1`, `16:9`) DEFAULT '9:16'
* `target_duration`: Thời lượng mục tiêu (giây, vd: 15, 30, 45, 60)
* `voice_id`: Giọng đọc TTS (`vi-VN-Standard-A`, `vi-VN-Neural2-Male`, `vi-VN-Neural2-Female`...)
* `template_id`: Mẫu hiển thị visual (`PROBLEM_SOLUTION`, `PRODUCT_REVIEW`, `COMPARISON`)
* `scenes_data`: Mảng phân cảnh chuẩn hóa JSON (Scene 1..N kèm asset mapping)
* `script_hash`: Mã băm kịch bản tại thời điểm render
* `version`: Phiên bản video (v1, v2...)
* `is_active`: TINYINT(1) - Phiên bản video chính
* `is_outdated`: TINYINT(1) - Đánh dấu khi script gốc bị chỉnh sửa
* `provider`: Tên provider (`mock`, `creatify`, `arcads`, `heygen`, `manual`)
* `provider_job_id`: ID tác vụ phía provider bên ngoài
* `status`: Trạng thái (`DRAFT`, `WAITING_ASSET`, `READY`, `QUEUED`, `PROCESSING`, `RENDERED`, `VALIDATING`, `REVIEW_REQUIRED`, `APPROVED`, `REJECTED`, `FAILED`, `ARCHIVED`)
* `reject_reason`: Lý do từ chối của Admin VARCHAR(255)
* `video_file`: Đường dẫn file video sau khi tải về lưu trữ cục bộ (`upload/video/...mp4`)
* `thumbnail`: File ảnh đại diện video (`upload/video/...jpg`)
* `duration_actual`: Thời lượng thực tế (giây)
* `width`, `height`: Kích thước pixel (vd: 1080x1920)
* `file_size`: Dung lượng file (bytes)
* `cost_estimate`: Chi phí ước tính (USD / Credits)
* `quality_report`: Kết quả kiểm định chất lượng JSON
* `reviewed_by`: Tên/ID Admin kiểm duyệt
* `reviewed_at`: Unix timestamp duyệt
* `date_created`, `date_updated`: Unix timestamp

### 3.2. `table_ai_video_job` (Hàng đợi Render Tác vụ Nền)
* `id`: Khóa chính INT(11) UNSIGNED AUTO_INCREMENT
* `id_video`: Khóa ngoại liên kết `table_ai_video.id`
* `provider`: Tên provider thực thi
* `status`: Trạng thái (`PENDING`, `RUNNING`, `SUCCESS`, `FAILED`, `CANCELLED`)
* `attempts`: Số lần thử lại INT(11) DEFAULT 0
* `max_attempts`: Giới hạn retry INT(11) DEFAULT 3
* `next_poll_at`: Thời điểm polling trạng thái tiếp theo INT(11)
* `poll_count`: Số lần đã polling
* `error_message`: Chi tiết lỗi nếu thất bại TEXT
* `started_at`, `completed_at`: Unix timestamp
* `duration`: Thời gian xử lý tính bằng giây DOUBLE
* `date_created`, `date_updated`: Unix timestamp

### 3.3. `table_ai_video_asset` (Kho Tài nguyên Dự án Video)
* `id`: Khóa chính INT(11) UNSIGNED AUTO_INCREMENT
* `id_video`: Khóa ngoại liên kết `table_ai_video.id`
* `scene_number`: Phân cảnh tương ứng INT(11)
* `asset_type`: Phân loại (`PRODUCT_PHOTO`, `PRODUCT_VIDEO`, `BROLL`, `LOGO`, `CTA_OVERLAY`, `VOICE_AUDIO`, `CAPTIONS`)
* `source_type`: Nguồn gốc (`PRODUCT_DB`, `GALLERY_DB`, `MANUAL_UPLOAD`, `AI_GENERATED`)
* `source_ref`: Đường dẫn file hoặc URL tham chiếu
* `is_approved`: TINYINT(1) DEFAULT 1 - Xác nhận bản quyền / an toàn
* `date_created`: Unix timestamp

---

## 4. QUY TRÌNH & NGUYÊN TẮC KỸ THUẬT

### 4.1. Video Provider Abstraction (`VideoProviderInterface`)
* Phương thức chuẩn:
  - `createRenderJob($videoProjectData)`: Gửi cấu hình render và nhận `provider_job_id`.
  - `checkJobStatus($providerJobId)`: Kiểm tra tiến độ và trạng thái (PROCESSING, READY, FAILED).
  - `downloadVideoAsset($remoteUrl, $destinationPath)`: Tải thành phẩm với xác thực SSRF và lưu trữ an toàn.
  - `cancelRenderJob($providerJobId)`: Hủy tác vụ nếu cần.
  - `getCapabilities()`: Khai báo năng lực (tỷ lệ khung hình, độ dài, hỗ trợ avatar, tiếng Việt).

### 4.2. Quản trị Asset & Tái sử dụng Phương tiện (Media Reuse)
* Ưu tiên tái sử dụng ảnh từ `table_product.photo` và `table_gallery`.
* Kiểm tra tính sẵn sàng của Asset trước khi đưa vào hàng đợi: Nếu thiếu ảnh/video bắt buộc cho bất kỳ Scene nào -> Chuyển trạng thái sang `WAITING_ASSET`, thông báo cho Admin upload bổ sung.
* Không tự ý cào video lậu/bản quyền từ TikTok/Shopee để tránh vi phạm pháp lý.

### 4.3. Tiếng Việt & Safe Area Phụ đề (Captions & Safe Area)
* Quy chuẩn vùng an toàn (Safe Area) cho video 9:16 TikTok:
  - Tránh cách lề trên 150px (khu vực Live/Search TikTok).
  - Tránh cách lề dưới 250px (khu vực tên Shop/Mô tả/Âm thanh).
  - Tránh cách lề phải 120px (khu vực Avatar/Like/Comment/Share).
* Hỗ trợ từ điển phiên âm thương hiệu (Pronunciation Dictionary) cho các từ chuyên ngành Gym: *Deadlift, Whey, Creatine, Pre-workout, Gymshark, Squat, Strap*.

### 4.4. Cơ chế Hoàn nguyên & Versioning (Không bao giờ ghi đè Video cũ)
* Khi Admin yêu cầu tạo lại (Regenerate): Tạo bản ghi mới `version = v+1`, giữ nguyên file và lịch sử của `v1`.
* Nếu kịch bản gốc trong `table_ai_content` bị thay đổi sau khi render video -> Video cũ tự động đánh dấu cờ `is_outdated = 1` kèm cảnh báo trực quan trên Admin.

### 4.5. Nghiêm ngặt Human Approval Gate (Không Auto-Publish)
* Trạng thái video sau khi tải và kiểm định thành công: `REVIEW_REQUIRED`.
* Admin phải xem trực tiếp video qua trình phát HTML5 `<video controls>`, nghe âm thanh và kiểm tra chữ hiển thị.
* Khi duyệt (`APPROVED`), video chuyển sang trạng thái sẵn sàng cho phân phối (`READY_FOR_PUBLISHING`). **Tuyệt đối không tự động đẩy lên API TikTok / YouTube**.

---

## 5. KẾ HOẠCH BẢO ĐẢM HỒI QUY & KIỂM THỬ (TEST PLAN)

1. **Kiểm thử Cú pháp**: Toàn bộ file mới/sửa phải vượt qua `php -l` (PHP 7.4).
2. **Kiểm thử Module Phase 06 (`test_phase06.php`)**:
   - Khởi tạo Video Project từ Approved Script Phase 05.
   - Kiểm tra rào chắn Content Gate (chặn kịch bản DRAFT).
   - Kiểm tra bộ giải quyết Asset & phát hiện thiếu Asset (`WAITING_ASSET`).
   - Mock Provider Render Flow: QUEUED -> PROCESSING -> RENDERED -> VALIDATING -> REVIEW_REQUIRED.
   - Xử lý lỗi nhà cung cấp (429, 500, Timeout, Invalid Media).
   - Tải về và kiểm định file an toàn (Zero-byte check, SSRF protection).
   - Phát hiện Outdated Script và quản lý đa phiên bản (v1, v2).
   - Quy trình Human Gate & Chặn tuyệt đối Auto-Publish.
3. **Kiểm thử Hồi quy Toàn hệ thống**:
   - `test_phase05.php`: 49/49 PASS.
   - `test_phase04.php`: 35/35 PASS.
   - `test_phase03.php`: 26/26 PASS.
   - `test_regression.php`: 5/5 PASS.
