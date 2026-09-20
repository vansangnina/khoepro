-- ======================================================================
-- FITNADO PHASE 08: ANALYTICS, AFFILIATE ATTRIBUTION & WINNER DETECTION
-- Database Migration Script
-- ======================================================================

-- 1. Extend table_publish_post with unique tracking_code
ALTER TABLE `table_publish_post` 
ADD COLUMN IF NOT EXISTS `tracking_code` varchar(64) NULL AFTER `disclosure_text`,
ADD UNIQUE KEY IF NOT EXISTS `idx_tracking_code` (`tracking_code`);

-- 2. Extend table_affiliate_click with attribution fields
ALTER TABLE `table_affiliate_click`
ADD COLUMN IF NOT EXISTS `tracking_code` varchar(64) NULL AFTER `id_affiliate`,
ADD COLUMN IF NOT EXISTS `session_id` varchar(64) NULL AFTER `tracking_code`,
ADD COLUMN IF NOT EXISTS `id_post` int(10) unsigned NULL AFTER `session_id`,
ADD COLUMN IF NOT EXISTS `id_video` int(10) unsigned NULL AFTER `id_post`,
ADD COLUMN IF NOT EXISTS `id_content` int(10) unsigned NULL AFTER `id_video`,
ADD COLUMN IF NOT EXISTS `is_internal` tinyint(1) DEFAULT 0 AFTER `referer`,
ADD KEY IF NOT EXISTS `idx_tracking_code` (`tracking_code`),
ADD KEY IF NOT EXISTS `idx_session_id` (`session_id`),
ADD KEY IF NOT EXISTS `idx_post_click` (`id_post`);

-- 3. Create table_analytics_event for standardized behavioral events
CREATE TABLE IF NOT EXISTS `table_analytics_event` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_type` varchar(50) NOT NULL COMMENT 'PAGE_VIEW, PRODUCT_VIEW, AFFILIATE_CLICK, POST_VIEW, VIDEO_VIEW, ENGAGEMENT, ADD_TO_CART, CONVERSION, REVENUE',
  `id_product` int(10) unsigned DEFAULT NULL,
  `id_post` int(10) unsigned DEFAULT NULL,
  `id_video` int(10) unsigned DEFAULT NULL,
  `id_content` int(10) unsigned DEFAULT NULL,
  `id_affiliate_offer` int(10) unsigned DEFAULT NULL,
  `tracking_code` varchar(64) DEFAULT NULL,
  `session_id` varchar(64) DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `medium` varchar(50) DEFAULT NULL,
  `campaign` varchar(100) DEFAULT NULL,
  `content_ref` varchar(100) DEFAULT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `ip_hash` varchar(64) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `device_type` varchar(20) DEFAULT 'desktop',
  `is_internal` tinyint(1) DEFAULT 0,
  `metadata` mediumtext DEFAULT NULL COMMENT 'JSON context',
  `event_time` int(11) NOT NULL,
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_tracking_code` (`tracking_code`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_product_event` (`id_product`),
  KEY `idx_post_event` (`id_post`),
  KEY `idx_event_time` (`event_time`),
  KEY `idx_internal` (`is_internal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create table_affiliate_conversion for merchant order reconciliation
CREATE TABLE IF NOT EXISTS `table_affiliate_conversion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversion_id` varchar(100) NOT NULL COMMENT 'Merchant order ID',
  `platform` varchar(50) NOT NULL COMMENT 'shopee, tiktok_shop, lazada, tiki, brand, other',
  `tracking_code` varchar(64) DEFAULT NULL,
  `id_product` int(10) unsigned DEFAULT NULL,
  `id_post` int(10) unsigned DEFAULT NULL,
  `id_video` int(10) unsigned DEFAULT NULL,
  `id_content` int(10) unsigned DEFAULT NULL,
  `id_affiliate_offer` int(10) unsigned DEFAULT NULL,
  `order_value` decimal(15,2) DEFAULT 0.00,
  `commission_value` decimal(15,2) DEFAULT 0.00,
  `currency` varchar(10) DEFAULT 'VND',
  `status` varchar(50) DEFAULT 'CONFIRMED' COMMENT 'PENDING, CONFIRMED, CANCELLED, REVERSED',
  `attribution_type` varchar(50) DEFAULT 'AUTO_MATCHED' COMMENT 'AUTO_MATCHED, MANUAL_MATCHED, UNATTRIBUTED',
  `conversion_at` int(11) NOT NULL,
  `settled_at` int(11) DEFAULT NULL,
  `raw_reference` mediumtext DEFAULT NULL COMMENT 'JSON raw CSV record',
  `is_manual_matched` tinyint(1) DEFAULT 0,
  `matched_by` varchar(50) DEFAULT NULL,
  `date_created` int(11) NOT NULL,
  `date_updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_conv_id_platform` (`conversion_id`, `platform`),
  KEY `idx_tracking_code` (`tracking_code`),
  KEY `idx_product_conv` (`id_product`),
  KEY `idx_post_conv` (`id_post`),
  KEY `idx_status` (`status`),
  KEY `idx_conversion_at` (`conversion_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create table_conversion_import_log for CSV upload history
CREATE TABLE IF NOT EXISTS `table_conversion_import_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `platform` varchar(50) NOT NULL,
  `total_rows` int(11) DEFAULT 0,
  `imported_count` int(11) DEFAULT 0,
  `matched_count` int(11) DEFAULT 0,
  `unattributed_count` int(11) DEFAULT 0,
  `duplicate_count` int(11) DEFAULT 0,
  `total_order_value` decimal(15,2) DEFAULT 0.00,
  `total_commission` decimal(15,2) DEFAULT 0.00,
  `currency` varchar(10) DEFAULT 'VND',
  `summary_json` mediumtext DEFAULT NULL,
  `imported_by` varchar(50) DEFAULT 'admin',
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_platform` (`platform`),
  KEY `idx_date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Create table_winner_evaluation for Winner snapshots and recommendations
CREATE TABLE IF NOT EXISTS `table_winner_evaluation` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(10) unsigned NOT NULL,
  `id_post` int(10) unsigned DEFAULT NULL,
  `id_video` int(10) unsigned DEFAULT NULL,
  `id_content` int(10) unsigned DEFAULT NULL,
  `winner_status` varchar(50) NOT NULL COMMENT 'WINNER, PROMISING, TESTING, UNDERPERFORMING, INSUFFICIENT_DATA',
  `signal_level` varchar(50) DEFAULT 'NONE' COMMENT 'REVENUE, CONVERSION, CLICK, TRAFFIC, NONE',
  `metrics_snapshot` mediumtext NOT NULL COMMENT 'JSON snapshot of CTR, CVR, EPC, ROI, sessions, clicks, etc.',
  `rules_snapshot` mediumtext NOT NULL COMMENT 'JSON snapshot of thresholds at evaluation time',
  `recommendation` mediumtext NOT NULL COMMENT 'JSON action plan and next steps',
  `ai_analysis` text DEFAULT NULL,
  `evaluated_at` int(11) NOT NULL,
  `evaluated_by` varchar(50) DEFAULT 'admin',
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_product_eval` (`id_product`),
  KEY `idx_winner_status` (`winner_status`),
  KEY `idx_signal_level` (`signal_level`),
  KEY `idx_evaluated_at` (`evaluated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Create table_analytics_setting for rules and thresholds
CREATE TABLE IF NOT EXISTS `table_analytics_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_group` varchar(50) DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL,
  `date_updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Seed default analytics settings
INSERT INTO `table_analytics_setting` (`setting_key`, `setting_value`, `setting_group`, `description`, `date_updated`) VALUES
('attribution_window_days', '30', 'attribution', 'Number of days to attribute clicks/conversions back to the last content touch', UNIX_TIMESTAMP()),
('min_landing_sessions', '30', 'winner_rules', 'Minimum landing sessions required before evaluating product performance', UNIX_TIMESTAMP()),
('min_affiliate_clicks', '10', 'winner_rules', 'Minimum affiliate clicks required before evaluating product performance', UNIX_TIMESTAMP()),
('min_conversions', '2', 'winner_rules', 'Minimum orders required to confirm a Winner status on revenue/conversion signal', UNIX_TIMESTAMP()),
('min_test_age_days', '3', 'winner_rules', 'Minimum test duration in days before flagging a product as Underperforming', UNIX_TIMESTAMP()),
('promising_ctr_pct', '5.0', 'winner_rules', 'CTR threshold (%) to classify a product as Promising', UNIX_TIMESTAMP()),
('winner_ctr_pct', '10.0', 'winner_rules', 'CTR threshold (%) to classify a product as Winner', UNIX_TIMESTAMP()),
('winner_cvr_pct', '5.0', 'winner_rules', 'CVR threshold (%) to classify a product as Winner', UNIX_TIMESTAMP()),
('underperforming_ctr_pct', '1.0', 'winner_rules', 'CTR threshold (%) below which a product is classified as Underperforming', UNIX_TIMESTAMP()),
('internal_ips', '["127.0.0.1", "::1"]', 'traffic', 'JSON list of IP addresses to filter out from public analytics', UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE `date_updated` = VALUES(`date_updated`);
