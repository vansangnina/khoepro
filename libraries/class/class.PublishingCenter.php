<?php
/**
 * FITNADO Publishing Center Engine
 * Phase 07: Publishing Center & TikTok Publishing Foundation
 * Flow: APPROVED VIDEO -> POST PACKAGE -> GATES -> SNAPSHOT -> SCHEDULE/MANUAL -> PUBLISHED -> AUDIT LOG
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once LIBRARIES . 'class/class.PublishProvider.php';

class PublishingCenter {
    private $d;
    private $func;
    private $config;

    // Supported Platforms
    const PLATFORMS = array(
        'tiktok' => array('name' => 'TikTok', 'icon' => 'fab fa-tiktok', 'color' => '#000000', 'badge_class' => 'badge-dark'),
        'facebook' => array('name' => 'Facebook Reels / Page', 'icon' => 'fab fa-facebook', 'color' => '#1877F2', 'badge_class' => 'badge-primary'),
        'instagram' => array('name' => 'Instagram Reels', 'icon' => 'fab fa-instagram', 'color' => '#E4405F', 'badge_class' => 'badge-danger'),
        'youtube_shorts' => array('name' => 'YouTube Shorts', 'icon' => 'fab fa-youtube', 'color' => '#FF0000', 'badge_class' => 'badge-danger'),
        'website' => array('name' => 'FITNADO Web Video', 'icon' => 'fas fa-globe', 'color' => '#0256AA', 'badge_class' => 'badge-info')
    );

    // Lifecycle Statuses
    const STATUSES = array(
        'DRAFT' => array('name' => 'Bản nháp (Draft)', 'badge' => 'badge-secondary'),
        'READY' => array('name' => 'Sẵn sàng xuất bản (Ready)', 'badge' => 'badge-info'),
        'SCHEDULED' => array('name' => 'Đã lên lịch (Scheduled)', 'badge' => 'badge-primary'),
        'QUEUED' => array('name' => 'Đang trong hàng đợi (Queued)', 'badge' => 'badge-warning'),
        'PUBLISHING' => array('name' => 'Đang xuất bản (Publishing)', 'badge' => 'badge-warning'),
        'PUBLISHED' => array('name' => 'Đã xuất bản (Published)', 'badge' => 'badge-success'),
        'FAILED' => array('name' => 'Thất bại (Failed)', 'badge' => 'badge-danger'),
        'CANCELLED' => array('name' => 'Đã hủy (Cancelled)', 'badge' => 'badge-light')
    );

    public function __construct($d = null, $func = null) {
        $this->d = $d;
        $this->func = $func;
        
        global $config;
        $this->config = !empty($config['publishing']) ? $config['publishing'] : array();
    }

    /**
     * Khởi tạo Post Package từ Approved Video
     * Strict Human Gate: Chỉ video có status = 'APPROVED' mới được tạo post
     * Outdated Gate: Cảnh báo nếu video hoặc dữ liệu liên quan bị thay đổi
     * @param int $idVideo
     * @param array $options [platform, account_id, provider, custom_caption, custom_hashtags]
     * @param string $adminUser
     * @return array ['success' => bool, 'id_post' => int|null, 'error' => string|null, 'post' => array()|null]
     */
    public function createPostFromApprovedVideo($idVideo, array $options = array(), $adminUser = 'admin') {
        if (!$this->d) {
            return array('success' => false, 'error' => 'Chưa kết nối cơ sở dữ liệu.');
        }

        // 1. Human Gate: Kiểm tra trạng thái Video
        $video = $this->d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array((int)$idVideo));
        if (empty($video)) {
            return array('success' => false, 'error' => "Không tìm thấy dự án video #{$idVideo} trong hệ thống.");
        }

        if ($video['status'] !== 'APPROVED') {
            return array(
                'success' => false,
                'error' => "RÀO CHẮN BẢO VỆ: Chỉ video có trạng thái ĐÃ DUYỆT (APPROVED) mới được phép tạo Post Package. Trạng thái hiện tại: [{$video['status']}]."
            );
        }

        $productId = (int)$video['id_product'];
        $product = $this->d->rawQueryOne("SELECT * FROM table_product WHERE id = ? LIMIT 1", array($productId));
        if (empty($product)) {
            return array('success' => false, 'error' => "Không tìm thấy sản phẩm liên kết #{$productId}.");
        }

        // 2. Outdated Gate: Kiểm tra cờ lỗi thời
        $isOutdated = !empty($video['is_outdated']) ? 1 : 0;

        // 3. Trích xuất nội dung từ AI Content đã duyệt
        $content = array();
        if (!empty($video['id_content'])) {
            $content = $this->d->rawQueryOne("SELECT * FROM table_ai_content WHERE id = ? LIMIT 1", array((int)$video['id_content']));
        }

        // 4. Chuẩn bị Caption và Hashtags
        $caption = '';
        if (!empty($options['caption'])) {
            $caption = trim($options['caption']);
        } elseif (!empty($content['title'])) {
            $caption = $content['title'];
        } else {
            $caption = "Review chi tiết {$product['namevi']} - Chuẩn form, an toàn, hiệu quả.";
        }

        $hashtags = '';
        if (!empty($options['hashtags'])) {
            $hashtags = trim($options['hashtags']);
        } else {
            $tagList = array('#fitnado', '#reviewgym', '#tapgym');
            $cleanProdName = preg_replace('/[^a-zA-Z0-9]/', '', $product['namevi'] ?? '');
            if (!empty($cleanProdName)) {
                $tagList[] = '#' . mb_strtolower(mb_substr($cleanProdName, 0, 20));
            }
            $hashtags = implode(' ', array_slice($tagList, 0, 5));
        }

        // 5. Platform & Account
        $platform = !empty($options['platform']) ? strtolower(trim($options['platform'])) : 'tiktok';
        if (!isset(self::PLATFORMS[$platform])) {
            $platform = 'tiktok';
        }

        // 6. Sinh Unique Tracking Code & Tạo Landing URL có gắn UTM
        require_once LIBRARIES . 'class/class.AnalyticsService.php';
        $trackingCode = AnalyticsService::generateTrackingCode($platform);

        global $configUrl;
        $baseUrl = !empty($configUrl) ? ('http://' . $configUrl) : 'https://fitnado.vn';
        $productSlug = !empty($product['slugvi']) ? $product['slugvi'] : ('san-pham/' . $product['id']);
        $campaignSlug = !empty($product['slugvi']) ? $product['slugvi'] : ('prod_' . $productId);
        $landingUrl = $baseUrl . '/' . $productSlug . '?ref=' . urlencode($trackingCode) . '&utm_source=' . urlencode($platform) . '&utm_medium=organic_video&utm_campaign=' . urlencode($campaignSlug) . '&utm_content=' . urlencode($trackingCode);

        $bestAffiliate = $this->d->rawQueryOne(
            "SELECT id FROM table_product_affiliate WHERE id_product = ? AND find_in_set('hienthi', status) ORDER BY is_best_deal DESC, priority ASC LIMIT 1",
            array($productId)
        );
        $affiliateOfferId = !empty($bestAffiliate['id']) ? (int)$bestAffiliate['id'] : null;

        $accountId = !empty($options['account_id']) ? (int)$options['account_id'] : null;
        if (empty($accountId)) {
            $defaultAcc = $this->d->rawQueryOne("SELECT id FROM table_publish_account WHERE platform = ? AND is_default = 1 LIMIT 1", array($platform));
            $accountId = !empty($defaultAcc['id']) ? (int)$defaultAcc['id'] : null;
        }

        $provider = !empty($options['provider']) ? strtolower(trim($options['provider'])) : 'manual';
        $disclosureText = !empty($options['disclosure_text']) ? trim($options['disclosure_text']) : 'FITNADO Affiliate Partner - Tham khảo kỹ thông số trước khi mua';

        // 7. Tạo bản ghi Post Package (DRAFT)
        $postData = array(
            'id_product' => $productId,
            'id_video' => (int)$idVideo,
            'id_ai_content' => !empty($video['id_content']) ? (int)$video['id_content'] : null,
            'platform' => $platform,
            'post_type' => 'VIDEO_POST',
            'tracking_code' => $trackingCode,
            'title' => !empty($options['title']) ? trim($options['title']) : ("Post: " . ($video['title'] ?: $product['namevi'])),
            'caption' => $caption,
            'hashtags' => $hashtags,
            'affiliate_offer_id' => $affiliateOfferId,
            'landing_url' => $landingUrl,
            'disclosure_text' => $disclosureText,
            'provider' => $provider,
            'account_id' => $accountId,
            'status' => 'DRAFT',
            'scheduled_at' => !empty($options['scheduled_at']) ? (int)$options['scheduled_at'] : null,
            'published_at' => null,
            'external_post_id' => null,
            'external_post_url' => null,
            'provider_response' => null,
            'error_message' => null,
            'attempts' => 0,
            'is_outdated' => $isOutdated,
            'snapshot_data' => null,
            'publish_lock' => null,
            'date_created' => time(),
            'date_updated' => time()
        );

        $idPost = $this->d->insert('publish_post', $postData);
        if (!$idPost) {
            return array('success' => false, 'error' => 'Không thể ghi bản ghi Post Package vào CSDL.');
        }

        // 8. Log Lịch sử
        $this->logEvent($idPost, 'CREATED', null, 'DRAFT', $adminUser, array(
            'id_video' => $idVideo,
            'id_product' => $productId,
            'platform' => $platform,
            'provider' => $provider
        ));

        $createdPost = $this->getPost($idPost);

        return array(
            'success' => true,
            'id_post' => $idPost,
            'post' => $createdPost,
            'is_outdated' => $isOutdated,
            'error' => null
        );
    }

    /**
     * Lấy chi tiết Post Package kèm thông tin liên kết
     * @param int $idPost
     * @return array|null
     */
    public function getPost($idPost) {
        if (!$this->d) return null;

        $post = $this->d->rawQueryOne("SELECT * FROM table_publish_post WHERE id = ? LIMIT 1", array((int)$idPost));
        if (empty($post)) return null;

        $post['product'] = $this->d->rawQueryOne("SELECT id, namevi, slugvi, photo, regular_price, sale_price, specs FROM table_product WHERE id = ? LIMIT 1", array((int)$post['id_product']));
        $post['video'] = $this->d->rawQueryOne("SELECT id, title, video_file, thumbnail, duration_actual, file_size, mode, status FROM table_ai_video WHERE id = ? LIMIT 1", array((int)$post['id_video']));
        $post['account'] = !empty($post['account_id']) ? $this->d->rawQueryOne("SELECT id, account_name, account_handle, platform, provider, auth_status FROM table_publish_account WHERE id = ? LIMIT 1", array((int)$post['account_id'])) : null;
        $post['affiliate'] = !empty($post['affiliate_offer_id']) ? $this->d->rawQueryOne("SELECT id, platform, seller_name, price, affiliate_url, coupon_code FROM table_product_affiliate WHERE id = ? LIMIT 1", array((int)$post['affiliate_offer_id'])) : null;
        $post['logs'] = $this->d->rawQuery("SELECT * FROM table_publish_log WHERE id_post = ? ORDER BY id DESC", array((int)$idPost));

        return $post;
    }

    /**
     * Chạy Pre-publish Checklist kiểm định toàn diện trước khi cho phép READY
     * @param int $idPost
     * @return array ['valid' => bool, 'errors' => array(), 'post' => array()]
     */
    public function validatePrePublishChecklist($idPost) {
        $post = $this->getPost($idPost);
        if (empty($post)) {
            return array('valid' => false, 'errors' => array('Không tìm thấy Post Package.'), 'post' => null);
        }

        $errors = array();

        // 1. Kiểm tra Video
        if (empty($post['video'])) {
            $errors[] = 'Dự án video liên kết không tồn tại trong hệ thống.';
        } elseif ($post['video']['status'] !== 'APPROVED') {
            $errors[] = "Video chưa được phê duyệt (Trạng thái hiện tại: {$post['video']['status']}).";
        } elseif (empty($post['video']['video_file']) || !file_exists($post['video']['video_file'])) {
            $errors[] = "Tệp video thành phẩm không tồn tại trên máy chủ ({$post['video']['video_file']}).";
        } elseif (filesize($post['video']['video_file']) < 1000) {
            $errors[] = "Tệp video rỗng hoặc bị lỗi dung lượng.";
        }

        // 2. Kiểm tra Sản phẩm
        if (empty($post['product'])) {
            $errors[] = 'Sản phẩm liên kết không tồn tại trong hệ thống.';
        }

        // 3. Kiểm tra Caption & Nội dung
        if (empty(trim($post['caption'] ?? ''))) {
            $errors[] = 'Nội dung Caption không được để trống.';
        }

        // 4. Kiểm tra Platform & Account
        if (empty($post['platform']) || !isset(self::PLATFORMS[$post['platform']])) {
            $errors[] = 'Chưa chọn nền tảng xuất bản hợp lệ.';
        }

        // 5. Kiểm tra Provider Readiness
        $provider = PublishProviderFactory::create($post['provider'] ?? 'manual', $this->d, $this->func);
        $providerVal = $provider->validate(array(
            'id_video' => $post['id_video'],
            'video_file' => $post['video']['video_file'] ?? '',
            'caption' => $post['caption'],
            'hashtags' => $post['hashtags']
        ));

        if (!$providerVal['valid']) {
            $errors = array_merge($errors, $providerVal['errors']);
        }

        return array(
            'valid' => empty($errors),
            'errors' => $errors,
            'post' => $post
        );
    }

    /**
     * Chuyển Post Package sang READY (hoặc SCHEDULED) và Đóng băng Snapshot bất biến
     * @param int $idPost
     * @param string $adminUser
     * @return array ['success' => bool, 'status' => string, 'errors' => array()]
     */
    public function markPostReady($idPost, $adminUser = 'admin') {
        $checklist = $this->validatePrePublishChecklist($idPost);
        if (!$checklist['valid']) {
            return array('success' => false, 'status' => 'FAILED_CHECKLIST', 'errors' => $checklist['errors']);
        }

        $post = $checklist['post'];
        $oldStatus = $post['status'];

        // Không cho phép sửa nếu đã PUBLISHED
        if ($oldStatus === 'PUBLISHED') {
            return array('success' => false, 'status' => 'PUBLISHED', 'errors' => array('Bài đăng đã xuất bản thành công. Không thể đánh dấu READY lại.'));
        }

        // Tạo Snapshot Bất biến (Immutable Content Snapshot)
        $snapshot = array(
            'snapshotted_at' => time(),
            'product_id' => $post['id_product'],
            'product_name' => $post['product']['namevi'] ?? '',
            'video_id' => $post['id_video'],
            'video_file' => $post['video']['video_file'] ?? '',
            'video_duration' => $post['video']['duration_actual'] ?? 0,
            'caption' => $post['caption'],
            'hashtags' => $post['hashtags'],
            'disclosure_text' => $post['disclosure_text'],
            'landing_url' => $post['landing_url'],
            'affiliate_offer_id' => $post['affiliate_offer_id'],
            'affiliate_platform' => $post['affiliate']['platform'] ?? '',
            'affiliate_url' => $post['affiliate']['affiliate_url'] ?? '',
            'platform' => $post['platform'],
            'account_handle' => $post['account']['account_handle'] ?? '',
            'provider' => $post['provider']
        );

        $newStatus = (!empty($post['scheduled_at']) && $post['scheduled_at'] > time()) ? 'SCHEDULED' : 'READY';

        $this->d->rawQuery(
            "UPDATE table_publish_post SET status = ?, snapshot_data = ?, error_message = NULL, date_updated = ? WHERE id = ?",
            array($newStatus, json_encode($snapshot, JSON_UNESCAPED_UNICODE), time(), (int)$idPost)
        );

        $this->logEvent($idPost, ($newStatus === 'SCHEDULED' ? 'SCHEDULED' : 'READY'), $oldStatus, $newStatus, $adminUser, array(
            'snapshot_created' => true,
            'scheduled_at' => $post['scheduled_at']
        ));

        return array(
            'success' => true,
            'status' => $newStatus,
            'errors' => array()
        );
    }

    /**
     * Cập nhật thông tin Post Package
     * Quy tắc: Nếu sửa bài đang ở trạng thái READY hoặc SCHEDULED, hệ thống tự động hoàn nguyên về DRAFT để duyệt lại
     * Quy tắc: Không cho phép sửa bài đã PUBLISHED (bắt buộc Duplicate)
     * @param int $idPost
     * @param array $data
     * @param string $adminUser
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function updatePost($idPost, array $data, $adminUser = 'admin') {
        $post = $this->getPost($idPost);
        if (empty($post)) {
            return array('success' => false, 'error' => 'Không tìm thấy Post Package.');
        }

        if ($post['status'] === 'PUBLISHED') {
            return array('success' => false, 'error' => 'Không thể sửa bài đăng đã xuất bản. Vui lòng sử dụng tính năng Nhân bản (Duplicate) để tạo bài đăng mới.');
        }

        $oldStatus = $post['status'];
        $needInvalidateReady = in_array($oldStatus, array('READY', 'SCHEDULED'));

        $updateFields = array();
        $params = array();

        if (isset($data['title'])) {
            $updateFields[] = 'title = ?';
            $params[] = trim($data['title']);
        }
        if (isset($data['caption'])) {
            $updateFields[] = 'caption = ?';
            $params[] = trim($data['caption']);
        }
        if (isset($data['hashtags'])) {
            $updateFields[] = 'hashtags = ?';
            $params[] = trim($data['hashtags']);
        }
        if (isset($data['platform']) && isset(self::PLATFORMS[$data['platform']])) {
            $updateFields[] = 'platform = ?';
            $params[] = strtolower(trim($data['platform']));
        }
        if (isset($data['account_id'])) {
            $updateFields[] = 'account_id = ?';
            $params[] = (int)$data['account_id'] ?: null;
        }
        if (isset($data['provider'])) {
            $updateFields[] = 'provider = ?';
            $params[] = strtolower(trim($data['provider']));
        }
        if (isset($data['affiliate_offer_id'])) {
            $updateFields[] = 'affiliate_offer_id = ?';
            $params[] = (int)$data['affiliate_offer_id'] ?: null;
        }
        if (isset($data['disclosure_text'])) {
            $updateFields[] = 'disclosure_text = ?';
            $params[] = trim($data['disclosure_text']);
        }
        if (isset($data['scheduled_at'])) {
            $updateFields[] = 'scheduled_at = ?';
            $params[] = !empty($data['scheduled_at']) ? (int)$data['scheduled_at'] : null;
        }

        $newStatus = $oldStatus;
        if ($needInvalidateReady) {
            $newStatus = 'DRAFT';
            $updateFields[] = 'status = ?';
            $params[] = 'DRAFT';
            $updateFields[] = 'snapshot_data = NULL'; // Invalidate snapshot
        }

        if (empty($updateFields)) {
            return array('success' => true, 'message' => 'Không có thay đổi.');
        }

        $updateFields[] = 'date_updated = ?';
        $params[] = time();

        $params[] = (int)$idPost;
        $sql = "UPDATE table_publish_post SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $this->d->rawQuery($sql, $params);

        $this->logEvent($idPost, 'EDIT', $oldStatus, $newStatus, $adminUser, array(
            'invalidated_to_draft' => $needInvalidateReady,
            'updated_fields' => array_keys($data)
        ));

        return array(
            'success' => true,
            'invalidated' => $needInvalidateReady,
            'error' => null
        );
    }

    /**
     * Thực thi Xuất bản ngay (Publish Now)
     * Đối với Manual: Chuẩn bị gói tải video & thông tin sao chép
     * Đối với API: Kích hoạt Provider Publish
     * Có rào chắn Idempotency & Double Publish Protection
     * @param int $idPost
     * @param string $adminUser
     * @return array
     */
    public function publishNow($idPost, $adminUser = 'admin') {
        $post = $this->getPost($idPost);
        if (empty($post)) {
            return array('success' => false, 'error' => 'Không tìm thấy Post Package.');
        }

        if ($post['status'] === 'PUBLISHED') {
            return array('success' => false, 'error' => 'Bài đăng này đã được xuất bản trước đó (Trạng thái: PUBLISHED).');
        }

        // 1. Kiểm tra khóa Idempotency (Chống double click / chạy song song)
        if (!empty($post['publish_lock']) && $post['status'] === 'PUBLISHING') {
            return array('success' => false, 'error' => 'Bài đăng đang trong tiến trình xuất bản (Locked). Vui lòng không thao tác lặp lại.');
        }

        // 2. Kiểm tra checklist
        $checklist = $this->validatePrePublishChecklist($idPost);
        if (!$checklist['valid']) {
            return array('success' => false, 'error' => 'Checklist không đạt: ' . implode('; ', $checklist['errors']));
        }

        // 3. Thiết lập Khóa Concurrency
        $lockKey = md5($idPost . '_' . microtime(true));
        $this->d->rawQuery(
            "UPDATE table_publish_post SET publish_lock = ?, status = 'PUBLISHING', date_updated = ? WHERE id = ? AND (publish_lock IS NULL OR status != 'PUBLISHING')",
            array($lockKey, time(), (int)$idPost)
        );

        $provider = PublishProviderFactory::create($post['provider'] ?? 'manual', $this->d, $this->func);
        $result = $provider->publish(array(
            'id_video' => $post['id_video'],
            'video_file' => $post['video']['video_file'] ?? '',
            'caption' => $post['caption'],
            'hashtags' => $post['hashtags'],
            'disclosure_text' => $post['disclosure_text'],
            'landing_url' => $post['landing_url']
        ));

        // 4. Giải phóng khóa và cập nhật trạng thái
        if ($post['provider'] === 'manual') {
            // Manual provider không đánh dấu PUBLISHED ngay mà trả về manual package
            $this->d->rawQuery(
                "UPDATE table_publish_post SET publish_lock = NULL, status = 'READY', provider_response = ?, date_updated = ? WHERE id = ?",
                array(json_encode($result['response'], JSON_UNESCAPED_UNICODE), time(), (int)$idPost)
            );

            $this->logEvent($idPost, 'MANUAL_PACKAGE_GENERATED', 'PUBLISHING', 'READY', $adminUser, array(
                'provider' => 'manual'
            ));

            return array(
                'success' => true,
                'mode' => 'MANUAL',
                'manual_package' => $result['response'],
                'post' => $this->getPost($idPost)
            );
        } else {
            // API Provider
            $newStatus = $result['success'] ? 'PUBLISHED' : 'FAILED';
            $this->d->rawQuery(
                "UPDATE table_publish_post SET publish_lock = NULL, status = ?, external_post_id = ?, external_post_url = ?, error_message = ?, provider_response = ?, date_updated = ? WHERE id = ?",
                array($newStatus, $result['external_id'], $result['external_url'], $result['error'], json_encode($result['response'], JSON_UNESCAPED_UNICODE), time(), (int)$idPost)
            );

            $this->logEvent($idPost, ($result['success'] ? 'PUBLISHED' : 'FAILED'), 'PUBLISHING', $newStatus, $adminUser, array(
                'error' => $result['error']
            ));

            return $result;
        }
    }

    /**
     * Xác nhận Đã Xuất Bản Thủ Công (Mark as Published)
     * Admin nhập TikTok Post URL sau khi đăng thủ công
     * @param int $idPost
     * @param string $externalUrl
     * @param string|null $externalId
     * @param string $adminUser
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function markManualPublished($idPost, $externalUrl, $externalId = null, $adminUser = 'admin') {
        $post = $this->getPost($idPost);
        if (empty($post)) {
            return array('success' => false, 'error' => 'Không tìm thấy Post Package.');
        }

        if ($post['status'] === 'PUBLISHED') {
            return array('success' => false, 'error' => 'Bài đăng này đã ở trạng thái ĐÃ XUẤT BẢN.');
        }

        // 1. Xác thực URL
        $manualProvider = new ManualPublishProvider($this->d, $this->func);
        $urlCheck = $manualProvider->validateExternalUrl($externalUrl, $post['platform'] ?? 'tiktok');

        if (!$urlCheck['valid']) {
            return array('success' => false, 'error' => $urlCheck['error']);
        }

        $finalExtId = !empty($externalId) ? trim($externalId) : $urlCheck['extracted_id'];
        $oldStatus = $post['status'];

        // 2. Cập nhật trạng thái PUBLISHED
        $this->d->rawQuery(
            "UPDATE table_publish_post SET status = 'PUBLISHED', published_at = ?, external_post_url = ?, external_post_id = ?, error_message = NULL, date_updated = ? WHERE id = ?",
            array(time(), $urlCheck['normalized_url'], $finalExtId, time(), (int)$idPost)
        );

        // 3. Log Audit
        $this->logEvent($idPost, 'MANUAL_MARK', $oldStatus, 'PUBLISHED', $adminUser, array(
            'external_post_url' => $urlCheck['normalized_url'],
            'external_post_id' => $finalExtId
        ));

        return array(
            'success' => true,
            'external_post_url' => $urlCheck['normalized_url'],
            'external_post_id' => $finalExtId,
            'error' => null
        );
    }

    /**
     * Nhân bản Post Package (Duplicate) để đăng lại hoặc tạo biến thể
     * @param int $idPost
     * @param string $adminUser
     * @return array ['success' => bool, 'id_new_post' => int|null, 'error' => string|null]
     */
    public function duplicatePost($idPost, $adminUser = 'admin') {
        $post = $this->getPost($idPost);
        if (empty($post)) {
            return array('success' => false, 'error' => 'Không tìm thấy Post Package gốc.');
        }

        require_once LIBRARIES . 'class/class.AnalyticsService.php';
        $newTrackingCode = AnalyticsService::generateTrackingCode($post['platform']);

        // Update landing URL with new tracking code
        $landingUrl = $post['landing_url'];
        if (!empty($landingUrl)) {
            $landingUrl = preg_replace('/ref=[^&]+/', 'ref=' . urlencode($newTrackingCode), $landingUrl);
            $landingUrl = preg_replace('/utm_content=[^&]+/', 'utm_content=' . urlencode($newTrackingCode), $landingUrl);
        }

        $newData = array(
            'id_product' => $post['id_product'],
            'id_video' => $post['id_video'],
            'id_ai_content' => $post['id_ai_content'],
            'platform' => $post['platform'],
            'post_type' => $post['post_type'],
            'tracking_code' => $newTrackingCode,
            'title' => $post['title'] . ' (Bản sao)',
            'caption' => $post['caption'],
            'hashtags' => $post['hashtags'],
            'affiliate_offer_id' => $post['affiliate_offer_id'],
            'landing_url' => $landingUrl,
            'disclosure_text' => $post['disclosure_text'],
            'provider' => $post['provider'],
            'account_id' => $post['account_id'],
            'status' => 'DRAFT',
            'scheduled_at' => null,
            'published_at' => null,
            'external_post_id' => null,
            'external_post_url' => null,
            'provider_response' => null,
            'error_message' => null,
            'attempts' => 0,
            'is_outdated' => $post['is_outdated'],
            'snapshot_data' => null,
            'publish_lock' => null,
            'date_created' => time(),
            'date_updated' => time()
        );

        $newId = $this->d->insert('publish_post', $newData);
        if (!$newId) {
            return array('success' => false, 'error' => 'Không thể nhân bản bài đăng.');
        }

        $this->logEvent($newId, 'CREATED', null, 'DRAFT', $adminUser, array(
            'duplicated_from_id' => $idPost
        ));

        return array(
            'success' => true,
            'id_new_post' => $newId,
            'error' => null
        );
    }

    /**
     * Ghi nhật ký tiến trình xuất bản (Publish Log)
     * @param int $idPost
     * @param string $event
     * @param string|null $oldStatus
     * @param string $newStatus
     * @param string $actor
     * @param array $details
     * @return bool
     */
    public function logEvent($idPost, $event, $oldStatus, $newStatus, $actor = 'admin', array $details = array()) {
        if (!$this->d) return false;

        // Xóa sạch bí mật nếu có trong details
        if (isset($details['client_secret'])) unset($details['client_secret']);
        if (isset($details['access_token'])) unset($details['access_token']);
        if (isset($details['refresh_token'])) unset($details['refresh_token']);

        return $this->d->insert('publish_log', array(
            'id_post' => (int)$idPost,
            'event' => $event,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'actor' => $actor,
            'details' => !empty($details) ? json_encode($details, JSON_UNESCAPED_UNICODE) : null,
            'date_created' => time()
        ));
    }
}
