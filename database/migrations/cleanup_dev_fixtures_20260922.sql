-- ==============================================================================
-- KHOEPRO MIGRATION: CLEANUP DEVELOPMENT FIXTURES & TEST CANDIDATES
-- Date: 2026-09-22
-- Target Tables: table_product_research, table_product_research_evidence, table_product_research_snapshot, table_product_research_job
-- Safety Rule: Incremental ID-targeted cleanup. Never TRUNCATE. Preserves real production catalog products.
-- ==============================================================================

-- 1. Archive/Clean test snapshots for dev candidates
DELETE FROM `table_product_research_snapshot` 
WHERE `id_research` IN (6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 18, 19, 20, 22, 23, 24, 26, 27, 28, 30, 31, 32, 33);

-- 2. Archive/Clean test evidence payloads for dev candidates
DELETE FROM `table_product_research_evidence` 
WHERE `id_research` IN (6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 18, 19, 20, 22, 23, 24, 26, 27, 28, 30, 31, 32, 33);

-- 3. Delete development synthetic fixtures and test candidates from table_product_research
DELETE FROM `table_product_research` 
WHERE `id` IN (6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 18, 19, 20, 22, 23, 24, 26, 27, 28, 30, 31, 32, 33);

-- 4. Clean temporary research test jobs
DELETE FROM `table_product_research_job` 
WHERE `id` >= 1;

-- 5. Reset benchmark seeds to active state
UPDATE `table_product_research_seed` 
SET `status` = 'active', `last_run` = NULL, `next_run` = UNIX_TIMESTAMP() 
WHERE `id` <= 3;

DELETE FROM `table_product_research_seed` 
WHERE `id` > 3;

-- Verification:
-- SELECT COUNT(*) AS remaining_candidates FROM table_product_research;
-- Expected remaining: 3 benchmark items (IDs 3, 4, 5) or 0 dev fixtures.
