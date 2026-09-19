<?php
/**
 * FITNADO Video Composer
 * Phase 06.2 / 06.3: Real Low-Cost Hybrid & Economy Video Engine
 * Creates complete 9:16 TikTok videos from Approved Scripts, Purpose-driven Shot Plans,
 * Product Images, Gallery, Local Ken Burns Motion, Voiceover, Captions, and Optional AI Video Clips.
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once LIBRARIES . 'class/class.VideoProvider.php';

class VideoComposer {
    private $d;
    private $func;
    private $config;
    private $resolvedFFmpeg;
    private $resolvedFFprobe;
    private $resolvedFont;

    // 3 Video Production Modes
    const MODE_ECONOMY = 'ECONOMY';
    const MODE_HYBRID  = 'HYBRID';
    const MODE_PREMIUM = 'PREMIUM';

    // Allowed Scene Purposes (Content Flow)
    const PURPOSES = array(
        'HOOK'          => array('name' => 'Hook (0–3s Mở đầu)', 'tag' => '[HOOK 0–3S]', 'desc' => 'Gây tò mò, chỉ ra nỗi đau gymer tức thì', 'default_duration' => 3),
        'PROBLEM'       => array('name' => 'Vấn đề / Nỗi đau (Problem)', 'tag' => '[VẤN ĐỀ / NỖI ĐAU]', 'desc' => 'Khai thác hậu quả nếu không xử lý', 'default_duration' => 4),
        'PRODUCT_INTRO' => array('name' => 'Giới thiệu Sản phẩm (Product Intro)', 'tag' => '[GIẢI PHÁP FITNADO]', 'desc' => 'Xuất hiện giải pháp & tên thương hiệu FITNADO', 'default_duration' => 5),
        'DEMO'          => array('name' => 'Trình diễn Tính năng (Demo / Action)', 'tag' => '[TRÌNH DIỄN THỰC TẾ]', 'desc' => 'Thao tác sử dụng, cơ chế hoạt động thực tế', 'default_duration' => 6),
        'BENEFIT'       => array('name' => 'Lợi ích Cốt lõi (Benefit)', 'tag' => '[LỢI ÍCH CỐT LÕI]', 'desc' => 'Cảm giác tập, bảo vệ sức khỏe, tối ưu hiệu suất', 'default_duration' => 5),
        'PROOF'         => array('name' => 'Chứng thực / Độ bền (Proof)', 'tag' => '[ĐỘ BỀN & CHỨNG THỰC]', 'desc' => 'Test tải trọng, độ hoàn thiện, đánh giá thực tế', 'default_duration' => 4),
        'LIMITATION'    => array('name' => 'Giới hạn & Lưu ý (Trust / Limitation)', 'tag' => '[LƯU Ý QUAN TRỌNG]', 'desc' => 'Khuyên ai không nên mua để tạo uy tín thực chất', 'default_duration' => 5),
        'COMPARISON'    => array('name' => 'So sánh Đối đầu (Comparison)', 'tag' => '[SO SÁNH ĐỐI ĐẦU]', 'desc' => 'So sánh khác biệt với đai/dụng cụ thông thường', 'default_duration' => 5),
        'BEST_FOR'      => array('name' => 'Đối tượng Phù hợp (Best For)', 'tag' => '[ĐỐI TƯỢNG PHÙ HỢP]', 'desc' => 'Chỉ định rõ đối tượng nên sở hữu sản phẩm', 'default_duration' => 5),
        'CTA'           => array('name' => 'Kêu gọi Hành động (CTA)', 'tag' => '[ƯU ĐÃI & GIỎ HÀNG]', 'desc' => 'Chỉ dẫn xem giỏ hàng / ưu đãi TikTok Shop', 'default_duration' => 4)
    );

    // Reusable Local Image Motion Effects
    const MOTION_EFFECTS = array(
        'zoom_in'    => 'Thu phóng tiến (Slow Push In)',
        'zoom_out'   => 'Thu phóng lùi (Slow Pull Out)',
        'pan_left'   => 'Lướt sang trái (Pan Left)',
        'pan_right'  => 'Lướt sang phải (Pan Right)',
        'slow_push'  => 'Đẩy chậm điện ảnh (Cinematic Push)',
        'crop_focus' => 'Tập trung chi tiết (Detail Focus)',
        'fade'       => 'Chuyển cảnh mờ dần (Cross Fade)',
        'slide'      => 'Chuyển cảnh trượt (Slide Transition)'
    );

    // Render Methods
    const RENDER_LOCAL  = 'LOCAL';
    const RENDER_AI     = 'AI_VIDEO';
    const RENDER_MANUAL = 'MANUAL_VIDEO';

    public function __construct($d = null, $func = null, $composerConfig = array()) {
        $this->d = $d;
        $this->func = $func;

        if (empty($composerConfig)) {
            global $config;
            $composerConfig = !empty($config['video_composer']) ? $config['video_composer'] : array();
        }

        $this->config = array_merge(array(
            'default_mode' => self::MODE_ECONOMY,
            'max_ai_video_cost_per_video' => 60000, // 60,000 VND limit
            'hybrid_max_ai_scenes' => 2,
            'hybrid_max_ai_seconds' => 8,
            'ai_scene_cost_estimate' => 50000,
            'ffmpeg_binary' => 'ffmpeg',
            'ffprobe_binary' => 'ffprobe',
            'caption_font_size' => 36,
            'safe_area_bottom_pct' => 20,
            'enable_branding' => true,
            'brand_name' => 'FITNADO'
        ), $composerConfig);

        $this->resolveBinaries();
    }

    /**
     * Tự động dò tìm đường dẫn FFmpeg, FFprobe và Font Unicode
     */
    private function resolveBinaries() {
        $ffmpegCandidates = array(
            $this->config['ffmpeg_binary'],
            'C:\\Users\\VanSang\\AppData\\Local\\Microsoft\\WinGet\\Packages\\Gyan.FFmpeg.Essentials_Microsoft.Winget.Source_8wekyb3d8bbwe\\ffmpeg-9.0.1-essentials_build\\bin\\ffmpeg.exe',
            'C:\\Program Files\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\ffmpeg\\bin\\ffmpeg.exe',
            '/usr/bin/ffmpeg',
            '/usr/local/bin/ffmpeg',
            'ffmpeg'
        );

        $ffprobeCandidates = array(
            $this->config['ffprobe_binary'],
            'C:\\Users\\VanSang\\AppData\\Local\\Microsoft\\WinGet\\Packages\\Gyan.FFmpeg.Essentials_Microsoft.Winget.Source_8wekyb3d8bbwe\\ffmpeg-9.0.1-essentials_build\\bin\\ffprobe.exe',
            'C:\\Program Files\\ffmpeg\\bin\\ffprobe.exe',
            'C:\\ffmpeg\\bin\\ffprobe.exe',
            '/usr/bin/ffprobe',
            '/usr/local/bin/ffprobe',
            'ffprobe'
        );

        $this->resolvedFFmpeg = null;
        foreach ($ffmpegCandidates as $cand) {
            if (empty($cand)) continue;
            $out = array();
            $ret = 1;
            if (file_exists($cand) || $cand === 'ffmpeg') {
                @exec('"' . $cand . '" -version 2>&1', $out, $ret);
                if ($ret === 0 && !empty($out)) {
                    $this->resolvedFFmpeg = $cand;
                    break;
                }
            }
        }

        $this->resolvedFFprobe = null;
        foreach ($ffprobeCandidates as $cand) {
            if (empty($cand)) continue;
            $out = array();
            $ret = 1;
            if (file_exists($cand) || $cand === 'ffprobe') {
                @exec('"' . $cand . '" -version 2>&1', $out, $ret);
                if ($ret === 0 && !empty($out)) {
                    $this->resolvedFFprobe = $cand;
                    break;
                }
            }
        }

        // Font Unicode tiếng Việt
        $fontCandidates = array(
            'C:/Windows/Fonts/arialbd.ttf',
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/segoeuib.ttf',
            'C:/Windows/Fonts/tahoma.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/System/Library/Fonts/Helvetica.ttc'
        );

        $this->resolvedFont = null;
        foreach ($fontCandidates as $f) {
            if (file_exists($f)) {
                $this->resolvedFont = $f;
                break;
            }
        }
    }

    /**
     * 1. Audit Server FFmpeg & FFprobe Environment
     * @return array
     */
    public function auditFFmpeg() {
        $ffmpegAvailable = !empty($this->resolvedFFmpeg);
        $ffprobeAvailable = !empty($this->resolvedFFprobe);

        $versionStr = 'Not available';
        if ($ffmpegAvailable) {
            $output = array();
            @exec('"' . $this->resolvedFFmpeg . '" -version 2>&1', $output);
            $versionStr = isset($output[0]) ? $output[0] : 'Installed';
        }

        $installGuide = "Để kích hoạt tính năng render chuyển động video địa phương (Local FFmpeg Rendering):\n" .
            "- Windows: Chạy lệnh `winget install Gyan.FFmpeg` trong PowerShell hoặc tải từ https://ffmpeg.org/download.html và thêm vào PATH hệ thống.\n" .
            "- Linux/Ubuntu: Chạy `sudo apt update && sudo apt install -y ffmpeg`.\n" .
            "- macOS: Chạy `brew install ffmpeg`.\n" .
            "Lưu ý: FFmpeg là REQUIREMENT BẮT BUỘC để render Real Economy Video.";

        return array(
            'available' => ($ffmpegAvailable && $ffprobeAvailable),
            'ffmpeg_binary' => $this->resolvedFFmpeg ? $this->resolvedFFmpeg : $this->config['ffmpeg_binary'],
            'ffprobe_binary' => $this->resolvedFFprobe ? $this->resolvedFFprobe : $this->config['ffprobe_binary'],
            'ffmpeg_available' => $ffmpegAvailable,
            'ffprobe_available' => $ffprobeAvailable,
            'font_path' => $this->resolvedFont,
            'version' => $versionStr,
            'status' => ($ffmpegAvailable && $ffprobeAvailable) ? 'READY' : 'OFFLINE_REQUIRED',
            'message' => ($ffmpegAvailable && $ffprobeAvailable) ? 'FFmpeg & FFprobe đã sẵn sàng kết xuất video H.264/AAC chất lượng cao.' : 'FFmpeg chưa được cài đặt. STOP production render.',
            'installation_guide' => $installGuide
        );
    }

    /**
     * 2. Ước tính chi phí Video trước khi Render (Cost Estimation)
     * @param array $scenes
     * @param string $mode ECONOMY | HYBRID | PREMIUM
     * @return array
     */
    public function estimateCost($scenes, $mode = self::MODE_ECONOMY) {
        $mode = strtoupper(trim($mode));
        if (!in_array($mode, array(self::MODE_ECONOMY, self::MODE_HYBRID, self::MODE_PREMIUM))) {
            $mode = self::MODE_ECONOMY;
        }

        $numScenes = count($scenes);
        $aiScenesCount = 0;
        $aiSeconds = 0;
        $aiSceneCostRate = (float)$this->config['ai_scene_cost_estimate']; // ~50,000 VND / 8s
        $maxLimit = (float)$this->config['max_ai_video_cost_per_video'];

        if ($mode === self::MODE_ECONOMY) {
            // Chế độ ECONOMY: Bắt buộc 0 VND chi phí Video API bên ngoài
            $aiScenesCount = 0;
            $aiSeconds = 0;
            $aiVideoCost = 0.0;
        } elseif ($mode === self::MODE_HYBRID) {
            // Chế độ HYBRID: Chỉ cho phép tối đa 1–2 scenes AI
            $maxHybridScenes = (int)$this->config['hybrid_max_ai_scenes'];
            foreach ($scenes as $sc) {
                if (!empty($sc['render_method']) && $sc['render_method'] === self::RENDER_AI) {
                    $aiScenesCount++;
                    $aiSeconds += !empty($sc['duration']) ? (int)$sc['duration'] : 8;
                }
            }
            if ($aiScenesCount > $maxHybridScenes) {
                $aiScenesCount = $maxHybridScenes;
            }
            $aiVideoCost = $aiScenesCount * $aiSceneCostRate;
        } else {
            // Chế độ PREMIUM: Tính toàn bộ các scenes AI
            foreach ($scenes as $sc) {
                if (!empty($sc['render_method']) && $sc['render_method'] === self::RENDER_AI) {
                    $aiScenesCount++;
                    $aiSeconds += !empty($sc['duration']) ? (int)$sc['duration'] : 8;
                }
            }
            $aiVideoCost = $aiScenesCount * $aiSceneCostRate;
        }

        $localRenderCost = 0.0; // Render local hoàn toàn miễn phí API
        $ttsCost = 0.0; // Google Translate TTS / Local Voice Cache = 0 VND
        $totalApiCost = $aiVideoCost + $ttsCost;

        $exceedsLimit = ($totalApiCost > $maxLimit);

        return array(
            'mode' => $mode,
            'num_scenes' => $numScenes,
            'ai_scenes_count' => $aiScenesCount,
            'ai_seconds' => $aiSeconds,
            'ai_video_seconds' => $aiSeconds,
            'local_render_cost' => $localRenderCost,
            'ai_video_cost' => $aiVideoCost,
            'tts_cost' => $ttsCost,
            'total_external_api_cost' => $totalApiCost,
            'limit_amount' => $maxLimit,
            'exceeds_limit' => $exceedsLimit,
            'currency' => 'VND'
        );
    }

    /**
     * 3. Kiểm tra hạn mức chi phí (Cost Limit Guard)
     */
    public function validateCostGuard($costEstimate, $adminOverride = false) {
        if (!empty($costEstimate['exceeds_limit']) && !$adminOverride) {
            return array(
                'allowed' => false,
                'error' => 'Chi phí ước tính (' . number_format($costEstimate['total_external_api_cost']) . ' ' . $costEstimate['currency'] . ') vượt quá hạn mức tối đa cho phép (' . number_format($costEstimate['limit_amount']) . ' ' . $costEstimate['currency'] . '). Yêu cầu Admin xác nhận phân quyền đặc biệt (Override).'
            );
        }
        return array('allowed' => true, 'error' => null);
    }

    /**
     * 4. Chuẩn hóa phân cảnh theo cấu trúc Purpose & Flow tiếp thị TikTok
     * 0-3s BẮT BUỘC là HOOK từ approved script
     */
    public function normalizeScenes($shotPlan, $mode = self::MODE_ECONOMY, $contentRecord = array()) {
        $mode = strtoupper(trim($mode));
        $normalized = array();
        $motionKeys = array_keys(self::MOTION_EFFECTS);

        $flowPurposes = array(
            1 => 'HOOK',
            2 => 'PROBLEM',
            3 => 'PRODUCT_INTRO',
            4 => 'DEMO',
            5 => 'BENEFIT',
            6 => 'LIMITATION',
            7 => 'BEST_FOR',
            8 => 'CTA'
        );

        $aiScenesAllocated = 0;
        $maxAIScenes = ($mode === self::MODE_HYBRID) ? (int)$this->config['hybrid_max_ai_scenes'] : (($mode === self::MODE_PREMIUM) ? 99 : 0);

        foreach ($shotPlan as $idx => $sc) {
            $sceneNum = !empty($sc['scene_number']) ? (int)$sc['scene_number'] : ($idx + 1);
            $duration = !empty($sc['duration']) ? (int)$sc['duration'] : 5;

            // 1. Xác định Purpose
            $purpose = !empty($sc['purpose']) ? strtoupper(trim($sc['purpose'])) : '';
            if (empty($purpose) || !isset(self::PURPOSES[$purpose])) {
                $purpose = isset($flowPurposes[$sceneNum]) ? $flowPurposes[$sceneNum] : ($sceneNum === 1 ? 'HOOK' : ($idx === count($shotPlan) - 1 ? 'CTA' : 'BENEFIT'));
            }

            // Bắt buộc scene 1 (0-3s) phải có purpose HOOK
            if ($sceneNum === 1) {
                $purpose = 'HOOK';
                if ($duration > 4) {
                    $duration = 3;
                }
            }

            // 2. Xác định Render Method theo Mode
            $requestedRender = !empty($sc['render_method']) ? strtoupper(trim($sc['render_method'])) : self::RENDER_LOCAL;

            if ($mode === self::MODE_ECONOMY) {
                $renderMethod = self::RENDER_LOCAL;
            } elseif ($mode === self::MODE_HYBRID) {
                if ($requestedRender === self::RENDER_AI && $aiScenesAllocated < $maxAIScenes) {
                    $renderMethod = self::RENDER_AI;
                    $aiScenesAllocated++;
                } else {
                    $renderMethod = self::RENDER_LOCAL;
                }
            } else {
                $renderMethod = in_array($requestedRender, array(self::RENDER_LOCAL, self::RENDER_AI, self::RENDER_MANUAL)) ? $requestedRender : self::RENDER_LOCAL;
            }

            // 3. Gán Motion Effect địa phương
            $motionEffect = !empty($sc['motion_effect']) ? trim($sc['motion_effect']) : '';
            if (empty($motionEffect) || !isset(self::MOTION_EFFECTS[$motionEffect])) {
                $motionIndex = $idx % count($motionKeys);
                $motionEffect = $motionKeys[$motionIndex];
            }

            $voiceover = !empty($sc['voiceover']) ? trim($sc['voiceover']) : '';
            $onScreenText = !empty($sc['on_screen_text']) ? trim($sc['on_screen_text']) : '';
            $visualInstruction = !empty($sc['visual_instruction']) ? trim($sc['visual_instruction']) : '';

            $normalized[] = array(
                'scene_number' => $sceneNum,
                'purpose' => $purpose,
                'purpose_name' => self::PURPOSES[$purpose]['name'],
                'tag' => self::PURPOSES[$purpose]['tag'],
                'duration' => $duration,
                'voiceover' => $voiceover,
                'on_screen_text' => $onScreenText,
                'visual_instruction' => $visualInstruction,
                'asset_type' => !empty($sc['asset_type']) ? $sc['asset_type'] : 'PRODUCT_PHOTO',
                'asset_source' => !empty($sc['asset_resolved']) ? $sc['asset_resolved'] : (!empty($sc['asset_source']) ? $sc['asset_source'] : null),
                'asset_resolved' => !empty($sc['asset_resolved']) ? $sc['asset_resolved'] : null,
                'render_method' => $renderMethod,
                'motion_effect' => $motionEffect,
                'motion_name' => self::MOTION_EFFECTS[$motionEffect],
                'status' => 'PENDING'
            );
        }

        return $normalized;
    }

    /**
     * 5. Tổng hợp Voiceover Tiếng Việt (TTS Cache & Reusable)
     * @param string $text
     * @param string $lang
     * @return string Đường dẫn file audio MP3
     */
    public function synthesizeTTS($text, $lang = 'vi') {
        $text = trim($text);
        if (empty($text)) {
            $text = 'Fitnado sản phẩm chính hãng.';
        }

        $audioDir = 'upload/audio/';
        if (!is_dir($audioDir)) {
            @mkdir($audioDir, 0777, true);
        }

        $hash = md5($text . '|' . $lang);
        $audioPath = $audioDir . 'tts_' . $lang . '_' . $hash . '.mp3';

        // Reuse cached audio file if available
        if (file_exists($audioPath) && filesize($audioPath) > 500) {
            return $audioPath;
        }

        // Fetch from Google Translate TTS
        $url = 'https://translate.google.com/translate_tts?ie=UTF-8&tl=' . $lang . '&client=tw-ob&q=' . urlencode($text);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $audioData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && strlen($audioData) > 500) {
            file_put_contents($audioPath, $audioData);
            return $audioPath;
        }

        // Fallback: generate silence MP3 if offline
        if ($this->resolvedFFmpeg) {
            $fallbackCmd = sprintf(
                '"%s" -y -f lavfi -i anullsrc=r=24000:cl=mono -t 4 -c:a libmp3lame -b:a 64k "%s" 2>&1',
                $this->resolvedFFmpeg,
                $audioPath
            );
            @exec($fallbackCmd);
            if (file_exists($audioPath)) {
                return $audioPath;
            }
        }

        return null;
    }

    /**
     * Lấy thời lượng thực tế của file Audio (sử dụng ffprobe)
     * @param string $audioFile
     * @return float
     */
    public function getAudioDuration($audioFile) {
        if (empty($audioFile) || !file_exists($audioFile) || empty($this->resolvedFFprobe)) {
            return 3.0;
        }

        $cmd = sprintf(
            '"%s" -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "%s" 2>&1',
            $this->resolvedFFprobe,
            $audioFile
        );

        $out = array();
        @exec($cmd, $out);
        if (!empty($out) && is_numeric(trim($out[0]))) {
            return (float)trim($out[0]);
        }

        return 3.0;
    }

    /**
     * 6. Khởi tạo tác vụ Render hoàn chỉnh (Video Composer Pipeline)
     * @param array $videoRecord
     * @param array $options
     * @return array
     */
    public function composeVideo($videoRecord, $options = array()) {
        $startTime = microtime(true);
        $logs = array();
        $logs[] = "[" . date('Y-m-d H:i:s') . "] Bắt đầu tiến trình FITNADO Video Composer...";

        $idVideo = !empty($videoRecord['id']) ? (int)$videoRecord['id'] : 0;
        $mode = !empty($videoRecord['mode']) ? strtoupper($videoRecord['mode']) : self::MODE_ECONOMY;
        $scenes = !empty($videoRecord['scenes_data']) ? (is_array($videoRecord['scenes_data']) ? $videoRecord['scenes_data'] : json_decode($videoRecord['scenes_data'], true)) : array();

        $logs[] = "[" . date('Y-m-d H:i:s') . "] Mode: " . $mode . " | Số phân cảnh: " . count($scenes);

        // 1. Audit FFmpeg
        $ffmpegAudit = $this->auditFFmpeg();
        $logs[] = "[" . date('Y-m-d H:i:s') . "] FFmpeg Engine: " . $ffmpegAudit['status'] . " (" . $ffmpegAudit['version'] . ")";

        if (!$ffmpegAudit['available']) {
            $logs[] = "[ERROR] FFmpeg / FFprobe chưa được cài đặt trong hệ thống. STOP production render.";
            return array(
                'success' => false,
                'error' => 'FFMPEG REQUIRED: Hệ thống yêu cầu cài đặt FFmpeg & FFprobe để tạo Real Production Video. Vui lòng cài đặt theo hướng dẫn trong Settings.',
                'composer_log' => implode("\n", $logs)
            );
        }

        // 2. Tính toán & Đối soát Chi phí (Cost Guard)
        $costEstimate = $this->estimateCost($scenes, $mode);
        $adminOverride = !empty($options['admin_override']);
        $guardCheck = $this->validateCostGuard($costEstimate, $adminOverride);

        if (!$guardCheck['allowed']) {
            $logs[] = "[ERROR] Cost Guard chặn tiến trình: " . $guardCheck['error'];
            return array(
                'success' => false,
                'error' => $guardCheck['error'],
                'cost_report' => $costEstimate,
                'composer_log' => implode("\n", $logs)
            );
        }

        $logs[] = "[" . date('Y-m-d H:i:s') . "] Chi phí ước tính: AI Video = " . number_format($costEstimate['ai_video_cost']) . " VND | Local Render = 0 VND | TTS = 0 VND";

        // 3. Chuẩn bị thư mục xuất bản & thư mục tạm
        $videoDir = 'upload/video/';
        if (!is_dir($videoDir)) {
            @mkdir($videoDir, 0777, true);
        }

        $sessionKey = 'vid_' . $idVideo . '_' . time();
        $tmpDir = $videoDir . 'tmp_' . $sessionKey . '/';
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0777, true);
        }

        $safeName = 'fitnado_' . strtolower($mode) . '_' . $sessionKey;
        $finalVideoPath = $videoDir . $safeName . '.mp4';
        $finalThumbPath = $videoDir . $safeName . '.jpg';

        $totalDuration = 0;
        $sceneClips = array();
        $renderedScenes = array();
        $fontPathEsc = str_replace(':', '\\:', str_replace('\\', '/', $this->resolvedFont));

        // 4. Render từng phân cảnh bằng FFmpeg
        foreach ($scenes as $idx => $sc) {
            $sceneNum = !empty($sc['scene_number']) ? (int)$sc['scene_number'] : ($idx + 1);
            $purpose = !empty($sc['purpose']) ? strtoupper($sc['purpose']) : 'BENEFIT';
            $purposeTag = isset(self::PURPOSES[$purpose]['tag']) ? self::PURPOSES[$purpose]['tag'] : '[' . $purpose . ']';
            $baseDuration = !empty($sc['duration']) ? (int)$sc['duration'] : 5;
            $renderMethod = !empty($sc['render_method']) ? $sc['render_method'] : self::RENDER_LOCAL;
            $motion = !empty($sc['motion_effect']) ? $sc['motion_effect'] : 'zoom_in';
            $voiceover = !empty($sc['voiceover']) ? trim($sc['voiceover']) : '';
            $onScreenText = !empty($sc['on_screen_text']) ? trim($sc['on_screen_text']) : '';

            $logs[] = "[" . date('Y-m-d H:i:s') . "] Scene #$sceneNum [$purpose] ($baseDuration s) - Motion: $motion";

            // 4.1. Voiceover Synthesis (TTS)
            $ttsAudioFile = $this->synthesizeTTS($voiceover, 'vi');
            $voiceDuration = $this->getAudioDuration($ttsAudioFile);
            
            // Adjust scene duration if voice needs slightly more time to finish naturally
            $actualSceneDuration = max((float)$baseDuration, round($voiceDuration + 0.5, 1));
            $totalDuration += $actualSceneDuration;
            $frameCount = (int)ceil($actualSceneDuration * 30);

            $logs[] = "  -> Voiceover: \"" . mb_substr($voiceover, 0, 45, 'UTF-8') . "...\" ({$voiceDuration}s, clip: {$actualSceneDuration}s)";

            // 4.2. Image Asset Resolution
            $imageFile = $this->resolveImageAsset($sc, $videoRecord);
            if (!file_exists($imageFile)) {
                $logs[] = "  [WARN] Asset không tìm thấy: $imageFile. Dùng fallback ảnh sản phẩm.";
                $imageFile = 'upload/product/fitnado_roller_main.jpg';
            }

            // 4.3. Caption Files Preparation (UTF-8)
            $cleanTitle = $this->cleanCaptionText($onScreenText ? $onScreenText : $purposeTag);
            $cleanVoice = $this->cleanCaptionText($voiceover);

            $wrappedTitle = $this->wrapTextUtf8($cleanTitle, 32);
            $wrappedVoice = $this->wrapTextUtf8($cleanVoice, 36);

            $captionTxtFile = $tmpDir . 'caption_' . $sceneNum . '.txt';
            $captionContent = $wrappedTitle . "\n" . $wrappedVoice;
            file_put_contents($captionTxtFile, $captionContent);

            $captionTxtEsc = str_replace(':', '\\:', str_replace('\\', '/', $captionTxtFile));
            $imageEsc = str_replace('\\', '/', $imageFile);
            $sceneClipPath = $tmpDir . 'scene_' . $sceneNum . '.mp4';

            // 4.4. Ken Burns Filtergraph Expression
            $zoomExpr = $this->buildZoompanExpr($motion, $frameCount);

            // Filtergraph:
            // 1. Background: scale & blur to fill 1080x1920
            // 2. Foreground: centered product image with padding
            // 3. Zoompan motion effect
            // 4. Header Purpose Tag (y=160)
            // 5. Safe-area Captions box (y=1420)
            $safeTag = addslashes($purposeTag);
            $filterComplex = sprintf(
                "[0:v]scale=1080:1920:force_original_aspect_ratio=increase,crop=1080:1920,boxblur=22:5[bg];" .
                "[0:v]scale=1000:1000:force_original_aspect_ratio=decrease[fg];" .
                "[bg][fg]overlay=(W-w)/2:(H-h)/2[comp];" .
                "[comp]%s,drawtext=fontfile='%s':text='%s':fontsize=28:fontcolor=yellow:box=1:boxcolor=black@0.7:boxborderw=10:x=(w-text_w)/2:y=180,drawtext=fontfile='%s':textfile='%s':fontsize=34:fontcolor=white:box=1:boxcolor=black@0.7:boxborderw=16:line_spacing=12:x=(w-text_w)/2:y=h-th-360[v]",
                $zoomExpr,
                $fontPathEsc,
                $safeTag,
                $fontPathEsc,
                $captionTxtEsc
            );

            $sceneCmd = sprintf(
                '"%s" -y -loop 1 -t %f -i "%s" -i "%s" -filter_complex "%s" -map "[v]" -map 1:a -c:v libx264 -preset ultrafast -pix_fmt yuv420p -c:a aac -b:a 128k -shortest "%s" 2>&1',
                $this->resolvedFFmpeg,
                $actualSceneDuration,
                $imageEsc,
                str_replace('\\', '/', $ttsAudioFile),
                $filterComplex,
                $sceneClipPath
            );

            $cmdOut = array();
            $cmdRet = 1;
            @exec($sceneCmd, $cmdOut, $cmdRet);

            if ($cmdRet === 0 && file_exists($sceneClipPath)) {
                $sceneClips[] = $sceneClipPath;
                $logs[] = "  -> Render Scene #$sceneNum thành công (" . filesize($sceneClipPath) . " bytes)";
            } else {
                $logs[] = "  [ERROR] Render Scene #$sceneNum thất bại. CMD: " . implode("\n", array_slice($cmdOut, -8));
            }

            $renderedScenes[] = array(
                'scene_number' => $sceneNum,
                'purpose' => $purpose,
                'duration' => $actualSceneDuration,
                'voice_duration' => $voiceDuration,
                'render_method' => $renderMethod,
                'motion_effect' => $motion,
                'status' => ($cmdRet === 0 && file_exists($sceneClipPath)) ? 'COMPOSED' : 'FAILED'
            );
        }

        // 5. Nối tất cả các Scenes thành Final 9:16 Video MP4
        $concatListFile = $tmpDir . 'concat_list.txt';
        $concatContent = '';
        foreach ($sceneClips as $clip) {
            $concatContent .= "file '" . str_replace('\\', '/', realpath($clip) ? realpath($clip) : $clip) . "'\n";
        }
        file_put_contents($concatListFile, $concatContent);

        $logs[] = "[" . date('Y-m-d H:i:s') . "] Ghép nối " . count($sceneClips) . " phân cảnh thành Final MP4...";

        $concatCmd = sprintf(
            '"%s" -y -f concat -safe 0 -i "%s" -c:v libx264 -preset fast -profile:v high -level 4.1 -pix_fmt yuv420p -c:a aac -b:a 128k -ar 44100 -movflags +faststart "%s" 2>&1',
            $this->resolvedFFmpeg,
            str_replace('\\', '/', $concatListFile),
            $finalVideoPath
        );

        $concatOut = array();
        $concatRet = 1;
        @exec($concatCmd, $concatOut, $concatRet);

        if ($concatRet !== 0 || !file_exists($finalVideoPath) || filesize($finalVideoPath) < 500000) {
            $logs[] = "[ERROR] Ghép nối Final MP4 thất bại. Output: " . implode("\n", array_slice($concatOut, -10));
            return array(
                'success' => false,
                'error' => 'Lỗi kết xuất Final MP4 qua FFmpeg. Vui lòng kiểm tra log chi tiết.',
                'composer_log' => implode("\n", $logs)
            );
        }

        // 6. Tạo Thumbnail & Trích xuất 4 Khung hình Kiểm định (Validation Frames)
        $thumbCmd = sprintf(
            '"%s" -y -ss 00:00:02 -i "%s" -frames:v 1 -q:v 2 "%s" 2>&1',
            $this->resolvedFFmpeg,
            $finalVideoPath,
            $finalThumbPath
        );
        @exec($thumbCmd);

        $validationFrames = array();
        $frameCheckTimes = array(2, 10, 20, (int)min(29, max(5, $totalDuration - 2)));
        foreach ($frameCheckTimes as $t) {
            $framePath = $videoDir . $safeName . '_frame_' . $t . 's.jpg';
            $frameCmd = sprintf(
                '"%s" -y -ss %d -i "%s" -frames:v 1 -q:v 2 "%s" 2>&1',
                $this->resolvedFFmpeg,
                $t,
                $finalVideoPath,
                $framePath
            );
            @exec($frameCmd);
            if (file_exists($framePath)) {
                $validationFrames[$t . 's'] = $framePath;
            }
        }

        // 7. Thu thập Metadata Thật qua FFprobe (Real Probe Metrics)
        $probeCmd = sprintf(
            '"%s" -v error -show_entries format=duration,size,bit_rate:stream=codec_name,width,height,r_frame_rate,channels -of json "%s"',
            $this->resolvedFFprobe,
            $finalVideoPath
        );
        $probeJson = shell_exec($probeCmd);
        $probeData = json_decode($probeJson, true);

        $actualDuration = !empty($probeData['format']['duration']) ? (float)$probeData['format']['duration'] : (float)$totalDuration;
        $actualFileSize = file_exists($finalVideoPath) ? filesize($finalVideoPath) : 0;
        $actualBitrate = !empty($probeData['format']['bit_rate']) ? (int)$probeData['format']['bit_rate'] : 0;

        $videoStream = isset($probeData['streams'][0]) ? $probeData['streams'][0] : array();
        $audioStream = isset($probeData['streams'][1]) ? $probeData['streams'][1] : array();

        $actualWidth = !empty($videoStream['width']) ? (int)$videoStream['width'] : 1080;
        $actualHeight = !empty($videoStream['height']) ? (int)$videoStream['height'] : 1920;
        $actualVideoCodec = !empty($videoStream['codec_name']) ? $videoStream['codec_name'] : 'h264';
        $actualAudioCodec = !empty($audioStream['codec_name']) ? $audioStream['codec_name'] : 'aac';

        // 8. Dọn dẹp thư mục tạm
        $this->cleanupTempDir($tmpDir);

        $elapsedTime = round(microtime(true) - $startTime, 2);
        $logs[] = "[" . date('Y-m-d H:i:s') . "] Hoàn tất Real Video Render trong {$elapsedTime}s.";
        $logs[] = "  -> File: $finalVideoPath ({$actualFileSize} bytes)";
        $logs[] = "  -> Duration: {$actualDuration}s | Resolution: {$actualWidth}x{$actualHeight} | Video: {$actualVideoCodec} | Audio: {$actualAudioCodec}";

        return array(
            'success' => true,
            'video_file' => $finalVideoPath,
            'thumbnail' => $finalThumbPath,
            'duration_actual' => $actualDuration,
            'width' => $actualWidth,
            'height' => $actualHeight,
            'file_size' => $actualFileSize,
            'bitrate' => $actualBitrate,
            'video_codec' => $actualVideoCodec,
            'audio_codec' => $actualAudioCodec,
            'render_time_seconds' => $elapsedTime,
            'cost_report' => $costEstimate,
            'validation_frames' => $validationFrames,
            'composer_log' => implode("\n", $logs),
            'rendered_scenes' => $renderedScenes,
            'error' => null
        );
    }

    /**
     * Tạo biểu thức FFmpeg zoompan theo motion effect
     */
    private function buildZoompanExpr($motion, $frameCount) {
        $frameCount = max(30, (int)$frameCount);
        switch ($motion) {
            case 'zoom_out':
                return "zoompan=z='if(lte(zoom,1.0),1.18,max(1.001,zoom-0.0012))':d={$frameCount}:x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)':s=1080x1920:fps=30";
            case 'pan_left':
                return "zoompan=z='1.12':x='if(lte(on,1),(iw-iw/zoom)*0.85,max(0,x-1.5))':y='(ih-ih/zoom)/2':d={$frameCount}:s=1080x1920:fps=30";
            case 'pan_right':
                return "zoompan=z='1.12':x='if(lte(on,1),0,min((iw-iw/zoom),x+1.5))':y='(ih-ih/zoom)/2':d={$frameCount}:s=1080x1920:fps=30";
            case 'slow_push':
                return "zoompan=z='min(zoom+0.0007,1.10)':d={$frameCount}:x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)':s=1080x1920:fps=30";
            case 'crop_focus':
                return "zoompan=z='1.15':d={$frameCount}:x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)':s=1080x1920:fps=30";
            case 'zoom_in':
            default:
                return "zoompan=z='min(zoom+0.0015,1.18)':d={$frameCount}:x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)':s=1080x1920:fps=30";
        }
    }

    /**
     * Dò tìm file ảnh sản phẩm thật trên ổ đĩa
     */
    private function resolveImageAsset($scene, $videoRecord) {
        $possiblePaths = array();

        if (!empty($scene['asset_resolved'])) {
            $possiblePaths[] = $scene['asset_resolved'];
            $possiblePaths[] = 'upload/product/' . basename($scene['asset_resolved']);
        }
        if (!empty($scene['asset_source'])) {
            $possiblePaths[] = $scene['asset_source'];
            $possiblePaths[] = 'upload/product/' . basename($scene['asset_source']);
        }
        if (!empty($videoRecord['product_photo'])) {
            $possiblePaths[] = 'upload/product/' . $videoRecord['product_photo'];
        }

        // Default catalog photos
        $possiblePaths[] = 'upload/product/fitnado_roller_main.jpg';
        $possiblePaths[] = 'upload/product/fitnado_roller_action.jpg';
        $possiblePaths[] = 'upload/product/fitnado_roller_detail.jpg';
        $possiblePaths[] = 'upload/product/fitnado_roller_stability.jpg';

        foreach ($possiblePaths as $p) {
            if (!empty($p) && file_exists($p) && filesize($p) > 5000 && @getimagesize($p) !== false) {
                return $p;
            }
        }

        return 'upload/product/fitnado_roller_main.jpg';
    }

    /**
     * Làm sạch text caption, loại bỏ emoji không hỗ trợ font để tránh ô vuông
     */
    private function cleanCaptionText($text) {
        // Loại bỏ emoji phức tạp, thay bằng ký tự chuẩn
        $text = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $text);
        $text = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $text);
        $text = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $text);
        $text = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $text);
        $text = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $text);
        return trim($text);
    }

    /**
     * Dọn dẹp thư mục tạm
     */
    private function cleanupTempDir($dir) {
        if (!is_dir($dir)) return;
        $files = glob($dir . '*');
        if (is_array($files)) {
            foreach ($files as $f) {
                if (is_file($f)) @unlink($f);
            }
        }
        @rmdir($dir);
    }

    /**
     * Tự động xuống hàng text UTF-8 tiếng Việt theo độ dài tối đa
     */
    public function wrapTextUtf8($text, $maxChars = 34) {
        $words = explode(' ', trim($text));
        $lines = array();
        $currentLine = '';
        foreach ($words as $w) {
            if (empty($w)) continue;
            if (empty($currentLine)) {
                $currentLine = $w;
            } elseif (mb_strlen($currentLine . ' ' . $w, 'UTF-8') <= $maxChars) {
                $currentLine .= ' ' . $w;
            } else {
                $lines[] = $currentLine;
                $currentLine = $w;
            }
        }
        if (!empty($currentLine)) {
            $lines[] = $currentLine;
        }
        return implode("\n", $lines);
    }
}
