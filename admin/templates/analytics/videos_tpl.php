<?php
$linkVideos = "index.php?com=analytics&act=videos";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-video mr-2 text-primary"></i>Hiệu Quả Video AI (Video Mode & Cost Analytics)
                </h1>
                <small class="text-muted">So sánh hiệu quả chuyển đổi và chi phí giữa chế độ ECONOMY (0 VND) vs HYBRID (AI Video)</small>
            </div>
            <div class="col-sm-6 text-sm-right">
                <div class="btn-group shadow-sm mr-2" role="group">
                    <a href="<?=$linkVideos?>&time_range=today" class="btn btn-sm <?=($timeRange === 'today') ? 'btn-primary' : 'btn-outline-secondary'?>">Hôm nay</a>
                    <a href="<?=$linkVideos?>&time_range=7d" class="btn btn-sm <?=($timeRange === '7d') ? 'btn-primary' : 'btn-outline-secondary'?>">7 ngày</a>
                    <a href="<?=$linkVideos?>&time_range=30d" class="btn btn-sm <?=($timeRange === '30d') ? 'btn-primary' : 'btn-outline-secondary'?>">30 ngày</a>
                    <a href="<?=$linkVideos?>&time_range=all" class="btn btn-sm <?=($timeRange === 'all') ? 'btn-primary' : 'btn-outline-secondary'?>">Tất cả</a>
                </div>
                <a href="index.php?com=ai_video&act=man" class="btn btn-sm btn-info shadow-sm">
                    <i class="fas fa-external-link-alt mr-1"></i> Quản Lý Video
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header bg-white py-2">
                <h3 class="card-title font-weight-bold"><i class="fas fa-film mr-2 text-primary"></i>Báo Cáo Hiệu Suất Theo Dự Án Video</h3>
                <div class="card-tools">
                    <span class="badge badge-light border">Khoảng thời gian: <?=$timeRange?></span>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">STT</th>
                            <th style="min-width: 250px;">Dự án Video</th>
                            <th class="text-center" style="width: 110px;">Chế độ (Mode)</th>
                            <th style="min-width: 200px;">Sản phẩm</th>
                            <th class="text-center" style="width: 100px;">Thời lượng</th>
                            <th class="text-right" style="width: 120px;">Chi phí Video</th>
                            <th class="text-center" style="width: 90px;">Sessions</th>
                            <th class="text-center" style="width: 90px;">Clicks</th>
                            <th class="text-center" style="width: 100px;">CTR (%)</th>
                            <th class="text-center" style="width: 90px;">Đơn hàng</th>
                            <th class="text-right" style="width: 130px;">Hoa hồng (VND)</th>
                            <th class="text-right" style="width: 130px;">Content ROI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($videoMetrics)): ?>
                            <?php foreach ($videoMetrics as $k => $item): ?>
                                <?php
                                $modeBadge = 'badge-success';
                                if ($item['mode'] === 'HYBRID') $modeBadge = 'badge-primary';
                                elseif ($item['mode'] === 'PREMIUM') $modeBadge = 'badge-warning text-dark';
                                ?>
                                <tr>
                                    <td class="text-center font-weight-bold text-muted"><?=($k + 1)?></td>
                                    <td>
                                        <a href="index.php?com=ai_video&act=view&id=<?=$item['id_video']?>" class="font-weight-bold text-dark d-block">
                                            <?=$item['title']?>
                                        </a>
                                        <small class="text-muted">ID: #<?=$item['id_video']?> | Trạng thái: <span class="badge badge-light border"><?=$item['status']?></span></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?=$modeBadge?> px-2 py-1 font-weight-bold">
                                            <?=$item['mode']?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-dark"><?=$item['product_name']?></span>
                                    </td>
                                    <td class="text-center">
                                        <?=$item['duration_actual'] ? ($item['duration_actual'] . 's') : 'N/A'?>
                                    </td>
                                    <td class="text-right text-danger font-weight-bold">
                                        <?=number_format($item['total_cost_vnd'])?> đ
                                    </td>
                                    <td class="text-center font-weight-bold text-primary">
                                        <?=number_format($item['sessions'])?>
                                    </td>
                                    <td class="text-center font-weight-bold text-info">
                                        <?=number_format($item['clicks'])?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light border font-weight-bold">
                                            <?=$item['ctr_pct']?>%
                                        </span>
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
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="12" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-2 d-block text-secondary"></i>
                                    <strong>Chưa có dữ liệu hiệu suất video</strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
