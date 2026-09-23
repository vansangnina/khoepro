<?php
/**
 * KHOEPRO - Affiliate Provider Factory
 * Dynamically resolves and creates Affiliate Providers based on provider keys or database configurations.
 * PHP 7.4 & 8.x Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once __DIR__ . '/class.AffiliateProviderInterface.php';
require_once __DIR__ . '/class.AccessTradeProvider.php';

class AffiliateProviderFactory
{
    /**
     * Create an affiliate provider instance
     * @param string $providerKey ('accesstrade', 'shopee', etc.)
     * @param PDODb|null $d
     * @param Functions|null $func
     * @param array $customConfig
     * @return AffiliateProviderInterface
     */
    public static function create($providerKey = 'accesstrade', $d = null, $func = null, array $customConfig = array())
    {
        $key = strtolower(trim($providerKey));

        // If custom config not passed, try loading provider record from database
        if (empty($customConfig) && $d) {
            $record = $d->rawQueryOne("SELECT * FROM table_affiliate_provider WHERE provider_key = ? AND status = 'ACTIVE' LIMIT 1", array($key));
            if (!empty($record)) {
                $opts = !empty($record['options']) ? json_decode($record['options'], true) : array();
                if (!is_array($opts)) $opts = array();
                
                $customConfig = array_merge(array(
                    'base_url' => $record['base_url'] ?? '',
                    'rate_limit_per_minute' => (int)($record['rate_limit_per_minute'] ?? 30),
                    'sync_interval_minutes' => (int)($record['sync_interval_minutes'] ?? 30)
                ), $opts);
            }
        }

        switch ($key) {
            case 'accesstrade':
            default:
                return new AccessTradeProvider($d, $func, $customConfig);
        }
    }

    /**
     * Get list of all registered & active providers
     * @param PDODb|null $d
     * @return array
     */
    public static function getActiveProviders($d = null)
    {
        $list = array();
        if ($d) {
            $rows = $d->rawQuery("SELECT * FROM table_affiliate_provider WHERE status = 'ACTIVE' ORDER BY id ASC");
            if (!empty($rows)) {
                foreach ($rows as $r) {
                    $list[$r['provider_key']] = array(
                        'key' => $r['provider_key'],
                        'name' => $r['name'],
                        'platform' => $r['platform'],
                        'sync_enabled' => (bool)$r['sync_enabled'],
                        'last_synced_at' => $r['last_synced_at']
                    );
                }
            }
        }

        if (empty($list)) {
            $list['accesstrade'] = array(
                'key' => 'accesstrade',
                'name' => 'ACCESSTRADE Publisher Network',
                'platform' => 'accesstrade',
                'sync_enabled' => true,
                'last_synced_at' => null
            );
        }

        return $list;
    }
}
