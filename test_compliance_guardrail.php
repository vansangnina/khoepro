<?php
/**
 * Test Suite: AI Content & Social Media Compliance Guardrail (21 Principles)
 * Tests risk classifications, policy checks, fact verification, medical claim restrictions,
 * affiliate disclosures, synthetic media tags, and structured backend response schema.
 */

require_once __DIR__ . '/libraries/class/class.ComplianceGuardrail.php';

$totalTests = 0;
$passedTests = 0;

function assertTest($description, $actual, $expected) {
    global $totalTests, $passedTests;
    $totalTests++;
    if ($actual === $expected) {
        $passedTests++;
        echo "[PASS] {$description}\n";
    } else {
        echo "[FAIL] {$description}\n";
        echo "   Expected: " . var_export($expected, true) . "\n";
        echo "   Actual:   " . var_export($actual, true) . "\n";
    }
}

echo "=== KHOEPRO AI COMPLIANCE GUARDRAIL TEST SUITE ===\n\n";

// Test 1: Low Risk Normal Content
$res1 = ComplianceGuardrail::evaluate(array(
    'content_text' => 'Băng quấn cổ tay tập gym KhoePro hỗ trợ giữ khớp cổ tay ổn định khi tập đẩy ngực nặng. Thiết kế vải co giãn 4 chiều thoáng khí.',
    'platform' => 'website',
    'has_affiliate' => false,
    'is_ai_generated' => true,
    'allow_auto_publish_low' => true
));
assertTest("Test 1.1: Normal informative fitness content has status PASS", $res1['status'], 'PASS');
assertTest("Test 1.2: Normal informative content has risk_level LOW", $res1['risk_level'], 'LOW');
assertTest("Test 1.3: Low risk allows auto_publish = true when configured", $res1['auto_publish'], true);
assertTest("Test 1.4: Low risk has empty issues list", count($res1['issues']), 0);
assertTest("Test 1.5: AI disclosure required is true for AI generated content", $res1['ai_disclosure_required'], true);

// Test 2: Medical Cure Claim (HIGH RISK)
$res2 = ComplianceGuardrail::evaluate(array(
    'content_text' => 'Đai lưng này chữa khỏi hoàn toàn bệnh đau lưng và điều trị dứt điểm thoát vị đĩa đệm sau 7 ngày.',
    'platform' => 'tiktok',
    'has_affiliate' => false
));
assertTest("Test 2.1: Illegal medical cure claim has status REVIEW_REQUIRED", $res2['status'], 'REVIEW_REQUIRED');
assertTest("Test 2.2: Illegal medical cure claim has risk_level HIGH", $res2['risk_level'], 'HIGH');
assertTest("Test 2.3: Medical claim disables auto_publish", $res2['auto_publish'], false);
assertTest("Test 2.4: Medical claim sets human_review_required = true", $res2['human_review_required'], true);
assertTest("Test 2.5: Claims requiring evidence contains medical violation", count($res2['claims_requiring_evidence']) > 0, true);

// Test 3: Fake Personal Experience Claim without REAL_TEST (HIGH RISK)
$res3 = ComplianceGuardrail::evaluate(array(
    'content_text' => 'Tôi đã dùng 30 ngày và thấy cơ bắp săn chắc rõ rệt.',
    'platform' => 'facebook',
    'has_real_test' => false
));
assertTest("Test 3.1: Fake personal experience without evidence has risk_level HIGH", $res3['risk_level'], 'HIGH');
assertTest("Test 3.2: Fake personal experience disables auto_publish", $res3['auto_publish'], false);

// Test 4: Extreme Clickbait (MEDIUM RISK / REWRITE)
$res4 = ComplianceGuardrail::evaluate(array(
    'content_text' => 'Đây là phụ kiện tập gym 100% hiệu quả tốt nhất thị trường số 1 thế giới.',
    'platform' => 'website',
    'has_affiliate' => false
));
assertTest("Test 4.1: Clickbait superlatives set risk_level MEDIUM", $res4['risk_level'], 'MEDIUM');
assertTest("Test 4.2: Clickbait sets status REWRITE", $res4['status'], 'REWRITE');
assertTest("Test 4.3: Medium risk disables auto_publish until rewritten", $res4['auto_publish'], false);

// Test 5: Auto-Sanitization for Medium Risk Text
$sanitized = ComplianceGuardrail::sanitizeText('Sản phẩm 100% hiệu quả và tốt nhất thị trường.');
assertTest("Test 5.1: Sanitized text replaces 100% hiệu quả", mb_stripos($sanitized, '100% hiệu quả'), false);
assertTest("Test 5.2: Sanitized text replaces tốt nhất thị trường", mb_stripos($sanitized, 'tốt nhất thị trường'), false);

// Test 6: Affiliate Disclosure Requirement
$res6 = ComplianceGuardrail::evaluate(array(
    'content_text' => 'Mua ngay tại Shopee giá siêu hời.',
    'platform' => 'tiktok',
    'has_affiliate' => true
));
assertTest("Test 6.1: Affiliate content flags affiliate_disclosure_required = true", $res6['affiliate_disclosure_required'], true);
assertTest("Test 6.2: Missing disclosure text flags issue", count($res6['issues']) > 0, true);

// Test 7: Privacy & PII Violation (HIGH RISK)
$res7 = ComplianceGuardrail::evaluate(array(
    'content_text' => 'Liên hệ ngay 0988123456 hoặc gửi CCCD số 079199001234 để nhận quà.',
    'platform' => 'website'
));
assertTest("Test 7.1: PII leak sets risk_level HIGH", $res7['risk_level'], 'HIGH');
assertTest("Test 7.2: Privacy risks identified", count($res7['privacy_risks']) >= 2, true);

// Test 8: Prohibited / Dangerous Content (BLOCKED)
$res8 = ComplianceGuardrail::evaluate(array(
    'content_text' => 'Hướng dẫn hack tài khoản và cờ bạc online kiếm tiền.',
    'platform' => 'tiktok'
));
assertTest("Test 8.1: Dangerous / illegal content has risk_level BLOCKED", $res8['risk_level'], 'BLOCKED');
assertTest("Test 8.2: Dangerous content has status BLOCKED", $res8['status'], 'BLOCKED');
assertTest("Test 8.3: Blocked content disables auto_publish", $res8['auto_publish'], false);

// Test 9: Platform Specific Policy Check (TikTok Zalo/Inbox spam)
$res9 = ComplianceGuardrail::evaluate(array(
    'content_text' => 'Anh em mua hàng inbox ngay nhắn tin zalo số lượng có hạn.',
    'platform' => 'tiktok'
));
assertTest("Test 9.1: TikTok external redirect spam flagged in platform_policy_risks", count($res9['platform_policy_risks']) > 0, true);

// Test 10: Backend JSON Schema Conformance (Section 20)
$keys = array_keys($res1);
$requiredKeys = array(
    'status', 'risk_level', 'platform', 'language', 'market', 'content_type',
    'auto_publish', 'issues', 'claims_requiring_evidence', 'copyright_risks',
    'privacy_risks', 'cultural_risks', 'platform_policy_risks',
    'affiliate_disclosure_required', 'ai_disclosure_required',
    'human_review_required', 'final_content', 'reason'
);
$hasAllKeys = true;
foreach ($requiredKeys as $rk) {
    if (!array_key_exists($rk, $res1)) {
        $hasAllKeys = false;
        break;
    }
}
assertTest("Test 10.1: Standard backend response schema conforms to Section 20", $hasAllKeys, true);

echo "\n==================================================\n";
echo "TEST RESULTS: {$passedTests}/{$totalTests} PASSED\n";
if ($passedTests === $totalTests) {
    echo "AI CONTENT & SOCIAL MEDIA COMPLIANCE GUARDRAIL VERIFICATION: 100% PASS\n";
} else {
    echo "AI COMPLIANCE GUARDRAIL VERIFICATION FAILED\n";
    exit(1);
}
