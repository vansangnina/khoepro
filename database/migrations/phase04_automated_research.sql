-- FITNADO Phase 04: Automated Product Research & AI Agent Migration

-- 1. Table: table_product_research_seed
CREATE TABLE IF NOT EXISTS `table_product_research_seed` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `seed_type` varchar(50) NOT NULL DEFAULT 'keyword',
  `category_id` int(11) unsigned DEFAULT 0,
  `platform` varchar(50) NOT NULL DEFAULT 'all',
  `priority` int(11) NOT NULL DEFAULT 10,
  `depth` varchar(20) NOT NULL DEFAULT 'STANDARD',
  `frequency` varchar(20) NOT NULL DEFAULT 'manual',
  `max_results` int(11) NOT NULL DEFAULT 10,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `last_run` int(11) NOT NULL DEFAULT 0,
  `next_run` int(11) NOT NULL DEFAULT 0,
  `date_created` int(11) NOT NULL DEFAULT 0,
  `date_updated` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_seed_type` (`seed_type`),
  KEY `idx_platform` (`platform`),
  KEY `idx_priority` (`priority`),
  KEY `idx_next_run` (`next_run`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table: table_product_research_job
CREATE TABLE IF NOT EXISTS `table_product_research_job` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_seed` int(11) unsigned NOT NULL DEFAULT 0,
  `provider` varchar(50) NOT NULL DEFAULT 'ai_agent',
  `depth` varchar(20) NOT NULL DEFAULT 'STANDARD',
  `status` varchar(20) NOT NULL DEFAULT 'PENDING',
  `attempts` int(11) NOT NULL DEFAULT 0,
  `max_attempts` int(11) NOT NULL DEFAULT 3,
  `payload` longtext DEFAULT NULL,
  `candidates_found` int(11) NOT NULL DEFAULT 0,
  `candidates_created` int(11) NOT NULL DEFAULT 0,
  `duplicates_count` int(11) NOT NULL DEFAULT 0,
  `result_summary` text DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `started_at` int(11) NOT NULL DEFAULT 0,
  `finished_at` int(11) NOT NULL DEFAULT 0,
  `duration` double NOT NULL DEFAULT 0,
  `date_created` int(11) NOT NULL DEFAULT 0,
  `date_updated` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_seed` (`id_seed`),
  KEY `idx_provider` (`provider`),
  KEY `idx_date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table: table_product_research_evidence
CREATE TABLE IF NOT EXISTS `table_product_research_evidence` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_research` int(11) unsigned NOT NULL,
  `id_job` int(11) unsigned NOT NULL DEFAULT 0,
  `provider` varchar(50) NOT NULL DEFAULT 'manual',
  `source_url` text DEFAULT NULL,
  `evidence_type` varchar(50) NOT NULL DEFAULT 'FACT',
  `field_name` varchar(100) NOT NULL,
  `field_value` text DEFAULT NULL,
  `captured_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_research` (`id_research`),
  KEY `idx_job` (`id_job`),
  KEY `idx_evidence_type` (`evidence_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table: table_product_research_snapshot
CREATE TABLE IF NOT EXISTS `table_product_research_snapshot` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_research` int(11) unsigned NOT NULL,
  `id_job` int(11) unsigned NOT NULL DEFAULT 0,
  `price` double DEFAULT NULL,
  `sales_count` int(11) DEFAULT NULL,
  `rating` double DEFAULT NULL,
  `review_count` int(11) DEFAULT NULL,
  `commission_rate` double DEFAULT NULL,
  `creator_count` int(11) DEFAULT NULL,
  `video_count` int(11) DEFAULT NULL,
  `top_video_views` bigint(20) DEFAULT NULL,
  `captured_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_research` (`id_research`),
  KEY `idx_job` (`id_job`),
  KEY `idx_captured_at` (`captured_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Extend table_product_research with tracking & AI fields
SET @dbname = DATABASE();
SET @tablename = "table_product_research";

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = "discovery_source"
  ) > 0,
  "SELECT 1",
  "ALTER TABLE table_product_research ADD COLUMN discovery_source varchar(50) NOT NULL DEFAULT 'manual' AFTER status, ADD COLUMN ai_analysis longtext DEFAULT NULL AFTER discovery_source, ADD COLUMN ai_confidence double DEFAULT NULL AFTER ai_analysis, ADD COLUMN first_seen_at int(11) DEFAULT NULL AFTER ai_confidence, ADD COLUMN last_seen_at int(11) DEFAULT NULL AFTER first_seen_at, ADD KEY idx_discovery_source (discovery_source), ADD KEY idx_last_seen (last_seen_at);"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
