-- ======================================================================
-- FITNADO PHASE 10: OPERATIONS & AUTOMATION CONTROL CENTER
-- Database Migration Script
-- ======================================================================

-- 1. Create table_system_worker_status for tracking worker heartbeats and health
CREATE TABLE IF NOT EXISTS `table_system_worker_status` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `worker_key` varchar(64) NOT NULL,
  `worker_name` varchar(255) NOT NULL,
  `worker_type` varchar(30) NOT NULL DEFAULT 'worker' COMMENT 'worker, cron, service',
  `last_started_at` int(11) DEFAULT NULL,
  `last_heartbeat_at` int(11) DEFAULT NULL,
  `last_completed_at` int(11) DEFAULT NULL,
  `last_success_at` int(11) DEFAULT NULL,
  `last_error_at` int(11) DEFAULT NULL,
  `last_error` text DEFAULT NULL,
  `hostname` varchar(100) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `metadata` mediumtext DEFAULT NULL COMMENT 'JSON runtime metadata',
  `date_created` int(11) NOT NULL,
  `date_updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_worker_key` (`worker_key`),
  KEY `idx_worker_type` (`worker_type`),
  KEY `idx_last_heartbeat` (`last_heartbeat_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create table_system_alert for centralized incidents & alerts with deduplication
CREATE TABLE IF NOT EXISTS `table_system_alert` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `alert_key` varchar(128) NOT NULL COMMENT 'Unique fingerprint hash for deduplication',
  `severity` varchar(20) NOT NULL DEFAULT 'WARNING' COMMENT 'INFO, WARNING, ERROR, CRITICAL',
  `module` varchar(50) NOT NULL COMMENT 'research, content, video, publishing, analytics, optimization, system, budget, provider',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reference` varchar(255) DEFAULT NULL COMMENT 'job_id, provider_name, tracking_code, etc.',
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE' COMMENT 'ACTIVE, ACKNOWLEDGED, RESOLVED',
  `first_seen_at` int(11) NOT NULL,
  `last_seen_at` int(11) NOT NULL,
  `occurrences` int(11) NOT NULL DEFAULT 1,
  `acknowledged_by` varchar(50) DEFAULT NULL,
  `acknowledged_at` int(11) DEFAULT NULL,
  `resolved_at` int(11) DEFAULT NULL,
  `metadata` mediumtext DEFAULT NULL COMMENT 'JSON extra context',
  `date_created` int(11) NOT NULL,
  `date_updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_alert_key` (`alert_key`),
  KEY `idx_severity` (`severity`),
  KEY `idx_module` (`module`),
  KEY `idx_status` (`status`),
  KEY `idx_last_seen` (`last_seen_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create table_operations_override_log for audit logging of budget and emergency overrides
CREATE TABLE IF NOT EXISTS `table_operations_override_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_user` varchar(50) NOT NULL DEFAULT 'admin',
  `action_type` varchar(50) NOT NULL COMMENT 'BUDGET_OVERRIDE, EMERGENCY_PAUSE, EMERGENCY_RESUME, SETTINGS_CHANGE, JOB_RETRY, JOB_CANCEL',
  `reason` text NOT NULL,
  `amount_context` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ip_address` varchar(64) DEFAULT NULL,
  `metadata` mediumtext DEFAULT NULL COMMENT 'JSON details',
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_action_type` (`action_type`),
  KEY `idx_admin_user` (`admin_user`),
  KEY `idx_date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Seed operational settings into table_analytics_setting
INSERT INTO `table_analytics_setting` (`setting_key`, `setting_value`, `setting_group`, `description`, `date_updated`) VALUES
('automation_enabled', '1', 'operations', 'Master global automation switch (1=ON, 0=OFF)', UNIX_TIMESTAMP()),
('pause_paid_automation', '1', 'operations', 'Emergency stop switch for paid external API calls (1=PAUSED, 0=NORMAL, default PAUSED on deploy)', UNIX_TIMESTAMP()),
('research_automation_enabled', '1', 'operations', 'Research background automation switch (1=ON, 0=OFF)', UNIX_TIMESTAMP()),
('content_automation_enabled', '1', 'operations', 'AI Content background automation switch (1=ON, 0=OFF)', UNIX_TIMESTAMP()),
('video_automation_enabled', '1', 'operations', 'AI Video render background automation switch (1=ON, 0=OFF)', UNIX_TIMESTAMP()),
('publishing_automation_enabled', '1', 'operations', 'Publishing background automation switch (1=ON, 0=OFF)', UNIX_TIMESTAMP()),
('optimization_automation_enabled', '1', 'operations', 'Optimization background evaluation switch (1=ON, 0=OFF)', UNIX_TIMESTAMP()),
('daily_external_api_budget', '200000', 'operations', 'Daily budget limit for external paid APIs in VND', UNIX_TIMESTAMP()),
('monthly_external_api_budget', '3000000', 'operations', 'Monthly budget limit for external paid APIs in VND', UNIX_TIMESTAMP()),
('worker_heartbeat_threshold_seconds', '300', 'operations', 'Seconds before a worker without heartbeat is considered STALE/WARNING (default 5m)', UNIX_TIMESTAMP()),
('cron_missed_threshold_seconds', '3600', 'operations', 'Seconds before a scheduled cron is considered MISSED (default 1h)', UNIX_TIMESTAMP()),
('stuck_job_threshold_seconds', '1800', 'operations', 'Seconds before a running job is flagged as STUCK (default 30m, video 60m)', UNIX_TIMESTAMP()),
('tracking_stale_threshold_hours', '24', 'operations', 'Hours before event tracking is flagged as STALE if no events recorded', UNIX_TIMESTAMP()),
('affiliate_click_stale_threshold_hours', '48', 'operations', 'Hours before affiliate click stream is flagged as STALE', UNIX_TIMESTAMP()),
('conversion_stale_threshold_days', '7', 'operations', 'Days before conversion import is flagged as STALE for connected sources', UNIX_TIMESTAMP()),
('analytics_stale_threshold_hours', '24', 'operations', 'Hours before analytics aggregation is flagged as STALE', UNIX_TIMESTAMP()),
('optimization_stale_threshold_hours', '24', 'operations', 'Hours before optimization evaluation is flagged as STALE', UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE `date_updated` = VALUES(`date_updated`);
