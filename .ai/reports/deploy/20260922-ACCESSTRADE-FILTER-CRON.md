# KHOEPRO — PRODUCTION DEPLOYMENT PACKAGE & RUNBOOK
## AccessTrade Discovery Filter & Production Cron Safety Runbook

**Deployment Date:** September 22, 2026  
**Target Domain:** `khoepro.com` (LIVE Production)  
**Target Architecture:** Two-Stage Product Discovery Filter + Dynamic Attribution (`utm_source=khoepro`)  
**Safety Classification:** NON-DISRUPTIVE INCREMENTAL DEPLOYMENT (Zero Downtime)

---

## 1. Separation of Local vs Production Database State

> [!IMPORTANT]
> **Production database is authoritative.**
> Local development fixtures existed only on local development environment and were already cleaned locally.
> **Production requires ZERO mandatory database migrations or schema alterations.**

| Environment | Database Schema Change | Database Data Change | Required Migration |
| :--- | :--- | :--- | :--- |
| **LOCAL** | NONE | 23 Dev Fixtures Cleaned | Completed locally |
| **PRODUCTION** | **NONE** | **NONE** | **NONE** |

---

## 2. Files to Upload (Verified Code Changes Only)

Based on exact git diff against base, only the following **4 PHP files** contain changes:

| File Path | Status | Rationale & Change Summary |
| :--- | :--- | :--- |
| `libraries/class/class.ProductRelevanceFilter.php` | **NEW** | Two-stage product relevance filter engine (Heuristic negative blacklist + AI semantic analysis gate). |
| `libraries/class/class.AccessTradeProvider.php` | **MODIFIED** | Set default `utm_source=khoepro` for all new deep links; corrected transaction endpoint to `/v1/transactions`. |
| `libraries/class/class.ResearchProvider.php` | **MODIFIED** | Integrated `ProductRelevanceFilter` gate into `AccessTradeResearchProvider::discoverCandidates()`. |
| `libraries/class/class.AIResearchAgent.php` | **MODIFIED** | Added KhoePro fitness relevance prompt handler in `MockAIProvider` for test/fallback coverage. |

*(Note: `cron/accesstrade_sync_worker.php` is already in place on production from Phase 10.1 and has zero code diff).*

---

## 3. Files to Preserve (DO NOT OVERWRITE)

> [!CAUTION]
> **STRICT SECURITY RULE — NEVER OVERWRITE THE FOLLOWING FILES:**
> 1. **`libraries/config.php`** — Contains live production database credentials, production domain SSL rules, and production AccessTrade API key (`access_key`).
> 2. **Production `.htaccess` / Web Server Config**.
> 3. **Production storage directories:** `upload/`, `cache/`, `logs/`.

---

## 4. Production Database Verification (Optional Read-Only Preview)

If the production database administrator wishes to inspect for any old development test markers before running cron, use the safe **semantic-only** script:

`database/migrations/cleanup_dev_fixtures_20260922.sql`

**Safety Rules Enforced:**
- **Zero hardcoded numeric IDs** (No `WHERE id IN (...)`).
- **Zero broad table deletions** (No `WHERE id >= 1`).
- All deletions require `id_product IS NULL` to guarantee real production catalog products are never touched.
- Step 1 provides `SELECT` preview queries before any transaction is opened.

---

## 5. Step-by-Step Production Deployment Procedure

### Step 1: Pre-Deployment Backup
Create a timestamped backup of the 3 existing files on the production server:
```bash
# On production server:
cp libraries/class/class.AccessTradeProvider.php libraries/class/class.AccessTradeProvider.php.bak_20260922
cp libraries/class/class.ResearchProvider.php libraries/class/class.ResearchProvider.php.bak_20260922
cp libraries/class/class.AIResearchAgent.php libraries/class/class.AIResearchAgent.php.bak_20260922
```

### Step 2: Upload Verified Code Files
Upload the 4 files listed in Section 2 to their respective paths under the production webroot.

### Step 3: Production PHP 7.4 Syntax Validation
Validate syntax of all 4 uploaded files using the production server's PHP 7.4 CLI binary:
```bash
# Find production PHP 7.4 binary (e.g., /usr/bin/php7.4 or via which):
PHP_BIN=$(which php7.4 || which php)

$PHP_BIN -l libraries/class/class.ProductRelevanceFilter.php
$PHP_BIN -l libraries/class/class.AccessTradeProvider.php
$PHP_BIN -l libraries/class/class.ResearchProvider.php
$PHP_BIN -l libraries/class/class.AIResearchAgent.php
$PHP_BIN -l cron/accesstrade_sync_worker.php
```
**Assertion:** Every file must output `No syntax errors detected`.

### Step 4: Admin Live API Verification
Log into KhoePro Admin Panel:
- URL: `https://khoepro.com/admin/index.php?com=operations&act=providers`
- Click **"Kiểm Tra Kết Nối (ACCESSTRADE)"**
- Verify output: `HTTP 200 OK — Kết nối thành công`.

### Step 5: Manual One-Time Worker Test Run
Execute the worker manually once from CLI:
```bash
$PHP_BIN cron/accesstrade_sync_worker.php
```
**Expected Output:**
- `Status: SUCCESS`
- `0 duplicates created`
- Zero fatal errors or warnings.
- Verify in Admin that NO public products were created and NO research candidates were auto-approved.

### Step 6: Configure Production Crontab (Initial Controlled Logging)
Configure the crontab using the resolved PHP 7.4 binary and absolute webroot path. For the initial rollout period, log output to a dedicated log file rather than `/dev/null`:

```cron
# KhoePro ACCESSTRADE Background Sync Worker (Runs every 30 minutes)
*/30 * * * * /usr/bin/php7.4 /path/to/khoepro.com/cron/accesstrade_sync_worker.php >> /path/to/khoepro.com/logs/accesstrade_cron.log 2>&1
```
*(After 48 hours of stable operation with zero errors, logging may be redirected or rotated).*

---

## 6. Rollback Plan

If any issue is detected after upload:
1. **Disable Cron:** Comment out the cron line in `crontab -e`.
2. **Restore Backups:**
   ```bash
   cp libraries/class/class.AccessTradeProvider.php.bak_20260922 libraries/class/class.AccessTradeProvider.php
   cp libraries/class/class.ResearchProvider.php.bak_20260922 libraries/class/class.ResearchProvider.php
   cp libraries/class/class.AIResearchAgent.php.bak_20260922 libraries/class/class.AIResearchAgent.php
   rm -f libraries/class/class.ProductRelevanceFilter.php
   ```
3. **Database:** No database rollback needed (Zero DDL/DML applied).

---

## 7. Production Test & Monitoring Endpoints

- Operations Control Center: `https://khoepro.com/admin/index.php?com=operations&act=list`
- Affiliate Providers & Connection Ping: `https://khoepro.com/admin/index.php?com=operations&act=providers`
- Candidate Research Staging Queue: `https://khoepro.com/admin/index.php?com=product_research&act=man`
- Affiliate Conversion Ledger: `https://khoepro.com/admin/index.php?com=affiliate&act=conversions`
