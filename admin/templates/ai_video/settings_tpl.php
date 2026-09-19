<?php
$linkMan = "index.php?com=ai_video&act=man";
$linkSave = "index.php?com=ai_video&act=save_settings";
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    Cấu hình Nhà cung cấp & Hạn mức Video AI (Settings)
                </h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="<?=$linkSave?>" method="POST">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-outline card-primary shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-key mr-2 text-primary"></i>API Keys & Nhà cung cấp Video Thương mại</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Nhà cung cấp Video Mặc định (Active Provider):</label>
                                <select name="active_provider" class="form-control">
                                    <option value="mock" <?=(($aiVideoConfig['active_provider'] ?? 'mock') === 'mock') ? 'selected' : ''?>>Mock Video Engine (Môi trường Thử nghiệm / Miễn phí)</option>
                                    <option value="creatify" <?=(($aiVideoConfig['active_provider'] ?? '') === 'creatify') ? 'selected' : ''?>>Creatify AI (Text/Product to Video)</option>
                                    <option value="arcads" <?=(($aiVideoConfig['active_provider'] ?? '') === 'arcads') ? 'selected' : ''?>>Arcads AI (AI Actor UGC Ads)</option>
                                    <option value="heygen" <?=(($aiVideoConfig['active_provider'] ?? '') === 'heygen') ? 'selected' : ''?>>HeyGen AI (Avatar Presentation)</option>
                                    <option value="manual" <?=(($aiVideoConfig['active_provider'] ?? '') === 'manual') ? 'selected' : ''?>>Manual Video Upload (Tải lên thủ công)</option>
                                </select>
                            </div>

                            <hr>

                            <!-- Creatify API Key -->
                            <div class="form-group">
                                <label class="font-weight-bold">Creatify API Key:</label>
                                <div class="input-group">
                                    <input type="password" name="creatify_api_key" class="form-control" placeholder="••••••••••••••••" value="<?=!empty($aiVideoConfig['creatify_api_key']) ? htmlspecialchars($aiVideoConfig['creatify_api_key']) : ''?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><?=!empty($aiVideoConfig['creatify_api_key']) ? '<span class="text-success font-weight-bold">Đã cấu hình</span>' : '<span class="text-muted">Chưa cấu hình</span>'?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Arcads API Key -->
                            <div class="form-group">
                                <label class="font-weight-bold">Arcads API Key:</label>
                                <div class="input-group">
                                    <input type="password" name="arcads_api_key" class="form-control" placeholder="••••••••••••••••" value="<?=!empty($aiVideoConfig['arcads_api_key']) ? htmlspecialchars($aiVideoConfig['arcads_api_key']) : ''?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><?=!empty($aiVideoConfig['arcads_api_key']) ? '<span class="text-success font-weight-bold">Đã cấu hình</span>' : '<span class="text-muted">Chưa cấu hình</span>'?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- HeyGen API Key -->
                            <div class="form-group">
                                <label class="font-weight-bold">HeyGen API Key:</label>
                                <div class="input-group">
                                    <input type="password" name="heygen_api_key" class="form-control" placeholder="••••••••••••••••" value="<?=!empty($aiVideoConfig['heygen_api_key']) ? htmlspecialchars($aiVideoConfig['heygen_api_key']) : ''?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><?=!empty($aiVideoConfig['heygen_api_key']) ? '<span class="text-success font-weight-bold">Đã cấu hình</span>' : '<span class="text-muted">Chưa cấu hình</span>'?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card card-outline card-warning shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-shield-alt mr-2 text-warning"></i>Hạn mức Chi phí & Bảo vệ (Cost Guard)</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Giới hạn Render Video / Ngày:</label>
                                <input type="number" name="daily_video_limit" class="form-control" value="<?=$aiVideoConfig['daily_video_limit'] ?? 20?>" min="1" max="100">
                                <small class="text-muted">Ngăn chặn tạo video hàng loạt vô ý vượt ngân sách.</small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Giọng đọc Tiếng Việt Mặc định:</label>
                                <select name="default_voice" class="form-control">
                                    <?php foreach ($voicesList as $vKey => $vVal): ?>
                                        <option value="<?=$vKey?>" <?=(($aiVideoConfig['default_voice'] ?? 'vi-VN-Standard-A') === $vKey) ? 'selected' : ''?>><?=htmlspecialchars($vVal['name'])?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Visual Template Mặc định:</label>
                                <select name="default_template" class="form-control">
                                    <?php foreach ($templatesList as $tKey => $tVal): ?>
                                        <option value="<?=$tKey?>" <?=(($aiVideoConfig['default_template'] ?? 'PROBLEM_SOLUTION') === $tKey) ? 'selected' : ''?>><?=htmlspecialchars($tVal['name'])?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm">
                                <i class="fas fa-save mr-1"></i> Lưu Cấu hình Video AI
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
