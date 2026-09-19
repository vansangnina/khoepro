<?php
$linkMan = "index.php?com=ai_content&act=man";
$linkAiConfig = "index.php?com=product_research&act=provider_config";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="<?= $linkMan ?>">Kho nội dung AI</a></li>
                <li class="breadcrumb-item active">Cấu Hình & Prompt Templates (Settings & Prompts)</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- AI Engine Status -->
        <div class="col-lg-4">
            <div class="card card-purple card-outline shadow-sm text-sm mb-3">
                <div class="card-header font-weight-bold">
                    <i class="fas fa-server mr-1"></i> Trạng Thái AI Engine & Model
                </div>
                <div class="card-body p-3">
                    <div class="mb-2">
                        <strong>Active Provider:</strong>
                        <span class="badge badge-success ml-1"><?= strtoupper($aiConfig['active_provider'] ?? 'MOCK') ?></span>
                    </div>
                    <div class="mb-2">
                        <strong>Default Model:</strong>
                        <span class="badge badge-info ml-1"><?= htmlspecialchars($aiConfig['gemini_model'] ?? 'gemini-1.5-flash') ?></span>
                    </div>
                    <div class="mb-3">
                        <strong>Daily Request Limit:</strong>
                        <span class="badge badge-secondary ml-1"><?= (int)($aiConfig['daily_request_limit'] ?? 50) ?> requests/ngày</span>
                    </div>
                    <a href="<?= $linkAiConfig ?>" class="btn btn-sm btn-outline-purple btn-block">
                        <i class="fas fa-key mr-1"></i> Quản lý API Keys & Nhà cung cấp AI
                    </a>
                </div>
            </div>

            <!-- Quality Gates Info -->
            <div class="card card-outline card-success shadow-sm text-sm">
                <div class="card-header font-weight-bold">
                    <i class="fas fa-shield-alt mr-1"></i> Quy Tắc Kiểm Định Chất Lượng (Quality Gates)
                </div>
                <div class="card-body p-3 text-xs">
                    <ul class="pl-3 mb-0">
                        <li class="mb-2"><strong>FACT > AI_ANALYSIS:</strong> Số liệu thị trường chỉ được nạp từ Evidence thực.</li>
                        <li class="mb-2"><strong>Không tự nhận trải nghiệm cá nhân:</strong> Cấm tự nhận "Tôi đã test 30 ngày..." khi chưa có bằng chứng `REAL_TEST`.</li>
                        <li class="mb-2"><strong>Không tạo fake review:</strong> Lời chứng thực chỉ được lấy từ `table_comment` thật.</li>
                        <li class="mb-2"><strong>An toàn Y tế & Thể lực:</strong> Cấm cam kết điều trị y khoa, chữa thoát vị, chữa đau lưng.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Prompt Templates Catalog -->
        <div class="col-lg-8">
            <div class="card card-primary card-outline shadow-sm text-sm">
                <div class="card-header font-weight-bold">
                    <i class="fas fa-code mr-1"></i> Danh Mục Prompt Templates Chuẩn Hóa (Prompt Versioning)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover text-sm mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 180px;">Prompt Version</th>
                                    <th style="width: 160px;">Loại nội dung</th>
                                    <th>Cấu trúc Schema & Mục tiêu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge badge-primary">product-analysis-v1</span></td>
                                    <td><strong>Phân tích sản phẩm</strong></td>
                                    <td class="text-xs">Phân tích 12 khía cạnh: problem_solved, target_audience, key_benefits, limitations, pros, cons, suitable_for, not_suitable_for, buying_considerations, comparison_angles, risk_notes, confidence.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-danger">tiktok-hooks-v1</span></td>
                                    <td><strong>7 TikTok Hooks</strong></td>
                                    <td class="text-xs">Sinh 7 biến thể hooks: Problem, Mistake, Comparison, Curiosity, Demonstration, Buyer Warning, Value Hooks.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-dark">tiktok-script-v1</span></td>
                                    <td><strong>Kịch bản TikTok + Shot Plan</strong></td>
                                    <td class="text-xs">Kịch bản 15s/30s/45s/60s kèm bảng phân cảnh Shot Plan chi tiết (scene_number, duration, visual_instruction, voiceover, on_screen_text, asset_requirement). Cầu nối cho Phase 06.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-success">seo-v1</span></td>
                                    <td><strong>SEO Metadata & FAQs</strong></td>
                                    <td class="text-xs">Primary Keyword, Secondary Keywords, Search Intent, SEO Title (<= 65 ký tự), Meta Description (<= 160 ký tự), Article Outline, FAQ Schema.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-info">review-draft-v1</span></td>
                                    <td><strong>Editorial Review Draft</strong></td>
                                    <td class="text-xs">Bài viết đánh giá chuyên sâu hoàn chỉnh với các thẻ Heading H2/H3, tóm tắt đánh giá và lời khuyên mua hàng.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-secondary">faq-v1</span></td>
                                    <td><strong>Bộ câu hỏi FAQ</strong></td>
                                    <td class="text-xs">Hỏi đáp kỹ thuật thể thao và hướng dẫn sử dụng / bảo quản sản phẩm.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
