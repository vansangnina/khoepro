<?php
/**
 * KHOEPRO - Social Publisher Factory
 * Resolves appropriate SocialPublisherInterface implementation based on platform name.
 * PHP 7.4 & 8.x Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once __DIR__ . '/class.SocialPublisherInterface.php';
require_once __DIR__ . '/class.TikTokPublisher.php';
require_once __DIR__ . '/class.FacebookPublisher.php';
require_once __DIR__ . '/class.YouTubePublisher.php';

class SocialPublisherFactory
{
    /**
     * Create Social Publisher Instance
     * @param string $platform ('tiktok', 'facebook', 'youtube', 'youtube_shorts')
     * @param PDODb|null $d
     * @param Functions|null $func
     * @return SocialPublisherInterface
     */
    public static function create($platform = 'tiktok', $d = null, $func = null)
    {
        $platform = strtolower(trim($platform));

        switch ($platform) {
            case 'facebook':
            case 'fb':
            case 'facebook_reels':
                return new FacebookPublisher($d, $func);

            case 'youtube':
            case 'youtube_shorts':
            case 'yt':
                return new YouTubePublisher($d, $func);

            case 'tiktok':
            default:
                return new TikTokPublisher($d, $func);
        }
    }
}
