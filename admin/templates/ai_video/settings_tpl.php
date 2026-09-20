<?php
$linkMan = "index.php?com=ai_video&act=man";
$linkSave = "index.php?com=ai_video&act=save_settings";
$ajaxPreviewUrl = "index.php?com=ai_video&act=ajax_voice_preview";
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    Cấu hình Video Composer & Voice Engine (Settings)
                </h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- 1. LIVE VOICE PREVIEW & BENCHMARK TESTER -->
        <div class="card card-outline card-success shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title font-weight-bold"><i class="fas fa-microphone-alt mr-2 text-success"></i>Thử giọng & Nghe thử Trực tiếp (Live Voice Preview Tester)</h3>
                <div class="card-tools">
                    <span class="badge badge-success font-weight-bold">Live Audio Synth</span>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    <i class="fas fa-info-circle mr-1"></i> Tính năng cho phép Admin thử nghiệm phát âm kịch bản thực tế tức thì mà <strong>không cần render video</strong>. Hệ thống sẽ tự động chạy qua bộ chuẩn hóa Voice Preparation (số, đơn vị, giá tiền, từ điển thương hiệu).
                </p>
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label class="font-weight-bold">Câu kịch bản thử nghiệm:</label>
                            <textarea id="preview_text" class="form-control" rows="3" placeholder="Nhập câu thoại kịch bản cần test giọng...">Squat trên 150kg mà dùng đai dán mỏng là sai lầm nguy hiểm nhất! Đổi ngay sang Đai Cứng FITNADO Pro Lever khóa đòn bẩy hợp kim nguyên khối giá chỉ 399.000đ.</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Voice Provider:</label>
                                    <select id="preview_provider" class="form-control form-control-sm">
                                        <option value="beeknoee" selected>Beeknoee HD Voice</option>
                                        <option value="google_translate">Google Translate (Baseline)</option>
                                        <option value="mock">Mock Voice</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Giọng đọc (Voice):</label>
                                    <select id="preview_voice" class="form-control form-control-sm font-weight-bold text-primary">
                                        <?php foreach ($voicesList as $vKey => $vVal): ?>
                                            <option value="<?=$vKey?>" <?=$vKey === ($aiVideoConfig['default_voice'] ?? 'nova') ? 'selected' : ''?>>
                                                <?=htmlspecialchars($vVal['name'])?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold">Tốc độ (Speed):</label>
                                    <select id="preview_speed" class="form-control form-control-sm">
                                        <option value="0.95">0.95x (Chậm rãi, nhấn nhá)</option>
                                        <option value="1.00" selected>1.00x (Tự nhiên chuẩn Creator)</option>
                                        <option value="1.05">1.05x (Nhanh, năng động)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="btn_generate_preview" class="btn btn-success btn-sm font-weight-bold mt-2">
                            <i class="fas fa-play mr-1"></i> Thử giọng ngay (Generate Preview)
                        </button>
                    </div>

                    <div class="col-md-5">
                        <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-headphones mr-1 text-primary"></i> Kết quả Phát âm (Spoken Output):</h6>
                                <div id="preview_spoken_box" class="small p-2 bg-white rounded border text-secondary mb-2" style="min-height: 60px;">
                                    <em>Bấm "Thử giọng ngay" để nghe phát âm đã chuẩn hóa...</em>
                                </div>
                                <div id="preview_meta_box" class="small text-muted mb-2 d-none">
                                    <span class="badge badge-info mr-1" id="badge_duration">0.0s</span>
                                    <span class="badge badge-secondary mr-1" id="badge_chars">0 chars</span>
                                    <span class="badge badge-warning mr-1" id="badge_cost">0 VND</span>
                                    <span class="badge badge-light border" id="badge_cache">Fresh Call</span>
                                </div>
                            </div>
                            <div>
                                <audio id="preview_audio_player" controls class="w-100 d-none mt-2" style="height: 38px;"></audio>
                                <div id="preview_loading" class="text-center py-2 d-none">
                                    <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                                    <span class="small font-weight-bold text-muted ml-2">Đang xử lý Voice Preparation & TTS...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                                    <pre class="bg-dark text-light p-2 rounded mb-1" style="font-size: 11px;">winget install Gyan.FFmpeg</pre>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Voice Provider & Pronunciation Dictionary -->
                    <div class="card card-outline card-primary shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-volume-up mr-2 text-primary"></i>Cấu hình Giọng đọc & Từ điển Phiên âm (Voice & Pronunciation)</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Nhà cung cấp Voice Mặc định:</label>
                                        <select name="default_voice_provider" class="form-control font-weight-bold text-primary">
                                            <option value="beeknoee" <?=(($aiVideoConfig['default_voice_provider'] ?? 'beeknoee') === 'beeknoee') ? 'selected' : ''?>>Beeknoee AI Voice (OpenAI HD / Gemini Speech)</option>
                                            <option value="google_translate" <?=(($aiVideoConfig['default_voice_provider'] ?? '') === 'google_translate') ? 'selected' : ''?>>Google Translate (Baseline / Miễn phí)</option>
                                            <option value="mock" <?=(($aiVideoConfig['default_voice_provider'] ?? '') === 'mock') ? 'selected' : ''?>>Mock Voice (Offline)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tốc độ Đọc Mặc định:</label>
                                        <select name="default_voice_speed" class="form-control">
                                            <option value="0.95" <?=(($aiVideoConfig['default_voice_speed'] ?? '1.0') == '0.95') ? 'selected' : ''?>>0.95x (Chậm rãi, nhấn nhá rõ ràng)</option>
                                            <option value="1.00" <?=(($aiVideoConfig['default_voice_speed'] ?? '1.0') == '1.00' || ($aiVideoConfig['default_voice_speed'] ?? '1.0') == '1') ? 'selected' : ''?>>1.00x (Tự nhiên chuẩn Creator - Khuyên dùng)</option>
                                            <option value="1.05" <?=(($aiVideoConfig['default_voice_speed'] ?? '1.0') == '1.05') ? 'selected' : ''?>>1.05x (Nhanh, cuốn hút TikTok)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Pronunciation Dictionary Editor -->
                            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-spell-check mr-1 text-info"></i> Từ điển Phiên âm Thương hiệu & Thuật ngữ Gym (Pronunciation Dictionary):</h6>
                            <p class="text-muted small mb-2">Hệ thống tự động thay thế các từ ngữ tiếng Anh / viết tắt sang phiên âm tiếng Việt tự nhiên trước khi gửi tới TTS.</p>

                            <div class="table-responsive mb-2" style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-sm table-bordered table-striped small" id="table_pronunciation">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 45%;">Thuật ngữ gốc (Word)</th>
                                            <th style="width: 45%;">Phiên âm tiếng Việt (Spoken Replacement)</th>
                                            <th style="width: 10%; text-align: center;">Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($pronunciationDict)): ?>
                                            <?php foreach ($pronunciationDict as $word => $rep): ?>
                                                <tr>
                                                    <td><input type="text" name="pronunciation_words[]" class="form-control form-control-sm" value="<?=htmlspecialchars($word)?>"></td>
                                                    <td><input type="text" name="pronunciation_replacements[]" class="form-control form-control-sm" value="<?=htmlspecialchars($rep)?>"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger btn-remove-row"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-xs font-weight-bold" id="btn_add_pronunciation_row">
                                <i class="fas fa-plus mr-1"></i> Thêm từ phiên âm mới
                            </button>
                        </div>
                    </div>

                    <!-- API Keys & External Providers -->
                    <div class="card card-outline card-secondary shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="fas fa-key mr-2 text-secondary"></i>Nhà cung cấp Phân cảnh AI (Optional AI Scene Providers)</h3>
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
                            </div>

                            <hr>

                            <!-- Beeknoee AI Video & TTS Config -->
                            <?php
                            global $config;
                            $beeknoeeKeySet = !empty($config['beeknoee']['api_key']) || !empty($aiVideoConfig['beeknoee_api_key']);
                            ?>
                            <div class="card card-outline card-success mb-3">
                                <div class="card-header py-2 bg-light">
                                    <strong class="text-success"><i class="fas fa-bolt mr-1"></i> Beeknoee API (AI Scene & HD Voice Engine)</strong>
                                    <span class="badge badge-<?=$beeknoeeKeySet ? 'success' : 'secondary'?> float-right">
                                        <?=$beeknoeeKeySet ? 'Configured (Đã cấu hình)' : 'Not configured'?>
                                    </span>
                                </div>
                                <div class="card-body py-2 small">
                                    <div class="form-group mb-1">
                                        <label class="font-weight-bold">Beeknoee API Key (Override nếu không đặt trong config.php):</label>
                                        <input type="password" name="beeknoee_api_key" class="form-control form-control-sm" placeholder="sk-bee-..." value="<?=!empty($aiVideoConfig['beeknoee_api_key']) ? htmlspecialchars($aiVideoConfig['beeknoee_api_key']) : ''?>">
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
                                <label class="font-weight-bold">Giọng đọc Mặc định Dự án:</label>
                                <select name="default_voice" class="form-control font-weight-bold text-primary">
                                    <?php foreach ($voicesList as $vKey => $vVal): ?>
                                        <option value="<?=$vKey?>" <?=(($aiVideoConfig['default_voice'] ?? 'nova') === $vKey) ? 'selected' : ''?>><?=htmlspecialchars($vVal['name'])?></option>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. AJAX Voice Preview Handler
    const btnPreview = document.getElementById('btn_generate_preview');
    const previewText = document.getElementById('preview_text');
    const previewProvider = document.getElementById('preview_provider');
    const previewVoice = document.getElementById('preview_voice');
    const previewSpeed = document.getElementById('preview_speed');
    const audioPlayer = document.getElementById('preview_audio_player');
    const spokenBox = document.getElementById('preview_spoken_box');
    const loadingBox = document.getElementById('preview_loading');
    const metaBox = document.getElementById('preview_meta_box');
    const badgeDuration = document.getElementById('badge_duration');
    const badgeChars = document.getElementById('badge_chars');
    const badgeCost = document.getElementById('badge_cost');
    const badgeCache = document.getElementById('badge_cache');

    if (btnPreview) {
        btnPreview.addEventListener('click', function() {
            const text = previewText.value.trim();
            if (!text) {
                alert('Vui lòng nhập câu thoại cần thử giọng.');
                return;
            }

            btnPreview.disabled = true;
            loadingBox.classList.remove('d-none');
            audioPlayer.classList.add('d-none');
            metaBox.classList.add('d-none');

            const formData = new FormData();
            formData.append('text', text);
            formData.append('provider', previewProvider.value);
            formData.append('voice', previewVoice.value);
            formData.append('speed', previewSpeed.value);

            fetch('<?=$ajaxPreviewUrl?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btnPreview.disabled = false;
                loadingBox.classList.add('d-none');

                if (data.success && data.audio_url) {
                    audioPlayer.src = data.audio_url + '?t=' + new Date().getTime();
                    audioPlayer.classList.remove('d-none');
                    audioPlayer.play().catch(e => console.log('Audio autoplay prevented'));

                    spokenBox.innerHTML = '<strong>Văn bản đọc:</strong> ' + data.spoken_script;
                    badgeDuration.textContent = data.duration + 's';
                    badgeChars.textContent = data.characters + ' chars';
                    badgeCost.textContent = data.cost + ' VND';
                    badgeCache.textContent = data.cached ? 'Cache Hit (0 VND)' : 'Fresh Synthesis';
                    metaBox.classList.remove('d-none');
                } else {
                    alert('Lỗi thử giọng: ' + (data.error || 'Không xác định'));
                    spokenBox.innerHTML = '<span class="text-danger">Lỗi: ' + (data.error || 'Thử giọng thất bại') + '</span>';
                }
            })
            .catch(err => {
                btnPreview.disabled = false;
                loadingBox.classList.add('d-none');
                alert('Lỗi kết nối tới máy chủ: ' + err.message);
            });
        });
    }

    // 2. Pronunciation Dictionary Row Add/Remove
    const btnAddRow = document.getElementById('btn_add_pronunciation_row');
    const tableBody = document.querySelector('#table_pronunciation tbody');

    if (btnAddRow && tableBody) {
        btnAddRow.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="pronunciation_words[]" class="form-control form-control-sm" placeholder="Ví dụ: Whey"></td>
                <td><input type="text" name="pronunciation_replacements[]" class="form-control form-control-sm" placeholder="Ví dụ: Uây"></td>
                <td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger btn-remove-row"><i class="fas fa-trash"></i></button></td>
            `;
            tableBody.appendChild(tr);
        });

        tableBody.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-row')) {
                e.target.closest('tr').remove();
            }
        });
    }
});
</script>
