# FITNADO — PHASE 10: OPERATIONS & AUTOMATION CONTROL CENTER PLAN

## 1. MỤC TIÊU & NGUYÊN TẮC THIẾT KẾ

Phase 10 xây dựng **Operations & Automation Control Center** (Trung tâm Vận hành & Giám sát Tự động hóa) tập trung duy nhất cho toàn bộ hệ thống FITNADO (Pipeline Phase 01–09):
```text
Research → Product → Content → Video → Publishing → Tracking → Analytics → Optimization → Experiment
```

### Nguyên tắc bất khả xâm phạm:
1. **MONITOR — CONTROL — RECOVER — AUDIT — COST CONTROL**: Chỉ đóng vai trò Control Plane (quan sát, điều khiển, phục hồi, kiểm toán, kiểm soát chi phí). Toàn bộ nghiệp vụ gốc (Edit/Approve) vẫn diễn ra tại module gốc.
2. **AI RECOMMENDS, HUMAN DECIDES, SYSTEM EXECUTES**: Không tự động sinh thêm AI logic hay vòng lặp tự trị mới.
3. **NO FAKE HEALTH**: Tuyệt đối không trả về `HEALTHY` nếu không có dữ liệu kiểm chứng thực tế.
4. **NO PAID HEALTH CHECKS**: Bảng điều khiển không được gọi các API trả phí (Runway, Luma, TTS, LLM) mỗi khi F5/refresh. Kiểm tra dựa trên nhật ký request gần nhất.
5. **SECRETS NEVER EXPOSED**: Mọi API key, Bearer token, refresh token, client secret đều phải được lọc (mask/sanitize) trước khi ghi log hoặc hiển thị trên giao diện.
6. **IDEMPOTENT RETRY & CANCEL**: Chỉ cho phép Retry các Job thật sự `FAILED`. Tuyệt đối không retry các Post đã `PUBLISHED` hoặc Job trả phí đã thành công. Không hủy job external đã hoàn thành.
7. **EMERGENCY STOP & BUDGET GUARDS**: Cho phép dừng khẩn cấp các tác vụ API trả phí (`pause_paid_automation = 1`) mà không làm mất hàng đợi cục bộ; Áp dụng trần ngân sách ngày/tháng với cơ chế Admin Override có kiểm toán (Audit Trail).
8. **NOT_CONNECTED != FAILED**: Nguồn dữ liệu chưa kết nối (như TikTok API hoặc Affiliate CSV) hiển thị rõ ràng `NOT_CONFIGURED` / `NOT_CONNECTED`, không đánh đồng là lỗi hệ thống `FAILED`.

---

## 2. AUDIT TOÀN BỘ BACKGROUND PROCESSES & INFRASTRUCTURE HIỆN CÓ

| Stage | Process / Worker Name | Worker Key / File | Job Queue Table | Trigger / Schedule | External Providers |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Research (Ph04)** | Product Research Worker | `product_research_worker`<br>`cron/product_research_worker.php` | `table_product_research_job` | Cron / CLI / Secure HTTP token | `ai_agent` (Gemini/OpenAI), `shopee`, `tiktok`, `lazada`, `csv` |
| **Content (Ph05)** | AI Content Worker | `ai_content_worker`<br>`cron/ai_content_worker.php` | `table_ai_content_job` | Cron / CLI / Secure HTTP token | `gemini-1.5-pro`, `gpt-4o`, Mock LLM |
| **Video (Ph06)** | AI Video Render Worker | `video_render_worker`<br>`cron/video_render_worker.php` | `table_ai_video_job` | Cron / CLI / Secure HTTP token | `beeknoee`, `openai/tts-1-hd`, `google_translate`, FFmpeg local |
| **Publishing (Ph07)** | Publish Dispatch Worker | `publish_worker`<br>`cron/publish_worker.php` | `table_publish_post` (SCHEDULED) | Cron / CLI / Secure HTTP token | `manual`, `tiktok_api` (Not configured) |
| **Analytics (Ph08)** | Analytics & Attribution Engine | `analytics_service`<br>`class.AnalyticsService.php` | `table_analytics_event`<br>`table_affiliate_click`<br>`table_affiliate_conversion` | Realtime on-event / CSV import | Local tracking, CSV Import, Affiliate networks |
| **Optimization (Ph09)** | Optimization Engine | `optimization_engine`<br>`class.OptimizationEngine.php` | `table_optimization_recommendation`<br>`table_optimization_experiment` | Event-driven / Evaluation trigger | Internal Rule-based Matrix |

---

## 3. CƠ SỞ DỮ LIỆU & SCHEMA PHASE 10

### 3.1. Bảng `table_system_worker_status` (Theo dõi Heartbeat & Sức khỏe Worker)
Lưu trạng thái định kỳ của các background worker:
- `id` (bigint unsigned auto_increment)
- `worker_key` (varchar 64 unique)
- `worker_name` (varchar 255)
- `worker_type` (varchar 30: `worker`, `cron`, `service`)
- `last_started_at` (int 11)
- `last_heartbeat_at` (int 11)
- `last_completed_at` (int 11)
- `last_success_at` (int 11)
- `last_error_at` (int 11)
- `last_error` (text)
- `hostname` (varchar 100)
- `pid` (int 11)
- `metadata` (mediumtext JSON)
- `date_created`, `date_updated` (int 11)

### 3.2. Bảng `table_system_alert` (Cảnh báo & Sự cố vận hành với Deduplication)
- `id` (bigint unsigned auto_increment)
- `alert_key` (varchar 128 unique) — mã băm chống trùng lặp alert
- `severity` (`INFO`, `WARNING`, `ERROR`, `CRITICAL`)
- `module` (`research`, `content`, `video`, `publishing`, `analytics`, `optimization`, `system`, `budget`, `provider`)
- `title` (varchar 255)
- `message` (text)
- `reference` (varchar 255)
- `status` (`ACTIVE`, `ACKNOWLEDGED`, `RESOLVED`)
- `first_seen_at` (int 11)
- `last_seen_at` (int 11)
- `occurrences` (int 11 default 1)
- `acknowledged_by` (varchar 50)
- `acknowledged_at` (int 11)
- `resolved_at` (int 11)
- `metadata` (mediumtext JSON)
- `date_created`, `date_updated` (int 11)

### 3.3. Bảng `table_operations_override_log` (Kiểm toán Phê duyệt Ngân sách & Khẩn cấp)
- `id` (bigint unsigned auto_increment)
- `admin_user` (varchar 50)
- `action_type` (`BUDGET_OVERRIDE`, `EMERGENCY_PAUSE`, `EMERGENCY_RESUME`, `SETTINGS_CHANGE`, `JOB_RETRY`, `JOB_CANCEL`)
- `reason` (text)
- `amount_context` (decimal 15,2 default 0)
- `ip_address` (varchar 64)
- `metadata` (mediumtext JSON)
- `date_created` (int 11)

### 3.4. Cấu hình Vận hành trong `table_analytics_setting` (Group `operations`)
- `automation_enabled`: `1` (Master Switch Bật/Tắt toàn bộ tự động hóa)
- `pause_paid_automation`: `0` (Dừng khẩn cấp toàn bộ API trả phí)
- `research_automation_enabled`: `1`
- `content_automation_enabled`: `1`
- `video_automation_enabled`: `1`
- `publishing_automation_enabled`: `1`
- `optimization_automation_enabled`: `1`
- `daily_external_api_budget`: `200000` (VND/ngày)
- `monthly_external_api_budget`: `3000000` (VND/tháng)
- `worker_heartbeat_threshold_seconds`: `300` (5 phút)
- `stuck_job_threshold_seconds`: `1800` (30 phút, video 3600s)
- `tracking_stale_threshold_hours`: `24`
- `affiliate_click_stale_threshold_hours`: `48`
- `conversion_stale_threshold_days`: `7`
- `analytics_stale_threshold_hours`: `24`
- `optimization_stale_threshold_hours`: `24`

---

## 4. THIẾT KẾ CÁC DỊCH VỤ & MODULE LOGIC

### 4.1. `OperationsService.php` (`libraries/class/class.OperationsService.php`)
Chịu trách nhiệm tập trung toàn bộ truy vấn giám sát & hành động điều khiển:
1. **Process Registry & Heartbeats**:
   - Quản lý danh sách các worker chính thức.
   - Hàm `recordHeartbeat($workerKey, $type, $metadata)`: Ghi nhận nhịp tim từ cron/CLI.
   - Hàm `getWorkerStatuses()`: Đánh giá trạng thái (`HEALTHY`, `WARNING`, `DEGRADED`, `FAILED`, `DISABLED`, `UNKNOWN`).
2. **Queue Health & Stuck Job Detection**:
   - Hàm `getQueueMetrics($module)`: Đếm số lượng Pending, Processing, Completed, Failed, Oldest Pending, Avg Duration.
   - Hàm `detectStuckJobs()`: Phát hiện các Job ở trạng thái `RUNNING`/`PROCESSING` vượt quá ngưỡng cấu hình và đánh dấu cảnh báo `STUCK`.
3. **Provider Health & Zero-Cost Diagnostic**:
   - Trích xuất trạng thái cấu hình của: AI Research (`ai_agent`), LLM (`gemini-1.5-pro`/`gpt-4o`), TTS (`beeknoee`/`google`), Video Provider (`beeknoee`/`ffmpeg`), Social Publisher (`tiktok_api`/`manual`), Affiliate Importer (`csv`).
   - Phân biệt chính xác: `CONFIGURED`, `NOT_CONFIGURED`, `AVAILABLE`, `UNAVAILABLE`, `RATE_LIMITED`, `AUTH_ERROR`.
   - Tuyệt đối không gọi paid API khi render dashboard.
4. **Cost Center & Dimension Aggregation**:
   - Hàm `getCostSummary($timeRange)`: Tổng hợp chi phí API thực tế (`ACTUAL_COST`) phân tách rạch ròi với chi phí ước tính (`ESTIMATED_COST`).
   - Phân loại chi phí theo: TTS, Video AI, LLM, Research, Tổng chi phí.
   - Đối soát ngân sách Ngày / Tháng và xác định cấp độ: `NORMAL`, `WARNING`, `LIMIT_REACHED`.
5. **Human Action Queue ("CẦN BẠN XỬ LÝ")**:
   - Tổng hợp 8 nhóm hành động cần con người can thiệp:
     - Ứng viên Research chờ duyệt (`table_product_research`).
     - Nội dung Content chờ duyệt (`table_ai_content`).
     - Video thành phẩm chờ duyệt (`table_ai_video`).
     - Bài đăng Video chờ xuất bản thủ công (`table_publish_post`).
     - Khuyến nghị Tối ưu hóa chờ phê duyệt (`table_optimization_recommendation`).
     - Thử nghiệm A/B đang chạy hoặc đã đủ mẫu chờ nghiệm thu (`table_optimization_experiment`).
     - Đơn hàng Affiliate chưa rõ nguồn tracking (`table_affiliate_conversion` - `UNATTRIBUTED`).
     - Tác vụ lỗi / kẹt cần can thiệp xử lý (`FAILED` / `STUCK`).
   - Sắp xếp độ ưu tiên: `CRITICAL/ERROR` > `COST_BLOCKED` > `WAITING_APPROVAL` > `NORMAL`.
6. **Data Freshness Tracker**:
   - Theo dõi mốc thời gian sự kiện cuối cùng của Tracking, Affiliate Click, Conversion Import, Winner Evaluation, Optimization Recommendation.
   - Báo `STALE` khi vượt ngưỡng cho các nguồn ĐÃ KẾT NỐI. Báo `NOT_CONNECTED` cho các nguồn chưa kết nối.
7. **Environment & Diagnostics**:
   - Báo cáo trung thực: PHP Runtime hiện tại (CLI/Web), MySQL Connection Ping, FFmpeg & FFprobe Executable Paths, Timezone, Disk Space.
8. **Log Sanitizer & Alert Deduplication**:
   - Hàm `sanitizeSecrets($textOrArray)`: Che giấu toàn bộ Token/Key (`Bearer ***`, `sk-***`).
   - Hàm `recordAlert($module, $severity, $title, $message, $reference)`: Tự động gom lỗi trùng lặp và tăng biến đếm `occurrences`.
   - Hàm `acknowledgeAlert($alertId, $adminUser)`, `resolveAlert($alertId)`.
9. **Safe Control & Retry Plane**:
   - Hàm `retryJob($module, $jobId, $adminUser)`: Tái thực thi an toàn thông qua hàng đợi gốc tương ứng.
   - Hàm `cancelJob($module, $jobId, $adminUser)`: Hủy an toàn khi job chưa thực thi.
   - Hàm `toggleAutomation($module, $enabled, $adminUser)`.
   - Hàm `toggleEmergencyPausePaid($pause, $reason, $adminUser)`.
   - Hàm `overrideBudget($amount, $reason, $adminUser)`.

---

## 5. GIAO DIỆN OPERATIONS CONTROL CENTER (ADMIN UI)

Menu FITNADO:
```text
FITNADO
└── Operations Center (Trung tâm Vận hành)
    ├── Overview (Tổng quan hệ thống)
    ├── Pipeline (Quy trình & Hàng đợi hành động)
    ├── Jobs (Quản lý tác vụ & Hàng đợi)
    ├── Providers (Nhà cung cấp & Kết nối API)
    ├── Costs (Trung tâm Chi phí & Ngân sách)
    ├── Alerts (Cảnh báo & Sự cố)
    ├── Logs (Nhật ký hệ thống & Bảo mật)
    └── Settings (Cấu hình Vận hành & Ngưỡng)
```

Giao diện tích hợp đầy đủ AdminLTE, huy hiệu trạng thái sống động, bảo vệ CSRF, Modal xác nhận cho các thao tác rủi ro (Retry, Budget Override, Emergency Stop).

---

## 6. KẾ HOẠCH KIỂM THỬ TỰ ĐỘNG & REGRESSION (PHP 7.4)

1. `test_phase10.php`: Bộ kiểm thử toàn diện 32+ kịch bản logic:
   - Worker Heartbeat recording & Health status calculation
   - Queue health aggregation & Oldest pending calculation
   - Stuck job detection & safe status marking
   - Provider health detection without paid API calls
   - Secret sanitization (Bearer, API keys, Passwords masked)
   - Real cost center aggregation & Estimated vs Actual separation
   - Daily & Monthly Budget Guards & Admin Override audit logging
   - Global & Module automation toggles
   - Emergency Stop (Pause Paid Automation) behavior & Free job continuity
   - Human Action Queue aggregation & priority ordering
   - Data freshness checks (Stale vs Not Connected separation)
   - Alert deduplication & lifecycle (Active -> Acknowledged -> Resolved)
   - Safe Retry & Idempotency guards (Blocking published post & paid job duplication)
   - Environment diagnostics (PHP, MySQL, FFmpeg, FFprobe)
2. `test_http_operations.php`: Kiểm thử toàn bộ 8 endpoint Admin và render templates.
3. Regression Test toàn diện Phases 01 đến 09 (317 test cases).
