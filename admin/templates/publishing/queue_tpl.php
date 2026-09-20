<?php
$linkMan = "index.php?com=publishing&act=man";
$linkQueue = "index.php?com=publishing&act=queue";
$linkProcessQueue = "index.php?com=publishing&act=process_queue";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.25rem;">
                    <i class="fas fa-tasks mr-2 text-warning"></i>Hàng đợi Xuất bản (Publishing Queue Monitor)
                </h1>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkProcessQueue?>" class="btn btn-sm btn-primary mr-2 shadow-sm font-weight-bold" onclick="return confirm('Kích hoạt tiến trình quét hàng đợi ngay bây giờ?')">
                    <i class="fas fa-play mr-1"></i> Chạy Worker Ngay
                </a>
                <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Danh sách bài đăng
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm pb-4">
    <div class="container-fluid">
        <!-- Widget Trạng thái Hàng đợi -->
        <div class="row mb-3">
            <div class="col-md-3 col-6 mb-2">
                <div class="small-box bg-light border border-primary shadow-sm mb-0 p-3">
                    <h4 class="text-primary font-weight-bold mb-1"><?=$queueStats['scheduled_count']?></h4>
                    <p class="text-muted mb-0 small font-weight-bold">Tổng Bài Đã Lên Lịch</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="small-box bg-light border border-warning shadow-sm mb-0 p-3">
                    <h4 class="text-warning font-weight-bold mb-1"><?=$queueStats['due_count']?></h4>
                    <p class="text-muted mb-0 small font-weight-bold">Đã Đến Hạn (Due / Ready)</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="small-box bg-light border border-info shadow-sm mb-0 p-3">
                    <h4 class="text-info font-weight-bold mb-1"><?=$queueStats['publishing_count']?></h4>
                    <p class="text-muted mb-0 small font-weight-bold">Đang Xử Lý (Publishing)</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="small-box bg-light border border-danger shadow-sm mb-0 p-3">
                    <h4 class="text-danger font-weight-bold mb-1"><?=$queueStats['failed_count']?></h4>
                    <p class="text-muted mb-0 small font-weight-bold">Lỗi Cần Xử Lý</p>
                </div>
            </div>
        </div>

        <div class="card card-outline card-warning shadow-sm">
            <div class="card-header p-2 d-flex justify-content-between align-items-center">
                <span class="font-weight-bold text-dark"><i class="fas fa-list mr-1"></i> Danh sách Bài đăng trong Hàng đợi (Queue)</span>
                <span class="badge badge-warning"><?=count($scheduledPosts)?> bài</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 60px;" class="text-center">ID</th>
                                <th>Tiêu đề / Sản phẩm</th>
                                <th style="width: 140px;" class="text-center">Tài khoản</th>
                                <th style="width: 130px;" class="text-center">Nền tảng</th>
                                <th style="width: 120px;" class="text-center">Phương thức</th>
                                <th style="width: 150px;" class="text-center">Giờ hẹn đăng</th>
                                <th style="width: 120px;" class="text-center">Trạng thái</th>
                                <th style="width: 140px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($scheduledPosts)): ?>
                                <?php foreach ($scheduledPosts as $p): ?>
                                    <tr>
                                        <td class="text-center font-weight-bold">#<?=$p['id']?></td>
                                        <td>
                                            <a href="index.php?com=publishing&act=view&id=<?=$p['id']?>" class="font-weight-bold text-primary"><?=htmlspecialchars($p['title'])?></a>
                                            <div class="text-muted small">SP: <?=htmlspecialchars($p['product_name'] ?? 'N/A')?></div>
                                        </td>
                                        <td class="text-center">
                                            <strong><?=htmlspecialchars($p['account_handle'] ?: '@fitnado.vn')?></strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-dark"><?=strtoupper($p['platform'])?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info"><?=strtoupper($p['provider'])?></span>
                                        </td>
                                        <td class="text-center small">
                                            <?php if (!empty($p['scheduled_at'])): ?>
                                                <div class="font-weight-bold"><?=date('H:i d/m/Y', $p['scheduled_at'])?></div>
                                                <?php if ($p['scheduled_at'] <= time()): ?>
                                                    <span class="badge badge-warning">Đã đến hạn</span>
                                                <?php else: ?>
                                                    <span class="text-muted">(Còn <?=round(($p['scheduled_at'] - time()) / 3600, 1)?> giờ)</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa hẹn</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary"><?=$p['status']?></span>
                                        </td>
                                        <td class="text-center">
                                            <a href="index.php?com=publishing&act=view&id=<?=$p['id']?>" class="btn btn-sm btn-primary shadow-sm">
                                                <i class="fas fa-eye mr-1"></i> Xem
                                            </a>
                                            <?php if ($p['status'] === 'FAILED'): ?>
                                                <a href="index.php?com=publishing&act=retry_failed&id=<?=$p['id']?>" class="btn btn-sm btn-warning shadow-sm">
                                                    <i class="fas fa-redo"></i> Thử lại
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-check-circle fa-3x mb-2 text-success" style="opacity: 0.5;"></i><br>
                                        Hàng đợi xuất bản đang trống. Không có bài đăng nào bị nghẽn!
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
