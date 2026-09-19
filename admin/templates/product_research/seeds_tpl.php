<?php
$linkSeeds = "index.php?com=product_research&act=seeds";
$linkAddSeed = "index.php?com=product_research&act=add_seed";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="index.php?com=product_research&act=man">Nghiên cứu sản phẩm</a></li>
                <li class="breadcrumb-item active">Từ khóa & Hạt giống nghiên cứu (Research Seeds)</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <!-- Filter & Action Bar -->
    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3 mb-2 mb-md-0">
                    <select class="form-control form-control-sm" id="filter_seed_type" onchange="applySeedFilter()">
                        <option value="">-- Tất cả loại hạt giống --</option>
                        <option value="keyword" <?= ($_GET['seed_type'] ?? '') == 'keyword' ? 'selected' : '' ?>>Từ khóa sản phẩm (Keyword)</option>
                        <option value="problem" <?= ($_GET['seed_type'] ?? '') == 'problem' ? 'selected' : '' ?>>Vấn đề / Pain Point</option>
                        <option value="audience" <?= ($_GET['seed_type'] ?? '') == 'audience' ? 'selected' : '' ?>>Đối tượng khách hàng (Audience)</option>
                        <option value="category" <?= ($_GET['seed_type'] ?? '') == 'category' ? 'selected' : '' ?>>Danh mục (Category)</option>
                        <option value="product_idea" <?= ($_GET['seed_type'] ?? '') == 'product_idea' ? 'selected' : '' ?>>Ý tưởng sản phẩm (Idea)</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select class="form-control form-control-sm" id="filter_seed_status" onchange="applySeedFilter()">
                        <option value="">-- Trạng thái --</option>
                        <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động (Active)</option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Tạm dừng (Inactive)</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="seed_keyword" placeholder="Tìm tiêu đề, từ khóa..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>" onkeypress="if (event.keyCode == 13) applySeedFilter();">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" onclick="applySeedFilter()"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-right">
                    <a href="<?= $linkAddSeed ?>" class="btn btn-sm btn-success mr-1"><i class="fas fa-plus mr-1"></i>Thêm Hạt giống mới</a>
                    <a href="index.php?com=product_research&act=jobs" class="btn btn-sm btn-outline-info"><i class="fas fa-tasks mr-1"></i>Xem Hàng đợi Jobs</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Seeds Table -->
    <div class="card card-primary card-outline text-sm shadow-sm mb-0">
        <div class="card-header">
            <h3 class="card-title font-weight-bold"><i class="fas fa-seedling mr-1"></i> Danh sách Hạt giống Khám phá Sản phẩm (Seeds)</h3>
            <div class="card-tools">
                <span class="badge badge-secondary"><?= number_format($countTotal) ?> hạt giống</span>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap align-middle">
                <thead>
                    <tr>
                        <th class="align-middle text-center" width="5%">ID</th>
                        <th class="align-middle" width="30%">Tiêu đề & Từ khóa hạt giống</th>
                        <th class="align-middle text-center" width="12%">Phân loại</th>
                        <th class="align-middle text-center" width="10%">Nền tảng / Chiều sâu</th>
                        <th class="align-middle text-center" width="10%">Tần suất</th>
                        <th class="align-middle text-center" width="12%">Lần chạy gần nhất</th>
                        <th class="align-middle text-center" width="8%">Trạng thái</th>
                        <th class="align-middle text-center" width="13%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)) { ?>
                        <?php foreach ($items as $v) { ?>
                            <tr>
                                <td class="align-middle text-center text-muted">#<?= $v['id'] ?></td>
                                <td class="align-middle">
                                    <a href="index.php?com=product_research&act=edit_seed&id=<?= $v['id'] ?>" class="font-weight-bold text-primary">
                                        <?= htmlspecialchars($v['title']) ?>
                                    </a>
                                    <div class="text-xs text-muted mt-1">
                                        <i class="fas fa-key mr-1"></i>Từ khóa: <strong class="text-dark"><?= htmlspecialchars($v['keyword']) ?></strong>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-info text-uppercase px-2 py-1"><?= $v['seed_type'] ?></span>
                                </td>
                                <td class="align-middle text-center text-xs">
                                    <div><span class="badge badge-light border text-uppercase"><?= $v['platform'] ?></span></div>
                                    <div class="text-muted mt-1">Độ sâu: <strong><?= $v['depth'] ?></strong></div>
                                </td>
                                <td class="align-middle text-center text-xs">
                                    <span class="badge badge-secondary"><?= ucfirst($v['frequency']) ?></span>
                                    <div class="text-muted mt-1">Max: <?= $v['max_results'] ?> SP</div>
                                </td>
                                <td class="align-middle text-center text-xs text-muted">
                                    <?= !empty($v['last_run']) ? date('d/m/Y H:i', $v['last_run']) : 'Chưa chạy' ?>
                                    <?php if (!empty($v['next_run']) && $v['frequency'] !== 'manual') { ?>
                                        <div class="text-info mt-1">Kế tiếp: <?= date('d/m/Y', $v['next_run']) ?></div>
                                    <?php } ?>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge <?= ($v['status'] == 'active') ? 'badge-success' : 'badge-danger' ?> px-2 py-1">
                                        <?= strtoupper($v['status']) ?>
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="index.php?com=product_research&act=run_seed&id=<?= $v['id'] ?>" class="btn btn-warning text-dark font-weight-bold" onclick="return confirm('Kích hoạt tác vụ nghiên cứu ngay cho hạt giống này?')" title="Nghiên cứu ngay (Run Now)">
                                            <i class="fas fa-play mr-1"></i> Chạy
                                        </a>
                                        <a href="index.php?com=product_research&act=edit_seed&id=<?= $v['id'] ?>" class="btn btn-default" title="Chỉnh sửa"><i class="fas fa-edit"></i></a>
                                        <a href="index.php?com=product_research&act=delete_seed&id=<?= $v['id'] ?>" class="btn btn-default text-danger" onclick="return confirm('Bạn có chắc muốn xóa hạt giống này?')" title="Xóa"><i class="fas fa-trash-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-seedling fa-2x mb-2 text-muted"></i>
                                <div>Chưa có hạt giống nghiên cứu nào. Nhấn <strong>"Thêm Hạt giống mới"</strong> để bắt đầu.</div>
                            </td>
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
function applySeedFilter() {
    var type = document.getElementById('filter_seed_type').value;
    var status = document.getElementById('filter_seed_status').value;
    var keyword = document.getElementById('seed_keyword').value;

    var url = "index.php?com=product_research&act=seeds";
    if (type) url += "&seed_type=" + encodeURIComponent(type);
    if (status) url += "&status=" + encodeURIComponent(status);
    if (keyword) url += "&keyword=" + encodeURIComponent(keyword);

    window.location.href = url;
}
</script>
