<?php
$linkJobs = "index.php?com=ai_content&act=jobs";
$linkGenerate = "index.php?com=ai_content&act=generate";
$linkRetry = "index.php?com=ai_content&act=job_retry";
$linkDelete = "index.php?com=ai_content&act=job_delete";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="index.php?com=ai_content&act=man">Kho nội dung AI</a></li>
                <li class="breadcrumb-item active">Hàng Đợi Content Jobs (Queue Monitor)</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="card card-outline card-warning shadow-sm text-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-tasks mr-1"></i> Giám Sát Hàng Đợi Tác Vụ Sinh Nội Dung Nền (Background Content Queue)
            </h3>
            <div class="card-tools">
                <a href="<?= $linkGenerate ?>" class="btn btn-sm btn-success mr-2"><i class="fas fa-plus mr-1"></i> Tạo Job mới</a>
                <a href="<?= $linkJobs ?>" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt mr-1"></i> Làm mới</a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped text-nowrap">
                <thead>
                    <tr class="text-center bg-light">
                        <th style="width: 50px;">#Job ID</th>
                        <th class="text-left" style="width: 250px;">Sản phẩm</th>
                        <th class="text-left">Các loại nội dung yêu cầu</th>
                        <th>Trạng thái</th>
                        <th>Thử lại</th>
                        <th>Thời gian chạy</th>
                        <th>Thời điểm tạo</th>
                        <th style="width: 120px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($jobs)) {
                        foreach ($jobs as $j) {
                            $statusMap = array(
                                'PENDING' => '<span class="badge badge-warning p-1"><i class="fas fa-hourglass-start mr-1"></i> Đang chờ (Pending)</span>',
                                'RUNNING' => '<span class="badge badge-info p-1"><i class="fas fa-spinner fa-spin mr-1"></i> Đang xử lý (Running)</span>',
                                'SUCCESS' => '<span class="badge badge-success p-1"><i class="fas fa-check-circle mr-1"></i> Hoàn thành (Success)</span>',
                                'FAILED' => '<span class="badge badge-danger p-1"><i class="fas fa-times-circle mr-1"></i> Thất bại (Failed)</span>'
                            );
                            $badge = $statusMap[$j['status']] ?? '<span class="badge badge-secondary">' . $j['status'] . '</span>';
                    ?>
                        <tr>
                            <td class="text-center font-weight-bold text-muted">#<?= $j['id'] ?></td>
                            <td class="text-left font-weight-bold text-wrap" style="max-width: 250px;">
                                <a href="index.php?com=product&act=edit&type=san-pham&id=<?= $j['id_product'] ?>" target="_blank" class="text-primary">
                                    <?= htmlspecialchars($j['product_name'] ?? 'Sản phẩm #' . $j['id_product']) ?>
                                </a>
                            </td>
                            <td class="text-left text-wrap text-xs" style="max-width: 320px;">
                                <?= htmlspecialchars(str_replace(',', ' | ', $j['content_types'])) ?>
                                <?php if (!empty($j['error_message'])) { ?>
                                    <div class="text-danger mt-1"><i class="fas fa-exclamation-triangle mr-1"></i> <?= htmlspecialchars($j['error_message']) ?></div>
                                <?php } ?>
                            </td>
                            <td class="text-center"><?= $badge ?></td>
                            <td class="text-center text-xs">
                                <span class="badge badge-light border"><?= $j['attempts'] ?> / <?= $j['max_attempts'] ?></span>
                            </td>
                            <td class="text-center text-xs font-weight-bold">
                                <?= ($j['duration']) ? $j['duration'] . 's' : '-' ?>
                            </td>
                            <td class="text-center text-xs text-muted">
                                <?= date('d/m/Y H:i:s', $j['date_created']) ?>
                            </td>
                            <td class="text-center">
                                <?php if ($j['status'] === 'FAILED' || $j['status'] === 'PENDING') { ?>
                                    <a href="<?= $linkRetry ?>&id=<?= $j['id'] ?>" class="btn btn-sm btn-warning" title="Chạy ngay / Thử lại"><i class="fas fa-play"></i></a>
                                <?php } ?>
                                <a href="<?= $linkDelete ?>&id=<?= $j['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa Job #<?= $j['id'] ?> khỏi hàng đợi?')" title="Xóa"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php }
                    } else { ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-check-double fa-3x mb-2 text-success"></i><br>
                                Hàng đợi hiện đang trống! Không có tác vụ nào đang chờ.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($paging)) { ?>
            <div class="card-footer clearfix">
                <?= $paging ?>
            </div>
        <?php } ?>
    </div>
</section>
