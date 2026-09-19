<?php
$linkMan = "index.php?com=ai_video&act=man";
$linkView = "index.php?com=ai_video&act=view";
$linkCreate = "index.php?com=ai_video&act=create";
$linkJobs = "index.php?com=ai_video&act=jobs";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.25rem;">
                    <i class="fas fa-video mr-2 text-primary"></i>Dự án Video AI (AI Video Production)
                </h1>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkCreate?>" class="btn btn-sm btn-success mr-2 shadow-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Tạo Dự án Video
                </a>
                <a href="<?=$linkJobs?>" class="btn btn-sm btn-outline-info shadow-sm">
                    <i class="fas fa-tasks mr-1"></i> Hàng đợi Render
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Widgets Thống kê -->
<section class="content mb-2 text-sm">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-2 col-lg-4 col-sm-6 col-12 mb-3">
                <div class="small-box bg-light border shadow-sm mb-0 h-100">
                    <div class="inner p-3">
                        <h3 class="mb-1" style="font-size: 1.75rem;"><?=$stats['total']?></h3>
                        <p class="text-muted mb-0 font-weight-bold">Tổng Video Projects</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-film text-secondary" style="opacity: 0.3;"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-sm-6 col-12 mb-3">
                <div class="small-box bg-light border border-info shadow-sm mb-0 h-100">
                    <div class="inner p-3">
                        <h3 class="text-info mb-1" style="font-size: 1.75rem;"><?=$stats['ready']?></h3>
                        <p class="text-muted mb-0 font-weight-bold">Sẵn sàng Render</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-play-circle text-info" style="opacity: 0.3;"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-sm-6 col-12 mb-3">
                <div class="small-box bg-light border border-warning shadow-sm mb-0 h-100">
                    <div class="inner p-3">
                        <h3 class="text-warning mb-1" style="font-size: 1.75rem;"><?=$stats['review_required']?></h3>
                        <p class="text-muted mb-0 font-weight-bold">Chờ duyệt (Review)</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-clock text-warning" style="opacity: 0.3;"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-sm-6 col-12 mb-3">
                <div class="small-box bg-light border border-success shadow-sm mb-0 h-100">
                    <div class="inner p-3">
                        <h3 class="text-success mb-1" style="font-size: 1.75rem;"><?=$stats['approved']?></h3>
                        <p class="text-muted mb-0 font-weight-bold">Đã phê duyệt</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-check-double text-success" style="opacity: 0.3;"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-sm-6 col-12 mb-3">
                <div class="small-box bg-light border border-danger shadow-sm mb-0 h-100">
                    <div class="inner p-3">
                        <h3 class="text-danger mb-1" style="font-size: 1.75rem;"><?=$stats['outdated']?></h3>
                        <p class="text-muted mb-0 font-weight-bold">Kịch bản Lỗi thời</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-exclamation-triangle text-danger" style="opacity: 0.3;"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-sm-6 col-12 mb-3">
                <div class="small-box bg-light border border-primary shadow-sm mb-0 h-100">
                    <div class="inner p-3">
                        <h3 class="text-primary mb-1" style="font-size: 1.75rem;"><?=$stats['processing']?></h3>
                        <p class="text-muted mb-0 font-weight-bold">Đang Render</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-spinner fa-spin text-primary" style="opacity: 0.3;"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bộ lọc & Danh sách -->
<section class="content text-sm">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header py-2">
                <form action="index.php" method="GET" class="form-row align-items-center">
                    <input type="hidden" name="com" value="ai_video">
                    <input type="hidden" name="act" value="man">

                    <div class="col-lg-3 col-md-6 col-12 mb-2 mb-lg-0">
                        <div class="input-group input-group-sm">
                            <input type="text" name="keyword" class="form-control" placeholder="Tìm tiêu đề, sản phẩm..." value="<?=htmlspecialchars($_GET['keyword'] ?? '')?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-12 mb-2 mb-lg-0">
                        <select name="id_product" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">-- Tất cả sản phẩm --</option>
                            <?php if (!empty($productsList)) foreach ($productsList as $p): ?>
                                <option value="<?=$p['id']?>" <?=(isset($_GET['id_product']) && $_GET['id_product'] == $p['id']) ? 'selected' : ''?>><?=htmlspecialchars($p['namevi'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12 mb-2 mb-lg-0">
                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="READY" <?=(isset($_GET['status']) && $_GET['status'] == 'READY') ? 'selected' : ''?>>Sẵn sàng Render (READY)</option>
                            <option value="WAITING_ASSET" <?=(isset($_GET['status']) && $_GET['status'] == 'WAITING_ASSET') ? 'selected' : ''?>>Thiếu tài nguyên (WAITING_ASSET)</option>
                            <option value="PROCESSING" <?=(isset($_GET['status']) && $_GET['status'] == 'PROCESSING') ? 'selected' : ''?>>Đang Render (PROCESSING)</option>
                            <option value="REVIEW_REQUIRED" <?=(isset($_GET['status']) && $_GET['status'] == 'REVIEW_REQUIRED') ? 'selected' : ''?>>Chờ Admin duyệt (REVIEW_REQUIRED)</option>
                            <option value="APPROVED" <?=(isset($_GET['status']) && $_GET['status'] == 'APPROVED') ? 'selected' : ''?>>Đã duyệt (APPROVED)</option>
                            <option value="REJECTED" <?=(isset($_GET['status']) && $_GET['status'] == 'REJECTED') ? 'selected' : ''?>>Từ chối (REJECTED)</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 col-12 text-lg-right">
                        <a href="<?=$linkMan?>" class="btn btn-sm btn-secondary btn-block"><i class="fas fa-undo mr-1"></i> Xóa lọc</a>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <table class="table table-hover table-striped align-middle mb-0" style="width: 100%; table-layout: auto;">
                    <thead class="thead-light">
                        <tr class="text-center" style="font-size: 13px;">
                            <th style="width: 50px;">ID</th>
                            <th style="width: 85px;">Thumbnail</th>
                            <th class="text-left" style="min-width: 220px;">Dự án Video & Sản phẩm</th>
                            <th style="width: 110px;">Định dạng</th>
                            <th style="width: 100px;">Provider</th>
                            <th style="width: 70px;">Version</th>
                            <th style="width: 140px;">Trạng thái</th>
                            <th style="width: 105px;">Cập nhật</th>
                            <th style="width: 110px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): foreach ($items as $item): ?>
                            <tr>
                                <td class="text-center align-middle">
                                    <span class="badge badge-light border text-muted">#<?=$item['id']?></span>
                                </td>
                                <td class="text-center align-middle" style="width: 85px;">
                                    <?php if (!empty($item['video_file']) && file_exists($item['video_file'])): ?>
                                        <a href="<?=$linkView?>&id=<?=$item['id']?>" class="d-inline-block shadow-sm rounded overflow-hidden" title="Xem video preview">
                                            <div class="position-relative bg-dark text-center rounded" style="width: 65px; height: 90px; overflow: hidden;">
                                                <i class="fas fa-play-circle fa-2x text-white position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.9;"></i>
                                                <span class="badge badge-dark position-absolute" style="bottom: 2px; right: 2px; font-size: 9px; padding: 2px 4px;">
                                                    <?=$item['duration_actual'] ? round($item['duration_actual']).'s' : $item['target_duration'].'s'?>
                                                </span>
                                            </div>
                                        </a>
                                    <?php elseif (!empty($item['product_photo']) && file_exists('upload/product/'.$item['product_photo'])): ?>
                                        <a href="<?=$linkView?>&id=<?=$item['id']?>">
                                            <img src="upload/product/<?=$item['product_photo']?>" class="img-thumbnail rounded shadow-sm" style="width: 65px; height: 65px; object-fit: cover;">
                                        </a>
                                    <?php else: ?>
                                        <div class="bg-light border text-center p-2 rounded d-inline-flex align-items-center justify-content-center" style="width: 65px; height: 65px;">
                                            <i class="fas fa-film fa-2x text-secondary"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <div class="font-weight-bold" style="word-break: break-word; line-height: 1.35;">
                                        <a href="<?=$linkView?>&id=<?=$item['id']?>" class="text-primary" style="font-size: 14px;">
                                            <?=htmlspecialchars($item['title'] ?: 'Video Project #'.$item['id'])?>
                                        </a>
                                    </div>
                                    <div class="text-muted small mt-1" style="word-break: break-word; line-height: 1.3;">
                                        <i class="fas fa-box text-secondary mr-1"></i><?=htmlspecialchars($item['product_name'] ?: 'N/A')?>
                                    </div>
                                    <?php if (!empty($item['is_outdated'])): ?>
                                        <span class="badge badge-danger mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>Kịch bản gốc đã thay đổi</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <div>
                                        <span class="badge badge-info"><i class="fas fa-mobile-alt mr-1"></i><?=$item['aspect_ratio']?></span>
                                        <span class="badge badge-secondary"><?=$item['target_duration']?>s</span>
                                    </div>
                                    <div class="text-muted text-xs mt-1" style="word-break: break-word;"><?=$item['template_id']?></div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-light border text-uppercase font-weight-bold"><?=$item['provider']?></span>
                                    <?php if (!empty($item['cost_estimate'])): ?>
                                        <div class="text-muted text-xs mt-1">$<?=number_format($item['cost_estimate'], 3)?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-secondary font-weight-bold">v<?=$item['version']?></span>
                                    <?php if (!empty($item['is_active'])): ?>
                                        <div class="mt-1"><span class="badge badge-success text-xs">Active</span></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <?php
                                    $st = $item['status'];
                                    if ($st === 'REVIEW_REQUIRED'):
                                        echo '<span class="badge badge-warning p-1 text-wrap d-block"><i class="fas fa-clock mr-1"></i>Chờ Admin duyệt</span>';
                                    elseif ($st === 'APPROVED'):
                                        echo '<span class="badge badge-success p-1 text-wrap d-block"><i class="fas fa-check-circle mr-1"></i>Đã phê duyệt</span>';
                                    elseif ($st === 'READY'):
                                        echo '<span class="badge badge-info p-1 text-wrap d-block"><i class="fas fa-play mr-1"></i>Sẵn sàng Render</span>';
                                    elseif ($st === 'WAITING_ASSET'):
                                        echo '<span class="badge badge-danger p-1 text-wrap d-block"><i class="fas fa-images mr-1"></i>Thiếu tài nguyên</span>';
                                    elseif ($st === 'PROCESSING' || $st === 'QUEUED'):
                                        echo '<span class="badge badge-primary p-1 text-wrap d-block"><i class="fas fa-spinner fa-spin mr-1"></i>Đang Render</span>';
                                    elseif ($st === 'REJECTED'):
                                        echo '<span class="badge badge-danger p-1 text-wrap d-block"><i class="fas fa-times-circle mr-1"></i>Bị từ chối</span>';
                                    else:
                                        echo '<span class="badge badge-secondary p-1 text-wrap d-block">'.$st.'</span>';
                                    endif;
                                    ?>
                                </td>
                                <td class="text-center align-middle text-muted" style="font-size: 12px; line-height: 1.25;">
                                    <?=date('d/m/Y', $item['date_updated'] ?: $item['date_created'])?><br>
                                    <span class="text-xs"><?=date('H:i', $item['date_updated'] ?: $item['date_created'])?></span>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-inline-flex flex-wrap justify-content-center" style="gap: 3px;">
                                        <a href="<?=$linkView?>&id=<?=$item['id']?>" class="btn btn-xs btn-info shadow-sm" title="Xem chi tiết & Preview"><i class="fas fa-eye"></i> Xem</a>
                                        <?php if (in_array($item['status'], array('READY', 'FAILED', 'REJECTED', 'WAITING_ASSET'))): ?>
                                            <a href="index.php?com=ai_video&act=render_now&id=<?=$item['id']?>" class="btn btn-xs btn-primary shadow-sm" title="Render ngay" onclick="return confirm('Kích hoạt tiến trình render video ngay bây giờ?');"><i class="fas fa-bolt"></i> Render</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-film fa-3x mb-3 text-secondary d-block" style="opacity: 0.5;"></i>
                                    <h5>Chưa có dự án video nào</h5>
                                    <p class="mb-0">Hãy bấm "<strong>Tạo Dự án Video</strong>" từ kịch bản TikTok đã duyệt để bắt đầu!</p>
                                </td>
                            </tr>
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

