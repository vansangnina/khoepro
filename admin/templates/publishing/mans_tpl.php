<?php
$linkMan = "index.php?com=publishing&act=man";
$linkView = "index.php?com=publishing&act=view";
$linkCreate = "index.php?com=publishing&act=create";
$linkQueue = "index.php?com=publishing&act=queue";
$linkCalendar = "index.php?com=publishing&act=calendar";
$linkAccounts = "index.php?com=publishing&act=accounts";
$linkSettings = "index.php?com=publishing&act=settings";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6 mb-2 mb-sm-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.25rem;">
                    <i class="fas fa-paper-plane mr-2 text-primary"></i>Trung tâm Xuất bản (Publishing Center)
                </h1>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?=$linkCreate?>" class="btn btn-sm btn-success mr-2 shadow-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Tạo Post Package Mới
                </a>
                <a href="<?=$linkQueue?>" class="btn btn-sm btn-outline-warning mr-2 shadow-sm">
                    <i class="fas fa-tasks mr-1"></i> Hàng đợi Xuất bản
                </a>
                <a href="<?=$linkCalendar?>" class="btn btn-sm btn-outline-info shadow-sm">
                    <i class="fas fa-calendar-alt mr-1"></i> Lịch Xuất bản
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
                        <p class="text-muted mb-0 font-weight-bold">Tổng Post Packages</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-layer-group text-secondary" style="opacity: 0.3;"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-sm-6 col-12 mb-3">
                <div class="small-box bg-light border border-secondary shadow-sm mb-0 h-100">
                    <div class="inner p-3">
                        <h3 class="text-secondary mb-1" style="font-size: 1.75rem;"><?=$stats['draft']?></h3>
                        <p class="text-muted mb-0 font-weight-bold">Bản nháp (Draft)</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-edit text-secondary" style="opacity: 0.3;"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-sm-6 col-12 mb-3">
                <div class="small-box bg-light border border-info shadow-sm mb-0 h-100">
                    <div class="inner p-3">
                        <h3 class="text-info mb-1" style="font-size: 1.75rem;"><?=$stats['ready']?></h3>
                        <p class="text-muted mb-0 font-weight-bold">Sẵn sàng (Ready)</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-check-circle text-info" style="opacity: 0.3;"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-sm-6 col-12 mb-3">
                <div class="small-box bg-light border border-primary shadow-sm mb-0 h-100">
                    <div class="inner p-3">
                        <h3 class="text-primary mb-1" style="font-size: 1.75rem;"><?=$stats['scheduled']?></h3>
                        <p class="text-muted mb-0 font-weight-bold">Đã lên lịch (Scheduled)</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-clock text-primary" style="opacity: 0.3;"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-sm-6 col-12 mb-3">
                <div class="small-box bg-light border border-success shadow-sm mb-0 h-100">
                    <div class="inner p-3">
                        <h3 class="text-success mb-1" style="font-size: 1.75rem;"><?=$stats['published']?></h3>
                        <p class="text-muted mb-0 font-weight-bold">Đã xuất bản (Published)</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-share-square text-success" style="opacity: 0.3;"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-sm-6 col-12 mb-3">
                <div class="small-box bg-light border border-danger shadow-sm mb-0 h-100">
                    <div class="inner p-3">
                        <h3 class="text-danger mb-1" style="font-size: 1.75rem;"><?=$stats['failed']?></h3>
                        <p class="text-muted mb-0 font-weight-bold">Lỗi xuất bản (Failed)</p>
                    </div>
                    <div class="icon" style="top: 10px; right: 10px; font-size: 40px;"><i class="fas fa-exclamation-triangle text-danger" style="opacity: 0.3;"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bộ lọc & Danh sách bài đăng -->
<section class="content text-sm">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <?php $curStatus = $_GET['status'] ?? ''; ?>
                    <li class="nav-item"><a class="nav-link <?= empty($curStatus) ? 'active' : '' ?>" href="<?=$linkMan?>">Tất cả</a></li>
                    <li class="nav-item"><a class="nav-link <?= ($curStatus === 'DRAFT') ? 'active' : '' ?>" href="<?=$linkMan?>&status=DRAFT">Bản nháp (<?=$stats['draft']?>)</a></li>
                    <li class="nav-item"><a class="nav-link <?= ($curStatus === 'READY') ? 'active' : '' ?>" href="<?=$linkMan?>&status=READY">Sẵn sàng (<?=$stats['ready']?>)</a></li>
                    <li class="nav-item"><a class="nav-link <?= ($curStatus === 'SCHEDULED') ? 'active' : '' ?>" href="<?=$linkMan?>&status=SCHEDULED">Đã lên lịch (<?=$stats['scheduled']?>)</a></li>
                    <li class="nav-item"><a class="nav-link <?= ($curStatus === 'PUBLISHED') ? 'active' : '' ?>" href="<?=$linkMan?>&status=PUBLISHED">Đã xuất bản (<?=$stats['published']?>)</a></li>
                    <li class="nav-item"><a class="nav-link <?= ($curStatus === 'FAILED') ? 'active' : '' ?>" href="<?=$linkMan?>&status=FAILED">Lỗi (<?=$stats['failed']?>)</a></li>
                </ul>
            </div>
            <div class="card-body p-3">
                <form action="<?=$linkMan?>" method="GET" class="form-inline mb-3">
                    <input type="hidden" name="com" value="publishing">
                    <input type="hidden" name="act" value="man">
                    <?php if (!empty($curStatus)): ?>
                        <input type="hidden" name="status" value="<?=$curStatus?>">
                    <?php endif; ?>
                    <div class="input-group input-group-sm mr-2 mb-2">
                        <select name="platform" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Tất cả Nền tảng --</option>
                            <?php foreach (PublishingCenter::PLATFORMS as $pKey => $pInfo): ?>
                                <option value="<?=$pKey?>" <?= (($_GET['platform'] ?? '') === $pKey) ? 'selected' : '' ?>><?=$pInfo['name']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group input-group-sm mr-2 mb-2">
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tiêu đề, caption, SP..." value="<?=$_GET['keyword'] ?? ''?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
                        </div>
                    </div>
                    <?php if (!empty($_GET['keyword']) || !empty($_GET['platform'])): ?>
                        <a href="<?=$linkMan?><?= !empty($curStatus) ? '&status=' . $curStatus : '' ?>" class="btn btn-sm btn-outline-secondary mb-2"><i class="fas fa-times"></i> Xóa lọc</a>
                    <?php endif; ?>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 100px;" class="text-center">Video</th>
                                <th>Tiêu đề / Sản phẩm</th>
                                <th style="width: 130px;" class="text-center">Nền tảng</th>
                                <th style="width: 140px;" class="text-center">Tài khoản</th>
                                <th style="width: 130px;" class="text-center">Trạng thái</th>
                                <th style="width: 150px;" class="text-center">Lịch / Xuất bản</th>
                                <th style="width: 110px;" class="text-center">Phương thức</th>
                                <th style="width: 160px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $idx => $p): ?>
                                    <?php
                                    $platInfo = PublishingCenter::PLATFORMS[$p['platform']] ?? array('name' => $p['platform'], 'badge_class' => 'badge-secondary', 'icon' => 'fas fa-share');
                                    $statusInfo = PublishingCenter::STATUSES[$p['status']] ?? array('name' => $p['status'], 'badge' => 'badge-secondary');
                                    ?>
                                    <tr>
                                        <td class="text-center font-weight-bold"><?=$p['id']?></td>
                                        <td class="text-center">
                                            <?php if (!empty($p['video_thumbnail']) && file_exists($p['video_thumbnail'])): ?>
                                                <img src="<?=$p['video_thumbnail']?>" alt="Thumb" class="rounded shadow-sm" style="width: 60px; height: 90px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded bg-dark text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 90px;">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark mb-1">
                                                <a href="<?=$linkView?>&id=<?=$p['id']?>" class="text-primary font-weight-bold" style="font-size: 1rem;"><?=htmlspecialchars($p['title'])?></a>
                                                <?php if (!empty($p['is_outdated'])): ?>
                                                    <span class="badge badge-danger ml-1" title="Dữ liệu gốc đã bị thay đổi"><i class="fas fa-exclamation-triangle"></i> Outdated</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted small mb-1">
                                                <i class="fas fa-box text-secondary mr-1"></i> SP: <strong><?=htmlspecialchars($p['product_name'] ?? 'N/A')?></strong>
                                            </div>
                                            <div class="text-muted small" style="max-height: 40px; overflow: hidden; text-overflow: ellipsis;">
                                                <?=nl2br(htmlspecialchars(mb_substr($p['caption'], 0, 90, 'UTF-8')))?>...
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?=$platInfo['badge_class']?> p-2" style="font-size: 0.85rem;">
                                                <i class="<?=$platInfo['icon']?> mr-1"></i> <?=$platInfo['name']?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="font-weight-bold text-dark"><?=htmlspecialchars($p['account_handle'] ?: '@fitnado.vn')?></div>
                                            <small class="text-muted"><?=htmlspecialchars($p['account_name'] ?: 'Official Channel')?></small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?=$statusInfo['badge']?> p-2" style="font-size: 0.85rem;">
                                                <?=$statusInfo['name']?>
                                            </span>
                                            <?php if (!empty($p['error_message'])): ?>
                                                <div class="text-danger small mt-1 text-truncate" style="max-width: 120px;" title="<?=htmlspecialchars($p['error_message'])?>">
                                                    <i class="fas fa-info-circle"></i> <?=htmlspecialchars($p['error_message'])?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center small">
                                            <?php if ($p['status'] === 'PUBLISHED' && !empty($p['published_at'])): ?>
                                                <div class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> <?=date('H:i d/m/Y', $p['published_at'])?></div>
                                                <?php if (!empty($p['external_post_url'])): ?>
                                                    <a href="<?=$p['external_post_url']?>" target="_blank" class="badge badge-outline-success mt-1"><i class="fas fa-external-link-alt"></i> Xem bài đăng</a>
                                                <?php endif; ?>
                                            <?php elseif (!empty($p['scheduled_at'])): ?>
                                                <div class="text-primary font-weight-bold"><i class="fas fa-calendar mr-1"></i> <?=date('H:i d/m/Y', $p['scheduled_at'])?></div>
                                                <?php if ($p['scheduled_at'] <= time()): ?>
                                                    <span class="badge badge-warning">Đã đến hạn!</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa lên lịch</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($p['provider'] === 'manual'): ?>
                                                <span class="badge badge-info"><i class="fas fa-user-edit mr-1"></i> Thủ công</span>
                                            <?php else: ?>
                                                <span class="badge badge-purple"><i class="fas fa-robot mr-1"></i> TikTok API</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?=$linkView?>&id=<?=$p['id']?>" class="btn btn-sm btn-primary shadow-sm mb-1" title="Xem chi tiết & Thao tác">
                                                <i class="fas fa-eye"></i> Chi tiết
                                            </a>
                                            <a href="index.php?com=publishing&act=duplicate&id=<?=$p['id']?>" class="btn btn-sm btn-outline-secondary shadow-sm mb-1" title="Nhân bản bài đăng" onclick="return confirm('Nhân bản bài đăng này?')">
                                                <i class="fas fa-copy"></i>
                                            </a>
                                            <?php if ($p['status'] !== 'PUBLISHED'): ?>
                                                <a href="index.php?com=publishing&act=delete&id=<?=$p['id']?>" class="btn btn-sm btn-outline-danger shadow-sm mb-1" title="Xóa bài đăng" onclick="return confirm('Bạn có chắc chắn muốn xóa bài đăng này?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-2 text-secondary" style="opacity: 0.5;"></i><br>
                                        Chưa có bài đăng nào trong danh mục này. Hãy bấm <strong>"Tạo Post Package Mới"</strong> từ video đã duyệt!
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <?php if (!empty($paging)): ?>
                    <div class="mt-3 d-flex justify-content-center">
                        <?=$paging?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
