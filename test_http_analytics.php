<?php
/**
 * FITNADO Phase 08 - HTTP Endpoint Verification Script
 */

$urls = [
    'Admin Overview' => 'http://127.0.0.1:8080/admin/index.php?com=analytics&act=overview',
    'Admin Products' => 'http://127.0.0.1:8080/admin/index.php?com=analytics&act=products',
    'Admin Posts' => 'http://127.0.0.1:8080/admin/index.php?com=analytics&act=posts',
    'Admin Videos' => 'http://127.0.0.1:8080/admin/index.php?com=analytics&act=videos',
    'Admin Content' => 'http://127.0.0.1:8080/admin/index.php?com=analytics&act=content',
    'Admin Conversions' => 'http://127.0.0.1:8080/admin/index.php?com=analytics&act=conversions',
    'Admin Winner Detection' => 'http://127.0.0.1:8080/admin/index.php?com=analytics&act=winner_detection',
    'Admin Winner Rules' => 'http://127.0.0.1:8080/admin/index.php?com=analytics&act=winner_rules',
    'Admin Conversion Import' => 'http://127.0.0.1:8080/admin/index.php?com=analytics&act=conversion_import',
    'Frontend Home' => 'http://127.0.0.1:8080/index.php',
    'Frontend Product with Tracking' => 'http://127.0.0.1:8080/dai-lung-mem-fitnado-quick-lock-1789804060?ref=fp_tikt_d92d99fe&utm_source=tiktok&utm_medium=short_video&utm_campaign=post_5'
];

echo "=======================================================\n";
echo "FITNADO PHASE 08 - HTTP ENDPOINT VERIFICATION\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

foreach ($urls as $name => $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    $hasPhpError = (stripos($response, 'Fatal error') !== false || stripos($response, 'Parse error') !== false || stripos($response, 'Notice:') !== false || stripos($response, 'Warning:') !== false);
    
    if ($httpCode === 200 && !$hasPhpError && strlen($response) > 500) {
        echo sprintf("[PASS] %-30s | HTTP %d | Length: %6d bytes\n", $name, $httpCode, strlen($response));
        $passCount++;
    } else {
        echo sprintf("[FAIL] %-30s | HTTP %d | Length: %6d bytes | Err: %s\n", $name, $httpCode, strlen($response), $err ?: ($hasPhpError ? 'PHP Error/Notice detected' : 'Invalid response'));
        $failCount++;
    }
}

echo "\n=======================================================\n";
echo sprintf("HTTP VERIFICATION RESULTS: %d PASSED, %d FAILED\n", $passCount, $failCount);
echo "=======================================================\n";

if ($failCount > 0) {
    exit(1);
}
exit(0);
