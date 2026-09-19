<?php
$linkMan = "index.php?com=ai_video&act=man";
$linkView = "index.php?com=ai_video&act=view";
$linkCreate = "index.php?com=ai_video&act=create";
$linkJobs = "index.php?com=ai_video&act=jobs";
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-video mr-2 text-primary"></i>Dự án Video AI (AI Video Production)
                </h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="<?=$linkCreate?>" class="btn btn-success mr-2">
                        <i class="fas fa-plus-circle mr-1"></i> Tạo Dự án Video
                    </a>
                    <a href="<?=$linkJobs?>" class="btn btn-outline-info">
                        <i class="fas fa-tasks mr-1"></i> Hàng đợi Render
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Widgets Thống kê -->
<section class="content mb-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-6">
                <div class="small-box bg-light border">
                    <div class="inner">
                        <h3><?=$stats['total']?></h3>
                        <p class="text-muted mb-0">Tổng Video Projects</p>
                    </div>
                    <div class="icon"><i class="fas fa-film text-secondary"></i></div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-light border border-info">
                    <div class="inner">
                        <h3 class="text-info"><?=$stats['ready']?></h3>
                        <p class="text-muted mb-0">Sẵn sàng Render</p>
                    </div>
                    <div class="icon"><i class="fas fa-play-circle text-info"></i></div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-light border border-warning">
                    <div class="inner">
                        <h3 class="text-warning"><?=$stats['review_required']?></h3>
                        <p class="text-muted mb-0">Chờ duyệt (Review)</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock text-warning"></i></div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-light border border-success">
                    <div class="inner">
                        <h3 class="text-success"><?=$stats['approved']?></h3>
                        <p class="text-muted mb-0">Đã phê duyệt</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-double text-success"></i></div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-light border border-danger">
                    <div class="inner">
                        <h3 class="text-danger"><?=$stats['outdated']?></h3>
                        <p class="text-muted mb-0">Kịch bản Lỗi thời</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-light border border-primary">
                    <div class="inner">
                        <h3 class="text-primary"><?=$stats['processing']?></h3>
                        <p class="text-muted mb-0">Đang Render</p>
                    </div>
                    <div class="icon"><i class="fas fa-spinner fa-spin text-primary"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bộ lọc & Danh sách -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <form action="index.php" method="GET" class="form-inline">
                    <input type="hidden" name="com" value="ai_video">
                    <input type="hidden" name="act" value="man">

                    <div class="input-group input-group-sm mr-2 mb-2" style="width: 250px;">
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tiêu đề, sản phẩm..." value="<?=htmlspecialchars($_GET['keyword'] ?? '')?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>

                    <select name="id_product" class="form-control form-control-sm mr-2 mb-2" onchange="this.form.submit()">
                        <option value="">-- Tất cả sản phẩm --</option>
                        <?php if (!empty($productsList)) foreach ($productsList as $p): ?>
                            <option value="<?=$p['id']?>" <?=(isset($_GET['id_product']) && $_GET['id_product'] == $p['id']) ? 'selected' : ''?>><?=htmlspecialchars($p['namevi'])?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="status" class="form-control form-control-sm mr-2 mb-2" onchange="this.form.submit()">
                        <option value="">-- Trạng thái --</option>
                        <option value="READY" <?=(isset($_GET['status']) && $_GET['status'] == 'READY') ? 'selected' : ''?>>Sẵn sàng Render (READY)</option>
                        <option value="WAITING_ASSET" <?=(isset($_GET['status']) && $_GET['status'] == 'WAITING_ASSET') ? 'selected' : ''?>>Thiếu tài nguyên (WAITING_ASSET)</option>
                        <option value="PROCESSING" <?=(isset($_GET['status']) && $_GET['status'] == 'PROCESSING') ? 'selected' : ''?>>Đang Render (PROCESSING)</option>
                        <option value="REVIEW_REQUIRED" <?=(isset($_GET['status']) && $_GET['status'] == 'REVIEW_REQUIRED') ? 'selected' : ''?>>Chờ Admin duyệt (REVIEW_REQUIRED)</option>
                        <option value="APPROVED" <?=(isset($_GET['status']) && $_GET['status'] == 'APPROVED') ? 'selected' : ''?>>Đã duyệt (APPROVED)</option>
                        <option value="REJECTED" <?=(isset($_GET['status']) && $_GET['status'] == 'REJECTED') ? 'selected' : ''?>>Từ chối (REJECTED)</option>
                    </select>

                    <a href="<?=$linkMan?>" class="btn btn-sm btn-secondary mb-2"><i class="fas fa-undo mr-1"></i> Xóa lọc</a>
                </form>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped align-middle text-nowrap mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Thumbnail / Video</th>
                            <th>Sản phẩm liên kết</th>
                            <th>Định dạng / Độ dài</th>
                            <th>Nhà cung cấp</th>
                            <th>Phiên bản</th>
                            <th>Trạng thái</th>
                            <th>Ngày cập nhật</th>
                            <th style="width: 150px;" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): foreach ($items as $item): ?>
                            <tr>
                                <td><span class="badge badge-light border">#<?=$item['id']?></span></td>
                                <td style="width: 120px;">
                                    <?php if (!empty($item['video_file']) && file_exists($item['video_file'])): ?>
                                        <a href="<?=$linkView?>&id=<?=$item['id']?>">
                                            <div class="position-relative d-inline-block rounded overflow-hidden bg-dark text-center" style="width: 80px; height: 110px;">
                                                <i class="fas fa-play-circle fa-2x text-white position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.85;"></i>
                                                <span class="badge badge-dark position-absolute" style="bottom: 4px; right: 4px; font-size: 10px;"><?=$item['duration_actual'] ? round($item['duration_actual']).'s' : $item['target_duration'].'s'?></span>
                                            </div>
                                        </a>
                                    <?php elseif (!empty($item['product_photo']) && file_exists('upload/product/'.$item['product_photo'])): ?>
                                        <img src="upload/product/<?=$item['product_photo']?>" class="img-thumbnail" style="width: 70px; height: 70px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary text-center p-3 rounded" style="width: 70px; height: 70px;"><i class="fas fa-film fa-2x text-light"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><a href="<?=$linkView?>&id=<?=$item['id']?>"><?=htmlspecialchars($item['title'] ?: 'Video Project #'.$item['id'])?></a></strong>
                                    <div class="small text-muted">
                                        <i class="fas fa-box text-secondary mr-1"></i><?=htmlspecialchars($item['product_name'] ?: 'N/A')?>
                                    </div>
                                    <?php if (!empty($item['is_outdated'])): ?>
                                        <span class="badge badge-danger mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>Kịch bản gốc đã thay đổi</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info"><i class="fas fa-mobile-alt mr-1"></i><?=$item['aspect_ratio']?></span>
                                    <span class="badge badge-secondary"><?=$item['target_duration']?>s</span>
                                    <div class="small text-muted mt-1"><?=$item['template_id']?></div>
                                </td>
                                <td>
                                    <span class="badge badge-light border text-uppercase font-weight-bold"><?=$item['provider']?></span>
                                    <?php if (!empty($item['cost_estimate'])): ?>
                                        <div class="small text-muted">$<?=number_format($item['cost_estimate'], 3)?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">v<?=$item['version']?></span>
                                    <?php if (!empty($item['is_active'])): ?>
                                        <span class="badge badge-success ml-1">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $st = $item['status'];
                                    if ($st === 'REVIEW_REQUIRED'):
                                        echo '<span class="badge badge-warning p-2"><i class="fas fa-clock mr-1"></i>Chờ Admin duyệt</span>';
                                    elseif ($st === 'APPROVED'):
                                        echo '<span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i>Đã phê duyệt</span>';
                                    elseif ($st === 'READY'):
                                        echo '<span class="badge badge-info p-2"><i class="fas fa-play mr-1"></i>Sẵn sàng Render</span>';
                                    elseif ($st === 'WAITING_ASSET'):
                                        echo '<span class="badge badge-danger p-2"><i class="fas fa-images mr-1"></i>Thiếu tài nguyên</span>';
                                    elseif ($st === 'PROCESSING' || $st === 'QUEUED'):
                                        echo '<span class="badge badge-primary p-2"><i class="fas fa-spinner fa-spin mr-1"></i>Đang Render</span>';
                                    elseif ($st === 'REJECTED'):
                                        echo '<span class="badge badge-danger p-2"><i class="fas fa-times-circle mr-1"></i>Bị từ chối</span>';
                                    else:
                                        echo '<span class="badge badge-secondary p-2">'.$st.'</span>';
                                    endif;
                                    ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?=date('d/m/Y H:i', $item['date_updated'] ?: $item['date_created'])?></small>
                                </td>
                                <td class="text-center">
                                    <a href="<?=$linkView?>&id=<?=$item['id']?>" class="btn btn-sm btn-info mr-1" title="Xem chi tiết & Preview"><i class="fas fa-eye"></i> Xem</a>
                                    <?php if (in_array($item['status'], array('READY', 'FAILED', 'REJECTED'))): ?>
                                        <a href="index.php?com=ai_video&act=render_now&id=<?=$item['id']?>" class="btn btn-sm btn-primary" title="Render ngay" onclick="return confirm('Kích hoạt tiến trình render video ngay bây giờ?');"><i class="fas fa-bolt"></i> Render</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-film fa-3x mb-3 text-secondary d-block"></i>
                                    Chưa có dự án video nào. Hãy bấm "<strong>Tạo Dự án Video</strong>" từ kịch bản TikTok đã duyệt!
                                </td>
                            </tr>
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
