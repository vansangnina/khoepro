<?php
/**
 * KhoePro AI Content & Social Media Compliance Guardrail Engine
 * Strictly enforces 21 Compliance Guardrail Principles across AI Generation, SEO, Video, Script, and Social Publishing.
 * 
 * Safety-First Hierarchy:
 * SAFETY -> ACCURACY -> COMPLIANCE -> QUALITY -> DISTRIBUTION
 * 
 * Risk Tiers:
 * - LOW: Routine, platform-safe, passes all checks -> auto_publish: true (if configured)
 * - MEDIUM: Minor issues, auto-correctable/rewritable -> self-sanitized or REVIEW_REQUIRED
 * - HIGH: Medical claims, financial claims, children, unverified facts, copyright/PII -> REVIEW_REQUIRED, auto_publish: false
 * - BLOCKED: Illegal, dangerous, hateful, extreme violations -> BLOCKED, auto_publish: false
 * 
 * PHP 7.4 & 8.x Compatible
 */

class ComplianceGuardrail
{
    const RISK_LOW = 'LOW';
    const RISK_MEDIUM = 'MEDIUM';
    const RISK_HIGH = 'HIGH';
    const RISK_BLOCKED = 'BLOCKED';

    const STATUS_PASS = 'PASS';
    const STATUS_REWRITE = 'REWRITE';
    const STATUS_REVIEW_REQUIRED = 'REVIEW_REQUIRED';
    const STATUS_BLOCKED = 'BLOCKED';

    // Supported Platforms
    const PLATFORMS = array('tiktok', 'facebook', 'instagram', 'youtube', 'website', 'other');

    /**
     * Return Full Authoritative System Prompt text
     */
    public static function getSystemPrompt()
    {
        $guardrailFile = dirname(__DIR__, 2) . '/.ai/guardrails/AI-CONTENT-COMPLIANCE-GUARDRAIL.md';
        if (file_exists($guardrailFile)) {
            return (string)file_get_contents($guardrailFile);
        }

        return "SYSTEM PROMPT — AI CONTENT & SOCIAL MEDIA COMPLIANCE GUARDRAIL\n"
            . "1. VAI TRÒ: Tạo và kiểm soát nội dung, tuyệt đối không hy sinh an toàn, tuân thủ vì CTR hay viral.\n"
            . "2. NGUYÊN TẮC: AN TOÀN -> CHÍNH XÁC -> TUÂN THỦ -> CHẤT LƯỢNG -> TỐI ƯU PHÂN PHỐI.\n"
            . "3. CẤM: Bịa số liệu, tuyên bố chữa bệnh, review giả, vi phạm bản quyền, lộ PII, kích động nguy hiểm.\n"
            . "4. PHÂN LOẠI RỦI RO: LOW (cho phép publish), MEDIUM (sửa an toàn), HIGH (human review), BLOCKED (chặn hoàn toàn).\n"
            . "5. KHI NGHI NGỜ: WHEN IN DOUBT -> DO NOT AUTO-PUBLISH.\n";
    }

    /**
     * Main Compliance Evaluation Entrypoint
     * 
     * @param array $input [
     *   'content_text' => string,
     *   'structured_data' => array|string,
     *   'title' => string,
     *   'caption' => string,
     *   'hashtags' => string,
     *   'platform' => string (tiktok|facebook|instagram|youtube|website),
     *   'language' => string (default 'vi'),
     *   'market' => string (default 'VN'),
     *   'content_type' => string (article|seo|video_script|social_post|affiliate_review),
     *   'has_affiliate' => bool,
     *   'is_ai_generated' => bool,
     *   'evidence' => array,
     *   'has_real_test' => bool,
     *   'allow_auto_publish_low' => bool
     * ]
     * @return array Standard Section 20 JSON Output
     */
    public static function evaluate(array $input)
    {
        $platform = strtolower($input['platform'] ?? 'website');
        if (!in_array($platform, self::PLATFORMS, true)) {
            $platform = 'other';
        }
        $language = $input['language'] ?? 'vi';
        $market = $input['market'] ?? 'VN';
        $contentType = $input['content_type'] ?? 'general';
        $hasAffiliate = !empty($input['has_affiliate']);
        $isAiGenerated = isset($input['is_ai_generated']) ? (bool)$input['is_ai_generated'] : true;
        $hasRealTest = !empty($input['has_real_test']);
        $allowAutoLow = isset($input['allow_auto_publish_low']) ? (bool)$input['allow_auto_publish_low'] : true;

        // Collect all text surfaces to scan
        $textFragments = array();
        if (!empty($input['content_text'])) $textFragments[] = (string)$input['content_text'];
        if (!empty($input['title'])) $textFragments[] = (string)$input['title'];
        if (!empty($input['caption'])) $textFragments[] = (string)$input['caption'];
        if (!empty($input['hashtags'])) $textFragments[] = (string)$input['hashtags'];
        if (!empty($input['structured_data'])) {
            if (is_array($input['structured_data'])) {
                $textFragments[] = json_encode($input['structured_data'], JSON_UNESCAPED_UNICODE);
            } else {
                $textFragments[] = (string)$input['structured_data'];
            }
        }

        $fullScanText = implode("\n", $textFragments);
        $normalizedText = mb_strtolower($fullScanText, 'UTF-8');

        // Violation containers
        $issues = array();
        $claimsRequiringEvidence = array();
        $copyrightRisks = array();
        $privacyRisks = array();
        $culturalRisks = array();
        $platformPolicyRisks = array();

        $riskLevel = self::RISK_LOW;
        $status = self::STATUS_PASS;
        $reasons = array();

        // ----------------------------------------------------
        // 1. DANGEROUS & ILLEGAL CONTENT (BLOCKED)
        // ----------------------------------------------------
        $dangerousPatterns = array(
            'chế tạo bom', 'chất nổ', 'tự tử', 'tự hại bản thân', 'tự sát',
            'mua bán ma túy', 'chất cấm', 'thuốc kích dục', 'cờ bạc online',
            'hack tài khoản', 'bẻ khóa tài khoản', 'rửa tiền', 'lừa đảo chiếm đoạt'
        );
        foreach ($dangerousPatterns as $dp) {
            if (mb_stripos($normalizedText, $dp) !== false) {
                $issues[] = "Nội dung nguy hiểm / bất hợp pháp bị cấm hoàn toàn: '{$dp}'";
                $riskLevel = self::RISK_BLOCKED;
                $status = self::STATUS_BLOCKED;
                $reasons[] = "Nội dung vi phạm nghiêm trọng an toàn và pháp luật ({$dp})";
            }
        }

        // ----------------------------------------------------
        // 2. PRIVACY & PII RISKS (HIGH / BLOCKED)
        // ----------------------------------------------------
        if (preg_match('/(\b0[3|5|7|8|9][0-9]{8}\b)/', $fullScanText, $phoneMatch)) {
            $privacyRisks[] = "Phát hiện số điện thoại cá nhân không được ẩn danh: {$phoneMatch[0]}";
            if ($riskLevel !== self::RISK_BLOCKED) $riskLevel = self::RISK_HIGH;
        }
        if (preg_match('/((cccd|cmnd|căn cước|hộ chiếu)(\s*số)?\s*[:=]?\s*[0-9]{9,12}|\b[0-9]{9,12}\b\s*(cccd|cmnd|căn cước))/i', $fullScanText, $idMatch)) {
            $privacyRisks[] = "Phát hiện thông tin giấy tờ tùy thân / CCCD: {$idMatch[0]}";
            if ($riskLevel !== self::RISK_BLOCKED) $riskLevel = self::RISK_HIGH;
        }
        if (preg_match('/(stk|số tài khoản)\s*[:=]?\s*[0-9]{6,16}/i', $fullScanText, $bankMatch)) {
            $privacyRisks[] = "Phát hiện thông tin tài khoản ngân hàng riêng tư";
            if ($riskLevel !== self::RISK_BLOCKED) $riskLevel = self::RISK_HIGH;
        }

        // ----------------------------------------------------
        // 3. CULTURAL, HARASSMENT & BODY SHAMING RISKS (HIGH / BLOCKED)
        // ----------------------------------------------------
        $culturalViolations = array(
            'đồ béo phì', 'đồ mập địch', 'xấu ma chê quỷ hờn', 'đồ ngu ngốc',
            'dân tộc mọi rợ', 'lũ hạ đẳng', 'bọn mọi'
        );
        foreach ($culturalViolations as $cv) {
            if (mb_stripos($normalizedText, $cv) !== false) {
                $culturalRisks[] = "Phát hiện ngôn từ miệt thị ngoại hình, xúc phạm hoặc thù ghét: '{$cv}'";
                if ($riskLevel !== self::RISK_BLOCKED) $riskLevel = self::RISK_BLOCKED;
                $status = self::STATUS_BLOCKED;
            }
        }

        // ----------------------------------------------------
        // 4. HEALTH & MEDICAL CURE CLAIMS (HIGH)
        // ----------------------------------------------------
        $strictMedicalClaims = array(
            'chữa khỏi hoàn toàn', 'chữa dứt điểm', 'điều trị dứt điểm', 'thay thế thuốc chữa bệnh',
            'cam kết chữa khỏi', 'trị dứt điểm đau lưng', 'chữa khỏi thoát vị', 'chữa bách bệnh',
            'cam kết giảm 10kg trong 7 ngày', 'giảm cân cấp tốc không cần tập', 'cam kết tăng 10kg cơ'
        );
        foreach ($strictMedicalClaims as $med) {
            if (mb_stripos($normalizedText, $med) !== false) {
                $claimsRequiringEvidence[] = "Tuyên bố y tế / cam kết điều trị không được phép: '{$med}'";
                $issues[] = "Tuyên bố điều trị y khoa / cam kết kết quả sức khỏe không có căn cứ ({$med})";
                if ($riskLevel !== self::RISK_BLOCKED) $riskLevel = self::RISK_HIGH;
                $reasons[] = "Tuyên bố y tế/sức khỏe tuyệt đối cần kiểm tra y khoa";
            }
        }

        // ----------------------------------------------------
        // 5. FAKE EXPERIENCE & UNVERIFIED CLAIMS (HIGH)
        // ----------------------------------------------------
        $firstPersonClaims = array(
            'tôi đã dùng 30 ngày', 'mình đã dùng thử', 'tôi đã mua và test',
            'trải nghiệm 1 tháng của mình', 'tôi cam đoan 100%'
        );
        if (!$hasRealTest) {
            foreach ($firstPersonClaims as $fpc) {
                if (mb_stripos($normalizedText, $fpc) !== false) {
                    $claimsRequiringEvidence[] = "Xưng trải nghiệm cá nhân thực tế ('{$fpc}') khi chưa có hồ sơ REAL_TEST";
                    $issues[] = "Tự tạo trải nghiệm cá nhân không có dữ liệu nguồn";
                    if ($riskLevel !== self::RISK_BLOCKED) $riskLevel = self::RISK_HIGH;
                }
            }
        }

        // ----------------------------------------------------
        // 6. CLICKBAIT & ABSOLUTE SUPERLATIVES (MEDIUM)
        // ----------------------------------------------------
        $clickbaitPatterns = array(
            '100% hiệu quả', 'chắc chắn thành công', 'tốt nhất thị trường',
            'số 1 thế giới', 'cam kết khỏi', 'không có rủi ro', 'ai cũng thành công',
            'kiếm tiền chắc chắn', 'bảo đảm 100%'
        );
        $foundClickbaits = array();
        foreach ($clickbaitPatterns as $cb) {
            if (mb_stripos($normalizedText, $cb) !== false) {
                $foundClickbaits[] = $cb;
                $issues[] = "Cụm từ giật gân/tuyên bố tuyệt đối không có chứng thực: '{$cb}'";
            }
        }
        if (!empty($foundClickbaits) && $riskLevel === self::RISK_LOW) {
            $riskLevel = self::RISK_MEDIUM;
            $status = self::STATUS_REWRITE;
            $reasons[] = "Có yếu tố clickbait/tuyên bố tuyệt đối cần điều chỉnh sang ngôn ngữ trung lập";
        }

        // ----------------------------------------------------
        // 7. PLATFORM SPECIFIC POLICY COMPLIANCE
        // ----------------------------------------------------
        if ($platform === 'tiktok') {
            // TikTok strictly restricts direct external redirect spam, unauthorized logos, unverified affiliate claims
            if (preg_match('/(inbox ngay|nhắn tin zalo|add zalo|zalo:?\s*[0-9]+)/i', $fullScanText)) {
                $platformPolicyRisks[] = "TikTok: Hạn chế kêu gọi dẫn luồng sang Zalo/inbox ngoài nền tảng";
                if ($riskLevel === self::RISK_LOW) $riskLevel = self::RISK_MEDIUM;
            }
        } elseif ($platform === 'facebook' || $platform === 'instagram') {
            // Meta policy restricts before-after weight loss imagery claims, exaggerated personal attributes
            if (preg_match('/(trước và sau khi|before and after|ảnh trước sau)/i', $fullScanText)) {
                $platformPolicyRisks[] = "Meta/Facebook/Instagram: Lưu ý chính sách hình ảnh/tuyên bố Before/After giảm cân";
            }
        }

        // ----------------------------------------------------
        // 8. AFFILIATE & COMMERCIAL DISCLOSURE REQUIREMENTS
        // ----------------------------------------------------
        $affiliateDisclosureRequired = false;
        if ($hasAffiliate || $contentType === 'affiliate_review') {
            $affiliateDisclosureRequired = true;
            $hasDisclosureText = (
                mb_stripos($normalizedText, 'affiliate') !== false ||
                mb_stripos($normalizedText, 'tài trợ') !== false ||
                mb_stripos($normalizedText, 'đối tác') !== false ||
                mb_stripos($normalizedText, 'hoa hồng') !== false ||
                mb_stripos($normalizedText, 'quảng cáo') !== false ||
                mb_stripos($normalizedText, 'liên kết') !== false
            );
            if (!$hasDisclosureText) {
                $issues[] = "Nội dung affiliate/thương mại thiếu câu công khai quan hệ tiếp thị (Affiliate Disclosure)";
                if ($riskLevel === self::RISK_LOW) {
                    $riskLevel = self::RISK_MEDIUM;
                    $status = self::STATUS_REWRITE;
                }
            }
        }

        // ----------------------------------------------------
        // 9. AI / SYNTHETIC MEDIA DISCLOSURE
        // ----------------------------------------------------
        $aiDisclosureRequired = $isAiGenerated;

        // ----------------------------------------------------
        // 10. FINAL RISK CLASSIFICATION & DECISION
        // ----------------------------------------------------
        if (!empty($privacyRisks) || !empty($culturalRisks)) {
            if ($riskLevel !== self::RISK_BLOCKED) {
                $riskLevel = self::RISK_HIGH;
            }
        }

        $humanReviewRequired = false;
        $autoPublish = false;

        if ($riskLevel === self::RISK_BLOCKED) {
            $status = self::STATUS_BLOCKED;
            $humanReviewRequired = false;
            $autoPublish = false;
        } elseif ($riskLevel === self::RISK_HIGH) {
            $status = self::STATUS_REVIEW_REQUIRED;
            $humanReviewRequired = true;
            $autoPublish = false;
        } elseif ($riskLevel === self::RISK_MEDIUM) {
            $status = self::STATUS_REWRITE;
            $humanReviewRequired = false;
            $autoPublish = false; // Requires rewriting before publish
        } else {
            // LOW RISK
            $status = self::STATUS_PASS;
            $humanReviewRequired = false;
            $autoPublish = $allowAutoLow ? true : false;
        }

        $finalContent = $input['content_text'] ?? ($input['caption'] ?? '');

        return array(
            'status' => $status,
            'risk_level' => $riskLevel,
            'platform' => $platform,
            'language' => $language,
            'market' => $market,
            'content_type' => $contentType,
            'auto_publish' => $autoPublish,
            'issues' => array_values(array_unique($issues)),
            'claims_requiring_evidence' => array_values(array_unique($claimsRequiringEvidence)),
            'copyright_risks' => array_values(array_unique($copyrightRisks)),
            'privacy_risks' => array_values(array_unique($privacyRisks)),
            'cultural_risks' => array_values(array_unique($culturalRisks)),
            'platform_policy_risks' => array_values(array_unique($platformPolicyRisks)),
            'affiliate_disclosure_required' => $affiliateDisclosureRequired,
            'ai_disclosure_required' => $aiDisclosureRequired,
            'human_review_required' => $humanReviewRequired,
            'final_content' => $finalContent,
            'reason' => !empty($reasons) ? implode('; ', $reasons) : ($status === self::STATUS_PASS ? 'Nội dung đạt chuẩn an toàn và tuân thủ pháp lý' : 'Cần kiểm duyệt bổ sung')
        );
    }

    /**
     * Auto-Sanitize & Neutralize Medium Risk Text (Anti-Clickbait & Objective Phrasing)
     */
    public static function sanitizeText($text, $hasAffiliate = false)
    {
        $replacements = array(
            '/100% hiệu quả/iu' => 'hỗ trợ tối ưu theo thiết kế',
            '/chắc chắn thành công/iu' => 'hỗ trợ quá trình tập luyện',
            '/tốt nhất thị trường/iu' => 'thuộc phân khúc chất lượng cao',
            '/số 1 thế giới/iu' => 'được nhiều người tập lựa chọn',
            '/cam kết khỏi/iu' => 'hỗ trợ cải thiện tư thế',
            '/không có rủi ro/iu' => 'đáp ứng tiêu chuẩn an toàn',
            '/ai cũng thành công/iu' => 'phù hợp với đa số người tập',
            '/kiếm tiền chắc chắn/iu' => 'tiết kiệm chi phí đầu tư',
            '/giảm (\d+)kg chắc chắn/iu' => 'hỗ trợ mục tiêu kiểm soát cân nặng',
            '/được chuyên gia khuyên dùng/iu' => 'dựa trên thông số tiêu chuẩn công bố'
        );

        $sanitized = preg_replace(array_keys($replacements), array_values($replacements), $text);

        if ($hasAffiliate && mb_stripos($sanitized, 'Affiliate') === false && mb_stripos($sanitized, 'đối tác') === false) {
            $sanitized .= "\n\n*(Thông tin sản phẩm dựa trên công bố từ nhà sản xuất và đối tác liên kết KhoePro)*";
        }

        return $sanitized;
    }
}
