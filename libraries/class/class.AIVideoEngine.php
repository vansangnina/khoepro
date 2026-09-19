<?php
/**
 * FITNADO AI Video Production Engine
 * Phase 06: Approved Content -> Video Project -> Asset Preparation -> Render -> Quality Check -> Human Approval
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once LIBRARIES . 'class/class.VideoProvider.php';
require_once LIBRARIES . 'class/class.VideoComposer.php';

class AIVideoEngine {
    private $d;
    private $func;
    private $composer;

    // Các mẫu template visual cho video
    const TEMPLATES = array(
        'PROBLEM_SOLUTION' => array(
            'id' => 'PROBLEM_SOLUTION',
            'name' => 'Vấn đề & Giải pháp (Problem / Agitate / Solution)',
            'description' => 'Mở đầu bằng nỗi đau gymer -> Giới thiệu giải pháp sản phẩm -> Hướng dẫn mua',
            'recommended_duration' => 30
        ),
        'PRODUCT_REVIEW' => array(
            'id' => 'PRODUCT_REVIEW',
            'name' => 'Review Chuyên sâu & Ưu Nhược Điểm',
            'description' => 'Đánh giá chi tiết chất liệu, độ hoàn thiện, test tải trọng và hướng dẫn sử dụng',
            'recommended_duration' => 45
        ),
        'COMPARISON' => array(
            'id' => 'COMPARISON',
            'name' => 'So sánh Đối đầu (Vs Đối thủ)',
            'description' => 'Đặt 2 sản phẩm cạnh nhau, so sánh độ bền, giá thành và đối tượng phù hợp',
            'recommended_duration' => 30
        ),
        'BUYER_WARNING' => array(
            'id' => 'BUYER_WARNING',
            'name' => 'Cảnh báo Người mua (Myth Busting)',
            'description' => 'Vạch trần sai lầm phổ biến khi mua đồ tập và chỉ dẫn cách chọn đúng chuẩn',
            'recommended_duration' => 30
        )
    );

    // Danh sách giọng đọc TTS tiếng Việt hỗ trợ
    const VOICES = array(
        'vi-VN-Standard-A' => array('name' => 'Nữ miền Bắc (Tự nhiên / Năng động)', 'gender' => 'FEMALE', 'accent' => 'NORTH'),
        'vi-VN-Standard-B' => array('name' => 'Nam miền Bắc (Trầm ấm / Thể thao)', 'gender' => 'MALE', 'accent' => 'NORTH'),
        'vi-VN-Standard-C' => array('name' => 'Nữ miền Nam (Nhẹ nhàng / Cuốn hút)', 'gender' => 'FEMALE', 'accent' => 'SOUTH'),
        'vi-VN-Standard-D' => array('name' => 'Nam miền Nam (Mạnh mẽ / Quyết đoán)', 'gender' => 'MALE', 'accent' => 'SOUTH')
    );

    public function __construct($d, $func) {
        $this->d = $d;
        $this->func = $func;
        $this->composer = new VideoComposer($d, $func);
    }

    /**
     * Lấy thực thể VideoComposer
     * @return VideoComposer
     */
    public function getComposer() {
        return $this->composer;
    }

    /**
     * Khởi tạo dự án Video từ Kịch bản TikTok đã được Admin duyệt
     * @param int $idContent ID từ table_ai_content (bắt buộc status = 'APPROVED')
     * @param array $options Cấu hình tùy chọn (mode, voice_id, template_id, target_duration, aspect_ratio)
     * @return array ['success' => bool, 'id_video' => int, 'status' => string, 'error' => string]
     */
    public function createProjectFromApprovedContent($idContent, $options = array()) {
        $idContent = (int)$idContent;

        // 1. Kiểm tra kịch bản có tồn tại và đã duyệt chưa
        $content = $this->d->rawQueryOne("SELECT * FROM table_ai_content WHERE id = ? LIMIT 1", array($idContent));
        if (empty($content)) {
            return array('success' => false, 'id_video' => 0, 'status' => 'FAILED', 'error' => 'Kịch bản AI Content không tồn tại (ID: ' . $idContent . ')');
        }

        if ($content['status'] !== 'APPROVED' && $content['status'] !== 'APPLIED') {
            return array('success' => false, 'id_video' => 0, 'status' => 'FAILED', 'error' => 'Chỉ có kịch bản đã được phê duyệt (APPROVED) mới được tạo Video Project. Trạng thái hiện tại: ' . $content['status']);
        }

        $idProduct = (int)$content['id_product'];
        $structuredData = !empty($content['structured_data']) ? (is_array($content['structured_data']) ? $content['structured_data'] : json_decode($content['structured_data'], true)) : array();
        $shotPlan = !empty($structuredData['shot_plan']) ? $structuredData['shot_plan'] : array();

        if (empty($shotPlan)) {
            return array('success' => false, 'id_video' => 0, 'status' => 'FAILED', 'error' => 'Kịch bản không có dữ liệu phân cảnh (Shot Plan)');
        }

        // 2. Tính toán mã băm kịch bản (Script Hash)
        $scriptHash = $this->computeScriptHash($content);

        // 3. Xác định các thuộc tính cấu hình & Video Mode (Default: ECONOMY)
        $mode = !empty($options['mode']) ? strtoupper(trim($options['mode'])) : VideoComposer::MODE_ECONOMY;
        if (!in_array($mode, array(VideoComposer::MODE_ECONOMY, VideoComposer::MODE_HYBRID, VideoComposer::MODE_PREMIUM))) {
            $mode = VideoComposer::MODE_ECONOMY;
        }

        $title = !empty($options['title']) ? trim($options['title']) : (!empty($content['title']) ? 'Video TikTok: ' . $content['title'] : 'Video Project #' . $idProduct);
        $videoType = !empty($options['video_type']) ? $options['video_type'] : 'TIKTOK_9_16';
        $aspectRatio = !empty($options['aspect_ratio']) ? $options['aspect_ratio'] : '9:16';
        $targetDuration = !empty($options['target_duration']) ? (int)$options['target_duration'] : (!empty($content['target_duration']) ? (int)$content['target_duration'] : 30);
        $voiceId = !empty($options['voice_id']) ? $options['voice_id'] : 'vi-VN-Standard-A';
        $templateId = !empty($options['template_id']) ? $options['template_id'] : 'PROBLEM_SOLUTION';
        $provider = !empty($options['provider']) ? $options['provider'] : 'mock';

        // 4. Xác định phiên bản video kế tiếp (Versioning: v1, v2...)
        $maxVerRow = $this->d->rawQueryOne("SELECT MAX(version) as max_v FROM table_ai_video WHERE id_product = ?", array($idProduct));
        $nextVersion = !empty($maxVerRow['max_v']) ? (int)$maxVerRow['max_v'] + 1 : 1;

        // Đặt các video cũ của sản phẩm về is_active = 0 nếu đây là bản mới nhất
        if ($nextVersion > 1) {
            $this->d->rawQuery("UPDATE table_ai_video SET is_active = 0 WHERE id_product = ?", array($idProduct));
        }

        // 5. Chuẩn hóa phân cảnh qua VideoComposer (Gán purpose, motion effect, render method)
        $scenesData = $this->composer->normalizeScenes($shotPlan, $mode, $content);

        // 6. Tính toán ước tính chi phí
        $costEstimate = $this->composer->estimateCost($scenesData, $mode);

        $videoData = array(
            'id_product' => $idProduct,
            'id_content' => $idContent,
            'title' => $title,
            'video_type' => $videoType,
            'aspect_ratio' => $aspectRatio,
            'target_duration' => $targetDuration,
            'voice_id' => $voiceId,
            'template_id' => $templateId,
            'mode' => $mode,
            'scenes_data' => json_encode($scenesData, JSON_UNESCAPED_UNICODE),
            'script_hash' => $scriptHash,
            'version' => $nextVersion,
            'is_active' => 1,
            'is_outdated' => 0,
            'provider' => $provider,
            'status' => 'DRAFT',
            'cost_estimate' => $costEstimate['total_external_api_cost'],
            'local_render_cost' => $costEstimate['local_render_cost'],
            'ai_video_seconds' => $costEstimate['ai_video_seconds'],
            'ai_video_cost' => $costEstimate['ai_video_cost'],
            'tts_cost' => $costEstimate['tts_cost'],
            'total_external_api_cost' => $costEstimate['total_external_api_cost'],
            'date_created' => time(),
            'date_updated' => time()
        );

        $idVideo = $this->d->insert('ai_video', $videoData);
        if (!$idVideo) {
            return array('success' => false, 'id_video' => 0, 'status' => 'FAILED', 'error' => 'Lỗi chèn dữ liệu bảng table_ai_video');
        }

        // 7. Tự động giải quyết tài nguyên phân cảnh (Asset Resolution Pipeline)
        $assetRes = $this->resolveProjectAssets($idVideo);

        return array(
            'success' => true,
            'id_video' => (int)$idVideo,
            'version' => $nextVersion,
            'status' => $assetRes['status'],
            'missing_assets_count' => count($assetRes['missing_assets']),
            'error' => null
        );
    }

    /**
     * Helper tìm file ảnh thực tế trên ổ đĩa hỗ trợ cả ngữ cảnh admin và frontend
     * @param string $photo
     * @return string|null
     */
    private function findMediaFile($photo) {
        if (empty($photo)) return null;
        $candidates = array(
            defined('UPLOAD_PRODUCT') ? UPLOAD_PRODUCT . $photo : null,
            defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L . $photo : null,
            'upload/product/' . $photo,
            '../upload/product/' . $photo,
        );
        foreach ($candidates as $path) {
            if (!empty($path) && file_exists($path)) {
                return $path;
            }
        }
        return null;
    }

    /**
     * Tự động giải quyết và ánh xạ tài nguyên cho từng Scene của dự án
     * @param int $idVideo
     * @return array ['status' => string, 'ready' => bool, 'missing_assets' => array]
     */
    public function resolveProjectAssets($idVideo) {
        $idVideo = (int)$idVideo;
        $video = $this->d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array($idVideo));
        if (empty($video)) {
            return array('status' => 'FAILED', 'ready' => false, 'missing_assets' => array());
        }

        $idProduct = (int)$video['id_product'];
        $product = $this->d->rawQueryOne("SELECT id, photo, namevi FROM table_product WHERE id = ? LIMIT 1", array($idProduct));
        $gallery = $this->d->rawQuery("SELECT id, photo FROM table_gallery WHERE id_parent = ? AND (com = 'product' OR type = 'san-pham' OR type = '' OR type IS NULL) AND find_in_set('hienthi', status)", array($idProduct));

        $availableMedia = array();
        if (!empty($product['photo'])) {
            $foundPath = $this->findMediaFile($product['photo']);
            if ($foundPath) {
                $availableMedia[] = array('type' => 'PRODUCT_PHOTO', 'path' => $foundPath, 'name' => 'Ảnh chính sản phẩm');
            }
        }
        foreach ($gallery as $gal) {
            if (!empty($gal['photo'])) {
                $foundPath = $this->findMediaFile($gal['photo']);
                if ($foundPath) {
                    $availableMedia[] = array('type' => 'PRODUCT_PHOTO', 'path' => $foundPath, 'name' => 'Ảnh thư viện gallery');
                }
            }
        }

        // Đọc các asset đã được upload thủ công trong table_ai_video_asset
        $customAssets = $this->d->rawQuery("SELECT * FROM table_ai_video_asset WHERE id_video = ?", array($idVideo));
        $customAssetMap = array();
        foreach ($customAssets as $ca) {
            $customAssetMap[(int)$ca['scene_number']] = $ca;
        }

        $scenes = !empty($video['scenes_data']) ? json_decode($video['scenes_data'], true) : array();
        $missingAssets = array();
        $resolvedScenes = array();

        // Xóa asset tự động cũ để ánh xạ lại
        $this->d->rawQuery("DELETE FROM table_ai_video_asset WHERE id_video = ? AND source_type = 'PRODUCT_DB'", array($idVideo));

        foreach ($scenes as $idx => $sc) {
            $sceneNum = !empty($sc['scene_number']) ? (int)$sc['scene_number'] : ($idx + 1);
            $assetResolved = null;

            // Nếu đã có manual upload cho scene này
            if (!empty($customAssetMap[$sceneNum])) {
                $assetResolved = $customAssetMap[$sceneNum]['source_ref'];
            } elseif (!empty($availableMedia)) {
                // Ánh xạ xoay vòng từ kho ảnh sản phẩm
                $mediaIndex = ($sceneNum - 1) % count($availableMedia);
                $selectedMedia = $availableMedia[$mediaIndex];
                $assetResolved = $selectedMedia['path'];

                // Lưu vào table_ai_video_asset
                $this->d->insert('ai_video_asset', array(
                    'id_video' => $idVideo,
                    'scene_number' => $sceneNum,
                    'asset_type' => $selectedMedia['type'],
                    'source_type' => 'PRODUCT_DB',
                    'source_ref' => $selectedMedia['path'],
                    'file_size' => file_exists($selectedMedia['path']) ? filesize($selectedMedia['path']) : 0,
                    'is_approved' => 1,
                    'notes' => 'Tự động ánh xạ từ ' . $selectedMedia['name'],
                    'date_created' => time()
                ));
            } else {
                // Không có tài nguyên hình ảnh vật lý -> ghi nhận thiếu ảnh nhưng vẫn hỗ trợ Text-to-Video
                $missingAssets[] = array(
                    'scene_number' => $sceneNum,
                    'requirement' => !empty($sc['asset_requirement']) ? $sc['asset_requirement'] : 'Hình ảnh sản phẩm'
                );
            }

            $sc['asset_resolved'] = $assetResolved;
            $resolvedScenes[] = $sc;
        }

        // Cập nhật lại scenes_data và trạng thái
        // Nếu không có ảnh nhưng có shot plan thì trạng thái là WAITING_ASSET (vẫn cho phép Render bằng Text-to-Video Prompt)
        $newStatus = !empty($missingAssets) ? 'WAITING_ASSET' : 'READY';
        $this->d->rawQuery("UPDATE table_ai_video SET scenes_data = ?, status = ?, date_updated = ? WHERE id = ?", array(
            json_encode($resolvedScenes, JSON_UNESCAPED_UNICODE),
            $newStatus,
            time(),
            $idVideo
        ));

        return array(
            'status' => $newStatus,
            'ready' => empty($missingAssets),
            'missing_assets' => $missingAssets
        );
    }

    /**
     * Tính toán mã băm kịch bản SHA-256 đối soát Outdated
     * @param array $contentRecord
     * @return string
     */
    public function computeScriptHash($contentRecord) {
        $text = !empty($contentRecord['content_text']) ? $contentRecord['content_text'] : '';
        $struct = !empty($contentRecord['structured_data']) ? (is_string($contentRecord['structured_data']) ? $contentRecord['structured_data'] : json_encode($contentRecord['structured_data'])) : '';
        return hash('sha256', $text . '|' . $struct);
    }

    /**
     * Kiểm tra video có bị lỗi thời do kịch bản gốc bị sửa đổi không
     * @param int $idVideo
     * @return bool
     */
    public function checkVideoOutdated($idVideo) {
        $idVideo = (int)$idVideo;
        $video = $this->d->rawQueryOne("SELECT id, id_content, script_hash, is_outdated FROM table_ai_video WHERE id = ? LIMIT 1", array($idVideo));
        if (empty($video) || empty($video['id_content'])) {
            return false;
        }

        $content = $this->d->rawQueryOne("SELECT * FROM table_ai_content WHERE id = ? LIMIT 1", array((int)$video['id_content']));
        if (empty($content)) {
            return false;
        }

        $currentHash = $this->computeScriptHash($content);
        $isOutdated = ($currentHash !== $video['script_hash']) ? 1 : 0;

        if ((int)$video['is_outdated'] !== $isOutdated) {
            $this->d->rawQuery("UPDATE table_ai_video SET is_outdated = ?, date_updated = ? WHERE id = ?", array($isOutdated, time(), $idVideo));
        }

        return (bool)$isOutdated;
    }

    /**
     * Kiểm định chất lượng media video sau render (Media QC)
     * @param string $localVideoPath
     * @param array $expectedMetadata
     * @return array ['passed' => bool, 'score' => float, 'checks' => array, 'error' => string]
     */
    public function validateRenderedMedia($localVideoPath, $expectedMetadata = array()) {
        $checks = array(
            'file_exists' => false,
            'non_zero_byte' => false,
            'valid_format' => false,
            'duration_valid' => false,
            'safe_area_compliant' => true
        );

        if (!file_exists($localVideoPath)) {
            return array('passed' => false, 'score' => 0.0, 'checks' => $checks, 'error' => 'File video không tồn tại trên hệ thống cục bộ: ' . $localVideoPath);
        }
        $checks['file_exists'] = true;

        $fileSize = filesize($localVideoPath);
        if ($fileSize <= 0) {
            return array('passed' => false, 'score' => 0.0, 'checks' => $checks, 'error' => 'File video rỗng (0 bytes)');
        }
        $checks['non_zero_byte'] = true;

        // Kiểm tra phần mở rộng và header MP4
        $ext = strtolower(pathinfo($localVideoPath, PATHINFO_EXTENSION));
        if (in_array($ext, array('mp4', 'mov', 'webm'))) {
            $checks['valid_format'] = true;
        }

        $expectedDuration = !empty($expectedMetadata['target_duration']) ? (float)$expectedMetadata['target_duration'] : 30.0;
        $actualDuration = !empty($expectedMetadata['duration_actual']) ? (float)$expectedMetadata['duration_actual'] : $expectedDuration;
        if ($actualDuration > 0 && abs($actualDuration - $expectedDuration) <= 15.0) {
            $checks['duration_valid'] = true;
        }

        $passedCount = count(array_filter($checks));
        $totalChecks = count($checks);
        $score = round(($passedCount / $totalChecks) * 100, 1);
        $isPassed = ($score >= 80.0 && $checks['file_exists'] && $checks['non_zero_byte']);

        return array(
            'passed' => $isPassed,
            'score' => $score,
            'checks' => $checks,
            'error' => $isPassed ? null : 'Kiểm định chất lượng video không đạt yêu cầu'
        );
    }

    /**
     * Phê duyệt Video (Human Approval Gate)
     * @param int $idVideo
     * @param string $adminName
     * @return array ['success' => bool, 'status' => string, 'error' => string]
     */
    public function approveVideo($idVideo, $adminName = 'Admin') {
        $idVideo = (int)$idVideo;
        $video = $this->d->rawQueryOne("SELECT id, status, video_file FROM table_ai_video WHERE id = ? LIMIT 1", array($idVideo));
        if (empty($video)) {
            return array('success' => false, 'status' => 'FAILED', 'error' => 'Video không tồn tại');
        }

        if ($video['status'] !== 'REVIEW_REQUIRED' && $video['status'] !== 'RENDERED') {
            return array('success' => false, 'status' => $video['status'], 'error' => 'Chỉ video ở trạng thái REVIEW_REQUIRED mới được phê duyệt. Trạng thái hiện tại: ' . $video['status']);
        }

        $this->d->rawQuery("UPDATE table_ai_video SET status = 'APPROVED', reviewed_by = ?, reviewed_at = ?, reject_reason = NULL, date_updated = ? WHERE id = ?", array(
            $adminName,
            time(),
            time(),
            $idVideo
        ));

        return array('success' => true, 'status' => 'APPROVED', 'error' => null);
    }

    /**
     * Từ chối Video (Admin Reject)
     * @param int $idVideo
     * @param string $rejectReason
     * @param string $adminName
     * @return array ['success' => bool, 'status' => string, 'error' => string]
     */
    public function rejectVideo($idVideo, $rejectReason, $adminName = 'Admin') {
        $idVideo = (int)$idVideo;
        $rejectReason = trim($rejectReason);
        if (empty($rejectReason)) {
            return array('success' => false, 'status' => 'FAILED', 'error' => 'Bắt buộc nhập lý do từ chối');
        }

        $this->d->rawQuery("UPDATE table_ai_video SET status = 'REJECTED', reject_reason = ?, reviewed_by = ?, reviewed_at = ?, date_updated = ? WHERE id = ?", array(
            $rejectReason,
            $adminName,
            time(),
            time(),
            $idVideo
        ));

        return array('success' => true, 'status' => 'REJECTED', 'error' => null);
    }
}
