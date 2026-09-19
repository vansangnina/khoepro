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
     * @param int|null $specificJobId
     * @return array|null Kết quả xử lý hoặc null nếu hết job
     */
    public function processNextJob($specificJobId = null) {
        // 1. Phục hồi các job bị treo (Stale Jobs > 10 phút)
        $this->recoverStaleJobs();

        // 2. Tìm job PENDING hoặc RUNNING cần poll
        $now = time();
        if ($specificJobId) {
            $job = $this->d->rawQueryOne("SELECT * FROM table_ai_video_job WHERE id = ? LIMIT 1", array((int)$specificJobId));
        } else {
            $job = $this->d->rawQueryOne("SELECT * FROM table_ai_video_job WHERE status = 'PENDING' OR (status = 'RUNNING' AND next_poll_at <= ?) ORDER BY id ASC LIMIT 1", array($now));
        }

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
        $composer = new VideoComposer($this->d, $this->func);
        $mode = !empty($video['mode']) ? strtoupper($video['mode']) : VideoComposer::MODE_ECONOMY;
        $scenes = !empty($video['scenes_data']) ? (is_array($video['scenes_data']) ? $video['scenes_data'] : json_decode($video['scenes_data'], true)) : array();

        // Bắt đầu xử lý Job
        if ($job['status'] === 'PENDING') {
            $this->d->rawQuery("UPDATE table_ai_video_job SET status = 'RUNNING', attempts = attempts + 1, started_at = ?, date_updated = ? WHERE id = ?", array(time(), time(), $idJob));
            $this->d->rawQuery("UPDATE table_ai_video SET status = 'PROCESSING', date_updated = ? WHERE id = ?", array(time(), $idVideo));

            // Kiểm tra Cost Guard trước khi Render
            $costEst = $composer->estimateCost($scenes, $mode);
            $guard = $composer->validateCostGuard($costEst, false);
            if (!$guard['allowed']) {
                $this->handleJobFailure($idJob, $idVideo, $job['attempts'] + 1, $job['max_attempts'], $guard['error']);
                return array('id_job' => $idJob, 'success' => false, 'error' => $guard['error']);
            }

            // Kích hoạt VideoComposer Pipeline (Economy / Hybrid / Premium)
            $composeRes = $composer->composeVideo($video);

            if (!$composeRes['success']) {
                $this->handleJobFailure($idJob, $idVideo, $job['attempts'] + 1, $job['max_attempts'], $composeRes['error']);
                return array('id_job' => $idJob, 'success' => false, 'error' => $composeRes['error']);
            }

            $localVideoPath = $composeRes['video_file'];
            $localThumbPath = $composeRes['thumbnail'];
            $costReport = $composeRes['cost_report'];
            $composerLog = $composeRes['composer_log'];

            // Kiểm định chất lượng Media Validation
            $expectedMeta = array(
                'target_duration' => $video['target_duration'],
                'duration_actual' => $composeRes['duration_actual']
            );
            $qcReport = $this->videoEngine->validateRenderedMedia($localVideoPath, $expectedMeta);
            $durationSec = round(microtime(true) - $startTime, 2);

            // Cập nhật video sang REVIEW_REQUIRED (Strict Human Gate)
            $this->d->rawQuery("UPDATE table_ai_video SET status = 'REVIEW_REQUIRED', video_file = ?, thumbnail = ?, duration_actual = ?, width = ?, height = ?, file_size = ?, local_render_cost = ?, ai_video_seconds = ?, ai_video_cost = ?, tts_cost = ?, total_external_api_cost = ?, cost_estimate = ?, composer_log = ?, quality_report = ?, date_updated = ? WHERE id = ?", array(
                $localVideoPath,
                file_exists($localThumbPath) ? $localThumbPath : null,
                !empty($composeRes['duration_actual']) ? (float)$composeRes['duration_actual'] : 30.0,
                !empty($composeRes['width']) ? (int)$composeRes['width'] : 1080,
                !empty($composeRes['height']) ? (int)$composeRes['height'] : 1920,
                !empty($composeRes['file_size']) ? (int)$composeRes['file_size'] : 0,
                !empty($costReport['local_render_cost']) ? (float)$costReport['local_render_cost'] : 0.0,
                !empty($costReport['ai_video_seconds']) ? (int)$costReport['ai_video_seconds'] : (!empty($costReport['ai_seconds']) ? (int)$costReport['ai_seconds'] : 0),
                !empty($costReport['ai_video_cost']) ? (float)$costReport['ai_video_cost'] : 0.0,
                !empty($costReport['tts_cost']) ? (float)$costReport['tts_cost'] : 0.0,
                !empty($costReport['total_external_api_cost']) ? (float)$costReport['total_external_api_cost'] : 0.0,
                !empty($costReport['total_external_api_cost']) ? (float)$costReport['total_external_api_cost'] : 0.0,
                $composerLog,
                json_encode($qcReport, JSON_UNESCAPED_UNICODE),
                time(),
                $idVideo
            ));

            // Hoàn thành Job
            $this->d->rawQuery("UPDATE table_ai_video_job SET status = 'SUCCESS', completed_at = ?, duration = ?, results_summary = ?, date_updated = ? WHERE id = ?", array(
                time(),
                $durationSec,
                'Composed successfully in ' . $mode . ' mode (' . $composeRes['file_size'] . ' bytes, API Cost: ' . number_format($costReport['total_external_api_cost']) . ' VND)',
                time(),
                $idJob
            ));

            return array(
                'id_job' => $idJob,
                'id_video' => $idVideo,
                'success' => true,
                'status' => 'REVIEW_REQUIRED',
                'video_file' => $localVideoPath,
                'duration' => $durationSec,
                'cost_report' => $costReport
            );
        }

        return array('id_job' => $idJob, 'id_video' => $idVideo, 'success' => true, 'status' => 'PROCESSING');
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
