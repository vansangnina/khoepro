<?php
/**
 * FITNADO - AI Content Engine (Phase 05)
 * Research -> Approved Product -> Content Package -> Quality Gate -> Human Approval -> Apply to Product
 * PHP 7.4 Compatible
 */

if (!class_exists('AIResearchAgent')) {
    require_once __DIR__ . '/class.AIResearchAgent.php';
}
if (!class_exists('ProductResearch')) {
    require_once __DIR__ . '/class.ProductResearch.php';
}

class AIContentEngine
{
    private $d;
    private $func;
    private $aiAgent;

    public function __construct($d = null, $func = null, $aiAgent = null)
    {
        $this->d = $d;
        $this->func = $func;
        $this->aiAgent = $aiAgent ?: new AIResearchAgent($d, $func);
    }

    /**
     * Aggregate all verified real data for a product
     */
    public function aggregateProductData($productId)
    {
        if (!$this->d) return array();

        $product = $this->d->rawQueryOne("select * from #_product where id = ? limit 0,1", array((int)$productId));
        if (empty($product)) return array();

        // 1. Research candidate data
        $research = $this->d->rawQueryOne(
            "select * from #_product_research where id_product = ? or external_product_id = ? order by id desc limit 0,1",
            array((int)$productId, $product['code'] ?? '')
        );

        // 2. Fact Evidence
        $evidence = array();
        if (!empty($research['id'])) {
            $evidence = $this->d->rawQuery(
                "select * from #_product_research_evidence where id_research = ? order by id asc",
                array((int)$research['id'])
            );
        }

        // 3. Affiliate Offers
        $affiliates = $this->d->rawQuery(
            "select * from #_product_affiliate where id_product = ? and find_in_set('hienthi', status) order by is_best_deal desc, priority asc",
            array((int)$productId)
        );

        // 4. Category & Brand Taxonomy
        $categoryName = '';
        if (!empty($product['id_list'])) {
            $cat = $this->d->rawQueryOne("select namevi from #_product_list where id = ? limit 0,1", array((int)$product['id_list']));
            $categoryName = $cat['namevi'] ?? '';
        }

        $brandName = '';
        if (!empty($product['id_brand'])) {
            $br = $this->d->rawQueryOne("select namevi from #_product_brand where id = ? limit 0,1", array((int)$product['id_brand']));
            $brandName = $br['namevi'] ?? '';
        }

        // 5. Approved Real User Reviews
        $reviews = $this->d->rawQuery(
            "select star, title, content, fullname from #_comment where id_variant = ? and find_in_set('hienthi', status) and (id_parent = 0 or id_parent is null) order by id desc limit 0,5",
            array((int)$productId)
        );

        return array(
            'product' => $product,
            'research' => $research ?: array(),
            'evidence' => $evidence ?: array(),
            'affiliates' => $affiliates ?: array(),
            'category_name' => $categoryName,
            'brand_name' => $brandName,
            'reviews' => $reviews ?: array()
        );
    }

    /**
     * Deterministic SHA-256 Source Hash
     * Computed only on content-affecting factual inputs
     */
    public function generateSourceHash(array $aggregatedData)
    {
        $prod = $aggregatedData['product'] ?? array();
        $res = $aggregatedData['research'] ?? array();
        $affs = $aggregatedData['affiliates'] ?? array();
        $evs = $aggregatedData['evidence'] ?? array();

        $factTokens = array(
            'name' => trim(mb_strtolower($prod['namevi'] ?? '')),
            'brand' => trim(mb_strtolower($aggregatedData['brand_name'] ?? '')),
            'category' => trim(mb_strtolower($aggregatedData['category_name'] ?? '')),
            'price' => (float)($prod['sale_price'] ?: ($prod['regular_price'] ?? 0)),
            'specs' => trim($prod['specs'] ?? ''),
            'problem_solved' => trim($res['problem_solved'] ?? ''),
            'target_audience' => trim($res['target_audience'] ?? ''),
            'primary_keyword' => trim($res['primary_keyword'] ?? ''),
            'demand_score' => (float)($res['demand_score'] ?? 0),
            'affiliate_count' => count($affs),
            'evidence_count' => count($evs)
        );

        return hash('sha256', json_encode($factTokens, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Generate specific AI content with Quality Gate & Versioning
     */
    public function generateContent($productId, $contentType, array $options = array())
    {
        if (!$this->d) {
            return array('status' => false, 'error' => 'Database connection missing');
        }

        $aggregated = $this->aggregateProductData($productId);
        if (empty($aggregated['product'])) {
            return array('status' => false, 'error' => 'Product not found: ID #' . $productId);
        }

        $product = $aggregated['product'];
        $research = $aggregated['research'];
        $language = $options['language'] ?? 'vi';
        $tone = $options['tone'] ?? 'FITNADO_DEFAULT';
        $targetDuration = isset($options['target_duration']) ? (int)$options['target_duration'] : 30;
        $contentAngle = $options['content_angle'] ?? 'Problem/Solution';
        $sourceHash = $this->generateSourceHash($aggregated);

        // Build Prompt and Schema according to content_type
        $promptConfig = $this->buildPromptAndSchema($contentType, $aggregated, array(
            'language' => $language,
            'tone' => $tone,
            'target_duration' => $targetDuration,
            'content_angle' => $contentAngle
        ));

        if (empty($promptConfig)) {
            return array('status' => false, 'error' => 'Unsupported content type: ' . $contentType);
        }

        // Generate response from AI Provider
        $startTime = microtime(true);
        $aiRes = $this->aiAgent->generateStructuredResponse(
            $promptConfig['prompt'],
            $promptConfig['schema'],
            array('system_instruction' => $promptConfig['system_instruction'])
        );
        $duration = round(microtime(true) - $startTime, 2);

        if (!$aiRes['status'] || empty($aiRes['data'])) {
            return array(
                'status' => false,
                'error' => 'AI Generation failed: ' . ($aiRes['error'] ?? 'Invalid response')
            );
        }

        $generatedData = $aiRes['data'];

        // Quality Gate Validation
        $qualityCheck = $this->validateQualityGate($contentType, $generatedData, $aggregated);

        // Format readable content text
        $formattedText = $this->formatContentText($contentType, $generatedData, $options);

        // Versioning: determine next version number
        $lastVer = $this->d->rawQueryOne(
            "select max(version) as max_v from #_ai_content where id_product = ? and content_type = ? and language = ?",
            array((int)$productId, $contentType, $language)
        );
        $nextVersion = !empty($lastVer['max_v']) ? ((int)$lastVer['max_v'] + 1) : 1;

        // Prepare token usage metadata
        $tokenUsage = array(
            'duration' => $duration,
            'provider' => $aiRes['provider'] ?? 'mock',
            'model' => $aiRes['model'] ?? 'gemini-1.5-flash',
            'prompt_version' => $promptConfig['prompt_version']
        );

        $contentRecord = array(
            'id_product' => (int)$productId,
            'id_research' => !empty($research['id']) ? (int)$research['id'] : null,
            'content_type' => $contentType,
            'title' => $this->generateTitle($contentType, $product['namevi'], $options),
            'language' => $language,
            'tone' => $tone,
            'target_duration' => ($contentType === 'tiktok_script') ? $targetDuration : null,
            'content_angle' => $contentAngle,
            'content_text' => $formattedText,
            'structured_data' => json_encode($generatedData, JSON_UNESCAPED_UNICODE),
            'version' => $nextVersion,
            'is_active' => 0,
            'status' => $qualityCheck['passed'] ? 'REVIEW_REQUIRED' : 'REJECTED',
            'reject_reason' => !$qualityCheck['passed'] ? implode('; ', $qualityCheck['flags']) : null,
            'source_hash' => $sourceHash,
            'is_outdated' => 0,
            'provider' => $aiRes['provider'] ?? 'mock',
            'model' => $aiRes['model'] ?? 'gemini-1.5-flash',
            'prompt_version' => $promptConfig['prompt_version'],
            'token_usage' => json_encode($tokenUsage, JSON_UNESCAPED_UNICODE),
            'quality_check' => json_encode($qualityCheck, JSON_UNESCAPED_UNICODE),
            'date_created' => time(),
            'date_updated' => time()
        );

        $insertId = $this->d->insert('ai_content', $contentRecord);

        if ($insertId) {
            return array(
                'status' => true,
                'content_id' => $insertId,
                'version' => $nextVersion,
                'content_type' => $contentType,
                'quality_check' => $qualityCheck,
                'duration' => $duration,
                'data' => $generatedData
            );
        }

        return array('status' => false, 'error' => 'Failed to save content to database');
    }

    /**
     * Rule-based Quality Gate Validator
     * Strictly enforces Factual Integrity (no fake metrics, fake reviews, fake test claims, or medical promises)
     */
    public function validateQualityGate($contentType, array $data, array $aggregatedData)
    {
        $flags = array();
        $passed = true;

        $hasRealTest = false;
        if (!empty($aggregatedData['evidence'])) {
            foreach ($aggregatedData['evidence'] as $ev) {
                if (($ev['evidence_type'] ?? '') === 'REAL_TEST') {
                    $hasRealTest = true;
                    break;
                }
            }
        }

        $jsonStr = json_encode($data, JSON_UNESCAPED_UNICODE);

        // 1. Check for Fake Personal Testing Claims without REAL_TEST evidence
        $firstPersonClaims = array(
            'tôi đã dùng', 'mình đã test', 'tôi đã thử', 'mình đã trải nghiệm',
            'fitnado đã test', 'chúng tôi đã dùng thử', 'kinh nghiệm 30 ngày dùng của tôi'
        );
        if (!$hasRealTest) {
            foreach ($firstPersonClaims as $claim) {
                if (mb_stripos($jsonStr, $claim) !== false) {
                    $flags[] = "Phát hiện tự nhận trải nghiệm cá nhân/test thực tế ('{$claim}') khi chưa có bằng chứng REAL_TEST trong hệ thống";
                    $passed = false;
                }
            }
        }

        // 2. Check for Illegal Medical Promises
        $medicalClaims = array(
            'chữa đau lưng', 'điều trị thoát vị', 'chữa dứt điểm', 'trị dứt điểm chấn thương',
            'chữa khỏi', 'cam kết tăng 5kg cơ', 'cam kết giảm 10kg'
        );
        foreach ($medicalClaims as $med) {
            if (mb_stripos($jsonStr, $med) !== false) {
                $flags[] = "Phát hiện cam kết y khoa/điều trị chấn thương không được phép ('{$med}')";
                $passed = false;
            }
        }

        // 3. Check for Fake Customer Testimonials
        if (empty($aggregatedData['reviews'])) {
            $testimonialPatterns = array(
                'anh nam chia sẻ:', 'chị lan đánh giá:', 'khách hàng review 5 sao:', 'người mua nhận xét:'
            );
            foreach ($testimonialPatterns as $tp) {
                if (mb_stripos($jsonStr, $tp) !== false) {
                    $flags[] = "Phát hiện trích dẫn nhận xét người dùng giả định ('{$tp}') khi chưa có review thực tế trong database";
                    $passed = false;
                }
            }
        }

        // 4. Check for Invented Exact Ratings / Sales Count in Product Analysis
        if ($contentType === 'product_analysis') {
            if (isset($data['rating']) && empty($aggregatedData['research']['rating'])) {
                $flags[] = "AI tự ý bổ sung số liệu đánh giá sao (rating) không có trong dữ liệu nguồn";
                $passed = false;
            }
            if (isset($data['sales_count']) && empty($aggregatedData['research']['sales_count'])) {
                $flags[] = "AI tự ý bổ sung số liệu lượt bán (sales_count) không có trong dữ liệu nguồn";
                $passed = false;
            }
        }

        return array(
            'passed' => $passed,
            'flags' => $flags,
            'checked_at' => time()
        );
    }

    /**
     * Approve Content Version and set it as Active Version
     */
    public function approveContent($contentId)
    {
        if (!$this->d) return false;

        $content = $this->d->rawQueryOne("select * from #_ai_content where id = ? limit 0,1", array((int)$contentId));
        if (empty($content)) return false;

        // Deactivate all previous versions of this (id_product, content_type, language)
        $this->d->rawQuery(
            "update #_ai_content set is_active = 0, date_updated = ? where id_product = ? and content_type = ? and language = ?",
            array(time(), (int)$content['id_product'], $content['content_type'], $content['language'])
        );

        // Activate and approve this version
        return $this->d->rawQuery(
            "update #_ai_content set status = 'APPROVED', is_active = 1, reject_reason = null, date_updated = ? where id = ?",
            array(time(), (int)$contentId)
        );
    }

    /**
     * Reject Content Version
     */
    public function rejectContent($contentId, $reason = '')
    {
        if (!$this->d) return false;

        return $this->d->rawQuery(
            "update #_ai_content set status = 'REJECTED', is_active = 0, reject_reason = ?, date_updated = ? where id = ?",
            array(trim($reason) ?: 'Không đạt tiêu chuẩn kiểm duyệt của ban biên tập', time(), (int)$contentId)
        );
    }

    /**
     * Apply Approved Content to Product with Automatic Reversible Backup
     */
    public function applyToProduct($contentId, $adminUser = 'admin')
    {
        if (!$this->d) return array('status' => false, 'error' => 'Database connection missing');

        $content = $this->d->rawQueryOne("select * from #_ai_content where id = ? limit 0,1", array((int)$contentId));
        if (empty($content)) {
            return array('status' => false, 'error' => 'Content record not found');
        }

        if ($content['status'] !== 'APPROVED') {
            return array('status' => false, 'error' => 'Chỉ có thể áp dụng nội dung đã được duyệt (APPROVED)');
        }

        $productId = (int)$content['id_product'];
        $product = $this->d->rawQueryOne("select * from #_product where id = ? limit 0,1", array($productId));
        if (empty($product)) {
            return array('status' => false, 'error' => 'Product not found');
        }

        $structured = !empty($content['structured_data']) ? json_decode($content['structured_data'], true) : array();
        if (!is_array($structured)) $structured = array();

        $updatedFields = array();

        // 1. Apply Product Analysis (Pros, Cons, Verdict, Best For, Summary)
        if ($content['content_type'] === 'product_analysis') {
            $pros = !empty($structured['pros']) ? (is_array($structured['pros']) ? implode("\n", $structured['pros']) : $structured['pros']) : $product['expert_pros'];
            $cons = !empty($structured['cons']) ? (is_array($structured['cons']) ? implode("\n", $structured['cons']) : $structured['cons']) : $product['expert_cons'];
            $verdict = !empty($structured['buying_considerations']) ? (is_array($structured['buying_considerations']) ? implode('. ', $structured['buying_considerations']) : $structured['buying_considerations']) : $product['verdict'];
            $bestFor = !empty($structured['suitable_for']) ? (is_array($structured['suitable_for']) ? implode(', ', $structured['suitable_for']) : $structured['suitable_for']) : $product['best_for'];
            $desc = !empty($structured['problem_solved']) ? $structured['problem_solved'] : $product['descvi'];

            // Backup Old Values
            $this->backupField($productId, $contentId, 'expert_pros', $product['expert_pros'], $pros, $adminUser);
            $this->backupField($productId, $contentId, 'expert_cons', $product['expert_cons'], $cons, $adminUser);
            $this->backupField($productId, $contentId, 'verdict', $product['verdict'], $verdict, $adminUser);
            $this->backupField($productId, $contentId, 'best_for', $product['best_for'], $bestFor, $adminUser);
            $this->backupField($productId, $contentId, 'descvi', $product['descvi'], $desc, $adminUser);

            $this->d->rawQuery(
                "update #_product set expert_pros = ?, expert_cons = ?, verdict = ?, best_for = ?, descvi = ?, date_updated = ? where id = ?",
                array($pros, $cons, $verdict, $bestFor, $desc, time(), $productId)
            );
            $updatedFields = array('expert_pros', 'expert_cons', 'verdict', 'best_for', 'descvi');
        }

        // 2. Apply Review Draft (contentvi)
        if ($content['content_type'] === 'review_draft') {
            $reviewHtml = !empty($structured['article_body']) ? $structured['article_body'] : $content['content_text'];

            $this->backupField($productId, $contentId, 'contentvi', $product['contentvi'], $reviewHtml, $adminUser);

            $this->d->rawQuery(
                "update #_product set contentvi = ?, date_updated = ? where id = ?",
                array($reviewHtml, time(), $productId)
            );
            $updatedFields = array('contentvi');
        }

        // 3. Apply SEO Metadata (table_seo)
        if ($content['content_type'] === 'seo_content') {
            $seoTitle = $structured['seo_title'] ?? ($product['namevi'] . ' - Đánh Giá & Mua Giá Tốt FITNADO');
            $seoDesc = $structured['seo_description'] ?? $product['descvi'];
            $seoKeywords = !empty($structured['keywords']) ? (is_array($structured['keywords']) ? implode(', ', $structured['keywords']) : $structured['keywords']) : '';

            $currentSeo = $this->d->rawQueryOne("select * from #_seo where id_parent = ? and com = 'product' and act = 'man' and type = 'san-pham' limit 0,1", array($productId));

            $oldTitle = $currentSeo['titlevi'] ?? '';
            $oldDesc = $currentSeo['descriptionvi'] ?? '';
            $oldKeywords = $currentSeo['keywordsvi'] ?? '';

            $this->backupField($productId, $contentId, 'seo_titlevi', $oldTitle, $seoTitle, $adminUser);
            $this->backupField($productId, $contentId, 'seo_descriptionvi', $oldDesc, $seoDesc, $adminUser);
            $this->backupField($productId, $contentId, 'seo_keywordsvi', $oldKeywords, $seoKeywords, $adminUser);

            if (!empty($currentSeo['id'])) {
                $this->d->rawQuery(
                    "update #_seo set titlevi = ?, descriptionvi = ?, keywordsvi = ? where id = ?",
                    array($seoTitle, $seoDesc, $seoKeywords, $currentSeo['id'])
                );
            } else {
                $this->d->insert('seo', array(
                    'id_parent' => $productId,
                    'com' => 'product',
                    'act' => 'man',
                    'type' => 'san-pham',
                    'titlevi' => $seoTitle,
                    'descriptionvi' => $seoDesc,
                    'keywordsvi' => $seoKeywords
                ));
            }
            $updatedFields = array('seo_titlevi', 'seo_descriptionvi', 'seo_keywordsvi');
        }

        // Mark content as APPLIED
        $this->d->rawQuery(
            "update #_ai_content set status = 'APPLIED', applied_at = ?, applied_by = ?, applied_target = ?, date_updated = ? where id = ?",
            array(time(), $adminUser, implode(', ', $updatedFields), time(), (int)$contentId)
        );

        return array(
            'status' => true,
            'message' => 'Nội dung đã được áp dụng thành công vào sản phẩm và backup an toàn',
            'updated_fields' => $updatedFields
        );
    }

    /**
     * Helper to store backup field record
     */
    private function backupField($productId, $contentId, $fieldName, $oldVal, $newVal, $adminUser)
    {
        return $this->d->insert('product_content_backup', array(
            'id_product' => (int)$productId,
            'id_content' => (int)$contentId,
            'field_name' => $fieldName,
            'old_value' => $oldVal,
            'new_value' => $newVal,
            'backup_reason' => 'APPLY_AI_CONTENT',
            'created_by' => $adminUser,
            'date_created' => time()
        ));
    }

    /**
     * Check if product content is outdated due to input research data changes
     */
    public function checkOutdatedContent($productId)
    {
        if (!$this->d) return false;

        $aggregated = $this->aggregateProductData($productId);
        if (empty($aggregated['product'])) return false;

        $currentHash = $this->generateSourceHash($aggregated);

        // Find contents whose source_hash does not match
        $contents = $this->d->rawQuery(
            "select id, source_hash from #_ai_content where id_product = ?",
            array((int)$productId)
        );

        $outdatedCount = 0;
        foreach ($contents as $c) {
            if ($c['source_hash'] !== $currentHash) {
                $this->d->rawQuery("update #_ai_content set is_outdated = 1, date_updated = ? where id = ?", array(time(), $c['id']));
                $outdatedCount++;
            } else {
                $this->d->rawQuery("update #_ai_content set is_outdated = 0, date_updated = ? where id = ?", array(time(), $c['id']));
            }
        }

        return $outdatedCount;
    }

    /**
     * Check if product meets minimum criteria for CONTENT_READY
     * Criteria: Approved Product Analysis + At least one Approved TikTok Script
     */
    public function isProductContentReady($productId)
    {
        if (!$this->d) return false;

        $analysis = $this->d->rawQueryOne(
            "select id from #_ai_content where id_product = ? and content_type = 'product_analysis' and status in ('APPROVED', 'APPLIED') limit 0,1",
            array((int)$productId)
        );

        $tiktokScript = $this->d->rawQueryOne(
            "select id from #_ai_content where id_product = ? and content_type = 'tiktok_script' and status in ('APPROVED', 'APPLIED') limit 0,1",
            array((int)$productId)
        );

        return (!empty($analysis) && !empty($tiktokScript));
    }

    /**
     * Check if product meets criteria for SEO_READY
     * Criteria: Approved SEO metadata + Approved Review Draft or Buying Guide
     */
    public function isProductSeoReady($productId)
    {
        if (!$this->d) return false;

        $seo = $this->d->rawQueryOne(
            "select id from #_ai_content where id_product = ? and content_type = 'seo_content' and status in ('APPROVED', 'APPLIED') limit 0,1",
            array((int)$productId)
        );

        $review = $this->d->rawQueryOne(
            "select id from #_ai_content where id_product = ? and content_type in ('review_draft', 'buying_guide') and status in ('APPROVED', 'APPLIED') limit 0,1",
            array((int)$productId)
        );

        return (!empty($seo) && !empty($review));
    }

    /**
     * Helper to build prompt and JSON Schema per content_type
     */
    private function buildPromptAndSchema($contentType, array $aggregated, array $options)
    {
        $product = $aggregated['product'];
        $research = $aggregated['research'];
        $evidence = $aggregated['evidence'];
        $affiliates = $aggregated['affiliates'];

        $productName = $product['namevi'] ?? '';
        $category = $aggregated['category_name'] ?: ($research['category_hint'] ?? 'Gym & Fitness');
        $brand = $aggregated['brand_name'] ?: ($research['brand_hint'] ?? '');
        $price = (float)($product['sale_price'] ?: ($product['regular_price'] ?? 0));
        $tone = $options['tone'] ?? 'FITNADO_DEFAULT';
        $duration = $options['target_duration'] ?? 30;
        $angle = $options['content_angle'] ?? 'Problem/Solution';

        $evidenceFacts = array();
        foreach ($evidence as $ev) {
            if (($ev['evidence_type'] ?? '') === 'FACT') {
                $evidenceFacts[] = "{$ev['field_name']}: {$ev['field_value']}";
            }
        }
        $evidenceText = !empty($evidenceFacts) ? implode("; ", $evidenceFacts) : "Không có bằng chứng sàn bổ sung";

        $systemInstruction = "Bạn là Giám đốc Sáng tạo Nội dung Gym/Fitness của FITNADO.\n"
            . "NGUYÊN TẮC BẮT BUỘC:\n"
            . "1. DỮ LIỆU THỰC TẾ LÀ CHÂN LÝ: Không tự bịa đặt thông số kỹ thuật, lượt bán, đánh giá sao, giấy chứng nhận y tế.\n"
            . "2. CẤM XƯNG TRẢI NGHIỆM CÁ NHÂN: Tuyệt đối không tự xưng 'Tôi đã test 30 ngày', 'Mình đã dùng...' trừ khi hệ thống có bằng chứng REAL_TEST.\n"
            . "3. CẤM FAKE REVIEW: Không bịa lời chứng thực hay trích dẫn khách hàng giả định.\n"
            . "4. AN TOÀN Y KHOA: Không cam kết chữa bệnh, chữa đau lưng, trị thoát vị.\n"
            . "5. TRẢ VỀ JSON THUẦN TÚY theo đúng schema được chỉ định.\n";

        switch ($contentType) {
            case 'product_analysis':
                $prompt = "Hãy phân tích chuyên sâu 12 khía cạnh cho sản phẩm: '{$productName}' (Thương hiệu: {$brand}, Danh mục: {$category}, Giá tham khảo: " . number_format($price) . " VND).\n"
                    . "Dữ liệu nghiên cứu: Giải pháp: " . ($research['problem_solved'] ?? 'N/A') . ", Đối tượng: " . ($research['target_audience'] ?? 'N/A') . ".\n"
                    . "Bằng chứng thực tế: {$evidenceText}.\n"
                    . "Tone giọng: {$tone}.\n";

                $schema = array(
                    'problem_solved' => 'string',
                    'target_audience' => 'string',
                    'key_benefits' => 'array of string',
                    'limitations' => 'array of string',
                    'pros' => 'array of string',
                    'cons' => 'array of string',
                    'suitable_for' => 'array of string',
                    'not_suitable_for' => 'array of string',
                    'buying_considerations' => 'array of string',
                    'comparison_angles' => 'array of string',
                    'risk_notes' => 'string',
                    'confidence' => 'number (0-100)'
                );

                return array('prompt' => $prompt, 'schema' => $schema, 'system_instruction' => $systemInstruction, 'prompt_version' => 'product-analysis-v1');

            case 'tiktok_hooks':
                $prompt = "Hãy tạo 7 biến thể TikTok Strategic Hooks hấp dẫn và kích thích tò mò cho sản phẩm '{$productName}'.\n"
                    . "Bao gồm đủ 7 loại hook: Problem Hook, Mistake Hook, Comparison Hook, Curiosity Hook, Demonstration Hook, Buyer Warning Hook, Value Hook.\n"
                    . "Sản phẩm: {$productName} (Danh mục: {$category}, Đối tượng: " . ($research['target_audience'] ?? 'Gymer') . ").\n";

                $schema = array(
                    'hooks' => array(
                        'type' => 'array',
                        'items' => array(
                            'hook_type' => 'string (problem, mistake, comparison, curiosity, demo, buyer_warning, value)',
                            'headline' => 'string',
                            'hook_script' => 'string',
                            'target_emotion' => 'string',
                            'visual_action' => 'string'
                        )
                    )
                );

                return array('prompt' => $prompt, 'schema' => $schema, 'system_instruction' => $systemInstruction, 'prompt_version' => 'tiktok-hooks-v1');

            case 'tiktok_script':
                $prompt = "Hãy viết kịch bản video ngắn TikTok hoàn chỉnh thời lượng {$duration} giây cho sản phẩm '{$productName}'.\n"
                    . "Góc tiếp cận: {$angle}, Tone giọng: {$tone}.\n"
                    . "Cấu trúc kịch bản bắt buộc: HOOK (3s), PROBLEM (5s), INTRO & DEMO (10s), KEY BENEFIT (7s), TRUST/LIMITATION (3s), CTA (2s).\n"
                    . "Kèm theo bảng kế hoạch phân cảnh Video Shot Plan chi tiết từng cảnh (scene_number, duration, visual_instruction, voiceover, on_screen_text, asset_requirement).\n";

                $schema = array(
                    'title' => 'string',
                    'target_duration' => 'number',
                    'hook_text' => 'string',
                    'full_script' => 'string',
                    'cta_text' => 'string',
                    'shot_plan' => array(
                        'type' => 'array',
                        'items' => array(
                            'scene_number' => 'number',
                            'duration' => 'number (seconds)',
                            'visual_instruction' => 'string',
                            'voiceover' => 'string',
                            'on_screen_text' => 'string',
                            'asset_requirement' => 'string'
                        )
                    )
                );

                return array('prompt' => $prompt, 'schema' => $schema, 'system_instruction' => $systemInstruction, 'prompt_version' => 'tiktok-script-v1');

            case 'seo_content':
                $prompt = "Tạo bộ tối ưu hóa SEO cho trang chi tiết sản phẩm: '{$productName}' (Thương hiệu: {$brand}, Danh mục: {$category}).\n"
                    . "Bao gồm: Primary Keyword, Secondary Keywords, Search Intent, SEO Title (<= 65 ký tự), Meta Description (<= 160 ký tự), Dàn ý bài viết Outline, và danh sách FAQ chuẩn SEO.\n";

                $schema = array(
                    'primary_keyword' => 'string',
                    'secondary_keywords' => 'array of string',
                    'search_intent' => 'string (Commercial, Transactional, Informational)',
                    'seo_title' => 'string',
                    'seo_description' => 'string',
                    'article_outline' => 'array of string',
                    'faqs' => array(
                        'type' => 'array',
                        'items' => array(
                            'question' => 'string',
                            'answer' => 'string'
                        )
                    )
                );

                return array('prompt' => $prompt, 'schema' => $schema, 'system_instruction' => $systemInstruction, 'prompt_version' => 'seo-v1');

            case 'review_draft':
                $prompt = "Hãy soạn thảo bản thảo bài viết đánh giá chuyên sâu (Editorial Review Draft) cho sản phẩm '{$productName}'.\n"
                    . "Dựa trên các đặc tính kỹ thuật: " . ($product['specs'] ?? 'N/A') . " và giải pháp: " . ($research['problem_solved'] ?? 'N/A') . ".\n"
                    . "Bao gồm các phần: Giới thiệu tổng quan, Thiết kế & Chất liệu, Khả năng trợ lực khi tập luyện, Ưu điểm thực tế, Nhược điểm cần lưu ý, So sánh mức giá và Lời khuyên chọn mua.\n";

                $schema = array(
                    'article_title' => 'string',
                    'summary' => 'string',
                    'article_body' => 'string (Formatted HTML/Markdown with <h2>, <h3>, <p>, <ul>)',
                    'verdict_summary' => 'string',
                    'pros_summary' => 'array of string',
                    'cons_summary' => 'array of string'
                );

                return array('prompt' => $prompt, 'schema' => $schema, 'system_instruction' => $systemInstruction, 'prompt_version' => 'review-draft-v1');

            case 'buying_guide':
            case 'faq':
            default:
                $prompt = "Hãy tạo bộ câu hỏi thường gặp FAQ và hướng dẫn chọn mua cho sản phẩm '{$productName}'.\n";
                $schema = array(
                    'title' => 'string',
                    'faqs' => array(
                        'type' => 'array',
                        'items' => array(
                            'question' => 'string',
                            'answer' => 'string'
                        )
                    )
                );
                return array('prompt' => $prompt, 'schema' => $schema, 'system_instruction' => $systemInstruction, 'prompt_version' => 'faq-v1');
        }
    }

    /**
     * Format generated structured data into readable presentation text
     */
    private function formatContentText($contentType, array $data, array $options)
    {
        switch ($contentType) {
            case 'product_analysis':
                $out = "### TỔNG QUAN PHÂN TÍCH SẢN PHẨM\n\n";
                $out .= "**Vấn đề giải quyết:** " . ($data['problem_solved'] ?? '') . "\n\n";
                $out .= "**Đối tượng phù hợp:** " . ($data['target_audience'] ?? '') . "\n\n";
                if (!empty($data['pros'])) {
                    $out .= "**Ưu điểm nổi bật:**\n- " . implode("\n- ", (array)$data['pros']) . "\n\n";
                }
                if (!empty($data['cons'])) {
                    $out .= "**Hạn chế cần lưu ý:**\n- " . implode("\n- ", (array)$data['cons']) . "\n\n";
                }
                if (!empty($data['suitable_for'])) {
                    $out .= "**Nên mua nếu bạn:** " . implode(", ", (array)$data['suitable_for']) . "\n\n";
                }
                if (!empty($data['not_suitable_for'])) {
                    $out .= "**Không nên mua nếu:** " . implode(", ", (array)$data['not_suitable_for']) . "\n\n";
                }
                return $out;

            case 'tiktok_hooks':
                $out = "### 7 BIẾN THỂ TIKTOK HOOK CHIẾN LƯỢC\n\n";
                if (!empty($data['hooks'])) {
                    foreach ($data['hooks'] as $i => $h) {
                        $idx = $i + 1;
                        $type = strtoupper($h['hook_type'] ?? 'HOOK');
                        $out .= "#### #{$idx} [{$type}]: {$h['headline']}\n";
                        $out .= "- **Lời thoại:** \"{$h['hook_script']}\"\n";
                        $out .= "- **Hành động hình ảnh:** {$h['visual_action']}\n\n";
                    }
                }
                return $out;

            case 'tiktok_script':
                $dur = $data['target_duration'] ?? 30;
                $out = "### KỊCH BẢN TIKTOK ({$dur} GIÂY): " . ($data['title'] ?? '') . "\n\n";
                $out .= "**HOOK:** " . ($data['hook_text'] ?? '') . "\n\n";
                $out .= "**NỘI DUNG ĐẦY ĐỦ:**\n" . ($data['full_script'] ?? '') . "\n\n";
                $out .= "**CTA:** " . ($data['cta_text'] ?? '') . "\n\n";
                if (!empty($data['shot_plan'])) {
                    $out .= "### BẢNG PHÂN CẢNH VIDEO (SHOT PLAN):\n";
                    foreach ($data['shot_plan'] as $sp) {
                        $out .= "- **Cảnh {$sp['scene_number']} ({$sp['duration']}s):** [Hình ảnh: {$sp['visual_instruction']}] - Voiceover: \"{$sp['voiceover']}\" (Text trên màn hình: \"{$sp['on_screen_text']}\")\n";
                    }
                }
                return $out;

            case 'seo_content':
                $out = "### GÓI TỐI ƯU SEO METADATA\n\n";
                $out .= "**Từ khóa chính:** " . ($data['primary_keyword'] ?? '') . "\n";
                if (!empty($data['secondary_keywords'])) {
                    $out .= "**Từ khóa phụ:** " . implode(', ', (array)$data['secondary_keywords']) . "\n";
                }
                $out .= "**Ý định tìm kiếm (Search Intent):** " . ($data['search_intent'] ?? '') . "\n\n";
                $out .= "**SEO Title:** " . ($data['seo_title'] ?? '') . "\n";
                $out .= "**Meta Description:** " . ($data['seo_description'] ?? '') . "\n\n";
                if (!empty($data['faqs'])) {
                    $out .= "### CÂU HỎI THƯỜNG GẶP (FAQ SCHEMA):\n";
                    foreach ($data['faqs'] as $faq) {
                        $out .= "**Q: {$faq['question']}**\n- A: {$faq['answer']}\n\n";
                    }
                }
                return $out;

            case 'review_draft':
                return $data['article_body'] ?? ($data['summary'] ?? '');

            default:
                return is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : (string)$data;
        }
    }

    /**
     * Generate descriptive title for content record
     */
    private function generateTitle($contentType, $productName, array $options)
    {
        $map = array(
            'product_analysis' => 'Phân tích chuyên sâu',
            'tiktok_hooks' => '7 Biến thể TikTok Hooks',
            'tiktok_script' => 'Kịch bản TikTok ' . ($options['target_duration'] ?? 30) . 's (' . ($options['content_angle'] ?? 'Standard') . ')',
            'seo_content' => 'Gói SEO Metadata & FAQs',
            'review_draft' => 'Bản thảo bài đánh giá sản phẩm',
            'buying_guide' => 'Hướng dẫn chọn mua',
            'faq' => 'Bộ câu hỏi thường gặp FAQ'
        );
        $prefix = $map[$contentType] ?? 'Nội dung AI';
        return "{$prefix} - {$productName}";
    }
}
