<?php
$linkMan = "index.php?com=ai_video&act=man";
$linkSave = "index.php?com=ai_video&act=save_create";
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    Tạo Dự án Video Mới (Fitnado Video Composer)
                </h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="<?=$linkSave?>" method="POST" id="formCreateVideo">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Card 1: Chọn Chế độ Video (Video Mode) -->
                    <div class="card card-outline card-success shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-sliders-h mr-2 text-success"></i>1. Chọn Chế độ Sản xuất Video (Video Mode)</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 border-success shadow-sm mode-card selected" style="cursor: pointer;" onclick="selectMode('ECONOMY')">
                                        <div class="card-body text-center p-3">
                                            <div class="custom-control custom-radio mb-2">
                                                <input type="radio" id="modeEconomy" name="mode" value="ECONOMY" class="custom-control-input" checked>
                                                <label class="custom-control-label font-weight-bold text-success" for="modeEconomy" style="font-size: 1.1rem;">ECONOMY</label>
                                            </div>
                                            <span class="badge badge-success mb-2">Mặc định • Chi phí 0 VND</span>
                                            <p class="small text-muted mb-0" style="text-align: left;">
                                                <i class="fas fa-check text-success mr-1"></i> Ảnh sản phẩm & Gallery<br>
                                                <i class="fas fa-check text-success mr-1"></i> Chuyển động Pan / Zoom / Push<br>
                                                <i class="fas fa-check text-success mr-1"></i> Lồng tiếng TTS & Phụ đề Safe-area<br>
                                                <i class="fas fa-check text-success mr-1"></i> <strong>API Video Cost = 0 VND</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 border-info shadow-sm mode-card" style="cursor: pointer;" onclick="selectMode('HYBRID')">
                                        <div class="card-body text-center p-3">
                                            <div class="custom-control custom-radio mb-2">
                                                <input type="radio" id="modeHybrid" name="mode" value="HYBRID" class="custom-control-input">
                                                <label class="custom-control-label font-weight-bold text-info" for="modeHybrid" style="font-size: 1.1rem;">HYBRID</label>
                                            </div>
                                            <span class="badge badge-info mb-2">1–2 Scene AI Video Clip</span>
                                            <p class="small text-muted mb-0" style="text-align: left;">
                                                <i class="fas fa-check text-info mr-1"></i> Toàn bộ tính năng Economy<br>
                                                <i class="fas fa-check text-info mr-1"></i> Tối đa 1–2 cảnh AI chuyển động<br>
                                                <i class="fas fa-check text-info mr-1"></i> Các cảnh còn lại render Local<br>
                                                <i class="fas fa-coins text-warning mr-1"></i> Ước tính: <strong>~50.000 VND</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 border-secondary shadow-sm mode-card" style="cursor: pointer;" onclick="selectMode('PREMIUM')">
                                        <div class="card-body text-center p-3">
                                            <div class="custom-control custom-radio mb-2">
                                                <input type="radio" id="modePremium" name="mode" value="PREMIUM" class="custom-control-input">
                                                <label class="custom-control-label font-weight-bold text-secondary" for="modePremium" style="font-size: 1.1rem;">PREMIUM</label>
                                            </div>
                                            <span class="badge badge-secondary mb-2">Nhiều Cảnh AI Video</span>
                                            <p class="small text-muted mb-0" style="text-align: left;">
                                                <i class="fas fa-check text-secondary mr-1"></i> Cho phép nhiều phân cảnh AI<br>
                                                <i class="fas fa-shield-alt text-danger mr-1"></i> Bắt buộc Admin duyệt ngân sách<br>
                                                <i class="fas fa-exclamation-circle text-danger mr-1"></i> Dành riêng sản phẩm trọng điểm
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Chọn Kịch bản TikTok Nguồn -->
                    <div class="card card-outline card-primary shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-magic mr-2 text-primary"></i>2. Chọn Kịch bản TikTok Đã Phê Duyệt</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Kịch bản TikTok Nguồn (Approved Script): <span class="text-danger">*</span></label>
                                <select name="id_content" class="form-control select2" required style="width: 100%;">
                                    <option value="">-- Chọn kịch bản TikTok đã duyệt --</option>
                                    <?php if (!empty($approvedScripts)): foreach ($approvedScripts as $sc): ?>
                                        <option value="<?=$sc['id']?>">
                                            [#<?=$sc['id']?>] <?=htmlspecialchars($sc['product_name'])?> - <?=htmlspecialchars($sc['title'])?> (<?=$sc['target_duration']?>s)
                                        </option>
                                    <?php endforeach; else: ?>
                                        <option value="" disabled>Chưa có kịch bản TikTok nào ở trạng thái APPROVED. Vui lòng duyệt kịch bản tại module AI Content.</option>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted"><i class="fas fa-lock mr-1"></i> Chỉ hiển thị các kịch bản TikTok đã được Admin phê duyệt (Human Gate Phase 05).</small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Tiêu đề Dự án Video (Tùy chọn):</label>
                                <input type="text" name="title" class="form-control" placeholder="Để trống để tự động lấy theo tiêu đề kịch bản">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Định dạng Video:</label>
                                        <select name="video_type" class="form-control">
                                            <option value="TIKTOK_9_16" selected>TikTok / Reels / Shorts (Dọc 9:16)</option>
                                            <option value="PRODUCT_SHOWCASE">Showcase Vuông (1:1)</option>
                                            <option value="YOUTUBE_SHORTS">YouTube Ngang (16:9)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tỷ lệ khung hình:</label>
                                        <select name="aspect_ratio" class="form-control">
                                            <option value="9:16" selected>9:16 (1080 x 1920 px) - Chuẩn TikTok Safe Area</option>
                                            <option value="1:1">1:1 (1080 x 1080 px)</option>
                                            <option value="16:9">16:9 (1920 x 1080 px)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Cấu trúc Content Flow 30s TikTok -->
                    <div class="card card-outline card-info shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-stream mr-2 text-info"></i>3. Cấu trúc Phân cảnh Mục tiêu (Purpose-Driven Flow)</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-striped mb-0 text-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 80px;" class="text-center">Thời gian</th>
                                        <th style="width: 140px;">Purpose</th>
                                        <th>Mục tiêu Tiếp thị</th>
                                        <th style="width: 130px;">Render Method</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center font-weight-bold">0–3s</td>
                                        <td><span class="badge badge-danger">HOOK</span></td>
                                        <td>Bắt buộc từ kịch bản đã duyệt, gây chú ý tức thì</td>
                                        <td><span class="badge badge-success">LOCAL Motion</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center font-weight-bold">3–7s</td>
                                        <td><span class="badge badge-warning text-dark">PROBLEM</span></td>
                                        <td>Nỗi đau gymer (đau lưng, cấn hông, tạ nặng)</td>
                                        <td><span class="badge badge-success">LOCAL Motion</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center font-weight-bold">7–12s</td>
                                        <td><span class="badge badge-primary">PRODUCT_INTRO</span></td>
                                        <td>Giới thiệu giải pháp thương hiệu FITNADO</td>
                                        <td><span class="badge badge-success">LOCAL Motion</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center font-weight-bold">12–18s</td>
                                        <td><span class="badge badge-info">BENEFIT / DEMO</span></td>
                                        <td>Trình diễn tính năng, gồng core, nén khoang bụng</td>
                                        <td><span class="badge badge-info">LOCAL / AI Scene</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center font-weight-bold">18–23s</td>
                                        <td><span class="badge badge-secondary">LIMITATION</span></td>
                                        <td>Khuyên ai không nên mua để tạo uy tín thực chất</td>
                                        <td><span class="badge badge-success">LOCAL Motion</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center font-weight-bold">23–28s</td>
                                        <td><span class="badge badge-dark">BEST_FOR</span></td>
                                        <td>Chỉ định đúng đối tượng người tập phù hợp nhất</td>
                                        <td><span class="badge badge-success">LOCAL Motion</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center font-weight-bold">28–32s</td>
                                        <td><span class="badge badge-danger">CTA</span></td>
                                        <td>Kêu gọi bấm giỏ hàng nhận ưu đãi chính hãng</td>
                                        <td><span class="badge badge-success">LOCAL Motion</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Bảng Dự toán Chi phí (Cost Estimation Guard) -->
                    <div class="card card-outline card-warning shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-coins mr-2 text-warning"></i>Ước tính Chi phí (Cost Guard)</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-2">
                                <tr>
                                    <td>Chế độ đã chọn:</td>
                                    <td class="text-right font-weight-bold text-success" id="costModeLabel">ECONOMY</td>
                                </tr>
                                <tr>
                                    <td>Số cảnh AI Video:</td>
                                    <td class="text-right font-weight-bold" id="costAiScenes">0 cảnh</td>
                                </tr>
                                <tr>
                                    <td>Chi phí Render Local:</td>
                                    <td class="text-right font-weight-bold text-success">0 VND</td>
                                </tr>
                                <tr>
                                    <td>Chi phí AI Video API:</td>
                                    <td class="text-right font-weight-bold text-dark" id="costAiApi">0 VND</td>
                                </tr>
                                <tr class="border-top">
                                    <th class="pt-2">Tổng chi phí API:</th>
                                    <th class="text-right pt-2 text-primary" style="font-size: 1.1rem;" id="costTotal">0 VND</th>
                                </tr>
                            </table>
                            <div class="alert alert-light border small mb-0">
                                <i class="fas fa-shield-alt text-success mr-1"></i>
                                Hạn mức bảo vệ tối đa: <strong>60.000 VND / Video</strong>.
                            </div>
                        </div>
                    </div>

                    <!-- Cấu hình Giọng đọc & Template -->
                    <div class="card card-outline card-secondary shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-cogs mr-2"></i>Cấu hình Giọng đọc & Template</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Giọng đọc Tiếng Việt (TTS):</label>
                                <select name="voice_id" class="form-control">
                                    <?php foreach ($voicesList as $vKey => $vVal): ?>
                                        <option value="<?=$vKey?>"><?=htmlspecialchars($vVal['name'])?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Mẫu Visual Presentation:</label>
                                <select name="template_id" class="form-control">
                                    <?php foreach ($templatesList as $tKey => $tVal): ?>
                                        <option value="<?=$tKey?>"><?=htmlspecialchars($tVal['name'])?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">AI Scene Provider (Khi cần AI Clip):</label>
                                <select name="provider" class="form-control">
                                    <option value="mock" selected>Mock Video Engine (Mặc định)</option>
                                    <option value="beeknoee">Beeknoee AI Scene Provider (Veo-3.1 Model)</option>
                                    <option value="creatify">Creatify AI Video (Thương mại)</option>
                                    <option value="arcads">Arcads AI (Thương mại)</option>
                                    <option value="manual">Manual DIY Upload (Tải lên thủ công)</option>
                                </select>
                                <small class="text-muted">Chỉ được gọi khi có phân cảnh yêu cầu AI Scene trong Hybrid/Premium mode.</small>
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow-sm">
                                <i class="fas fa-layer-group mr-1"></i> Khởi tạo Dự án Video
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
function selectMode(mode) {
    document.querySelectorAll('.mode-card').forEach(function(card) {
        card.classList.remove('border-primary', 'shadow');
    });
    if (mode === 'ECONOMY') {
        document.getElementById('modeEconomy').checked = true;
        document.getElementById('costModeLabel').innerText = 'ECONOMY';
        document.getElementById('costModeLabel').className = 'text-right font-weight-bold text-success';
        document.getElementById('costAiScenes').innerText = '0 cảnh';
        document.getElementById('costAiApi').innerText = '0 VND';
        document.getElementById('costTotal').innerText = '0 VND';
    } else if (mode === 'HYBRID') {
        document.getElementById('modeHybrid').checked = true;
        document.getElementById('costModeLabel').innerText = 'HYBRID';
        document.getElementById('costModeLabel').className = 'text-right font-weight-bold text-info';
        document.getElementById('costAiScenes').innerText = '1 cảnh (max 8s)';
        document.getElementById('costAiApi').innerText = '50.000 VND';
        document.getElementById('costTotal').innerText = '50.000 VND';
    } else if (mode === 'PREMIUM') {
        document.getElementById('modePremium').checked = true;
        document.getElementById('costModeLabel').innerText = 'PREMIUM';
        document.getElementById('costModeLabel').className = 'text-right font-weight-bold text-danger';
        document.getElementById('costAiScenes').innerText = '2–4 cảnh';
        document.getElementById('costAiApi').innerText = '100.000–200.000 VND';
        document.getElementById('costTotal').innerText = 'Theo thực tế';
    }
}
</script>
