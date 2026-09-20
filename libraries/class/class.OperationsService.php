<?php
/**
 * FITNADO Operations & Automation Control Center Service
 * Phase 10: Unified Operations Control Plane
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

class OperationsService {
    private $d;
    private $func;
    private $settingsCache = null;

    // Severity Levels
    const SEV_INFO     = 'INFO';
    const SEV_WARNING  = 'WARNING';
    const SEV_ERROR    = 'ERROR';
    const SEV_CRITICAL = 'CRITICAL';

    // Health Statuses
    const STATUS_HEALTHY   = 'HEALTHY';
    const STATUS_WARNING   = 'WARNING';
    const STATUS_DEGRADED  = 'DEGRADED';
    const STATUS_FAILED    = 'FAILED';
    const STATUS_DISABLED  = 'DISABLED';
    const STATUS_UNKNOWN   = 'UNKNOWN';

    // Provider Statuses
    const PROV_CONFIGURED     = 'CONFIGURED';
    const PROV_NOT_CONFIGURED = 'NOT_CONFIGURED';
    const PROV_AVAILABLE      = 'AVAILABLE';
    const PROV_UNAVAILABLE    = 'UNAVAILABLE';
    const PROV_RATE_LIMITED   = 'RATE_LIMITED';
    const PROV_AUTH_ERROR     = 'AUTH_ERROR';

    public function __construct($d = null, $func = null) {
        $this->d = $d;
        $this->func = $func;
    }

    /**
     * Get setting value with cache and default fallback
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting($key, $default = null) {
        if ($this->settingsCache === null && $this->d) {
            $this->settingsCache = array();
            $rows = $this->d->rawQuery("SELECT setting_key, setting_value FROM table_analytics_setting");
            if (!empty($rows)) {
                foreach ($rows as $r) {
                    $this->settingsCache[$r['setting_key']] = $r['setting_value'];
                }
            }
        }

        if (isset($this->settingsCache[$key])) {
            $val = $this->settingsCache[$key];
            if (is_numeric($val)) {
                return (strpos($val, '.') !== false) ? (float)$val : (int)$val;
            }
            return $val;
        }

        return $default;
    }

    /**
     * Save/Update setting value
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string $desc
     * @return bool
     */
    public function saveSetting($key, $value, $group = 'operations', $desc = '') {
        if (!$this->d) return false;

        $strVal = is_array($value) ? json_encode($value) : (string)$value;
        $exists = $this->d->rawQueryOne("SELECT id FROM table_analytics_setting WHERE setting_key = ? LIMIT 1", array($key));

        $data = array(
            'setting_value' => $strVal,
            'date_updated' => time()
        );

        if (!empty($exists)) {
            $this->d->where('setting_key', $key);
            $res = $this->d->update('analytics_setting', $data);
        } else {
            $data['setting_key'] = $key;
            $data['setting_group'] = $group;
            $data['description'] = $desc;
            $res = $this->d->insert('analytics_setting', $data);
        }

        if ($this->settingsCache !== null) {
            $this->settingsCache[$key] = $strVal;
        }

        return (bool)$res;
    }

    // =========================================================================
    // 1. PROCESS REGISTRY & WORKER HEARTBEATS
    // =========================================================================

    /**
     * Official Registered Background Workers
     * @return array
     */
    public function getRegisteredWorkers() {
        return array(
            'product_research_worker' => array(
                'key' => 'product_research_worker',
                'name' => 'Product Research Worker',
                'module' => 'research',
                'file' => 'cron/product_research_worker.php',
                'queue_table' => 'table_product_research_job',
                'expected_interval_sec' => 3600, // 1 hour
                'type' => 'worker'
            ),
            'ai_content_worker' => array(
                'key' => 'ai_content_worker',
                'name' => 'AI Content Worker',
                'module' => 'content',
                'file' => 'cron/ai_content_worker.php',
                'queue_table' => 'table_ai_content_job',
                'expected_interval_sec' => 1800, // 30 mins
                'type' => 'worker'
            ),
            'video_render_worker' => array(
                'key' => 'video_render_worker',
                'name' => 'AI Video Render Worker',
                'module' => 'video',
                'file' => 'cron/video_render_worker.php',
                'queue_table' => 'table_ai_video_job',
                'expected_interval_sec' => 1800, // 30 mins
                'type' => 'worker'
            ),
            'publish_worker' => array(
                'key' => 'publish_worker',
                'name' => 'Publishing Dispatch Worker',
                'module' => 'publishing',
                'file' => 'cron/publish_worker.php',
                'queue_table' => 'table_publish_post',
                'expected_interval_sec' => 900, // 15 mins
                'type' => 'worker'
            ),
            'analytics_service' => array(
                'key' => 'analytics_service',
                'name' => 'Analytics & Attribution Engine',
                'module' => 'analytics',
                'file' => 'libraries/class/class.AnalyticsService.php',
                'queue_table' => 'table_analytics_event',
                'expected_interval_sec' => 3600,
                'type' => 'service'
            ),
            'optimization_engine' => array(
                'key' => 'optimization_engine',
                'name' => 'Optimization & Experiment Engine',
                'module' => 'optimization',
                'file' => 'libraries/class/class.OptimizationEngine.php',
                'queue_table' => 'table_optimization_recommendation',
                'expected_interval_sec' => 86400, // Daily
                'type' => 'service'
            )
        );
    }

    /**
     * Record worker start
     * @param string $workerKey
     * @param string $type
     * @param array $metadata
     * @return bool
     */
    public function recordWorkerStart($workerKey, $type = 'worker', $metadata = array()) {
        if (!$this->d) return false;
        $now = time();
        $hostname = gethostname() ?: 'localhost';
        $pid = function_exists('getmypid') ? getmypid() : null;

        $registered = $this->getRegisteredWorkers();
        $name = isset($registered[$workerKey]) ? $registered[$workerKey]['name'] : $workerKey;

        $data = array(
            'worker_key' => $workerKey,
            'worker_name' => $name,
            'worker_type' => $type,
            'last_started_at' => $now,
            'last_heartbeat_at' => $now,
            'hostname' => $hostname,
            'pid' => $pid,
            'metadata' => !empty($metadata) ? json_encode($this->sanitizeSecrets($metadata)) : null,
            'date_updated' => $now
        );

        $exists = $this->d->rawQueryOne("SELECT id FROM table_system_worker_status WHERE worker_key = ? LIMIT 1", array($workerKey));
        if ($exists) {
            $this->d->where('worker_key', $workerKey);
            return (bool)$this->d->update('system_worker_status', $data);
        } else {
            $data['date_created'] = $now;
            return (bool)$this->d->insert('system_worker_status', $data);
        }
    }

    /**
     * Record worker heartbeat
     * @param string $workerKey
     * @param array $metadata
     * @return bool
     */
    public function recordHeartbeat($workerKey, $metadata = array()) {
        if (!$this->d) return false;
        $now = time();
        $pid = function_exists('getmypid') ? getmypid() : null;

        $data = array(
            'last_heartbeat_at' => $now,
            'pid' => $pid,
            'date_updated' => $now
        );
        if (!empty($metadata)) {
            $data['metadata'] = json_encode($this->sanitizeSecrets($metadata));
        }

        $exists = $this->d->rawQueryOne("SELECT id FROM table_system_worker_status WHERE worker_key = ? LIMIT 1", array($workerKey));
        if ($exists) {
            $this->d->where('worker_key', $workerKey);
            return (bool)$this->d->update('system_worker_status', $data);
        } else {
            return $this->recordWorkerStart($workerKey, 'worker', $metadata);
        }
    }

    /**
     * Record worker success
     * @param string $workerKey
     * @param array $metadata
     * @return bool
     */
    public function recordWorkerSuccess($workerKey, $metadata = array()) {
        if (!$this->d) return false;
        $now = time();

        $data = array(
            'last_heartbeat_at' => $now,
            'last_completed_at' => $now,
            'last_success_at' => $now,
            'last_error' => null,
            'date_updated' => $now
        );
        if (!empty($metadata)) {
            $data['metadata'] = json_encode($this->sanitizeSecrets($metadata));
        }

        $this->d->where('worker_key', $workerKey);
        return (bool)$this->d->update('system_worker_status', $data);
    }

    /**
     * Record worker error
     * @param string $workerKey
     * @param string $error
     * @param array $metadata
     * @return bool
     */
    public function recordWorkerError($workerKey, $error, $metadata = array()) {
        if (!$this->d) return false;
        $now = time();
        $cleanError = is_string($error) ? $this->sanitizeSecrets($error) : json_encode($this->sanitizeSecrets($error));

        $data = array(
            'last_heartbeat_at' => $now,
            'last_completed_at' => $now,
            'last_error_at' => $now,
            'last_error' => $cleanError,
            'date_updated' => $now
        );
        if (!empty($metadata)) {
            $data['metadata'] = json_encode($this->sanitizeSecrets($metadata));
        }

        $this->d->where('worker_key', $workerKey);
        $res = (bool)$this->d->update('system_worker_status', $data);

        // Also record an incident alert
        $this->recordAlert($workerKey, self::SEV_ERROR, "Worker '{$workerKey}' encountered error", $cleanError, $workerKey);

        return $res;
    }

    /**
     * Get Worker Statuses with Health Computation
     * @return array
     */
    public function getWorkerStatuses() {
        $registered = $this->getRegisteredWorkers();
        $thresholdSec = (int)$this->getSetting('worker_heartbeat_threshold_seconds', 300);
        $now = time();

        $dbWorkers = array();
        if ($this->d) {
            $rows = $this->d->rawQuery("SELECT * FROM table_system_worker_status");
            if (!empty($rows)) {
                foreach ($rows as $r) {
                    $dbWorkers[$r['worker_key']] = $r;
                }
            }
        }

        $results = array();
        foreach ($registered as $key => $meta) {
            $dbRow = $dbWorkers[$key] ?? null;
            $module = $meta['module'];
            $isModuleEnabled = $this->isAutomationEnabled($module);

            if (!$isModuleEnabled || !$this->isAutomationEnabled()) {
                $health = self::STATUS_DISABLED;
                $healthReason = 'Automation switch is OFF';
            } elseif (empty($dbRow) || empty($dbRow['last_heartbeat_at'])) {
                $health = self::STATUS_UNKNOWN;
                $healthReason = 'No heartbeat recorded yet';
            } else {
                $heartbeatAge = $now - (int)$dbRow['last_heartbeat_at'];
                $hasRecentError = !empty($dbRow['last_error_at']) 
                    && ($now - (int)$dbRow['last_error_at']) < $thresholdSec 
                    && (empty($dbRow['last_success_at']) || (int)$dbRow['last_success_at'] < (int)$dbRow['last_error_at']);

                if ($hasRecentError) {
                    $health = self::STATUS_FAILED;
                    $healthReason = 'Recent fatal error: ' . ($dbRow['last_error'] ?? '');
                } elseif ($heartbeatAge <= $thresholdSec) {
                    $health = self::STATUS_HEALTHY;
                    $healthReason = "Heartbeat active ({$heartbeatAge}s ago)";
                } elseif ($heartbeatAge <= ($thresholdSec * 3)) {
                    $health = self::STATUS_WARNING;
                    $healthReason = "Heartbeat delayed ({$heartbeatAge}s ago)";
                } else {
                    $health = self::STATUS_DEGRADED;
                    $healthReason = "Heartbeat stale ({$heartbeatAge}s ago)";
                }
            }

            // Pending and failed jobs in respective queue
            $queueStats = $this->getQueueMetricsForModule($module);

            $results[$key] = array(
                'key' => $key,
                'name' => $meta['name'],
                'module' => $module,
                'type' => $meta['type'],
                'enabled' => $isModuleEnabled,
                'health' => $health,
                'health_reason' => $healthReason,
                'last_started_at' => $dbRow['last_started_at'] ?? null,
                'last_heartbeat_at' => $dbRow['last_heartbeat_at'] ?? null,
                'last_completed_at' => $dbRow['last_completed_at'] ?? null,
                'last_success_at' => $dbRow['last_success_at'] ?? null,
                'last_error_at' => $dbRow['last_error_at'] ?? null,
                'last_error' => $dbRow['last_error'] ?? null,
                'hostname' => $dbRow['hostname'] ?? null,
                'pid' => $dbRow['pid'] ?? null,
                'pending_jobs' => $queueStats['pending'] ?? 0,
                'failed_jobs' => $queueStats['failed'] ?? 0,
                'processing_jobs' => $queueStats['processing'] ?? 0
            );
        }

        return $results;
    }

    // =========================================================================
    // 2. QUEUE HEALTH & STUCK JOB DETECTION
    // =========================================================================

    /**
     * Get Queue Metrics for a specific module
     * @param string $module
     * @return array
     */
    public function getQueueMetricsForModule($module) {
        if (!$this->d) return array('pending' => 0, 'processing' => 0, 'completed' => 0, 'failed' => 0);

        $table = '';
        $statusCol = 'status';
        $pendingVal = 'PENDING';
        $runningVal = 'RUNNING';
        $successVal = 'SUCCESS';
        $failedVal = 'FAILED';

        switch ($module) {
            case 'research':
                $table = 'table_product_research_job';
                break;
            case 'content':
                $table = 'table_ai_content_job';
                break;
            case 'video':
                $table = 'table_ai_video_job';
                break;
            case 'publishing':
                $table = 'table_publish_post';
                $pendingVal = 'SCHEDULED';
                $runningVal = 'PUBLISHING';
                $successVal = 'PUBLISHED';
                break;
            default:
                return array('pending' => 0, 'processing' => 0, 'completed' => 0, 'failed' => 0);
        }

        $res = array(
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'failed' => 0,
            'oldest_pending_at' => null,
            'avg_duration' => 0.0
        );

        $counts = $this->d->rawQuery("SELECT {$statusCol}, COUNT(*) as cnt FROM {$table} GROUP BY {$statusCol}");
        if (!empty($counts)) {
            foreach ($counts as $c) {
                $st = strtoupper($c[$statusCol]);
                if ($st === $pendingVal) $res['pending'] += (int)$c['cnt'];
                elseif ($st === $runningVal || $st === 'PROCESSING' || $st === 'QUEUED') $res['processing'] += (int)$c['cnt'];
                elseif ($st === $successVal) $res['completed'] += (int)$c['cnt'];
                elseif ($st === $failedVal) $res['failed'] += (int)$c['cnt'];
            }
        }

        // Oldest pending
        $dateCol = ($module === 'publishing') ? 'scheduled_at' : 'date_created';
        $oldest = $this->d->rawQueryOne("SELECT {$dateCol} as dt FROM {$table} WHERE {$statusCol} = ? ORDER BY {$dateCol} ASC LIMIT 1", array($pendingVal));
        if (!empty($oldest['dt'])) {
            $res['oldest_pending_at'] = (int)$oldest['dt'];
        }

        // Avg duration
        if ($module !== 'publishing') {
            $avg = $this->d->rawQueryOne("SELECT AVG(duration) as avg_d FROM {$table} WHERE duration > 0 LIMIT 1");
            if (!empty($avg['avg_d'])) {
                $res['avg_duration'] = round((float)$avg['avg_d'], 2);
            }
        }

        return $res;
    }

    /**
     * Get All Queue Summaries
     * @return array
     */
    public function getQueueSummaries() {
        return array(
            'research' => $this->getQueueMetricsForModule('research'),
            'content' => $this->getQueueMetricsForModule('content'),
            'video' => $this->getQueueMetricsForModule('video'),
            'publishing' => $this->getQueueMetricsForModule('publishing')
        );
    }

    /**
     * Detect and Flag Stuck Jobs
     * @return array
     */
    public function detectAndFlagStuckJobs() {
        if (!$this->d) return array();
        $now = time();
        $defaultThreshold = (int)$this->getSetting('stuck_job_threshold_seconds', 1800); // 30 mins
        $videoThreshold = $defaultThreshold * 2; // 60 mins for video

        $stuckJobs = array();

        // 1. Research Jobs
        $researchStuck = $this->d->rawQuery(
            "SELECT id, provider, status, started_at, date_created FROM table_product_research_job WHERE status = 'RUNNING' AND started_at > 0 AND started_at <= ?",
            array($now - $defaultThreshold)
        );
        if (!empty($researchStuck)) {
            foreach ($researchStuck as $j) {
                $stuckJobs[] = array(
                    'module' => 'research',
                    'job_id' => (int)$j['id'],
                    'provider' => $j['provider'],
                    'started_at' => (int)$j['started_at'],
                    'running_seconds' => $now - (int)$j['started_at'],
                    'threshold_seconds' => $defaultThreshold
                );
                $this->recordAlert('research', self::SEV_WARNING, "Research Job #{$j['id']} is stuck in RUNNING state", "Job #{$j['id']} has been running for " . ($now - (int)$j['started_at']) . " seconds.", (string)$j['id']);
            }
        }

        // 2. Content Jobs
        $contentStuck = $this->d->rawQuery(
            "SELECT id, content_types, status, started_at, date_created FROM table_ai_content_job WHERE status = 'RUNNING' AND started_at > 0 AND started_at <= ?",
            array($now - $defaultThreshold)
        );
        if (!empty($contentStuck)) {
            foreach ($contentStuck as $j) {
                $stuckJobs[] = array(
                    'module' => 'content',
                    'job_id' => (int)$j['id'],
                    'provider' => $j['content_types'] ?? 'ai_content',
                    'started_at' => (int)$j['started_at'],
                    'running_seconds' => $now - (int)$j['started_at'],
                    'threshold_seconds' => $defaultThreshold
                );
                $this->recordAlert('content', self::SEV_WARNING, "Content Job #{$j['id']} is stuck in RUNNING state", "Job #{$j['id']} has been running for " . ($now - (int)$j['started_at']) . " seconds.", (string)$j['id']);
            }
        }

        // 3. Video Jobs
        $videoStuck = $this->d->rawQuery(
            "SELECT id, provider, status, started_at, date_created FROM table_ai_video_job WHERE status = 'RUNNING' AND started_at > 0 AND started_at <= ?",
            array($now - $videoThreshold)
        );
        if (!empty($videoStuck)) {
            foreach ($videoStuck as $j) {
                $stuckJobs[] = array(
                    'module' => 'video',
                    'job_id' => (int)$j['id'],
                    'provider' => $j['provider'],
                    'started_at' => (int)$j['started_at'],
                    'running_seconds' => $now - (int)$j['started_at'],
                    'threshold_seconds' => $videoThreshold
                );
                $this->recordAlert('video', self::SEV_WARNING, "Video Job #{$j['id']} is stuck in RUNNING state", "Job #{$j['id']} has been rendering for " . ($now - (int)$j['started_at']) . " seconds.", (string)$j['id']);
            }
        }

        // 4. Publishing Posts in PUBLISHING state
        $pubStuck = $this->d->rawQuery(
            "SELECT id, provider, status, date_updated FROM table_publish_post WHERE status = 'PUBLISHING' AND date_updated > 0 AND date_updated <= ?",
            array($now - $defaultThreshold)
        );
        if (!empty($pubStuck)) {
            foreach ($pubStuck as $j) {
                $stuckJobs[] = array(
                    'module' => 'publishing',
                    'job_id' => (int)$j['id'],
                    'provider' => $j['provider'],
                    'started_at' => (int)$j['date_updated'],
                    'running_seconds' => $now - (int)$j['date_updated'],
                    'threshold_seconds' => $defaultThreshold
                );
                $this->recordAlert('publishing', self::SEV_WARNING, "Post #{$j['id']} is stuck in PUBLISHING state", "Post #{$j['id']} has been publishing for " . ($now - (int)$j['date_updated']) . " seconds.", (string)$j['id']);
            }
        }

        return $stuckJobs;
    }

    /**
     * Unified All Jobs Listing with Filters & Pagination
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAllJobs($filters = array(), $page = 1, $perPage = 20) {
        if (!$this->d) return array('items' => array(), 'total' => 0, 'page' => $page, 'total_pages' => 1);

        $module = $filters['module'] ?? 'all';
        $status = $filters['status'] ?? 'all';
        $offset = ($page - 1) * $perPage;

        $items = array();

        // 1. Research
        if ($module === 'all' || $module === 'research') {
            $where = array();
            $params = array();
            if ($status !== 'all') {
                $where[] = "status = ?";
                $params[] = strtoupper($status);
            }
            $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            $rows = $this->d->rawQuery("SELECT id, id_seed, provider, depth, status, attempts, error_message, started_at, finished_at as completed_at, duration, date_created FROM table_product_research_job {$whereSql} ORDER BY id DESC LIMIT 50", $params);
            if (!empty($rows)) {
                foreach ($rows as $r) {
                    $items[] = array(
                        'id' => (int)$r['id'],
                        'module' => 'research',
                        'type' => 'Research Discovery (' . ($r['depth'] ?? 'STANDARD') . ')',
                        'provider' => $r['provider'],
                        'status' => $r['status'],
                        'attempts' => (int)$r['attempts'],
                        'duration' => (float)$r['duration'],
                        'error_message' => $this->sanitizeSecrets($r['error_message']),
                        'date_created' => (int)$r['date_created'],
                        'started_at' => (int)$r['started_at'],
                        'completed_at' => (int)$r['completed_at'],
                        'link' => 'index.php?com=product_research&act=jobs'
                    );
                }
            }
        }

        // 2. Content
        if ($module === 'all' || $module === 'content') {
            $where = array();
            $params = array();
            if ($status !== 'all') {
                $where[] = "status = ?";
                $params[] = strtoupper($status);
            }
            $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            $rows = $this->d->rawQuery("SELECT id, id_product, content_types, status, attempts, error_message, started_at, completed_at, duration, date_created FROM table_ai_content_job {$whereSql} ORDER BY id DESC LIMIT 50", $params);
            if (!empty($rows)) {
                foreach ($rows as $r) {
                    $items[] = array(
                        'id' => (int)$r['id'],
                        'module' => 'content',
                        'type' => 'AI Content (' . ($r['content_types'] ?? 'ALL') . ')',
                        'provider' => 'gemini-1.5-flash',
                        'status' => $r['status'],
                        'attempts' => (int)$r['attempts'],
                        'duration' => (float)$r['duration'],
                        'error_message' => $this->sanitizeSecrets($r['error_message']),
                        'date_created' => (int)$r['date_created'],
                        'started_at' => (int)$r['started_at'],
                        'completed_at' => (int)$r['completed_at'],
                        'link' => 'index.php?com=ai_content&act=jobs'
                    );
                }
            }
        }

        // 3. Video
        if ($module === 'all' || $module === 'video') {
            $where = array();
            $params = array();
            if ($status !== 'all') {
                $where[] = "status = ?";
                $params[] = strtoupper($status);
            }
            $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            $rows = $this->d->rawQuery("SELECT id, id_video, provider, status, attempts, error_message, started_at, completed_at, duration, date_created FROM table_ai_video_job {$whereSql} ORDER BY id DESC LIMIT 50", $params);
            if (!empty($rows)) {
                foreach ($rows as $r) {
                    $items[] = array(
                        'id' => (int)$r['id'],
                        'module' => 'video',
                        'type' => 'Video Render (Video #' . ($r['id_video'] ?? 0) . ')',
                        'provider' => $r['provider'],
                        'status' => $r['status'],
                        'attempts' => (int)$r['attempts'],
                        'duration' => (float)$r['duration'],
                        'error_message' => $this->sanitizeSecrets($r['error_message']),
                        'date_created' => (int)$r['date_created'],
                        'started_at' => (int)$r['started_at'],
                        'completed_at' => (int)$r['completed_at'],
                        'link' => 'index.php?com=ai_video&act=jobs'
                    );
                }
            }
        }

        // 4. Publishing
        if ($module === 'all' || $module === 'publishing') {
            $where = array();
            $params = array();
            if ($status !== 'all') {
                $where[] = "status = ?";
                $params[] = strtoupper($status);
            }
            $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            $rows = $this->d->rawQuery("SELECT id, id_product, id_video, platform, provider, status, attempts, error_message, scheduled_at, published_at, date_created FROM table_publish_post {$whereSql} ORDER BY id DESC LIMIT 50", $params);
            if (!empty($rows)) {
                foreach ($rows as $r) {
                    $items[] = array(
                        'id' => (int)$r['id'],
                        'module' => 'publishing',
                        'type' => 'Post Package (' . strtoupper($r['platform'] ?? 'TIKTOK') . ')',
                        'provider' => $r['provider'],
                        'status' => $r['status'],
                        'attempts' => (int)$r['attempts'],
                        'duration' => 0.0,
                        'error_message' => $this->sanitizeSecrets($r['error_message']),
                        'date_created' => (int)$r['date_created'],
                        'started_at' => (int)$r['scheduled_at'],
                        'completed_at' => (int)$r['published_at'],
                        'link' => 'index.php?com=publishing&act=posts'
                    );
                }
            }
        }

        // Sort descending by date_created
        usort($items, function($a, $b) {
            return $b['date_created'] - $a['date_created'];
        });

        $total = count($items);
        $paginated = array_slice($items, $offset, $perPage);

        return array(
            'items' => $paginated,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => max(1, (int)ceil($total / $perPage))
        );
    }

    /**
     * Get Detailed Job Information
     * @param string $module
     * @param int $jobId
     * @return array|null
     */
    public function getJobDetail($module, $jobId) {
        if (!$this->d || empty($jobId)) return null;

        switch ($module) {
            case 'research':
                $job = $this->d->rawQueryOne("SELECT * FROM table_product_research_job WHERE id = ? LIMIT 1", array($jobId));
                if ($job) {
                    $seed = !empty($job['id_seed']) ? $this->d->rawQueryOne("SELECT * FROM table_product_research_seed WHERE id = ? LIMIT 1", array($job['id_seed'])) : null;
                    return array(
                        'module' => 'research',
                        'job' => $this->sanitizeSecrets($job),
                        'related' => array('seed' => $this->sanitizeSecrets($seed))
                    );
                }
                break;
            case 'content':
                $job = $this->d->rawQueryOne("SELECT * FROM table_ai_content_job WHERE id = ? LIMIT 1", array($jobId));
                if ($job) {
                    $product = !empty($job['id_product']) ? $this->d->rawQueryOne("SELECT id, namevi, code, sale_price FROM table_product WHERE id = ? LIMIT 1", array($job['id_product'])) : null;
                    return array(
                        'module' => 'content',
                        'job' => $this->sanitizeSecrets($job),
                        'related' => array('product' => $product)
                    );
                }
                break;
            case 'video':
                $job = $this->d->rawQueryOne("SELECT * FROM table_ai_video_job WHERE id = ? LIMIT 1", array($jobId));
                if ($job) {
                    $video = !empty($job['id_video']) ? $this->d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array($job['id_video'])) : null;
                    return array(
                        'module' => 'video',
                        'job' => $this->sanitizeSecrets($job),
                        'related' => array('video' => $this->sanitizeSecrets($video))
                    );
                }
                break;
            case 'publishing':
                $post = $this->d->rawQueryOne("SELECT * FROM table_publish_post WHERE id = ? LIMIT 1", array($jobId));
                if ($post) {
                    $logs = $this->d->rawQuery("SELECT * FROM table_publish_log WHERE id_post = ? ORDER BY id DESC LIMIT 10", array($jobId));
                    return array(
                        'module' => 'publishing',
                        'job' => $this->sanitizeSecrets($post),
                        'related' => array('logs' => $this->sanitizeSecrets($logs))
                    );
                }
                break;
        }

        return null;
    }

    // =========================================================================
    // 3. PROVIDER HEALTH (ZERO-PAID DIAGNOSTICS)
    // =========================================================================

    /**
     * Get Provider Readiness & Health Status without making paid API calls
     * @return array
     */
    public function getProvidersStatus() {
        global $config;
        $now = time();

        $providers = array(
            'ai_research' => array(
                'key' => 'ai_research',
                'name' => 'AI Research Agent',
                'purpose' => 'Product Discovery & Market Scoring',
                'type' => 'ai_agent',
                'status' => self::PROV_CONFIGURED,
                'status_label' => 'Configured (Internal Engine)',
                'last_success_at' => null,
                'last_error' => null
            ),
            'llm_engine' => array(
                'key' => 'llm_engine',
                'name' => 'LLM Content Engine',
                'purpose' => 'TikTok Scripts, Hooks & SEO Metadata',
                'type' => 'gemini-1.5-pro',
                'status' => self::PROV_CONFIGURED,
                'status_label' => 'Configured (Gemini / Mock)',
                'last_success_at' => null,
                'last_error' => null
            ),
            'voice_tts' => array(
                'key' => 'voice_tts',
                'name' => 'Beeknoee TTS Voice',
                'purpose' => 'Vietnamese Text-to-Speech synthesis',
                'type' => 'beeknoee',
                'status' => (!empty($config['beeknoee']['api_key'])) ? self::PROV_CONFIGURED : self::PROV_NOT_CONFIGURED,
                'status_label' => (!empty($config['beeknoee']['api_key'])) ? 'Configured (Beeknoee Active)' : 'Not Configured',
                'last_success_at' => null,
                'last_error' => null
            ),
            'video_engine' => array(
                'key' => 'video_engine',
                'name' => 'FFmpeg & Video Composer',
                'purpose' => 'Video assembly & Media QC validation',
                'type' => 'ffmpeg',
                'status' => self::PROV_AVAILABLE,
                'status_label' => 'Available (Local FFmpeg)',
                'last_success_at' => null,
                'last_error' => null
            ),
            'tiktok_api' => array(
                'key' => 'tiktok_api',
                'name' => 'TikTok Open API',
                'purpose' => 'Automated Video Dispatch',
                'type' => 'social_publish',
                'status' => self::PROV_NOT_CONFIGURED,
                'status_label' => 'Not Configured (Manual Publishing Available)',
                'last_success_at' => null,
                'last_error' => null
            ),
            'affiliate_source' => array(
                'key' => 'affiliate_source',
                'name' => 'Affiliate CSV Reconciler',
                'purpose' => 'Shopee / TikTok Shop Order Import',
                'type' => 'csv_reconciler',
                'status' => self::PROV_AVAILABLE,
                'status_label' => 'Available (Manual CSV Import)',
                'last_success_at' => null,
                'last_error' => null
            )
        );

        // Check last actual requests/successes from DB
        if ($this->d) {
            // Research last success
            $lastRes = $this->d->rawQueryOne("SELECT finished_at FROM table_product_research_job WHERE status = 'SUCCESS' ORDER BY id DESC LIMIT 1");
            if (!empty($lastRes['finished_at'])) $providers['ai_research']['last_success_at'] = (int)$lastRes['finished_at'];

            // Content last success
            $lastCont = $this->d->rawQueryOne("SELECT completed_at FROM table_ai_content_job WHERE status = 'SUCCESS' ORDER BY id DESC LIMIT 1");
            if (!empty($lastCont['completed_at'])) $providers['llm_engine']['last_success_at'] = (int)$lastCont['completed_at'];

            // Video last success
            $lastVid = $this->d->rawQueryOne("SELECT date_updated FROM table_ai_video WHERE status = 'APPROVED' OR status = 'RENDERED' ORDER BY id DESC LIMIT 1");
            if (!empty($lastVid['date_updated'])) {
                $providers['video_engine']['last_success_at'] = (int)$lastVid['date_updated'];
                $providers['voice_tts']['last_success_at'] = (int)$lastVid['date_updated'];
            }

            // Affiliate last import
            $lastImp = $this->d->rawQueryOne("SELECT date_created FROM table_conversion_import_log ORDER BY id DESC LIMIT 1");
            if (!empty($lastImp['date_created'])) $providers['affiliate_source']['last_success_at'] = (int)$lastImp['date_created'];
        }

        return $providers;
    }

    // =========================================================================
    // 4. COST CENTER & BUDGET GUARDS
    // =========================================================================

    /**
     * Get Actual & Estimated API Costs across Dimensions
     * @param string $range 'today', '7days', '30days', 'all'
     * @return array
     */
    public function getCostSummary($range = 'today') {
        $now = time();
        $startTime = 0;

        switch ($range) {
            case 'today':
                $startTime = strtotime('today midnight');
                break;
            case '7days':
                $startTime = $now - (7 * 86400);
                break;
            case '30days':
                $startTime = $now - (30 * 86400);
                break;
            default:
                $startTime = 0;
                break;
        }

        $res = array(
            'range' => $range,
            'start_time' => $startTime,
            'actual_cost_vnd' => 0.0,
            'estimated_cost_vnd' => 0.0,
            'breakdown' => array(
                'tts_cost' => 0.0,
                'ai_video_cost' => 0.0,
                'local_render_cost' => 0.0,
                'other_cost' => 0.0
            ),
            'budget' => array(
                'daily_limit' => (float)$this->getSetting('daily_external_api_budget', 200000),
                'monthly_limit' => (float)$this->getSetting('monthly_external_api_budget', 3000000),
                'spent_today' => 0.0,
                'spent_month' => 0.0,
                'daily_level' => 'NORMAL',
                'monthly_level' => 'NORMAL',
                'is_exceeded' => false
            )
        );

        if (!$this->d) return $res;

        // Query Video table costs
        $whereSql = ($startTime > 0) ? "WHERE date_created >= {$startTime}" : "";
        $costs = $this->d->rawQueryOne(
            "SELECT 
                SUM(total_external_api_cost) as total_actual,
                SUM(cost_estimate) as total_estimated,
                SUM(tts_cost) as sum_tts,
                SUM(ai_video_cost) as sum_ai_vid,
                SUM(local_render_cost) as sum_local
             FROM table_ai_video {$whereSql}"
        );

        if (!empty($costs)) {
            $res['actual_cost_vnd'] = (float)($costs['total_actual'] ?? 0);
            $res['estimated_cost_vnd'] = (float)($costs['total_estimated'] ?? 0);
            $res['breakdown']['tts_cost'] = (float)($costs['sum_tts'] ?? 0);
            $res['breakdown']['ai_video_cost'] = (float)($costs['sum_ai_vid'] ?? 0);
            $res['breakdown']['local_render_cost'] = (float)($costs['sum_local'] ?? 0);
        }

        // Budget Calculations (Today & Month)
        $todayStart = strtotime('today midnight');
        $monthStart = strtotime('first day of this month midnight');

        $todaySpend = $this->d->rawQueryOne("SELECT SUM(total_external_api_cost) as s FROM table_ai_video WHERE date_created >= ?", array($todayStart));
        $res['budget']['spent_today'] = (float)($todaySpend['s'] ?? 0);

        $monthSpend = $this->d->rawQueryOne("SELECT SUM(total_external_api_cost) as s FROM table_ai_video WHERE date_created >= ?", array($monthStart));
        $res['budget']['spent_month'] = (float)($monthSpend['s'] ?? 0);

        // Daily level
        $dailyRatio = $res['budget']['daily_limit'] > 0 ? ($res['budget']['spent_today'] / $res['budget']['daily_limit']) : 0;
        if ($dailyRatio >= 1.0) $res['budget']['daily_level'] = 'LIMIT_REACHED';
        elseif ($dailyRatio >= 0.8) $res['budget']['daily_level'] = 'WARNING';
        else $res['budget']['daily_level'] = 'NORMAL';

        // Monthly level
        $monthRatio = $res['budget']['monthly_limit'] > 0 ? ($res['budget']['spent_month'] / $res['budget']['monthly_limit']) : 0;
        if ($monthRatio >= 1.0) $res['budget']['monthly_level'] = 'LIMIT_REACHED';
        elseif ($monthRatio >= 0.8) $res['budget']['monthly_level'] = 'WARNING';
        else $res['budget']['monthly_level'] = 'NORMAL';

        $res['budget']['is_exceeded'] = ($res['budget']['daily_level'] === 'LIMIT_REACHED' || $res['budget']['monthly_level'] === 'LIMIT_REACHED');

        return $res;
    }

    // =========================================================================
    // 5. AUTOMATION SWITCHES & EMERGENCY STOP
    // =========================================================================

    /**
     * Check if Automation is Enabled
     * @param string|null $module
     * @return bool
     */
    public function isAutomationEnabled($module = null) {
        $master = (int)$this->getSetting('automation_enabled', 1);
        if ($master === 0) return false;

        if ($module) {
            $key = strtolower($module) . '_automation_enabled';
            return (bool)(int)$this->getSetting($key, 1);
        }

        return true;
    }

    /**
     * Check if Paid Automation is Paused
     * @return bool
     */
    public function isPaidAutomationPaused() {
        return (int)$this->getSetting('pause_paid_automation', 0) === 1;
    }

    /**
     * Check if Paid Action is Allowed (Respecting Budget & Emergency Stop)
     * @param float $costVnd
     * @param bool $isAdminOverride
     * @return array ['allowed' => bool, 'reason' => string]
     */
    public function canExecutePaidAction($costVnd = 0.0, $isAdminOverride = false) {
        if ($costVnd <= 0) {
            return array('allowed' => true, 'reason' => 'Free/Local Operation');
        }

        if (!$this->isAutomationEnabled()) {
            return array('allowed' => false, 'reason' => 'Global Automation is OFF');
        }

        if ($this->isPaidAutomationPaused() && !$isAdminOverride) {
            return array('allowed' => false, 'reason' => 'Emergency Stop: Paid Automation is PAUSED');
        }

        $costSummary = $this->getCostSummary('today');
        if ($costSummary['budget']['is_exceeded'] && !$isAdminOverride) {
            return array('allowed' => false, 'reason' => 'Budget Limit Reached (' . number_format($costSummary['budget']['spent_today']) . ' / ' . number_format($costSummary['budget']['daily_limit']) . ' VND)');
        }

        return array('allowed' => true, 'reason' => 'Budget within threshold');
    }

    /**
     * Toggle Automation Switch
     * @param string $module
     * @param bool $enabled
     * @param string $adminUser
     * @return bool
     */
    public function toggleAutomation($module, $enabled, $adminUser = 'admin') {
        $key = ($module === 'all' || $module === 'master') ? 'automation_enabled' : strtolower($module) . '_automation_enabled';
        $val = $enabled ? 1 : 0;
        $saved = $this->saveSetting($key, $val, 'operations');

        if ($saved && $this->d) {
            $this->d->insert('operations_override_log', array(
                'admin_user' => $adminUser,
                'action_type' => 'SETTINGS_CHANGE',
                'reason' => "Toggled automation switch '{$key}' to " . ($enabled ? 'ON' : 'OFF'),
                'amount_context' => $val,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'date_created' => time()
            ));
        }

        return $saved;
    }

    /**
     * Toggle Emergency Pause on Paid Automation
     * @param bool $pause
     * @param string $reason
     * @param string $adminUser
     * @return bool
     */
    public function toggleEmergencyPausePaid($pause, $reason, $adminUser = 'admin') {
        $val = $pause ? 1 : 0;
        $saved = $this->saveSetting('pause_paid_automation', $val, 'operations');

        if ($saved && $this->d) {
            $this->d->insert('operations_override_log', array(
                'admin_user' => $adminUser,
                'action_type' => $pause ? 'EMERGENCY_PAUSE' : 'EMERGENCY_RESUME',
                'reason' => $reason ?: ($pause ? 'Emergency pause on all external paid APIs' : 'Resumed paid automation'),
                'amount_context' => $val,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'date_created' => time()
            ));
        }

        return $saved;
    }

    /**
     * Record Admin Budget Override
     * @param float $amount
     * @param string $reason
     * @param string $adminUser
     * @return bool
     */
    public function overrideBudget($amount, $reason, $adminUser = 'admin') {
        if (!$this->d) return false;

        return (bool)$this->d->insert('operations_override_log', array(
            'admin_user' => $adminUser,
            'action_type' => 'BUDGET_OVERRIDE',
            'reason' => $reason,
            'amount_context' => (float)$amount,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'date_created' => time()
        ));
    }

    // =========================================================================
    // 6. HUMAN ACTION QUEUE ("CẦN BẠN XỬ LÝ")
    // =========================================================================

    /**
     * Aggregate Human Action Queue from all 8 business dimensions
     * @return array
     */
    public function getHumanActionQueue() {
        if (!$this->d) return array('items' => array(), 'total' => 0, 'by_priority' => array());

        $actions = array();

        // 1. Research Candidates awaiting approval
        $candidates = $this->d->rawQuery("SELECT id, name, total_score, platform, date_created FROM table_product_research WHERE status IN ('RESEARCHED', 'DISCOVERED') ORDER BY total_score DESC LIMIT 10");
        if (!empty($candidates)) {
            foreach ($candidates as $c) {
                $actions[] = array(
                    'category' => 'RESEARCH',
                    'title' => 'Ứng viên tiềm năng chờ duyệt: ' . $c['name'],
                    'priority' => 'WAITING_APPROVAL',
                    'priority_rank' => 3,
                    'reference' => 'Research #' . $c['id'],
                    'date' => (int)$c['date_created'],
                    'action_label' => 'Xem & Duyệt',
                    'url' => 'index.php?com=product_research&act=edit&id=' . $c['id']
                );
            }
        }

        // 2. AI Content awaiting approval
        $contents = $this->d->rawQuery("SELECT id, id_product, title, status, date_created FROM table_ai_content WHERE status IN ('REVIEW_REQUIRED', 'DRAFT') ORDER BY id DESC LIMIT 10");
        if (!empty($contents)) {
            foreach ($contents as $ct) {
                $actions[] = array(
                    'category' => 'CONTENT',
                    'title' => 'Gói nội dung AI chờ kiểm duyệt: ' . ($ct['title'] ?: 'Content #' . $ct['id']),
                    'priority' => 'WAITING_APPROVAL',
                    'priority_rank' => 3,
                    'reference' => 'Content #' . $ct['id'],
                    'date' => (int)$ct['date_created'],
                    'action_label' => 'Kiểm duyệt',
                    'url' => 'index.php?com=ai_content&act=edit&id=' . $ct['id']
                );
            }
        }

        // 3. AI Video awaiting review
        $videos = $this->d->rawQuery("SELECT id, id_product, title, mode, date_created FROM table_ai_video WHERE status IN ('REVIEW_REQUIRED', 'RENDERED') ORDER BY id DESC LIMIT 10");
        if (!empty($videos)) {
            foreach ($videos as $v) {
                $actions[] = array(
                    'category' => 'VIDEO',
                    'title' => 'Video thành phẩm chờ nghiệm thu: ' . ($v['title'] ?: 'Video #' . $v['id']),
                    'priority' => 'WAITING_APPROVAL',
                    'priority_rank' => 3,
                    'reference' => 'Video #' . $v['id'],
                    'date' => (int)$v['date_created'],
                    'action_label' => 'Nghiệm thu Video',
                    'url' => 'index.php?com=ai_video&act=edit&id=' . $v['id']
                );
            }
        }

        // 4. Publishing Posts awaiting manual publishing
        $posts = $this->d->rawQuery("SELECT id, title, platform, provider, scheduled_at, date_created FROM table_publish_post WHERE status = 'READY' AND provider = 'manual' ORDER BY id DESC LIMIT 10");
        if (!empty($posts)) {
            foreach ($posts as $p) {
                $actions[] = array(
                    'category' => 'PUBLISHING',
                    'title' => 'Gói bài đăng sẵn sàng xuất bản thủ công: ' . $p['title'],
                    'priority' => 'NORMAL',
                    'priority_rank' => 4,
                    'reference' => 'Post #' . $p['id'],
                    'date' => (int)$p['date_created'],
                    'action_label' => 'Mở Gói & Đăng',
                    'url' => 'index.php?com=publishing&act=edit_post&id=' . $p['id']
                );
            }
        }

        // 5. Optimization Recommendations awaiting approval
        $recs = $this->d->rawQuery("SELECT id, id_product, recommendation_type, estimated_cost_vnd, date_created FROM table_optimization_recommendation WHERE status = 'PENDING' ORDER BY id DESC LIMIT 10");
        if (!empty($recs)) {
            foreach ($recs as $r) {
                $actions[] = array(
                    'category' => 'OPTIMIZATION',
                    'title' => 'Khuyến nghị tối ưu hóa A/B chờ phê duyệt: ' . $r['recommendation_type'],
                    'priority' => ((float)$r['estimated_cost_vnd'] > 0) ? 'COST_BLOCKED' : 'WAITING_APPROVAL',
                    'priority_rank' => ((float)$r['estimated_cost_vnd'] > 0) ? 2 : 3,
                    'reference' => 'Rec #' . $r['id'],
                    'date' => (int)$r['date_created'],
                    'action_label' => 'Phê duyệt Thử nghiệm',
                    'url' => 'index.php?com=optimization&act=recommendation_detail&id=' . $r['id']
                );
            }
        }

        // 6. Experiments awaiting conclusion / review
        $exps = $this->d->rawQuery("SELECT id, experiment_code, changed_variable, status, date_created FROM table_optimization_experiment WHERE status IN ('RUNNING', 'ENOUGH_DATA') ORDER BY id DESC LIMIT 10");
        if (!empty($exps)) {
            foreach ($exps as $e) {
                $actions[] = array(
                    'category' => 'EXPERIMENT',
                    'title' => 'Thử nghiệm A/B đang chạy cần theo dõi: ' . $e['experiment_code'],
                    'priority' => 'NORMAL',
                    'priority_rank' => 4,
                    'reference' => 'Exp #' . $e['id'],
                    'date' => (int)$e['date_created'],
                    'action_label' => 'Xem Đối chứng',
                    'url' => 'index.php?com=optimization&act=experiment_detail&id=' . $e['id']
                );
            }
        }

        // 7. Unattributed Conversions
        $unmatched = $this->d->rawQuery("SELECT id, external_conversion_id, platform, order_value, commission_value, conversion_at FROM table_affiliate_conversion WHERE id_product IS NULL ORDER BY id DESC LIMIT 10");
        if (!empty($unmatched)) {
            foreach ($unmatched as $u) {
                $orderCode = !empty($u['external_conversion_id']) ? $u['external_conversion_id'] : $u['id'];
                $actions[] = array(
                    'category' => 'AFFILIATE',
                    'title' => 'Đơn hàng ' . strtoupper($u['platform']) . ' #' . $orderCode . ' chưa rõ nguồn tracking',
                    'priority' => 'WARNING',
                    'priority_rank' => 2,
                    'reference' => 'Conv #' . $u['id'],
                    'date' => (int)$u['conversion_at'],
                    'action_label' => 'Gán nguồn thủ công',
                    'url' => 'index.php?com=analytics&act=conversions'
                );
            }
        }

        // 8. Critical / Failed Jobs
        $failedJobs = $this->d->rawQuery("SELECT id, error_message, date_created FROM table_ai_video_job WHERE status = 'FAILED' ORDER BY id DESC LIMIT 5");
        if (!empty($failedJobs)) {
            foreach ($failedJobs as $fj) {
                $actions[] = array(
                    'category' => 'JOB_FAILED',
                    'title' => 'Tác vụ Video Render Job #' . $fj['id'] . ' bị lỗi thất bại',
                    'priority' => 'CRITICAL',
                    'priority_rank' => 1,
                    'reference' => 'Job #' . $fj['id'],
                    'date' => (int)$fj['date_created'],
                    'action_label' => 'Xem & Retry',
                    'url' => 'index.php?com=operations&act=jobs&module=video&status=failed'
                );
            }
        }

        // Sort by priority rank (1 = Critical, 2 = Cost Blocked/Warning, 3 = Approval, 4 = Normal)
        usort($actions, function($a, $b) {
            if ($a['priority_rank'] === $b['priority_rank']) {
                return $b['date'] - $a['date'];
            }
            return $a['priority_rank'] - $b['priority_rank'];
        });

        $byPriority = array('CRITICAL' => 0, 'COST_BLOCKED' => 0, 'WAITING_APPROVAL' => 0, 'NORMAL' => 0);
        foreach ($actions as $act) {
            $p = $act['priority'];
            if (isset($byPriority[$p])) $byPriority[$p]++;
        }

        return array(
            'items' => $actions,
            'total' => count($actions),
            'by_priority' => $byPriority
        );
    }

    // =========================================================================
    // 7. DATA FRESHNESS TRACKER
    // =========================================================================

    /**
     * Get Data Freshness Metrics across Pipeline Stages
     * @return array
     */
    public function getDataFreshness() {
        $now = time();
        $freshness = array(
            'tracking_events' => array(
                'label' => 'Sự kiện Tracking (Page/Product View)',
                'last_event_at' => null,
                'status' => self::STATUS_UNKNOWN,
                'status_label' => 'Chưa có sự kiện'
            ),
            'affiliate_clicks' => array(
                'label' => 'Click chuyển hướng Affiliate',
                'last_event_at' => null,
                'status' => self::STATUS_UNKNOWN,
                'status_label' => 'Chưa có click'
            ),
            'conversion_orders' => array(
                'label' => 'Đối soát Đơn hàng & Hoa hồng',
                'last_event_at' => null,
                'status' => self::STATUS_UNKNOWN,
                'status_label' => 'Chưa kết nối nguồn đơn'
            ),
            'winner_evaluations' => array(
                'label' => 'Đánh giá Hiệu năng Winner',
                'last_event_at' => null,
                'status' => self::STATUS_UNKNOWN,
                'status_label' => 'Chưa đánh giá'
            ),
            'optimization_recommendations' => array(
                'label' => 'Khuyến nghị Tối ưu hóa A/B',
                'last_event_at' => null,
                'status' => self::STATUS_UNKNOWN,
                'status_label' => 'Chưa sinh đề xuất'
            )
        );

        if (!$this->d) return $freshness;

        // 1. Tracking
        $lastTrack = $this->d->rawQueryOne("SELECT event_time FROM table_analytics_event ORDER BY id DESC LIMIT 1");
        if (!empty($lastTrack['event_time'])) {
            $t = (int)$lastTrack['event_time'];
            $freshness['tracking_events']['last_event_at'] = $t;
            $ageHours = ($now - $t) / 3600;
            $threshold = (int)$this->getSetting('tracking_stale_threshold_hours', 24);
            $freshness['tracking_events']['status'] = ($ageHours <= $threshold) ? self::STATUS_HEALTHY : self::STATUS_WARNING;
            $freshness['tracking_events']['status_label'] = ($ageHours <= $threshold) ? 'Tươi mới (' . round($ageHours, 1) . 'h trước)' : 'Chậm trễ (' . round($ageHours, 1) . 'h trước)';
        }

        // 2. Affiliate Click
        $lastClick = $this->d->rawQueryOne("SELECT date_created FROM table_affiliate_click ORDER BY id DESC LIMIT 1");
        if (!empty($lastClick['date_created'])) {
            $t = (int)$lastClick['date_created'];
            $freshness['affiliate_clicks']['last_event_at'] = $t;
            $ageHours = ($now - $t) / 3600;
            $threshold = (int)$this->getSetting('affiliate_click_stale_threshold_hours', 48);
            $freshness['affiliate_clicks']['status'] = ($ageHours <= $threshold) ? self::STATUS_HEALTHY : self::STATUS_WARNING;
            $freshness['affiliate_clicks']['status_label'] = ($ageHours <= $threshold) ? 'Tươi mới (' . round($ageHours, 1) . 'h trước)' : 'Chậm trễ (' . round($ageHours, 1) . 'h trước)';
        }

        // 3. Conversions (Check if connected)
        $hasConversions = $this->d->rawQueryOne("SELECT conversion_at FROM table_affiliate_conversion ORDER BY id DESC LIMIT 1");
        if (!empty($hasConversions['conversion_at'])) {
            $t = (int)$hasConversions['conversion_at'];
            $freshness['conversion_orders']['last_event_at'] = $t;
            $ageDays = ($now - $t) / 86400;
            $threshold = (int)$this->getSetting('conversion_stale_threshold_days', 7);
            $freshness['conversion_orders']['status'] = ($ageDays <= $threshold) ? self::STATUS_HEALTHY : self::STATUS_WARNING;
            $freshness['conversion_orders']['status_label'] = ($ageDays <= $threshold) ? 'Đã đối soát (' . round($ageDays, 1) . ' ngày trước)' : 'Cần nhập file mới (' . round($ageDays, 1) . ' ngày trước)';
        } else {
            $freshness['conversion_orders']['status'] = self::PROV_NOT_CONFIGURED;
            $freshness['conversion_orders']['status_label'] = 'Chưa kết nối sàn / Chưa có file CSV';
        }

        // 4. Winner Evaluations
        $lastWin = $this->d->rawQueryOne("SELECT evaluated_at FROM table_winner_evaluation ORDER BY id DESC LIMIT 1");
        if (!empty($lastWin['evaluated_at'])) {
            $t = (int)$lastWin['evaluated_at'];
            $freshness['winner_evaluations']['last_event_at'] = $t;
            $ageHours = ($now - $t) / 3600;
            $freshness['winner_evaluations']['status'] = ($ageHours <= 24) ? self::STATUS_HEALTHY : self::STATUS_WARNING;
            $freshness['winner_evaluations']['status_label'] = 'Đã đánh giá (' . round($ageHours, 1) . 'h trước)';
        }

        // 5. Optimization Recs
        $lastRec = $this->d->rawQueryOne("SELECT date_created FROM table_optimization_recommendation ORDER BY id DESC LIMIT 1");
        if (!empty($lastRec['date_created'])) {
            $t = (int)$lastRec['date_created'];
            $freshness['optimization_recommendations']['last_event_at'] = $t;
            $ageHours = ($now - $t) / 3600;
            $freshness['optimization_recommendations']['status'] = ($ageHours <= 48) ? self::STATUS_HEALTHY : self::STATUS_WARNING;
            $freshness['optimization_recommendations']['status_label'] = 'Đã cập nhật (' . round($ageHours, 1) . 'h trước)';
        }

        return $freshness;
    }

    // =========================================================================
    // 8. ENVIRONMENT & DIAGNOSTICS
    // =========================================================================

    /**
     * Get Environment Diagnostics
     * @return array
     */
    public function getEnvironmentHealth() {
        global $config;

        $phpVersion = PHP_VERSION;
        $phpSapi = php_sapi_name();
        $os = PHP_OS;
        $timezone = date_default_timezone_get();

        // Database health ping
        $dbStatus = self::STATUS_FAILED;
        $dbVersion = 'Unknown';
        if ($this->d) {
            try {
                $ver = $this->d->rawQueryOne("SELECT VERSION() as v");
                if (!empty($ver['v'])) {
                    $dbStatus = self::STATUS_HEALTHY;
                    $dbVersion = $ver['v'];
                }
            } catch (Exception $e) {
                $dbStatus = self::STATUS_FAILED;
            }
        }

        // FFmpeg & FFprobe executable checks
        $ffmpegPath = $config['video_composer']['ffmpeg_path'] ?? 'ffmpeg';
        $ffprobePath = $config['video_composer']['ffprobe_path'] ?? 'ffprobe';

        $ffmpegAvail = file_exists($ffmpegPath) || (stripos($os, 'WIN') === false && !empty(shell_exec('which ffmpeg 2>/dev/null')));
        $ffprobeAvail = file_exists($ffprobePath) || (stripos($os, 'WIN') === false && !empty(shell_exec('which ffprobe 2>/dev/null')));

        // Free Disk space if available
        $diskFree = function_exists('disk_free_space') ? @disk_free_space(__DIR__) : false;
        $diskTotal = function_exists('disk_total_space') ? @disk_total_space(__DIR__) : false;

        return array(
            'php' => array(
                'version' => $phpVersion,
                'sapi' => $phpSapi,
                'target_compat' => 'PHP 7.4 (Production Compatible)',
                'actual_runtime' => $phpVersion . ' (' . $phpSapi . ')',
                'status' => self::STATUS_HEALTHY
            ),
            'database' => array(
                'type' => 'MySQL / PDO',
                'version' => $dbVersion,
                'status' => $dbStatus
            ),
            'ffmpeg' => array(
                'available' => $ffmpegAvail,
                'path' => $ffmpegPath,
                'status' => $ffmpegAvail ? self::STATUS_HEALTHY : self::STATUS_WARNING
            ),
            'ffprobe' => array(
                'available' => $ffprobeAvail,
                'path' => $ffprobePath,
                'status' => $ffprobeAvail ? self::STATUS_HEALTHY : self::STATUS_WARNING
            ),
            'timezone' => $timezone,
            'disk' => array(
                'free_bytes' => $diskFree,
                'total_bytes' => $diskTotal,
                'free_gb' => ($diskFree !== false) ? round($diskFree / (1024 * 1024 * 1024), 2) : 'N/A'
            )
        );
    }

    // =========================================================================
    // 9. SECRET SANITIZER & ALERT CENTER
    // =========================================================================

    /**
     * Recursively Sanitize Sensitive Keys and Bearer Tokens
     * @param mixed $data
     * @return mixed
     */
    public function sanitizeSecrets($data) {
        if (is_null($data) || is_bool($data) || is_numeric($data)) {
            return $data;
        }

        if (is_string($data)) {
            // Mask Bearer tokens
            $data = preg_replace('/Bearer\s+([A-Za-z0-9_\-\.]{8,})/i', 'Bearer [MASKED_TOKEN_***]', $data);
            // Mask api_key=..., secret=...
            $data = preg_replace('/(api_key|apikey|secret|secretkey|access_token|refresh_token|password)=([^&\s]+)/i', '$1=[MASKED_SECRET_***]', $data);
            // Mask sk-bee-... or sk-...
            $data = preg_replace('/sk-[A-Za-z0-9_\-]{16,}/i', 'sk-[MASKED_KEY_***]', $data);
            return $data;
        }

        if (is_array($data)) {
            $sanitized = array();
            $sensitiveKeys = array('password', 'secret', 'api_key', 'apikey', 'secretkey', 'access_token', 'refresh_token', 'token', 'auth_data', 'auth_status_token');
            foreach ($data as $k => $v) {
                if (in_array(strtolower($k), $sensitiveKeys)) {
                    $sanitized[$k] = '[MASKED_SECRET_***]';
                } else {
                    $sanitized[$k] = $this->sanitizeSecrets($v);
                }
            }
            return $sanitized;
        }

        return $data;
    }

    /**
     * Record Alert with Fingerprint Deduplication
     * @param string $module
     * @param string $severity
     * @param string $title
     * @param string $message
     * @param string|null $reference
     * @param array $metadata
     * @return bool
     */
    public function recordAlert($module, $severity, $title, $message, $reference = null, $metadata = array()) {
        if (!$this->d) return false;
        $now = time();

        $cleanTitle = $this->sanitizeSecrets($title);
        $cleanMsg = $this->sanitizeSecrets($message);
        $alertKey = md5($module . '|' . $severity . '|' . $cleanTitle . '|' . ($reference ?: ''));

        $exists = $this->d->rawQueryOne("SELECT id, occurrences FROM table_system_alert WHERE alert_key = ? AND status = 'ACTIVE' LIMIT 1", array($alertKey));

        if (!empty($exists)) {
            $this->d->where('id', $exists['id']);
            return (bool)$this->d->update('system_alert', array(
                'occurrences' => (int)$exists['occurrences'] + 1,
                'last_seen_at' => $now,
                'date_updated' => $now
            ));
        } else {
            return (bool)$this->d->insert('system_alert', array(
                'alert_key' => $alertKey,
                'severity' => in_array($severity, array(self::SEV_INFO, self::SEV_WARNING, self::SEV_ERROR, self::SEV_CRITICAL)) ? $severity : self::SEV_WARNING,
                'module' => $module,
                'title' => $cleanTitle,
                'message' => $cleanMsg,
                'reference' => $reference,
                'status' => 'ACTIVE',
                'first_seen_at' => $now,
                'last_seen_at' => $now,
                'occurrences' => 1,
                'metadata' => !empty($metadata) ? json_encode($this->sanitizeSecrets($metadata)) : null,
                'date_created' => $now
            ));
        }
    }

    /**
     * Get Alerts
     * @param string $status 'ACTIVE', 'ACKNOWLEDGED', 'RESOLVED', 'ALL'
     * @param int $limit
     * @return array
     */
    public function getAlerts($status = 'ACTIVE', $limit = 50) {
        if (!$this->d) return array();

        $where = array();
        $params = array();
        if ($status !== 'ALL') {
            $where[] = "status = ?";
            $params[] = $status;
        }
        $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        return $this->d->rawQuery("SELECT * FROM table_system_alert {$whereSql} ORDER BY last_seen_at DESC LIMIT ?", array_merge($params, array($limit)));
    }

    /**
     * Acknowledge Alert
     * @param int $alertId
     * @param string $adminUser
     * @return bool
     */
    public function acknowledgeAlert($alertId, $adminUser = 'admin') {
        if (!$this->d || empty($alertId)) return false;

        $this->d->where('id', (int)$alertId);
        return (bool)$this->d->update('system_alert', array(
            'status' => 'ACKNOWLEDGED',
            'acknowledged_by' => $adminUser,
            'acknowledged_at' => time(),
            'date_updated' => time()
        ));
    }

    /**
     * Resolve Alert
     * @param int $alertId
     * @return bool
     */
    public function resolveAlert($alertId) {
        if (!$this->d || empty($alertId)) return false;

        $this->d->where('id', (int)$alertId);
        return (bool)$this->d->update('system_alert', array(
            'status' => 'RESOLVED',
            'resolved_at' => time(),
            'date_updated' => time()
        ));
    }

    // =========================================================================
    // 10. SAFE RETRY & JOB LIFECYCLE CONTROLS
    // =========================================================================

    /**
     * Safe Retry of a FAILED Job
     * @param string $module
     * @param int $jobId
     * @param string $adminUser
     * @return array ['success' => bool, 'message' => string]
     */
    public function retryJob($module, $jobId, $adminUser = 'admin') {
        if (!$this->d || empty($jobId)) {
            return array('success' => false, 'message' => 'Invalid parameters');
        }

        $now = time();

        switch ($module) {
            case 'research':
                $job = $this->d->rawQueryOne("SELECT id, status FROM table_product_research_job WHERE id = ? LIMIT 1", array($jobId));
                if (!$job) return array('success' => false, 'message' => 'Job not found');
                if ($job['status'] !== 'FAILED' && $job['status'] !== 'CANCELLED') {
                    return array('success' => false, 'message' => "Cannot retry job in status '{$job['status']}'");
                }
                $this->d->where('id', $jobId);
                $this->d->update('product_research_job', array('status' => 'PENDING', 'error_message' => null, 'date_updated' => $now));
                break;

            case 'content':
                $job = $this->d->rawQueryOne("SELECT id, status FROM table_ai_content_job WHERE id = ? LIMIT 1", array($jobId));
                if (!$job) return array('success' => false, 'message' => 'Job not found');
                if ($job['status'] !== 'FAILED' && $job['status'] !== 'CANCELLED') {
                    return array('success' => false, 'message' => "Cannot retry job in status '{$job['status']}'");
                }
                $this->d->where('id', $jobId);
                $this->d->update('ai_content_job', array('status' => 'PENDING', 'error_message' => null, 'date_updated' => $now));
                break;

            case 'video':
                $job = $this->d->rawQueryOne("SELECT id, id_video, status FROM table_ai_video_job WHERE id = ? LIMIT 1", array($jobId));
                if (!$job) return array('success' => false, 'message' => 'Job not found');
                if ($job['status'] !== 'FAILED' && $job['status'] !== 'CANCELLED') {
                    return array('success' => false, 'message' => "Cannot retry job in status '{$job['status']}'");
                }
                $this->d->where('id', $jobId);
                $this->d->update('ai_video_job', array('status' => 'PENDING', 'error_message' => null, 'date_updated' => $now));
                if (!empty($job['id_video'])) {
                    $this->d->where('id', $job['id_video']);
                    $this->d->update('ai_video', array('status' => 'QUEUED', 'date_updated' => $now));
                }
                break;

            case 'publishing':
                $post = $this->d->rawQueryOne("SELECT id, status FROM table_publish_post WHERE id = ? LIMIT 1", array($jobId));
                if (!$post) return array('success' => false, 'message' => 'Post not found');
                if ($post['status'] === 'PUBLISHED') {
                    return array('success' => false, 'message' => 'Safety Violation: Cannot retry already PUBLISHED post');
                }
                if ($post['status'] !== 'FAILED' && $post['status'] !== 'CANCELLED') {
                    return array('success' => false, 'message' => "Cannot retry post in status '{$post['status']}'");
                }
                $this->d->where('id', $jobId);
                $this->d->update('publish_post', array('status' => 'READY', 'publish_lock' => null, 'error_message' => null, 'date_updated' => $now));
                break;

            default:
                return array('success' => false, 'message' => 'Unknown module');
        }

        // Log action in override log
        $this->d->insert('operations_override_log', array(
            'admin_user' => $adminUser,
            'action_type' => 'JOB_RETRY',
            'reason' => "Admin retried {$module} job #{$jobId}",
            'amount_context' => (float)$jobId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'date_created' => $now
        ));

        return array('success' => true, 'message' => "Job #{$jobId} successfully re-queued");
    }

    /**
     * Cancel a PENDING Job
     * @param string $module
     * @param int $jobId
     * @param string $adminUser
     * @return array
     */
    public function cancelJob($module, $jobId, $adminUser = 'admin') {
        if (!$this->d || empty($jobId)) {
            return array('success' => false, 'message' => 'Invalid parameters');
        }

        $now = time();

        switch ($module) {
            case 'research':
                $this->d->where('id', $jobId);
                $this->d->update('product_research_job', array('status' => 'CANCELLED', 'date_updated' => $now));
                break;
            case 'content':
                $this->d->where('id', $jobId);
                $this->d->update('ai_content_job', array('status' => 'CANCELLED', 'date_updated' => $now));
                break;
            case 'video':
                $this->d->where('id', $jobId);
                $this->d->update('ai_video_job', array('status' => 'CANCELLED', 'date_updated' => $now));
                break;
            case 'publishing':
                $this->d->where('id', $jobId);
                $this->d->update('publish_post', array('status' => 'CANCELLED', 'date_updated' => $now));
                break;
            default:
                return array('success' => false, 'message' => 'Unknown module');
        }

        $this->d->insert('operations_override_log', array(
            'admin_user' => $adminUser,
            'action_type' => 'JOB_CANCEL',
            'reason' => "Admin cancelled {$module} job #{$jobId}",
            'amount_context' => (float)$jobId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'date_created' => $now
        ));

        return array('success' => true, 'message' => "Job #{$jobId} cancelled");
    }
}
