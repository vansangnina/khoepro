<?php
/**
 * KHOEPRO - Affiliate Provider Interface
 * Unified contract for all Affiliate Source Providers (AccessTrade, Shopee, TikTok Shop, Custom Datafeeds)
 * PHP 7.4 & 8.x Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

if (!class_exists('NormalizedProductDTO')) {
    require_once __DIR__ . '/class.NormalizedProductDTO.php';
}

interface AffiliateProviderInterface
{
    /**
     * Get unique provider identifier key (e.g. 'accesstrade', 'shopee', 'tiktok_shop')
     * @return string
     */
    public function getProviderKey();

    /**
     * Get human-readable provider name
     * @return string
     */
    public function getProviderName();

    /**
     * Check if provider has active API credentials configured
     * @return bool
     */
    public function isConfigured();

    /**
     * Test provider API connection status
     * @return array ['success' => bool, 'message' => string, 'latency_ms' => float, 'http_code' => int]
     */
    public function testConnection();

    /**
     * Search and retrieve products normalized into NormalizedProductDTO array
     * @param string $keyword
     * @param array $params
     * @return array ['success' => bool, 'products' => NormalizedProductDTO[], 'total' => int, 'error' => string|null]
     */
    public function searchProducts($keyword, array $params = array());

    /**
     * Get active campaigns / merchants from provider
     * @param array $params
     * @return array ['success' => bool, 'campaigns' => array[], 'total' => int, 'error' => string|null]
     */
    public function getCampaigns(array $params = array());

    /**
     * Generate dynamic tracking deep link with sub-IDs (sub1=product, sub2=post, sub3=experiment, sub4=tracking_code)
     * @param string $destinationUrl
     * @param string $campaignId
     * @param array $trackingParams
     * @return array ['success' => bool, 'tracking_url' => string, 'short_url' => string, 'error' => string|null]
     */
    public function generateTrackingLink($destinationUrl, $campaignId = '', array $trackingParams = array());

    /**
     * Sync conversions/orders from affiliate provider into database
     * @param array $options
     * @return array ['success' => bool, 'total' => int, 'imported' => int, 'updated' => int, 'skipped' => int, 'error' => string|null]
     */
    public function syncTransactions(array $options = array());
}
