<?php
/**
 * FITNADO Video Render Background Job Queue
 * Phase 06: AI Video Production Engine
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once LIBRARIES . 'class/class.VideoProvider.php';
require_once LIBRARIES . 'class/class.AIVideoEngine.php';

class AIVideoJobQueue {
    private $d;
    private $func;
    private $videoEngine;

    public function __construct($d, $func) {
        $this->d = $d;
        $this->func = $func;
        $this->videoEngine = new AIVideoEngine($d, $func);
    }

    /**
     * Đưa dự án Video vào hàng đợi Render
     * @param int $idVideo
     * @param string $provider Tên provider (mock, creatify, arcads, heygen)
     * @return array ['success' => bool, 'id_job' => int, 'error' => string]
     */
    public function enqueueVideoRender($idVideo, $provider = null) {
        $idVideo = (int)$idVideo;
        $video = $this->d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array($idVideo));
        if (empty($video)) {
            return array('success' => false, 'id_job' => 0, 'error' => 'Dự án video không tồn tại');
        }

        // Kiểm tra tính sẵn sàng của Asset
        if ($video['status'] === 'WAITING_ASSET') {
            return array('success' => false, 'id_job' => 0, 'error' => 'Dự án video đang thiếu tài nguyên trực quan (WAITING_ASSET). Vui lòng upload asset trước khi render.');
        }

        $chosenProvider = !empty($provider) ? $provider : (!empty($video['provider']) ? $video['provider'] : 'mock');

        // Kiểm tra xem đã có job PENDING / RUNNING cho video này chưa
        $existingJob = $this->d->rawQueryOne("SELECT id FROM table_ai_video_job WHERE id_video = ? AND status IN ('PENDING', 'RUNNING') LIMIT 1", array($idVideo));
        if (!empty($existingJob)) {
            return array('success' => true, 'id_job' => (int)$existingJob['id'], 'error' => 'Job render đã tồn tại trong hàng đợi');
        }

        // Tạo job mới
        $jobData = array(
            'id_video' => $idVideo,
            'provider' => $chosenProvider,
            'status' => 'PENDING',
            'attempts' => 0,
            'max_attempts' => 3,
            'poll_count' => 0,
            'date_created' => time(),
            'date_updated' => time()
        );

        $idJob = $this->d->insert('ai_video_job', $jobData);
        if (!$idJob) {
            return array('success' => false, 'id_job' => 0, 'error' => 'Không thể tạo bản ghi table_ai_video_job');
        }

        // Cập nhật trạng thái video sang QUEUED
        $this->d->rawQuery("UPDATE table_ai_video SET status = 'QUEUED', provider = ?, date_updated = ? WHERE id = ?", array(
            $chosenProvider,
            time(),
            $idVideo
        ));

        return array('success' => true, 'id_job' => (int)$idJob, 'error' => null);
    }

    /**
     * Đưa hàng loạt Video Projects vào hàng đợi
     * @param array $videoIds
     * @param string $provider
     * @return array ['total' => int, 'queued' => int, 'failed' => int, 'errors' => array]
     */
    public function enqueueBatch($videoIds, $provider = null) {
        $total = count($videoIds);
        $queued = 0;
        $failed = 0;
        $errors = array();

        foreach ($videoIds as $idVid) {
            $res = $this->enqueueVideoRender($idVid, $provider);
            if ($res['success']) {
                $queued++;
            } else {
                $failed++;
                $errors[] = 'Video #' . $idVid . ': ' . $res['error'];
            }
        }

        return array(
            'total' => $total,
            'queued' => $queued,
            'failed' => $failed,
            'errors' => $errors
        );
    }

    /**
     * Xử lý 1 Job tiếp theo trong hàng đợi
     * @return array|null Kết quả xử lý hoặc null nếu hết job
     */
    public function processNextJob() {
        // 1. Phục hồi các job bị treo (Stale Jobs > 10 phút)
        $this->recoverStaleJobs();

        // 2. Tìm job PENDING hoặc RUNNING cần poll
        $now = time();
        $job = $this->d->rawQueryOne("SELECT * FROM table_ai_video_job WHERE status = 'PENDING' OR (status = 'RUNNING' AND next_poll_at <= ?) ORDER BY id ASC LIMIT 1", array($now));

        if (empty($job)) {
            return null;
        }

        $idJob = (int)$job['id'];
        $idVideo = (int)$job['id_video'];
        $providerName = !empty($job['provider']) ? $job['provider'] : 'mock';

        $video = $this->d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array($idVideo));
        if (empty($video)) {
            $this->d->rawQuery("UPDATE table_ai_video_job SET status = 'FAILED', error_message = 'Video project not found', completed_at = ?, date_updated = ? WHERE id = ?", array(time(), time(), $idJob));
            return array('id_job' => $idJob, 'success' => false, 'error' => 'Video project not found');
        }

        $startTime = microtime(true);
        $provider = VideoProviderFactory::create($providerName, $this->d, $this->func);

        // Bắt đầu xử lý Job
        if ($job['status'] === 'PENDING') {
            $this->d->rawQuery("UPDATE table_ai_video_job SET status = 'RUNNING', attempts = attempts + 1, started_at = ?, date_updated = ? WHERE id = ?", array(time(), time(), $idJob));
            $this->d->rawQuery("UPDATE table_ai_video SET status = 'PROCESSING', date_updated = ? WHERE id = ?", array(time(), $idVideo));

            // Gửi render tới provider
            $renderRes = $provider->createRenderJob($video);

            if (!$renderRes['success']) {
                $this->handleJobFailure($idJob, $idVideo, $job['attempts'] + 1, $job['max_attempts'], $renderRes['error']);
                return array('id_job' => $idJob, 'success' => false, 'error' => $renderRes['error']);
            }

            $providerJobId = $renderRes['provider_job_id'];
            $costEstimate = !empty($renderRes['cost']) ? (float)$renderRes['cost'] : 0.0;

            $this->d->rawQuery("UPDATE table_ai_video SET provider_job_id = ?, cost_estimate = ?, date_updated = ? WHERE id = ?", array(
                $providerJobId,
                $costEstimate,
                time(),
                $idVideo
            ));
            $this->d->rawQuery("UPDATE table_ai_video_job SET provider_job_id = ?, next_poll_at = ?, date_updated = ? WHERE id = ?", array(
                $providerJobId,
                time() + 5, // Poll sau 5 giây
                time(),
                $idJob
            ));
        }

        // Kiểm tra kết quả từ Provider
        $providerJobId = !empty($job['provider_job_id']) ? $job['provider_job_id'] : (!empty($video['provider_job_id']) ? $video['provider_job_id'] : '');
        $statusRes = $provider->checkJobStatus($providerJobId);

        if ($statusRes['status'] === 'READY') {
            // Tải video và thumbnail về lưu trữ cục bộ
            $safeFilename = 'fitnado_vid_' . $idVideo . '_' . time() . '.mp4';
            $localVideoPath = 'upload/video/' . $safeFilename;
            $downloadRes = $provider->downloadVideoAsset($statusRes['video_url'], $localVideoPath);

            if (!$downloadRes['success']) {
                $this->handleJobFailure($idJob, $idVideo, $job['attempts'] + 1, $job['max_attempts'], 'Download video failed: ' . $downloadRes['error']);
                return array('id_job' => $idJob, 'success' => false, 'error' => $downloadRes['error']);
            }

            // Thumbnail
            $thumbFilename = preg_replace('/\.mp4$/i', '.jpg', $safeFilename);
            $localThumbPath = 'upload/video/' . $thumbFilename;

            // Kiểm định chất lượng Media Validation
            $expectedMeta = array(
                'target_duration' => $video['target_duration'],
                'duration_actual' => $statusRes['duration']
            );
            $qcReport = $this->videoEngine->validateRenderedMedia($localVideoPath, $expectedMeta);

            $durationSec = round(microtime(true) - $startTime, 2);

            // Cập nhật video sang REVIEW_REQUIRED
            $this->d->rawQuery("UPDATE table_ai_video SET status = 'REVIEW_REQUIRED', video_file = ?, thumbnail = ?, duration_actual = ?, width = ?, height = ?, file_size = ?, quality_report = ?, date_updated = ? WHERE id = ?", array(
                $localVideoPath,
                file_exists($localThumbPath) ? $localThumbPath : null,
                $statusRes['duration'],
                $statusRes['width'],
                $statusRes['height'],
                $downloadRes['file_size'],
                json_encode($qcReport, JSON_UNESCAPED_UNICODE),
                time(),
                $idVideo
            ));

            // Hoàn thành Job
            $this->d->rawQuery("UPDATE table_ai_video_job SET status = 'SUCCESS', completed_at = ?, duration = ?, results_summary = ?, date_updated = ? WHERE id = ?", array(
                time(),
                $durationSec,
                'Rendered and downloaded successfully (' . $downloadRes['file_size'] . ' bytes)',
                time(),
                $idJob
            ));

            return array(
                'id_job' => $idJob,
                'id_video' => $idVideo,
                'success' => true,
                'status' => 'REVIEW_REQUIRED',
                'video_file' => $localVideoPath,
                'duration' => $durationSec
            );
        } elseif ($statusRes['status'] === 'FAILED') {
            $this->handleJobFailure($idJob, $idVideo, $job['attempts'] + 1, $job['max_attempts'], $statusRes['error']);
            return array('id_job' => $idJob, 'success' => false, 'error' => $statusRes['error']);
        } else {
            // Vẫn đang PROCESSING -> Đặt lịch polling tiếp theo
            $this->d->rawQuery("UPDATE table_ai_video_job SET poll_count = poll_count + 1, next_poll_at = ?, date_updated = ? WHERE id = ?", array(
                time() + 10,
                time(),
                $idJob
            ));
            return array('id_job' => $idJob, 'id_video' => $idVideo, 'success' => true, 'status' => 'PROCESSING');
        }
    }

    /**
     * Xử lý lỗi Job với cơ chế Retry
     */
    private function handleJobFailure($idJob, $idVideo, $attempts, $maxAttempts, $errorMessage) {
        if ($attempts < $maxAttempts) {
            // Lên lịch retry sau 30 giây
            $this->d->rawQuery("UPDATE table_ai_video_job SET status = 'PENDING', next_poll_at = ?, error_message = ?, date_updated = ? WHERE id = ?", array(
                time() + 30,
                $errorMessage,
                time(),
                $idJob
            ));
        } else {
            // Đã hết số lần retry -> FAILED
            $this->d->rawQuery("UPDATE table_ai_video_job SET status = 'FAILED', completed_at = ?, error_message = ?, date_updated = ? WHERE id = ?", array(
                time(),
                $errorMessage,
                time(),
                $idJob
            ));
            $this->d->rawQuery("UPDATE table_ai_video SET status = 'FAILED', date_updated = ? WHERE id = ?", array(
                time(),
                $idVideo
            ));
        }
    }

    /**
     * Tự động phục hồi các Job bị treo quá 10 phút
     */
    public function recoverStaleJobs() {
        $staleThreshold = time() - 600; // 10 phút
        $staleJobs = $this->d->rawQuery("SELECT id, id_video, attempts, max_attempts FROM table_ai_video_job WHERE status = 'RUNNING' AND started_at < ?", array($staleThreshold));

        foreach ($staleJobs as $sj) {
            $idJob = (int)$sj['id'];
            $idVideo = (int)$sj['id_video'];
            $attempts = (int)$sj['attempts'];
            $maxAttempts = (int)$sj['max_attempts'];

            $this->handleJobFailure($idJob, $idVideo, $attempts, $maxAttempts, 'Job timed out after 10 minutes (Stale Recovery)');
        }
    }

    /**
     * Chạy theo lô tối đa $limit jobs
     * @param int $limit
     * @return array
     */
    public function processBatch($limit = 5) {
        $processed = 0;
        $results = array();

        while ($processed < $limit) {
            $res = $this->processNextJob();
            if ($res === null) {
                break;
            }
            $results[] = $res;
            $processed++;
        }

        return array(
            'processed_count' => $processed,
            'results' => $results
        );
    }
}
