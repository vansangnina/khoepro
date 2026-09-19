<?php
/**
 * FITNADO Video Provider Abstraction Layer
 * Phase 06: AI Video Production Engine
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

/**
 * Interface VideoProviderInterface
 */
interface VideoProviderInterface {
    /**
     * Khởi tạo tác vụ render video với các tham số dự án
     * @param array $videoData
     * @return array ['success' => bool, 'provider_job_id' => string, 'status' => string, 'error' => string, 'cost' => float]
     */
    public function createRenderJob($videoData);

    /**
     * Kiểm tra trạng thái tác vụ từ phía provider
     * @param string $providerJobId
     * @return array ['status' => string, 'progress' => int, 'video_url' => string, 'thumbnail_url' => string, 'duration' => float, 'width' => int, 'height' => int, 'file_size' => int, 'error' => string]
     */
    public function checkJobStatus($providerJobId);

    /**
     * Tải video thành phẩm từ remote URL về lưu trữ cục bộ
     * @param string $remoteUrl
     * @param string $localDestination
     * @return array ['success' => bool, 'local_path' => string, 'file_size' => int, 'error' => string]
     */
    public function downloadVideoAsset($remoteUrl, $localDestination);

    /**
     * Hủy bỏ tác vụ render
     * @param string $providerJobId
     * @return bool
     */
    public function cancelRenderJob($providerJobId);

    /**
     * Khai báo năng lực của provider
     * @return array
     */
    public function getCapabilities();
}

/**
 * Class MockVideoProvider
 * Giả lập quy trình sản xuất video phục vụ kiểm thử nội bộ và offline rendering
 */
class MockVideoProvider implements VideoProviderInterface {
    private $d;
    private $func;

    public function __construct($d = null, $func = null) {
        $this->d = $d;
        $this->func = $func;
    }

    public function createRenderJob($videoData) {
        $scenes = !empty($videoData['scenes_data']) ? (is_array($videoData['scenes_data']) ? $videoData['scenes_data'] : json_decode($videoData['scenes_data'], true)) : array();
        $targetDuration = !empty($videoData['target_duration']) ? (int)$videoData['target_duration'] : 30;

        // Sinh provider_job_id giả lập
        $providerJobId = 'mock_job_' . uniqid() . '_' . time();

        // Ước tính chi phí mock ($0.05 / 30s)
        $cost = round(($targetDuration / 30) * 0.05, 4);

        return array(
            'success' => true,
            'provider_job_id' => $providerJobId,
            'status' => 'PROCESSING',
            'cost' => $cost,
            'error' => null
        );
    }

    public function checkJobStatus($providerJobId) {
        // Mock provider hoàn thành ngay với sample output
        $sampleDuration = 30.0;
        $sampleWidth = 1080;
        $sampleHeight = 1920;

        return array(
            'status' => 'READY',
            'progress' => 100,
            'video_url' => 'mock://upload/video/sample_vertical_render.mp4',
            'thumbnail_url' => 'mock://upload/video/sample_vertical_thumb.jpg',
            'duration' => $sampleDuration,
            'width' => $sampleWidth,
            'height' => $sampleHeight,
            'file_size' => 2457600, // ~2.4MB
            'error' => null
        );
    }

    public function downloadVideoAsset($remoteUrl, $localDestination) {
        $uploadDir = dirname($localDestination);
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        // Tạo file MP4 mẫu giả lập hợp lệ (hoặc copy placeholder)
        if (strpos($remoteUrl, 'mock://') === 0 || !filter_var($remoteUrl, FILTER_VALIDATE_URL)) {
            // Tạo binary file placeholder có header MP4 hợp lệ để test validation
            $mp4Signature = "\x00\x00\x00\x18ftypmp42\x00\x00\x00\x00isommp42\x00\x00\x00\x08free";
            $mockData = $mp4Signature . str_repeat("\x00", 1024 * 100); // ~100KB mock valid MP4
            file_put_contents($localDestination, $mockData);

            // Tạo thumbnail placeholder kèm theo
            $thumbPath = preg_replace('/\.mp4$/i', '.jpg', $localDestination);
            if ($thumbPath && $thumbPath !== $localDestination) {
                // JPEG header
                $jpgHeader = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00`\x00`\x00\x00\xFF\xDB";
                file_put_contents($thumbPath, $jpgHeader . str_repeat("\x00", 1024));
            }

            return array(
                'success' => true,
                'local_path' => $localDestination,
                'file_size' => filesize($localDestination),
                'error' => null
            );
        }

        // Nếu là remote URL thật -> SSRF check & stream download
        $parsed = parse_url($remoteUrl);
        if (empty($parsed['scheme']) || !in_array(strtolower($parsed['scheme']), array('http', 'https'))) {
            return array('success' => false, 'local_path' => null, 'file_size' => 0, 'error' => 'Invalid URL scheme');
        }

        $host = isset($parsed['host']) ? strtolower($parsed['host']) : '';
        // SSRF protection: block private/loopback IP
        if (in_array($host, array('localhost', '127.0.0.1', '::1')) || preg_match('/^(10\.|192\.168\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/', $host)) {
            return array('success' => false, 'local_path' => null, 'file_size' => 0, 'error' => 'SSRF blocked: Private host access not allowed');
        }

        $fp = @fopen($localDestination, 'w+');
        if (!$fp) {
            return array('success' => false, 'local_path' => null, 'file_size' => 0, 'error' => 'Cannot open destination for writing');
        }

        $ch = curl_init($remoteUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $exec = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
        fclose($fp);

        if (!$exec || $httpCode < 200 || $httpCode >= 300) {
            @unlink($localDestination);
            return array('success' => false, 'local_path' => null, 'file_size' => 0, 'error' => 'Download failed (HTTP ' . $httpCode . '): ' . $curlErr);
        }

        return array(
            'success' => true,
            'local_path' => $localDestination,
            'file_size' => filesize($localDestination),
            'error' => null
        );
    }

    public function cancelRenderJob($providerJobId) {
        return true;
    }

    public function getCapabilities() {
        return array(
            'name' => 'Mock Video Engine',
            'version' => '1.0.0',
            'text_to_video' => true,
            'image_to_video' => true,
            'avatar' => false,
            'voice' => true,
            'captions' => true,
            'max_duration' => 60,
            'aspect_ratios' => array('9:16', '1:1', '16:9'),
            'languages' => array('vi', 'en'),
            'is_configured' => true
        );
    }
}

/**
 * Class ExternalVideoProvider
 * Adapter kết nối API Video thương mại (Creatify / Arcads / HeyGen / Runway)
 */
class ExternalVideoProvider implements VideoProviderInterface {
    private $apiKey;
    private $apiEndpoint;
    private $providerName;

    public function __construct($apiKey = '', $apiEndpoint = '', $providerName = 'creatify') {
        $this->apiKey = $apiKey;
        $this->apiEndpoint = $apiEndpoint;
        $this->providerName = $providerName;
    }

    public function createRenderJob($videoData) {
        if (empty($this->apiKey)) {
            return array(
                'success' => false,
                'provider_job_id' => null,
                'status' => 'FAILED',
                'cost' => 0,
                'error' => 'External Video API Key is not configured for provider: ' . $this->providerName
            );
        }

        // Chuẩn bị payload theo chuẩn REST API của provider
        $payload = array(
            'aspect_ratio' => !empty($videoData['aspect_ratio']) ? $videoData['aspect_ratio'] : '9:16',
            'target_duration' => !empty($videoData['target_duration']) ? (int)$videoData['target_duration'] : 30,
            'voice_id' => !empty($videoData['voice_id']) ? $videoData['voice_id'] : 'vi-VN-Standard-A',
            'scenes' => !empty($videoData['scenes_data']) ? (is_array($videoData['scenes_data']) ? $videoData['scenes_data'] : json_decode($videoData['scenes_data'], true)) : array()
        );

        // Gửi cURL POST tới Endpoint
        $ch = curl_init($this->apiEndpoint . '/render');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if (!$res || $httpCode < 200 || $httpCode >= 300) {
            return array(
                'success' => false,
                'provider_job_id' => null,
                'status' => 'FAILED',
                'cost' => 0,
                'error' => 'Provider API error (HTTP ' . $httpCode . '): ' . ($curlErr ?: $res)
            );
        }

        $data = json_decode($res, true);
        return array(
            'success' => true,
            'provider_job_id' => !empty($data['job_id']) ? $data['job_id'] : uniqid('ext_job_'),
            'status' => 'PROCESSING',
            'cost' => !empty($data['cost']) ? (float)$data['cost'] : 0.0,
            'error' => null
        );
    }

    public function checkJobStatus($providerJobId) {
        if (empty($this->apiKey)) {
            return array('status' => 'FAILED', 'progress' => 0, 'video_url' => null, 'thumbnail_url' => null, 'duration' => 0, 'width' => 0, 'height' => 0, 'file_size' => 0, 'error' => 'API key missing');
        }

        $ch = curl_init($this->apiEndpoint . '/jobs/' . urlencode($providerJobId));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->apiKey
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$res || $httpCode !== 200) {
            return array('status' => 'FAILED', 'progress' => 0, 'video_url' => null, 'thumbnail_url' => null, 'duration' => 0, 'width' => 0, 'height' => 0, 'file_size' => 0, 'error' => 'Failed to poll job status');
        }

        $data = json_decode($res, true);
        return array(
            'status' => !empty($data['status']) ? strtoupper($data['status']) : 'PROCESSING',
            'progress' => !empty($data['progress']) ? (int)$data['progress'] : 0,
            'video_url' => !empty($data['video_url']) ? $data['video_url'] : null,
            'thumbnail_url' => !empty($data['thumbnail_url']) ? $data['thumbnail_url'] : null,
            'duration' => !empty($data['duration']) ? (float)$data['duration'] : 0,
            'width' => !empty($data['width']) ? (int)$data['width'] : 1080,
            'height' => !empty($data['height']) ? (int)$data['height'] : 1920,
            'file_size' => !empty($data['file_size']) ? (int)$data['file_size'] : 0,
            'error' => !empty($data['error']) ? $data['error'] : null
        );
    }

    public function downloadVideoAsset($remoteUrl, $localDestination) {
        $mock = new MockVideoProvider();
        return $mock->downloadVideoAsset($remoteUrl, $localDestination);
    }

    public function cancelRenderJob($providerJobId) {
        return true;
    }

    public function getCapabilities() {
        return array(
            'name' => ucfirst($this->providerName) . ' Commercial Engine',
            'version' => '1.0.0',
            'text_to_video' => true,
            'image_to_video' => true,
            'avatar' => true,
            'voice' => true,
            'captions' => true,
            'max_duration' => 60,
            'aspect_ratios' => array('9:16', '16:9'),
            'languages' => array('vi', 'en'),
            'is_configured' => !empty($this->apiKey)
        );
    }
}

/**
 * Class ManualVideoProvider
 * Cho phép Admin tải lên video thành phẩm hoặc ghép thủ công
 */
class ManualVideoProvider implements VideoProviderInterface {
    public function createRenderJob($videoData) {
        return array(
            'success' => true,
            'provider_job_id' => 'manual_' . uniqid(),
            'status' => 'READY',
            'cost' => 0.0,
            'error' => null
        );
    }

    public function checkJobStatus($providerJobId) {
        return array(
            'status' => 'READY',
            'progress' => 100,
            'video_url' => null,
            'thumbnail_url' => null,
            'duration' => 0,
            'width' => 1080,
            'height' => 1920,
            'file_size' => 0,
            'error' => null
        );
    }

    public function downloadVideoAsset($remoteUrl, $localDestination) {
        return array('success' => true, 'local_path' => $localDestination, 'file_size' => file_exists($localDestination) ? filesize($localDestination) : 0, 'error' => null);
    }

    public function cancelRenderJob($providerJobId) {
        return true;
    }

    public function getCapabilities() {
        return array(
            'name' => 'Manual Video Upload',
            'version' => '1.0.0',
            'text_to_video' => false,
            'image_to_video' => false,
            'avatar' => false,
            'voice' => false,
            'captions' => false,
            'max_duration' => 300,
            'aspect_ratios' => array('9:16', '1:1', '16:9'),
            'languages' => array('vi', 'en'),
            'is_configured' => true
        );
    }
}

/**
 * Class BeeknoeeVideoProvider
 * Tích hợp chính thức API sản xuất video Beeknoee (https://platform.beeknoee.com)
 * ROLE: OPTIONAL AI SCENE GENERATOR (Không phải Default Full Video Generator)
 * Chỉ được gọi khi một phân cảnh có render_method = 'AI_VIDEO' trong chế độ HYBRID / PREMIUM.
 * Model: veo-3.1-fast-generate-preview (8s, 9:16)
 * Endpoint: POST /v1/video/generations, GET /v1/video/generations/{job_id}
 */
class BeeknoeeVideoProvider implements VideoProviderInterface {
    private $apiKey;
    private $baseUrl;
    private $model;
    private $aspectRatio;
    private $duration;
    private $timeout;
    private $active;
    private $d;
    private $func;

    public function __construct($beeknoeeConfig = array(), $d = null, $func = null) {
        $this->d = $d;
        $this->func = $func;

        // Ưu tiên nạp từ $config['beeknoee']
        if (empty($beeknoeeConfig)) {
            global $config;
            if (isset($config['beeknoee'])) {
                $beeknoeeConfig = $config['beeknoee'];
            }
        }

        $this->active = !empty($beeknoeeConfig['active']) ? (bool)$beeknoeeConfig['active'] : false;
        $this->apiKey = !empty($beeknoeeConfig['api_key']) ? trim($beeknoeeConfig['api_key']) : '';
        $this->baseUrl = !empty($beeknoeeConfig['base_url']) ? rtrim($beeknoeeConfig['base_url'], '/') : 'https://platform.beeknoee.com';
        $this->model = !empty($beeknoeeConfig['video_model']) ? trim($beeknoeeConfig['video_model']) : 'veo-3.1-fast-generate-preview';
        $this->aspectRatio = !empty($beeknoeeConfig['aspect_ratio']) ? trim($beeknoeeConfig['aspect_ratio']) : '9:16';
        $this->duration = !empty($beeknoeeConfig['duration']) ? (int)$beeknoeeConfig['duration'] : 8;
        $this->timeout = !empty($beeknoeeConfig['timeout']) ? (int)$beeknoeeConfig['timeout'] : 120;
    }

    public function createRenderJob($videoData) {
        // 1. Kiểm tra cấu hình và API key
        if (empty($this->apiKey)) {
            return array(
                'success' => false,
                'provider_job_id' => null,
                'status' => 'FAILED',
                'cost' => 0,
                'error' => 'Beeknoee Video Provider chưa được cấu hình API key. Vui lòng nhập api_key trong libraries/config.php.'
            );
        }

        if (!$this->active) {
            return array(
                'success' => false,
                'provider_job_id' => null,
                'status' => 'FAILED',
                'cost' => 0,
                'error' => 'Beeknoee Video Provider đang ở trạng thái tắt (active = false trong libraries/config.php).'
            );
        }

        // 2. Trích xuất chỉ dẫn phân cảnh từ Shot Plan
        $scenes = !empty($videoData['scenes_data']) ? (is_array($videoData['scenes_data']) ? $videoData['scenes_data'] : json_decode($videoData['scenes_data'], true)) : array();
        $visualInstruction = '';
        $productImageBase64 = null;

        if (!empty($scenes)) {
            $firstScene = $scenes[0];
            $visualInstruction = !empty($firstScene['visual_instruction']) ? $firstScene['visual_instruction'] : '';

            // Kiểm tra xem có ảnh sản phẩm để thực hiện Image-to-Video không
            if (!empty($firstScene['asset_resolved']) && file_exists($firstScene['asset_resolved'])) {
                $imgData = file_get_contents($firstScene['asset_resolved']);
                $mime = mime_content_type($firstScene['asset_resolved']) ?: 'image/jpeg';
                $productImageBase64 = 'data:' . $mime . ';base64,' . base64_encode($imgData);
            }
        }

        // 3. Xây dựng Prompt thương mại chuẩn xác
        $prompt = "Vertical commercial product video for TikTok (9:16). " . ($visualInstruction ?: 'Fitness equipment commercial product showcase.') . " Preserve exact product appearance, preserve logo, preserve color, preserve shape, no additional text, no watermark, realistic gym lighting, high quality 4K commercial shot.";

        $payload = array(
            'model' => $this->model,
            'prompt' => $prompt,
            'aspect_ratio' => $this->aspectRatio,
            'duration' => $this->duration
        );

        if (!empty($productImageBase64)) {
            $payload['image'] = $productImageBase64;
        }

        // 4. Gửi cURL POST tới Beeknoee Video Generations endpoint
        $endpoint = $this->baseUrl . '/v1/video/generations';
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
            'Accept: application/json'
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if (!$response || $httpCode < 200 || $httpCode >= 300) {
            $errMsg = 'Beeknoee API Error (HTTP ' . $httpCode . '): ' . ($curlErr ?: $response);
            $jsonErr = json_decode($response, true);
            if (!empty($jsonErr['error']['message'])) {
                $errMsg = 'Beeknoee Error: ' . $jsonErr['error']['message'];
            } elseif (!empty($jsonErr['message'])) {
                $errMsg = 'Beeknoee Error: ' . $jsonErr['message'];
            }
            return array(
                'success' => false,
                'provider_job_id' => null,
                'status' => 'FAILED',
                'cost' => 0,
                'error' => $errMsg
            );
        }

        $resData = json_decode($response, true);
        $jobId = !empty($resData['job_id']) ? $resData['job_id'] : (!empty($resData['id']) ? $resData['id'] : null);

        if (empty($jobId)) {
            return array(
                'success' => false,
                'provider_job_id' => null,
                'status' => 'FAILED',
                'cost' => 0,
                'error' => 'Beeknoee API không trả về job_id hợp lệ. Phản hồi: ' . substr($response, 0, 200)
            );
        }

        $costVnd = !empty($resData['cost_vnd']) ? (float)$resData['cost_vnd'] : (!empty($resData['cost']) ? (float)$resData['cost'] : 0.0);

        return array(
            'success' => true,
            'provider_job_id' => $jobId,
            'status' => 'PROCESSING',
            'cost' => $costVnd,
            'model' => $this->model,
            'error' => null
        );
    }

    public function checkJobStatus($providerJobId) {
        if (empty($this->apiKey)) {
            return array('status' => 'FAILED', 'progress' => 0, 'video_url' => null, 'thumbnail_url' => null, 'duration' => 0, 'width' => 1080, 'height' => 1920, 'file_size' => 0, 'error' => 'Beeknoee API key missing');
        }

        $endpoint = $this->baseUrl . '/v1/video/generations/' . urlencode($providerJobId);
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->apiKey,
            'Accept: application/json'
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response || $httpCode !== 200) {
            return array(
                'status' => 'FAILED',
                'progress' => 0,
                'video_url' => null,
                'thumbnail_url' => null,
                'duration' => 0,
                'width' => 1080,
                'height' => 1920,
                'file_size' => 0,
                'error' => 'Failed to poll Beeknoee job status (HTTP ' . $httpCode . ')'
            );
        }

        $resData = json_decode($response, true);
        $rawStatus = strtoupper(!empty($resData['status']) ? $resData['status'] : 'PROCESSING');

        $status = 'PROCESSING';
        if (in_array($rawStatus, array('COMPLETED', 'SUCCESS', 'READY', 'FINISHED'))) {
            $status = 'READY';
        } elseif (in_array($rawStatus, array('FAILED', 'ERROR', 'TIMEOUT', 'CANCELLED'))) {
            $status = 'FAILED';
        }

        $videoUrl = !empty($resData['video_url']) ? $resData['video_url'] : (!empty($resData['output']['video_url']) ? $resData['output']['video_url'] : (!empty($resData['download_url']) ? $resData['download_url'] : null));
        if (!empty($videoUrl) && strpos($videoUrl, 'http') !== 0) {
            $videoUrl = $this->baseUrl . $videoUrl;
        }

        $thumbUrl = !empty($resData['thumbnail_url']) ? $resData['thumbnail_url'] : (!empty($resData['output']['thumbnail_url']) ? $resData['output']['thumbnail_url'] : null);
        if (!empty($thumbUrl) && strpos($thumbUrl, 'http') !== 0) {
            $thumbUrl = $this->baseUrl . $thumbUrl;
        }

        $costVnd = !empty($resData['cost_vnd']) ? (float)$resData['cost_vnd'] : (!empty($resData['cost']) ? (float)$resData['cost'] : 0.0);

        return array(
            'status' => $status,
            'raw_status' => $rawStatus,
            'progress' => !empty($resData['progress']) ? (int)$resData['progress'] : ($status === 'READY' ? 100 : 50),
            'video_url' => $videoUrl,
            'thumbnail_url' => $thumbUrl,
            'duration' => !empty($resData['duration']) ? (float)$resData['duration'] : (float)$this->duration,
            'width' => !empty($resData['width']) ? (int)$resData['width'] : 1080,
            'height' => !empty($resData['height']) ? (int)$resData['height'] : 1920,
            'file_size' => !empty($resData['file_size']) ? (int)$resData['file_size'] : 0,
            'cost_vnd' => $costVnd,
            'elapsed_seconds' => !empty($resData['elapsed_seconds']) ? (float)$resData['elapsed_seconds'] : 0,
            'error' => !empty($resData['error']['message']) ? $resData['error']['message'] : (!empty($resData['error']) ? (is_string($resData['error']) ? $resData['error'] : json_encode($resData['error'])) : null)
        );
    }

    public function downloadVideoAsset($remoteUrl, $localDestination) {
        $dir = dirname($localDestination);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $fp = fopen($localDestination, 'w+');
        if (!$fp) {
            return array('success' => false, 'local_path' => null, 'file_size' => 0, 'error' => 'Cannot create file: ' . $localDestination);
        }

        $headers = array('Accept: */*');
        if (!empty($this->apiKey)) {
            $headers[] = 'Authorization: Bearer ' . $this->apiKey;
        }

        $ch = curl_init($remoteUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $exec = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
        fclose($fp);

        if (!$exec || $httpCode < 200 || $httpCode >= 300) {
            @unlink($localDestination);
            return array('success' => false, 'local_path' => null, 'file_size' => 0, 'error' => 'Download failed (HTTP ' . $httpCode . '): ' . $curlErr);
        }

        return array(
            'success' => true,
            'local_path' => $localDestination,
            'file_size' => filesize($localDestination),
            'error' => null
        );
    }

    public function cancelRenderJob($providerJobId) {
        $endpoint = $this->baseUrl . '/v1/video/generations/' . urlencode($providerJobId) . '/cancel';
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->apiKey
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);
        return true;
    }

    public function getCapabilities() {
        return array(
            'name' => 'Beeknoee AI Video Engine',
            'provider' => 'beeknoee',
            'model' => $this->model,
            'base_url' => $this->baseUrl,
            'text_to_video' => true,
            'image_to_video' => true,
            'avatar' => false,
            'voice' => true,
            'captions' => true,
            'duration' => $this->duration,
            'aspect_ratios' => array('9:16', '16:9', '1:1'),
            'languages' => array('vi', 'en'),
            'is_configured' => (!empty($this->apiKey) && $this->active)
        );
    }
}

/**
 * Class VideoProviderFactory
 */
class VideoProviderFactory {
    public static function create($providerName = 'mock', $d = null, $func = null) {
        $providerName = strtolower(trim($providerName));

        global $config;

        // Nạp API key từ config.php hoặc table_setting nếu có
        $settingOptions = array();
        if ($d) {
            $settingRow = $d->rawQueryOne("SELECT options FROM table_setting LIMIT 1");
            if (!empty($settingRow['options'])) {
                $settingOptions = json_decode($settingRow['options'], true);
            }
        }
        $aiVideoConfig = !empty($settingOptions['ai_video_config']) ? $settingOptions['ai_video_config'] : array();

        switch ($providerName) {
            case 'beeknoee':
                $beeknoeeConfig = !empty($config['beeknoee']) ? $config['beeknoee'] : array();
                // Merge với cấu hình trong setting nếu có override
                if (!empty($aiVideoConfig['beeknoee_api_key'])) {
                    $beeknoeeConfig['api_key'] = $aiVideoConfig['beeknoee_api_key'];
                    $beeknoeeConfig['active'] = true;
                }
                return new BeeknoeeVideoProvider($beeknoeeConfig, $d, $func);

            case 'creatify':
            case 'arcads':
            case 'heygen':
            case 'runway':
                $apiKey = !empty($aiVideoConfig[$providerName . '_api_key']) ? $aiVideoConfig[$providerName . '_api_key'] : '';
                $endpoint = !empty($aiVideoConfig[$providerName . '_endpoint']) ? $aiVideoConfig[$providerName . '_endpoint'] : 'https://api.' . $providerName . '.com/v1';
                return new ExternalVideoProvider($apiKey, $endpoint, $providerName);

            case 'manual':
                return new ManualVideoProvider();

            case 'mock':
            default:
                return new MockVideoProvider($d, $func);
        }
    }
}

