-- ==============================================================================
-- FITNADO PHASE 07 DATABASE MIGRATION: PUBLISHING CENTER & TIKTOK FOUNDATION
-- Non-destructive DDL migration for Post Packages, Accounts, and Publish Logs
-- Engine: MyISAM / InnoDB, Collation: utf8mb4_unicode_ci
-- ==============================================================================

-- 1. Table Publish Post (Post Packages)
CREATE TABLE IF NOT EXISTS `table_publish_post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) unsigned NOT NULL,
  `id_video` int(11) unsigned NOT NULL,
  `id_ai_content` int(11) unsigned DEFAULT NULL,
  `platform` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tiktok',
  `post_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VIDEO_POST',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashtags` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_offer_id` int(11) unsigned DEFAULT NULL,
  `landing_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disclosure_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `account_id` int(11) unsigned DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `scheduled_at` int(11) unsigned DEFAULT NULL,
  `published_at` int(11) unsigned DEFAULT NULL,
  `external_post_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_post_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_response` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `is_outdated` tinyint(1) NOT NULL DEFAULT 0,
  `snapshot_data` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publish_lock` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` int(11) unsigned NOT NULL DEFAULT 0,
  `date_updated` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_id_product` (`id_product`),
  KEY `idx_id_video` (`id_video`),
  KEY `idx_status` (`status`),
  KEY `idx_platform` (`platform`),
  KEY `idx_provider` (`provider`),
  KEY `idx_scheduled_at` (`scheduled_at`),
  KEY `idx_published_at` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table Publish Account (Multi-channel accounts)
CREATE TABLE IF NOT EXISTS `table_publish_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tiktok',
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_handle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `auth_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MANUAL_ONLY',
  `auth_data` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_expires_at` int(11) unsigned DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` int(11) unsigned NOT NULL DEFAULT 0,
  `date_updated` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_platform` (`platform`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table Publish Log (Audit Trail)
CREATE TABLE IF NOT EXISTS `table_publish_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_post` int(11) unsigned NOT NULL,
  `event` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_id_post` (`id_post`),
  KEY `idx_event` (`event`),
  KEY `idx_date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default TikTok Account if empty
INSERT INTO `table_publish_account` (`platform`, `account_name`, `account_handle`, `channel_id`, `provider`, `status`, `auth_status`, `is_default`, `date_created`, `date_updated`)
SELECT 'tiktok', 'FITNADO Official TikTok', '@fitnado.vn', 'fitnado_tiktok_01', 'manual', 'active', 'MANUAL_ONLY', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM DUAL
WHERE NOT EXISTS (SELECT id FROM `table_publish_account` WHERE `account_handle` = '@fitnado.vn');
