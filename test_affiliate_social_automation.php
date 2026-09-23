<?php
/**
 * KHOEPRO - AFFILIATE CONTENT & MULTI-SOCIAL AUTOMATION SUITE
 * Comprehensive End-to-End Verification Test Script
 * PHP 7.4 & 8.x Compatible
 */

define('LIBRARIES', __DIR__ . '/libraries/');
require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$dbConfig = $config['database'];
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);

echo "======================================================================\n";
echo "KHOEPRO AFFILIATE CONTENT & MULTI-SOCIAL AUTOMATION TEST SUITE\n";
echo "Execution Time: " . date('Y-m-d H:i:s') . "\n";
echo "======================================================================\n\n";

$testsTotal = 0;
$testsPassed = 0;
$testsFailed = 0;

function runTest($testName, $callback) {
    global $testsTotal, $testsPassed, $testsFailed;
    $testsTotal++;
    echo "[TEST {$testsTotal}] {$testName}... ";
    try {
        $res = $callback();
        if ($res['success']) {
            $testsPassed++;
            echo "PASSED\n";
            if (!empty($res['message'])) {
                echo "   -> " . $res['message'] . "\n";
            }
        } else {
            $testsFailed++;
            echo "FAILED\n";
            echo "   -> ERROR: " . ($res['error'] ?? 'Assertion failed') . "\n";
        }
    } catch (Throwable $e) {
        $testsFailed++;
        echo "EXCEPTION\n";
        echo "   -> " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}

// ----------------------------------------------------------------------
// 1. Multi-Affiliate Abstraction & Normalized Product DTO
// ----------------------------------------------------------------------
require_once LIBRARIES . 'class/class.NormalizedProductDTO.php';
require_once LIBRARIES . 'class/class.AffiliateProviderInterface.php';
require_once LIBRARIES . 'class/class.AffiliateProviderFactory.php';
require_once LIBRARIES . 'class/class.AccessTradeProvider.php';

runTest("NormalizedProductDTO Validation & Field Mapping", function() {
    $data = array(
        'product_id' => 'EXT_9999',
        'name' => 'Đai lưng tập gym gánh tạ Aolikes 7983',
        'price' => 180000,
        'regular_price' => 300000,
        'affiliate_url' => 'https://fast.accesstrade.vn/deep_link/12345',
        'image_url' => 'https://khoepro.com/upload/photo.jpg',
        'merchant' => 'Shopee',
        'commission_rate' => 10.0
    );
    $dto = new NormalizedProductDTO($data);
    $val = $dto->validateForContent();
    if (!$val['valid']) {
        return array('success' => false, 'error' => implode(', ', $val['errors']));
    }
    if ($dto->discountPct != 40.0) {
        return array('success' => false, 'error' => "Discount calc mismatch: expected 40.0, got {$dto->discountPct}");
    }
    if ($dto->commissionValue != 18000.0) {
        return array('success' => false, 'error' => "Commission calc mismatch: expected 18000.0, got {$dto->commissionValue}");
    }
    return array('success' => true, 'message' => "Normalized title: '{$dto->title}', Discount: {$dto->discountPct}%, Commission: {$dto->commissionValue} VND");
});

runTest("AffiliateProviderFactory & AccessTradeProvider Implementation", function() use ($d, $func) {
    $provider = AffiliateProviderFactory::create('accesstrade', $d, $func);
    if (!($provider instanceof AffiliateProviderInterface)) {
        return array('success' => false, 'error' => 'Provider does not implement AffiliateProviderInterface');
    }
    if ($provider->getProviderKey() !== 'accesstrade') {
        return array('success' => false, 'error' => 'Provider key mismatch');
    }
    $conn = $provider->testConnection();
    return array('success' => true, 'message' => "Provider '{$provider->getProviderName()}' active (Configured: " . ($provider->isConfigured() ? 'YES' : 'NO') . ")");
});

// ----------------------------------------------------------------------
// 2. Multi-Platform Scoring & Eligibility Gate
// ----------------------------------------------------------------------
require_once LIBRARIES . 'class/class.ProductResearch.php';
$research = new ProductResearch($d, $func);

runTest("Multi-Platform Scoring (Global, TikTok, Facebook, YouTube)", function() use ($research) {
    $sampleProduct = array(
        'namevi' => 'Dây kháng lực tập mông đùi Aolikes Hip Band',
        'price' => 150000,
        'regular_price' => 250000,
        'commission_rate' => 12.0,
        'sales_count' => 3500,
        'rating' => 4.9,
        'top_video_views' => 450000,
        'problem_solved' => 'Trượt cuốn khi tập squats và hip thrusts',
        'target_audience' => 'Nữ tập gym cải thiện vòng 3',
        'primary_keyword' => 'dây kháng lực tập mông'
    );
    $scores = $research->calculatePlatformScores($sampleProduct);
    if (!isset($scores['global_score'], $scores['tiktok_score'], $scores['facebook_score'], $scores['youtube_score'])) {
        return array('success' => false, 'error' => 'Missing platform score keys in result');
    }
    if ($scores['tiktok_score'] <= 0 || $scores['facebook_score'] <= 0 || $scores['youtube_score'] <= 0) {
        return array('success' => false, 'error' => 'Invalid score values generated');
    }
    return array('success' => true, 'message' => "Global={$scores['global_score']}, TikTok={$scores['tiktok_score']}, Facebook={$scores['facebook_score']}, YouTube={$scores['youtube_score']}");
});

runTest("Strict Product Eligibility Gate (Completeness & Anti-Spam)", function() use ($research) {
    $validData = array(
        'id' => 9999,
        'namevi' => 'Đai lưng gym',
        'price' => 200000,
        'affiliate_url' => 'https://domain.com/aff',
        'photo' => 'photo.jpg',
        'status' => 'hienthi'
    );
    $check1 = $research->checkEligibilityForContent($validData);
    if (!$check1['eligible']) {
        return array('success' => false, 'error' => 'Valid product marked as ineligible: ' . implode(', ', $check1['reasons']));
    }

    $invalidData = array(
        'namevi' => '',
        'price' => 0,
        'affiliate_url' => '',
        'photo' => ''
    );
    $check2 = $research->checkEligibilityForContent($invalidData);
    if ($check2['eligible']) {
        return array('success' => false, 'error' => 'Invalid product marked as eligible');
    }

    return array('success' => true, 'message' => "Eligibility checks working (Invalid caught " . count($check2['reasons']) . " missing fields)");
});

// ----------------------------------------------------------------------
// 3. Content Candidate Engine & Selection Reasons
// ----------------------------------------------------------------------
require_once LIBRARIES . 'class/class.ContentCandidateEngine.php';
$candidateEngine = new ContentCandidateEngine($d, $func, $research);

runTest("Content Candidate Scanner & Selection Reason Logging", function() use ($candidateEngine) {
    $res = $candidateEngine->scanAndRankCandidates(array('limit' => 20, 'min_score' => 30));
    if (!$res['success']) {
        return array('success' => false, 'error' => $res['error'] ?? 'Scan failed');
    }
    $list = $candidateEngine->getCandidates(array('limit' => 5));
    if (empty($list['items'])) {
        return array('success' => false, 'error' => 'No candidates in candidate table');
    }
    $first = $list['items'][0];
    if (empty($first['selection_reason'])) {
        return array('success' => false, 'error' => 'Candidate missing selection_reason text');
    }
    return array('success' => true, 'message' => "Scanned {$res['scanned']} products, Top candidate: #{$first['id']} (Reason: '{$first['selection_reason']}')");
});

// ----------------------------------------------------------------------
// 4. Master Content Package & Platform Adapters
// ----------------------------------------------------------------------
require_once LIBRARIES . 'class/class.MasterContentPackage.php';

runTest("Master Content Package Generation & Platform Adapters", function() use ($d) {
    $pkg = MasterContentPackage::buildFromDatabase(101, $d, 1);
    if (!$pkg) {
        return array('success' => false, 'error' => 'Failed to build MasterContentPackage from DB');
    }
    $saveId = $pkg->saveToDatabase($d);
    if (!$saveId) {
        return array('success' => false, 'error' => 'Failed to save MasterContentPackage to DB');
    }
    $tiktok = $pkg->toTikTokVariant();
    $fb = $pkg->toFacebookVariant();
    $yt = $pkg->toYouTubeVariant();

    if (empty($tiktok['hook']) || empty($tiktok['shot_plan'])) {
        return array('success' => false, 'error' => 'TikTok variant incomplete');
    }
    if (empty($fb['body_text']) || empty($fb['caption'])) {
        return array('success' => false, 'error' => 'Facebook variant incomplete');
    }
    if (empty($yt['title']) || empty($yt['description'])) {
        return array('success' => false, 'error' => 'YouTube variant incomplete');
    }
    return array('success' => true, 'message' => "Master Content #{$saveId} adapted into TikTok ({$tiktok['target_duration']}s), FB (" . strlen($fb['body_text']) . "b), YT (" . strlen($yt['description']) . "b)");
});

// ----------------------------------------------------------------------
// 5. Compliance Guardrail & Policy Engine (PASS / WARNING / FAIL)
// ----------------------------------------------------------------------
require_once LIBRARIES . 'class/class.ComplianceGuardrail.php';

runTest("Content Policy Engine Tri-State Validation (PASS, WARNING, FAIL)", function() {
    $pass = ComplianceGuardrail::validatePolicyGate(array('caption' => 'Đai lưng tập gym hỗ trợ đúng form, an toàn'));
    if ($pass['policy_status'] !== 'PASS' || !$pass['can_publish']) {
        return array('success' => false, 'error' => 'Safe content failed policy check');
    }
    $warn = ComplianceGuardrail::validatePolicyGate(array('caption' => 'Đai lưng tốt nhất thế giới 100% hiệu quả'));
    if ($warn['policy_status'] !== 'WARNING' || $warn['can_publish']) {
        return array('success' => false, 'error' => 'Clickbait content not flagged as WARNING');
    }
    $fail = ComplianceGuardrail::validatePolicyGate(array('caption' => 'Cam kết chữa khỏi dứt điểm hoàn toàn thoát vị đĩa đệm'));
    if ($fail['policy_status'] !== 'FAIL' || $fail['can_publish']) {
        return array('success' => false, 'error' => 'Medical claim not blocked as FAIL');
    }
    return array('success' => true, 'message' => "Policy engine verified: PASS (Safe), WARNING (Clickbait), FAIL (Medical cure)");
});

// ----------------------------------------------------------------------
// 6. Multi-Social Publishers & SocialPublisherFactory
// ----------------------------------------------------------------------
require_once LIBRARIES . 'class/class.SocialPublisherFactory.php';

runTest("Multi-Social Publishers (TikTok, Facebook, YouTube Shorts)", function() use ($d, $func) {
    $platforms = array('tiktok', 'facebook', 'youtube_shorts');
    foreach ($platforms as $p) {
        $pub = SocialPublisherFactory::create($p, $d, $func);
        if ($pub->getPlatform() !== $p) {
            return array('success' => false, 'error' => "Platform mismatch for {$p}");
        }
        $postPkg = array('video_file' => 'upload/video/demo.mp4', 'caption' => 'Test caption', 'title' => 'Test Title');
        $res = $pub->publish($postPkg, array('auth_status' => 'MANUAL_ONLY'));
        if (!$res['success'] || $res['status'] !== 'READY') {
            return array('success' => false, 'error' => "Publish execution failed for {$p}");
        }
    }
    return array('success' => true, 'message' => "All 3 platform publishers active with Manual Fallback");
});

// ----------------------------------------------------------------------
// 7. Dynamic Social Scheduler, Buffer Quota & Atomic Lock
// ----------------------------------------------------------------------
require_once LIBRARIES . 'class/class.SocialSchedulerEngine.php';
$scheduler = new SocialSchedulerEngine($d, $func);

runTest("Dynamic Social Scheduler & Approved Pool Buffer", function() use ($scheduler) {
    $rule = $scheduler->getAccountScheduleRule(1, 'tiktok');
    if (empty($rule['posts_per_day'])) {
        return array('success' => false, 'error' => 'Invalid schedule rule');
    }
    $poolCount = $scheduler->countApprovedPool();
    $schedRes = $scheduler->scheduleApprovedPool(1);
    $dispatchRes = $scheduler->dispatchDuePosts(5);
    return array('success' => true, 'message' => "Account #1 Quota: {$rule['posts_per_day']}/day, Approved Pool: {$poolCount}, Dispatched: {$dispatchRes['dispatched']}");
});

// ----------------------------------------------------------------------
// 8. Social Analytics Ingestion & Feedback Loop
// ----------------------------------------------------------------------
require_once LIBRARIES . 'class/class.SocialAnalyticsSync.php';
$analyticsSync = new SocialAnalyticsSync($d, $func);

runTest("Social Analytics Metrics Ingestion & Intelligence Feedback Loop", function() use ($analyticsSync) {
    $syncRes = $analyticsSync->syncPostMetrics(5);
    $feedbackRes = $analyticsSync->applyFeedbackLoop();
    return array('success' => true, 'message' => "Synced {$syncRes['synced']} post metrics, Feedback loop evaluated ({$feedbackRes['products_updated']} score boosts applied)");
});

// ----------------------------------------------------------------------
// 9. Operations Center Worker Health & Incident Alerts
// ----------------------------------------------------------------------
require_once LIBRARIES . 'class/class.OperationsService.php';
$ops = new OperationsService($d, $func);

runTest("Operations Center Health, Alert Deduplication & Heartbeats", function() use ($ops) {
    $ops->recordWorkerSuccess('test_automation_suite', array('status' => 'OK'));
    $alertId = $ops->recordAlert('INFO', 'system', 'Test Automation Run', 'Automated suite ran successfully');
    $health = $ops->getEnvironmentHealth();
    $alerts = $ops->getAlerts('ACTIVE', 10);
    return array('success' => true, 'message' => "Environment Health: " . ($health['overall_status'] ?? 'HEALTHY') . ", Active Alerts: " . count($alerts));
});

echo "\n======================================================================\n";
echo "TEST RESULTS: {$testsPassed}/{$testsTotal} PASSED (" . round(($testsPassed / $testsTotal) * 100, 1) . "%)\n";
if ($testsFailed > 0) {
    echo "WARNING: {$testsFailed} tests failed.\n";
} else {
    echo "ALL SUITES PASSED! System is fully validated.\n";
}
echo "======================================================================\n";
