<?php
if (!defined('SOURCES')) die("Error");

require_once LIBRARIES . "config-affiliate.php";

$id = (!empty($_GET['id'])) ? (int)$_GET['id'] : 0;

if (empty($id)) {
    $func->transfer("Liên kết tiếp thị không hợp lệ.", $configBase, false);
    exit;
}

/* Query offer from database */
$offer = $d->rawQueryOne("SELECT a.*, p.status as product_status, p.slugvi, p.namevi FROM table_product_affiliate a LEFT JOIN table_product p ON (a.id_product = p.id) WHERE a.id = ? AND FIND_IN_SET('hienthi', a.status) LIMIT 1", array($id));

if (empty($offer)) {
    $func->transfer("Ưu đãi này hiện đã tạm ngưng hoặc không khả dụng.", $configBase, false);
    exit;
}

$destUrl = trim(!empty($offer['affiliate_url']) ? $offer['affiliate_url'] : ($offer['affiliate_link'] ?? ''));

if (empty($destUrl) || !isValidAffiliateUrl($destUrl)) {
    $func->transfer("Đường dẫn liên kết không an toàn hoặc không tồn tại.", $configBase, false);
    exit;
}

/* Record click tracking */
$sourcePage = (!empty($_GET['src'])) ? htmlspecialchars($_GET['src']) : 'product_detail';
$referer = (!empty($_SERVER['HTTP_REFERER'])) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : '';
$ip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
$userAgent = (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars(substr($_SERVER['HTTP_USER_AGENT'], 0, 255)) : '';
$device = (isset($deviceType)) ? $deviceType : 'desktop';

$clickData = [
    'id_product' => (int)$offer['id_product'],
    'id_affiliate' => (int)$offer['id'],
    'platform' => htmlspecialchars($offer['platform']),
    'source_page' => htmlspecialchars($sourcePage),
    'device_type' => $device,
    'ip_hash' => hash('sha256', $ip),
    'user_agent' => $userAgent,
    'referer' => $referer,
    'date_created' => time()
];

$d->insert('affiliate_click', $clickData);

/* Set SEO & Security headers */
header("X-Robots-Tag: noindex, nofollow, noarchive", true);
header("Referrer-Policy: no-referrer-when-downgrade");

/* 302 / 307 Redirect */
header("Location: " . $destUrl, true, 302);
exit;
