---
name: fitnado-ai-video
description: Kỹ năng quản lý và vận hành Động cơ Sản xuất Video AI (AI Video Production Engine) cho FITNADO.
---

# FITNADO AI VIDEO PRODUCTION SKILL

## 1. NGUYÊN TẮC CỐT LÕI (CORE PRINCIPLES)

1. **APPROVED CONTENT ONLY (Chỉ dùng Kịch bản Đã Duyệt)**:
   - Video Project BẮT BUỘC chỉ được khởi tạo từ kịch bản TikTok trong `table_ai_content` có `status = 'APPROVED'` hoặc `'APPLIED'`.
   - Chặn tuyệt đối việc tạo video từ kịch bản `DRAFT` hoặc `REVIEW_REQUIRED`.

2. **SHOT PLAN IS SOURCE OF TRUTH (Bảng phân cảnh là Chân lý)**:
   - Video Engine tiêu thụ trực tiếp mảng `shot_plan` (`scene_number`, `duration`, `visual_instruction`, `voiceover`, `on_screen_text`, `asset_requirement`).
   - Tuyệt đối không cho phép AI tự ý viết lại kịch bản làm sai lệch thông điệp và thông số kỹ thuật.

3. **NO INVENTED CLAIMS (Không bịa đặt claim quảng cáo)**:
   - Giữ nguyên vẹn các sự thật về thông số, ưu nhược điểm và không bịa đặt cam kết y tế/chữa bệnh hay testimonial giả mạo.

4. **ASSET RESOLUTION & READINESS (Sẵn sàng Tài nguyên)**:
   - Tái sử dụng ảnh sản phẩm `table_product.photo` và thư viện `table_gallery`.
   - Nếu bất kỳ phân cảnh nào thiếu tài nguyên trực quan bắt buộc, dự án phải chuyển trạng thái sang `WAITING_ASSET` và không được đưa vào hàng đợi render.

5. **VERSION EVERYTHING (Lưu vết mọi phiên bản)**:
   - Khi tạo lại video cho cùng sản phẩm, tạo bản ghi mới với `version = v+1`, giữ nguyên vẹn file video và lịch sử của `v1`.

6. **MEDIA VALIDATION (Kiểm định Media sau Render)**:
   - Kiểm tra zero-byte file, định dạng MP4, thời lượng và tỷ lệ khung hình 9:16 trước khi chuyển sang vòng kiểm duyệt.

7. **STRICT HUMAN APPROVAL (Kiểm duyệt của Con người)**:
   - Video sau khi render bắt buộc chuyển sang trạng thái `REVIEW_REQUIRED`.
   - Admin xem trực tiếp qua trình phát HTML5 `<video controls>` để duyệt (`APPROVED`) hoặc từ chối (`REJECTED`) kèm lý do cụ thể.

8. **NO AUTO-PUBLISHING (Tuyệt đối không Auto-post)**:
   - Phase 06 dừng lại ở video đã duyệt (`APPROVED`). Tuyệt đối không tự động đẩy lên API TikTok / YouTube.

---

## 2. KIẾN TRÚC DỮ LIỆU & LỚP NGHIỆP VỤ

- `table_ai_video`: Dự án video, phân cảnh, file thành phẩm, thumbnail, trạng thái, QC score.
- `table_ai_video_job`: Hàng đợi render bất đồng bộ, concurrency locking, polling và retry.
- `table_ai_video_asset`: Ánh xạ tài nguyên trực quan cho từng Scene.
- `AIVideoEngine` (`libraries/class/class.AIVideoEngine.php`): Core engine quản trị dự án, asset resolution, hashing và QC.
- `AIVideoJobQueue` (`libraries/class/class.AIVideoJobQueue.php`): Queue manager và worker dispatcher.
- `VideoProviderFactory` & `VideoProviderInterface` (`libraries/class/class.VideoProvider.php`): Adapter đa nhà cung cấp (`MockVideoProvider`, `ExternalVideoProvider`, `ManualVideoProvider`).
- Worker: `cron/video_render_worker.php`.

---

## 3. CHECKLIST KIỂM THỬ KHI THAY ĐỔI CODE

- [ ] `php -l` PASS 100% trên toàn bộ các file PHP (PHP 7.4).
- [ ] Chạy `php test_phase06.php` đạt 28/28 test cases.
- [ ] Chạy kiểm thử hồi quy `test_phase05.php`, `test_phase04.php`, `test_phase03.php`, `test_regression.php`.
- [ ] Đẩy commit lên remote repository GitHub `https://github.com/vansangnina/khoepro`.
