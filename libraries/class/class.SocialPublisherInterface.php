<?php
/**
 * KHOEPRO - Social Publisher Interface
 * Unified contract for multi-platform social media video distribution.
 * PHP 7.4 & 8.x Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

interface SocialPublisherInterface
{
    /**
     * Get platform key ('tiktok', 'facebook', 'youtube_shorts', 'manual')
     * @return string
     */
    public function getPlatform();

    /**
     * Get human-readable provider name
     * @return string
     */
    public function getProviderName();

    /**
     * Check if provider has active valid credentials for the given account
     * @param array $accountData
     * @return bool
     */
    public function isConfigured(array $accountData = array());

    /**
     * Validate post package readiness before publish
     * @param array $postPackage
     * @param array $accountData
     * @return array ['valid' => bool, 'errors' => array()]
     */
    public function validate(array $postPackage, array $accountData = array());

    /**
     * Publish video/post to social network
     * @param array $postPackage
     * @param array $accountData
     * @return array ['success' => bool, 'status' => string, 'external_id' => string|null, 'external_url' => string|null, 'error' => string|null, 'response' => array|null]
     */
    public function publish(array $postPackage, array $accountData = array());

    /**
     * Query remote post status
     * @param string $externalPostId
     * @param array $accountData
     * @return array ['status' => string, 'details' => array()]
     */
    public function getStatus($externalPostId, array $accountData = array());

    /**
     * Query remote engagement metrics (Views, Likes, Comments, Shares)
     * @param string $externalPostId
     * @param array $accountData
     * @return array ['success' => bool, 'views' => int, 'likes' => int, 'comments' => int, 'shares' => int, 'raw' => array()]
     */
    public function getMetrics($externalPostId, array $accountData = array());
}
