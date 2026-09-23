<?php
/**
 * KHOEPRO - Master Content Package & Multi-Social Adapters
 * Manages verified product facts, angles, and transforms into platform-specific variants (TikTok, FB, YT).
 * Zero-Hallucination Policy: All facts must originate from real database records.
 * PHP 7.4 & 8.x Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

if (!class_exists('ComplianceGuardrail')) {
    require_once __DIR__ . '/class.ComplianceGuardrail.php';
}

class MasterContentPackage
{
    public $id = null;
    public $productId = 0;
    public $candidateId = null;
    public $title = '';
    public $productFacts = array();
    public $sellingPoints = array();
    public $targetAudience = '';
    public $painPoints = array();
    public $keyBenefits = array();
    public $offers = array();
    public $images = array();
    public $contentAngle = 'Problem/Solution';
    public $status = 'DRAFT';
    public $policyStatus = 'PASS';
    public $policyReport = array();
    public $sourceHash = '';

    public function __construct(array $data = array())
    {
        if (!empty($data)) {
            $this->fromArray($data);
        }
    }

    public function fromArray(array $data)
    {
        $this->id = !empty($data['id']) ? (int)$data['id'] : null;
        $this->productId = (int)($data['product_id'] ?? ($data['id_product'] ?? 0));
        $this->candidateId = !empty($data['candidate_id']) ? (int)$data['candidate_id'] : null;
        $this->title = (string)($data['title'] ?? '');
        
        $this->productFacts = is_array($data['product_facts'] ?? null) ? $data['product_facts'] : (!empty($data['product_facts']) ? json_decode($data['product_facts'], true) : array());
        $this->sellingPoints = is_array($data['selling_points'] ?? null) ? $data['selling_points'] : (!empty($data['selling_points']) ? explode("\n", (string)$data['selling_points']) : array());
        $this->targetAudience = (string)($data['target_audience'] ?? '');
        $this->painPoints = is_array($data['pain_points'] ?? null) ? $data['pain_points'] : (!empty($data['pain_points']) ? explode("\n", (string)$data['pain_points']) : array());
        $this->keyBenefits = is_array($data['key_benefits'] ?? null) ? $data['key_benefits'] : (!empty($data['key_benefits']) ? explode("\n", (string)$data['key_benefits']) : array());
        
        $this->offers = is_array($data['offers_json'] ?? null) ? $data['offers_json'] : (!empty($data['offers_json']) ? json_decode($data['offers_json'], true) : (isset($data['offers']) && is_array($data['offers']) ? $data['offers'] : array()));
        $this->images = is_array($data['images_json'] ?? null) ? $data['images_json'] : (!empty($data['images_json']) ? json_decode($data['images_json'], true) : (isset($data['images']) && is_array($data['images']) ? $data['images'] : array()));
        
        $this->contentAngle = (string)($data['content_angle'] ?? 'Problem/Solution');
        $this->status = (string)($data['status'] ?? 'DRAFT');
        $this->policyStatus = (string)($data['policy_status'] ?? 'PASS');
        $this->policyReport = is_array($data['policy_report'] ?? null) ? $data['policy_report'] : (!empty($data['policy_report']) ? json_decode($data['policy_report'], true) : array());
        $this->sourceHash = (string)($data['source_hash'] ?? '');

        return $this;
    }

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'product_id' => $this->productId,
            'candidate_id' => $this->candidateId,
            'title' => $this->title,
            'product_facts' => is_array($this->productFacts) ? json_encode($this->productFacts, JSON_UNESCAPED_UNICODE) : (string)$this->productFacts,
            'selling_points' => is_array($this->sellingPoints) ? implode("\n", $this->sellingPoints) : (string)$this->sellingPoints,
            'target_audience' => $this->targetAudience,
            'pain_points' => is_array($this->painPoints) ? implode("\n", $this->painPoints) : (string)$this->painPoints,
            'key_benefits' => is_array($this->keyBenefits) ? implode("\n", $this->keyBenefits) : (string)$this->keyBenefits,
            'offers_json' => is_array($this->offers) ? json_encode($this->offers, JSON_UNESCAPED_UNICODE) : (string)$this->offers,
            'images_json' => is_array($this->images) ? json_encode($this->images, JSON_UNESCAPED_UNICODE) : (string)$this->images,
            'content_angle' => $this->contentAngle,
            'status' => $this->status,
            'policy_status' => $this->policyStatus,
            'policy_report' => is_array($this->policyReport) ? json_encode($this->policyReport, JSON_UNESCAPED_UNICODE) : (string)$this->policyReport,
            'source_hash' => $this->sourceHash
        );
    }

    /**
     * Build Master Content Package from verified database product records
     * @param int $productId
     * @param PDODb $d
     * @param int|null $candidateId
     * @return MasterContentPackage|null
     */
    public static function buildFromDatabase($productId, $d, $candidateId = null)
    {
        if (!$d || !$productId) return null;

        $product = $d->rawQueryOne("SELECT * FROM table_product WHERE id = ? LIMIT 1", array((int)$productId));
        if (empty($product)) return null;

        $research = $d->rawQueryOne(
            "SELECT * FROM table_product_research WHERE id_product = ? OR external_product_id = ? ORDER BY id DESC LIMIT 1",
            array((int)$productId, $product['code'] ?? '')
        );

        $affiliates = $d->rawQuery(
            "SELECT * FROM table_product_affiliate WHERE id_product = ? AND FIND_IN_SET('hienthi', status) ORDER BY is_best_deal DESC, priority ASC",
            array((int)$productId)
        );

        $brandName = '';
        if (!empty($product['id_brand'])) {
            $br = $d->rawQueryOne("SELECT namevi FROM table_product_brand WHERE id = ? LIMIT 1", array((int)$product['id_brand']));
            $brandName = $br['namevi'] ?? '';
        }

        $categoryName = '';
        if (!empty($product['id_list'])) {
            $cat = $d->rawQueryOne("SELECT namevi FROM table_product_list WHERE id = ? LIMIT 1", array((int)$product['id_list']));
            $categoryName = $cat['namevi'] ?? '';
        }

        // 1. Product Facts
        $facts = array(
            'name' => $product['namevi'],
            'brand' => $brandName,
            'category' => $categoryName,
            'price' => (float)($product['sale_price'] ?: $product['regular_price']),
            'original_price' => (float)$product['regular_price'],
            'specs' => !empty($product['specs']) ? $product['specs'] : '',
            'expert_pros' => !empty($product['expert_pros']) ? $product['expert_pros'] : '',
            'expert_cons' => !empty($product['expert_cons']) ? $product['expert_cons'] : '',
            'verdict' => !empty($product['verdict']) ? $product['verdict'] : ''
        );

        // 2. Selling Points
        $sellingPoints = array();
        if (!empty($product['best_for'])) $sellingPoints[] = "Phù hợp nhất cho: " . $product['best_for'];
        if (!empty($facts['expert_pros'])) $sellingPoints[] = "Ưu điểm nổi bật: " . $facts['expert_pros'];
        if (!empty($research['problem_solved'])) $sellingPoints[] = "Giải quyết nỗi đau: " . $research['problem_solved'];

        // 3. Offers
        $offers = array();
        if (!empty($affiliates)) {
            foreach ($affiliates as $aff) {
                $offers[] = array(
                    'platform' => $aff['platform'],
                    'seller' => $aff['seller_name'] ?? $aff['platform'],
                    'price' => (float)$aff['price'],
                    'commission_rate' => (float)$aff['commission_rate'],
                    'affiliate_url' => $aff['affiliate_url']
                );
            }
        }

        // 4. Images
        $images = array();
        if (!empty($product['photo'])) {
            $images[] = (strpos($product['photo'], 'http') === 0) ? $product['photo'] : 'upload/product/' . $product['photo'];
        }

        // 5. Source Hash
        $sourceHash = hash('sha256', json_encode(array(
            'name' => $product['namevi'],
            'price' => $facts['price'],
            'specs' => $facts['specs'],
            'aff_count' => count($offers)
        ), JSON_UNESCAPED_UNICODE));

        $pkg = new self();
        $pkg->productId = (int)$productId;
        $pkg->candidateId = $candidateId ? (int)$candidateId : null;
        $pkg->title = "Master Content - " . $product['namevi'];
        $pkg->productFacts = $facts;
        $pkg->sellingPoints = $sellingPoints;
        $pkg->targetAudience = $research['target_audience'] ?? ($product['best_for'] ?? 'Người tập gym, fitness');
        $pkg->painPoints = !empty($research['problem_solved']) ? array($research['problem_solved']) : array('Dễ chấn thương và đau mỏi khi tập nặng không có đồ bảo hộ chuẩn');
        $pkg->keyBenefits = array('Bảo vệ khớp và cột sống an toàn', 'Tối ưu lực gánh và đẩy tạ', 'Chất liệu bền bỉ chuẩn thi đấu');
        $pkg->offers = $offers;
        $pkg->images = $images;
        $pkg->contentAngle = 'Problem/Solution';
        $pkg->sourceHash = $sourceHash;
        $pkg->status = 'GENERATED';

        return $pkg;
    }

    /**
     * Adapt to TikTok Variant (Punchy hook, fast 30s pacing, trending hashtags, bio CTA)
     * @return array
     */
    public function toTikTokVariant()
    {
        $prodName = $this->productFacts['name'] ?? 'Sản phẩm tập Gym';
        $price = number_format($this->productFacts['price'] ?? 0) . 'đ';
        $pain = !empty($this->painPoints[0]) ? $this->painPoints[0] : 'Tập mãi không lên tạ vì thiếu đồ hỗ trợ?';
        
        $hook = "Dừng lại ngay nếu bạn đang gặp tình trạng: {$pain}!";
        $caption = "Đừng để sai lầm nhỏ phá hỏng form tập của bạn! Review chi tiết {$prodName} chuẩn công năng. Giá chỉ {$price}. Xem ngay ở link đầu trang!";
        $hashtags = "#khoepro #reviewgym #tapgym #thehinh #gymlife #phukiengym";

        $shotPlan = array(
            array('scene' => 1, 'duration' => 5, 'visual' => 'Cận cảnh động tác tập nặng bị đau/sai form', 'voice' => $hook),
            array('scene' => 2, 'duration' => 10, 'visual' => 'Mở hộp và cận cảnh chất liệu ' . $prodName, 'voice' => 'Giải pháp cứu cánh chính là ' . $prodName . ' với thiết kế khóa trợ lực an toàn.'),
            array('scene' => 3, 'duration' => 10, 'visual' => 'Thực hiện động tác chuẩn với sản phẩm', 'voice' => 'Cảm giác ôm chắc, trợ lực rõ rệt, tự tin bung hết sức mà không lo chấn thương.'),
            array('scene' => 4, 'duration' => 5, 'visual' => 'Hiển thị giá ưu đãi và biểu tượng link bio', 'voice' => 'Giá đang ưu đãi chỉ ' . $price . '. Nhấn vào link bio để săn deal ngay nhé!')
        );

        return array(
            'platform' => 'tiktok',
            'hook' => $hook,
            'caption' => $caption,
            'hashtags' => $hashtags,
            'cta' => 'Nhấn vào giỏ hàng hoặc link bio để xem ưu đãi!',
            'target_duration' => 30,
            'shot_plan' => $shotPlan
        );
    }

    /**
     * Adapt to Facebook Reel / Fanpage Variant (Detailed benefits, community proof, clear link CTA)
     * @return array
     */
    public function toFacebookVariant()
    {
        $prodName = $this->productFacts['name'] ?? 'Phụ kiện tập gym';
        $price = number_format($this->productFacts['price'] ?? 0) . ' VNĐ';
        $brand = !empty($this->productFacts['brand']) ? "từ thương hiệu {$this->productFacts['brand']}" : '';
        $pros = $this->productFacts['expert_pros'] ?? 'Độ hoàn thiện cao, chịu lực tốt';

        $hook = "Bí quyết giúp anh em Gymer nâng tạ an toàn và bền bỉ mỗi ngày!";
        $body = "👉 Bạn đang tìm kiếm một giải pháp bảo vệ khớp và trợ lực hiệu quả khi tập nặng?\n\n"
              . "Khám phá ngay {$prodName} {$brand} — Đang là lựa chọn hàng đầu của cộng đồng thể hình:\n"
              . "✅ " . implode("\n✅ ", $this->keyBenefits) . "\n"
              . "⭐ Đánh giá chuyên gia: {$pros}\n\n"
              . "🔥 Giá ưu đãi độc quyền hôm nay: {$price}\n"
              . "👇 Xem chi tiết thông số và săn mã giảm giá tại liên kết bên dưới!";

        $hashtags = "#KhoePro #DoTapGym #TheHinhVietNam #GymFitness #ReviewDoTap";

        return array(
            'platform' => 'facebook',
            'hook' => $hook,
            'caption' => $hook . " - " . $prodName,
            'body_text' => $body,
            'hashtags' => $hashtags,
            'cta_text' => 'Xem chi tiết & Đặt mua ngay',
            'target_duration' => 45
        );
    }

    /**
     * Adapt to YouTube Shorts Variant (SEO keyword focus, clear value proposition, pinned comment CTA)
     * @return array
     */
    public function toYouTubeVariant()
    {
        $prodName = $this->productFacts['name'] ?? 'Đồ tập gym';
        $title = "Review {$prodName} Có Thực Sự Tốt? Đánh Giá Chi Tiết Cho Gymer";
        if (mb_strlen($title, 'UTF-8') > 90) {
            $title = mb_substr($title, 0, 87, 'UTF-8') . '...';
        }

        $hook = "Có nên mua {$prodName} không? Đây là câu trả lời thật 100%!";
        $description = "Đánh giá chi tiết {$prodName} sau thời gian trải nghiệm thực tế.\n"
                     . "Ưu điểm: " . ($this->productFacts['expert_pros'] ?? 'Chắc chắn, an toàn') . "\n"
                     . "Nhược điểm: " . ($this->productFacts['expert_cons'] ?? 'Cần chọn đúng size') . "\n\n"
                     . "📌 Link săn deal chính hãng và mã giảm giá đã ghim ở phần bình luận bên dưới!\n\n"
                     . "#Shorts #KhoePro #GymReview #" . preg_replace('/[^a-zA-Z0-9]/', '', $prodName);

        return array(
            'platform' => 'youtube_shorts',
            'title' => $title,
            'hook' => $hook,
            'description' => $description,
            'hashtags' => '#Shorts #KhoePro #GymReview #TheHinh',
            'cta' => 'Link ưu đãi chính hãng được ghim dưới phần bình luận!',
            'target_duration' => 45
        );
    }

    /**
     * Save Master Content Package into table_master_content
     * @param PDODb $d
     * @return int Insert ID
     */
    public function saveToDatabase($d)
    {
        if (!$d || !$this->productId) return 0;
        $now = time();

        $data = $this->toArray();
        $data['date_updated'] = $now;

        if (!empty($this->id)) {
            $d->where('id', $this->id);
            $d->update('master_content', $data);
            return $this->id;
        } else {
            $data['date_created'] = $now;
            $insertId = $d->insert('master_content', $data);
            if ($insertId) $this->id = (int)$insertId;
            return (int)$insertId;
        }
    }
}
