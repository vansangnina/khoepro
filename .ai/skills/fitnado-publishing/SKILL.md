---
name: fitnado-publishing
description: Kỹ năng quản lý và vận hành Trung tâm Xuất bản & Phân phối TikTok/Mạng xã hội (Publishing Center) cho FITNADO.
---

# FITNADO PUBLISHING CENTER & TIKTOK PUBLISHING SKILL

## 1. NGUYÊN TẮC CỐT LÕI (CORE PRINCIPLES)

1. **APPROVED VIDEO ONLY (Chỉ Xuất Bản Video Đã Phê Duyệt)**:
   - Bài đăng (`table_publish_post`) BẮT BUỘC chỉ được khởi tạo từ dự án video trong `table_video_project` có `status = 'APPROVED'`.
   - Chặn tuyệt đối việc tạo gói bài đăng từ video nháp (`DRAFT`), đang render (`RENDERING`), hoặc đang chờ duyệt (`REVIEW_REQUIRED`).

2. **HUMAN-IN-THE-LOOP & MANUAL PUBLISHING FIRST**:
   - Trọng tâm cốt lõi là quy trình **Manual Publishing Provider**: Cung cấp giao diện đóng gói xuất bản hoàn chỉnh, tải file MP4 chất lượng cao, copy 1-click Caption/Hashtags, xác thực và dán link TikTok Video URL / Post ID thực tế.

3. **PRE-PUBLISH CHECKLIST VALIDATION**:
   - Chuyển trạng thái từ `DRAFT` sang `READY` bắt buộc thỏa mãn 100% checklist: Video MP4 tồn tại cục bộ và dung lượng > 0, caption hợp lệ (1–2200 ký tự), có ít nhất 1 hashtag hợp lệ, kênh xuất bản đang hoạt động (`ACTIVE`), và video không bị lỗi thời.

4. **IMMUTABLE SNAPSHOT ON READY**:
   - Khi bài đăng chuyển sang `READY`, hệ thống tạo snapshot bất biến (`post_snapshot` JSON) đóng băng video URL, caption, hashtags, account, và mã băm `source_hash`.

5. **EDIT INVALIDATION TO DRAFT**:
   - Chỉnh sửa bất kỳ trường nào của bài đăng đang `READY` hoặc `SCHEDULED` sẽ tự động hủy bỏ snapshot và hạ trạng thái về `DRAFT` để yêu cầu kiểm tra checklist lại.

6. **PUBLISHED POST IMMUTABILITY**:
   - Không cho phép sửa trực tiếp bài đã `PUBLISHED`. Muốn đăng lại hoặc đổi nội dung phải dùng tính năng **Nhân bản (Duplicate Post)** để tạo bản nháp mới.

7. **DOUBLE PUBLISH CONCURRENCY LOCK**:
   - Khóa `publish_lock = 1` ngăn chặn đăng trùng lặp do bấm nút nhiều lần hoặc worker chạy song song. Tự động thu hồi stale locks sau 10 phút.

8. **HONEST API STATUS (NO FAKE API SUCCESS)**:
   - `TikTokPublishProvider` khi chưa có cấu hình OAuth trả về `NOT CONFIGURED` và `isConfigured() === false`. Tuyệt đối không giả lập thông báo xuất bản API thành công.

9. **STRICT POST ID & URL VALIDATION**:
   - URL bài đăng thủ công phải hợp lệ RFC URL và thuộc tên miền TikTok (`tiktok.com`, `vm.tiktok.com`, `vt.tiktok.com`). Post ID phải là chuỗi số nguyên dương.

---

## 2. KIẾN TRÚC DỮ LIỆU & LỚP NGHIỆP VỤ

- `table_publish_post`: Quản lý gói bài đăng, video liên kết, caption, hashtags, thumbnail, lịch đăng, trạng thái, provider, snapshot và khóa concurrency.
- `table_publish_account`: Quản lý danh sách kênh/tài khoản mạng xã hội (`TIKTOK`, `YOUTUBE_SHORTS`, `INSTAGRAM_REELS`, `FACEBOOK_REELS`), trạng thái, metadata và credentials (masked).
- `table_publish_log`: Lịch sử vết kiểm toán (Audit Trail) cho mọi thao tác xuất bản, đổi trạng thái, lỗi và cảnh báo.
- `PublishingCenter` (`libraries/class/class.PublishingCenter.php`): Core service quản lý vòng đời bài đăng, pre-publish checklist, snapshot, duplicate, publishNow, markManualPublished.
- `PublishJobQueue` (`libraries/class/class.PublishJobQueue.php`): Queue manager điều phối lịch đăng theo thời gian, stale lock recovery, và retry logic.
- `PublishProviderFactory` & `PublishProviderInterface` (`libraries/class/class.PublishProvider.php`): Multi-provider adapter (`ManualPublishProvider`, `TikTokPublishProvider`).
- Cron Worker: `cron/publish_worker.php` (hỗ trợ CLI và token-protected HTTP endpoint).

---

## 3. CHECKLIST KIỂM THỬ KHI THAY ĐỔI CODE

- [ ] `php -c php.ini -l` PASS 100% trên toàn bộ các file PHP (PHP 7.4).
- [ ] Chạy `php -c php.ini test_phase07.php` đạt 22/22 test assertions.
- [ ] Chạy kiểm thử hồi quy `test_phase06_3.php`, `test_phase06_2.php`, `test_phase06.php`, `test_phase05.php`, `test_phase04.php`, `test_phase03.php` đạt 100% PASS.
- [ ] Xác nhận giao diện AdminLTE hoạt động đầy đủ tại `admin/index.php?com=publishing&act=man`.
