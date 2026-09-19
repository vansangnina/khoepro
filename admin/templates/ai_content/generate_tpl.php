<?php
$linkSaveGenerate = "index.php?com=ai_content&act=save_generate";
$linkMan = "index.php?com=ai_content&act=man";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="<?= $linkMan ?>">Kho nội dung AI</a></li>
                <li class="breadcrumb-item active">Tạo Nội Dung AI Mới (AI Content Generator)</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <form method="post" action="<?= $linkSaveGenerate ?>">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-success card-outline shadow-sm text-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-magic mr-1"></i> Trình Khởi Tạo Gói Nội Dung Đa Kênh AI (Multi-Channel Content Generator)
                        </h3>
                    </div>

                    <div class="card-body">
                        <!-- 1. Select Product -->
                        <div class="form-group row align-items-center mb-4">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                <i class="fas fa-box text-primary mr-1"></i> Chọn sản phẩm mục tiêu:
                            </label>
                            <div class="col-sm-9">
                                <select name="single_product_id" class="form-control select2" required>
                                    <option value="">-- Chọn một sản phẩm từ danh mục website --</option>
                                    <?php foreach ($products as $p) { ?>
                                        <option value="<?= $p['id'] ?>" <?= ($selectedProductId == $p['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['namevi']) ?> (SKU: <?= $p['code'] ?: 'N/A' ?> - <?= number_format($p['sale_price'] ?: $p['regular_price']) ?> VND)
                                        </option>
                                    <?php } ?>
                                </select>
                                <small class="text-muted">Hệ thống sẽ tự động trích xuất thông số kỹ thuật, dữ liệu nghiên cứu và ưu đãi affiliate để làm context chuẩn xác cho AI.</small>
                            </div>
                        </div>

                        <!-- 2. Content Types Checklist -->
                        <div class="form-group row mb-4">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                <i class="fas fa-layer-group text-success mr-1"></i> Các loại nội dung cần tạo:
                            </label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="type_analysis" name="content_types[]" value="product_analysis" checked>
                                            <label class="custom-control-label font-weight-bold" for="type_analysis">
                                                <i class="fas fa-brain text-info mr-1"></i> Phân tích sản phẩm (12 khía cạnh)
                                            </label>
                                            <div class="text-xs text-muted pl-3">Pros, Cons, Vấn đề giải quyết, Đối tượng phù hợp</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="type_hooks" name="content_types[]" value="tiktok_hooks" checked>
                                            <label class="custom-control-label font-weight-bold" for="type_hooks">
                                                <i class="fab fa-tiktok text-danger mr-1"></i> 7 Biến thể TikTok Hooks
                                            </label>
                                            <div class="text-xs text-muted pl-3">Problem, Mistake, Comparison, Curiosity, Demo, Buyer Warning, Value</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="type_script" name="content_types[]" value="tiktok_script" checked>
                                            <label class="custom-control-label font-weight-bold" for="type_script">
                                                <i class="fas fa-video text-dark mr-1"></i> Kịch bản TikTok + Video Shot Plan
                                            </label>
                                            <div class="text-xs text-muted pl-3">Kịch bản video ngắn phân cảnh chi tiết (Bridge Phase 06)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="type_seo" name="content_types[]" value="seo_content" checked>
                                            <label class="custom-control-label font-weight-bold" for="type_seo">
                                                <i class="fas fa-search text-success mr-1"></i> Gói SEO Metadata & FAQs
                                            </label>
                                            <div class="text-xs text-muted pl-3">Từ khóa chính/phụ, SEO Title, Meta Description, FAQ Schema</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="type_review" name="content_types[]" value="review_draft">
                                            <label class="custom-control-label font-weight-bold" for="type_review">
                                                <i class="fas fa-pen-nib text-primary mr-1"></i> Bản thảo bài viết đánh giá (Review Draft)
                                            </label>
                                            <div class="text-xs text-muted pl-3">Bài review chi tiết hoàn chỉnh cho website</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Tone & Style Settings -->
                        <div class="form-group row align-items-center mb-3">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                <i class="fas fa-bullhorn text-warning mr-1"></i> Tone giọng nội dung (Voice Tone):
                            </label>
                            <div class="col-sm-4">
                                <select name="tone" class="form-control">
                                    <option value="FITNADO_DEFAULT">FITNADO Chuẩn (Rõ ràng, Khách quan, Thực tế)</option>
                                    <option value="EDUCATIONAL">Giáo dục / Phân tích chuyên sâu (Educational)</option>
                                    <option value="DIRECT">Trực diện / Quyết đoán (Direct & Bold)</option>
                                    <option value="COMPARISON">Đối đầu / So sánh giá (Comparison Driven)</option>
                                    <option value="SHORT_VIDEO">Viral TikTok / Năng lượng cao (High Energy)</option>
                                </select>
                            </div>
                            <label class="col-sm-2 col-form-label font-weight-bold text-right">
                                Thời lượng Video:
                            </label>
                            <div class="col-sm-3">
                                <select name="target_duration" class="form-control">
                                    <option value="15">15 Giây (Siêu ngắn)</option>
                                    <option value="30" selected>30 Giây (Chuẩn TikTok)</option>
                                    <option value="45">45 Giây (Chi tiết vừa)</option>
                                    <option value="60">60 Giây (Review đầy đủ)</option>
                                </select>
                            </div>
                        </div>

                        <!-- 4. Angle Setting -->
                        <div class="form-group row align-items-center mb-4">
                            <label class="col-sm-3 col-form-label font-weight-bold">
                                <i class="fas fa-compass text-info mr-1"></i> Góc tiếp cận chính (Angle):
                            </label>
                            <div class="col-sm-9">
                                <select name="content_angle" class="form-control">
                                    <option value="Problem/Solution">Giải quyết vấn đề / Nỗi đau thể lực (Problem / Solution)</option>
                                    <option value="Review">Đánh giá khách quan ưu nhược điểm (Real Review)</option>
                                    <option value="Comparison">So sánh đối đầu với hàng giá rẻ (Comparison vs Budget)</option>
                                    <option value="Mistakes">Cảnh báo sai lầm khi tập luyện (Mistakes to Avoid)</option>
                                    <option value="Beginner Guide">Hướng dẫn chọn mua cho người mới (Beginner Guide)</option>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-light border p-3 text-xs mb-0">
                            <strong><i class="fas fa-shield-alt text-success mr-1"></i> Cam kết An toàn Dữ liệu:</strong>
                            Hệ thống tự động kích hoạt <strong>Quality Gate</strong> sau khi tạo. Toàn bộ nội dung tạo ra đều phải qua kiểm duyệt của Admin trước khi áp dụng vào website.
                        </div>
                    </div>

                    <div class="card-footer bg-light d-flex justify-content-between">
                        <a href="<?= $linkMan ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Quay lại thư viện</a>
                        <button type="submit" class="btn btn-success font-weight-bold">
                            <i class="fas fa-play mr-1"></i> Bắt Đầu Tạo Nội Dung AI (Generate Content)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
