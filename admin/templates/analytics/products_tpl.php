<?php
$linkProducts = "index.php?com=analytics&act=products";
$linkWinner = "index.php?com=analytics&act=winner_detection";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-boxes mr-2 text-primary"></i>Hiệu Quả Sản Phẩm (Product Performance Analytics)
                </h1>
                <small class="text-muted">Đo lường hiệu suất thực tế từng sản phẩm (Observed Metrics) — Không trộn lẫn với Điểm Nghiên cứu cơ hội (Research Score)</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <div class="btn-group shadow-sm mr-2" role="group">
                    <a href="<?=$linkProducts?>&time_range=today" class="btn btn-sm <?=($timeRange === 'today') ? 'btn-primary' : 'btn-outline-secondary'?>">Hôm nay</a>
                    <a href="<?=$linkProducts?>&time_range=7d" class="btn btn-sm <?=($timeRange === '7d') ? 'btn-primary' : 'btn-outline-secondary'?>">7 ngày</a>
                    <a href="<?=$linkProducts?>&time_range=30d" class="btn btn-sm <?=($timeRange === '30d') ? 'btn-primary' : 'btn-outline-secondary'?>">30 ngày</a>
                    <a href="<?=$linkProducts?>&time_range=all" class="btn btn-sm <?=($timeRange === 'all') ? 'btn-primary' : 'btn-outline-secondary'?>">Tất cả</a>
                </div>
                <a href="<?=$linkWinner?>" class="btn btn-sm btn-warning shadow-sm font-weight-bold text-dark">
                    <i class="fas fa-trophy mr-1"></i> Bảng Xếp Hạng Winner
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header bg-white py-2">
                <h3 class="card-title font-weight-bold"><i class="fas fa-table mr-2 text-primary"></i>Danh Sách Sản Phẩm & Chỉ Số Hiệu Suất</h3>
                <div class="card-tools">
                    <span class="badge badge-light border">Khoảng thời gian: <?=$timeRange?></span>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">STT</th>
                            <th style="min-width: 250px;">Sản phẩm</th>
                            <th class="text-center" style="width: 120px;" title="Điểm nghiên cứu cơ hội trước xuất bản (Phase 03)">Research Score</th>
                            <th class="text-center" style="width: 100px;">Sessions</th>
                            <th class="text-center" style="width: 100px;">Clicks</th>
                            <th class="text-center" style="width: 120px;">CTR (%)</th>
                            <th class="text-center" style="width: 90px;">Đơn hàng</th>
                            <th class="text-center" style="width: 100px;">CVR (%)</th>
                            <th class="text-right" style="width: 130px;">Hoa hồng (VND)</th>
                            <th class="text-right" style="width: 120px;">Chi phí Video</th>
                            <th class="text-right" style="width: 130px;">Content ROI</th>
                            <th class="text-center" style="width: 140px;">Tín hiệu Winner</th>
                            <th class="text-center" style="width: 100px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($productMetrics)): ?>
                            <?php foreach ($productMetrics as $k => $item): ?>
                                <?php
                                $statusBadge = 'badge-secondary';
                                if ($item['winner_status'] === 'WINNER') $statusBadge = 'badge-warning text-dark font-weight-bold';
                                elseif ($item['winner_status'] === 'PROMISING') $statusBadge = 'badge-info';
                                elseif ($item['winner_status'] === 'TESTING') $statusBadge = 'badge-primary';
                                elseif ($item['winner_status'] === 'UNDERPERFORMING') $statusBadge = 'badge-danger';

                                $signalBadge = 'badge-light';
                                if ($item['signal_level'] === 'REVENUE_SIGNAL') $signalBadge = 'badge-success';
                                elseif ($item['signal_level'] === 'CONVERSION_SIGNAL') $signalBadge = 'badge-success';
                                elseif ($item['signal_level'] === 'CLICK_SIGNAL') $signalBadge = 'badge-info';
                                elseif ($item['signal_level'] === 'TRAFFIC_SIGNAL') $signalBadge = 'badge-primary';
                                ?>
                                <tr>
                                    <td class="text-center font-weight-bold text-muted"><?=($k + 1)?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['photo'])): ?>
                                                <img src="../upload/product/<?=$item['photo']?>" alt="<?=$item['name']?>" class="img-thumbnail mr-2" style="width: 45px; height: 45px; object-fit: cover;" onerror="this.src='../assets/images/noimage.png'">
                                            <?php else: ?>
                                                <div class="bg-light border rounded mr-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-box text-secondary"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <a href="../<?=$item['slug']?>" target="_blank" class="font-weight-bold text-dark d-block">
                                                    <?=$item['name']?>
                                                </a>
                                                <small class="text-muted">ID: #<?=$item['id_product']?> | Giá: <?=number_format($item['regular_price'])?> đ</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($item['research_score'] !== null): ?>
                                            <span class="badge badge-light border font-weight-bold" title="Điểm cơ hội thị trường: <?=$item['research_score']?>/100">
                                                <i class="fas fa-search-dollar text-warning mr-1"></i><?=$item['research_score']?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center font-weight-bold text-primary">
                                        <?=number_format($item['sessions'])?>
                                    </td>
                                    <td class="text-center font-weight-bold text-info">
                                        <?=number_format($item['clicks'])?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light border font-weight-bold text-dark" style="font-size: 0.85rem;">
                                            <?=$item['ctr_pct']?>%
                                        </span>
                                        <div class="text-muted" style="font-size: 0.7rem;"><?=$item['clicks']?> / <?=$item['sessions']?></div>
                                    </td>
                                    <td class="text-center font-weight-bold text-success">
                                        <?=number_format($item['conversions'])?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light border font-weight-bold">
                                            <?=$item['cvr_pct']?>%
                                        </span>
                                    </td>
                                    <td class="text-right font-weight-bold text-warning">
                                        <?=number_format($item['commission_vnd'])?> đ
                                    </td>
                                    <td class="text-right text-danger">
                                        <?=number_format($item['content_cost_vnd'])?> đ
                                    </td>
                                    <td class="text-right font-weight-bold <?=($item['roi_vnd'] >= 0) ? 'text-success' : 'text-danger'?>">
                                        <?=number_format($item['roi_vnd'])?> đ
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?=$statusBadge?> p-1 mb-1 d-block" style="font-size: 0.75rem;">
                                            <?=$item['winner_status']?>
                                        </span>
                                        <span class="badge <?=$signalBadge?>" style="font-size: 0.65rem;">
                                            <?=$item['signal_level']?>
                                        </span>
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
                                <td colspan="13" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-2 d-block text-secondary"></i>
                                    <strong>Chưa có dữ liệu hiệu suất sản phẩm</strong>
                                    <p class="small text-muted mb-0">Hệ thống sẽ tự động cập nhật khi có lượt truy cập từ bài đăng xuất bản.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
