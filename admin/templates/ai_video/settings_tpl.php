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
                    Cấu hình Video Composer & Hạn mức Chi phí (Settings)
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
                    <!-- Diagnostic FFmpeg Local Render Engine -->
                    <div class="card card-outline card-info shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-server mr-2 text-info"></i>Hạ tầng FFmpeg Local Render Engine (0 VND Cost)</h3>
                            <div class="card-tools">
                                <span class="badge badge-<?=(!empty($ffmpegAudit['available']) ? 'success' : 'warning')?> font-weight-bold">
                                    <?=(!empty($ffmpegAudit['available']) ? 'FFmpeg Sẵn sàng (READY)' : 'Chế độ Local Fallback')?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Trạng thái FFmpeg:</strong>
                                    <div><?=htmlspecialchars($ffmpegAudit['message'] ?? 'N/A')?></div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Phiên bản phát hiện:</strong>
                                    <div><code><?=htmlspecialchars($ffmpegAudit['version'] ?? 'N/A')?></code></div>
                                </div>
                            </div>
                            <?php if (empty($ffmpegAudit['available'])): ?>
                                <div class="alert alert-light border small mb-0">
                                    <h6 class="font-weight-bold text-primary mb-1"><i class="fas fa-terminal mr-1"></i> Hướng dẫn kích hoạt Local FFmpeg:</h6>
                                    <pre class="bg-dark text-light p-2 rounded mb-1" style="font-size: 11px;"># Cài đặt FFmpeg trên Windows bằng PowerShell:
winget install Gyan.FFmpeg

# Hoặc tải file zip từ https://ffmpeg.org và thêm thư mục bin vào PATH hệ thống.</pre>
                                    <small class="text-muted">Hệ thống luôn có bộ sinh MP4 nội bộ tự động để tiến trình kiểm thử và tạo video không bị gián đoạn.</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- API Keys & External Providers -->
                    <div class="card card-outline card-primary shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-key mr-2 text-primary"></i>Nhà cung cấp Phân cảnh AI (Optional AI Scene Providers)</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Nhà cung cấp Phân cảnh AI Mặc định (Active Provider):</label>
                                <select name="active_provider" class="form-control">
                                    <option value="mock" <?=(($aiVideoConfig['active_provider'] ?? 'mock') === 'mock') ? 'selected' : ''?>>Mock Video Engine (Môi trường Thử nghiệm / Miễn phí)</option>
                                    <option value="beeknoee" <?=(($aiVideoConfig['active_provider'] ?? '') === 'beeknoee') ? 'selected' : ''?>>Beeknoee AI Scene (Veo-3.1 Model - 50.000 VND / 8s clip)</option>
                                    <option value="creatify" <?=(($aiVideoConfig['active_provider'] ?? '') === 'creatify') ? 'selected' : ''?>>Creatify AI (Text/Product to Video)</option>
                                    <option value="arcads" <?=(($aiVideoConfig['active_provider'] ?? '') === 'arcads') ? 'selected' : ''?>>Arcads AI (AI Actor UGC Ads)</option>
                                    <option value="heygen" <?=(($aiVideoConfig['active_provider'] ?? '') === 'heygen') ? 'selected' : ''?>>HeyGen AI (Avatar Presentation)</option>
                                    <option value="manual" <?=(($aiVideoConfig['active_provider'] ?? '') === 'manual') ? 'selected' : ''?>>Manual Video Upload (Tải lên thủ công)</option>
                                </select>
                                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Provider này chỉ được gọi cho các phân cảnh yêu cầu chuyển động AI trong chế độ HYBRID / PREMIUM.</small>
                            </div>

                            <hr>

                            <!-- Beeknoee AI Video (Real Provider) -->
                            <?php
                            global $config;
                            $beeknoeeActive = !empty($config['beeknoee']['active']) || !empty($aiVideoConfig['beeknoee_api_key']);
                            $beeknoeeKeySet = !empty($config['beeknoee']['api_key']) || !empty($aiVideoConfig['beeknoee_api_key']);
                            $beeknoeeModel = !empty($config['beeknoee']['video_model']) ? $config['beeknoee']['video_model'] : 'veo-3.1-fast-generate-preview';
                            $beeknoeeDuration = !empty($config['beeknoee']['duration']) ? $config['beeknoee']['duration'] : 8;
                            ?>
                            <div class="card card-outline card-success mb-3">
                                <div class="card-header py-2 bg-light">
                                    <strong class="text-success"><i class="fas fa-bolt mr-1"></i> Beeknoee AI Scene Generator (Veo 3.1)</strong>
                                    <span class="badge badge-<?=$beeknoeeKeySet ? 'success' : 'secondary'?> float-right">
                                        <?=$beeknoeeKeySet ? 'Configured (Đã cấu hình)' : 'Not configured (Chưa cấu hình)'?>
                                    </span>
                                </div>
                                <div class="card-body py-2 small">
                                    <div class="row">
                                        <div class="col-md-4"><strong>Model:</strong> <code><?=$beeknoeeModel?></code></div>
                                        <div class="col-md-4"><strong>Thời lượng clip:</strong> <code><?=$beeknoeeDuration?>s (9:16)</code></div>
                                        <div class="col-md-4"><strong>Trạng thái:</strong> <?=$beeknoeeActive ? '<span class="text-success font-weight-bold">Bật (Active)</span>' : '<span class="text-warning font-weight-bold">Tắt (Inactive)</span>'?></div>
                                    </div>
                                    <div class="text-muted mt-2">
                                        <i class="fas fa-info-circle mr-1"></i> API Key được nạp bảo mật từ <code>libraries/config.php</code>.
                                    </div>
                                </div>
                            </div>

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
                                <label class="font-weight-bold">Chế độ Video Mặc định (Default Mode):</label>
                                <select name="default_mode" class="form-control font-weight-bold text-success">
                                    <option value="ECONOMY" <?=(($aiVideoConfig['default_mode'] ?? 'ECONOMY') === 'ECONOMY') ? 'selected' : ''?>>ECONOMY (0 VND API Cost - Khuyên dùng)</option>
                                    <option value="HYBRID" <?=(($aiVideoConfig['default_mode'] ?? '') === 'HYBRID') ? 'selected' : ''?>>HYBRID (Max 1-2 AI scenes)</option>
                                    <option value="PREMIUM" <?=(($aiVideoConfig['default_mode'] ?? '') === 'PREMIUM') ? 'selected' : ''?>>PREMIUM (High Investment)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Hạn mức AI Video Tối đa / Video (VND):</label>
                                <input type="number" name="max_ai_video_cost_per_video" class="form-control font-weight-bold" value="<?=$aiVideoConfig['max_ai_video_cost_per_video'] ?? 60000?>" step="5000" min="0">
                                <small class="text-muted">Nếu chi phí ước tính vượt mức này, hệ thống sẽ chặn và yêu cầu Admin xác nhận.</small>
                            </div>

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
                                <i class="fas fa-save mr-1"></i> Lưu Cấu hình Video Composer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
