<?php
$linkRecommendations = "index.php?com=optimization&act=recommendations";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-lightbulb mr-2 text-warning"></i>Chi tiết Khuyến nghị Tối ưu hóa #<?=$recommendation['id']?>
                </h1>
                <small class="text-muted">Kiểm duyệt đề xuất, đối soát dự toán chi phí & phê duyệt khởi tạo Thử nghiệm A/B</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkRecommendations?>" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm pb-4">
    <div class="container-fluid">
        <div class="row">
            <!-- Left Column: Recommendation & Action Plan -->
            <div class="col-lg-7 col-12 mb-3">
                <!-- Main Recommendation Card -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white font-weight-bold py-3">
                        <i class="fas fa-bullseye text-primary mr-1"></i> Đề xuất & Giả thuyết Kiểm thử
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge badge-primary px-3 py-2 mr-2" style="font-size: 0.95rem;">
                                <?=htmlspecialchars($recommendation['recommendation_type'])?>
                            </span>
                            <span class="badge badge-light border text-dark px-2 py-1">
                                Biến số: <strong><?=htmlspecialchars($recommendation['proposed_variable'])?></strong>
                            </span>
                            <span class="badge badge-light border text-muted px-2 py-1 ml-2">
                                Chế độ: <strong><?=htmlspecialchars($recommendation['target_mode'])?></strong>
                            </span>
                            <span class="badge badge-<?=($recommendation['status'] === 'PENDING') ? 'warning text-dark font-weight-bold' : 'info'?> ml-auto px-2 py-1">
                                <?=$recommendation['status']?>
                            </span>
                        </div>

                        <div class="alert alert-light border shadow-none mb-3">
                            <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-info-circle text-info mr-1"></i> Lý do đề xuất (Reason Summary):</h6>
                            <p class="mb-0 text-dark"><?=nl2br(htmlspecialchars($recommendation['reason_summary']))?></p>
                        </div>

                        <div class="alert alert-light border shadow-none mb-3">
                            <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-vial text-warning mr-1"></i> Giả thuyết Thử nghiệm (Hypothesis):</h6>
                            <p class="mb-0 text-dark"><?=nl2br(htmlspecialchars($recommendation['hypothesis']))?></p>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <small class="text-muted d-block">Mã nguyên nhân (Reason Code):</small>
                                <code><?=htmlspecialchars($recommendation['reason_code'])?></code>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <small class="text-muted d-block">Ngày tạo:</small>
                                <span><?=date('d/m/Y H:i:s', $recommendation['date_created'])?></span>
                            </div>
                        </div>

                        <?php if (!empty($recommendation['approved_by'])): ?>
                        <div class="p-2 bg-light rounded mt-2 border">
                            <small class="text-muted d-block">Phê duyệt bởi: <strong><?=htmlspecialchars($recommendation['approved_by'])?></strong> lúc <?=date('d/m/Y H:i', (int)$recommendation['approved_at'])?></small>
                            <?php if (!empty($recommendation['review_notes'])): ?>
                            <small class="text-dark d-block mt-1">Ghi chú: <em><?=htmlspecialchars($recommendation['review_notes'])?></em></small>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cost Gate & Budget Breakdown -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white font-weight-bold py-3">
                        <i class="fas fa-coins text-warning mr-1"></i> Dự toán Chi phí Thử nghiệm (Cost Gate)
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-2">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th>Hạng mục chi phí</th>
                                        <th>Loại biến thể</th>
                                        <th class="text-right">Chi phí ước tính</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Voiceover TTS (Vietnamese Spoken Voice)</td>
                                        <td>Tái sử dụng Voice Cache / TTS Cục bộ</td>
                                        <td class="text-right font-weight-bold text-success">0 đ</td>
                                    </tr>
                                    <tr>
                                        <td>Kết xuất Video Composer</td>
                                        <td><?=htmlspecialchars($recommendation['target_mode'])?> (<?=($recommendation['target_mode'] === 'HYBRID') ? '1 cảnh AI Beeknoee ~50k' : '100% Cục bộ Local MP4'?>)</td>
                                        <td class="text-right font-weight-bold text-<?=((float)$recommendation['estimated_cost_vnd'] > 0) ? 'danger' : 'success'?>">
                                            <?=number_format((float)$recommendation['estimated_cost_vnd'], 0, ',', '.')?> đ
                                        </td>
                                    </tr>
                                    <tr class="bg-light font-weight-bold">
                                        <td colspan="2">TỔNG CHI PHÍ ƯỚC TÍNH (ESTIMATED TOTAL COST)</td>
                                        <td class="text-right text-<?=((float)$recommendation['estimated_cost_vnd'] > 0) ? 'danger' : 'success'?>">
                                            <?=number_format((float)$recommendation['estimated_cost_vnd'], 0, ',', '.')?> đ
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted"><i class="fas fa-shield-alt mr-1 text-primary"></i> Hạn mức chi phí tối đa mặc định cho 1 thử nghiệm là <strong>60.000 đ</strong> (Hard Cost Limit).</small>
                    </div>
                </div>
            </div>

            <!-- Right Column: Product Context & Approval Form -->
            <div class="col-lg-5 col-12">
                <!-- Product Context Card -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white font-weight-bold py-3">
                        <i class="fas fa-box text-info mr-1"></i> Sản phẩm & Số liệu Quan sát
                    </div>
                    <div class="card-body">
                        <h5 class="font-weight-bold mb-1"><?=htmlspecialchars($product['namevi'] ?? ('Sản phẩm #' . $recommendation['id_product']))?></h5>
                        <p class="text-muted small mb-3">Mã SKU: <?=htmlspecialchars($product['code'] ?? 'N/A')?></p>

                        <!-- Metrics Snapshot Table -->
                        <div class="bg-light p-3 rounded border mb-3">
                            <h6 class="font-weight-bold text-muted small text-uppercase mb-2">Số liệu ghi nhận (Observed Metrics):</h6>
                            <div class="row text-center">
                                <div class="col-4 mb-2">
                                    <small class="text-muted d-block">Sessions</small>
                                    <strong class="text-primary font-weight-bold" style="font-size: 1.1rem;"><?=$metricsSnapshot['sessions'] ?? 0?></strong>
                                </div>
                                <div class="col-4 mb-2">
                                    <small class="text-muted d-block">Affiliate Clicks</small>
                                    <strong class="text-info font-weight-bold" style="font-size: 1.1rem;"><?=$metricsSnapshot['clicks'] ?? 0?></strong>
                                </div>
                                <div class="col-4 mb-2">
                                    <small class="text-muted d-block">CTR</small>
                                    <strong class="text-success font-weight-bold" style="font-size: 1.1rem;"><?=$metricsSnapshot['ctr_pct'] ?? 0?>%</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Đơn hàng</small>
                                    <strong class="text-dark font-weight-bold"><?=$metricsSnapshot['conversions'] ?? 0?></strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Hoa hồng</small>
                                    <strong class="text-dark font-weight-bold"><?=number_format($metricsSnapshot['commission_vnd'] ?? 0, 0, ',', '.')?> đ</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">ROI Ròng</small>
                                    <strong class="text-<?=((float)($metricsSnapshot['roi_vnd'] ?? 0) >= 0) ? 'success' : 'danger'?> font-weight-bold"><?=number_format($metricsSnapshot['roi_vnd'] ?? 0, 0, ',', '.')?> đ</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Data Source Status -->
                        <div class="mb-2">
                            <small class="text-muted">Trạng thái dữ liệu đơn hàng:</small>
                            <?php if (!empty($metricsSnapshot['conversion_connected'])): ?>
                                <span class="badge badge-success ml-1"><i class="fas fa-check-circle mr-1"></i> Đã kết nối đối soát CSV</span>
                            <?php else: ?>
                                <span class="badge badge-warning text-dark ml-1"><i class="fas fa-exclamation-triangle mr-1"></i> Chưa có dữ liệu đơn hàng sàn</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Approval / Action Card -->
                <?php if ($recommendation['status'] === 'PENDING' || $recommendation['status'] === 'STALE'): ?>
                <div class="card shadow-sm border-0 border-top-primary">
                    <div class="card-header bg-white font-weight-bold py-3">
                        <i class="fas fa-user-check text-primary mr-1"></i> Quyết định Phê duyệt (Human Gate)
                    </div>
                    <div class="card-body">
                        <!-- Approval Form -->
                        <form action="index.php?com=optimization&act=approve_recommendation" method="POST" class="mb-3">
                            <input type="hidden" name="recommendation_id" value="<?=$recommendation['id']?>">
                            
                            <div class="form-group mb-2">
                                <label class="font-weight-normal text-muted small">Ghi chú phê duyệt (Tùy chọn):</label>
                                <textarea name="review_notes" rows="2" class="form-control form-control-sm" placeholder="Nhập ghi chú định hướng thử nghiệm..."></textarea>
                            </div>

                            <?php if ((float)$recommendation['estimated_cost_vnd'] > (float)($rulesSnapshot['max_cost_per_experiment'] ?? 60000)): ?>
                            <div class="custom-control custom-checkbox mb-3 p-2 bg-light border rounded">
                                <input type="checkbox" class="custom-control-input" id="admin_override" name="admin_override" value="1">
                                <label class="custom-control-label text-danger font-weight-bold small" for="admin_override">
                                    Xác nhận Admin Override (Cho phép chi phí vượt hạn mức)
                                </label>
                            </div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-block btn-success shadow-sm font-weight-bold">
                                <i class="fas fa-check mr-1"></i> PHÊ DUYỆT & KHỞI TẠO THỬ NGHIỆM A/B
                            </button>
                        </form>

                        <!-- Reject Form -->
                        <form action="index.php?com=optimization&act=reject_recommendation" method="POST">
                            <input type="hidden" name="recommendation_id" value="<?=$recommendation['id']?>">
                            <div class="input-group">
                                <input type="text" name="reject_reason" class="form-control form-control-sm" placeholder="Lý do từ chối...">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn từ chối khuyến nghị này?');">
                                        <i class="fas fa-times mr-1"></i> Từ chối
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php elseif ($recommendation['status'] === 'EXECUTING' || $recommendation['status'] === 'APPROVED' || $recommendation['status'] === 'COMPLETED'): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3 text-center">
                        <i class="fas fa-flask fa-2x text-info mb-2"></i>
                        <p class="mb-2 font-weight-bold">Khuyến nghị này đã được chuyển thành Thử nghiệm A/B!</p>
                        <?php if (!empty($recommendation['id_experiment'])): ?>
                        <a href="index.php?com=optimization&act=experiment_detail&id=<?=$recommendation['id_experiment']?>" class="btn btn-sm btn-info shadow-sm">
                            <i class="fas fa-eye mr-1"></i> Xem Thử nghiệm #<?=$recommendation['id_experiment']?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
