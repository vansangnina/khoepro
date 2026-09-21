<?php
if (!defined('LIBRARIES')) die("Error");

/* Cấu hình danh sách nền tảng Affiliate tập trung */
$configAffiliate = array(
    'platforms' => array(
        'tiktok_shop' => array(
            'name' => 'TikTok Shop',
            'color' => '#000000',
            'bg' => '#000000',
            'text_color' => '#ffffff',
            'icon' => 'fab fa-tiktok',
            'btn_label' => 'Xem trên TikTok Shop'
        ),
        'shopee' => array(
            'name' => 'Shopee',
            'color' => '#ee4d2d',
            'bg' => '#ee4d2d',
            'text_color' => '#ffffff',
            'icon' => 'fas fa-shopping-bag',
            'btn_label' => 'Xem trên Shopee'
        ),
        'lazada' => array(
            'name' => 'Lazada',
            'color' => '#0f146d',
            'bg' => '#0f146d',
            'text_color' => '#ffffff',
            'icon' => 'fas fa-store',
            'btn_label' => 'Xem trên Lazada'
        ),
        'accesstrade' => array(
            'name' => 'ACCESSTRADE',
            'color' => '#E83E8C',
            'bg' => '#E83E8C',
            'text_color' => '#ffffff',
            'icon' => 'fas fa-link',
            'btn_label' => 'Xem ưu đãi ACCESSTRADE'
        ),
        'brand' => array(
            'name' => 'Website chính hãng',
            'color' => '#0256aa',
            'bg' => '#0256aa',
            'text_color' => '#ffffff',
            'icon' => 'fas fa-globe',
            'btn_label' => 'Xem tại website hãng'
        ),
        'other' => array(
            'name' => 'Khác',
            'color' => '#475467',
            'bg' => '#475467',
            'text_color' => '#ffffff',
            'icon' => 'fas fa-external-link-alt',
            'btn_label' => 'Xem nơi bán'
        )
    ),
    'review_types' => array(
        'EDITOR_REVIEW' => array(
            'name' => 'Đánh giá từ Biên tập viên FITNADO',
            'badge' => 'Chuyên gia đánh giá',
            'icon' => 'fas fa-user-check'
        ),
        'REAL_TEST' => array(
            'name' => 'Kiểm định thực tế tại phòng Gym',
            'badge' => 'Đã test thực tế',
            'icon' => 'fas fa-dumbbell'
        ),
        'AI_ANALYSIS' => array(
            'name' => 'Phân tích tổng hợp dữ liệu kỹ thuật',
            'badge' => 'Dữ liệu tổng hợp',
            'icon' => 'fas fa-microchip'
        )
    )
);

/**
 * Validate Affiliate URL
 * Only allow http / https protocols, strip dangerous schemes
 *
 * @param string $url
 * @return bool
 */
function isValidAffiliateUrl($url)
{
    if (empty($url)) {
        return false;
    }
    $url = trim($url);
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    $scheme = strtolower(parse_url($url, PHP_URL_SCHEME));
    if (!in_array($scheme, array('http', 'https'))) {
        return false;
    }
    return true;
}

/**
 * Get Platform Info Helper
 *
 * @param string $key
 * @return array
 */
function getAffiliatePlatformInfo($key)
{
    global $configAffiliate;
    if (isset($configAffiliate['platforms'][$key])) {
        return $configAffiliate['platforms'][$key];
    }
    return $configAffiliate['platforms']['other'];
}
