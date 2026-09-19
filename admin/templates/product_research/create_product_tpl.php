<?php
$linkSaveProduct = "index.php?com=product_research&act=save_product";
$linkBack = "index.php?com=product_research&act=edit&id=" . ($item['id'] ?? 0);
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="index.php?com=product_research&act=man">Nghiên cứu sản phẩm</a></li>
                <li class="breadcrumb-item"><a href="<?= $linkBack ?>">Ứng viên #<?= $item['id'] ?></a></li>
                <li class="breadcrumb-item active">Ánh xạ & Tạo Sản phẩm Website</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <?php if (!empty($duplicateWarning) && $duplicateWarning['is_duplicate']) { ?>
        <div class="alert alert-warning alert-dismissible shadow-sm">
            <h5><i class="icon fas fa-exclamation-triangle"></i> Cảnh báo Trùng lặp tiềm ẩn!</h5>
            <?= htmlspecialchars($duplicateWarning['message']) ?>
            <div class="text-xs mt-1 text-muted">Vui lòng kiểm tra lại để tránh tạo sản phẩm trùng lặp trên website.</div>
        </div>
    <?php } ?>

    <form method="post" action="<?= $linkSaveProduct ?>">
        <input type="hidden" name="id" value="<?= $item['id'] ?>">

        <div class="row">
            <!-- Left: Candidate Snapshot -->
            <div class="col-lg-5">
                <div class="card card-secondary card-outline shadow-sm text-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-microscope mr-1"></i> Dữ liệu Ứng viên Nghiên cứu (#<?= $item['id'] ?>)</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <?php if (!empty($item['image_url'])) { ?>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" class="rounded mr-3 border" style="width:64px;height:64px;object-fit:cover;" onerror="this.src='assets/images/noimage.png'">
                            <?php } ?>
                            <div>
                                <h6 class="font-weight-bold text-primary mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                <span class="badge badge-light border text-uppercase mr-1"><i class="fas fa-tag mr-1"></i><?= htmlspecialchars($item['platform']) ?></span>
                                <span class="badge badge-success font-weight-bold">Score: <?= $item['total_score'] ?? 'N/A' ?>/100</span>
                            </div>
                        </div>

                        <table class="table table-bordered table-sm text-xs mb-3">
                            <tbody>
                                <tr>
                                    <th width="40%" class="bg-light">Giá nghiên cứu:</th>
                                    <td><strong><?= ($item['price'] !== null) ? number_format($item['price'], 0, ',', '.') . 'đ' : 'Chưa rõ' ?></strong></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Lượt bán / Rating:</th>
                                    <td><?= number_format($item['sales_count'] ?? 0) ?> đã bán | ⭐ <?= number_format($item['rating'] ?? 0, 1) ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Tỷ lệ hoa hồng:</th>
                                    <td><span class="text-success font-weight-bold"><?= $item['commission_rate'] ?? 0 ?>%</span> (~<?= number_format($item['commission_value'] ?? 0, 0, ',', '.') ?>đ)</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Gợi ý Danh mục:</th>
                                    <td><?= htmlspecialchars($item['category_hint'] ?: 'Chưa có') ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Gợi ý Thương hiệu:</th>
                                    <td><?= htmlspecialchars($item['brand_hint'] ?: 'Chưa có') ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Liên kết nguồn:</th>
                                    <td><a href="<?= htmlspecialchars($item['source_url']) ?>" target="_blank" class="text-info text-truncate d-block" style="max-width:200px;"><i class="fas fa-external-link-alt mr-1"></i><?= htmlspecialchars($item['source_url']) ?></a></td>
                                </tr>
                            </tbody>
                        </table>

                        <?php if (!empty($item['problem_solved'])) { ?>
                            <div class="callout callout-info p-2 text-xs mb-2">
                                <strong>Pain Point / Vấn đề giải quyết:</strong>
                                <div><?= nl2br(htmlspecialchars($item['problem_solved'])) ?></div>
                            </div>
                        <?php } ?>

                        <?php if (!empty($item['target_audience'])) { ?>
                            <div class="text-xs text-muted">
                                <strong>Đối tượng phù hợp:</strong> <?= htmlspecialchars($item['target_audience']) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Right: Target Product Mapping Form -->
            <div class="col-lg-7">
                <div class="card card-primary card-outline shadow-sm text-sm">
                    <div class="card-header bg-light">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-cogs mr-1"></i> Ánh xạ sang Bảng Sản phẩm (table_product)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning p-2 text-xs mb-3">
                            <i class="fas fa-shield-alt mr-1"></i> <strong>Quy tắc an toàn:</strong> Sản phẩm tạo từ ứng viên sẽ được lưu dưới dạng <strong>BẢN NHÁP (Draft - Chưa xuất bản / Không có cờ hienthi)</strong>. Bạn cần cập nhật ảnh và hoàn thiện nội dung trước khi công khai trên website.
                        </div>

                        <div class="form-group">
                            <label for="namevi" class="font-weight-bold">Tên sản phẩm chính thức: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="namevi" id="namevi" value="<?= htmlspecialchars($item['name']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="slugvi" class="font-weight-bold">Đường dẫn thân thiện (Slug):</label>
                            <input type="text" class="form-control form-control-sm" name="slugvi" id="slugvi" placeholder="Tự động tạo nếu để trống...">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_list" class="font-weight-bold">Danh mục sản phẩm cấp 1 (Category): <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" name="id_list" id="id_list" required>
                                        <option value="">-- Chọn danh mục thật --</option>
                                        <?php if (!empty($categories)) {
                                            foreach ($categories as $cat) { ?>
                                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['namevi']) ?></option>
                                        <?php } } ?>
                                    </select>
                                    <small class="text-muted">Gợi ý từ nghiên cứu: <strong><?= htmlspecialchars($item['category_hint'] ?: 'Chưa có') ?></strong></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_brand" class="font-weight-bold">Thương hiệu (Brand):</label>
                                    <select class="form-control form-control-sm" name="id_brand" id="id_brand">
                                        <option value="0">-- Chọn thương hiệu thật (hoặc để trống) --</option>
                                        <?php if (!empty($brands)) {
                                            foreach ($brands as $br) { ?>
                                                <option value="<?= $br['id'] ?>"><?= htmlspecialchars($br['namevi']) ?></option>
                                        <?php } } ?>
                                    </select>
                                    <small class="text-muted">Gợi ý từ nghiên cứu: <strong><?= htmlspecialchars($item['brand_hint'] ?: 'Chưa có') ?></strong></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="regular_price" class="font-weight-bold">Giá niêm yết (Regular Price):</label>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm" name="regular_price" id="regular_price" value="<?= $item['price'] ?? 0 ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sale_price" class="font-weight-bold">Giá khuyến mãi (Sale Price):</label>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm" name="sale_price" id="sale_price" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descvi" class="font-weight-bold">Mô tả ngắn / Vấn đề giải quyết:</label>
                            <textarea class="form-control form-control-sm" name="descvi" id="descvi" rows="3"><?= htmlspecialchars($item['problem_solved'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="specs" class="font-weight-bold">Thông số kỹ thuật / Ghi chú đặc tính:</label>
                            <textarea class="form-control form-control-sm" name="specs" id="specs" rows="3"><?= htmlspecialchars($item['research_notes'] ?? '') ?></textarea>
                        </div>

                        <!-- Initial Affiliate Offer -->
                        <div class="card card-outline card-success shadow-none border mb-3">
                            <div class="card-header p-2">
                                <h6 class="card-title font-weight-bold text-success text-xs mb-0"><i class="fas fa-link mr-1"></i> Khởi tạo Ưu đãi Affiliate Ban đầu (table_product_affiliate)</h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="form-group mb-0">
                                    <label for="affiliate_url" class="font-weight-bold text-xs">Liên kết Affiliate / Nguồn:</label>
                                    <input type="url" class="form-control form-control-sm" name="affiliate_url" id="affiliate_url" value="<?= htmlspecialchars($item['source_url'] ?? '') ?>">
                                    <small class="text-muted">Hệ thống sẽ tự động tạo bản ghi ưu đãi ban đầu trên nền tảng <strong><?= strtoupper($item['platform']) ?></strong> với hoa hồng <strong><?= $item['commission_rate'] ?? 0 ?>%</strong>.</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="<?= $linkBack ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Quay lại</a>
                            <button type="submit" class="btn btn-success btn-sm font-weight-bold px-4 shadow-sm" onclick="return confirm('Xác nhận tạo sản phẩm nháp trong table_product từ ứng viên này?')">
                                <i class="fas fa-check mr-1"></i> Xác nhận Tạo Sản Phẩm Nháp
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
