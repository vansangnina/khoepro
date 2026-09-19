<?php
$linkMan = "index.php?com=ai_content&act=man";
$linkView = "index.php?com=ai_content&act=view";
$linkGenerate = "index.php?com=ai_content&act=generate";
$linkDelete = "index.php?com=ai_content&act=delete";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item active">Kho Nội Dung AI (AI Content Library)</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <!-- Stat Widgets -->
    <div class="row mb-3">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3><?= $stats['total'] ?></h3>
                    <p class="font-weight-bold">Tổng số nội dung AI</p>
                </div>
                <div class="icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3><?= $stats['review_required'] ?></h3>
                    <p class="font-weight-bold">Chờ biên tập duyệt</p>
                </div>
                <div class="icon"><i class="fas fa-user-check"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3><?= $stats['approved'] ?></h3>
                    <p class="font-weight-bold">Đã duyệt (Active)</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple shadow-sm">
                <div class="inner">
                    <h3><?= $stats['applied'] ?></h3>
                    <p class="font-weight-bold">Đã áp dụng vào Web</p>
                </div>
                <div class="icon"><i class="fas fa-upload"></i></div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-primary card-outline shadow-sm text-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold"><i class="fas fa-filter mr-1"></i> Bộ lọc & Tìm kiếm Nội dung</h3>
            <div class="card-tools">
                <a href="<?= $linkGenerate ?>" class="btn btn-sm btn-success"><i class="fas fa-magic mr-1"></i> Tạo nội dung AI mới</a>
            </div>
        </div>
        <div class="card-body">
            <form method="get" action="index.php" class="form-row align-items-center">
                <input type="hidden" name="com" value="ai_content">
                <input type="hidden" name="act" value="man">

                <!-- Product Filter -->
                <div class="col-md-4 col-sm-6 mb-2">
                    <select name="id_product" class="form-control select2">
                        <option value="">-- Tất cả sản phẩm --</option>
                        <?php foreach ($productsList as $p) { ?>
                            <option value="<?= $p['id'] ?>" <?= (!empty($_GET['id_product']) && $_GET['id_product'] == $p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['namevi']) ?> (<?= $p['code'] ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Content Type Filter -->
                <div class="col-md-3 col-sm-6 mb-2">
                    <select name="content_type" class="form-control">
                        <option value="">-- Tất cả loại nội dung --</option>
                        <option value="product_analysis" <?= (!empty($_GET['content_type']) && $_GET['content_type'] == 'product_analysis') ? 'selected' : '' ?>>Phân tích sản phẩm (12 khía cạnh)</option>
                        <option value="tiktok_hooks" <?= (!empty($_GET['content_type']) && $_GET['content_type'] == 'tiktok_hooks') ? 'selected' : '' ?>>7 Biến thể TikTok Hooks</option>
                        <option value="tiktok_script" <?= (!empty($_GET['content_type']) && $_GET['content_type'] == 'tiktok_script') ? 'selected' : '' ?>>Kịch bản TikTok + Shot Plan</option>
                        <option value="seo_content" <?= (!empty($_GET['content_type']) && $_GET['content_type'] == 'seo_content') ? 'selected' : '' ?>>SEO Metadata & FAQs</option>
                        <option value="review_draft" <?= (!empty($_GET['content_type']) && $_GET['content_type'] == 'review_draft') ? 'selected' : '' ?>>Bản thảo bài đánh giá</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-2 col-sm-6 mb-2">
                    <select name="status" class="form-control">
                        <option value="">-- Trạng thái --</option>
                        <option value="REVIEW_REQUIRED" <?= (!empty($_GET['status']) && $_GET['status'] == 'REVIEW_REQUIRED') ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="APPROVED" <?= (!empty($_GET['status']) && $_GET['status'] == 'APPROVED') ? 'selected' : '' ?>>Đã duyệt</option>
                        <option value="APPLIED" <?= (!empty($_GET['status']) && $_GET['status'] == 'APPLIED') ? 'selected' : '' ?>>Đã áp dụng</option>
                        <option value="REJECTED" <?= (!empty($_GET['status']) && $_GET['status'] == 'REJECTED') ? 'selected' : '' ?>>Bị từ chối</option>
                    </select>
                </div>

                <!-- Search box -->
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tiêu đề..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                            <a href="<?= $linkMan ?>" class="btn btn-secondary" title="Reset"><i class="fas fa-redo"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card card-outline card-info shadow-sm text-sm">
        <div class="card-header py-2">
            <h3 class="card-title font-weight-bold"><i class="fas fa-list mr-1"></i> Danh sách Gói Nội dung AI (<?= count($items) ?> mục)</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped align-middle mb-0" style="width: 100%; table-layout: auto;">
                <thead class="thead-light">
                    <tr class="text-center" style="font-size: 13px;">
                        <th style="width: 50px;">ID</th>
                        <th class="text-left" style="min-width: 200px;">Sản phẩm</th>
                        <th class="text-left" style="min-width: 250px;">Loại nội dung & Tiêu đề</th>
                        <th style="width: 70px;">Version</th>
                        <th style="width: 70px;">Active</th>
                        <th style="width: 130px;">Trạng thái</th>
                        <th style="width: 110px;">Model / Token</th>
                        <th style="width: 100px;">Cập nhật</th>
                        <th style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)) {
                        foreach ($items as $k => $v) {
                            $typeBadges = array(
                                'product_analysis' => '<span class="badge badge-info"><i class="fas fa-brain mr-1"></i> Phân tích 12 khía cạnh</span>',
                                'tiktok_hooks' => '<span class="badge badge-danger"><i class="fab fa-tiktok mr-1"></i> 7 TikTok Hooks</span>',
                                'tiktok_script' => '<span class="badge badge-dark"><i class="fas fa-video mr-1"></i> Kịch bản ' . ($v['target_duration'] ? $v['target_duration'] . 's' : 'TikTok') . '</span>',
                                'seo_content' => '<span class="badge badge-success"><i class="fas fa-search mr-1"></i> SEO & FAQs</span>',
                                'review_draft' => '<span class="badge badge-primary"><i class="fas fa-pen-nib mr-1"></i> Review Draft</span>'
                            );
                            $typeBadge = $typeBadges[$v['content_type']] ?? '<span class="badge badge-secondary">' . $v['content_type'] . '</span>';

                            $statusBadges = array(
                                'REVIEW_REQUIRED' => '<span class="badge badge-warning p-1 text-wrap d-block"><i class="fas fa-clock mr-1"></i> Chờ duyệt</span>',
                                'APPROVED' => '<span class="badge badge-success p-1 text-wrap d-block"><i class="fas fa-check mr-1"></i> Đã duyệt</span>',
                                'APPLIED' => '<span class="badge badge-purple p-1 text-wrap d-block"><i class="fas fa-check-double mr-1"></i> Đã áp dụng</span>',
                                'REJECTED' => '<span class="badge badge-danger p-1 text-wrap d-block"><i class="fas fa-times mr-1"></i> Từ chối</span>'
                            );
                            $statusBadge = $statusBadges[$v['status']] ?? '<span class="badge badge-secondary p-1 text-wrap d-block">' . $v['status'] . '</span>';
                            if ($v['is_outdated']) {
                                $statusBadge .= '<span class="badge badge-warning text-xs mt-1 d-block" title="Dữ liệu nghiên cứu đã thay đổi"><i class="fas fa-exclamation-triangle"></i> Cũ hơn dữ liệu</span>';
                            }
                    ?>
                        <tr>
                            <td class="text-center align-middle font-weight-bold text-muted">#<?= $v['id'] ?></td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-primary" style="word-break: break-word; line-height: 1.35;">
                                    <a href="index.php?com=product&act=edit&type=san-pham&id=<?= $v['id_product'] ?>" target="_blank">
                                        <?= htmlspecialchars($v['product_name'] ?? 'Sản phẩm #' . $v['id_product']) ?>
                                    </a>
                                </div>
                                <div class="text-xs text-muted mt-1">SKU: <?= $v['product_code'] ?? 'N/A' ?></div>
                            </td>
                            <td class="align-middle">
                                <div class="mb-1"><?= $typeBadge ?></div>
                                <div class="font-weight-bold text-dark" style="word-break: break-word; line-height: 1.35;"><?= htmlspecialchars($v['title']) ?></div>
                                <?php if (!empty($v['content_angle'])) { ?>
                                    <span class="badge badge-light border text-xs mt-1">Góc: <?= htmlspecialchars($v['content_angle']) ?></span>
                                <?php } ?>
                            </td>
                            <td class="text-center align-middle font-weight-bold">
                                <span class="badge badge-secondary">v<?= $v['version'] ?></span>
                            </td>
                            <td class="text-center align-middle">
                                <?= ($v['is_active']) ? '<span class="badge badge-success"><i class="fas fa-star text-warning"></i> Active</span>' : '<span class="text-muted text-xs">-</span>' ?>
                            </td>
                            <td class="text-center align-middle"><?= $statusBadge ?></td>
                            <td class="text-center align-middle text-xs">
                                <div><i class="fas fa-microchip mr-1"></i><?= htmlspecialchars($v['model'] ?? $v['provider']) ?></div>
                                <div class="text-muted text-xs mt-1"><?= htmlspecialchars($v['prompt_version']) ?></div>
                            </td>
                            <td class="text-center align-middle text-muted" style="font-size: 11px; line-height: 1.25;">
                                <?= date('d/m/Y', $v['date_created']) ?><br>
                                <span class="text-xs"><?= date('H:i', $v['date_created']) ?></span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-inline-flex flex-wrap justify-content-center" style="gap: 3px;">
                                    <a href="<?= $linkView ?>&id=<?= $v['id'] ?>" class="btn btn-xs btn-info" title="Xem & Kiểm duyệt"><i class="fas fa-eye"></i></a>
                                    <?php if ($v['status'] === 'APPROVED') { ?>
                                        <a href="index.php?com=ai_content&act=diff&id=<?= $v['id'] ?>" class="btn btn-xs btn-success" title="So sánh & Áp dụng"><i class="fas fa-share-square"></i></a>
                                    <?php } ?>
                                    <a href="<?= $linkDelete ?>&id=<?= $v['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Bạn có chắc muốn xóa bản ghi nội dung này?')" title="Xóa"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php }
                    } else { ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-2 text-secondary d-block" style="opacity: 0.5;"></i>
                                <h5>Chưa có nội dung AI nào trong thư viện</h5>
                                <p class="mb-0">Hãy bấm <strong>"Tạo nội dung AI mới"</strong> để bắt đầu!</p>
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
