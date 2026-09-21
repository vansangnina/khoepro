-- ======================================================================
-- FITNADO PHASE 10.1: ACCESSTRADE PUBLISHER API INTEGRATION
-- Database Migration Script (Non-destructive, idempotent)
-- ======================================================================

-- 1. Extend table_product_affiliate with ACCESSTRADE campaign & merchant info
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_product_affiliate' AND COLUMN_NAME = 'at_campaign_id');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_product_affiliate` ADD COLUMN `at_campaign_id` varchar(100) NULL AFTER `platform`, ADD COLUMN `at_merchant` varchar(100) NULL AFTER `at_campaign_id`, ADD COLUMN `is_accesstrade` tinyint(1) NOT NULL DEFAULT 0 AFTER `at_merchant`, ADD KEY `idx_at_campaign` (`at_campaign_id`)", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;

-- 2. Extend table_affiliate_conversion with ACCESSTRADE specific transaction timestamps
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_affiliate_conversion' AND COLUMN_NAME = 'at_transaction_id');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_affiliate_conversion` ADD COLUMN `at_transaction_id` varchar(100) NULL AFTER `external_conversion_id`, ADD COLUMN `at_click_time` int(11) NULL AFTER `conversion_at`, ADD COLUMN `at_conversion_time` int(11) NULL AFTER `at_click_time`, ADD KEY `idx_at_trans_id` (`at_transaction_id`)", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;

-- 3. Seed operational settings into table_analytics_setting
INSERT INTO `table_analytics_setting` (`setting_key`, `setting_value`, `setting_group`, `description`, `date_updated`) VALUES
('accesstrade_sync_enabled', '1', 'accesstrade', 'Master switch for ACCESSTRADE automated transaction sync (1=ON, 0=OFF)', UNIX_TIMESTAMP()),
('accesstrade_sync_interval_minutes', '30', 'accesstrade', 'Incremental transaction sync interval in minutes', UNIX_TIMESTAMP()),
('accesstrade_last_sync_time', '0', 'accesstrade', 'Unix timestamp of the last successful ACCESSTRADE transaction sync', UNIX_TIMESTAMP()),
('accesstrade_rate_limit_per_minute', '30', 'accesstrade', 'Maximum allowed API requests per minute to respect provider rate limit', UNIX_TIMESTAMP()),
('accesstrade_overlap_minutes', '60', 'accesstrade', 'Safety overlap window in minutes for incremental order fetch to prevent missing late conversions', UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE `date_updated` = VALUES(`date_updated`);
