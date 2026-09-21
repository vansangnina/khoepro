# FITNADO — Production Cron & Automation Schedule
**Phase 10.1: Production Readiness & ACCESSTRADE Publisher API Integration**  
**Date:** September 2026  

---

## 1. Background Worker Matrix

FITNADO automation runs entirely asynchronously via decoupled background cron workers. Each worker records its execution start, heartbeats, and success/error status into `table_system_worker_status`.

| Worker Key | Target Script | Frequency | Recommended Cron | Queue / Target Table | Purpose |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `publish_worker` | `cron/publish_worker.php` | Every 5-15 mins | `*/15 * * * *` | `table_publish_post` | Process scheduled video & content publications |
| `accesstrade_sync` | `cron/accesstrade_sync_worker.php` | Every 15-30 mins | `*/15 * * * *` | `table_affiliate_conversion` | Fetch conversions, clicks & sync transaction statuses |
| `video_render_worker` | `cron/video_render_worker.php` | Every 30 mins | `*/30 * * * *` | `table_ai_video_job` | Render background video compositions with FFmpeg & TTS |
| `ai_content_worker` | `cron/ai_content_worker.php` | Every 30 mins | `0 * * * *` | `table_ai_content_job` | Generate TikTok hooks, scripts & SEO metadata |
| `product_research_worker` | `cron/product_research_worker.php`| Hourly | `0 * * * *` | `table_product_research_job` | Crawl & discover gym products (staged for approval) |
| `analytics_service` | `cron/analytics_aggregation.php` | Hourly | `0 * * * *` | `table_analytics_summary_daily`| Aggregate hourly traffic, clicks, revenue & attribution |
| `optimization_engine` | `cron/optimization_daily.php` | Daily (03:00) | `0 3 * * *` | `table_optimization_recommendation` | Calculate experiment statistics, CTR/CR uplifts & recommendations |

---

## 2. Production Crontab Configuration

Replace `/home/username/public_html` with your actual production root directory.

```bash
# ==============================================================================
# FITNADO PRODUCTION AUTOMATION CRONTAB
# ==============================================================================
SHELL=/bin/bash
PATH=/usr/local/bin:/usr/bin:/bin:/usr/local/sbin:/usr/sbin
MAILTO=""

# 1. Publishing Dispatch Worker (Every 15 minutes)
*/15 * * * * cd /home/username/public_html && /usr/bin/php cron/publish_worker.php >> upload/logs/cron_publish.log 2>&1

# 2. ACCESSTRADE Transaction & Conversion Sync (Every 15 minutes)
*/15 * * * * cd /home/username/public_html && /usr/bin/php cron/accesstrade_sync_worker.php >> upload/logs/cron_accesstrade.log 2>&1

# 3. AI Video Render Worker (Every 30 minutes)
*/30 * * * * cd /home/username/public_html && /usr/bin/php cron/video_render_worker.php >> upload/logs/cron_video.log 2>&1

# 4. AI Content Generation Worker (Every hour)
0 * * * * cd /home/username/public_html && /usr/bin/php cron/ai_content_worker.php >> upload/logs/cron_content.log 2>&1

# 5. Product Research & ACCESSTRADE Datafeed Discovery (Every 2 hours)
0 */2 * * * cd /home/username/public_html && /usr/bin/php cron/product_research_worker.php >> upload/logs/cron_research.log 2>&1

# 6. Operations Stuck-Job Watchdog (Every 10 minutes)
*/10 * * * * cd /home/username/public_html && /usr/bin/php -r "require_once 'libraries/config.php'; require_once 'libraries/autoload.php'; new AutoLoad(); \$d=new PDODb(\$config['database']); require_once 'libraries/class/class.OperationsService.php'; \$ops=new OperationsService(\$d); \$ops->detectAndFlagStuckJobs();" >> upload/logs/cron_ops.log 2>&1
```

---

## 3. Alternative Webhook Execution (cPanel / External Uptime Monitors)

If shell crontab is restricted on shared hosting, workers can be triggered via HTTPS webhook protected with the application token:

```text
https://yourdomain.com/cron/publish_worker.php?token=<CONTRACT_TOKEN_HASH>
https://yourdomain.com/cron/accesstrade_sync_worker.php?token=<CONTRACT_TOKEN_HASH>&days=30
```

*Note: The `<CONTRACT_TOKEN_HASH>` is generated dynamically via `md5(NN_CONTRACT . $config['database']['dbname'])`.*

---

## 4. Operational Health & Alert Thresholds

- **Heartbeat Timeout Warning:** If a worker does not report a heartbeat within 300 seconds of expected run time, `OperationsService` raises an `ACTIVE` incident.
- **Stuck Job Auto-Flagging:** Any job in `PROCESSING` state for over 1800 seconds is automatically transitioned to `FAILED` with retry capability.
- **Budget Guard:** If external API cost exceeds daily limit (`200,000 VND`), external paid calls are paused automatically.
