<?php
$linkWinner = "index.php?com=analytics&act=winner_detection";
$linkRules = "index.php?com=analytics&act=winner_rules";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-trophy mr-2 text-warning"></i>Động Cơ Phát Hiện Sản Phẩm Thắng (Winner Detection Engine)
                </h1>
                <small class="text-muted">Đánh giá theo quy tắc thống kê thực nghiệm (Rule-Based Analytics) — Cổng kích thước mẫu nghiêm ngặt (Sample Size Gate)</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkRules?>" class="btn btn-sm btn-outline-secondary shadow-sm mr-2">
                    <i class="fas fa-sliders-h mr-1"></i> Cấu Hình Ngưỡng & Quy Tắc
                </a>
                <form method="POST" action="index.php?com=analytics&act=evaluate_winner" class="d-inline">
                    <button type="submit" class="btn btn-sm btn-warning shadow-sm font-weight-bold text-dark">
                        <i class="fas fa-sync-alt mr-1"></i> Chạy Đánh Giá Toàn Bộ Sản Phẩm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Winner Status KPI Cards -->
<section class="content mb-2 text-sm">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <div class="card shadow-sm border-0 h-100 bg-white" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body p-3">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem;">WINNERS</span>
                        <h3 class="font-weight-bold text-warning mb-0 mt-1"><?=$summaryStats['WINNER']?></h3>
                        <small class="text-muted">CTR &ge; <?=$rulesConfig['winner_ctr_pct']?>% &amp; Mẫu đủ</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <div class="card shadow-sm border-0 h-100 bg-white" style="border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body p-3">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem;">PROMISING</span>
                        <h3 class="font-weight-bold text-info mb-0 mt-1"><?=$summaryStats['PROMISING']?></h3>
                        <small class="text-muted">CTR &ge; <?=$rulesConfig['promising_ctr_pct']?>% (Tiềm năng)</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <div class="card shadow-sm border-0 h-100 bg-white" style="border-left: 4px solid #007bff !important;">
                    <div class="card-body p-3">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem;">TESTING</span>
                        <h3 class="font-weight-bold text-primary mb-0 mt-1"><?=$summaryStats['TESTING']?></h3>
                        <small class="text-muted">Đang chạy kiểm thử</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-6 mb-3">
                <div class="card shadow-sm border-0 h-100 bg-white" style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body p-3">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem;">UNDERPERFORMING</span>
                        <h3 class="font-weight-bold text-danger mb-0 mt-1"><?=$summaryStats['UNDERPERFORMING']?></h3>
                        <small class="text-muted">Đủ mẫu nhưng CTR &lt; <?=$rulesConfig['underperforming_ctr_pct']?>%</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-12 mb-3">
                <div class="card shadow-sm border-0 h-100 bg-white" style="border-left: 4px solid #6c757d !important;">
                    <div class="card-body p-3">
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem;">INSUFFICIENT DATA</span>
                        <h3 class="font-weight-bold text-secondary mb-0 mt-1"><?=$summaryStats['INSUFFICIENT_DATA']?></h3>
                        <small class="text-muted">Chưa đạt <?=$rulesConfig['min_landing_sessions']?> sessions / <?=$rulesConfig['min_affiliate_clicks']?> clicks</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Winner Board -->
<section class="content text-sm">
    <div class="container-fluid">
        <div class="card card-outline card-warning shadow-sm">
            <div class="card-header bg-white py-2">
                <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-trophy mr-2 text-warning"></i>Bảng Phân Hạng Winner & Đề Xuất Kinh Doanh</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">STT</th>
                            <th style="min-width: 240px;">Sản phẩm</th>
                            <th class="text-center" style="width: 140px;">Trạng thái Winner</th>
                            <th class="text-center" style="width: 130px;">Tín hiệu (Signal)</th>
                            <th class="text-center" style="width: 120px;">Độ lớn mẫu</th>
                            <th class="text-center" style="width: 100px;">CTR (%)</th>
                            <th class="text-center" style="width: 90px;">Đơn hàng</th>
                            <th class="text-right" style="width: 130px;">Hoa hồng</th>
                            <th class="text-right" style="width: 130px;">Content ROI</th>
                            <th style="min-width: 260px;">Đề xuất hành động (Recommendation)</th>
                            <th class="text-center" style="width: 100px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($evaluatedProducts)): ?>
                            <?php foreach ($evaluatedProducts as $k => $item): ?>
                                <?php
                                $st = $item['winner_status'] ?? 'INSUFFICIENT_DATA';
                                $sig = $item['signal_level'] ?? 'NONE';

                                $stBadge = 'badge-secondary';
                                if ($st === 'WINNER') $stBadge = 'badge-warning text-dark font-weight-bold';
                                elseif ($st === 'PROMISING') $stBadge = 'badge-info';
                                elseif ($st === 'TESTING') $stBadge = 'badge-primary';
                                elseif ($st === 'UNDERPERFORMING') $stBadge = 'badge-danger';

                                $sigBadge = 'badge-light border';
                                if ($sig === 'REVENUE_SIGNAL') $sigBadge = 'badge-success';
                                elseif ($sig === 'CONVERSION_SIGNAL') $sigBadge = 'badge-success';
                                elseif ($sig === 'CLICK_SIGNAL') $sigBadge = 'badge-info';
                                elseif ($sig === 'TRAFFIC_SIGNAL') $sigBadge = 'badge-primary';
                                ?>
                                <tr>
                                    <td class="text-center font-weight-bold text-muted"><?=($k + 1)?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['photo'])): ?>
                                                <img src="../upload/product/<?=$item['photo']?>" alt="<?=$item['name']?>" class="img-thumbnail mr-2" style="width: 45px; height: 45px; object-fit: cover;" onerror="this.src='../assets/images/noimage.png'">
                                            <?php endif; ?>
                                            <div>
                                                <a href="../<?=$item['slug']?>" target="_blank" class="font-weight-bold text-dark d-block">
                                                    <?=$item['name']?>
                                                </a>
                                                <small class="text-muted">ID: #<?=$item['id_product']?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?=$stBadge?> px-2 py-1" style="font-size: 0.8rem;">
                                            <?=$st?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?=$sigBadge?>" style="font-size: 0.75rem;">
                                            <?=$sig?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="font-weight-bold text-dark"><?=$item['sessions']?> sessions</div>
                                        <small class="text-muted"><?=$item['clicks']?> clicks</small>
                                    </td>
                                    <td class="text-center font-weight-bold">
                                        <span class="badge badge-light border" style="font-size: 0.85rem;"><?=$item['ctr_pct']?>%</span>
                                    </td>
                                    <td class="text-center font-weight-bold text-success">
                                        <?=number_format($item['conversions'])?>
                                    </td>
                                    <td class="text-right font-weight-bold text-warning">
                                        <?=number_format($item['commission_vnd'])?> đ
                                    </td>
                                    <td class="text-right font-weight-bold <?=($item['roi_vnd'] >= 0) ? 'text-success' : 'text-danger'?>">
                                        <?=number_format($item['roi_vnd'])?> đ
                                    </td>
                                    <td>
                                        <?php if ($st === 'WINNER'): ?>
                                            <span class="text-success font-weight-bold"><i class="fas fa-arrow-up mr-1"></i>Đề xuất: Nâng cấp Video HYBRID & Tạo thêm Hook</span>
                                        <?php elseif ($st === 'PROMISING'): ?>
                                            <span class="text-info font-weight-bold"><i class="fas fa-plus mr-1"></i>Đề xuất: Tạo thêm 2-3 kịch bản biến thể</span>
                                        <?php elseif ($st === 'UNDERPERFORMING'): ?>
                                            <span class="text-danger font-weight-bold"><i class="fas fa-redo mr-1"></i>Đề xuất: Đổi Hook mở đầu hoặc tạm dừng</span>
                                        <?php elseif ($st === 'INSUFFICIENT_DATA'): ?>
                                            <span class="text-muted"><i class="fas fa-hourglass-half mr-1"></i>Cần thêm dữ liệu (Chưa đủ mẫu kết luận)</span>
                                        <?php else: ?>
                                            <span class="text-primary"><i class="fas fa-eye mr-1"></i>Tiếp tục theo dõi chuyển đổi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="index.php?com=analytics&act=evaluate_winner" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?=$item['id_product']?>">
                                            <button type="submit" class="btn btn-xs btn-outline-warning" title="Đánh giá lại Winner">
                                                <i class="fas fa-sync-alt"></i> Đánh giá
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">
                                    <i class="fas fa-trophy fa-3x mb-2 d-block text-secondary"></i>
                                    <strong>Chưa có dữ liệu đánh giá Winner</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
