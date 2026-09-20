-- ======================================================================
-- FITNADO PHASE 09: DATA-DRIVEN OPTIMIZATION LOOP
-- Database Migration Script
-- ======================================================================

-- 1. Create table_optimization_recommendation
CREATE TABLE IF NOT EXISTS `table_optimization_recommendation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(10) unsigned NOT NULL,
  `id_post` int(10) unsigned DEFAULT NULL,
  `id_video` int(10) unsigned DEFAULT NULL,
  `id_content` int(10) unsigned DEFAULT NULL,
  `recommendation_type` varchar(50) NOT NULL COMMENT 'KEEP_TESTING, REVIEW_PRODUCT_PAGE, CREATE_NEW_HOOK, CREATE_CONTENT_VARIATION, CREATE_VIDEO_VARIATION, UPGRADE_TO_HYBRID, RETEST_PRODUCT, PAUSE_TESTING, WAIT_FOR_MORE_DATA',
  `reason_code` varchar(50) NOT NULL COMMENT 'INSUFFICIENT_SAMPLE, HIGH_TRAFFIC_LOW_CLICK, HIGH_CLICK_NO_CONVERSION, STRONG_CONVERSION_SIGNAL, POSITIVE_ROI, LOW_CTR_POST, TRACKING_DEGRADED',
  `reason_summary` text NOT NULL,
  `hypothesis` text NOT NULL,
  `proposed_variable` varchar(50) NOT NULL DEFAULT 'HOOK' COMMENT 'HOOK, CTA, SCRIPT, VIDEO_STYLE, VOICE, OFFER, MULTIVARIATE',
  `target_mode` varchar(20) NOT NULL DEFAULT 'ECONOMY' COMMENT 'ECONOMY, HYBRID, PREMIUM',
  `estimated_cost_vnd` decimal(15,2) NOT NULL DEFAULT 0.00,
  `metrics_snapshot` mediumtext NOT NULL,
  `rules_snapshot` mediumtext NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'PENDING' COMMENT 'PENDING, APPROVED, REJECTED, STALE, EXECUTING, COMPLETED, CANCELLED',
  `review_notes` text DEFAULT NULL,
  `approved_by` varchar(50) DEFAULT NULL,
  `approved_at` int(11) DEFAULT NULL,
  `id_experiment` bigint(20) unsigned DEFAULT NULL,
  `date_created` int(11) NOT NULL,
  `date_updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rec_product` (`id_product`),
  KEY `idx_rec_status` (`status`),
  KEY `idx_rec_type` (`recommendation_type`),
  KEY `idx_rec_date` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create table_optimization_experiment
CREATE TABLE IF NOT EXISTS `table_optimization_experiment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `experiment_code` varchar(64) NOT NULL,
  `id_recommendation` bigint(20) unsigned DEFAULT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `changed_variable` varchar(50) NOT NULL DEFAULT 'HOOK' COMMENT 'HOOK, CTA, SCRIPT, VIDEO_STYLE, VOICE, OFFER, MULTIVARIATE',
  `hypothesis` text NOT NULL,
  `baseline_type` varchar(30) NOT NULL DEFAULT 'POST',
  `id_baseline_post` int(10) unsigned DEFAULT NULL,
  `id_baseline_video` int(10) unsigned DEFAULT NULL,
  `id_baseline_content` int(10) unsigned DEFAULT NULL,
  `id_variation_content` int(10) unsigned DEFAULT NULL,
  `id_variation_video` int(10) unsigned DEFAULT NULL,
  `id_variation_post` int(10) unsigned DEFAULT NULL,
  `target_mode` varchar(20) NOT NULL DEFAULT 'ECONOMY' COMMENT 'ECONOMY, HYBRID, PREMIUM',
  `status` varchar(30) NOT NULL DEFAULT 'APPROVED' COMMENT 'DRAFT, APPROVED, RUNNING, ENOUGH_DATA, COMPLETED, CANCELLED',
  `baseline_metrics_snapshot` mediumtext NOT NULL,
  `variation_metrics_snapshot` mediumtext DEFAULT NULL,
  `result_conclusion` varchar(50) DEFAULT NULL COMMENT 'INSUFFICIENT_DATA, BASELINE_BETTER, VARIATION_BETTER, NO_MEANINGFUL_DIFFERENCE',
  `result_summary` text DEFAULT NULL,
  `total_cost_vnd` decimal(15,2) NOT NULL DEFAULT 0.00,
  `started_at` int(11) DEFAULT NULL,
  `completed_at` int(11) DEFAULT NULL,
  `created_by` varchar(50) NOT NULL DEFAULT 'admin',
  `date_created` int(11) NOT NULL,
  `date_updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_exp_code` (`experiment_code`),
  KEY `idx_exp_product` (`id_product`),
  KEY `idx_exp_status` (`status`),
  KEY `idx_exp_variable` (`changed_variable`),
  KEY `idx_exp_recommendation` (`id_recommendation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Extend table_winner_evaluation with is_legacy flag
ALTER TABLE `table_winner_evaluation`
ADD COLUMN IF NOT EXISTS `is_legacy` tinyint(1) DEFAULT 0 AFTER `ai_analysis`,
ADD KEY IF NOT EXISTS `idx_is_legacy` (`is_legacy`);

-- 4. Seed default optimization settings into table_analytics_setting
INSERT INTO `table_analytics_setting` (`setting_key`, `setting_value`, `setting_group`, `description`, `date_updated`) VALUES
('max_cost_per_experiment', '60000', 'optimization', 'Maximum external budget allowed per experiment in VND without admin override', UNIX_TIMESTAMP()),
('max_active_experiments_per_product', '2', 'optimization', 'Maximum concurrent running experiments allowed per product', UNIX_TIMESTAMP()),
('recommendation_cooldown_hours', '24', 'optimization', 'Hours to wait before generating duplicate recommendation for same product and metrics', UNIX_TIMESTAMP()),
('experiment_min_sessions', '30', 'optimization', 'Minimum landing sessions required before drawing experiment conclusions', UNIX_TIMESTAMP()),
('experiment_min_clicks', '10', 'optimization', 'Minimum affiliate clicks required before drawing experiment conclusions', UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE `date_updated` = VALUES(`date_updated`);
