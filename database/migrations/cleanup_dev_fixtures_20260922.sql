-- ==============================================================================
-- KHOEPRO SAFE AUDIT & CLEANUP SCRIPT (SEMANTIC-ONLY / NON-ID-BASED)
-- Target: Optional production hygiene / local fixture cleanup
-- Rules:
-- 1. NO hardcoded numeric IDs.
-- 2. NO broad WHERE id >= 1 deletes.
-- 3. Run PREVIEW SELECT queries first.
-- 4. Check dependencies before executing any action.
-- 5. Wrapped in transaction for safety.
-- ==============================================================================

-- ==============================================================================
-- STEP 1: PREVIEW QUERIES (RUN THESE FIRST IN READ-ONLY MODE)
-- ==============================================================================

-- 1.1. Preview research candidates with fixture/test provenance
SELECT id, name, platform, discovery_source, status, id_product, date_created
FROM `table_product_research`
WHERE `discovery_source` IN ('phase05_fixture', 'test_fixture')
   OR `name` LIKE 'FITNADO Pro Deadlift %'
   OR `name` LIKE '%178979%';

-- 1.2. Verify if any of the above candidates are linked to real production products
SELECT r.id AS research_id, r.name AS candidate_name, p.id AS product_id, p.namevi AS product_name
FROM `table_product_research` r
INNER JOIN `table_product` p ON (r.id_product = p.id)
WHERE r.discovery_source IN ('phase05_fixture', 'test_fixture')
   OR r.name LIKE 'FITNADO Pro Deadlift %'
   OR r.name LIKE '%178979%';
-- Expected result: 0 rows (No production product dependencies).

-- 1.3. Preview test seeds containing timestamped test keywords
SELECT id, title, keyword, seed_type, status
FROM `table_product_research_seed`
WHERE `keyword` LIKE '%1789%'
   OR `title` LIKE '%1789%';

-- 1.4. Preview mock/fixture test jobs
SELECT id, id_seed, provider, status, date_created
FROM `table_product_research_job`
WHERE `provider` = 'mock'
   OR `payload` LIKE '%fixture%';


-- ==============================================================================
-- STEP 2: SAFE SEMANTIC CLEANUP (EXECUTE ONLY IF PREVIEW CONFIRMS UNLINKED FIXTURES)
-- ==============================================================================

START TRANSACTION;

-- 2.1. Clean child evidence & snapshots only for unlinked semantic fixtures (id_product IS NULL)
DELETE s FROM `table_product_research_snapshot` s
INNER JOIN `table_product_research` r ON (s.id_research = r.id)
WHERE r.id_product IS NULL
  AND (r.discovery_source IN ('phase05_fixture', 'test_fixture') OR r.name LIKE 'FITNADO Pro Deadlift %' OR r.name LIKE '%178979%');

DELETE e FROM `table_product_research_evidence` e
INNER JOIN `table_product_research` r ON (e.id_research = r.id)
WHERE r.id_product IS NULL
  AND (r.discovery_source IN ('phase05_fixture', 'test_fixture') OR r.name LIKE 'FITNADO Pro Deadlift %' OR r.name LIKE '%178979%');

-- 2.2. Delete unlinked fixture candidate records
DELETE FROM `table_product_research`
WHERE `id_product` IS NULL
  AND (`discovery_source` IN ('phase05_fixture', 'test_fixture') OR `name` LIKE 'FITNADO Pro Deadlift %' OR `name` LIKE '%178979%');

-- 2.3. Remove only timestamped development test seeds
DELETE FROM `table_product_research_seed`
WHERE `keyword` LIKE '%1789%'
   OR `title` LIKE '%1789%';

-- 2.4. Remove only mock/fixture research jobs
DELETE FROM `table_product_research_job`
WHERE `provider` = 'mock'
   OR `payload` LIKE '%fixture%';

-- Verify remaining records before committing
SELECT COUNT(*) AS remaining_production_candidates FROM `table_product_research`;
SELECT COUNT(*) AS remaining_production_seeds FROM `table_product_research_seed`;

-- If all checks pass:
COMMIT;

-- If any anomaly is observed, rollback immediately:
-- ROLLBACK;
