<?php
$linkSave = "index.php?com=product_research&act=save";
$linkMan = "index.php?com=product_research&act=man";
$isEdit = !empty($item['id']);
$scoreBreakdown = !empty($item['score_breakdown']) ? json_decode($item['score_breakdown'], true) : null;
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="<?= $linkMan ?>">Nghiên cứu sản phẩm</a></li>
                <li class="breadcrumb-item active"><?= $isEdit ? 'Chỉnh sửa / Đánh giá ứng viên #' . $item['id'] : 'Thêm mới ứng viên' ?></li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <?php if (!empty($duplicateWarning) && $duplicateWarning['is_duplicate']) { ?>
        <div class="alert alert-<?= $duplicateWarning['severity'] ?? 'warning' ?> alert-dismissible shadow-sm">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-exclamation-triangle"></i> Cảnh báo Trùng lặp (Duplicate Detection)!</h5>
            <?= htmlspecialchars($duplicateWarning['message']) ?>
            <?php if (!empty($duplicateWarning['matched_id'])) { ?>
                <div class="mt-2">
                    <a href="index.php?com=product_research&act=edit&id=<?= $duplicateWarning['matched_id'] ?>" class="btn btn-sm btn-light font-weight-bold" target="_blank">
                        <i class="fas fa-external-link-alt mr-1"></i> Xem ứng viên trùng lặp (#<?= $duplicateWarning['matched_id'] ?>)
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if (!empty($linkedProduct)) { ?>
        <div class="alert alert-info alert-dismissible shadow-sm">
            <h5><i class="icon fas fa-check-double"></i> Đã tạo Sản phẩm Website!</h5>
            Ứng viên này đã được liên kết với sản phẩm: <strong><?= htmlspecialchars($linkedProduct['namevi']) ?></strong> (ID #<?= $linkedProduct['id'] ?>).
            <div class="mt-2">
                <a href="index.php?com=product&act=edit&type=san-pham&id=<?= $linkedProduct['id'] ?>" class="btn btn-sm btn-primary" target="_blank">
                    <i class="fas fa-box-open mr-1"></i> Quản lý sản phẩm trong Product Admin
                </a>
            </div>
        </div>
    <?php } ?>

    <form method="post" action="<?= $linkSave ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $item['id'] ?? 0 ?>">

        <div class="row">
            <!-- Left Column: Research Form -->
            <div class="col-lg-8">
                <!-- Basic Identifiers -->
                <div class="card card-primary card-outline shadow-sm text-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-info-circle mr-1"></i> 1. Thông tin Nhận diện & Nguồn gốc</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Tên sản phẩm ứng viên: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="data[name]" id="name" placeholder="Ví dụ: Dây kháng lực ngũ sắc FITPRO Resistance Bands" value="<?= htmlspecialchars($item['name'] ?? '') ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="platform" class="font-weight-bold">Nền tảng phát hiện (Platform): <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" name="data[platform]" id="platform" required>
                                        <option value="tiktok" <?= ($item['platform'] ?? '') == 'tiktok' ? 'selected' : '' ?>>TikTok Shop</option>
                                        <option value="shopee" <?= ($item['platform'] ?? '') == 'shopee' ? 'selected' : '' ?>>Shopee</option>
                                        <option value="lazada" <?= ($item['platform'] ?? '') == 'lazada' ? 'selected' : '' ?>>Lazada</option>
                                        <option value="brand" <?= ($item['platform'] ?? '') == 'brand' ? 'selected' : '' ?>>Brand Website</option>
                                        <option value="google" <?= ($item['platform'] ?? '') == 'google' ? 'selected' : '' ?>>Google Search / SEO</option>
                                        <option value="manual" <?= ($item['platform'] ?? '') == 'manual' ? 'selected' : '' ?>>Thủ công (Manual)</option>
                                        <option value="other" <?= ($item['platform'] ?? '') == 'other' ? 'selected' : '' ?>>Khác</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="external_product_id" class="font-weight-bold">ID sản phẩm ngoài (External ID):</label>
                                    <input type="text" class="form-control form-control-sm" name="data[external_product_id]" id="external_product_id" placeholder="Ví dụ: 172938491829" value="<?= htmlspecialchars($item['external_product_id'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="brand_hint" class="font-weight-bold">Thương hiệu gợi ý (Brand):</label>
                                    <input type="text" class="form-control form-control-sm" name="data[brand_hint]" id="brand_hint" placeholder="Ví dụ: Aolikes, Myprotein..." value="<?= htmlspecialchars($item['brand_hint'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="source_url" class="font-weight-bold">Liên kết nguồn (Source URL): <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="url" class="form-control" name="data[source_url]" id="source_url" placeholder="https://..." value="<?= htmlspecialchars($item['source_url'] ?? '') ?>" required>
                                <?php if (!empty($item['source_url'])) { ?>
                                    <div class="input-group-append">
                                        <a href="<?= htmlspecialchars($item['source_url']) ?>" target="_blank" class="btn btn-outline-info"><i class="fas fa-external-link-alt"></i> Mở link</a>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if (!empty($item['normalized_url'])) { ?>
                                <small class="text-muted d-block mt-1"><i class="fas fa-link mr-1"></i><strong>Normalized URL:</strong> <?= htmlspecialchars($item['normalized_url']) ?></small>
                            <?php } ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_hint" class="font-weight-bold">Danh mục gợi ý (Category Hint):</label>
                                    <input type="text" class="form-control form-control-sm" name="data[category_hint]" id="category_hint" placeholder="Ví dụ: Phụ kiện Gym, Đai lưng..." value="<?= htmlspecialchars($item['category_hint'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image_url" class="font-weight-bold">Ảnh đại diện (Image URL):</label>
                                    <input type="url" class="form-control form-control-sm" name="data[image_url]" id="image_url" placeholder="https://..." value="<?= htmlspecialchars($item['image_url'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Market & Financial Metrics -->
                <div class="card card-success card-outline shadow-sm text-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-chart-line mr-1"></i> 2. Chỉ số Thị trường & Tài chính (Market Signals)</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price" class="font-weight-bold">Giá bán hiện tại (VND):</label>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm" name="data[price]" id="price" placeholder="199000" value="<?= $item['price'] !== null ? $item['price'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="original_price" class="font-weight-bold">Giá gốc niêm yết (VND):</label>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm" name="data[original_price]" id="original_price" placeholder="299000" value="<?= $item['original_price'] !== null ? $item['original_price'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sales_count" class="font-weight-bold">Lượt đã bán (Sales Count):</label>
                                    <input type="number" min="0" class="form-control form-control-sm" name="data[sales_count]" id="sales_count" placeholder="5200" value="<?= $item['sales_count'] !== null ? $item['sales_count'] : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rating" class="font-weight-bold">Điểm đánh giá (Rating 0 - 5.0):</label>
                                    <input type="number" step="0.1" min="0" max="5" class="form-control form-control-sm" name="data[rating]" id="rating" placeholder="4.8" value="<?= $item['rating'] !== null ? $item['rating'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="review_count" class="font-weight-bold">Số lượng đánh giá:</label>
                                    <input type="number" min="0" class="form-control form-control-sm" name="data[review_count]" id="review_count" placeholder="1420" value="<?= $item['review_count'] !== null ? $item['review_count'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="estimated_gmv" class="font-weight-bold">Ước tính GMV (Doanh số):</label>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm" name="data[estimated_gmv]" id="estimated_gmv" placeholder="500000000" value="<?= $item['estimated_gmv'] !== null ? $item['estimated_gmv'] : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="commission_rate" class="font-weight-bold text-success">Tỷ lệ hoa hồng (%):</label>
                                    <input type="number" step="0.1" min="0" class="form-control form-control-sm" name="data[commission_rate]" id="commission_rate" placeholder="12.5" value="<?= $item['commission_rate'] !== null ? $item['commission_rate'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="commission_value" class="font-weight-bold text-success">Hoa hồng ước tính mỗi đơn (VND):</label>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm" name="data[commission_value]" id="commission_value" placeholder="25000" value="<?= $item['commission_value'] !== null ? $item['commission_value'] : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Potential & Video Signals -->
                <div class="card card-info card-outline shadow-sm text-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-video mr-1"></i> 3. Tiềm năng Nội dung & Video (Content Signals)</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="top_video_views" class="font-weight-bold">Lượt xem video cao nhất (Top Views):</label>
                                    <input type="number" min="0" class="form-control form-control-sm" name="data[top_video_views]" id="top_video_views" placeholder="1200000" value="<?= $item['top_video_views'] !== null ? $item['top_video_views'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="video_count" class="font-weight-bold">Số lượng video sản phẩm:</label>
                                    <input type="number" min="0" class="form-control form-control-sm" name="data[video_count]" id="video_count" placeholder="45" value="<?= $item['video_count'] !== null ? $item['video_count'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="creator_count" class="font-weight-bold">Số lượng Creator / KOC đã làm:</label>
                                    <input type="number" min="0" class="form-control form-control-sm" name="data[creator_count]" id="creator_count" placeholder="18" value="<?= $item['creator_count'] !== null ? $item['creator_count'] : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="problem_solved" class="font-weight-bold">Vấn đề sản phẩm giải quyết (Pain Point & Giải pháp):</label>
                            <textarea class="form-control form-control-sm" name="data[problem_solved]" id="problem_solved" rows="3" placeholder="Ví dụ: Giúp người tập tại nhà tự tập đủ các nhóm cơ lưng, vai, tay mà không cần máy tạ cồng kềnh..."><?= htmlspecialchars($item['problem_solved'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="target_audience" class="font-weight-bold">Khách hàng mục tiêu (Target Audience):</label>
                            <input type="text" class="form-control form-control-sm" name="data[target_audience]" id="target_audience" placeholder="Ví dụ: Người mới tập Gym, tập tại nhà (Home Workout), nhân viên văn phòng..." value="<?= htmlspecialchars($item['target_audience'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- SEO, Competition & Notes -->
                <div class="card card-warning card-outline shadow-sm text-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-search-location mr-1"></i> 4. Cơ hội Cạnh tranh & SEO</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="primary_keyword" class="font-weight-bold">Từ khóa chính SEO (Primary Keyword):</label>
                                    <input type="text" class="form-control form-control-sm" name="data[primary_keyword]" id="primary_keyword" placeholder="Ví dụ: day khang luc tap gym tai nha" value="<?= htmlspecialchars($item['primary_keyword'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="competition_score" class="font-weight-bold">Điểm cơ hội (0 - 100):</label>
                                    <input type="number" min="0" max="100" class="form-control form-control-sm" name="data[competition_score]" id="competition_score" placeholder="75" value="<?= $item['competition_score'] !== null ? $item['competition_score'] : '' ?>">
                                    <small class="text-muted">Điểm cao = ít bão hòa</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="seo_score" class="font-weight-bold">Điểm SEO (0 - 100):</label>
                                    <input type="number" min="0" max="100" class="form-control form-control-sm" name="data[seo_score]" id="seo_score" placeholder="80" value="<?= $item['seo_score'] !== null ? $item['seo_score'] : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="research_notes" class="font-weight-bold">Ghi chú nghiên cứu bổ sung:</label>
                            <textarea class="form-control form-control-sm" name="data[research_notes]" id="research_notes" rows="3" placeholder="Nhập ghi chú thêm về chất liệu, nguồn hàng, phản hồi người mua..."><?= htmlspecialchars($item['research_notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Phase 04: AI Research Analysis -->
                <?php if (!empty($item['ai_analysis'])) {
                    $ai = json_decode($item['ai_analysis'], true);
                    if (is_array($ai)) { ?>
                        <div class="card card-purple card-outline shadow-sm text-sm" style="border-top-color: #6f42c1;">
                            <div class="card-header bg-light">
                                <h3 class="card-title font-weight-bold text-purple" style="color: #6f42c1;"><i class="fas fa-robot mr-1"></i> 5. Phân tích Chuyên sâu bởi AI Agent (AI Research Insights)</h3>
                                <div class="card-tools">
                                    <span class="badge badge-purple" style="background-color: #6f42c1; color: #fff;">Độ tin cậy AI: <?= $item['ai_confidence'] ?? 85 ?>%</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($ai['content_angles']) && is_array($ai['content_angles'])) { ?>
                                    <div class="mb-3">
                                        <strong class="text-dark d-block mb-1"><i class="fas fa-bullhorn text-info mr-1"></i> Các góc tiếp cận nội dung (Content Angles):</strong>
                                        <ul class="pl-3 mb-0 text-xs">
                                            <?php foreach ($ai['content_angles'] as $angle) { ?>
                                                <li><?= htmlspecialchars($angle) ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="p-2 rounded bg-light border text-xs">
                                            <strong>Tiềm năng làm Video Visual/Demo:</strong>
                                            <span class="badge badge-<?= (($ai['visual_demo_potential'] ?? '') == 'HIGH') ? 'success' : 'secondary' ?> ml-1"><?= $ai['visual_demo_potential'] ?? 'MEDIUM' ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2 rounded bg-light border text-xs">
                                            <strong>Tiềm năng So sánh Đối đầu (Vs):</strong>
                                            <span class="badge badge-<?= (($ai['comparison_potential'] ?? '') == 'HIGH') ? 'primary' : 'secondary' ?> ml-1"><?= $ai['comparison_potential'] ?? 'MEDIUM' ?></span>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($ai['risk_notes'])) { ?>
                                    <div class="alert alert-warning p-2 text-xs mb-0">
                                        <strong><i class="fas fa-exclamation-circle mr-1"></i> Lưu ý rủi ro / nhược điểm:</strong>
                                        <div><?= htmlspecialchars($ai['risk_notes']) ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                <?php } } ?>

                <!-- Phase 04: Evidence Records -->
                <?php if (!empty($evidences)) { ?>
                    <div class="card card-outline card-secondary shadow-sm text-sm">
                        <div class="card-header bg-light">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-fingerprint mr-1"></i> 6. Bằng chứng & Nguồn gốc Dữ liệu (Evidence Provenance)</h3>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-sm table-striped text-xs mb-0">
                                <thead>
                                    <tr>
                                        <th>Trường dữ liệu</th>
                                        <th>Giá trị ghi nhận</th>
                                        <th>Loại bằng chứng</th>
                                        <th>Nguồn / Provider</th>
                                        <th>Thời gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($evidences as $ev) { ?>
                                        <tr>
                                            <td class="font-weight-bold"><?= htmlspecialchars($ev['field_name']) ?></td>
                                            <td><code><?= htmlspecialchars($ev['field_value']) ?></code></td>
                                            <td><span class="badge badge-<?= ($ev['evidence_type'] == 'FACT') ? 'success' : 'info' ?>"><?= $ev['evidence_type'] ?></span></td>
                                            <td><?= htmlspecialchars($ev['provider']) ?></td>
                                            <td class="text-muted"><?= date('d/m/Y H:i', $ev['captured_at']) ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Right Column: Score Summary & Action Panel -->
            <div class="col-lg-4">
                <!-- Score Breakdown Card -->
                <div class="card card-primary card-outline shadow-sm text-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-calculator mr-1"></i> Điểm Nghiên cứu (Score)</h3>
                        <?php if ($isEdit) { ?>
                            <a href="index.php?com=product_research&act=recalculate&id=<?= $item['id'] ?>" class="btn btn-xs btn-outline-primary" title="Tính toán lại toàn bộ điểm">
                                <i class="fas fa-sync-alt mr-1"></i> Tính lại
                            </a>
                        <?php } ?>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="display-4 font-weight-bold text-primary">
                                <?= ($item['total_score'] !== null) ? $item['total_score'] : '--' ?>
                            </div>
                            <span class="badge badge-pill badge-secondary px-3 py-1">TỔNG ĐIỂM TIỀM NĂNG / 100</span>
                            <div class="text-xs text-muted mt-1">Tín hiệu ưu tiên nghiên cứu (Research Priority Signal)</div>
                        </div>

                        <hr>

                        <!-- Sub Score Bars -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between text-xs font-weight-bold mb-1">
                                <span>Nhu cầu thị trường (Demand):</span>
                                <span class="text-primary"><?= ($item['demand_score'] !== null) ? $item['demand_score'] . '/100' : 'NULL' ?></span>
                            </div>
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-primary" style="width: <?= (float)($item['demand_score'] ?? 0) ?>%"></div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="d-flex justify-content-between text-xs font-weight-bold mb-1">
                                <span>Tiềm năng nội dung (Content):</span>
                                <span class="text-info"><?= ($item['content_score'] !== null) ? $item['content_score'] . '/100' : 'NULL' ?></span>
                            </div>
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-info" style="width: <?= (float)($item['content_score'] ?? 0) ?>%"></div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="d-flex justify-content-between text-xs font-weight-bold mb-1">
                                <span>Tiềm năng hoa hồng (Commission):</span>
                                <span class="text-success"><?= ($item['commission_score'] !== null) ? $item['commission_score'] . '/100' : 'NULL' ?></span>
                            </div>
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-success" style="width: <?= (float)($item['commission_score'] ?? 0) ?>%"></div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="d-flex justify-content-between text-xs font-weight-bold mb-1">
                                <span>Cơ hội cạnh tranh (Competition):</span>
                                <span class="text-warning"><?= ($item['competition_score'] !== null) ? $item['competition_score'] . '/100' : 'NULL' ?></span>
                            </div>
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-warning" style="width: <?= (float)($item['competition_score'] ?? 0) ?>%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between text-xs font-weight-bold mb-1">
                                <span>Tiềm năng SEO:</span>
                                <span class="text-secondary"><?= ($item['seo_score'] !== null) ? $item['seo_score'] . '/100' : 'NULL' ?></span>
                            </div>
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-secondary" style="width: <?= (float)($item['seo_score'] ?? 0) ?>%"></div>
                            </div>
                        </div>

                        <?php if (!empty($scoreBreakdown['reasons'])) { ?>
                            <div class="callout callout-info p-2 text-xs">
                                <h6 class="font-weight-bold mb-1"><i class="fas fa-lightbulb mr-1"></i> Diễn giải điểm số:</h6>
                                <ul class="pl-3 mb-0">
                                    <?php foreach ($scoreBreakdown['reasons'] as $r) { ?>
                                        <li><?= htmlspecialchars($r) ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Status & Lifecycle Management -->
                <div class="card card-secondary card-outline shadow-sm text-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-tasks mr-1"></i> Trạng thái & Quy trình</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status" class="font-weight-bold">Trạng thái hiện tại:</label>
                            <select class="form-control form-control-sm" name="data[status]" id="status">
                                <option value="DISCOVERED" <?= ($item['status'] ?? '') == 'DISCOVERED' ? 'selected' : '' ?>>DISCOVERED (Mới phát hiện)</option>
                                <option value="RESEARCHED" <?= ($item['status'] ?? '') == 'RESEARCHED' ? 'selected' : '' ?>>RESEARCHED (Đã hoàn tất nghiên cứu)</option>
                                <option value="APPROVED" <?= ($item['status'] ?? '') == 'APPROVED' ? 'selected' : '' ?>>APPROVED (Đã duyệt chấp thuận)</option>
                                <option value="REJECTED" <?= ($item['status'] ?? '') == 'REJECTED' ? 'selected' : '' ?>>REJECTED (Từ chối)</option>
                                <?php if (($item['status'] ?? '') == 'PRODUCT_CREATED') { ?>
                                    <option value="PRODUCT_CREATED" selected>PRODUCT_CREATED (Đã tạo SP)</option>
                                <?php } ?>
                            </select>
                        </div>

                        <?php if (!empty($item['reject_reason'])) { ?>
                            <div class="alert alert-danger p-2 text-xs">
                                <strong>Lý do từ chối:</strong> <?= htmlspecialchars($item['reject_reason']) ?>
                            </div>
                        <?php } ?>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-block font-weight-bold"><i class="fas fa-save mr-1"></i> Lưu thông tin ứng viên</button>
                        </div>

                        <?php if ($isEdit) { ?>
                            <div class="d-flex justify-content-between mt-2" style="gap:5px;">
                                <?php if ($item['status'] !== 'APPROVED' && empty($item['id_product'])) { ?>
                                    <a href="index.php?com=product_research&act=approve&id=<?= $item['id'] ?>" class="btn btn-success btn-sm flex-grow-1" onclick="return confirm('Duyệt ứng viên này sang trạng thái APPROVED?')">
                                        <i class="fas fa-check mr-1"></i> Duyệt (Approve)
                                    </a>
                                <?php } ?>

                                <?php if ($item['status'] !== 'REJECTED' && empty($item['id_product'])) { ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm flex-grow-1" onclick="openRejectModal(<?= $item['id'] ?>)">
                                        <i class="fas fa-ban mr-1"></i> Từ chối
                                    </button>
                                <?php } ?>
                            </div>

                            <?php if ($item['status'] == 'APPROVED' && empty($item['id_product'])) { ?>
                                <div class="mt-3">
                                    <a href="index.php?com=product_research&act=create_product&id=<?= $item['id'] ?>" class="btn btn-warning btn-block font-weight-bold text-dark shadow-sm">
                                        <i class="fas fa-magic mr-1"></i> Tiến hành Tạo Sản phẩm (table_product)
                                    </a>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <!-- History Audit Card -->
                <?php if (!empty($item['history'])) {
                    $hist = json_decode($item['history'], true);
                    if (is_array($hist)) { ?>
                        <div class="card card-outline card-secondary shadow-sm text-sm">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold"><i class="fas fa-history mr-1"></i> Lịch sử thay đổi (Audit)</h3>
                            </div>
                            <div class="card-body p-2">
                                <ul class="timeline timeline-inverse mb-0 pl-2">
                                    <?php foreach (array_reverse($hist) as $h) { ?>
                                        <li class="mb-1 text-xs">
                                            <span class="font-weight-bold text-primary"><?= htmlspecialchars($h['action'] ?? '') ?></span>
                                            <?php if (!empty($h['reason'])) { ?>
                                                <span class="text-danger">- <?= htmlspecialchars($h['reason']) ?></span>
                                            <?php } ?>
                                            <div class="text-muted"><?= htmlspecialchars($h['date'] ?? '') ?></div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                <?php } } ?>
            </div>
        </div>
    </form>
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
function openRejectModal(id) {
    document.getElementById('reject_candidate_id').value = id;
    $('#rejectModal').modal('show');
}
</script>
