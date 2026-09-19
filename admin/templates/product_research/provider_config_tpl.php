<?php
$linkSaveConfig = "index.php?com=product_research&act=save_provider_config";
$linkMan = "index.php?com=product_research&act=man";
$geminiMasked = !empty($aiConfig['gemini_api_key']) ? substr($aiConfig['gemini_api_key'], 0, 6) . '************************' : '';
$openaiMasked = !empty($aiConfig['openai_api_key']) ? substr($aiConfig['openai_api_key'], 0, 6) . '************************' : '';
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="<?= $linkMan ?>">Nghiên cứu sản phẩm</a></li>
                <li class="breadcrumb-item active">Cấu hình AI Research Agent & Nhà cung cấp API</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="post" action="<?= $linkSaveConfig ?>">
                <div class="card card-primary card-outline shadow-sm text-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-robot mr-1"></i> Cấu hình Động cơ AI & Nhà cung cấp Dữ liệu (AI & Provider Settings)</h3>
                    </div>
                    <div class="card-body">
                        <!-- Usage Tracker Badge -->
                        <div class="alert alert-info p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="font-weight-bold mb-1"><i class="fas fa-tachometer-alt mr-1"></i> Hạn ngạch gọi AI hôm nay:</h6>
                                    <div class="text-xs">Đã sử dụng: <strong><?= $aiConfig['requests_today'] ?? 0 ?></strong> / <strong><?= $aiConfig['daily_request_limit'] ?? 50 ?></strong> requests</div>
                                </div>
                                <span class="badge badge-light border px-3 py-2 font-weight-bold">
                                    Provider hiện tại: <?= strtoupper($aiConfig['active_provider'] ?? 'mock') ?>
                                </span>
                            </div>
                        </div>

                        <!-- Active Provider Selection -->
                        <div class="form-group">
                            <label for="active_provider" class="font-weight-bold">Động cơ AI hoạt động chính: <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" name="active_provider" id="active_provider" onchange="toggleProviderSettings(this.value)">
                                <option value="mock" <?= ($aiConfig['active_provider'] ?? '') == 'mock' ? 'selected' : '' ?>>Môi trường giả lập / Test (Mock AI Engine - Miễn phí không cần API Key)</option>
                                <option value="gemini" <?= ($aiConfig['active_provider'] ?? '') == 'gemini' ? 'selected' : '' ?>>Google Gemini AI (Gemini 1.5 Flash / Pro)</option>
                                <option value="openai" <?= ($aiConfig['active_provider'] ?? '') == 'openai' ? 'selected' : '' ?>>OpenAI Compatible (GPT-4o Mini / GPT-4o)</option>
                            </select>
                            <small class="text-muted">Chọn nhà cung cấp mô hình ngôn ngữ lớn để phân tích thị trường và khám phá sản phẩm.</small>
                        </div>

                        <hr>

                        <!-- Google Gemini Configuration -->
                        <div id="section_gemini" class="p-3 bg-light rounded border mb-3">
                            <h6 class="font-weight-bold text-primary mb-3"><i class="fab fa-google mr-1"></i> Cấu hình Google Gemini AI</h6>
                            <div class="form-group">
                                <label for="gemini_api_key" class="font-weight-bold">Gemini API Key:</label>
                                <input type="password" class="form-control form-control-sm" name="gemini_api_key" id="gemini_api_key" placeholder="AIzaSy..." value="<?= htmlspecialchars($geminiMasked) ?>">
                                <small class="text-muted">Khóa API được mã hóa an toàn và ẩn trên giao diện.</small>
                            </div>
                            <div class="form-group mb-0">
                                <label for="gemini_model" class="font-weight-bold">Mô hình Gemini (Model):</label>
                                <select class="form-control form-control-sm" name="gemini_model" id="gemini_model">
                                    <option value="gemini-1.5-flash" <?= ($aiConfig['gemini_model'] ?? '') == 'gemini-1.5-flash' ? 'selected' : '' ?>>Gemini 1.5 Flash (Tốc độ cao, tối ưu chi phí - Khuyên dùng)</option>
                                    <option value="gemini-1.5-pro" <?= ($aiConfig['gemini_model'] ?? '') == 'gemini-1.5-pro' ? 'selected' : '' ?>>Gemini 1.5 Pro (Phân tích chuyên sâu cao cấp)</option>
                                </select>
                            </div>
                        </div>

                        <!-- OpenAI Configuration -->
                        <div id="section_openai" class="p-3 bg-light rounded border mb-3">
                            <h6 class="font-weight-bold text-success mb-3"><i class="fas fa-brain mr-1"></i> Cấu hình OpenAI Compatible</h6>
                            <div class="form-group">
                                <label for="openai_api_key" class="font-weight-bold">OpenAI API Key:</label>
                                <input type="password" class="form-control form-control-sm" name="openai_api_key" id="openai_api_key" placeholder="sk-proj-..." value="<?= htmlspecialchars($openaiMasked) ?>">
                            </div>
                            <div class="form-group mb-0">
                                <label for="openai_model" class="font-weight-bold">Mô hình OpenAI (Model):</label>
                                <select class="form-control form-control-sm" name="openai_model" id="openai_model">
                                    <option value="gpt-4o-mini" <?= ($aiConfig['openai_model'] ?? '') == 'gpt-4o-mini' ? 'selected' : '' ?>>GPT-4o Mini (Tiết kiệm, tốc độ cao)</option>
                                    <option value="gpt-4o" <?= ($aiConfig['openai_model'] ?? '') == 'gpt-4o' ? 'selected' : '' ?>>GPT-4o (Đỉnh cao lý luận)</option>
                                </select>
                            </div>
                        </div>

                        <!-- General Automation Limits -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="daily_request_limit" class="font-weight-bold">Giới hạn gọi AI tối đa mỗi ngày:</label>
                                    <input type="number" min="5" max="500" class="form-control form-control-sm" name="daily_request_limit" id="daily_request_limit" value="<?= $aiConfig['daily_request_limit'] ?? 50 ?>">
                                    <small class="text-muted">Bảo vệ ngân sách và chống spam API.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="default_depth" class="font-weight-bold">Mức độ nghiên cứu mặc định (Default Depth):</label>
                                    <select class="form-control form-control-sm" name="default_depth" id="default_depth">
                                        <option value="QUICK" <?= ($aiConfig['default_depth'] ?? '') == 'QUICK' ? 'selected' : '' ?>>QUICK (Nhanh)</option>
                                        <option value="STANDARD" <?= ($aiConfig['default_depth'] ?? '') == 'STANDARD' ? 'selected' : '' ?>>STANDARD (Tiêu chuẩn)</option>
                                        <option value="DEEP" <?= ($aiConfig['default_depth'] ?? '') == 'DEEP' ? 'selected' : '' ?>>DEEP (Chuyên sâu)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="<?= $linkMan ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Quay lại</a>
                        <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-4"><i class="fas fa-save mr-1"></i> Lưu Cấu Hình AI</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
function toggleProviderSettings(val) {
    var gemini = document.getElementById('section_gemini');
    var openai = document.getElementById('section_openai');

    if (val === 'gemini') {
        gemini.style.display = 'block';
        openai.style.display = 'none';
    } else if (val === 'openai') {
        gemini.style.display = 'none';
        openai.style.display = 'block';
    } else {
        gemini.style.display = 'block';
        openai.style.display = 'block';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    toggleProviderSettings(document.getElementById('active_provider').value);
});
</script>
