# FITNADO — Production Deployment & Host Migration Guide
**Phase 10.1: Production Readiness & ACCESSTRADE Publisher API Integration**  
**Target Host:** Linux VPS / Dedicated Server / cPanel / Docker  
**PHP Version:** 7.4+ | **MySQL Version:** 5.7+ / 8.0+  

---

## 1. Pre-Deployment Prerequisites

Ensure the destination server satisfies the following requirements:
1. **PHP Runtime:** PHP 7.4.x - 8.2.x with extensions: `pdo_mysql`, `curl`, `json`, `mbstring`, `gd` or `imagick`, `fileinfo`, `zip`.
2. **Database:** MySQL 5.7+ / 8.0+ or MariaDB 10.3+.
3. **Web Server:** Apache 2.4+ (`mod_rewrite` enabled) or Nginx with PHP-FPM.
4. **Media Processing:** `ffmpeg` and `ffprobe` installed (or static build placed in `/usr/local/bin/`).
5. **SSL Certificate:** Valid HTTPS certificate (Let's Encrypt / Cloudflare SSL).

---

## 2. Step-by-Step Deployment Procedure

### Step 1: Upload / Clone Codebase
```bash
cd /home/username/public_html
git clone https://github.com/vansangnina/masterpdo.git .
```

### Step 2: Directory Permissions Setup
Ensure the web server user (`www-data` or `nobody`) has write permissions to storage paths:
```bash
chmod -R 755 .
chmod -R 777 upload/
chmod -R 777 upload/ai_video/
chmod -R 777 upload/cache/
chmod -R 777 upload/logs/
```

### Step 3: Configure Database & API Secrets in `libraries/config.php`
Edit `libraries/config.php` with production credentials:
```php
$config['database'] = array(
    'server-name' => 'localhost',
    'dbhost' => '127.0.0.1',
    'username' => 'prod_db_user',
    'password' => 'Prod_Strong_Password_Here',
    'dbname' => 'fitnado_prod',
    'port' => 3306,
    'prefix' => 'table_',
    'charset' => 'utf8mb4'
);

$config['accesstrade'] = array(
    'access_key' => 'YOUR_OFFICIAL_ACCESSTRADE_ACCESS_KEY',
    'secret_key' => 'YOUR_OFFICIAL_ACCESSTRADE_SECRET_KEY',
    'base_url' => 'https://api.accesstrade.vn',
    'timeout' => 30,
    'sync_enabled' => true,
    'rate_limit_per_minute' => 60
);
```

### Step 4: Import Database Migrations
Execute base schema and all incremental phase migrations in order:
```bash
mysql -u prod_db_user -p fitnado_prod < database/masterpdo_clean.sql
mysql -u prod_db_user -p fitnado_prod < database/migrations/phase02_affiliate.sql
mysql -u prod_db_user -p fitnado_prod < database/migrations/phase03_product_research.sql
mysql -u prod_db_user -p fitnado_prod < database/migrations/phase04_product_research_agent.sql
mysql -u prod_db_user -p fitnado_prod < database/migrations/phase05_content_engine.sql
mysql -u prod_db_user -p fitnado_prod < database/migrations/phase06_video_engine.sql
mysql -u prod_db_user -p fitnado_prod < database/migrations/phase07_publishing_center.sql
mysql -u prod_db_user -p fitnado_prod < database/migrations/phase08_analytics_attribution.sql
mysql -u prod_db_user -p fitnado_prod < database/migrations/phase09_optimization_cro.sql
mysql -u prod_db_user -p fitnado_prod < database/migrations/phase10_operations_center.sql
mysql -u prod_db_user -p fitnado_prod < database/migrations/phase10_1_accesstrade.sql
```

### Step 5: Install Production Crontab
Install scheduled workers as documented in [PRODUCTION-CRON.md](file:///Volumes/CD/web_2026/masterpdo/.ai/reports/PRODUCTION-CRON.md).

### Step 6: Post-Deployment Smoke Verification
Run automated test suite directly on server:
```bash
php test_phase10_1.php
```
Ensure all 12 test suites pass with `0 failures`.

---

## 3. Secret Management & Security Checklist

- **Never commit live API keys:** Keys must remain in `libraries/config.php` on production server only.
- **Secret Sanitizer Active:** All admin dashboard cards and logs automatically mask access keys (`****`).
- **Database Backups:** Schedule daily mysqldump backups to secure offsite storage.
- **Admin Access:** Enforce strong passwords and IP whitelisting for `/admin/` access if possible.

---

## 4. Rollback & Disaster Recovery

In case of an unexpected issue during deployment:
1. **Revert Database:** Restore latest database snapshot before migration.
2. **Revert Git Branch:** `git checkout <previous_stable_commit_tag>`.
3. **Emergency Pause:** Trigger Emergency Pause via Operations Center (`index.php?com=operations&act=emergency_pause_paid`) to instantly freeze all paid background workers.
