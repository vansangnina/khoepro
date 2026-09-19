<?php
$linkMan = "index.php?com=product_research&act=man";
$linkAdd = "index.php?com=product_research&act=add";
$linkWeights = "index.php?com=product_research&act=weights";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item active">Nghiên cứu sản phẩm (Product Research)</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <!-- Stat Summary Widgets -->
    <div class="row mb-3">
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-secondary"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tổng ứng viên</span>
                    <span class="info-box-number"><?= number_format($summaryStats['total'] ?? 0) ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info"><i class="fas fa-search"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Phát hiện mới</span>
                    <span class="info-box-number"><?= number_format($summaryStats['discovered'] ?? 0) ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary"><i class="fas fa-flask"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Đã nghiên cứu</span>
                    <span class="info-box-number"><?= number_format($summaryStats['researched'] ?? 0) ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Đã duyệt</span>
                    <span class="info-box-number"><?= number_format($summaryStats['approved'] ?? 0) ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Đã từ chối</span>
                    <span class="info-box-number"><?= number_format($summaryStats['rejected'] ?? 0) ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning text-white"><i class="fas fa-star"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Điểm TB / Sản phẩm</span>
                    <span class="info-box-number"><?= $summaryStats['avg_score'] ?? 0 ?> / 100</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Action Bar -->
    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 mb-2 mb-md-0">
                    <select class="form-control form-control-sm" id="filter_platform" onchange="applyFilter()">
                        <option value="">-- Nền tảng (Platform) --</option>
                        <option value="tiktok" <?= ($_GET['platform'] ?? '') == 'tiktok' ? 'selected' : '' ?>>TikTok Shop</option>
                        <option value="shopee" <?= ($_GET['platform'] ?? '') == 'shopee' ? 'selected' : '' ?>>Shopee</option>
                        <option value="lazada" <?= ($_GET['platform'] ?? '') == 'lazada' ? 'selected' : '' ?>>Lazada</option>
                        <option value="brand" <?= ($_GET['platform'] ?? '') == 'brand' ? 'selected' : '' ?>>Brand Website</option>
                        <option value="manual" <?= ($_GET['platform'] ?? '') == 'manual' ? 'selected' : '' ?>>Thủ công (Manual)</option>
                        <option value="other" <?= ($_GET['platform'] ?? '') == 'other' ? 'selected' : '' ?>>Khác</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select class="form-control form-control-sm" id="filter_source" onchange="applyFilter()">
                        <option value="">-- Nguồn phát hiện --</option>
                        <option value="manual" <?= ($_GET['discovery_source'] ?? '') == 'manual' ? 'selected' : '' ?>>Thủ công (Manual)</option>
                        <option value="ai_agent" <?= ($_GET['discovery_source'] ?? '') == 'ai_agent' ? 'selected' : '' ?>>AI Research Agent</option>
                        <option value="tiktok_crawler" <?= ($_GET['discovery_source'] ?? '') == 'tiktok_crawler' ? 'selected' : '' ?>>TikTok Platform</option>
                        <option value="shopee_crawler" <?= ($_GET['discovery_source'] ?? '') == 'shopee_crawler' ? 'selected' : '' ?>>Shopee Platform</option>
                        <option value="csv_import" <?= ($_GET['discovery_source'] ?? '') == 'csv_import' ? 'selected' : '' ?>>CSV Import</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select class="form-control form-control-sm" id="filter_status" onchange="applyFilter()">
                        <option value="">-- Trạng thái --</option>
                        <option value="DISCOVERED" <?= ($_GET['status'] ?? '') == 'DISCOVERED' ? 'selected' : '' ?>>DISCOVERED (Mới)</option>
                        <option value="RESEARCHED" <?= ($_GET['status'] ?? '') == 'RESEARCHED' ? 'selected' : '' ?>>RESEARCHED (Đã phân tích)</option>
                        <option value="APPROVED" <?= ($_GET['status'] ?? '') == 'APPROVED' ? 'selected' : '' ?>>APPROVED (Đã duyệt)</option>
                        <option value="REJECTED" <?= ($_GET['status'] ?? '') == 'REJECTED' ? 'selected' : '' ?>>REJECTED (Từ chối)</option>
                        <option value="PRODUCT_CREATED" <?= ($_GET['status'] ?? '') == 'PRODUCT_CREATED' ? 'selected' : '' ?>>PRODUCT_CREATED (Đã tạo SP)</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select class="form-control form-control-sm" id="filter_score" onchange="applyFilter()">
                        <option value="">-- Khoảng điểm (Score) --</option>
                        <option value="80-100" <?= ($_GET['score_range'] ?? '') == '80-100' ? 'selected' : '' ?>>80 - 100 (Rất tiềm năng)</option>
                        <option value="60-79" <?= ($_GET['score_range'] ?? '') == '60-79' ? 'selected' : '' ?>>60 - 79 (Tiềm năng khá)</option>
                        <option value="40-59" <?= ($_GET['score_range'] ?? '') == '40-59' ? 'selected' : '' ?>>40 - 59 (Trung bình)</option>
                        <option value="0-39" <?= ($_GET['score_range'] ?? '') == '0-39' ? 'selected' : '' ?>>0 - 39 (Thấp)</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="keyword" placeholder="Tìm tên, ID ngoài..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>" onkeypress="if (event.keyCode == 13) applyFilter();">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" onclick="applyFilter()"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 text-right">
                    <a href="<?= $linkAdd ?>" class="btn btn-sm btn-success mr-1" title="Thêm ứng viên"><i class="fas fa-plus"></i></a>
                    <a href="index.php?com=product_research&act=seeds" class="btn btn-sm btn-outline-primary mr-1" title="Quản lý Seeds"><i class="fas fa-seedling"></i></a>
                    <a href="index.php?com=product_research&act=jobs" class="btn btn-sm btn-outline-info mr-1" title="Hàng đợi Jobs"><i class="fas fa-tasks"></i></a>
                    <a href="index.php?com=product_research&act=provider_config" class="btn btn-sm btn-outline-secondary" title="Cấu hình AI & API"><i class="fas fa-robot"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Candidates Table -->
    <div class="card card-primary card-outline text-sm shadow-sm mb-0">
        <div class="card-header py-2">
            <h3 class="card-title font-weight-bold"><i class="fas fa-list mr-1"></i> Danh sách Ứng viên Nghiên cứu Sản phẩm</h3>
            <div class="card-tools">
                <span class="badge badge-secondary"><?= number_format($countTotal) ?> kết quả</span>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped align-middle mb-0" style="width: 100%; table-layout: auto;">
                <thead class="thead-light">
                    <tr class="text-center" style="font-size: 13px;">
                        <th style="width: 50px;">ID</th>
                        <th class="text-left" style="min-width: 230px;">Sản phẩm / Nền tảng</th>
                        <th style="width: 100px;">Giá bán</th>
                        <th style="width: 120px;">Tín hiệu thị trường</th>
                        <th style="width: 90px;">Hoa hồng</th>
                        <th style="width: 140px;">Điểm thành phần</th>
                        <th style="width: 80px;">Tổng điểm</th>
                        <th style="width: 110px;">Trạng thái</th>
                        <th style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)) { ?>
                        <?php foreach ($items as $v) {
                            $totalScore = $v['total_score'];
                            $scoreBadge = 'badge-secondary';
                            if ($totalScore !== null) {
                                if ($totalScore >= 80) $scoreBadge = 'badge-success';
                                elseif ($totalScore >= 60) $scoreBadge = 'badge-primary';
                                elseif ($totalScore >= 40) $scoreBadge = 'badge-warning';
                                else $scoreBadge = 'badge-danger';
                            }

                            $statusBadge = 'badge-secondary';
                            if ($v['status'] == 'APPROVED') $statusBadge = 'badge-success';
                            elseif ($v['status'] == 'REJECTED') $statusBadge = 'badge-danger';
                            elseif ($v['status'] == 'RESEARCHED') $statusBadge = 'badge-info';
                            elseif ($v['status'] == 'PRODUCT_CREATED') $statusBadge = 'badge-dark';
                        ?>
                            <tr>
                                <td class="align-middle text-center font-weight-bold text-muted">#<?= $v['id'] ?></td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-start">
                                        <?php if (!empty($v['image_url'])) { ?>
                                            <img src="<?= htmlspecialchars($v['image_url']) ?>" class="rounded mr-2 border shadow-sm flex-shrink-0" style="width:48px;height:48px;object-fit:cover;" onerror="this.src='assets/images/noimage.png'">
                                        <?php } else { ?>
                                            <div class="rounded mr-2 bg-light d-flex align-items-center justify-content-center text-muted border flex-shrink-0" style="width:48px;height:48px;"><i class="fas fa-box"></i></div>
                                        <?php } ?>
                                        <div style="min-width: 0;">
                                            <a href="index.php?com=product_research&act=edit&id=<?= $v['id'] ?>" class="font-weight-bold text-primary d-block" style="word-break: break-word; line-height: 1.35;" title="<?= htmlspecialchars($v['name']) ?>">
                                                <?= htmlspecialchars($v['name']) ?>
                                            </a>
                                            <div class="text-xs text-muted d-flex align-items-center flex-wrap mt-1" style="gap: 3px;">
                                                <?php if (($v['discovery_source'] ?? 'manual') === 'ai_agent') { ?>
                                                    <span class="badge badge-info mr-1" title="Khám phá bởi AI Research Agent"><i class="fas fa-robot mr-1"></i>AI</span>
                                                <?php } elseif (($v['discovery_source'] ?? 'manual') === 'tiktok_crawler') { ?>
                                                    <span class="badge badge-dark mr-1" title="Quét từ TikTok Platform"><i class="fab fa-tiktok mr-1"></i>TikTok</span>
                                                <?php } elseif (($v['discovery_source'] ?? 'manual') === 'shopee_crawler') { ?>
                                                    <span class="badge badge-warning text-dark mr-1" title="Quét từ Shopee Platform"><i class="fas fa-shopping-bag mr-1"></i>Shopee</span>
                                                <?php } ?>
                                                <span class="badge badge-light border text-uppercase mr-1"><i class="fas fa-tag mr-1"></i><?= htmlspecialchars($v['platform']) ?></span>
                                                <?php if (!empty($v['category_hint'])) { ?>
                                                    <span class="mr-1"><i class="fas fa-folder mr-1"></i><?= htmlspecialchars($v['category_hint']) ?></span>
                                                <?php } ?>
                                                <?php if (!empty($v['brand_hint'])) { ?>
                                                    <span class="mr-1 font-weight-bold text-dark"><i class="fas fa-award mr-1"></i><?= htmlspecialchars($v['brand_hint']) ?></span>
                                                <?php } ?>
                                                <a href="<?= htmlspecialchars($v['source_url']) ?>" target="_blank" class="text-info ml-1" title="Mở liên kết nguồn"><i class="fas fa-external-link-alt"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <?php if ($v['price'] !== null) { ?>
                                        <div class="font-weight-bold text-dark"><?= number_format($v['price'], 0, ',', '.') ?>đ</div>
                                        <?php if ($v['original_price'] > $v['price']) { ?>
                                            <del class="text-xs text-muted"><?= number_format($v['original_price'], 0, ',', '.') ?>đ</del>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span class="text-muted text-xs">Chưa rõ</span>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-center text-xs">
                                    <div><strong>Đã bán:</strong> <?= ($v['sales_count'] !== null) ? number_format($v['sales_count']) : '<span class="text-muted">-</span>' ?></div>
                                    <div class="mt-1"><strong>Rating:</strong> <?= ($v['rating'] !== null) ? '⭐ ' . number_format($v['rating'], 1) : '<span class="text-muted">-</span>' ?> (<?= ($v['review_count'] !== null) ? number_format($v['review_count']) : '-' ?>)</div>
                                    <?php if (!empty($v['top_video_views'])) { ?>
                                        <div class="mt-1"><strong>Top Video:</strong> <span class="text-info"><?= number_format($v['top_video_views']) ?> view</span></div>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?php if ($v['commission_rate'] !== null) { ?>
                                        <span class="badge badge-success px-2 py-1 font-weight-bold"><?= $v['commission_rate'] ?>%</span>
                                        <?php if ($v['commission_value'] > 0) { ?>
                                            <div class="text-xs text-muted mt-1">+<?= number_format($v['commission_value'], 0, ',', '.') ?>đ</div>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span class="text-muted text-xs">NULL</span>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-center text-xs">
                                    <div class="d-flex justify-content-center flex-wrap" style="gap:3px;">
                                        <span class="badge badge-light border" title="Nhu cầu (Demand)">Dem: <strong><?= $v['demand_score'] !== null ? $v['demand_score'] : '-' ?></strong></span>
                                        <span class="badge badge-light border" title="Tiềm năng nội dung (Content)">Cont: <strong><?= $v['content_score'] !== null ? $v['content_score'] : '-' ?></strong></span>
                                        <span class="badge badge-light border" title="Hoa hồng (Commission)">Comm: <strong><?= $v['commission_score'] !== null ? $v['commission_score'] : '-' ?></strong></span>
                                        <span class="badge badge-light border" title="Cơ hội cạnh tranh (Competition)">Comp: <strong><?= $v['competition_score'] !== null ? $v['competition_score'] : '-' ?></strong></span>
                                        <span class="badge badge-light border" title="Tiềm năng SEO">SEO: <strong><?= $v['seo_score'] !== null ? $v['seo_score'] : '-' ?></strong></span>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge <?= $scoreBadge ?> px-2 py-1 font-weight-bold" style="font-size: 1rem;">
                                            <?= $totalScore !== null ? $totalScore : 'N/A' ?>
                                        </span>
                                        <span class="text-xs text-muted mt-1">/100</span>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge <?= $statusBadge ?> p-1 text-wrap d-block"><?= $v['status'] ?></span>
                                    <?php if (!empty($v['id_product'])) { ?>
                                        <div class="mt-1">
                                            <a href="index.php?com=product&act=edit&type=san-pham&id=<?= $v['id_product'] ?>" class="badge badge-primary" target="_blank" title="Xem sản phẩm thật">SP #<?= $v['id_product'] ?></a>
                                        </div>
                                    <?php } ?>
                                    <?php if ($v['status'] == 'REJECTED' && !empty($v['reject_reason'])) { ?>
                                        <div class="text-xs text-danger mt-1" style="word-break: break-word;" title="<?= htmlspecialchars($v['reject_reason']) ?>"><?= htmlspecialchars($v['reject_reason']) ?></div>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="d-inline-flex flex-wrap justify-content-center" style="gap: 3px;">
                                        <a href="index.php?com=product_research&act=edit&id=<?= $v['id'] ?>" class="btn btn-xs btn-default border" title="Xem chi tiết & Sửa"><i class="fas fa-edit"></i></a>
                                        
                                        <?php if ($v['status'] == 'APPROVED' && empty($v['id_product'])) { ?>
                                            <a href="index.php?com=product_research&act=create_product&id=<?= $v['id'] ?>" class="btn btn-xs btn-success" title="Tạo sản phẩm thật"><i class="fas fa-magic"></i></a>
                                        <?php } ?>
                                        
                                        <?php if ($v['status'] == 'RESEARCHED' || $v['status'] == 'DISCOVERED') { ?>
                                            <a href="index.php?com=product_research&act=approve&id=<?= $v['id'] ?>" class="btn btn-xs btn-outline-success" onclick="return confirm('Xác nhận duyệt ứng viên này?')" title="Duyệt ứng viên (Approve)"><i class="fas fa-check"></i></a>
                                            <button type="button" class="btn btn-xs btn-outline-danger" onclick="openRejectModal(<?= $v['id'] ?>)" title="Từ chối (Reject)"><i class="fas fa-times"></i></button>
                                        <?php } ?>

                                        <?php if (empty($v['id_product'])) { ?>
                                            <a href="index.php?com=product_research&act=delete&id=<?= $v['id'] ?>" class="btn btn-xs btn-default text-danger border" onclick="return confirm('Bạn có chắc muốn xóa ứng viên này?')" title="Xóa"><i class="fas fa-trash-alt"></i></a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fas fa-search-dollar fa-2x mb-2 text-muted" style="opacity: 0.5;"></i>
                                <div>Chưa có ứng viên nghiên cứu sản phẩm nào. Nhấn <strong>"Thêm mới"</strong> để bắt đầu.</div>
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

<!-- Reject Reason Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" action="index.php?com=product_research&act=reject" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel"><i class="fas fa-times-circle mr-1"></i> Từ chối Ứng viên Nghiên cứu</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="reject_candidate_id" value="">
                <div class="form-group">
                    <label for="reject_reason" class="font-weight-bold">Lý do từ chối:</label>
                    <select class="form-control mb-2" onchange="document.getElementById('reject_reason').value = this.value;">
                        <option value="">-- Chọn lý do mẫu --</option>
                        <option value="Tỷ lệ hoa hồng quá thấp (<5%)">Tỷ lệ hoa hồng quá thấp (<5%)</option>
                        <option value="Thị trường bão hòa / Cạnh tranh quá cao">Thị trường bão hòa / Cạnh tranh quá cao</option>
                        <option value="Không phù hợp định vị Gym/Fitness của FITNADO">Không phù hợp định vị Gym/Fitness của FITNADO</option>
                        <option value="Chất lượng sản phẩm & Đánh giá sao thấp (<4.0)">Chất lượng sản phẩm & Đánh giá sao thấp (<4.0)</option>
                        <option value="Thiếu dữ liệu thị trường và video kiểm chứng">Thiếu dữ liệu thị trường và video kiểm chứng</option>
                        <option value="Trùng lặp với sản phẩm đã có">Trùng lặp với sản phẩm đã có</option>
                    </select>
                    <textarea class="form-control" name="reject_reason" id="reject_reason" rows="3" placeholder="Nhập lý do cụ thể..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-danger font-weight-bold"><i class="fas fa-ban mr-1"></i> Xác nhận Từ chối</button>
            </div>
        </form>
    </div>
</div>

<script>
function applyFilter() {
    var platform = document.getElementById('filter_platform').value;
    var source = document.getElementById('filter_source').value;
    var status = document.getElementById('filter_status').value;
    var score = document.getElementById('filter_score').value;
    var keyword = document.getElementById('keyword').value;

    var url = "index.php?com=product_research&act=man";
    if (platform) url += "&platform=" + encodeURIComponent(platform);
    if (source) url += "&discovery_source=" + encodeURIComponent(source);
    if (status) url += "&status=" + encodeURIComponent(status);
    if (score) url += "&score_range=" + encodeURIComponent(score);
    if (keyword) url += "&keyword=" + encodeURIComponent(keyword);

    window.location.href = url;
}

function openRejectModal(id) {
    document.getElementById('reject_candidate_id').value = id;
    $('#rejectModal').modal('show');
}
</script>
