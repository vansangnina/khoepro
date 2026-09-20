# FITNADO PHASE 09 — DATA-DRIVEN OPTIMIZATION LOOP
## Technical Implementation Plan

---

## 1. MỤC TIÊU & TẦM NHÌN (OBJECTIVES & SCOPE)

Phase 08 đã xây dựng thành công vòng đo lường khép kín:
```text
RESEARCH → PRODUCT → CONTENT → VIDEO → PUBLISH → TRACK → AFFILIATE CLICK → CONVERSION → ATTRIBUTION → PERFORMANCE SIGNAL
```

Phase 09 xây dựng **Vòng lặp Tối ưu hóa Dựa trên Dữ liệu Thực tế (Data-Driven Optimization Loop)**:
```text
PERFORMANCE SIGNAL
       ↓
OPTIMIZATION ENGINE (Rule-Based Core)
       ↓
RECOMMENDATION (Deduplicated, Cooldown, Stale & Quality Gated)
       ↓
HUMAN APPROVAL & COST GATE (Admin Decides, Cost Limit Block)
       ↓
EXPERIMENT INITIALIZATION (One-Variable Principle, Baseline vs Variation)
       ↓
VARIATION GENERATION (Content Reuses Phase 05, Video Reuses Phase 06)
       ↓
PUBLISHING INTEGRATION (Post Package with Distinct Tracking Code)
       ↓
MEASURE & COMPARE (Baseline vs Variation Analytics)
       ↓
EXPERIMENT CONCLUSION (Sample Gated: VARIATION_BETTER / BASELINE_BETTER / NO_DIFFERENCE)
```

### Nguyên tắc Bất biến:
1. **AI Recommends, Human Decides, System Executes**: Không bao giờ tự động chi tiền hoặc tự động xuất bản mà không có sự phê duyệt của Admin con người.
2. **Không Vòng Lặp Vô Hạn (No Infinite Loop)**: Kết thúc một thử nghiệm không tự ý sinh thử nghiệm tiếp theo.
3. **One Variable Principle**: Mỗi thử nghiệm ưu tiên thay đổi duy nhất một biến số (`HOOK`, `CTA`, `SCRIPT`, `VIDEO_STYLE`, `VOICE`, `OFFER`). Nếu thay đổi nhiều biến, phải gắn nhãn `MULTIVARIATE`.
4. **Economy First**: Thử nghiệm mặc định sử dụng video `ECONOMY` (0 VND API cost). `HYBRID` hoặc `PREMIUM` bắt buộc có sự phê duyệt riêng.
5. **Phase 08 Hotfix**: Sửa dứt điểm việc coi click/CTR cao là `WINNER` khi chưa có dữ liệu đơn hàng (`conversion_source_connected = false`).

---

## 2. PHASE 08 HOTFIX: PERFORMANCE MATURITY CLASSIFICATION

### 2.1. Phân định Cấp bậc Trưởng thành Hiệu suất (Performance Maturity Levels)
* **`INSUFFICIENT_DATA`**: Chưa đạt kích thước mẫu tối thiểu (`sessions < min_landing_sessions` HOẶC `clicks < min_affiliate_clicks`).
* **`TRAFFIC_PROMISING`**: Đạt số sessions (`>= min_landing_sessions`) nhưng số click thấp (`< min_affiliate_clicks`). Cần xem lại trang sản phẩm / nút CTA / ưu đãi.
* **`CLICK_PROMISING`**: Đạt số sessions & clicks (`CTR >= promising_ctr_pct`), nhưng **CHƯA KẾT NỐI** dữ liệu đối soát đơn hàng hoặc chưa phát sinh đơn hàng.
* **`CONVERSION_PROMISING`**: Đã kết nối dữ liệu đơn hàng và có ít nhất `min_conversions` (>= 2 đơn), nhưng chưa đạt lợi nhuận ròng vượt trội.
* **`REVENUE_WINNER`**: Đã kết nối dữ liệu đơn hàng + có ít nhất `min_conversions` + **Hoa hồng thực tế > Chi phí sản xuất video** (`Net ROI > 0`).
* **`UNDERPERFORMING`**: Đạt kích thước mẫu tối thiểu và thời gian thử nghiệm (`test_age_days >= min_test_age_days`) nhưng `CTR < underperforming_ctr_pct`.

### 2.2. Rào chắn Dữ liệu Đơn hàng (Data Source Gate)
* `WinnerDetectionEngine` và `OptimizationEngine` kiểm tra cờ `conversion_source_connected`.
* Nếu `conversion_source_connected = false`:
  - Tuyệt đối **KHÔNG** đánh giá `REVENUE_WINNER` hay `CONVERSION_PROMISING`.
  - Mức cao nhất chỉ là `CLICK_PROMISING`.
* Lịch sử snapshot cũ được bảo lưu tính toàn vẹn (không ghi đè lịch sử), đánh dấu `is_legacy = 1` nếu cần.

---

## 3. THIẾT KẾ CƠ SỞ DỮ LIỆU PHASE 09

### 3.1. Bảng `table_optimization_recommendation`
* `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `id_product`: INT(10) UNSIGNED NOT NULL
* `id_post`: INT(10) UNSIGNED NULL
* `id_video`: INT(10) UNSIGNED NULL
* `id_content`: INT(10) UNSIGNED NULL
* `recommendation_type`: VARCHAR(50) NOT NULL (`KEEP_TESTING`, `REVIEW_PRODUCT_PAGE`, `CREATE_NEW_HOOK`, `CREATE_CONTENT_VARIATION`, `CREATE_VIDEO_VARIATION`, `UPGRADE_TO_HYBRID`, `RETEST_PRODUCT`, `PAUSE_TESTING`, `WAIT_FOR_MORE_DATA`)
* `reason_code`: VARCHAR(50) NOT NULL (`INSUFFICIENT_SAMPLE`, `HIGH_TRAFFIC_LOW_CLICK`, `HIGH_CLICK_NO_CONVERSION`, `STRONG_CONVERSION_SIGNAL`, `POSITIVE_ROI`, `LOW_CTR_POST`, `TRACKING_DEGRADED`)
* `reason_summary`: TEXT NOT NULL (Diễn giải tiếng Việt)
* `hypothesis`: TEXT NOT NULL (Giả thuyết kiểm thử)
* `proposed_variable`: VARCHAR(50) DEFAULT 'HOOK' (`HOOK`, `CTA`, `SCRIPT`, `VIDEO_STYLE`, `VOICE`, `OFFER`, `MULTIVARIATE`)
* `target_mode`: VARCHAR(20) DEFAULT 'ECONOMY' (`ECONOMY`, `HYBRID`, `PREMIUM`)
* `estimated_cost_vnd`: DECIMAL(15,2) DEFAULT 0.00
* `metrics_snapshot`: MEDIUMTEXT NOT NULL (JSON)
* `rules_snapshot`: MEDIUMTEXT NOT NULL (JSON)
* `status`: VARCHAR(30) DEFAULT 'PENDING' (`PENDING`, `APPROVED`, `REJECTED`, `STALE`, `EXECUTING`, `COMPLETED`, `CANCELLED`)
* `review_notes`: TEXT NULL
* `approved_by`: VARCHAR(50) NULL
* `approved_at`: INT(11) NULL
* `id_experiment`: BIGINT(20) UNSIGNED NULL
* `date_created`, `date_updated`: INT(11) NOT NULL

### 3.2. Bảng `table_optimization_experiment`
* `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
* `experiment_code`: VARCHAR(64) NOT NULL UNIQUE (Ví dụ: `exp_prod66_hook_82f1`)
* `id_recommendation`: BIGINT(20) UNSIGNED NULL
* `id_product`: INT(10) UNSIGNED NOT NULL
* `changed_variable`: VARCHAR(50) NOT NULL (`HOOK`, `CTA`, `SCRIPT`, `VIDEO_STYLE`, `VOICE`, `OFFER`, `MULTIVARIATE`)
* `hypothesis`: TEXT NOT NULL
* `baseline_type`: VARCHAR(30) DEFAULT 'POST'
* `id_baseline_post`: INT(10) UNSIGNED NULL
* `id_baseline_video`: INT(10) UNSIGNED NULL
* `id_baseline_content`: INT(10) UNSIGNED NULL
* `id_variation_content`: INT(10) UNSIGNED NULL
* `id_variation_video`: INT(10) UNSIGNED NULL
* `id_variation_post`: INT(10) UNSIGNED NULL
* `target_mode`: VARCHAR(20) DEFAULT 'ECONOMY'
* `status`: VARCHAR(30) DEFAULT 'APPROVED' (`DRAFT`, `APPROVED`, `RUNNING`, `ENOUGH_DATA`, `COMPLETED`, `CANCELLED`)
* `baseline_metrics_snapshot`: MEDIUMTEXT NOT NULL (JSON)
* `variation_metrics_snapshot`: MEDIUMTEXT NULL (JSON)
* `result_conclusion`: VARCHAR(50) NULL (`INSUFFICIENT_DATA`, `BASELINE_BETTER`, `VARIATION_BETTER`, `NO_MEANINGFUL_DIFFERENCE`)
* `result_summary`: TEXT NULL
* `total_cost_vnd`: DECIMAL(15,2) DEFAULT 0.00
* `started_at`: INT(11) NULL
* `completed_at`: INT(11) NULL
* `created_by`: VARCHAR(50) DEFAULT 'admin'
* `date_created`, `date_updated`: INT(11) NOT NULL

### 3.3. Cài đặt Cấu hình Mới trong `table_analytics_setting`
* `max_cost_per_experiment`: 60,000 VND (Hard cost gate)
* `max_active_experiments_per_product`: 2 (Giới hạn thử nghiệm đồng thời)
* `recommendation_cooldown_hours`: 24 (Thời gian giãn cách tạo recommendation trùng)
* `experiment_min_sessions`: 30 (Ngưỡng mẫu tối thiểu để kết luận thử nghiệm)
* `experiment_min_clicks`: 10 (Ngưỡng click tối thiểu để kết luận thử nghiệm)

---

## 4. THIẾT KẾ DỊCH VỤ `OptimizationEngine`

Tạo file: `libraries/class/class.OptimizationEngine.php`

### 4.1. Các Phương thức Chính
1. `generateRecommendations($productId = null)`:
   - Quét các sản phẩm và bài đăng có traffic/hiệu suất.
   - Kiểm tra rào chắn chất lượng dữ liệu (Data Quality Gate) và trạng thái đơn hàng.
   - Áp dụng các quy tắc logic (Rule Engine) để sinh loại khuyến nghị tương ứng.
   - Kiểm tra Deduplication & Cooldown: Không tạo recommendation trùng lặp nếu chưa quá 24h hoặc metrics chưa đổi.
   - Tính toán chi phí ước tính trung thực (`Estimated TTS Cost`, `Estimated AI Video Cost`, `Total Cost`).
   - Lưu vào `table_optimization_recommendation`.
2. `approveRecommendation($recommendationId, $adminUser, $notes = '', $adminOverride = false)`:
   - Kiểm tra Hard Cost Gate: Nếu chi phí ước tính > `max_cost_per_experiment` và không có `$adminOverride`, CHẶN lại.
   - Chuyển trạng thái recommendation sang `APPROVED`.
   - Tự động gọi `createExperimentFromRecommendation`.
3. `rejectRecommendation($recommendationId, $adminUser, $reason)`:
   - Chuyển trạng thái sang `REJECTED`, lưu lý do từ chối.
4. `createExperimentFromRecommendation($recommendationId, $adminUser)`:
   - Tạo bản ghi `table_optimization_experiment`.
   - Tái sử dụng `AIContentEngine` sinh nội dung biến thể (ví dụ: Hook mới, CTA mới).
   - Tái sử dụng `VideoComposer` tạo video biến thể (Economy hoặc Hybrid).
   - Gắn kết với Publishing Center: Tạo Post Package biến thể với `tracking_code` riêng (ví dụ: `fp_tikt_exp_...`).
5. `evaluateExperiment($experimentId)`:
   - Đo lường và so sánh hiệu suất Baseline Post vs Variation Post qua `AnalyticsService`.
   - Kiểm tra Sample Gate (`sessions >= 30` và `clicks >= 10`).
   - Kết luận kết quả: `INSUFFICIENT_DATA`, `VARIATION_BETTER`, `BASELINE_BETTER`, hoặc `NO_MEANINGFUL_DIFFERENCE`.
   - Đóng băng snapshot và chuyển sang `COMPLETED`.

---

## 5. TÍCH HỢP QUẢN TRỊ ADMINLTE

1. **Controller**: `admin/sources/optimization.php`
   - `recommendations`: Danh sách khuyến nghị PENDING / APPROVED / REJECTED.
   - `recommendation_detail`: Xem chi tiết số liệu quan sát, giả thuyết, dự toán chi phí, nút Phê duyệt / Từ chối.
   - `approve_recommendation`: Xử lý phê duyệt + kiểm tra Cost Gate.
   - `reject_recommendation`: Xử lý từ chối.
   - `experiments`: Danh sách thử nghiệm đang chạy / đã hoàn tất.
   - `experiment_detail`: Chi tiết so sánh Baseline vs Variation (Content, Video, Post, Clicks, CTR, CVR).
   - `evaluate_experiment`: Kích hoạt đánh giá kết quả thử nghiệm.
   - `rules`: Cấu hình quy tắc, ngưỡng chi phí và cooldown.
   - `save_rules`: Lưu cấu hình.
2. **Templates**: `admin/templates/optimization/`
   - `recommendations_tpl.php`
   - `recommendation_detail_tpl.php`
   - `experiments_tpl.php`
   - `experiment_detail_tpl.php`
   - `rules_tpl.php`
3. **Menu**: `admin/templates/layout/menu.php`
   - Thêm nhóm "Tối ưu hóa (Optimization)" gồm Khuyến nghị, Thử nghiệm A/B, và Quy tắc.

---

## 6. KẾ HOẠCH KIỂM THỬ TỰ ĐỘNG (`test_phase09.php`)

Xây dựng bộ test toàn diện 30+ assertions bao phủ:
1. **Phase 08 Hotfix Verification**: Mẫu cao nhưng không có conversion data thì chỉ là `CLICK_PROMISING`, không phải `WINNER` hay `REVENUE_WINNER`.
2. **Performance Maturity Scale**: Xác thực đủ 6 mức maturity (`INSUFFICIENT_DATA`, `TRAFFIC_PROMISING`, `CLICK_PROMISING`, `CONVERSION_PROMISING`, `REVENUE_WINNER`, `UNDERPERFORMING`).
3. **Data Source Gate**: Xác thực không thể gán `REVENUE_WINNER` khi `conversion_source_connected = false`.
4. **Recommendation Generation & Deduplication**: Khuyến nghị sinh đúng quy tắc, không bị trùng lặp trong thời gian cooldown.
5. **Stale Recommendation Invalidation**: Tự động nhận diện recommendation cũ khi metrics biến động mạnh.
6. **Cost Gate Enforcement**: Chặn phê duyệt khi chi phí vượt hạn mức `max_cost_per_experiment` trừ khi Admin override.
7. **Human Gate & No Autonomous Execution**: Khuyến nghị PENDING tuyệt đối không tự sinh video hay tự đăng bài.
8. **One-Variable Experiment Creation**: Tạo thử nghiệm thay đổi 1 biến số (`HOOK` / `CTA` / `VIDEO_MODE`) rõ ràng.
9. **Content & Video Variation Integrity**: Tái sử dụng Phase 05 & Phase 06, giữ nguyên Fact Gate và Media QC.
10. **Publishing & Tracking Isolation**: Bài đăng variation có `tracking_code` độc nhất, không làm lẫn lộn số liệu click với baseline.
11. **Experiment Evaluation & Sample Size Gate**: Không tuyên bố kết quả thắng nếu chưa đạt đủ mẫu thử nghiệm.
12. **No Infinite Loop**: Thử nghiệm hoàn tất không tự ý tạo thử nghiệm mới.
13. **Regression Tests**: Toàn bộ Phase 01–08 vượt qua 100% không suy thoái.
