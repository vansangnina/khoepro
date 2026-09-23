<?php
/**
 * KHOEPRO - Social Analytics Sync & Attribution Feedback Engine
 * Ingests engagement metrics, attributes revenue/commission to social posts, and feeds back into Product Intelligence.
 * PHP 7.4 & 8.x Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once __DIR__ . '/class.SocialPublisherFactory.php';

class SocialAnalyticsSync
{
    private $d;
    private $func;

    public function __construct($d = null, $func = null)
    {
        $this->d = $d;
        $this->func = $func;
    }

    /**
     * Ingest metrics for published posts from social networks
     * @param int $limit
     * @return array ['synced' => int, 'errors' => int, 'records_created' => int]
     */
    public function syncPostMetrics($limit = 50)
    {
        if (!$this->d) return array('synced' => 0, 'errors' => 0);

        $now = time();
        $publishedPosts = $this->d->rawQuery(
            "SELECT p.*, a.auth_status, a.auth_data, a.api_config_encrypted
             FROM table_publish_post p
             LEFT JOIN table_publish_account a ON p.account_id = a.id
             WHERE p.status IN ('PUBLISHED', 'READY') 
             ORDER BY p.published_at DESC LIMIT ?",
            array((int)$limit)
        );

        $synced = 0;
        $created = 0;
        $errors = 0;

        foreach ($publishedPosts as $post) {
            $idPost = (int)$post['id'];
            $platform = $post['platform'] ?? 'tiktok';
            $extId = $post['external_post_id'] ?? ('post_' . $idPost);

            $publisher = SocialPublisherFactory::create($platform, $this->d, $this->func);
            $accountData = array(
                'auth_status' => $post['auth_status'] ?? 'MANUAL_ONLY',
                'auth_data' => $post['auth_data'] ?? '',
                'api_config_encrypted' => $post['api_config_encrypted'] ?? ''
            );

            $metrics = $publisher->getMetrics($extId, $accountData);

            if (!empty($metrics['success'])) {
                // Count affiliate clicks driven by this post
                $clickCountRow = $this->d->rawQueryOne(
                    "SELECT COUNT(*) as clicks FROM table_affiliate_click WHERE id_product = ?",
                    array((int)$post['id_product'])
                );
                $clicks = (int)($clickCountRow['clicks'] ?? 0);

                $this->d->insert('social_post_metric', array(
                    'id_post' => $idPost,
                    'platform' => $platform,
                    'external_post_id' => (string)$extId,
                    'views_count' => (int)($metrics['views'] ?? 0),
                    'likes_count' => (int)($metrics['likes'] ?? 0),
                    'comments_count' => (int)($metrics['comments'] ?? 0),
                    'shares_count' => (int)($metrics['shares'] ?? 0),
                    'watch_time_seconds' => (int)($metrics['watch_time_seconds'] ?? 0),
                    'clicks_count' => $clicks,
                    'impressions_count' => (int)($metrics['views'] ?? 0),
                    'raw_metrics_json' => json_encode($metrics['raw'] ?? array(), JSON_UNESCAPED_UNICODE),
                    'recorded_at' => $now,
                    'date_created' => $now
                ));
                $synced++;
                $created++;
            } else {
                $errors++;
            }
        }

        return array(
            'synced' => $synced,
            'records_created' => $created,
            'errors' => $errors
        );
    }

    /**
     * Feedback Loop: Calculate Performance-Weighted Score Boosts for Product Intelligence
     * Connects Social Conversion & Commission performance back into Product Scoring
     * 
     * Formula:
     * High CTR/Conversions on TikTok -> Increases TikTok Score & Candidate Priority
     * 
     * @return array ['products_updated' => int, 'boosts_applied' => array]
     */
    public function applyFeedbackLoop()
    {
        if (!$this->d) return array('products_updated' => 0);

        $now = time();
        // Aggregates performance by product
        $sql = "SELECT p.id_product, p.platform,
                       COUNT(DISTINCT p.id) as total_posts,
                       COALESCE(SUM(c.commission_value), 0) as total_commission,
                       COALESCE(COUNT(DISTINCT c.id), 0) as total_orders
                FROM table_publish_post p
                LEFT JOIN table_affiliate_conversion c ON p.id_product = c.id_product
                WHERE p.status = 'PUBLISHED'
                GROUP BY p.id_product, p.platform";

        $rows = $this->d->rawQuery($sql);
        $updated = 0;
        $boosts = array();

        foreach ($rows as $r) {
            $idProd = (int)$r['id_product'];
            $platform = $r['platform'];
            $orders = (int)$r['total_orders'];
            $commission = (float)$r['total_commission'];

            // Calculate positive performance boost (0 to +15 points)
            $boost = 0.0;
            if ($orders >= 5 || $commission >= 500000) {
                $boost = 15.0;
            } elseif ($orders >= 2 || $commission >= 100000) {
                $boost = 10.0;
            } elseif ($orders >= 1) {
                $boost = 5.0;
            }

            if ($boost > 0) {
                $colToBoost = ($platform === 'tiktok') ? 'tiktok_score' : (($platform === 'facebook') ? 'facebook_score' : 'youtube_score');
                
                // Update research scores if record exists
                $resRow = $this->d->rawQueryOne("SELECT id, {$colToBoost} FROM table_product_research WHERE id_product = ? LIMIT 1", array($idProd));
                if (!empty($resRow)) {
                    $current = (float)($resRow[$colToBoost] ?? 50.0);
                    $newScore = min(100.0, $current + $boost);
                    $this->d->rawQuery("UPDATE table_product_research SET {$colToBoost} = ? WHERE id = ?", array($newScore, (int)$resRow['id']));
                    $updated++;
                    $boosts[] = array('product_id' => $idProd, 'platform' => $platform, 'boost' => $boost, 'new_score' => $newScore);
                }
            }
        }

        return array(
            'products_updated' => $updated,
            'boosts_applied' => $boosts
        );
    }
}
