<?php
$linkMan = "index.php?com=affiliate&act=man";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item active">Thống kê Click Affiliate</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <!-- Stat boxes -->
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary"><i class="fas fa-mouse-pointer"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tổng lượt Click</span>
                    <span class="info-box-number"><?= number_format($countTotal, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
        <?php if (!empty($summaryPlatform)) {
            foreach ($summaryPlatform as $sumPlat) { ?>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info"><i class="fas fa-chart-line"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?= strtoupper($sumPlat['platform']) ?></span>
                            <span class="info-box-number"><?= number_format($sumPlat['total_clicks'], 0, ',', '.') ?> <small>clicks</small></span>
                        </div>
                    </div>
                </div>
        <?php } } ?>
    </div>

    <div class="card card-primary card-outline text-sm mb-0">
        <div class="card-header">
            <h3 class="card-title">Nhật ký chuyển hướng Affiliate (Click Logs)</h3>
            <div class="card-tools">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="keyword" placeholder="<?=timkiem?>" value="<?= (!empty($_GET['keyword'])) ? htmlspecialchars($_GET['keyword']) : '' ?>" onkeypress="if (event.keyCode == 13) onSearch();">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" onclick="onSearch()"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th class="align-middle text-center" width="5%">ID</th>
                        <th class="align-middle" width="30%">Sản phẩm</th>
                        <th class="align-middle text-center" width="15%">Nền tảng / Shop</th>
                        <th class="align-middle text-center" width="12%">Nguồn (Source)</th>
                        <th class="align-middle text-center" width="10%">Thiết bị</th>
                        <th class="align-middle text-center" width="15%">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)) { ?>
                        <?php foreach ($items as $v) { ?>
                            <tr>
                                <td class="align-middle text-center"><?= $v['id'] ?></td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($v['product_photo'])) { ?>
                                            <img src="<?= THUMBS ?>/50x50x1/<?= UPLOAD_PRODUCT_L . $v['product_photo'] ?>" class="rounded mr-2" style="width:40px;height:40px;object-fit:cover;">
                                        <?php } ?>
                                        <div>
                                            <strong class="text-primary"><?= htmlspecialchars($v['product_name'] ?: 'Sản phẩm #' . $v['id_product']) ?></strong>
                                            <?php if ($v['offer_price'] > 0) { ?>
                                                <div class="text-xs text-muted">Giá sàn: <?= number_format($v['offer_price'], 0, ',', '.') ?>đ</div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-primary px-2 py-1"><?= strtoupper($v['platform']) ?></span>
                                    <?php if (!empty($v['seller_name'])) { ?>
                                        <div class="text-xs text-muted mt-1"><?= htmlspecialchars($v['seller_name']) ?></div>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-secondary"><?= htmlspecialchars($v['source_page']) ?></span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-light border"><i class="fas fa-<?= ($v['device_type'] == 'mobile') ? 'mobile-alt' : 'desktop' ?> mr-1"></i><?= ucfirst($v['device_type']) ?></span>
                                </td>
                                <td class="align-middle text-center text-sm text-muted">
                                    <?= date('d/m/Y H:i:s', $v['date_created']) ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu lượt click affiliate nào.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-sm pb-0">
            <?= (!empty($paging)) ? $paging : '' ?>
        </div>
    </div>
</section>

<script>
    function onSearch() {
        var keyword = document.getElementById('keyword').value;
        window.location.href = "index.php?com=affiliate&act=man&keyword=" + encodeURIComponent(keyword);
    }
</script>
