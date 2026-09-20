<?php
$linkExperiments = "index.php?com=optimization&act=experiments";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-balance-scale mr-2 text-info"></i>Đối soát Thử nghiệm: <?=htmlspecialchars($experiment['experiment_code'])?>
                </h1>
                <small class="text-muted">So sánh hiệu suất trực quan giữa Bài đăng Gốc (Baseline) và Biến thể mới (Variation)</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <form action="index.php?com=optimization&act=evaluate_experiment" method="POST" class="d-inline">
                    <input type="hidden" name="experiment_id" value="<?=$experiment['id']?>">
                    <button type="submit" class="btn btn-sm btn-success shadow-sm font-weight-bold mr-1">
                        <i class="fas fa-check-double mr-1"></i> Đánh giá Kết luận Thử nghiệm
                    </button>
                </form>
                <a href="<?=$linkExperiments?>" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm pb-4">
    <div class="container-fluid">
        <!-- Hypothesis & Conclusion Alert -->
        <?php if (!empty($experiment['result_conclusion'])): ?>
        <div class="alert alert-<?=($experiment['result_conclusion'] === 'VARIATION_BETTER') ? 'success' : (($experiment['result_conclusion'] === 'INSUFFICIENT_DATA') ? 'warning' : 'info')?> shadow-sm mb-3">
            <h5 class="font-weight-bold mb-1"><i class="fas fa-flag-checkered mr-2"></i>KẾT LUẬN: <?=$experiment['result_conclusion']?></h5>
            <p class="mb-0"><?=htmlspecialchars($experiment['result_summary'])?></p>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge badge-light border text-dark mr-2">Biến số: <strong><?=htmlspecialchars($experiment['changed_variable'])?></strong></span>
                        <span class="badge badge-light border text-muted mr-2">Chế độ: <strong><?=htmlspecialchars($experiment['target_mode'])?></strong></span>
                        <span class="badge badge-primary mr-2">Trạng thái: <strong><?=$experiment['status']?></strong></span>
                        <div class="mt-2 text-dark font-weight-bold">
                            <i class="fas fa-vial text-warning mr-1"></i> Giả thuyết: <?=htmlspecialchars($experiment['hypothesis'])?>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-right mt-2 mt-md-0">
                        <small class="text-muted d-block">Sản phẩm thử nghiệm:</small>
                        <strong class="text-dark"><?=htmlspecialchars($product['namevi'] ?? ('Sản phẩm #' . $experiment['id_product']))?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side by Side Comparison Cards -->
        <div class="row">
            <!-- 1. BASELINE (GỐC) -->
            <div class="col-lg-6 col-12 mb-3">
                <div class="card shadow-sm border-0 h-100 bg-white" style="border-top: 4px solid #6c757d !important;">
                    <div class="card-header bg-white font-weight-bold py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-history text-secondary mr-2"></i>
                            <span>BẢN GỐC (BASELINE)</span>
                            <span class="badge badge-secondary ml-auto">Control</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Post Information -->
                        <?php if (!empty($baselinePost)): ?>
                        <div class="mb-3 p-3 bg-light rounded border">
                            <div class="font-weight-bold text-dark mb-1"><?=htmlspecialchars($baselinePost['title'])?></div>
                            <small class="text-muted d-block mb-1">Mã tracking: <code><?=$baselinePost['tracking_code']?></code></small>
                            <small class="text-muted d-block text-truncate" style="max-width: 100%;"><?=htmlspecialchars($baselinePost['caption'])?></small>
                            <?php if (!empty($baselinePost['external_post_url'])): ?>
                            <a href="<?=htmlspecialchars($baselinePost['external_post_url'])?>" target="_blank" class="btn btn-xs btn-outline-dark mt-2">
                                <i class="fab fa-tiktok mr-1"></i> Xem bài đăng TikTok gốc
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-light border text-muted small mb-3">Không có bài đăng gốc trực tiếp (sử dụng baseline sản phẩm).</div>
                        <?php endif; ?>

                        <!-- Performance Metrics -->
                        <h6 class="font-weight-bold text-muted small text-uppercase mb-2">Chỉ số Hiệu suất Baseline:</h6>
                        <div class="row text-center">
                            <div class="col-4 mb-3">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Sessions</small>
                                    <strong class="font-weight-bold text-dark" style="font-size: 1.15rem;"><?=$baselineMetrics['sessions'] ?? 0?></strong>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Clicks</small>
                                    <strong class="font-weight-bold text-dark" style="font-size: 1.15rem;"><?=$baselineMetrics['clicks'] ?? 0?></strong>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">CTR</small>
                                    <strong class="font-weight-bold text-dark" style="font-size: 1.15rem;"><?=$baselineMetrics['ctr_pct'] ?? 0?>%</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Đơn hàng (Conversions)</small>
                                    <strong class="font-weight-bold text-dark"><?=$baselineMetrics['conversions'] ?? 0?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Hoa hồng thực tế</small>
                                    <strong class="font-weight-bold text-dark"><?=number_format($baselineMetrics['commission_vnd'] ?? 0, 0, ',', '.')?> đ</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. VARIATION (BIẾN THỂ MỚI) -->
            <div class="col-lg-6 col-12 mb-3">
                <div class="card shadow-sm border-0 h-100 bg-white" style="border-top: 4px solid #28a745 !important;">
                    <div class="card-header bg-white font-weight-bold py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-magic text-success mr-2"></i>
                            <span>BIẾN THỂ MỚI (VARIATION)</span>
                            <span class="badge badge-success ml-auto">Variant</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Post Information -->
                        <?php if (!empty($variationPost)): ?>
                        <div class="mb-3 p-3 bg-light rounded border border-success">
                            <div class="font-weight-bold text-primary mb-1"><?=htmlspecialchars($variationPost['title'])?></div>
                            <small class="text-muted d-block mb-1">Mã tracking: <code><?=$variationPost['tracking_code']?></code></small>
                            <small class="text-muted d-block text-truncate" style="max-width: 100%;"><?=htmlspecialchars($variationPost['caption'])?></small>
                            <div class="mt-2">
                                <span class="badge badge-<?=($variationPost['status'] === 'PUBLISHED') ? 'success' : 'info'?>">
                                    Trạng thái bài: <?=$variationPost['status']?>
                                </span>
                                <?php if (!empty($variationPost['external_post_url'])): ?>
                                <a href="<?=htmlspecialchars($variationPost['external_post_url'])?>" target="_blank" class="btn btn-xs btn-outline-success ml-2">
                                    <i class="fab fa-tiktok mr-1"></i> Xem bài biến thể TikTok
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning small mb-3">Đang trong tiến trình khởi tạo biến thể nội dung & video...</div>
                        <?php endif; ?>

                        <!-- Performance Metrics -->
                        <h6 class="font-weight-bold text-muted small text-uppercase mb-2">Chỉ số Hiệu suất Biến thể (Real-time):</h6>
                        <div class="row text-center">
                            <div class="col-4 mb-3">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Sessions</small>
                                    <strong class="font-weight-bold text-primary" style="font-size: 1.15rem;"><?=$variationMetrics['sessions'] ?? 0?></strong>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Clicks</small>
                                    <strong class="font-weight-bold text-info" style="font-size: 1.15rem;"><?=$variationMetrics['clicks'] ?? 0?></strong>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">CTR</small>
                                    <strong class="font-weight-bold text-success" style="font-size: 1.15rem;"><?=$variationMetrics['ctr_pct'] ?? 0?>%</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Đơn hàng (Conversions)</small>
                                    <strong class="font-weight-bold text-dark"><?=$variationMetrics['conversions'] ?? 0?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Hoa hồng thực tế</small>
                                    <strong class="font-weight-bold text-dark"><?=number_format($variationMetrics['commission_vnd'] ?? 0, 0, ',', '.')?> đ</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
