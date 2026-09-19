<?php
class Affiliate
{
    private $d;

    public function __construct($d)
    {
        $this->d = $d;
    }

    /**
     * Get platform metadata (color, icon, display name)
     */
    public static function getPlatformMeta($platform = '')
    {
        $platform = strtolower(trim($platform));
        // Normalize platform keys (e.g. tiktok_shop -> tiktok)
        if (strpos($platform, 'tiktok') !== false) {
            $platformKey = 'tiktok';
        } else if (strpos($platform, 'shopee') !== false) {
            $platformKey = 'shopee';
        } else if (strpos($platform, 'lazada') !== false) {
            $platformKey = 'lazada';
        } else if (strpos($platform, 'tiki') !== false) {
            $platformKey = 'tiki';
        } else if (strpos($platform, 'amazon') !== false) {
            $platformKey = 'amazon';
        } else if (strpos($platform, 'brand') !== false || strpos($platform, 'web') !== false) {
            $platformKey = 'web';
        } else {
            $platformKey = $platform;
        }

        $platforms = [
            'shopee' => [
                'name' => 'Shopee',
                'badge_class' => 'badge-shopee',
                'color' => '#EE4D2D',
                'bg_color' => '#FEF0EE',
                'text_color' => '#EE4D2D',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19.5 7.05h-2.52C16.5 4.18 14.5 2 12 2S7.5 4.18 7.02 7.05H4.5C3.67 7.05 3 7.72 3 8.55v11.4c0 .83.67 1.5 1.5 1.5h15c.83 0 1.5-.67 1.5-1.5V8.55c0-.83-.67-1.5-1.5-1.5zM12 4c1.47 0 2.72 1.41 3.05 3.05H8.95C9.28 5.41 10.53 4 12 4zm7.5 15.45H4.5V9.05h15v10.4z"/></svg>',
                'cta_text' => 'Mua trên Shopee'
            ],
            'lazada' => [
                'name' => 'Lazada',
                'badge_class' => 'badge-lazada',
                'color' => '#0f146d',
                'bg_color' => '#EEF1FE',
                'text_color' => '#0f146d',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 7l10 5 10-5-10-5zm0 8.5L4.5 7 12 3.2 19.5 7 12 10.5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>',
                'cta_text' => 'Mua trên Lazada'
            ],
            'tiktok' => [
                'name' => 'TikTok Shop',
                'badge_class' => 'badge-tiktok',
                'color' => '#000000',
                'bg_color' => '#F1F1F2',
                'text_color' => '#000000',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.34 6.34 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 0 0 0 6.33-6.34V9.41a8.16 8.16 0 0 0 4.77 1.52V7.48c-.4 0-.79-.27-1-.79z"/></svg>',
                'cta_text' => 'Mua trên TikTok'
            ],
            'tiki' => [
                'name' => 'Tiki',
                'badge_class' => 'badge-tiki',
                'color' => '#1A94FF',
                'bg_color' => '#EDF7FF',
                'text_color' => '#1A94FF',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/><path fill="#fff" d="M8 8h8v2.5H8zm0 4h8v2.5H8zm0 4h5v2.5H8z"/></svg>',
                'cta_text' => 'Mua trên Tiki'
            ],
            'amazon' => [
                'name' => 'Amazon',
                'badge_class' => 'badge-amazon',
                'color' => '#FF9900',
                'bg_color' => '#FFF8E7',
                'text_color' => '#111111',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>',
                'cta_text' => 'Mua trên Amazon'
            ],
            'web' => [
                'name' => 'Website Chính Hãng',
                'badge_class' => 'badge-official',
                'color' => '#10B981',
                'bg_color' => '#ECFDF5',
                'text_color' => '#065F46',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20M2 12h20"/></svg>',
                'cta_text' => 'Mua tại Hãng'
            ]
        ];

        return $platforms[$platformKey] ?? [
            'name' => ucfirst($platform ?: 'Đại lý'),
            'badge_class' => 'badge-default',
            'color' => '#4B5563',
            'bg_color' => '#F3F4F6',
            'text_color' => '#1F2937',
            'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
            'cta_text' => 'Xem giá tốt nhất'
        ];
    }

    /**
     * Get all active affiliate offers for a product
     */
    public function getOffersByProductId($productId, $onlyActive = true)
    {
        $productId = (int)$productId;
        if (!$productId) return [];

        $where = "id_product = ?";
        $params = [$productId];

        if ($onlyActive) {
            $where .= " AND FIND_IN_SET('hienthi', status)";
        }

        $sql = "SELECT * FROM table_product_affiliate WHERE $where ORDER BY is_best_deal DESC, priority DESC, price ASC, id ASC";
        $offers = $this->d->rawQuery($sql, $params);

        if ($offers) {
            foreach ($offers as &$offer) {
                $offer['meta'] = self::getPlatformMeta($offer['platform']);
                $offer['affiliate_price'] = $offer['price'] ?? 0;
                $offer['discount_percent'] = self::calculateDiscountPercent($offer['original_price'], $offer['price']);
                $offer['go_url'] = 'go/' . $offer['id'];
                if (empty($offer['seller_name'])) {
                    $offer['seller_name'] = $offer['meta']['name'];
                }
            }
        }

        return $offers ?: [];
    }

    /**
     * Get single best offer for product
     */
    public function getBestOffer($productId)
    {
        $offers = $this->getOffersByProductId($productId, true);
        if (empty($offers)) return null;

        // Check if there is an explicit is_best_deal = 1
        foreach ($offers as $offer) {
            if (!empty($offer['is_best_deal'])) {
                return $offer;
            }
        }

        // Otherwise return the first one (which is lowest price / highest priority)
        return $offers[0];
    }

    /**
     * Calculate discount percentage
     */
    public static function calculateDiscountPercent($originalPrice, $affiliatePrice)
    {
        $original = (float)$originalPrice;
        $affiliate = (float)$affiliatePrice;

        if ($original > 0 && $affiliate > 0 && $original > $affiliate) {
            return (int)round((($original - $affiliate) / $original) * 100);
        }
        return 0;
    }

    /**
     * Track affiliate click and return destination URL
     */
    public function trackClick($affiliateId, $sourcePage = '')
    {
        $affiliateId = (int)$affiliateId;
        if (!$affiliateId) return false;

        $offer = $this->d->rawQueryOne("SELECT a.*, p.status as product_status, p.slugvi FROM table_product_affiliate a LEFT JOIN table_product p ON a.id_product = p.id WHERE a.id = ? AND FIND_IN_SET('hienthi', a.status) LIMIT 1", [$affiliateId]);

        if (empty($offer) || empty($offer['affiliate_url'])) {
            return false;
        }

        // Determine device
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $device = 'desktop';
        if (preg_match('/(android|iphone|ipad|mobile)/i', $userAgent)) {
            $device = 'mobile';
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        // Record click log
        $this->d->rawQuery("INSERT INTO table_affiliate_click (id_product, id_affiliate, platform, source_page, device_type, ip_hash, user_agent, referer, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            $offer['id_product'],
            $offer['id'],
            $offer['platform'],
            $sourcePage ?: $referer,
            $device,
            hash('sha256', $ip),
            substr($userAgent, 0, 255),
            $referer,
            time()
        ]);

        return $offer['affiliate_url'];
    }

    /**
     * Recalculate product review score & count from table_comment
     */
    public function recalculateProductRating($productId)
    {
        $productId = (int)$productId;
        if (!$productId) return;

        // Calculate average star & total count of approved comments
        $stats = $this->d->rawQueryOne("SELECT AVG(star) as avg_star, COUNT(id) as total_count FROM table_comment WHERE id_variant = ? AND FIND_IN_SET('hienthi', status) AND star > 0", [$productId]);

        $score = !empty($stats['avg_star']) ? round((float)$stats['avg_star'], 1) : 0.0;
        $count = !empty($stats['total_count']) ? (int)$stats['total_count'] : 0;

        $this->d->rawQuery("UPDATE table_product SET review_score = ?, review_count = ? WHERE id = ?", [$score, $count, $productId]);

        return [
            'review_score' => $score,
            'review_count' => $count
        ];
    }
}
