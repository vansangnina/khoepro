<?php
/**
 * KHOEPRO - Dynamic Social Scheduler Engine
 * Manages posting quota, time windows, anti-spam cooldowns, atomic dispatching and pool buffering.
 * PHP 7.4 & 8.x Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once __DIR__ . '/class.SocialPublisherFactory.php';

class SocialSchedulerEngine
{
    private $d;
    private $func;

    public function __construct($d = null, $func = null)
    {
        $this->d = $d;
        $this->func = $func;
    }

    /**
     * Get or create default scheduling rule for an account
     * @param int $accountId
     * @param string $platform
     * @return array
     */
    public function getAccountScheduleRule($accountId, $platform = 'tiktok')
    {
        if (!$this->d) return array();

        $rule = $this->d->rawQueryOne(
            "SELECT * FROM table_social_schedule_rule WHERE account_id = ? LIMIT 1",
            array((int)$accountId)
        );

        if (empty($rule)) {
            $defaultTimes = ($platform === 'tiktok') ? array("09:00", "14:00", "20:00") : (($platform === 'facebook') ? array("11:30", "19:30") : array("18:00"));
            $defaultQuota = ($platform === 'tiktok') ? 3 : (($platform === 'facebook') ? 2 : 1);

            $insData = array(
                'account_id' => (int)$accountId,
                'platform' => $platform,
                'is_enabled' => 1,
                'posts_per_day' => $defaultQuota,
                'posting_times_json' => json_encode($defaultTimes),
                'product_cooldown_days' => 14,
                'merchant_cooldown_posts' => 3,
                'content_cooldown_days' => 30,
                'require_approval' => 1,
                'minimum_approved_pool' => 3,
                'date_created' => time(),
                'date_updated' => time()
            );
            $this->d->insert('social_schedule_rule', $insData);
            return $insData;
        }

        return $rule;
    }

    /**
     * Count available approved items in Approved Content Pool
     * @param string $platform
     * @return int
     */
    public function countApprovedPool($platform = 'all')
    {
        if (!$this->d) return 0;
        
        $where = array("p.status IN ('READY', 'DRAFT')", "p.approval_status = 'APPROVED'", "v.status = 'APPROVED'");
        $params = array();

        if ($platform !== 'all') {
            $where[] = "p.platform = ?";
            $params[] = $platform;
        }

        $whereSql = implode(" AND ", $where);
        $row = $this->d->rawQueryOne(
            "SELECT COUNT(*) as pool_count 
             FROM table_publish_post p
             INNER JOIN table_ai_video v ON p.id_video = v.id
             WHERE {$whereSql}",
            $params
        );

        return (int)($row['pool_count'] ?? 0);
    }

    /**
     * Schedule Approved Content into Daily Time Slots obeying Quota & Cooldowns
     * 
     * Buffer Principle:
     * - If quota = 3 posts/day and approved pool has 2 items -> Schedules exactly 2 items.
     * - Never takes unapproved / pending / rejected items to fill quota.
     * 
     * @param int $accountId
     * @param string $actor
     * @return array ['scheduled_count' => int, 'pool_available' => int, 'quota_today' => int, 'skipped_reasons' => array]
     */
    public function scheduleApprovedPool($accountId, $actor = 'system_scheduler')
    {
        if (!$this->d) return array('scheduled_count' => 0, 'error' => 'No database connection');

        $account = $this->d->rawQueryOne("SELECT * FROM table_publish_account WHERE id = ? LIMIT 1", array((int)$accountId));
        if (empty($account)) {
            return array('scheduled_count' => 0, 'error' => 'Account not found: #' . $accountId);
        }

        $platform = $account['platform'] ?? 'tiktok';
        $rule = $this->getAccountScheduleRule($accountId, $platform);
        if (empty($rule['is_enabled'])) {
            return array('scheduled_count' => 0, 'message' => 'Scheduling is disabled for this account.');
        }

        $postsPerDay = (int)($rule['posts_per_day'] ?? 3);
        $postingTimes = !empty($rule['posting_times_json']) ? json_decode($rule['posting_times_json'], true) : array("09:00", "14:00", "20:00");
        if (!is_array($postingTimes) || empty($postingTimes)) {
            $postingTimes = array("09:00", "14:00", "20:00");
        }

        $now = time();
        $todayStart = strtotime(date('Y-m-d 00:00:00', $now));
        $todayEnd = strtotime(date('Y-m-d 23:59:59', $now));

        // Count already scheduled/published posts for this account today
        $alreadyScheduled = $this->d->rawQueryOne(
            "SELECT COUNT(*) as count_today FROM table_publish_post 
             WHERE account_id = ? AND scheduled_at BETWEEN ? AND ? AND status IN ('SCHEDULED', 'PUBLISHED', 'READY', 'PUBLISHING')",
            array((int)$accountId, $todayStart, $todayEnd)
        );
        $countToday = (int)($alreadyScheduled['count_today'] ?? 0);
        $slotsRemaining = max(0, $postsPerDay - $countToday);

        if ($slotsRemaining <= 0) {
            return array('scheduled_count' => 0, 'message' => 'Daily quota reached for today (' . $postsPerDay . ' posts).', 'quota_today' => $postsPerDay);
        }

        // Fetch candidates strictly from Approved Pool
        $poolPosts = $this->d->rawQuery(
            "SELECT p.*, v.video_file, v.target_duration, v.duration_actual, prod.namevi as prod_name, prod.id_brand, prod.id_list
             FROM table_publish_post p
             INNER JOIN table_ai_video v ON p.id_video = v.id
             INNER JOIN table_product prod ON p.id_product = prod.id
             WHERE (p.platform = ? OR p.platform = 'all')
               AND p.approval_status = 'APPROVED'
               AND p.status IN ('DRAFT', 'READY')
               AND (p.scheduled_at IS NULL OR p.scheduled_at = 0)
             ORDER BY p.id ASC
             LIMIT ?",
            array($platform, $slotsRemaining * 3)
        );

        $scheduledCount = 0;
        $skippedReasons = array();

        foreach ($poolPosts as $post) {
            if ($scheduledCount >= $slotsRemaining) break;

            $idProduct = (int)$post['id_product'];
            $idPost = (int)$post['id'];

            // 1. Check Product Cooldown (e.g. >= 14 days)
            $cooldownDays = (int)($rule['product_cooldown_days'] ?? 14);
            $cooldownCutoff = $now - ($cooldownDays * 86400);
            $lastPosted = $this->d->rawQueryOne(
                "SELECT id, published_at, scheduled_at FROM table_publish_post 
                 WHERE id_product = ? AND account_id = ? AND id != ? AND (published_at > ? OR (scheduled_at > ? AND scheduled_at < ?)) LIMIT 1",
                array($idProduct, (int)$accountId, $idPost, $cooldownCutoff, $cooldownCutoff, $now)
            );

            if (!empty($lastPosted)) {
                $skippedReasons[] = "Post #{$idPost} (Sản phẩm #{$idProduct}) bỏ qua do vi phạm Cooldown sản phẩm (< {$cooldownDays} ngày).";
                continue;
            }

            // Assign next available time slot
            $slotIndex = $countToday + $scheduledCount;
            $slotTimeStr = $postingTimes[$slotIndex % count($postingTimes)] ?? '14:00';
            $scheduledTimestamp = strtotime(date('Y-m-d ') . $slotTimeStr);

            // If time slot in today already passed, schedule for slot today + 30 mins or tomorrow
            if ($scheduledTimestamp <= $now) {
                $scheduledTimestamp = $now + (30 * 60); // 30 mins from now
            }

            // Idempotency Key (Fingerprint)
            $idempotencyKey = hash('sha256', "post_{$idPost}_acc_{$accountId}_slot_{$scheduledTimestamp}");

            $this->d->where('id', $idPost);
            $this->d->update('publish_post', array(
                'account_id' => (int)$accountId,
                'status' => 'SCHEDULED',
                'scheduled_at' => $scheduledTimestamp,
                'idempotency_key' => $idempotencyKey,
                'date_updated' => $now
            ));

            $scheduledCount++;
        }

        return array(
            'scheduled_count' => $scheduledCount,
            'quota_today' => $postsPerDay,
            'slots_remaining' => $slotsRemaining,
            'skipped_reasons' => $skippedReasons
        );
    }

    /**
     * Dispatch due scheduled posts with atomic concurrency locking & retry policies
     * @param int $limit
     * @param string $actor
     * @return array
     */
    public function dispatchDuePosts($limit = 10, $actor = 'scheduler_worker')
    {
        if (!$this->d) return array('dispatched' => 0, 'error' => 'Database connection missing');

        $now = time();
        $duePosts = $this->d->rawQuery(
            "SELECT p.*, a.platform as acc_platform, a.account_handle, a.auth_status, a.auth_data, a.api_config_encrypted
             FROM table_publish_post p
             LEFT JOIN table_publish_account a ON p.account_id = a.id
             WHERE p.status = 'SCHEDULED' 
               AND p.scheduled_at <= ?
               AND (p.retry_after IS NULL OR p.retry_after <= ?)
               AND p.publish_lock IS NULL
             ORDER BY p.scheduled_at ASC
             LIMIT ?",
            array($now, $now, (int)$limit)
        );

        $dispatched = 0;
        $manualReady = 0;
        $apiPublished = 0;
        $failed = 0;

        foreach ($duePosts as $p) {
            $idPost = (int)$p['id'];
            $lockToken = uniqid('lock_' . $idPost . '_', true);

            // 1. Atomic lock acquisition
            $this->d->rawQuery(
                "UPDATE table_publish_post SET publish_lock = ?, date_updated = ? WHERE id = ? AND publish_lock IS NULL",
                array($lockToken, $now, $idPost)
            );

            // Verify lock was won
            $lockedRow = $this->d->rawQueryOne(
                "SELECT id, publish_lock FROM table_publish_post WHERE id = ? AND publish_lock = ? LIMIT 1",
                array($idPost, $lockToken)
            );
            if (empty($lockedRow)) {
                // Lock was taken by concurrent process -> Skip
                continue;
            }

            $dispatched++;
            $platform = $p['platform'] ?? ($p['acc_platform'] ?? 'tiktok');
            $publisher = SocialPublisherFactory::create($platform, $this->d, $this->func);

            $accountData = array(
                'account_handle' => $p['account_handle'] ?? '',
                'auth_status' => $p['auth_status'] ?? 'MANUAL_ONLY',
                'auth_data' => $p['auth_data'] ?? '',
                'api_config_encrypted' => $p['api_config_encrypted'] ?? ''
            );

            $res = $publisher->publish($p, $accountData);

            if ($res['success']) {
                $newStatus = ($res['status'] === 'PUBLISHED') ? 'PUBLISHED' : 'READY';
                $this->d->rawQuery(
                    "UPDATE table_publish_post 
                     SET status = ?, publish_lock = NULL, external_post_id = ?, external_post_url = ?, published_at = ?, date_updated = ?
                     WHERE id = ?",
                    array(
                        $newStatus,
                        $res['external_id'] ?? null,
                        $res['external_url'] ?? null,
                        ($newStatus === 'PUBLISHED') ? time() : null,
                        time(),
                        $idPost
                    )
                );

                if ($newStatus === 'PUBLISHED') $apiPublished++;
                else $manualReady++;
            } else {
                $failed++;
                $attempts = (int)$p['attempts'] + 1;
                $backoffSeconds = min(3600, pow(2, $attempts) * 60); // 2m, 4m, 8m, 16m...
                $retryAfter = ($attempts < 4) ? (time() + $backoffSeconds) : null;
                $finalStatus = ($attempts >= 4) ? 'FAILED' : 'SCHEDULED';

                $this->d->rawQuery(
                    "UPDATE table_publish_post 
                     SET status = ?, publish_lock = NULL, attempts = ?, retry_after = ?, error_message = ?, date_updated = ?
                     WHERE id = ?",
                    array($finalStatus, $attempts, $retryAfter, ($res['error'] ?? 'Publish execution failed'), time(), $idPost)
                );
            }
        }

        return array(
            'dispatched' => $dispatched,
            'api_published' => $apiPublished,
            'manual_ready' => $manualReady,
            'failed' => $failed
        );
    }
}
