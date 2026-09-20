<?php
$linkRules = "index.php?com=analytics&act=winner_rules";
$linkWinner = "index.php?com=analytics&act=winner_detection";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-sliders-h mr-2 text-primary"></i>Cấu Hình Quy Tắc Analytics & Winner Detection
                </h1>
                <small class="text-muted">Quản trị các ngưỡng kích thước mẫu tối thiểu, ngưỡng CTR/CVR để phân loại sản phẩm thắng và thời gian lưu vết phân bổ</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkWinner?>" class="btn btn-sm btn-outline-warning shadow-sm font-weight-bold text-dark">
                    <i class="fas fa-trophy mr-1"></i> Winner Detection Board
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm">
    <div class="container-fluid">
        <form method="POST" action="index.php?com=analytics&act=save_rules">
            <div class="row">
                <!-- Group 1: Attribution & Tracking -->
                <div class="col-md-6 col-12 mb-3">
                    <div class="card card-outline card-primary shadow-sm h-100">
                        <div class="card-header bg-white py-2">
                            <h3 class="card-title font-weight-bold text-dark">
                                <i class="fas fa-fingerprint mr-2 text-primary"></i>1. Cấu Hình Phân Bổ & Lưu Vết (Attribution)
                            </h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold mb-1">Thời gian lưu vết phân bổ (Attribution Window):</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="attribution_window_days" class="form-control" value="<?=$currentSettings['attribution_window_days']?>" min="1" max="365" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">ngày</span>
                                    </div>
                                </div>
                                <small class="text-muted">Khoảng thời gian tối đa từ khi người dùng click link TikTok đến khi phát sinh đơn hàng đối soát (Mặc định: 30 ngày).</small>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold mb-1">Lọc địa chỉ IP nội bộ / Admin (JSON Array):</label>
                                <textarea name="internal_ips" rows="3" class="form-control form-control-sm text-monospace" required><?=htmlspecialchars($currentSettings['internal_ips'])?></textarea>
                                <small class="text-muted">Danh sách các IP được đánh dấu <code>is_internal = 1</code> để loại trừ khỏi báo cáo người dùng thật.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Group 2: Sample Size Gates -->
                <div class="col-md-6 col-12 mb-3">
                    <div class="card card-outline card-warning shadow-sm h-100">
                        <div class="card-header bg-white py-2">
                            <h3 class="card-title font-weight-bold text-dark">
                                <i class="fas fa-shield-alt mr-2 text-warning"></i>2. Cổng Kích Thước Mẫu Tối Thiểu (Sample Size Gate)
                            </h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-6 form-group mb-3">
                                    <label class="font-weight-bold mb-1">Lượt truy cập tối thiểu:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="min_landing_sessions" class="form-control" value="<?=$currentSettings['min_landing_sessions']?>" min="1" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">sessions</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 form-group mb-3">
                                    <label class="font-weight-bold mb-1">Click affiliate tối thiểu:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="min_affiliate_clicks" class="form-control" value="<?=$currentSettings['min_affiliate_clicks']?>" min="1" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">clicks</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 form-group mb-3">
                                    <label class="font-weight-bold mb-1">Số đơn hàng tối thiểu:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="min_conversions" class="form-control" value="<?=$currentSettings['min_conversions']?>" min="1" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">đơn</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 form-group mb-3">
                                    <label class="font-weight-bold mb-1">Thời gian test tối thiểu:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="min_test_age_days" class="form-control" value="<?=$currentSettings['min_test_age_days']?>" min="1" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">ngày</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small class="text-danger font-weight-bold">
                                <i class="fas fa-exclamation-circle mr-1"></i>Nếu sản phẩm chưa đạt các ngưỡng trên, hệ thống BẮT BUỘC giữ trạng thái <code>INSUFFICIENT_DATA</code>.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Group 3: Winner & Underperforming Thresholds -->
                <div class="col-12 mb-3">
                    <div class="card card-outline card-success shadow-sm">
                        <div class="card-header bg-white py-2">
                            <h3 class="card-title font-weight-bold text-dark">
                                <i class="fas fa-trophy mr-2 text-success"></i>3. Ngưỡng Tỷ Lệ Đánh Giá Winner & Underperforming
                            </h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-3 col-6 form-group mb-3">
                                    <label class="font-weight-bold mb-1">Ngưỡng CTR Tiềm năng (Promising):</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" name="promising_ctr_pct" class="form-control" value="<?=$currentSettings['promising_ctr_pct']?>" min="0.1" max="100" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">CTR &ge; ngưỡng này sẽ gắn nhãn <strong>PROMISING</strong>.</small>
                                </div>

                                <div class="col-md-3 col-6 form-group mb-3">
                                    <label class="font-weight-bold mb-1">Ngưỡng CTR Sản phẩm Thắng (Winner):</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" name="winner_ctr_pct" class="form-control" value="<?=$currentSettings['winner_ctr_pct']?>" min="0.1" max="100" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">CTR &ge; ngưỡng này sẽ được công nhận <strong>WINNER</strong>.</small>
                                </div>

                                <div class="col-md-3 col-6 form-group mb-3">
                                    <label class="font-weight-bold mb-1">Ngưỡng CVR Sản phẩm Thắng:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" name="winner_cvr_pct" class="form-control" value="<?=$currentSettings['winner_cvr_pct']?>" min="0.1" max="100" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">Tỷ lệ CVR từ click sang đơn hàng đối soát.</small>
                                </div>

                                <div class="col-md-3 col-6 form-group mb-3">
                                    <label class="font-weight-bold mb-1">Ngưỡng Kém Hiệu Quả (Underperforming):</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" name="underperforming_ctr_pct" class="form-control" value="<?=$currentSettings['underperforming_ctr_pct']?>" min="0.0" max="50" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">Đủ mẫu nhưng CTR &lt; ngưỡng này -> <strong>UNDERPERFORMING</strong>.</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-right py-2">
                            <button type="submit" class="btn btn-sm btn-primary font-weight-bold px-4 shadow-sm">
                                <i class="fas fa-save mr-1"></i> Lưu Cấu Hình Quy Tắc
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
