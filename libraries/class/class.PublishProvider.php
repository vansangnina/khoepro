<?php
/**
 * FITNADO Publish Provider Layer
 * Phase 07: Publishing Center & TikTok Publishing Foundation
 * Multi-platform provider abstraction (Manual fallback & TikTok API foundation)
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

interface PublishProviderInterface {
    /**
     * Validate post package readiness before publish
     * @param array $postPackage
     * @return array ['valid' => bool, 'errors' => array()]
     */
    public function validate(array $postPackage);

    /**
     * Publish post package (or prepare manual package)
     * @param array $postPackage
     * @return array ['success' => bool, 'status' => string, 'external_id' => string|null, 'external_url' => string|null, 'error' => string|null, 'response' => array|null]
     */
    public function publish(array $postPackage);

    /**
     * Query remote post status
     * @param string $externalPostId
     * @return array ['status' => string, 'details' => array()]
     */
    public function getStatus($externalPostId);

    /**
     * Get remote post details
     * @param string $externalPostId
     * @return array ['success' => bool, 'post' => array()|null]
     */
    public function getPost($externalPostId);

    /**
     * Check if provider is configured with valid credentials
     * @return bool
     */
    public function isConfigured();

    /**
     * Get provider identifier name
     * @return string
     */
    public function getProviderName();
}

/**
 * Manual Publishing Provider (100% Production Fallback)
 * Sẵn sàng cho môi trường thực tế: Cung cấp link tải video, sao chép caption/hashtags, xác thực URL bài đăng và cập nhật trạng thái
 */
class ManualPublishProvider implements PublishProviderInterface {
    private $d;
    private $func;

    public function __construct($d = null, $func = null) {
        $this->d = $d;
        $this->func = $func;
    }

    public function isConfigured() {
        return true; // Manual Provider luôn sẵn sàng
    }

    public function getProviderName() {
        return 'manual';
    }

    /**
     * Validate manual post package
     */
    public function validate(array $postPackage) {
        $errors = array();

        if (empty($postPackage['id_video'])) {
            $errors[] = 'Chưa chọn video được phê duyệt (Approved Video).';
        }

        if (empty($postPackage['video_file']) || !file_exists($postPackage['video_file'])) {
            $errors[] = 'Tệp video thành phẩm không tồn tại trên hệ thống (' . ($postPackage['video_file'] ?? 'none') . ').';
        } elseif (filesize($postPackage['video_file']) < 1000) {
            $errors[] = 'Tệp video rỗng hoặc không hợp lệ (Dung lượng < 1KB).';
        }

        if (empty(trim($postPackage['caption'] ?? ''))) {
            $errors[] = 'Nội dung Caption không được để trống.';
        }

        return array(
            'valid' => empty($errors),
            'errors' => $errors
        );
    }

    /**
     * Prepare manual publish package
     */
    public function publish(array $postPackage) {
        $val = $this->validate($postPackage);
        if (!$val['valid']) {
            return array(
                'success' => false,
                'status' => 'FAILED',
                'external_id' => null,
                'external_url' => null,
                'error' => implode('; ', $val['errors']),
                'response' => null
            );
        }

        // Tạo dữ liệu gói xuất bản thủ công
        $fullCaption = trim($postPackage['caption'] ?? '');
        if (!empty($postPackage['hashtags'])) {
            $fullCaption .= "\n\n" . trim($postPackage['hashtags']);
        }
        if (!empty($postPackage['disclosure_text'])) {
            $fullCaption .= "\n\n" . trim($postPackage['disclosure_text']);
        }

        $packageData = array(
            'mode' => 'MANUAL',
            'video_download_url' => $postPackage['video_file'] ?? '',
            'full_caption' => $fullCaption,
            'caption_only' => $postPackage['caption'] ?? '',
            'hashtags_only' => $postPackage['hashtags'] ?? '',
            'landing_url' => $postPackage['landing_url'] ?? '',
            'tiktok_creator_url' => 'https://www.tiktok.com/creator-center/upload?from=webapp',
            'ready_at' => time()
        );

        return array(
            'success' => true,
            'status' => 'READY',
            'external_id' => null,
            'external_url' => null,
            'error' => null,
            'response' => $packageData
        );
    }

    public function getStatus($externalPostId) {
        return array(
            'status' => 'MANUAL_TRACKED',
            'details' => array('external_post_id' => $externalPostId)
        );
    }

    public function getPost($externalPostId) {
        return array(
            'success' => true,
            'post' => array('external_post_id' => $externalPostId)
        );
    }

    /**
     * Xác thực URL bài đăng TikTok / Mạng xã hội thủ công
     * Chặn các URL nguy hiểm (javascript:, file:, data:) và kiểm tra đúng domain
     * @param string $url
     * @param string $platform
     * @return array ['valid' => bool, 'error' => string|null, 'normalized_url' => string, 'extracted_id' => string|null]
     */
    public function validateExternalUrl($url, $platform = 'tiktok') {
        $url = trim($url);
        if (empty($url)) {
            return array('valid' => false, 'error' => 'URL bài đăng không được để trống', 'normalized_url' => '', 'extracted_id' => null);
        }

        // 1. Chặn schema độc hại
        if (preg_match('/^(javascript|file|data|vbscript|about):/i', $url)) {
            return array('valid' => false, 'error' => 'Giao thức URL không an toàn và bị chặn', 'normalized_url' => '', 'extracted_id' => null);
        }

        // 2. Validate URL RFC
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return array('valid' => false, 'error' => 'Định dạng URL không hợp lệ (Bắt buộc bắt đầu bằng http:// hoặc https://)', 'normalized_url' => '', 'extracted_id' => null);
        }

        $parsed = parse_url($url);
        $scheme = strtolower($parsed['scheme'] ?? '');
        if (!in_array($scheme, array('http', 'https'))) {
            return array('valid' => false, 'error' => 'URL chỉ hỗ trợ giao thức http hoặc https', 'normalized_url' => '', 'extracted_id' => null);
        }

        $host = strtolower($parsed['host'] ?? '');
        $extractedId = null;

        // 3. Kiểm tra domain theo từng platform
        if ($platform === 'tiktok') {
            $allowedDomains = array(
                'tiktok.com', 'www.tiktok.com', 'vt.tiktok.com', 'm.tiktok.com', 'v.douyin.com'
            );
            $isValidDomain = false;
            foreach ($allowedDomains as $domain) {
                if ($host === $domain || substr($host, -strlen('.' . $domain)) === '.' . $domain) {
                    $isValidDomain = true;
                    break;
                }
            }

            if (!$isValidDomain) {
                return array('valid' => false, 'error' => 'URL không thuộc tên miền TikTok hợp lệ (tiktok.com, vt.tiktok.com)', 'normalized_url' => '', 'extracted_id' => null);
            }

            // Trích xuất TikTok Video ID nếu có trong path: /video/7345678901234567890 hoặc /v/123456
            if (preg_match('/\/video\/(\d+)/i', $url, $m)) {
                $extractedId = $m[1];
            } elseif (preg_match('/vt\.tiktok\.com\/([a-zA-Z0-9_-]+)/i', $url, $m)) {
                $extractedId = $m[1];
            }
        }

        return array(
            'valid' => true,
            'error' => null,
            'normalized_url' => $url,
            'extracted_id' => $extractedId
        );
    }
}

/**
 * TikTok API Publishing Provider Foundation
 * Tích hợp TikTok Content Posting API chính thức khi có thông tin xác thực
 * Báo cáo trung thực: NOT CONFIGURED nếu chưa có API credentials
 */
class TikTokPublishProvider implements PublishProviderInterface {
    private $d;
    private $func;
    private $config;

    public function __construct($d = null, $func = null, array $apiConfig = array()) {
        $this->d = $d;
        $this->func = $func;

        if (empty($apiConfig)) {
            global $config;
            $apiConfig = !empty($config['tiktok_api']) ? $config['tiktok_api'] : array();
        }

        $this->config = array_merge(array(
            'client_key' => '',
            'client_secret' => '',
            'redirect_uri' => '',
            'access_token' => '',
            'refresh_token' => '',
            'token_expires_at' => 0,
            'base_url' => 'https://open.tiktokapis.com'
        ), $apiConfig);
    }

    public function isConfigured() {
        return (!empty($this->config['client_key']) && !empty($this->config['client_secret']) && !empty($this->config['access_token']));
    }

    public function getProviderName() {
        return 'tiktok_api';
    }

    public function getStatusInfo() {
        if (!$this->isConfigured()) {
            return array(
                'status' => 'NOT_CONFIGURED',
                'message' => 'TikTok Developer App & Content Posting API chưa được cấu hình credentials. Hệ thống sử dụng Manual Provider làm giải pháp xuất bản chính thức.',
                'configured' => false
            );
        }

        return array(
            'status' => 'CONFIGURED',
            'message' => 'TikTok API credentials đã được cấu hình.',
            'configured' => true
        );
    }

    public function validate(array $postPackage) {
        $errors = array();

        if (!$this->isConfigured()) {
            $errors[] = 'TikTok API chưa được cấu hình (Thiếu Client Key, Client Secret hoặc Access Token). Vui lòng sử dụng Manual Provider.';
        }

        if (empty($postPackage['video_file']) || !file_exists($postPackage['video_file'])) {
            $errors[] = 'Tệp video không tồn tại trên hệ thống.';
        }

        if (empty(trim($postPackage['caption'] ?? ''))) {
            $errors[] = 'Nội dung Caption không được để trống.';
        }

        return array(
            'valid' => empty($errors),
            'errors' => $errors
        );
    }

    public function publish(array $postPackage) {
        $val = $this->validate($postPackage);
        if (!$val['valid']) {
            return array(
                'success' => false,
                'status' => 'FAILED',
                'external_id' => null,
                'external_url' => null,
                'error' => implode('; ', $val['errors']),
                'response' => array('api_status' => 'NOT_CONFIGURED')
            );
        }

        // Nếu API credentials thật có trong tương lai, thực thi upload qua TikTok Content Posting API
        // Không giả lập success:
        return array(
            'success' => false,
            'status' => 'FAILED',
            'external_id' => null,
            'external_url' => null,
            'error' => 'TikTok API posting requires valid OAuth token and human authorization confirmation.',
            'response' => null
        );
    }

    public function getStatus($externalPostId) {
        if (!$this->isConfigured()) {
            return array('status' => 'NOT_CONFIGURED', 'details' => null);
        }
        return array('status' => 'UNKNOWN', 'details' => null);
    }

    public function getPost($externalPostId) {
        return array('success' => false, 'post' => null);
    }
}

/**
 * Factory khởi tạo Publish Provider
 */
class PublishProviderFactory {
    public static function create($providerName = 'manual', $d = null, $func = null) {
        $providerName = strtolower(trim($providerName));
        switch ($providerName) {
            case 'tiktok':
            case 'tiktok_api':
                return new TikTokPublishProvider($d, $func);
            case 'manual':
            default:
                return new ManualPublishProvider($d, $func);
        }
    }
}
