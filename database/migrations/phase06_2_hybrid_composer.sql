-- FITNADO Phase 06.2 Database Migration: Low-Cost Hybrid Video Composer
-- Non-destructive DDL

-- Add mode column to table_ai_video
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_ai_video' AND COLUMN_NAME = 'mode');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_ai_video` ADD COLUMN `mode` varchar(20) NOT NULL DEFAULT 'ECONOMY' COMMENT 'ECONOMY, HYBRID, PREMIUM' AFTER `template_id`", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;

-- Add local_render_cost
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_ai_video' AND COLUMN_NAME = 'local_render_cost');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_ai_video` ADD COLUMN `local_render_cost` double NOT NULL DEFAULT 0 AFTER `cost_estimate`", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;

-- Add ai_video_seconds
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_ai_video' AND COLUMN_NAME = 'ai_video_seconds');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_ai_video` ADD COLUMN `ai_video_seconds` int(11) NOT NULL DEFAULT 0 AFTER `local_render_cost`", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;

-- Add ai_video_cost
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_ai_video' AND COLUMN_NAME = 'ai_video_cost');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_ai_video` ADD COLUMN `ai_video_cost` double NOT NULL DEFAULT 0 AFTER `ai_video_seconds`", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;

-- Add tts_cost
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_ai_video' AND COLUMN_NAME = 'tts_cost');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_ai_video` ADD COLUMN `tts_cost` double NOT NULL DEFAULT 0 AFTER `ai_video_cost`", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;

-- Add total_external_api_cost
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_ai_video' AND COLUMN_NAME = 'total_external_api_cost');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_ai_video` ADD COLUMN `total_external_api_cost` double NOT NULL DEFAULT 0 AFTER `tts_cost`", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;

-- Add composer_log
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_ai_video' AND COLUMN_NAME = 'composer_log');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_ai_video` ADD COLUMN `composer_log` mediumtext DEFAULT NULL AFTER `quality_report`", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;
