# KHOEPRO — SOCIAL PROVIDER API INTEGRATION REQUIREMENTS

> **Tài liệu phân tích kỹ thuật:** `DOCS-SOCIAL-API-REQUIREMENTS-2026`  
> **Phạm vi nghiên cứu:** TikTok Content Posting API, Meta Graph API (Facebook Page & Reels), YouTube Data API v3 (Shorts).  
> **Nguyên tắc:** Sử dụng API chính thức (Official REST API), bảo mật Credentials/Token, không dùng browser scraping giả lập.

---

## 1. TIKTOK CONTENT POSTING API (OFFICIAL API)

### 1.1. Tổng quan & Luồng xác thực (OAuth 2.0)
- **Cổng nhà phát triển:** [TikTok for Developers](https://developers.tiktok.com/)
- **Scopes bắt buộc:**
  - `user.info.basic`: Đọc thông tin tài khoản cơ bản.
  - `video.upload`: Quyền tải video lên server TikTok.
  - `video.publish`: Quyền yêu cầu xuất bản video hoặc lưu bản nháp.
- **Quy trình cấp phép OAuth:**
  1. Chuyển hướng Admin sang: `https://www.tiktok.com/v2/auth/authorize/` kèm `client_key`, `scope`, `redirect_uri`.
  2. TikTok trả về `code` tại `redirect_uri`.
  3. Server đổi `code` lấy `access_token` (Hạn 24 giờ) và `refresh_token` (Hạn 365 ngày) qua `POST https://open.tiktokapis.com/v2/oauth/token/`.
  4. Hệ thống tự động làm mới `access_token` định kỳ qua `POST https://open.tiktokapis.com/v2/oauth/token/` với `grant_type=refresh_token`.

### 1.2. Luồng Xuất bản Video (Direct Post / Pull from URL)
1. **Bước 1 — Khởi tạo tác vụ tải lên (Initialize Upload)**:
   - Endpoint: `POST https://open.tiktokapis.com/v2/post/publish/video/init/`
   - Header: `Authorization: Bearer {access_token}`, `Content-Type: application/json`
   - Body:
     ```json
     {
       "post_info": {
         "title": "Caption kèm #hashtag",
         "privacy_level": "PUBLIC_TO_EVERYONE",
         "disable_duet": false,
         "disable_stitch": false,
         "disable_comment": false,
         "video_cover_timestamp_ms": 1000
       },
       "source_info": {
         "source": "PULL_FROM_URL",
         "video_url": "https://khoepro.com/upload/video/rendered_video.mp4"
       }
     }
     ```
   - Trả về: `publish_id`.
2. **Bước 2 — Truy vấn trạng thái xuất bản (Polling Status)**:
   - Endpoint: `POST https://open.tiktokapis.com/v2/post/publish/status/fetch/`
   - Body: `{"publish_id": "v_pub_..."}`
   - Trạng thái: `PROCESSING_DOWNLOAD` → `PROCESSING_UPLOAD` → `PUBLISH_COMPLETE` (hoặc `FAILED`).

### 1.3. Giới hạn & Rate Limit
- **Tần suất yêu cầu:** 30 req/min cho mỗi access token.
- **Giới hạn số bài đăng:** Khuyến nghị 3 - 5 bài/ngày/tài khoản để tránh bị thuật toán TikTok nhận diện là spam.

---

## 2. META GRAPH API — FACEBOOK REELS & FANPAGE (OFFICIAL API)

### 2.1. Tổng quan & Luồng xác thực (Graph API v19.0+)
- **Cổng nhà phát triển:** [Meta for Developers](https://developers.facebook.com/)
- **Permissions bắt buộc:**
  - `pages_show_list`: Liệt kê các Fanpage quản lý.
  - `pages_read_engagement`: Đọc tương tác và số liệu bài đăng.
  - `pages_manage_posts`: Đăng bài và quản lý Fanpage.
  - `page_events` / `publish_video`: Tải lên và xuất bản video/Reels.
- **Quy trình Token Lifecycle:**
  1. Nhận User Access Token qua Facebook Login SDK.
  2. Đổi lấy Long-Lived User Access Token (Hạn 60 ngày).
  3. Lấy **Never-Expiring Page Access Token** cho Fanpage:
     `GET https://graph.facebook.com/v19.0/me/accounts?access_token={long_lived_user_token}`.
  4. Lưu Page Access Token an toàn vào CSDL để đăng bài tự động vĩnh viễn không cần đăng nhập lại.

### 2.2. Luồng Xuất bản Facebook Reels
1. **Bước 1 — Khởi tạo phiên tải lên Reel**:
   - Endpoint: `POST https://graph.facebook.com/v19.0/{page_id}/video_reels`
   - Body: `{"upload_phase": "start", "access_token": "{page_access_token}"}`
   - Trả về: `video_id` và `upload_url`.
2. **Bước 2 — Đẩy file video lên Binary URL**:
   - `POST {upload_url}` kèm binary buffer của video.
3. **Bước 3 — Phát hành Reel (Publish)**:
   - Endpoint: `POST https://graph.facebook.com/v19.0/{page_id}/video_reels`
   - Body:
     ```json
     {
       "upload_phase": "finish",
       "video_id": "{video_id}",
       "video_state": "PUBLISHED",
       "description": "Nội dung bài viết Facebook kèm link ưu đãi #khoepro",
       "access_token": "{page_access_token}"
     }
     ```
   - Trả về: `success: true` và `post_id`.

---

## 3. YOUTUBE DATA API V3 — YOUTUBE SHORTS (OFFICIAL API)

### 3.1. Tổng quan & Luồng xác thực (Google OAuth 2.0)
- **Cổng nhà phát triển:** [Google Cloud Console](https://console.cloud.google.com/)
- **Scopes bắt buộc:**
  - `https://www.googleapis.com/auth/youtube.upload`: Tải video lên kênh.
  - `https://www.googleapis.com/auth/youtube.readonly`: Đọc danh sách và chỉ số video.
- **Quy trình Token Lifecycle:**
  1. OAuth Consent Screen cấp quyền qua `redirect_uri`.
  2. Đổi mã `code` lấy `access_token` (Hạn 1 giờ) và `refresh_token` (Vĩnh viễn cho đến khi bị thu hồi).
  3. Server tự động refresh access token trước mỗi lần upload.

### 3.2. Luồng Xuất bản YouTube Shorts (Resumable Upload)
1. **Quy chuẩn định dạng Shorts:** Tỷ lệ dọc 9:16, độ dài ≤ 60 giây, tiêu đề hoặc mô tả chứa `#Shorts`.
2. **Khởi tạo Resumable Upload Session**:
   - Endpoint: `POST https://www.googleapis.com/upload/youtube/v3/videos?uploadType=resumable&part=snippet,status`
   - Header: `Authorization: Bearer {access_token}`, `Content-Type: application/json`
   - Metadata Snippet:
     ```json
     {
       "snippet": {
         "title": "Review Đai Lưng Gym Aolikes #Shorts",
         "description": "Đánh giá chi tiết phụ kiện tập thể hình.\nLink mua ưu đãi ở bình luận.\n#Shorts #KhoePro",
         "tags": ["khoepro", "reviewgym", "tapgym", "thehinh"],
         "categoryId": "17"
       },
       "status": {
         "privacyStatus": "public",
         "selfDeclaredMadeForKids": false
       }
     }
     ```
   - Trả về Header: `Location: {upload_chunk_url}`.
3. **Tải lên Binary Stream & Hoàn tất**:
   - Đẩy binary tệp video `.mp4` lên `{upload_chunk_url}`.
   - Trả về: ID video YouTube (ví dụ: `v=dQw4w9WgXcQ`).

---

## 4. MA TRẬN PHÂN LOẠI LỖI & CHIẾN LƯỢC RETRY

| Mã lỗi | Nền tảng | Bản chất | Hành vi hệ thống |
|---|---|---|---|
| `401 Unauthorized` / `190 Invalid Token` | TikTok / Meta / YT | Hết hạn Access Token | Tự động làm mới qua `refresh_token`. Nếu thất bại → Đánh dấu `REQUIRES_REAUTH`, gửi alert Admin, ngừng retry. |
| `429 Rate Limit Exceeded` | All | Quá tải tần suất | Tạm dừng, tính toán `retry_after = now + 900s` (15 phút), chuyển post về `SCHEDULED`. |
| `500 / 502 / 503 Gateway Error` | All | Lỗi máy chủ mạng xã hội | `TEMPORARY_ERROR` → Thử lại theo Exponential Backoff (1m, 5m, 15m), tối đa 3 lần. |
| `Content Policy Rejected / Copyright` | TikTok / Meta / YT | Nội dung bị sàn từ chối | `PERMANENT_ERROR` → Chuyển post sang `FAILED`, ghi rõ lý do sàn phản hồi, không retry. |
