-- ==============================================================================
-- KHOEPRO PHASE 12: AFFILIATE CONTENT & MULTI-SOCIAL AUTOMATION ENGINE
-- Database Migration Script (Non-destructive, Idempotent, Safe Rollback Capable)
-- Engine: InnoDB, Collation: utf8mb4_unicode_ci
-- ==============================================================================

-- 1. Create table_affiliate_provider (Multi-Affiliate Abstraction)
CREATE TABLE IF NOT EXISTS `table_affiliate_provider` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `provider_key` varchar(50) NOT NULL COMMENT 'accesstrade, shopee, tiktok_shop, custom_feed',
  `name` varchar(255) NOT NULL,
  `platform` varchar(50) NOT NULL DEFAULT 'affiliate',
  `base_url` varchar(255) DEFAULT NULL,
  `api_key_encrypted` text DEFAULT NULL,
  `api_secret_encrypted` text DEFAULT NULL,
  `sync_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sync_interval_minutes` int(11) NOT NULL DEFAULT 30,
  `rate_limit_per_minute` int(11) NOT NULL DEFAULT 30,
  `last_synced_at` int(11) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'ACTIVE' COMMENT 'ACTIVE, INACTIVE, ERROR',
  `last_error` text DEFAULT NULL,
  `options` mediumtext DEFAULT NULL COMMENT 'JSON extra config',
  `date_created` int(11) NOT NULL DEFAULT 0,
  `date_updated` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_provider_key` (`provider_key`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default AccessTrade provider if empty
INSERT INTO `table_affiliate_provider` (`provider_key`, `name`, `platform`, `base_url`, `sync_enabled`, `sync_interval_minutes`, `rate_limit_per_minute`, `status`, `date_created`, `date_updated`)
SELECT 'accesstrade', 'ACCESSTRADE Publisher Network', 'accesstrade', 'https://api.accesstrade.vn', 1, 30, 30, 'ACTIVE', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM DUAL
WHERE NOT EXISTS (SELECT id FROM `table_affiliate_provider` WHERE `provider_key` = 'accesstrade');

-- 2. Extend table_product_research with Platform-specific Scores & Content tracking
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_product_research' AND COLUMN_NAME = 'tiktok_score');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_product_research` ADD COLUMN `tiktok_score` decimal(5,2) NULL AFTER `total_score`, ADD COLUMN `facebook_score` decimal(5,2) NULL AFTER `tiktok_score`, ADD COLUMN `youtube_score` decimal(5,2) NULL AFTER `facebook_score`, ADD COLUMN `platform_scores_json` text NULL AFTER `youtube_score`, ADD COLUMN `last_content_created_at` int(11) NULL AFTER `platform_scores_json`, ADD COLUMN `content_count` int(11) NOT NULL DEFAULT 0 AFTER `last_content_created_at`", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;

-- 3. Create table_content_candidate (Selected High-Potential Products for Content Automation)
CREATE TABLE IF NOT EXISTS `table_content_candidate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `research_id` int(11) unsigned DEFAULT NULL,
  `global_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tiktok_score` decimal(5,2) DEFAULT NULL,
  `facebook_score` decimal(5,2) DEFAULT NULL,
  `youtube_score` decimal(5,2) DEFAULT NULL,
  `target_platform` varchar(50) NOT NULL DEFAULT 'all' COMMENT 'all, tiktok, facebook, youtube',
  `selection_reason` text NOT NULL COMMENT 'Human-readable why this product was chosen',
  `eligibility_status` varchar(30) NOT NULL DEFAULT 'ELIGIBLE' COMMENT 'ELIGIBLE, INELIGIBLE, COOLDOWN',
  `eligibility_reasons` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'PENDING' COMMENT 'PENDING, APPROVED, PROCESSED, DISCARDED',
  `cooldown_until` int(11) DEFAULT NULL,
  `created_by` varchar(100) NOT NULL DEFAULT 'system',
  `date_created` int(11) NOT NULL DEFAULT 0,
  `date_updated` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_status` (`status`),
  KEY `idx_target_platform` (`target_platform`),
  KEY `idx_global_score` (`global_score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create table_master_content (Master Content Packages before platform adaptation)
CREATE TABLE IF NOT EXISTS `table_master_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `candidate_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `product_facts` mediumtext NOT NULL COMMENT 'Verified facts from DB only',
  `selling_points` text NOT NULL,
  `target_audience` varchar(255) DEFAULT NULL,
  `pain_points` text DEFAULT NULL,
  `key_benefits` text DEFAULT NULL,
  `offers_json` mediumtext DEFAULT NULL COMMENT 'Verified price & discount options',
  `images_json` mediumtext DEFAULT NULL COMMENT 'Verified image asset URLs',
  `content_angle` varchar(100) NOT NULL DEFAULT 'Problem/Solution',
  `status` varchar(30) NOT NULL DEFAULT 'DRAFT' COMMENT 'DRAFT, GENERATING, GENERATED, VALIDATING, PENDING_APPROVAL, APPROVED, REJECTED',
  `policy_status` varchar(20) NOT NULL DEFAULT 'PASS' COMMENT 'PASS, WARNING, FAIL',
  `policy_report` mediumtext DEFAULT NULL COMMENT 'JSON compliance validation details',
  `source_hash` varchar(64) NOT NULL COMMENT 'SHA-256 integrity hash',
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` int(11) DEFAULT NULL,
  `date_created` int(11) NOT NULL DEFAULT 0,
  `date_updated` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_candidate_id` (`candidate_id`),
  KEY `idx_status` (`status`),
  KEY `idx_policy_status` (`policy_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create table_social_schedule_rule (Dynamic Publishing Configuration)
CREATE TABLE IF NOT EXISTS `table_social_schedule_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL,
  `platform` varchar(50) NOT NULL DEFAULT 'tiktok',
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `posts_per_day` int(11) NOT NULL DEFAULT 3,
  `posting_times_json` varchar(255) NOT NULL DEFAULT '["09:00","14:00","20:00"]',
  `product_cooldown_days` int(11) NOT NULL DEFAULT 14,
  `merchant_cooldown_posts` int(11) NOT NULL DEFAULT 3,
  `content_cooldown_days` int(11) NOT NULL DEFAULT 30,
  `require_approval` tinyint(1) NOT NULL DEFAULT 1,
  `minimum_approved_pool` int(11) NOT NULL DEFAULT 5,
  `date_created` int(11) NOT NULL DEFAULT 0,
  `date_updated` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_account_rule` (`account_id`),
  KEY `idx_platform` (`platform`),
  KEY `idx_is_enabled` (`is_enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Extend table_publish_account with Credential Vault & Rate Limiting
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_publish_account' AND COLUMN_NAME = 'api_config_encrypted');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_publish_account` ADD COLUMN `api_config_encrypted` text NULL AFTER `auth_data`, ADD COLUMN `refresh_token_encrypted` text NULL AFTER `api_config_encrypted`, ADD COLUMN `token_scope` varchar(255) NULL AFTER `refresh_token_encrypted`, ADD COLUMN `rate_limit_per_day` int(11) NOT NULL DEFAULT 10 AFTER `token_scope`, ADD COLUMN `daily_posted_count` int(11) NOT NULL DEFAULT 0 AFTER `rate_limit_per_day`, ADD COLUMN `last_posted_at` int(11) NULL AFTER `daily_posted_count`", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;

-- 7. Extend table_publish_post with Master Content & Idempotency Key
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'table_publish_post' AND COLUMN_NAME = 'master_content_id');
SET @stmt = IF(@col_exists = 0, "ALTER TABLE `table_publish_post` ADD COLUMN `master_content_id` int(11) unsigned NULL AFTER `id_ai_content`, ADD COLUMN `id_candidate` int(11) unsigned NULL AFTER `master_content_id`, ADD COLUMN `retry_after` int(11) NULL AFTER `attempts`, ADD COLUMN `idempotency_key` varchar(64) NULL AFTER `retry_after`, ADD COLUMN `content_angle` varchar(100) NULL AFTER `idempotency_key`, ADD COLUMN `approval_status` varchar(30) NOT NULL DEFAULT 'APPROVED' AFTER `content_angle`, ADD KEY `idx_master_content` (`master_content_id`), ADD KEY `idx_idempotency` (`idempotency_key`)", "SELECT 1");
PREPARE st FROM @stmt;
EXECUTE st;
DEALLOCATE PREPARE st;

-- 8. Create table_social_post_metric (Performance Ingestion from Social Platforms)
CREATE TABLE IF NOT EXISTS `table_social_post_metric` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_post` int(11) unsigned NOT NULL,
  `platform` varchar(50) NOT NULL DEFAULT 'tiktok',
  `external_post_id` varchar(255) NOT NULL,
  `views_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `likes_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `comments_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `shares_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `watch_time_seconds` bigint(20) unsigned NOT NULL DEFAULT 0,
  `clicks_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `impressions_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `raw_metrics_json` mediumtext DEFAULT NULL,
  `recorded_at` int(11) NOT NULL,
  `date_created` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_post_time` (`id_post`, `recorded_at`),
  KEY `idx_external_post` (`external_post_id`),
  KEY `idx_platform` (`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Seed Facebook and YouTube default accounts in table_publish_account if missing
INSERT INTO `table_publish_account` (`platform`, `account_name`, `account_handle`, `channel_id`, `provider`, `status`, `auth_status`, `is_default`, `date_created`, `date_updated`)
SELECT 'facebook', 'KhoePro Official Fanpage & Reels', 'khoepro.official', 'fb_page_khoepro', 'manual', 'active', 'MANUAL_ONLY', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM DUAL
WHERE NOT EXISTS (SELECT id FROM `table_publish_account` WHERE `platform` = 'facebook' AND `account_handle` = 'khoepro.official');

INSERT INTO `table_publish_account` (`platform`, `account_name`, `account_handle`, `channel_id`, `provider`, `status`, `auth_status`, `is_default`, `date_created`, `date_updated`)
SELECT 'youtube_shorts', 'KhoePro Fitness Shorts Channel', '@khoepro_fit', 'yt_channel_khoepro', 'manual', 'active', 'MANUAL_ONLY', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM DUAL
WHERE NOT EXISTS (SELECT id FROM `table_publish_account` WHERE `platform` = 'youtube_shorts' AND `account_handle` = '@khoepro_fit');
