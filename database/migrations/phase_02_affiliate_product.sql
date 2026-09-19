-- FITNADO PHASE 02: DATABASE MIGRATION
-- Non-destructive schema additions for Product Detail, Affiliate Offers, and Click Tracking

-- 1. Create table_product_affiliate
CREATE TABLE IF NOT EXISTS `table_product_affiliate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) unsigned NOT NULL DEFAULT 0,
  `platform` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `seller_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_url` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_url` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` double NOT NULL DEFAULT 0,
  `commission_rate` double NOT NULL DEFAULT 0,
  `commission_value` double NOT NULL DEFAULT 0,
  `priority` int(11) NOT NULL DEFAULT 0,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hienthi',
  `date_created` int(11) NOT NULL DEFAULT 0,
  `date_updated` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_product` (`id_product`),
  KEY `platform` (`platform`),
  KEY `status` (`status`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create table_affiliate_click
CREATE TABLE IF NOT EXISTS `table_affiliate_click` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) unsigned NOT NULL DEFAULT 0,
  `id_affiliate` int(11) unsigned NOT NULL DEFAULT 0,
  `platform` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_page` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'product_detail',
  `device_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'desktop',
  `referer` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_product` (`id_product`),
  KEY `id_affiliate` (`id_affiliate`),
  KEY `platform` (`platform`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Extend table_product with Review & Spec fields (Non-destructive)
-- The columns below will be added via PHP script checking existence to prevent errors if rerun.
