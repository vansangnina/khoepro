<?php
/**
 * FITNADO Publishing Job Queue & Background Scheduler
 * Phase 07: Publishing Center & TikTok Publishing Foundation
 * Handles scheduled posts, concurrency locking, stale lock recovery, and retry policies.
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once LIBRARIES . 'class/class.PublishingCenter.php';

class PublishJobQueue {
    private $d;
    private $func;
    private $publishingCenter;

    public function __construct($d = null, $func = null) {
        $this->d = $d;
        $this->func = $func;
        $this->publishingCenter = new PublishingCenter($d, $func);
    }

    /**
     * Xử lý các bài đăng đến hạn xuất bản (Scheduled -> Due / Ready / Publish)
     * @param int $limit
     * @param string $actor
     * @return array ['processed' => int, 'manual_due' => int, 'api_published' => int, 'failed' => int]
     */
    public function processScheduledPosts($limit = 10, $actor = 'publish_worker') {
        if (!$this->d) {
            return array('processed' => 0, 'error' => 'Database connection missing');
        }

        // 1. Thu hồi các khóa bị treo quá 10 phút
        $this->recoverStaleLocks(600);

        // 2. Lấy danh sách bài đăng đã đến hạn (scheduled_at <= now)
        $now = time();
        $posts = $this->d->rawQuery(
            "SELECT id, provider, status, scheduled_at, attempts FROM table_publish_post WHERE status = 'SCHEDULED' AND scheduled_at <= ? ORDER BY scheduled_at ASC LIMIT ?",
            array($now, (int)$limit)
        );

        $processed = 0;
        $manualDue = 0;
        $apiPublished = 0;
        $failed = 0;

        foreach ($posts as $p) {
            $idPost = (int)$p['id'];
            $processed++;

            if ($p['provider'] === 'manual') {
                // Manual Provider: Chuyển sang READY để Admin vào copy/đăng
                $this->d->rawQuery(
                    "UPDATE table_publish_post SET status = 'READY', date_updated = ? WHERE id = ? AND status = 'SCHEDULED'",
                    array($now, $idPost)
                );

                $this->publishingCenter->logEvent($idPost, 'SCHEDULE_DUE', 'SCHEDULED', 'READY', $actor, array(
                    'message' => 'Đã đến giờ xuất bản theo lịch. Sẵn sàng cho Admin đăng thủ công.',
                    'scheduled_at' => $p['scheduled_at']
                ));

                $manualDue++;
            } else {
                // API Provider (TikTok / External API)
                $res = $this->publishingCenter->publishNow($idPost, $actor);
                if (!empty($res['success'])) {
                    $apiPublished++;
                } else {
                    $failed++;
                    $attempts = (int)$p['attempts'] + 1;
                    $this->d->rawQuery(
                        "UPDATE table_publish_post SET attempts = ?, error_message = ?, date_updated = ? WHERE id = ?",
                        array($attempts, ($res['error'] ?? 'API Publish Failed'), $now, $idPost)
                    );
                }
            }
        }

        return array(
            'processed' => $processed,
            'manual_due' => $manualDue,
            'api_published' => $apiPublished,
            'failed' => $failed
        );
    }

    /**
     * Tự động phục hồi các bài đăng bị treo trong trạng thái PUBLISHING (Stale Locks)
     * @param int $timeoutSeconds (Mặc định: 600s = 10 phút)
     * @return int
     */
    public function recoverStaleLocks($timeoutSeconds = 600) {
        if (!$this->d) return 0;

        $staleTime = time() - $timeoutSeconds;
        $stalePosts = $this->d->rawQuery(
            "SELECT id, status FROM table_publish_post WHERE status = 'PUBLISHING' AND publish_lock IS NOT NULL AND date_updated < ?",
            array($staleTime)
        );

        $recovered = 0;
        foreach ($stalePosts as $sp) {
            $this->d->rawQuery(
                "UPDATE table_publish_post SET publish_lock = NULL, status = 'READY', error_message = 'Tự động phục hồi sau khi bị treo tác vụ (Stale Lock)', date_updated = ? WHERE id = ?",
                array(time(), (int)$sp['id'])
            );

            $this->publishingCenter->logEvent((int)$sp['id'], 'LOCK_RECOVERED', 'PUBLISHING', 'READY', 'cron_recovery', array(
                'stale_timeout' => $timeoutSeconds
            ));
            $recovered++;
        }

        return $recovered;
    }

    /**
     * Thử lại bài đăng bị thất bại (Retry Failed Post)
     * @param int $idPost
     * @param string $adminUser
     * @return array
     */
    public function retryFailedPost($idPost, $adminUser = 'admin') {
        $post = $this->publishingCenter->getPost($idPost);
        if (empty($post)) {
            return array('success' => false, 'error' => 'Không tìm thấy bài đăng.');
        }

        if ($post['status'] !== 'FAILED') {
            return array('success' => false, 'error' => 'Chỉ có thể thử lại bài đăng đang có trạng thái FAILED.');
        }

        // Tăng số lần thử lại
        $attempts = (int)$post['attempts'] + 1;
        $this->d->rawQuery(
            "UPDATE table_publish_post SET status = 'READY', attempts = ?, error_message = NULL, date_updated = ? WHERE id = ?",
            array($attempts, time(), (int)$idPost)
        );

        $this->publishingCenter->logEvent($idPost, 'RETRIED', 'FAILED', 'READY', $adminUser, array(
            'attempts' => $attempts
        ));

        return array('success' => true, 'attempts' => $attempts);
    }
}
