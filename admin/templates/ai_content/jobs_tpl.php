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

        <div class="card-body p-0">
            <table class="table table-hover table-striped align-middle mb-0" style="width: 100%; table-layout: auto;">
                <thead class="thead-light">
                    <tr class="text-center" style="font-size: 13px;">
                        <th style="width: 60px;">#Job ID</th>
                        <th class="text-left" style="min-width: 200px;">Sản phẩm</th>
                        <th class="text-left" style="min-width: 250px;">Các loại nội dung yêu cầu</th>
                        <th style="width: 130px;">Trạng thái</th>
                        <th style="width: 80px;">Thử lại</th>
                        <th style="width: 90px;">Thời gian</th>
                        <th style="width: 130px;">Thời điểm tạo</th>
                        <th style="width: 90px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($jobs)) {
                        foreach ($jobs as $j) {
                            $statusMap = array(
                                'PENDING' => '<span class="badge badge-warning p-1 text-wrap d-block"><i class="fas fa-hourglass-start mr-1"></i> Đang chờ</span>',
                                'RUNNING' => '<span class="badge badge-info p-1 text-wrap d-block"><i class="fas fa-spinner fa-spin mr-1"></i> Đang xử lý</span>',
                                'SUCCESS' => '<span class="badge badge-success p-1 text-wrap d-block"><i class="fas fa-check-circle mr-1"></i> Hoàn thành</span>',
                                'FAILED' => '<span class="badge badge-danger p-1 text-wrap d-block"><i class="fas fa-times-circle mr-1"></i> Thất bại</span>'
                            );
                            $badge = $statusMap[$j['status']] ?? '<span class="badge badge-secondary p-1 text-wrap d-block">' . $j['status'] . '</span>';
                    ?>
                        <tr>
                            <td class="text-center align-middle font-weight-bold text-muted">#<?= $j['id'] ?></td>
                            <td class="align-middle">
                                <a href="index.php?com=product&act=edit&type=san-pham&id=<?= $j['id_product'] ?>" target="_blank" class="font-weight-bold text-primary" style="word-break: break-word;">
                                    <?= htmlspecialchars($j['product_name'] ?? 'Sản phẩm #' . $j['id_product']) ?>
                                </a>
                            </td>
                            <td class="align-middle text-xs" style="word-break: break-word;">
                                <div class="font-weight-bold text-dark"><?= htmlspecialchars(str_replace(',', ' | ', $j['content_types'])) ?></div>
                                <?php if (!empty($j['error_message'])) { ?>
                                    <div class="text-danger mt-1" style="word-break: break-word;"><i class="fas fa-exclamation-triangle mr-1"></i> <?= htmlspecialchars($j['error_message']) ?></div>
                                <?php } ?>
                            </td>
                            <td class="text-center align-middle"><?= $badge ?></td>
                            <td class="text-center align-middle text-xs">
                                <span class="badge badge-light border"><?= $j['attempts'] ?> / <?= $j['max_attempts'] ?></span>
                            </td>
                            <td class="text-center align-middle text-xs font-weight-bold">
                                <?= ($j['duration']) ? $j['duration'] . 's' : '-' ?>
                            </td>
                            <td class="text-center align-middle text-muted" style="font-size: 11px; line-height: 1.25;">
                                <?= date('d/m/Y', $j['date_created']) ?><br>
                                <span class="text-xs"><?= date('H:i:s', $j['date_created']) ?></span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-inline-flex flex-wrap justify-content-center" style="gap: 3px;">
                                    <?php if ($j['status'] === 'FAILED' || $j['status'] === 'PENDING') { ?>
                                        <a href="<?= $linkRetry ?>&id=<?= $j['id'] ?>" class="btn btn-xs btn-warning" title="Chạy ngay / Thử lại"><i class="fas fa-play"></i></a>
                                    <?php } ?>
                                    <a href="<?= $linkDelete ?>&id=<?= $j['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Xóa Job #<?= $j['id'] ?> khỏi hàng đợi?')" title="Xóa"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php }
                    } else { ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-check-double fa-3x mb-2 text-success" style="opacity: 0.5;"></i><br>
                                <h5>Hàng đợi hiện đang trống!</h5>
                                <p class="mb-0">Không có tác vụ nào đang chờ xử lý.</p>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($paging)) { ?>
            <div class="card-footer clearfix py-2">
                <?= $paging ?>
            </div>
        <?php } ?>
    </div>
</section>
