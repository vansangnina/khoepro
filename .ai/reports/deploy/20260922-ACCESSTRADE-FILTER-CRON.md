# KHOEPRO — PRODUCTION DEPLOYMENT PACKAGE & PROCEDURE
## AccessTrade Discovery Filter & Pre-Cron Production Runbook

**Deployment Date:** September 22, 2026  
**Target Domain:** `khoepro.com` (LIVE Production)  
**Target Architecture:** Two-Stage Product Discovery Filter + Dynamic Attribution (`utm_source=khoepro`)  
**Safety Classification:** NON-DISRUPTIVE INCREMENTAL DEPLOYMENT (Zero Downtime)

---

## 1. Files to Upload (Deployment Payload)

Upload ONLY the following files to production via Git pull or SFTP:

| Path | Type | Purpose |
| :--- | :--- | :--- |
| `libraries/class/class.ProductRelevanceFilter.php` | NEW | Two-Stage Product Relevance Filter Engine (Basic heuristic + AI semantic gate) |
| `libraries/class/class.AccessTradeProvider.php` | MODIFIED | Updated `utm_source=khoepro` default, `/v1/transactions` endpoint fix |
| `libraries/class/class.ResearchProvider.php` | MODIFIED | Integrated `ProductRelevanceFilter` gate into `AccessTradeResearchProvider` |
| `libraries/class/class.AIResearchAgent.php` | MODIFIED | Added KhoePro relevance analysis handler in `MockAIProvider` |
| `cron/accesstrade_sync_worker.php` | EXISTING/VERIFIED | Background transaction sync & reconciliation worker |
| `database/migrations/cleanup_dev_fixtures_20260922.sql` | MIGRATION | Incremental SQL script to archive/clean dev fixtures |

---

## 2. Files NOT to Upload (Strictly Protected)

> [!CAUTION]
> **DO NOT UPLOAD OR OVERWRITE THE FOLLOWING FILES:**
> 1. `libraries/config.php` — **NEVER OVERWRITE**. Production database credentials, live domain SSL, and production AccessTrade API key (`access_key`) must remain untouched.
> 2. `admin/` directory or admin source files unless specifically designated.
> 3. `.git/` or local test scripts (`test_*.php`).

---

## 3. Database Changes & SQL Migration

### Schema Changes:
- **NONE** (Schema was fully prepared in Phase 03/Phase 04/Phase 10.1).
- `table_product_research`, `table_product_research_evidence`, `table_product_research_snapshot`, `table_affiliate_conversion`, `table_analytics_event` already exist.

### Data Changes & Cleanup:
Execute the safe incremental cleanup migration on production database:
```sql
-- Run database/migrations/cleanup_dev_fixtures_20260922.sql

DELETE FROM `table_product_research_snapshot` 
WHERE `id_research` IN (6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 18, 19, 20, 22, 23, 24, 26, 27, 28, 30, 31, 32, 33);

DELETE FROM `table_product_research_evidence` 
WHERE `id_research` IN (6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 18, 19, 20, 22, 23, 24, 26, 27, 28, 30, 31, 32, 33);

DELETE FROM `table_product_research` 
WHERE `id` IN (6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 18, 19, 20, 22, 23, 24, 26, 27, 28, 30, 31, 32, 33);

DELETE FROM `table_product_research_job` 
WHERE `id` >= 1;

UPDATE `table_product_research_seed` 
SET `status` = 'active', `last_run` = NULL, `next_run` = UNIX_TIMESTAMP() 
WHERE `id` <= 3;

DELETE FROM `table_product_research_seed` 
WHERE `id` > 3;
```

---

## 4. PHP 7.4 Production Syntax Validation Step

Before enabling cron or executing workers on the live server, run syntax linting using production PHP 7.4 binary:

```bash
# Execute on production server:
php7.4 -l libraries/class/class.ProductRelevanceFilter.php
php7.4 -l libraries/class/class.AccessTradeProvider.php
php7.4 -l libraries/class/class.ResearchProvider.php
php7.4 -l libraries/class/class.AIResearchAgent.php
php7.4 -l cron/accesstrade_sync_worker.php
```

All files must return `No syntax errors detected in <file>`.

---

## 5. Production Step-by-Step Deployment Procedure

### Step 1: Code Synchronization
Pull or upload the verified files from Section 1 to the webroot `/home/khoepro/public_html/` (or production path).

### Step 2: Database Migration
Execute `database/migrations/cleanup_dev_fixtures_20260922.sql` via phpMyAdmin or MySQL CLI.

### Step 3: Verify Live API Connection via Admin Panel
Access KhoePro Admin Panel:
- URL: `https://khoepro.com/admin/index.php?com=operations&act=providers`
- Click **"Kiểm Tra Kết Nối (ACCESSTRADE)"**
- Verify response: `HTTP 200 OK — Kết nối thành công`.

### Step 4: Dry-run Manual Sync
Run sync worker manually once to verify transaction and campaign sync:
```bash
php cron/accesstrade_sync_worker.php
```
Verify output shows: `Status: SUCCESS`, `0 duplicates`.

### Step 5: Configure Production Crontab (When Approved)
Add the cron entry to crontab (`crontab -e`):
```cron
# KhoePro ACCESSTRADE Automated Sync Worker (Every 30 minutes)
*/30 * * * * /usr/bin/php /home/khoepro/public_html/cron/accesstrade_sync_worker.php > /dev/null 2>&1
```

---

## 6. Rollback Plan

If any anomaly occurs:
1. **Disable Cron:** Comment out the cron entry in `crontab -e`.
2. **Revert Files:**
   ```bash
   git checkout HEAD~1 libraries/class/class.AccessTradeProvider.php libraries/class/class.ResearchProvider.php
   ```
3. **Database:** No schema rollback required (no DDL applied).

---

## 7. Production Verification URLs

- Operations Control Center: `https://khoepro.com/admin/index.php?com=operations&act=list`
- Affiliate Providers: `https://khoepro.com/admin/index.php?com=operations&act=providers`
- Product Research Queue: `https://khoepro.com/admin/index.php?com=product_research&act=man`
- Affiliate Conversions: `https://khoepro.com/admin/index.php?com=affiliate&act=conversions`
