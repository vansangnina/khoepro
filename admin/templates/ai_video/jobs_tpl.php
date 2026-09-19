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

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title font-weight-bold"><i class="fas fa-list mr-2"></i>Danh sách Tác vụ Render</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped mb-0 text-nowrap">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Dự án Video</th>
                            <th>Provider</th>
                            <th>Trạng thái</th>
                            <th>Lần thử (Attempts)</th>
                            <th>Thời gian bắt đầu / Hoàn tất</th>
                            <th>Kết quả / Lỗi</th>
                            <th style="width: 100px;" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($jobs)): foreach ($jobs as $jb): ?>
                            <tr>
                                <td>#<?=$jb['id']?></td>
                                <td>
                                    <a href="index.php?com=ai_video&act=view&id=<?=$jb['id_video']?>">
                                        <strong><?=htmlspecialchars($jb['video_title'] ?: 'Video #'.$jb['id_video'])?></strong>
                                    </a>
                                    <div class="small text-muted"><?=htmlspecialchars($jb['product_name'] ?: '')?></div>
                                </td>
                                <td><span class="badge badge-light border"><?=$jb['provider']?></span></td>
                                <td>
                                    <?php
                                    $st = $jb['status'];
                                    if ($st === 'PENDING') echo '<span class="badge badge-warning">PENDING</span>';
                                    elseif ($st === 'RUNNING') echo '<span class="badge badge-primary"><i class="fas fa-spinner fa-spin mr-1"></i>RUNNING</span>';
                                    elseif ($st === 'SUCCESS') echo '<span class="badge badge-success">SUCCESS</span>';
                                    elseif ($st === 'FAILED') echo '<span class="badge badge-danger">FAILED</span>';
                                    else echo '<span class="badge badge-secondary">'.$st.'</span>';
                                    ?>
                                </td>
                                <td><?=$jb['attempts']?> / <?=$jb['max_attempts']?></td>
                                <td>
                                    <small>
                                        Bắt đầu: <?=$jb['started_at'] ? date('H:i:s d/m', $jb['started_at']) : 'Chưa'?><br>
                                        Hoàn tất: <?=$jb['completed_at'] ? date('H:i:s d/m', $jb['completed_at']) : 'Chưa'?>
                                    </small>
                                </td>
                                <td>
                                    <?php if (!empty($jb['error_message'])): ?>
                                        <span class="text-danger small"><i class="fas fa-exclamation-triangle"></i> <?=htmlspecialchars($jb['error_message'])?></span>
                                    <?php else: ?>
                                        <span class="text-success small"><?=htmlspecialchars($jb['results_summary'] ?: 'Hoàn thành tốt')?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($jb['status'] === 'PENDING' || $jb['status'] === 'RUNNING'): ?>
                                        <a href="index.php?com=ai_video&act=job_process&id=<?=$jb['id']?>" class="btn btn-xs btn-primary mr-1" title="Xử lý / Kiểm tra tiến độ ngay"><i class="fas fa-play"></i></a>
                                    <?php endif; ?>
                                    <?php if ($jb['status'] === 'FAILED'): ?>
                                        <a href="index.php?com=ai_video&act=job_retry&id=<?=$jb['id']?>" class="btn btn-xs btn-warning mr-1" title="Thử lại"><i class="fas fa-redo"></i></a>
                                    <?php endif; ?>
                                    <a href="index.php?com=ai_video&act=job_delete&id=<?=$jb['id']?>" class="btn btn-xs btn-danger" onclick="return confirm('Xóa tác vụ này?');" title="Xóa"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="8" class="text-center py-4 text-muted">Hàng đợi trống.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($paging)): ?>
                <div class="card-footer clearfix"><?=$paging?></div>
            <?php endif; ?>
        </div>
    </div>
</section>
