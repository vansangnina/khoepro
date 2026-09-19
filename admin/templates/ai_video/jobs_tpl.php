<?php
$linkJobs = "index.php?com=ai_video&act=jobs";
$linkMan = "index.php?com=ai_video&act=man";
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    Hàng đợi Tác vụ Render Video (Video Job Queue)
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="index.php?com=ai_video&act=process_queue" class="btn btn-primary mr-2"><i class="fas fa-play mr-1"></i> Chạy Hàng đợi Ngay</a>
                <a href="<?=$linkJobs?>" class="btn btn-outline-secondary"><i class="fas fa-sync mr-1"></i> Làm mới</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Thống kê Jobs -->
        <div class="row mb-3">
            <div class="col-md-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-start"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Chờ xử lý (PENDING)</span>
                        <span class="info-box-number"><?=$jobStats['PENDING']?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-primary"><i class="fas fa-spinner fa-spin"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Đang Render (RUNNING)</span>
                        <span class="info-box-number"><?=$jobStats['RUNNING']?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Thành công (SUCCESS)</span>
                        <span class="info-box-number"><?=$jobStats['SUCCESS']?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Thất bại (FAILED)</span>
                        <span class="info-box-number"><?=$jobStats['FAILED']?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-primary shadow-sm text-sm">
            <div class="card-header py-2">
                <h3 class="card-title font-weight-bold"><i class="fas fa-list mr-2"></i>Danh sách Tác vụ Render</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped align-middle mb-0" style="width: 100%; table-layout: auto;">
                    <thead class="thead-light">
                        <tr class="text-center" style="font-size: 13px;">
                            <th style="width: 50px;">ID</th>
                            <th class="text-left" style="min-width: 200px;">Dự án Video & Sản phẩm</th>
                            <th style="width: 100px;">Provider</th>
                            <th style="width: 120px;">Trạng thái</th>
                            <th style="width: 90px;">Lần thử</th>
                            <th style="width: 130px;">Thời gian chạy</th>
                            <th class="text-left" style="min-width: 200px;">Kết quả / Chi tiết lỗi</th>
                            <th style="width: 90px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($jobs)): foreach ($jobs as $jb): ?>
                            <tr>
                                <td class="text-center align-middle font-weight-bold text-muted">#<?=$jb['id']?></td>
                                <td class="align-middle">
                                    <a href="index.php?com=ai_video&act=view&id=<?=$jb['id_video']?>" class="font-weight-bold text-primary" style="word-break: break-word;">
                                        <?=htmlspecialchars($jb['video_title'] ?: 'Video #'.$jb['id_video'])?>
                                    </a>
                                    <div class="small text-muted mt-1" style="word-break: break-word;"><i class="fas fa-box mr-1"></i><?=htmlspecialchars($jb['product_name'] ?: '')?></div>
                                </td>
                                <td class="text-center align-middle"><span class="badge badge-light border text-uppercase font-weight-bold"><?=$jb['provider']?></span></td>
                                <td class="text-center align-middle">
                                    <?php
                                    $st = $jb['status'];
                                    if ($st === 'PENDING') echo '<span class="badge badge-warning p-1 text-wrap d-block">PENDING</span>';
                                    elseif ($st === 'RUNNING') echo '<span class="badge badge-primary p-1 text-wrap d-block"><i class="fas fa-spinner fa-spin mr-1"></i>RUNNING</span>';
                                    elseif ($st === 'SUCCESS') echo '<span class="badge badge-success p-1 text-wrap d-block">SUCCESS</span>';
                                    elseif ($st === 'FAILED') echo '<span class="badge badge-danger p-1 text-wrap d-block">FAILED</span>';
                                    else echo '<span class="badge badge-secondary p-1 text-wrap d-block">'.$st.'</span>';
                                    ?>
                                </td>
                                <td class="text-center align-middle text-xs">
                                    <span class="badge badge-light border"><?=$jb['attempts']?> / <?=$jb['max_attempts']?></span>
                                </td>
                                <td class="text-center align-middle text-muted" style="font-size: 11px; line-height: 1.3;">
                                    <?php if ($jb['started_at']): ?>
                                        <div><i class="fas fa-play fa-xs text-muted mr-1"></i><?=date('H:i:s d/m', $jb['started_at'])?></div>
                                    <?php endif; ?>
                                    <?php if ($jb['completed_at']): ?>
                                        <div><i class="fas fa-check fa-xs text-success mr-1"></i><?=date('H:i:s d/m', $jb['completed_at'])?></div>
                                    <?php endif; ?>
                                    <?php if (!$jb['started_at'] && !$jb['completed_at']): ?>
                                        <span class="text-muted">Chưa chạy</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle text-xs" style="word-break: break-word;">
                                    <?php if (!empty($jb['error_message'])): ?>
                                        <div class="text-danger" style="word-break: break-word;"><i class="fas fa-exclamation-triangle mr-1"></i> <?=htmlspecialchars($jb['error_message'])?></div>
                                    <?php else: ?>
                                        <div class="text-success" style="word-break: break-word;"><i class="fas fa-check-circle mr-1"></i> <?=htmlspecialchars($jb['results_summary'] ?: 'Hoàn thành tốt')?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-inline-flex flex-wrap justify-content-center" style="gap: 3px;">
                                        <?php if ($jb['status'] === 'PENDING' || $jb['status'] === 'RUNNING'): ?>
                                            <a href="index.php?com=ai_video&act=job_process&id=<?=$jb['id']?>" class="btn btn-xs btn-primary" title="Xử lý / Kiểm tra tiến độ ngay"><i class="fas fa-play"></i></a>
                                        <?php endif; ?>
                                        <?php if ($jb['status'] === 'FAILED'): ?>
                                            <a href="index.php?com=ai_video&act=job_retry&id=<?=$jb['id']?>" class="btn btn-xs btn-warning" title="Thử lại"><i class="fas fa-redo"></i></a>
                                        <?php endif; ?>
                                        <a href="index.php?com=ai_video&act=job_delete&id=<?=$jb['id']?>" class="btn btn-xs btn-danger" onclick="return confirm('Xóa tác vụ này?');" title="Xóa"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="8" class="text-center py-5 text-muted"><i class="fas fa-check-circle fa-2x mb-2 text-success d-block"></i>Hàng đợi hiện đang trống.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($paging)): ?>
                <div class="card-footer clearfix py-2"><?=$paging?></div>
            <?php endif; ?>
        </div>
    </div>
</section>
