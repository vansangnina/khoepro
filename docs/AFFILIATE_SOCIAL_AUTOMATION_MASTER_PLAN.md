# KHOEPRO — AFFILIATE CONTENT & MULTI-SOCIAL AUTOMATION MASTER PLAN

> **Mã tài liệu:** `DOCS-KHOEPRO-MASTER-2026-01`  
> **Hệ thống:** KhoePro (Fitness & Nutrition E-Commerce & Marketing Operations Suite)  
> **Môi trường:** Local Development → Staging Test → Production Review  
> **Nguyên tắc cốt lõi:** REUSE → EXTEND → REFACTOR NHẸ (Không làm lại phần đang chạy tốt, tuyệt đối không tự deploy production).

---

## 1. KIẾN TRÚC HIỆN TẠI

Hệ thống KhoePro hiện tại được xây dựng trên nền tảng PHP 7.4/8.x kết hợp MySQL (PDO), AdminLTE 3 UI và một hệ sinh thái AI Operations Suite đã trải qua 10 giai đoạn phát triển:

```
[Nguồn Dữ Liệu]                  [Lõi Xử Lý & Đánh Giá]                [Sản Xuất Nội Dung]               [Phân Phối & Đo Lường]
AccessTrade API / Seeds  ───►  ProductResearch / Filter  ───►  AIContentEngine (Gemini/OpenAI)  ───►  AIVideoEngine / VideoComposer
          │                               │                                     │                                    │
          ▼                               ▼                                     ▼                                    ▼
table_product_affiliate        table_product_research                 table_ai_content                     table_ai_video
          │                               │                                     │                                    │
          └───────────────────────────────┴─────────────────────────────────────┼────────────────────────────────────┘
                                                                                ▼
                                                                     ComplianceGuardrail (21 Rules)
                                                                                │
                                                                                ▼
                                                                  PublishingCenter / TikTok Post
                                                                                │
                                                                                ▼
                                                                 AnalyticsService / WinnerEngine
```

### Các lớp kiến trúc chính đang vận hành:
1. **Catalog & Affiliate Layer**: Quản lý sản phẩm gốc (`table_product`), ưu đãi affiliate đa sàn (`table_product_affiliate`), ghi nhận click (`table_affiliate_click`), đồng bộ đối soát đơn hàng từ AccessTrade API v1 (`table_affiliate_conversion`).
2. **Product Intelligence & Discovery Layer**: Cào và lọc từ khóa hạt giống (`table_product_research_seed`), chấm điểm đa tiêu chí (Demand, Content, Commission, Competition, SEO), bộ lọc 2 tầng `ProductRelevanceFilter` (Rule-based Blacklist + AI Gemini Relevance).
3. **AI Content Studio**: Sinh bài phân tích chuyên sâu, Hook kịch bản, Video Script, SEO Metadata (`table_ai_content`), kiểm soát phiên bản (v1, v2, v3) và băm kiểm tra dữ liệu (`source_hash`).
4. **AI Video Production**: Tạo video từ kịch bản approved (`table_ai_video`), cơ chế hybrid ghép ảnh/video/nhãn (`VideoComposer`), tích hợp TTS HD Beeknoee / Google Baseline, render video qua FFmpeg engine nội bộ.
5. **Publishing Center**: Đóng gói bài đăng (`table_publish_post`), kiểm tra điều kiện xuất bản (Human Gate, Outdated Gate), hỗ trợ Manual Publisher và nền móng TikTok API Publisher.
6. **Analytics & Optimization**: Theo dõi sự kiện (`table_analytics_event`), đối soát CSV/API, đánh giá maturity (`WinnerDetectionEngine`), đề xuất thử nghiệm A/B (`OptimizationEngine`).
7. **Operations Control Center**: Quản lý trạng thái worker (`table_system_worker_status`), cảnh báo deduplicated (`table_system_alert`), nhật ký can thiệp (`table_operations_override_log`).

---

## 2. CHỨC NĂNG HIỆN TẠI CÓ THỂ REUSE (TÁI SỬ DỤNG TỐI ĐA)

| Phân hệ | File / Class hiện có | Khả năng tái sử dụng (Reuse) |
|---|---|---|
| **Affiliate Core** | `class.AccessTradeProvider.php`<br>`class.Affiliate.php` | • Đã có sẵn kết nối AccessTrade Publisher API v1 (Search Campaigns, Datafeeds, Deep Link generation sub1-sub4, Transaction Reconciliation).<br>• Giữ nguyên làm provider chuẩn đầu tiên, bọc trong interface trừu tượng. |
| **Product Filter** | `class.ProductRelevanceFilter.php` | • Đã có bộ lọc từ khóa Gym/Fitness âm tính (Blacklist 60+ cụm từ salon, mỹ phẩm, tài chính) và dương tính (100+ từ khóa thể hình).<br>• Tái sử dụng 100% làm tầng tiền lọc trước khi chấm điểm. |
| **Product Scoring** | `class.ProductResearch.php` | • Đã có thuật toán tính Demand Score, Content Score, Commission Score, Competition Score, SEO Score với trọng số tùy biến lưu trong CSDL.<br>• Tái sử dụng logic toán học và mở rộng thêm platform scoring (TikTok, Facebook, YouTube). |
| **Content Engine** | `class.AIContentEngine.php`<br>`class.AIResearchAgent.php` | • Đã có pipeline gom dữ liệu thực (`aggregateProductData`), tạo source hash SHA-256 chống hallucination, sinh JSON có cấu trúc qua Gemini/OpenAI.<br>• Tái sử dụng để tạo Master Content Package. |
| **Compliance** | `class.ComplianceGuardrail.php` | • Đã có 21 nguyên tắc an toàn nội dung, phân loại rủi ro (LOW, MEDIUM, HIGH, BLOCKED), quét từ cấm y tế/tài chính/mỹ phẩm.<br>• Tái sử dụng làm Content Policy Engine trung tâm. |
| **Video Production** | `class.AIVideoEngine.php`<br>`class.VideoComposer.php`<br>`class.VideoProvider.php` | • Đã có pipeline xử lý FFmpeg, template Visual, lồng tiếng Voiceover TTS, phụ đề tự động an toàn safe area, quản lý tài nguyên assets.<br>• Tái sử dụng toàn bộ pipeline render video. |
| **Publishing Queue** | `class.PublishingCenter.php`<br>`class.PublishJobQueue.php` | • Đã có bảng `table_publish_post`, `table_publish_account`, `table_publish_log`, cơ chế snapshot bài đăng, khóa lock chống chạy trùng.<br>• Mở rộng thêm scheduler đa tài khoản và các platform publishers. |
| **Analytics & Winner**| `class.AnalyticsService.php`<br>`class.WinnerDetectionEngine.php` | • Đã có theo dõi sự kiện, đối soát đơn hàng, tính CTR, CVR, ROI, phân loại sản phẩm theo maturity.<br>• Tái sử dụng để làm feedback loop cho Product Intelligence. |
| **Operations** | `class.OperationsService.php` | • Heartbeat worker, ghi nhận alert, bật tắt safe mode/automation switches.<br>• Tái sử dụng cho toàn bộ cron jobs tự động. |

---

## 3. NHỮNG CHỨC NĂNG THIẾU (CẦN EXTEND / VIẾT MỚI)

1. **Affiliate Provider Abstraction**:
   - Hiện tại logic AccessTrade đang gắn chặt vào `AccessTradeProvider`.
   - *Cần bổ sung:* `AffiliateProviderInterface`, `AffiliateProviderFactory`, chuẩn hóa `NormalizedProductDTO`, bảng `table_affiliate_provider` để quản lý nhiều nguồn (Shopee Open API, Lazada, TikTok Shop Partner, Custom Feed).
2. **Multi-Platform Scoring & Candidate Reason Engine**:
   - Hiện chỉ có `total_score` chung.
   - *Cần bổ sung:* Điểm số chuyên biệt `global_score`, `tiktok_score`, `facebook_score`, `youtube_score`, lưu vết lý do chọn sản phẩm (`selection_reason`, `eligibility_status`, `cooldown_until`).
3. **Master Content & Platform Content Adapters**:
   - Hiện tại `AIContentEngine` sinh kịch bản TikTok đơn lẻ.
   - *Cần bổ sung:* Sinh **Master Content Package** (Facts, Selling Points, Visual Assets, Offers) → tự động phân nhánh thành các gói nội dung nền tảng (TikTok Hook/Script/Hashtags, Facebook Reel Caption/Text, YouTube Shorts Title/Description/Tags).
4. **Content Policy & Pre-publish Gate (PASS / WARNING / FAIL)**:
   - Cần chuẩn hóa output của `ComplianceGuardrail` thành 3 trạng thái rõ ràng:
     - `PASS` → Chuyển vào Approval Queue.
     - `WARNING` → Đưa vào hàng đợi Admin xem xét đặc biệt kèm lý do.
     - `FAIL` → Chặn tuyệt đối, lưu log vi phạm.
5. **Approved Content Pool & Dynamic Scheduler Buffer**:
   - Hiện tại hệ thống lên lịch dựa trên bài post riêng lẻ.
   - *Cần bổ sung:* Khái niệm **Approved Content Pool** (kho nội dung đệm đã duyệt). Worker chỉ rút bài từ Pool theo hạn ngạch ngày (ví dụ: TikTok 3 bài/ngày, FB 2 bài/ngày) và tuân thủ các quy tắc giãn cách (Cooldown: sản phẩm lặp lại ≥ 14 ngày, merchant ≥ 3 bài, content angle ≥ 30 ngày).
6. **Multi-Social Provider Abstraction**:
   - Hiện tại `PublishProvider.php` chỉ có `ManualPublishProvider` và khung `TikTokPublishProvider`.
   - *Cần bổ sung:* `SocialPublisherInterface`, `TikTokPublisher`, `FacebookPublisher` (Meta Graph API / Reels), `YouTubePublisher` (YouTube Data API v3), `SocialPublisherFactory`.
7. **Social Account Management & Safe Token Vault**:
   - Hiện bảng `table_publish_account` mới chỉ lưu tài khoản cơ bản.
   - *Cần bổ sung:* Mã hóa credentials/tokens, cơ chế Refresh Token tự động, phân loại trạng thái kết nối (`ACTIVE`, `TOKEN_EXPIRED`, `REQUIRES_REAUTH`), quản lý nhiều kênh cho cùng một nền tảng.
8. **Feedback Loop to Product Intelligence**:
   - Chưa có tiến trình tự động đưa chỉ số tương tác mạng xã hội (Views, Likes, CTR, Conversions) về cập nhật lại trọng số và độ ưu tiên sản phẩm cho chu kỳ chọn lọc tiếp theo.

---

## 4. DATABASE HIỆN TẠI LIÊN QUAN

Hệ thống đang sở hữu 22 bảng dữ liệu vận hành:

```
├── Catalog & Affiliate:
│   ├── table_product (Sản phẩm gốc, specs, review_score, expert_pros/cons)
│   ├── table_product_affiliate (Offers affiliate đa sàn, commission, at_campaign_id, is_accesstrade)
│   ├── table_affiliate_click (Tracking click, IP hash, sub-IDs)
│   └── table_affiliate_conversion (Đơn hàng đối soát, hoa hồng, at_transaction_id)
├── Discovery & Intelligence:
│   ├── table_product_research (Ứng viên nghiên cứu, demand_score, content_score, total_score)
│   ├── table_product_research_seed (Từ khóa hạt giống ngách Gym/Fitness)
│   ├── table_product_research_job (Hàng đợi tác vụ cào & phân tích AI)
│   ├── table_product_research_evidence (Bằng chứng kiểm chứng dữ liệu)
│   └── table_product_research_snapshot (Lịch sử biến động điểm số)
├── AI Content & Video:
│   ├── table_ai_content (Kho nội dung AI, versions, source_hash, quality_check)
│   ├── table_ai_content_job (Hàng đợi sinh nội dung nền)
│   ├── table_product_content_backup (Bản sao lưu nội dung trước khi ghi đè)
│   ├── table_ai_video (Dự án video, template, voice, render status, video_file)
│   ├── table_ai_video_job (Hàng đợi render FFmpeg / Veo)
│   └── table_ai_video_asset (Tài nguyên hình ảnh sản phẩm, âm thanh)
├── Publishing & Social:
│   ├── table_publish_post (Gói bài đăng, video, caption, hashtags, status, scheduled_at)
│   ├── table_publish_account (Tài khoản mạng xã hội, auth_status, provider)
│   └── table_publish_log (Audit trail sự kiện xuất bản)
├── Analytics & Optimization:
│   ├── table_analytics_event (Sự kiện hành vi người dùng)
│   ├── table_conversion_import_log (Lịch sử tải CSV đối soát)
│   ├── table_winner_evaluation (Đánh giá Winner & Maturity)
│   ├── table_analytics_setting (Cấu hình trọng số, ngưỡng, rate limit)
│   ├── table_optimization_recommendation (Gợi ý tối ưu A/B)
│   └── table_optimization_experiment (Thử nghiệm A/B đang chạy)
└── Operations Center:
    ├── table_system_worker_status (Heartbeat & tiến độ background workers)
    ├── table_system_alert (Cảnh báo & sự cố tập trung có deduplication)
    └── table_operations_override_log (Nhật ký can thiệp ngân sách & an toàn)
```

---

## 5. FILE / SOURCE HIỆN TẠI LIÊN QUAN

```
khoepro/
├── libraries/
│   ├── config.php (Cấu hình hệ thống, DB, API keys: AccessTrade, Beeknoee, FFmpeg)
│   ├── config-affiliate.php (Cấu hình nền tảng Affiliate, review types)
│   ├── autoload.php
│   └── class/
│       ├── class.AccessTradeProvider.php (AccessTrade API client)
│       ├── class.Affiliate.php (Tracking & platform helper)
│       ├── class.ProductResearch.php (Scoring & duplicate check)
│       ├── class.ProductRelevanceFilter.php (Two-stage fitness filter)
│       ├── class.AIResearchAgent.php (Gemini/OpenAI client)
│       ├── class.AIContentEngine.php (Master prompt & content generator)
│       ├── class.ComplianceGuardrail.php (21 safety rules engine)
│       ├── class.AIVideoEngine.php (Video lifecycle management)
│       ├── class.VideoComposer.php (FFmpeg rendering & overlay engine)
│       ├── class.VideoProvider.php (Beeknoee & baseline provider)
│       ├── class.PublishingCenter.php (Post packages & safety gates)
│       ├── class.PublishProvider.php (Manual & TikTok API provider)
│       ├── class.PublishJobQueue.php (Publishing background scheduler)
│       ├── class.AnalyticsService.php (Event tracking & attribution)
│       ├── class.WinnerDetectionEngine.php (Winner rules engine)
│       ├── class.OptimizationEngine.php (A/B testing loop)
│       └── class.OperationsService.php (Health, alerts, switches)
├── cron/
│   ├── accesstrade_sync_worker.php (Đồng bộ đơn AccessTrade)
│   ├── product_research_worker.php (Quét & chấm điểm ứng viên)
│   ├── ai_content_worker.php (Sinh nội dung nền)
│   ├── video_render_worker.php (Render video FFmpeg/Veo nền)
│   └── publish_worker.php (Xuất bản bài đăng đã lên lịch)
└── admin/
    ├── sources/
    │   ├── affiliate.php, product_research.php, ai_content.php,
    │   ├── ai_video.php, publishing.php, analytics.php,
    │   ├── optimization.php, operations.php
    └── templates/ (Tương ứng với các module admin trên)
```

---

## 6. CRON HIỆN TẠI

1. `cron/accesstrade_sync_worker.php`: Đồng bộ đơn hàng, hoa hồng từ AccessTrade API định kỳ (hỗ trợ CLI và Web HTTP có Token bảo vệ).
2. `cron/product_research_worker.php`: Lấy seeds, quét sản phẩm tiềm năng, chạy bộ lọc 2 tầng và chấm điểm.
3. `cron/ai_content_worker.php`: Xử lý hàng đợi sinh nội dung AI chạy nền.
4. `cron/video_render_worker.php`: Xử lý hàng đợi render video qua FFmpeg/Beeknoee.
5. `cron/publish_worker.php`: Quét các bài đăng đến giờ hẹn trong `table_publish_post` để kích hoạt xuất bản hoặc chuyển trạng thái sẵn sàng cho Admin đăng thủ công.

---

## 7. API / PROVIDER HIỆN TẠI

1. **AccessTrade Publisher API v1**:
   - Endpoints: `/v1/campaigns`, `/v1/datafeeds`, `/v1/toplink/customlink`, `/v1/orders`.
   - Trạng thái: **REAL API TESTED & READY** (Đã kiểm tra kết nối thực tế).
2. **Google Gemini / OpenAI (AI Content & Research)**:
   - Providers: Google Gemini API (gemini-1.5-flash / pro), OpenAI API.
   - Trạng thái: **INTEGRATED & WORKING**.
3. **Beeknoee AI & TTS**:
   - Endpoints: TTS HD (`openai/tts-1-hd`), Veo Video generation preview.
   - Trạng thái: **INTEGRATED & WORKING**.
4. **FFmpeg Server Engine**:
   - Xử lý cục bộ trên server (cắt ghép video, chèn watermark, phụ đề srt, âm thanh nền, chuyển cảnh).
   - Trạng thái: **INTEGRATED & WORKING**.
5. **TikTok Publishing**:
   - Hiện hữu: `ManualPublishProvider` (100% Production Ready) + Khung tích hợp `TikTokPublishProvider` (Chờ cấp OAuth credentials từ TikTok Developer App).
6. **Meta / Facebook Graph API & YouTube Data API v3**:
   - Hiện hữu: Chưa tích hợp chính thức (Sẽ bổ sung tài liệu và provider).

---

## 8. CÁC VẤN ĐỀ / RỦI RO PHÁT HIỆN & BIỆN PHÁP KIỂM SOÁT

| Rủi ro phát hiện | Mức độ | Biện pháp kiểm soát trong kiến trúc mới |
|---|---|---|
| **Hardcode nguồn AccessTrade** | Trung bình | Tạo `AffiliateProviderInterface` bọc ngoài, giữ `AccessTradeProvider` nguyên vẹn nhưng biến nó thành một implementation chuẩn. |
| **Đăng trùng lặp sản phẩm/video** | Cao | Bổ sung `table_social_schedule_rule` với cấu hình Cooldown nghiêm ngặt: kiểm tra lịch sử post theo `id_product`, `id_video`, `merchant`, `content_angle`. |
| **Chạy trùng Cron (Duplicate Post)** | Cao | Sử dụng cơ chế `publish_lock` (token khóa nguyên tử) và giao dịch DB với `FOR UPDATE` khi nhận job. |
| **Chi phí AI/Video mất kiểm soát** | Cao | Giữ vững cơ chế Daily Cost Limit và Safe Mode trong `OperationsService` & `config['video_composer']['max_ai_video_cost_per_video']`. |
| **AI bịa đặt thông tin (Hallucination)** | Rất cao | Giữ nguyên quy tắc `generateSourceHash` và `ComplianceGuardrail`: chỉ trích xuất dữ liệu có trong DB, nếu thiếu phải bỏ qua, không được suy diễn số liệu. |
| **Lỗi xác thực Token mạng xã hội** | Trung bình | Tách riêng mã lỗi: `TEMPORARY_ERROR` (retry backoff), `AUTH_EXPIRED` (chuyển sang `ACCOUNT_ACTION_REQUIRED`, gửi alert, không retry vô hạn). |
| **Tự ý phá vỡ tính tương thích ngược** | Rất cao | Mọi migration là **Non-destructive** (chỉ `ADD COLUMN`, `CREATE TABLE IF NOT EXISTS`, không `DROP`/`RENAME`). Không sửa đổi bảng lõi website `table_product`. |

---

## 9. KIẾN TRÚC ĐỀ XUẤT (TARGET ARCHITECTURE)

Thiết kế hệ thống theo mô hình Pipeline khép kín, phân tách độc lập giữa Nguồn dữ liệu (Affiliate) - Lõi trí tuệ (Intelligence) - Sản xuất (Content/Video Studio) - Phân phối (Multi-Social Scheduler) - Phản hồi (Analytics Feedback):

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 1. AFFILIATE CONNECTORS LAYER                                                          │
│   [AccessTradeProvider]  │  [ShopeeProvider]  │  [TikTokShopProvider]  │  [CustomFeed] │
│   └──────────────────────┴─────────┬──────────┴────────────────────────┴───────────────┘
│                                    ▼
│                     Standardized NormalizedProductDTO
└────────────────────────────────────┬───────────────────────────────────────────────────┘
                                     ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 2. PRODUCT INTELLIGENCE & MULTI-PLATFORM SCORING                                       │
│   • Eligibility Gate (Active, In-Stock, Valid URL, Quality Image, Cooldown Check)      │
│   • Multi-Platform Scoring: Global (100) | TikTok (100) | Facebook (100) | YouTube(100)│
│   • Candidate Selector with Logged Selection Reasons (table_content_candidate)         │
└────────────────────────────────────┬───────────────────────────────────────────────────┘
                                     ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 3. CONTENT ENGINE & ADAPTERS                                                           │
│   • Master Content Package (Factual Specs, Real Review, Pain Points, Hooks, Offer)     │
│   • Platform Adapters:                                                                 │
│       ├─► TikTok Adapter (Fast Hook, 30s Script, Captions, Trending Tags, Bio CTA)     │
│       ├─► Facebook Reel Adapter (Benefit Hook, Engaging Body Text, Direct Link CTA)    │
│       └─► YouTube Shorts Adapter (SEO Title, Focused Hook, Description, Tags)          │
└────────────────────────────────────┬───────────────────────────────────────────────────┘
                                     ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 4. AI VIDEO ENGINE & QUALITY GATE                                                      │
│   • Video Composer (Economy / Hybrid 1-2 AI Scenes / Veo Video)                        │
│   • Quality Gate: Resolution Check, Audio Sync, Duration Check, Safe Area, File Exist  │
└────────────────────────────────────┬───────────────────────────────────────────────────┘
                                     ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 5. POLICY VALIDATION & HUMAN APPROVAL POOL                                             │
│   • ComplianceGuardrail Scan (PASS / WARNING / FAIL)                                   │
│   • Admin Approval Queue (Preview, Edit Script, Re-render, Approve, Reject)            │
│   • APPROVED CONTENT POOL (Buffer đệm nội dung sẵn sàng đăng)                          │
└────────────────────────────────────┬───────────────────────────────────────────────────┘
                                     ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 6. DYNAMIC SOCIAL SCHEDULER & DISPATCHER                                               │
│   • Account-level Quota (Ví dụ: TikTok 3/ngày, FB 2/ngày, YT 1/ngày)                   │
│   • Posting Time Windows (09:00, 14:00, 20:00)                                         │
│   • Anti-Spam & Cooldown Enforcer (Product ≥ 14d, Merchant ≥ 3 posts, Angle ≥ 30d)    │
│   • Atomic Lock & Idempotent Post Dispatcher                                           │
└────────────────────────────────────┬───────────────────────────────────────────────────┘
                                     ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 7. MULTI-SOCIAL PUBLISHERS LAYER                                                       │
│   [TikTokPublisher]    │  [FacebookPublisher]  │  [YouTubePublisher]  │ [ManualFallback]│
│   └────────────────────┴───────────┬───────────┴──────────────────────┴────────────────┘
│                                    ▼
│                      Execution Status & External ID
└────────────────────────────────────┬───────────────────────────────────────────────────┘
                                     ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 8. PERFORMANCE ANALYTICS, ATTRIBUTION & FEEDBACK LOOP                                  │
│   • Social Metrics Ingestion (Views, Likes, Shares, CTR)                               │
│   • Affiliate Attribution (Sub-ID Matching: Post -> Click -> Order -> Commission)      │
│   • Feedback Loop -> Tự động cập nhật Score & Trọng số cho Product Intelligence        │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 10. DATABASE MIGRATION DỰ KIẾN (NON-DESTRUCTIVE)

Tất cả các bảng mới tuân thủ tiền tố `table_` và charset `utf8mb4_unicode_ci`:

### 10.1. Mở rộng Bảng Hiện Tại (Idempotent ALTER):
- `table_product_research`: Thêm cột `tiktok_score`, `facebook_score`, `youtube_score`, `platform_scores_json`, `last_content_created_at`, `content_count`.
- `table_publish_account`: Thêm cột `api_config_encrypted`, `refresh_token_encrypted`, `token_scope`, `rate_limit_per_day`, `daily_posted_count`, `last_posted_at`.
- `table_publish_post`: Thêm cột `master_content_id`, `id_candidate`, `retry_after`, `idempotency_key`, `content_angle`.

### 10.2. Bảng Mới Cần Tạo (Non-destructive `CREATE TABLE IF NOT EXISTS`):
1. `table_affiliate_provider`: Quản lý danh sách nguồn Affiliate (AccessTrade, Shopee, TikTok Shop,...), cấu hình sync, rate limit, trạng thái.
2. `table_content_candidate`: Lưu trữ các sản phẩm được chọn từ Product Intelligence kèm lý do (`selection_reason`), platform mục tiêu, điểm số chi tiết, trạng thái (`PENDING`, `APPROVED`, `PROCESSED`, `DISCARDED`).
3. `table_master_content`: Lưu gói nội dung gốc (Master Content Package) độc lập nền tảng, chứa đầy đủ sự thật sản phẩm, selling points, hình ảnh chuẩn.
4. `table_social_schedule_rule`: Cấu hình lịch đăng linh hoạt theo tài khoản (Giờ đăng, số lượng bài/ngày, khoảng cách cooldown sản phẩm/merchant/giao diện).
5. `table_social_post_metric`: Lưu trữ chỉ số hiệu suất bài đăng từ mạng xã hội (Views, Likes, Comments, Shares, Watch time, Impressions) theo từng mốc thời gian.

---

## 11. FILE DỰ KIẾN TẠO MỚI

1. `libraries/class/class.AffiliateProviderInterface.php`: Định nghĩa Interface chuẩn cho mọi nhà cung cấp Affiliate.
2. `libraries/class/class.AffiliateProviderFactory.php`: Factory khởi tạo provider phù hợp (`accesstrade`, `shopee`, `custom_feed`).
3. `libraries/class/class.NormalizedProductDTO.php`: Đối tượng dữ liệu sản phẩm chuẩn hóa thống nhất toàn hệ thống.
4. `libraries/class/class.ContentCandidateEngine.php`: Lõi tự động chọn sản phẩm tốt nhất từ Product Intelligence và kiểm tra điều kiện Eligibility.
5. `libraries/class/class.MasterContentPackage.php`: Đóng gói Master Content và điều phối sinh biến thể cho TikTok, Facebook, YouTube.
6. `libraries/class/class.SocialPublisherInterface.php`: Định nghĩa Interface chuẩn cho việc xuất bản lên các mạng xã hội.
7. `libraries/class/class.TikTokPublisher.php`: Provider chính thức cho TikTok Content Posting API.
8. `libraries/class/class.FacebookPublisher.php`: Provider cho Meta Graph API (Facebook Page & Reels).
9. `libraries/class/class.YouTubePublisher.php`: Provider cho YouTube Data API v3 (Shorts).
10. `libraries/class/class.SocialPublisherFactory.php`: Factory điều phối Publisher dựa trên nền tảng và tài khoản.
11. `libraries/class/class.SocialSchedulerEngine.php`: Bộ lập lịch thông minh, kiểm soát hàng đợi, hạn ngạch (quota) và quy tắc chống spam.
12. `libraries/class/class.SocialAnalyticsSync.php`: Thu thập chỉ số hiệu suất từ các mạng xã hội và liên kết đơn hàng đối soát.
13. `docs/SOCIAL_PROVIDER_API_REQUIREMENTS.md`: Tài liệu nghiên cứu chi tiết API chính thức của TikTok, Meta Graph API và YouTube API.
14. `database/migrations/phase12_affiliate_social_automation.sql`: File SQL migration duy nhất cho toàn bộ nâng cấp.

---

## 12. FILE DỰ KIẾN SỬA (EXTEND & REFACTOR NHẸ)

1. `libraries/class/class.AccessTradeProvider.php`: Implement `AffiliateProviderInterface`, chuẩn hóa output về `NormalizedProductDTO` mà không sửa logic API sẵn có.
2. `libraries/class/class.ProductResearch.php`: Thêm hàm tính điểm đa nền tảng (`calculatePlatformScores`) và mở rộng kiểm tra Cooldown.
3. `libraries/class/class.AIContentEngine.php`: Tích hợp với `MasterContentPackage` để tạo nội dung đa nền tảng đồng bộ từ một nguồn sự thật.
4. `libraries/class/class.PublishingCenter.php`: Tích hợp `SocialPublisherFactory` và cơ chế Approved Pool.
5. `libraries/class/class.PublishJobQueue.php`: Bổ sung kiểm tra quota ngày, cooldown sản phẩm/merchant và cơ chế retry có backoff.
6. `cron/publish_worker.php`: Cập nhật xử lý đa provider và ghi nhận metrics.
7. `admin/sources/publishing.php` & `admin/templates/publishing/`: Bổ sung UI quản lý Social Accounts đa kênh, Calendar phân loại theo platform, Approved Pool buffer counter.
8. `admin/sources/product_research.php` & `admin/templates/product_research/`: Hiển thị điểm số đa nền tảng (TikTok, FB, YT) và lý do đề xuất ứng viên.

---

## 13. CÁC PHASE TRIỂN KHAI CHI TIẾT

```
PHASE 01: Chuẩn hóa Affiliate Provider Abstraction & Normalized Product DTO (Không phá AccessTrade)
   └── Mục tiêu: Tách rời AccessTrade thành 1 provider chuẩn, tạo Interface và Factory dùng chung.
   
PHASE 02: Nâng cấp Product Intelligence, Multi-Platform Scoring & Candidate Selector
   └── Mục tiêu: Tính điểm Global / TikTok / Facebook / YouTube, lưu Content Candidates kèm lý do chọn.

PHASE 03: Master Content Package & Platform Adapters (TikTok / Facebook / YouTube)
   └── Mục tiêu: Sinh nội dung từ một Master Content Package duy nhất, ngăn chặn AI Hallucination.

PHASE 04: Content Policy Engine & Strict Quality Gate
   └── Mục tiêu: Chuẩn hóa kết quả kiểm định (PASS / WARNING / FAIL), chặn nội dung vi phạm hoặc thiếu cơ sở.

PHASE 05: Approved Content Pool & Human Approval Management
   └── Mục tiêu: Xây dựng vùng đệm nội dung đã duyệt, giao diện Admin duyệt/từ chối/chỉnh sửa nhanh.

PHASE 06: Multi-Social Publisher Abstraction & Account Vault
   └── Mục tiêu: Chuẩn hóa SocialPublisherInterface, quản lý tài khoản TikTok/Facebook/YouTube an toàn.

PHASE 07: Nghiên cứu & Tài liệu hóa API Mạng Xã Hội Chính Thức
   └── Mục tiêu: Xuất bản tài liệu `docs/SOCIAL_PROVIDER_API_REQUIREMENTS.md` chi tiết OAuth, Quota, Endpoint.

PHASE 08: Triển khai TikTok Publisher (Official API + Manual Fallback)
   └── Mục tiêu: Upload video & xuất bản qua TikTok Content Posting API khi có token, fallback Manual an toàn.

PHASE 09: Triển khai Facebook / Fanpage / Reels Publisher
   └── Mục tiêu: Tích hợp Meta Graph API xuất bản Reels lên Fanpage.

PHASE 10: Triển khai YouTube Shorts Publisher
   └── Mục tiêu: Tích hợp YouTube Data API v3 tải lên Shorts.

PHASE 11: Dynamic Social Scheduler, Cooldown Rules & Publishing Queue
   └── Mục tiêu: Lập lịch thông minh theo khung giờ, hạn ngạch ngày, khóa chống trùng bài và retry backoff.

PHASE 12: Social Analytics Ingestion & Performance Tracking
   └── Mục tiêu: Thu thập Views, Likes, Clicks từ social posts định kỳ.

PHASE 13: Affiliate Attribution & Full-Funnel Tracking
   └── Mục tiêu: Kết nối Post -> Social Click -> Affiliate Click -> Order -> Commission.

PHASE 14: Data Feedback Loop to Product Intelligence
   └── Mục tiêu: Đưa dữ liệu thực tế quay ngược lại cải thiện điểm số đề xuất sản phẩm.

PHASE 15: Operations Hardening, Feature Flags, Dashboard & Production Readiness
   └── Mục tiêu: Hoàn thiện Dashboard trực quan, kiểm thử toàn diện, đóng gói quy trình deploy an toàn.
```

---

## 14. TEST PLAN CHI TIẾT

Mọi giai đoạn đều phải chạy kiểm thử cục bộ (Local Testing) với các kịch bản thực tế:

1. **Kiểm thử Affiliate Sync**:
   - Chạy đồng bộ thực tế qua `AccessTradeProvider` với API Key thật.
   - Xác nhận sản phẩm được chuyển hóa chính xác vào `NormalizedProductDTO`.
   - Đảm bảo các sản phẩm cũ trên website `table_product` không bị xáo trộn.
2. **Kiểm thử Multi-Platform Scoring**:
   - Đưa dữ liệu 10 sản phẩm mẫu (có đủ/thiếu trường).
   - Kiểm tra điểm `global_score`, `tiktok_score`, `facebook_score`, `youtube_score`.
   - Xác minh các sản phẩm vi phạm blacklist hoặc thiếu URL bị loại trừ kèm lý do rõ ràng.
3. **Kiểm thử Content Package**:
   - Sinh nội dung thử nghiệm cho 1 sản phẩm.
   - Kiểm tra văn bản TikTok, Facebook, YouTube tạo ra khớp 100% với dữ liệu thực tế trong DB, không có thông số bịa đặt.
4. **Kiểm thử Policy Gate**:
   - Thử nghiệm với nội dung chứa từ khóa nhạy cảm (y tế, cam kết khỏi bệnh 100%).
   - Xác nhận hệ thống trả về `FAIL` hoặc `WARNING` và chặn không cho vào Approved Pool.
5. **Kiểm thử Approved Buffer & Scheduler**:
   - Cấu hình hạn ngạch: 3 bài/ngày.
   - Đặt trong Pool: 2 bài đã duyệt (`APPROVED`), 2 bài chờ duyệt (`PENDING_APPROVAL`).
   - Kích hoạt scheduler: Xác nhận scheduler chỉ lấy đúng 2 bài `APPROVED`, tuyệt đối không đăng bài `PENDING`.
6. **Kiểm thử Khóa Chống Trùng (Idempotency)**:
   - Kích hoạt 2 tiến trình publish worker đồng thời cùng 1 giây.
   - Xác nhận chỉ có đúng 1 tiến trình chiếm được lock và xử lý post, không bị đăng trùng lặp.
7. **Kiểm thử Regression Toàn Website**:
   - Kiểm tra giao diện người dùng frontend (Trang chủ, Chi tiết sản phẩm, Click link Affiliate).
   - Kiểm tra hệ thống Admin hiện tại (Menu, Danh mục, Đơn hàng, Settings).

---

## 15. ROLLBACK PLAN

Trong trường hợp phát hiện lỗi trong quá trình chạy thử nghiệm local:

1. **Về Mã Nguồn (Source Code)**:
   - Mọi thay đổi đều được phân nhánh Git rõ ràng.
   - Khi cần hoàn tác: `git checkout -- <file>` hoặc quay về commit ổn định trước đó.
2. **Về Cơ Sở Dữ Liệu (Database)**:
   - File migration `database/migrations/phase12_affiliate_social_automation.sql` được thiết kế hoàn toàn bổ sung (Add-only).
   - Nếu cần rollback cấu trúc bảng mới:
     ```sql
     DROP TABLE IF EXISTS `table_social_post_metric`;
     DROP TABLE IF EXISTS `table_social_schedule_rule`;
     DROP TABLE IF EXISTS `table_master_content`;
     DROP TABLE IF EXISTS `table_content_candidate`;
     DROP TABLE IF EXISTS `table_affiliate_provider`;
     ```
   - Không ảnh hưởng đến dữ liệu `table_product`, `table_product_affiliate` gốc của website.
3. **Về Feature Flags**:
   - Mỗi module mới đều có cờ bật/tắt trong `table_analytics_setting` / `config.php`:
     - `affiliate_multi_source_enabled = 0`
     - `social_automation_enabled = 0`
     - `tiktok_api_enabled = 0`
   - Khi tắt cờ, hệ thống lập tức trở về chế độ vận hành thủ công an toàn (Manual Provider).

---

## 16. PRODUCTION DEPLOYMENT PROTOCOL (QUY TRÌNH KHI ĐƯỢC CHẤP THUẬN)

> **CẢNH BÁO QUAN TRỌNG:**  
> Tuyệt đối không tự ý deploy lên Production. Quy trình dưới đây chỉ được thực hiện bởi Chủ sở hữu hệ thống sau khi đã kiểm tra và duyệt toàn bộ báo cáo Local.

### Quy trình chuẩn bị khi bàn giao:
1. **Bước 1 — Sao lưu CSDL Production**:
   - Thực hiện Full Dump cơ sở dữ liệu `masterpdo` trước khi chạy bất kỳ lệnh SQL nào.
2. **Bước 2 — Chạy SQL Migration**:
   - Chạy file migration `database/migrations/phase12_affiliate_social_automation.sql` trên production database.
   - Kiểm tra log thực thi, đảm bảo không có lỗi `Table already exists` hay `Syntax error`.
3. **Bước 3 — Tải Lên Mã Nguồn**:
   - Tải lên các file mới trong `libraries/class/`, `cron/`, `admin/`.
   - Cập nhật các file core đã được mở rộng.
4. **Bước 4 — Kiểm Tra Môi Trường & Cấu Hình**:
   - Xác nhận đường dẫn FFmpeg, thư mục lưu video `upload/video/` có quyền ghi `chmod 755 / 775`.
   - Kiểm tra API keys trong file `libraries/config.php` production.
5. **Bước 5 — Kiểm Tra Khả Năng Vận Hành (Health Check)**:
   - Đăng nhập Admin → Truy cập `Trung tâm Vận hành` (`index.php?com=operations&act=overview`).
   - Xác nhận tất cả các worker đều báo trạng thái `HEALTHY`.
   - Chạy thử nghiệm 1 bài đăng ở chế độ `Manual Provider` để xác nhận quy trình thông suốt.

---
*Kế hoạch được lập dựa trên việc phân tích trực tiếp toàn bộ 41 class, 12 migration và 5 background workers của mã nguồn KhoePro thực tế.*
