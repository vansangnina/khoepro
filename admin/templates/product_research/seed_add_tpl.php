<?php
$linkSaveSeed = "index.php?com=product_research&act=save_seed";
$linkSeeds = "index.php?com=product_research&act=seeds";
$isEdit = !empty($item['id']);
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="index.php?com=product_research&act=man">Nghiên cứu sản phẩm</a></li>
                <li class="breadcrumb-item"><a href="<?= $linkSeeds ?>">Hạt giống nghiên cứu</a></li>
                <li class="breadcrumb-item active"><?= $isEdit ? 'Chỉnh sửa Hạt giống #' . $item['id'] : 'Thêm mới Hạt giống' ?></li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="post" action="<?= $linkSaveSeed ?>">
                <input type="hidden" name="id" value="<?= $item['id'] ?? 0 ?>">

                <div class="card card-primary card-outline shadow-sm text-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-seedling mr-1"></i> Cấu hình Hạt giống Khám phá Sản phẩm (Seed Configuration)</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title" class="font-weight-bold">Tiêu đề hạt giống nghiên cứu: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="data[title]" id="title" placeholder="Ví dụ: Phụ kiện hỗ trợ Squat và Deadlift nặng" value="<?= htmlspecialchars($item['title'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="keyword" class="font-weight-bold">Từ khóa / Mô tả vấn đề (Seed Keyword / Problem): <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm" name="data[keyword]" id="keyword" rows="2" placeholder="Ví dụ: đai lưng tập gym squat deadlift bảo vệ cột sống..." required><?= htmlspecialchars($item['keyword'] ?? '') ?></textarea>
                            <small class="text-muted">Từ khóa này sẽ là đầu vào cho AI Agent và các Provider quét tìm kiếm sản phẩm trên thị trường.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="seed_type" class="font-weight-bold">Phân loại hạt giống:</label>
                                    <select class="form-control form-control-sm" name="data[seed_type]" id="seed_type">
                                        <option value="keyword" <?= ($item['seed_type'] ?? '') == 'keyword' ? 'selected' : '' ?>>Từ khóa sản phẩm (Keyword)</option>
                                        <option value="problem" <?= ($item['seed_type'] ?? '') == 'problem' ? 'selected' : '' ?>>Vấn đề / Pain Point thể hình (Problem)</option>
                                        <option value="audience" <?= ($item['seed_type'] ?? '') == 'audience' ? 'selected' : '' ?>>Đối tượng khách hàng mục tiêu (Audience)</option>
                                        <option value="category" <?= ($item['seed_type'] ?? '') == 'category' ? 'selected' : '' ?>>Danh mục thị trường (Category)</option>
                                        <option value="product_idea" <?= ($item['seed_type'] ?? '') == 'product_idea' ? 'selected' : '' ?>>Ý tưởng sản phẩm ngách (Product Idea)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id" class="font-weight-bold">Danh mục sản phẩm liên kết:</label>
                                    <select class="form-control form-control-sm" name="data[category_id]" id="category_id">
                                        <option value="0">-- Tự động gợi ý theo nội dung --</option>
                                        <?php if (!empty($categories)) {
                                            foreach ($categories as $cat) { ?>
                                                <option value="<?= $cat['id'] ?>" <?= (($item['category_id'] ?? 0) == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['namevi']) ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="platform" class="font-weight-bold">Nền tảng ưu tiên:</label>
                                    <select class="form-control form-control-sm" name="data[platform]" id="platform">
                                        <option value="all" <?= ($item['platform'] ?? '') == 'all' ? 'selected' : '' ?>>Tất cả nền tảng (AI Multi-Platform)</option>
                                        <option value="tiktok" <?= ($item['platform'] ?? '') == 'tiktok' ? 'selected' : '' ?>>TikTok Shop</option>
                                        <option value="shopee" <?= ($item['platform'] ?? '') == 'shopee' ? 'selected' : '' ?>>Shopee</option>
                                        <option value="lazada" <?= ($item['platform'] ?? '') == 'lazada' ? 'selected' : '' ?>>Lazada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="depth" class="font-weight-bold">Độ sâu nghiên cứu (Depth):</label>
                                    <select class="form-control form-control-sm" name="data[depth]" id="depth">
                                        <option value="QUICK" <?= ($item['depth'] ?? '') == 'QUICK' ? 'selected' : '' ?>>QUICK (Nhanh - Tiết kiệm Token)</option>
                                        <option value="STANDARD" <?= ($item['depth'] ?? '') == 'STANDARD' ? 'selected' : '' ?>>STANDARD (Tiêu chuẩn - Khuyên dùng)</option>
                                        <option value="DEEP" <?= ($item['depth'] ?? '') == 'DEEP' ? 'selected' : '' ?>>DEEP (Chuyên sâu - Đầy đủ góc phân tích)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="frequency" class="font-weight-bold">Tần suất tự động quét:</label>
                                    <select class="form-control form-control-sm" name="data[frequency]" id="frequency">
                                        <option value="manual" <?= ($item['frequency'] ?? '') == 'manual' ? 'selected' : '' ?>>Thủ công (Chỉ chạy khi nhấn)</option>
                                        <option value="daily" <?= ($item['frequency'] ?? '') == 'daily' ? 'selected' : '' ?>>Hàng ngày (Daily Cron)</option>
                                        <option value="weekly" <?= ($item['frequency'] ?? '') == 'weekly' ? 'selected' : '' ?>>Hàng tuần (Weekly Cron)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_results" class="font-weight-bold">Số lượng tối đa / lần quét:</label>
                                    <input type="number" min="1" max="50" class="form-control form-control-sm" name="data[max_results]" id="max_results" value="<?= $item['max_results'] ?? 10 ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="priority" class="font-weight-bold">Độ ưu tiên (1 - 100):</label>
                                    <input type="number" min="1" max="100" class="form-control form-control-sm" name="data[priority]" id="priority" value="<?= $item['priority'] ?? 10 ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status" class="font-weight-bold">Trạng thái:</label>
                                    <select class="form-control form-control-sm" name="data[status]" id="status">
                                        <option value="active" <?= ($item['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động (Active)</option>
                                        <option value="inactive" <?= ($item['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Tạm dừng (Inactive)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="<?= $linkSeeds ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Quay lại</a>
                        <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-4"><i class="fas fa-save mr-1"></i> Lưu Hạt Giống Nghiên Cứu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
