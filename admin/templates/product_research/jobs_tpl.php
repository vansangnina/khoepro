<?php
$linkJobs = "index.php?com=product_research&act=jobs";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="index.php?com=product_research&act=man">Nghiên cứu sản phẩm</a></li>
                <li class="breadcrumb-item active">Hàng đợi Tác vụ Nghiên cứu (Research Jobs Queue)</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <!-- Stat Summary Boxes -->
    <div class="row mb-3">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-secondary"><i class="fas fa-tasks"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tổng tác vụ</span>
                    <span class="info-box-number"><?= number_format($jobStats['total'] ?? 0) ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Đang chờ (Pending)</span>
                    <span class="info-box-number"><?= number_format($jobStats['pending'] ?? 0) ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Thành công (Success)</span>
                    <span class="info-box-number"><?= number_format($jobStats['success'] ?? 0) ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Thất bại (Failed)</span>
                    <span class="info-box-number"><?= number_format($jobStats['failed'] ?? 0) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3 mb-2 mb-md-0">
                    <select class="form-control form-control-sm" id="filter_job_status" onchange="applyJobFilter()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="PENDING" <?= ($_GET['status'] ?? '') == 'PENDING' ? 'selected' : '' ?>>PENDING (Đang chờ)</option>
                        <option value="RUNNING" <?= ($_GET['status'] ?? '') == 'RUNNING' ? 'selected' : '' ?>>RUNNING (Đang chạy)</option>
                        <option value="SUCCESS" <?= ($_GET['status'] ?? '') == 'SUCCESS' ? 'selected' : '' ?>>SUCCESS (Thành công)</option>
                        <option value="FAILED" <?= ($_GET['status'] ?? '') == 'FAILED' ? 'selected' : '' ?>>FAILED (Thất bại)</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select class="form-control form-control-sm" id="filter_job_provider" onchange="applyJobFilter()">
                        <option value="">-- Tất cả Nhà cung cấp --</option>
                        <option value="ai_agent" <?= ($_GET['provider'] ?? '') == 'ai_agent' ? 'selected' : '' ?>>AI Research Agent</option>
                        <option value="tiktok" <?= ($_GET['provider'] ?? '') == 'tiktok' ? 'selected' : '' ?>>TikTok Platform</option>
                        <option value="shopee" <?= ($_GET['provider'] ?? '') == 'shopee' ? 'selected' : '' ?>>Shopee Platform</option>
                        <option value="lazada" <?= ($_GET['provider'] ?? '') == 'lazada' ? 'selected' : '' ?>>Lazada Platform</option>
                        <option value="csv" <?= ($_GET['provider'] ?? '') == 'csv' ? 'selected' : '' ?>>CSV Import</option>
                    </select>
                </div>
                <div class="col-md-6 text-right">
                    <a href="index.php?com=product_research&act=seeds" class="btn btn-sm btn-outline-primary mr-1"><i class="fas fa-seedling mr-1"></i>Quản lý Seeds</a>
                    <a href="index.php?com=product_research&act=provider_config" class="btn btn-sm btn-outline-secondary"><i class="fas fa-cog mr-1"></i>Cấu hình AI & API</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs Table -->
    <div class="card card-primary card-outline text-sm shadow-sm mb-0">
        <div class="card-header py-2">
            <h3 class="card-title font-weight-bold"><i class="fas fa-tasks mr-1"></i> Nhật ký Hàng đợi Tác vụ Nghiên cứu</h3>
            <div class="card-tools">
                <span class="badge badge-secondary"><?= number_format($countTotal) ?> jobs</span>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped align-middle mb-0" style="width: 100%; table-layout: auto;">
                <thead class="thead-light">
                    <tr class="text-center" style="font-size: 13px;">
                        <th style="width: 50px;">ID</th>
                        <th class="text-left" style="min-width: 220px;">Hạt giống / Từ khóa</th>
                        <th style="width: 110px;">Nhà cung cấp</th>
                        <th style="width: 110px;">Trạng thái</th>
                        <th style="width: 80px;">Số lần thử</th>
                        <th class="text-left" style="min-width: 230px;">Kết quả & Tóm tắt</th>
                        <th style="width: 120px;">Thời gian</th>
                        <th style="width: 90px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)) { ?>
                        <?php foreach ($items as $v) {
                            $statusBadge = 'badge-secondary';
                            if ($v['status'] == 'SUCCESS') $statusBadge = 'badge-success';
                            elseif ($v['status'] == 'RUNNING') $statusBadge = 'badge-primary';
                            elseif ($v['status'] == 'FAILED') $statusBadge = 'badge-danger';
                            elseif ($v['status'] == 'PENDING') $statusBadge = 'badge-info';
                        ?>
                            <tr>
                                <td class="align-middle text-center font-weight-bold text-muted">#<?= $v['id'] ?></td>
                                <td class="align-middle">
                                    <?php if (!empty($v['seed_title'])) { ?>
                                        <div class="font-weight-bold text-dark" style="word-break: break-word;"><?= htmlspecialchars($v['seed_title']) ?></div>
                                        <div class="text-xs text-muted mt-1" style="word-break: break-word;"><i class="fas fa-key mr-1 text-secondary"></i><?= htmlspecialchars($v['seed_keyword']) ?></div>
                                    <?php } else { ?>
                                        <span class="text-muted font-italic">Quét trực tiếp không dùng seed</span>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-light border text-uppercase font-weight-bold px-2 py-1"><?= $v['provider'] ?></span>
                                    <div class="text-xs text-muted mt-1"><?= $v['depth'] ?></div>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge <?= $statusBadge ?> p-1 text-wrap d-block"><?= $v['status'] ?></span>
                                </td>
                                <td class="align-middle text-center text-xs">
                                    <span class="badge badge-light border"><?= $v['attempts'] ?> / <?= $v['max_attempts'] ?></span>
                                </td>
                                <td class="align-middle text-xs" style="word-break: break-word;">
                                    <?php if ($v['status'] == 'SUCCESS') { ?>
                                        <div class="text-success font-weight-bold" style="word-break: break-word;"><i class="fas fa-check-circle mr-1"></i><?= htmlspecialchars($v['result_summary'] ?: 'Hoàn thành') ?></div>
                                        <div class="text-muted mt-1">Tìm: <strong><?= $v['candidates_found'] ?></strong> | Mới: <strong class="text-primary"><?= $v['candidates_created'] ?></strong> | Trùng: <strong><?= $v['duplicates_count'] ?></strong></div>
                                    <?php } elseif ($v['status'] == 'FAILED') { ?>
                                        <div class="text-danger font-weight-bold"><i class="fas fa-times-circle mr-1"></i>Thất bại:</div>
                                        <div class="text-danger mt-1" style="word-break: break-word;"><?= htmlspecialchars($v['error_message'] ?? 'Lỗi không xác định') ?></div>
                                    <?php } else { ?>
                                        <span class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i>Đang xếp hàng thực thi...</span>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-center text-muted" style="font-size: 11px; line-height: 1.25;">
                                    <?= date('d/m/Y', $v['date_created']) ?><br>
                                    <span class="text-xs"><?= date('H:i:s', $v['date_created']) ?></span>
                                    <?php if ($v['duration'] > 0) { ?>
                                        <div class="text-info font-weight-bold mt-1"><?= $v['duration'] ?>s</div>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?php if ($v['status'] == 'FAILED' || $v['status'] == 'RUNNING') { ?>
                                        <a href="index.php?com=product_research&act=retry_job&id=<?= $v['id'] ?>" class="btn btn-xs btn-outline-danger font-weight-bold" onclick="return confirm('Chạy lại tác vụ này ngay?')" title="Thử lại (Retry)">
                                            <i class="fas fa-redo mr-1"></i> Thử lại
                                        </a>
                                    <?php } else { ?>
                                        <span class="text-muted text-xs"><i class="fas fa-check text-success mr-1"></i> Xong</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-tasks fa-2x mb-2 text-muted" style="opacity: 0.5;"></i>
                                <h5>Hàng đợi trống</h5>
                                <p class="mb-0">Chưa có tác vụ nghiên cứu nào trong hàng đợi.</p>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-sm pb-0 py-2">
            <?= (!empty($paging)) ? $paging : '' ?>
        </div>
    </div>
</section>

<script>
function applyJobFilter() {
    var status = document.getElementById('filter_job_status').value;
    var provider = document.getElementById('filter_job_provider').value;

    var url = "index.php?com=product_research&act=jobs";
    if (status) url += "&status=" + encodeURIComponent(status);
    if (provider) url += "&provider=" + encodeURIComponent(provider);

    window.location.href = url;
}
</script>
