<?php
/**
 * KHOEPRO - Product Discovery & Relevance Filter Engine
 * Two-Stage Filter: Basic Relevance Filter + AI Product Relevance Analysis
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

if (!class_exists('AIResearchAgent')) {
    require_once __DIR__ . '/class.AIResearchAgent.php';
}

class ProductRelevanceFilter
{
    private $d;
    private $func;
    private $aiAgent;

    const VERDICT_RELEVANT     = 'RELEVANT';
    const VERDICT_NOT_RELEVANT = 'NOT_RELEVANT';
    const VERDICT_NEEDS_REVIEW = 'NEEDS_REVIEW';

    /**
     * Negative Blacklist Patterns (Obvious Irrelevant Niches)
     * Matches against title, merchant, category, campaign, description
     */
    private $negativePatterns = array(
        // Hair styling, salon, grooming, cosmetics
        'gôm xịt tóc', 'gom xit toc', 'sáp vuốt tóc', 'sap vuot toc', 'keo vuốt tóc', 'keo vuot toc',
        'dầu gội', 'dau goi', 'dầu xả', 'dau xa', 'kem ủ tóc', 'màu xịt tóc', 'reuzel', 'kevin murphy',
        'glanzen', 'tigi', 'lady killer', 'orzen', 'davines', '30shine', 'salonista', 'pomade',
        'sữa rửa mặt', 'sua rua mat', 'kem trị mụn', 'kem tri mun', 'serum', 'nước hoa hồng', 'nuoc hoa hong',
        'toner', 'son dưỡng', 'son duong', 'son môi', 'son moi', 'mặt nạ', 'mat na', 'tẩy da chết', 'tay da chet',
        'tẩy tế bào chết', 'kem chống nắng', 'kem chong nang', 'lột mụn', 'lot mun', 'kem trắng da', 'trang da',
        'nước tẩy trang', 'tinh chất', 'cây gạt mụn', 'xà phòng trị mụn', 'sua tam', 'sữa tắm', 'dưỡng ẩm',
        'máy cạo râu', 'may cao rau', 'lưỡi cạo râu', 'máy sấy tóc', 'may say toc', 'lăn khử mùi', 'lan khu mui',
        'xịt khử mùi', 'gel vệ sinh', 'nước hoa', 'perfume', 'makeup', 'trang điểm',

        // Finance, Banking, Loans, Insurance, Telco
        'banking', 'ngân hàng', 'ngan hang', 'mở thẻ', 'mo the', 'thẻ tín dụng', 'the tin dung',
        'khoản vay', 'khoan vay', 'vay tiền', 'vay tien', 'tài chính', 'tai chinh', 'bảo hiểm', 'bao hiem',
        'credit', 'shb', 'mb bank', 'taptap', 'vcredit', 'viet credit', 'sim cước', 'sim cuoc', 'vinaphone',
        'viettel', 'mobifone', 'gói cước', 'goi cuoc', 'tuyển tài xế', 'tuyen tai xe', 'tuyển dụng', 'tuyen dung',
        'driver recruitment', 'gsm philippines',

        // Tech, Electronics, Home Appliances (Unrelated)
        'iphone', 'ipad', 'macbook', 'laptop', 'smartphone', 'điện thoại', 'dien thoai', 'tủ lạnh', 'tu lanh',
        'máy giặt', 'may giat', 'tivi', 'máy lọc không khí', 'tai nghe bluetooth', 'airpods', 'bàn ủi', 'nồi cơm',

        // Unrelated Fashion & Accessories
        'áo sơ mi', 'ao so mi', 'áo đầm', 'ao dam', 'váy', 'vay', 'chân váy', 'giày cao gót', 'giay cao got',
        'túi xách nữ', 'tui xach nu', 'ví da', 'vi da', 'đồng hồ thời trang nữ', 'nhẫn', 'dây chuyền',

        // Unrelated Travel, Food & Services
        'vé máy bay', 've may bay', 'tour du lịch', 'tour du lich', 'khách sạn', 'khach san', 'vé tham quan',
        'ẩm thực', 'thịt bò', 'homefarm', 'buffet', 'nhà hàng'
    );

    /**
     * Negative Merchant & Campaign Slugs
     */
    private $negativeMerchants = array(
        '30shine_store', 'shopeepay_cpr', 'homefarm', 'cellphonesambassador_preorder',
        'cellphones_preordercamp', 'shb_taptap', 'gsm_ph', 'cellphones_ambassador',
        'aldo_web', 'aldo_miniapp', 'admitad_lenovo', 'kkday_admitad', 'skinlosophy_shopee_loyalty',
        'vnpt_sim', 'malayairline_tyroo', 'skinlosophy_shopee', 'vcredit2026', 'mb_thudo',
        'vne_tour'
    );

    /**
     * Positive Fitness & KhoePro Core Topics (Seed Taxonomy)
     */
    private $positivePatterns = array(
        // Gym, Weightlifting & Bodybuilding
        'đai lưng', 'dai lung', 'lifting belt', 'dây kéo lưng', 'day keo lung', 'lifting straps',
        'quấn cổ tay', 'quan co tay', 'wrist wraps', 'găng tay tập gym', 'gang tay tap gym', 'găng tay gym',
        'gang tay gym', 'knee sleeve', 'bó gối', 'bo goi', 'bọc khuỷu tay', 'elbow sleeve', 'wrist support',
        'bảo vệ cổ chân', 'ankle support', 'phấn tập tạ', 'liquid chalk', 'móc treo xà', 'pull up',
        'đệm gánh tạ', 'barbell pad', 'tạ tay', 'tạ đơn', 'dumbbell', 'tạ ấm', 'kettlebell', 'tạ đòn',
        'barbell', 'đòn tạ', 'bánh tạ', 'ghế tập gym', 'ghế tạ', 'xà đơn', 'xa don', 'xà kép',

        // Resistance, Core & Mobility
        'dây kháng lực', 'day khang luc', 'resistance band', 'booty band', 'dây ngũ sắc', 'day ngu sac',
        'dây mini band', 'con lăn tập bụng', 'con lan tap bung', 'power roller', 'ab roller',
        'bánh xe tập bụng', 'dây nhảy', 'day nhay', 'jump rope', 'thảm tập gym', 'tham tap gym',
        'thảm yoga', 'tham yoga', 'yoga mat', 'gạch yoga', 'vòng yoga', 'bóng yoga', 'con lăn yoga',

        // Recovery, Massage & Health Devices
        'con lăn massage', 'con lan massage', 'foam roller', 'súng massage', 'sung massage', 'massage gun',
        'súng giãn cơ', 'sung gian co', 'bóng massage', 'lacrosse ball', 'cân sức khỏe', 'can suc khoe',
        'cân điện tử', 'can dien tu', 'cân thông minh', 'body scale', 'smart scale', 'cân đo mỡ', 'inbody',
        'vòng đeo tay thể thao', 'fitness tracker', 'smartwatch thể thao', 'đồng hồ thể thao', 'garmin',

        // Sports, Running & Active Lifestyle Accessories
        'bình lắc', 'binh lac', 'shaker', 'bình giữ nhiệt thể thao', 'binh nuoc the thao', 'sports bottle',
        'áo tập gym', 'ao tap gym', 'quần tập gym', 'quan tap gym', 'áo thể thao', 'quần thể thao',
        'sportswear', 'activewear', 'đai chạy bộ', 'túi chạy bộ', 'băng trán thể thao', 'bó cơ',

        // Sports Nutrition & Supplements
        'whey protein', 'whey', 'bcaa', 'creatine', 'pre-workout', 'preworkout', 'eaa',
        'mass gainer', 'dinh dưỡng thể hình', 'thực phẩm bổ sung thể thao', 'tăng cơ', 'tang co'
    );

    public function __construct($d = null, $func = null, $aiAgent = null)
    {
        $this->d = $d;
        $this->func = $func;
        $this->aiAgent = $aiAgent ?: new AIResearchAgent($d, $func);
    }

    /**
     * Stage 1: Basic Relevance Filter
     * Evaluates campaign, merchant, category, product title, description using heuristic rules
     * @param array $product
     * @return array ['verdict' => string, 'confidence' => float, 'reasons' => array, 'matched_positive' => array, 'matched_negative' => array]
     */
    public function evaluateBasic(array $product)
    {
        $title = trim($product['name'] ?? ($product['title'] ?? ''));
        $merchant = trim($product['merchant'] ?? ($product['at_merchant'] ?? ''));
        $category = trim($product['category'] ?? ($product['category_name'] ?? ''));
        $desc = trim($product['description'] ?? ($product['problem_solved'] ?? ''));
        $campaign = trim($product['campaign_id'] ?? ($product['at_campaign_id'] ?? ''));

        $searchCorpus = mb_strtolower($title . ' ' . $merchant . ' ' . $category . ' ' . $desc . ' ' . $campaign, 'UTF-8');
        $merchantLower = strtolower($merchant);

        $matchedNegative = array();
        $matchedPositive = array();

        // 1. Check Negative Merchant Blacklist
        if (!empty($merchantLower)) {
            foreach ($this->negativeMerchants as $negM) {
                if (strpos($merchantLower, $negM) !== false) {
                    $matchedNegative[] = "Merchant '{$merchant}' thuộc danh sách loại trừ trực tiếp ({$negM})";
                }
            }
        }

        // 2. Check Negative Keyword Patterns
        foreach ($this->negativePatterns as $pattern) {
            if (mb_strpos($searchCorpus, $pattern, 0, 'UTF-8') !== false) {
                $matchedNegative[] = "Phát hiện từ khóa không liên quan KhoePro: '{$pattern}'";
            }
        }

        // 3. Check Positive Fitness Patterns
        foreach ($this->positivePatterns as $pos) {
            if (mb_strpos($searchCorpus, $pos, 0, 'UTF-8') !== false) {
                $matchedPositive[] = $pos;
            }
        }

        // Decision logic for Stage 1
        if (!empty($matchedNegative)) {
            // Clearly irrelevant product
            return array(
                'verdict' => self::VERDICT_NOT_RELEVANT,
                'stage' => 'BASIC_FILTER',
                'confidence' => 95.0,
                'reasons' => $matchedNegative,
                'matched_positive' => $matchedPositive,
                'matched_negative' => $matchedNegative,
                'is_relevant' => false
            );
        }

        if (!empty($matchedPositive)) {
            // Matches fitness keywords strongly
            return array(
                'verdict' => self::VERDICT_RELEVANT,
                'stage' => 'BASIC_FILTER',
                'confidence' => 85.0,
                'reasons' => array("Trùng khớp nhóm từ khóa chủ đề Gym/Fitness/Sports: " . implode(', ', array_slice($matchedPositive, 0, 3))),
                'matched_positive' => $matchedPositive,
                'matched_negative' => array(),
                'is_relevant' => true
            );
        }

        // Ambiguous -> Needs AI analysis
        return array(
            'verdict' => self::VERDICT_NEEDS_REVIEW,
            'stage' => 'BASIC_FILTER',
            'confidence' => 50.0,
            'reasons' => array("Chưa đủ bằng chứng rõ ràng từ bộ lọc cơ bản; chuyển tiếp sang AI Relevance Engine."),
            'matched_positive' => array(),
            'matched_negative' => array(),
            'is_relevant' => null
        );
    }

    /**
     * Stage 2: AI Product Relevance Analysis
     * Reuses existing AIResearchAgent/AIProviderInterface to analyze semantic context,
     * KhoePro audience relevance, review potential, comparison potential & content potential.
     * @param array $product
     * @param array $basicResult
     * @return array
     */
    public function evaluateAiRelevance(array $product, array $basicResult = array())
    {
        $name = trim($product['name'] ?? ($product['title'] ?? ''));
        $merchant = trim($product['merchant'] ?? '');
        $category = trim($product['category'] ?? '');
        $price = isset($product['price']) ? (float)$product['price'] : 0;
        $desc = trim($product['description'] ?? ($product['problem_solved'] ?? ''));

        $prompt = "Bạn là Giám đốc Nghiên cứu Sản phẩm của KHOEPRO (Nền tảng Review & Trải nghiệm Đồ tập Gym, Phụ kiện Fitness, Dụng cụ Thể thao & Sức khỏe Chủ động hàng đầu).\n";
        $prompt .= "Nhiệm vụ: Đánh giá độ phù hợp (Relevance) của sản phẩm sau đây đối với tôn chỉ nội dung và độc giả KhoePro:\n\n";
        $prompt .= "- Tên sản phẩm: {$name}\n";
        $prompt .= "- Merchant / Gian hàng: {$merchant}\n";
        $prompt .= "- Danh mục: {$category}\n";
        $prompt .= "- Giá bán dự kiến: " . number_format($price) . " VND\n";
        if (!empty($desc)) {
            $prompt .= "- Mô tả sản phẩm: {$desc}\n";
        }
        $prompt .= "\nTIÊU CHUẨN ĐÁNH GIÁ CỦA KHOEPRO:\n";
        $prompt .= "1. CORE TOPICS PHÙ HỢP: Gym, Fitness, Workout, Thể thao, Dụng cụ tập tại nhà, Phục hồi cơ bắp (Foam roller, Massage gun), Vận động, Phụ kiện tập luyện (đai lưng, găng tay, dây kháng lực, quấn cổ tay, thảm tập), Thiết bị theo dõi sức khỏe (Smart scale đo mỡ, Fitness tracker, Smartwatch thể thao), Thực phẩm bổ sung (Whey, Creatine, BCAA).\n";
        $prompt .= "2. LOẠI TRỪ NGAY (NOT_RELEVANT): Gôm xịt tóc, sáp vuốt tóc, mỹ phẩm, kem trị mụn, sữa rửa mặt, đồ cạo râu, ngân hàng, vay vốn, thẻ tín dụng, sim cước, tuyển dụng tài xế, điện thoại, laptop, thời trang phổ thông không phải đồ thể thao.\n";
        $prompt .= "3. ĐÁNH GIÁ TIỀM NĂNG: Khả năng làm bài Review thực tế, khả năng so sánh đối đầu (Comparison), tiềm năng tạo nội dung video/bài viết cho người tập.\n\n";
        $prompt .= "Hãy trả về kết quả JSON với cấu trúc bắt buộc:\n";

        $schema = array(
            'type' => 'object',
            'properties' => array(
                'verdict' => 'string (RELEVANT, NOT_RELEVANT, NEEDS_REVIEW)',
                'confidence' => 'number (0-100)',
                'fitness_niche' => 'string',
                'khoepro_audience_fit' => 'string (HIGH, MEDIUM, LOW, NONE)',
                'review_potential' => 'string (HIGH, MEDIUM, LOW)',
                'comparison_potential' => 'string (HIGH, MEDIUM, LOW)',
                'content_potential' => 'string (HIGH, MEDIUM, LOW)',
                'reasons' => 'array of string',
                'summary_rationale' => 'string'
            ),
            'required' => array('verdict', 'confidence', 'reasons', 'summary_rationale')
        );

        $aiRes = $this->aiAgent->generateStructuredResponse($prompt, $schema, array(
            'system_instruction' => "KHOEPRO AI Product Discovery Gate. Phân loại chuẩn xác sản phẩm phù hợp hệ sinh thái Gym/Fitness/Sports KhoePro. Trả về đúng JSON."
        ));

        if ($aiRes['status'] && !empty($aiRes['data'])) {
            $data = $aiRes['data'];
            $rawVerdict = strtoupper(trim($data['verdict'] ?? ''));
            $verdict = in_array($rawVerdict, array(self::VERDICT_RELEVANT, self::VERDICT_NOT_RELEVANT, self::VERDICT_NEEDS_REVIEW)) ? $rawVerdict : self::VERDICT_NOT_RELEVANT;
            $confidence = isset($data['confidence']) ? (float)$data['confidence'] : 75.0;
            $reasons = is_array($data['reasons']) ? $data['reasons'] : array($data['summary_rationale'] ?? 'AI relevance decision');

            return array(
                'verdict' => $verdict,
                'stage' => 'AI_ANALYSIS',
                'confidence' => $confidence,
                'reasons' => $reasons,
                'fitness_niche' => $data['fitness_niche'] ?? '',
                'khoepro_audience_fit' => $data['khoepro_audience_fit'] ?? 'MEDIUM',
                'review_potential' => $data['review_potential'] ?? 'MEDIUM',
                'comparison_potential' => $data['comparison_potential'] ?? 'MEDIUM',
                'content_potential' => $data['content_potential'] ?? 'MEDIUM',
                'summary_rationale' => $data['summary_rationale'] ?? '',
                'is_relevant' => ($verdict === self::VERDICT_RELEVANT)
            );
        }

        // If AI Provider unavailable or error, fall back to Stage 1 basic assessment
        $fallbackVerdict = ($basicResult['verdict'] === self::VERDICT_RELEVANT) ? self::VERDICT_RELEVANT : self::VERDICT_NOT_RELEVANT;
        return array(
            'verdict' => $fallbackVerdict,
            'stage' => 'BASIC_FALLBACK',
            'confidence' => 60.0,
            'reasons' => array("AI Engine không phản hồi (" . ($aiRes['error'] ?? 'timeout') . "), sử dụng kết quả đánh giá cơ bản."),
            'fitness_niche' => 'Fitness & Sports',
            'khoepro_audience_fit' => 'MEDIUM',
            'review_potential' => 'MEDIUM',
            'comparison_potential' => 'MEDIUM',
            'content_potential' => 'MEDIUM',
            'summary_rationale' => 'AI fallback based on basic keyword evaluation',
            'is_relevant' => ($fallbackVerdict === self::VERDICT_RELEVANT)
        );
    }

    /**
     * Complete Two-Stage Product Filter Pipeline
     * ACCESSTRADE DATAFEED -> BASIC RELEVANCE FILTER -> AI PRODUCT RELEVANCE ANALYSIS (Optional) -> RESULT
     * @param array $product
     * @param bool $enableAi
     * @return array
     */
    public function evaluateProduct(array $product, $enableAi = false)
    {
        // Stage 1: Basic Relevance Filter (Rule-based, instant <1ms)
        $basic = $this->evaluateBasic($product);

        if ($basic['verdict'] === self::VERDICT_NOT_RELEVANT) {
            // Dropped immediately at Stage 1
            return $basic;
        }

        // Only trigger heavy LLM API call if explicitly requested ($enableAi === true)
        if ($enableAi) {
            $aiResult = $this->evaluateAiRelevance($product, $basic);
            return array(
                'verdict' => $aiResult['verdict'],
                'stage' => $aiResult['stage'],
                'confidence' => $aiResult['confidence'],
                'reasons' => array_unique(array_merge($basic['reasons'], $aiResult['reasons'])),
                'fitness_niche' => $aiResult['fitness_niche'] ?? '',
                'khoepro_audience_fit' => $aiResult['khoepro_audience_fit'] ?? 'MEDIUM',
                'review_potential' => $aiResult['review_potential'] ?? 'MEDIUM',
                'comparison_potential' => $aiResult['comparison_potential'] ?? 'MEDIUM',
                'content_potential' => $aiResult['content_potential'] ?? 'MEDIUM',
                'summary_rationale' => $aiResult['summary_rationale'] ?? '',
                'is_relevant' => $aiResult['is_relevant'],
                'raw_product' => $product
            );
        }

        return $basic;
    }
}
