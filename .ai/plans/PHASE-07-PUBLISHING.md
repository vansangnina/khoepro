# KẾ HOẠCH TRIỂN KHAI FITNADO PHASE 07 (PHASE-07-PUBLISHING.MD)
## PUBLISHING CENTER & TIKTOK PUBLISHING FOUNDATION

- **Thời gian lập kế hoạch:** 2026-09-20
- **Dự án:** FITNADO Publishing Center (Phase 07)
- **Mục tiêu:** Quản lý toàn bộ quy trình xuất bản video từ FITNADO (Approved Video -> Post Package -> Schedule -> Publish Provider -> Manual / API -> Published -> Post ID / URL) mà KHÔNG phụ thuộc cứng vào TikTok API.

---

## 1. KIỂM TOÁN HỆ THỐNG HIỆN TẠI (SYSTEM AUDIT)

### 1.1. Approved Video Model (`table_ai_video`)
- `table_ai_video` đã có các trường: `id`, `id_product`, `id_content`, `title`, `video_file`, `thumbnail`, `duration_actual`, `cost_estimate`, `status` (`DRAFT`, `WAITING_ASSET`, `READY`, `QUEUED`, `PROCESSING`, `RENDERED`, `VALIDATING`, `REVIEW_REQUIRED`, `APPROVED`, `REJECTED`, `FAILED`, `ARCHIVED`).
- Ràng buộc: Chỉ video có `status = 'APPROVED'` mới được phép khởi tạo Post Package.

### 1.2. Product Model & Affiliate Offers (`table_product`, `table_product_affiliate`)
- `table_product` lưu trữ thông tin sản phẩm chính, giá, slug, mô tả, thông số specs.
- `table_product_affiliate` lưu trữ các ưu đãi đa sàn (Shopee, Lazada, TikTok Shop, Tiki, Brand).
- `table_product_research_evidence` lưu trữ các fact evidence đã kiểm chứng.

### 1.3. AI Content Engine (`table_ai_content`)
- Kịch bản TikTok, Hooks, Title, Shot Plan, Tone guide.
- Post Package sẽ kế thừa caption, hashtags từ Approved AI Content tương ứng.

### 1.4. Hạ tầng Xử lý Nền & Cron (Background Jobs)
- Đã có mô hình chuẩn từ Phase 04, 05, 06: `ResearchJobQueue`, `AIContentJobQueue`, `AIVideoJobQueue`.
- Phase 07 kế thừa mô hình này để xây dựng `PublishJobQueue` và `cron/publish_worker.php`.

### 1.5. Kiểm toán Mã Đăng Mạng Xã Hội Hiện Tại
- Toàn bộ codebase hiện chỉ có `admin/sources/pushOnesignal.php` gửi Web Push Notification.
- **Không có** mã đăng mạng xã hội (Facebook / TikTok / Instagram) nào trước đây.
- Khởi tạo kiến trúc mới chuẩn mực, sạch sẽ, tuân thủ PHP 7.4.

### 1.6. Kiểm toán Cấu hình TikTok API
- `libraries/config.php` hiện **chưa có** cấu hình TikTok Developer App (`client_key`, `client_secret`, `access_token`, `refresh_token`, Content Posting API permissions).
- Hệ thống sẽ **báo cáo trung thực**: `TikTok API: NOT CONFIGURED`.
- Kích hoạt **ManualPublishProvider** làm giải pháp xuất bản chính thức sẵn sàng cho Production.

---

## 2. KIẾN TRÚC POST PACKAGE & CƠ SỞ DỮ LIỆU

### 2.1. Khái niệm Post Package
Post Package là một gói xuất bản độc lập bao gồm:
- **Sản phẩm:** `id_product`, tên sản phẩm, link landing page FITNADO.
- **Video Đã Duyệt:** `id_video`, file MP4, thumbnail, độ dài.
- **Nội dung:** Snapshot Caption, Snapshot Hashtags (chuẩn hóa theo brand, category, problem, không spam vô tội vạ).
- **Affiliate Offer:** `affiliate_offer_id`, sàn thương mại liên kết.
- **Phân phối:** Nền tảng (`tiktok`, `facebook`, `instagram`, `youtube_shorts`, `website`), Tài khoản (`account_id`), Kênh.
- **Lịch trình:** `scheduled_at`, `status`.
- **Nhà cung cấp:** `provider` (`manual`, `tiktok_api`).

### 2.2. Vòng đời Trạng thái (Publish Status Lifecycle)
```text
DRAFT ──▶ READY ──▶ SCHEDULED ──▶ QUEUED ──▶ PUBLISHING ──▶ PUBLISHED
  │         │                                                  │
  └─────────┴───────────────────▶ FAILED / CANCELLED ──────────┘
```

### 2.3. Bảng Cơ sở Dữ liệu

#### 1. `table_publish_post`
- `id`: INT(11) UNSIGNED AUTO_INCREMENT PK
- `id_product`: INT(11) UNSIGNED NOT NULL
- `id_video`: INT(11) UNSIGNED NOT NULL
- `id_ai_content`: INT(11) UNSIGNED NULL
- `platform`: VARCHAR(50) DEFAULT 'tiktok'
- `post_type`: VARCHAR(50) DEFAULT 'VIDEO_POST'
- `title`: VARCHAR(255) NOT NULL
- `caption`: TEXT NOT NULL (Snapshot)
- `hashtags`: VARCHAR(500) NULL (Snapshot)
- `affiliate_offer_id`: INT(11) UNSIGNED NULL
- `landing_url`: VARCHAR(500) NULL
- `disclosure_text`: VARCHAR(255) NULL
- `provider`: VARCHAR(50) DEFAULT 'manual'
- `account_id`: INT(11) UNSIGNED NULL
- `status`: VARCHAR(50) DEFAULT 'DRAFT' (`DRAFT`, `READY`, `SCHEDULED`, `QUEUED`, `PUBLISHING`, `PUBLISHED`, `FAILED`, `CANCELLED`)
- `scheduled_at`: INT(11) UNSIGNED NULL
- `published_at`: INT(11) UNSIGNED NULL
- `external_post_id`: VARCHAR(255) NULL
- `external_post_url`: VARCHAR(500) NULL
- `provider_response`: MEDIUMTEXT NULL (Sanitized)
- `error_message`: TEXT NULL
- `attempts`: INT(11) DEFAULT 0
- `is_outdated`: TINYINT(1) DEFAULT 0
- `snapshot_data`: MEDIUMTEXT NULL (JSON)
- `publish_lock`: VARCHAR(64) NULL
- `date_created`, `date_updated`: INT(11)

#### 2. `table_publish_account`
- `id`: INT(11) UNSIGNED AUTO_INCREMENT PK
- `platform`: VARCHAR(50) DEFAULT 'tiktok'
- `account_name`: VARCHAR(255) NOT NULL
- `account_handle`: VARCHAR(255) NOT NULL (vd: `@fitnado.vn`)
- `channel_id`: VARCHAR(255) NULL
- `provider`: VARCHAR(50) DEFAULT 'manual'
- `status`: VARCHAR(50) DEFAULT 'active'
- `auth_status`: VARCHAR(50) DEFAULT 'MANUAL_ONLY' (`NOT_CONFIGURED`, `AUTHORIZED`, `EXPIRED`, `MANUAL_ONLY`)
- `auth_data`: TEXT NULL
- `token_expires_at`: INT(11) NULL
- `is_default`: TINYINT(1) DEFAULT 0
- `date_created`, `date_updated`: INT(11)

#### 3. `table_publish_log`
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PK
- `id_post`: INT(11) UNSIGNED NOT NULL
- `event`: VARCHAR(50) NOT NULL (`CREATED`, `EDIT`, `READY`, `SCHEDULED`, `QUEUED`, `PUBLISHING`, `PUBLISHED`, `FAILED`, `RETRIED`, `CANCELLED`, `MANUAL_MARK`)
- `old_status`: VARCHAR(50) NULL
- `new_status`: VARCHAR(50) NOT NULL
- `actor`: VARCHAR(100) DEFAULT 'admin'
- `details`: TEXT NULL (JSON)
- `date_created`: INT(11)

---

## 3. PROVIDER ARCHITECTURE

### 3.1. `PublishProviderInterface`
```php
interface PublishProviderInterface {
    public function validate(array $postPackage): array;
    public function publish(array $postPackage): array;
    public function getStatus(string $externalPostId): array;
    public function getPost(string $externalPostId): array;
    public function isConfigured(): bool;
    public function getProviderName(): string;
}
```

### 3.2. `ManualPublishProvider` (Production Fallback Sẵn sàng 100%)
- **Validate:** Kiểm tra file video tồn tại, caption không rỗng, định dạng nền tảng hợp lệ.
- **Publish:** Chuẩn bị gói tải video, nút sao chép caption, sao chép hashtags, mở TikTok Creator Portal.
- **Mark Published:** Xác thực URL bài đăng TikTok thật (domain hợp lệ, không chứa `javascript:`, `file:`, `data:`), cập nhật `status = 'PUBLISHED'` và lưu log.

### 3.3. `TikTokPublishProvider` (Nền tảng API)
- Kiểm tra cấu hình và trả về `isConfigured() === false` nếu chưa có OAuth credentials.
- Cung cấp khung phương thức chuẩn cho TikTok Content Posting API.

---

## 4. KHOẢNG RÀO CHẮN & BẢO VỆ DỮ LIỆU (GATES & GUARDS)

1. **Human Gate:** Chỉ video có `status = 'APPROVED'` mới được tạo Post Package.
2. **Outdated Gate:** Cảnh báo nếu kịch bản/thông số sản phẩm bị thay đổi sau khi tạo video.
3. **Snapshot Immutability:** Khi chuyển sang `READY`, đóng băng toàn bộ caption, hashtags, phiên bản video, ưu đãi affiliate vào `snapshot_data`.
4. **Edit After Ready:** Bất kỳ chỉnh sửa nội dung nào sau `READY` sẽ hoàn nguyên bài đăng về `DRAFT` để kiểm duyệt lại.
5. **Double Publish Protection:** Khóa `publish_lock` nguyên tử ngăn chặn hai tiến trình cùng xuất bản một bài đăng.
6. **No Secrets in Logs:** Loại bỏ `client_secret`, `access_token`, `refresh_token` trước khi ghi vào log database.

---

## 5. LỘ TRÌNH THỰC HIỆN

1. **Hotfix Phase 06:** Fact Evidence Traceability & Audio/Video Duration Sync.
2. **Database Migration:** Tạo `table_publish_post`, `table_publish_account`, `table_publish_log`.
3. **Core Classes:** `class.PublishProvider.php`, `class.PublishingCenter.php`, `class.PublishJobQueue.php`.
4. **Worker:** `cron/publish_worker.php`.
5. **Admin UI:** `admin/sources/publishing.php`, các view template `publishing/*.php`, menu AdminLTE.
6. **Testing & Verification:** `test_phase07.php`, test hồi quy các phase cũ, kiểm tra cú pháp PHP 7.4.
7. **Tài liệu & Kỹ năng:** Update `.ai/DATABASE.md`, `.ai/ARCHITECTURE.md`, `.ai/BUSINESS_RULES.md`, `.ai/CHANGELOG.md`, tạo `.ai/skills/fitnado-publishing/SKILL.md` và `.ai/reports/PHASE-07-PUBLISHING-TEST-REPORT.md`.
