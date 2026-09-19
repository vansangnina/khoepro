<?php
/**
 * FITNADO - Research Provider Architecture (Phase 04)
 * PHP 7.4 Compatible
 */

if (!class_exists('AIResearchAgent')) {
    require_once __DIR__ . '/class.AIResearchAgent.php';
}

class ResearchProvider
{
    public static function factory($providerName, $d = null, $func = null)
    {
        return ResearchProviderFactory::create($providerName, $d, $func);
    }
}

class ResearchCandidateDTO
{
    public $name;
    public $platform;
    public $source_url;
    public $external_product_id;
    public $image_url;
    public $price;
    public $original_price;
    public $currency;
    public $sales_count;
    public $rating;
    public $review_count;
    public $commission_rate;
    public $commission_value;
    public $estimated_gmv;
    public $creator_count;
    public $video_count;
    public $top_video_views;
    public $category_hint;
    public $brand_hint;
    public $problem_solved;
    public $target_audience;
    public $research_notes;
    public $primary_keyword;
    public $competition_score;
    public $seo_score;
    public $raw_data;
    public $evidence; // Array of [field_name, field_value, evidence_type, source_url]
    public $discovery_source;

    public function __construct(array $data = array())
    {
        $this->name = $data['name'] ?? '';
        $this->platform = $data['platform'] ?? 'manual';
        $this->source_url = $data['source_url'] ?? '';
        $this->external_product_id = $data['external_product_id'] ?? null;
        $this->image_url = $data['image_url'] ?? null;
        $this->price = isset($data['price']) && $data['price'] !== '' ? (float)$data['price'] : null;
        $this->original_price = isset($data['original_price']) && $data['original_price'] !== '' ? (float)$data['original_price'] : null;
        $this->currency = $data['currency'] ?? 'VND';
        $this->sales_count = isset($data['sales_count']) && $data['sales_count'] !== '' ? (int)$data['sales_count'] : null;
        $this->rating = isset($data['rating']) && $data['rating'] !== '' ? (float)$data['rating'] : null;
        $this->review_count = isset($data['review_count']) && $data['review_count'] !== '' ? (int)$data['review_count'] : null;
        $this->commission_rate = isset($data['commission_rate']) && $data['commission_rate'] !== '' ? (float)$data['commission_rate'] : null;
        $this->commission_value = isset($data['commission_value']) && $data['commission_value'] !== '' ? (float)$data['commission_value'] : null;
        $this->estimated_gmv = isset($data['estimated_gmv']) && $data['estimated_gmv'] !== '' ? (float)$data['estimated_gmv'] : null;
        $this->creator_count = isset($data['creator_count']) && $data['creator_count'] !== '' ? (int)$data['creator_count'] : null;
        $this->video_count = isset($data['video_count']) && $data['video_count'] !== '' ? (int)$data['video_count'] : null;
        $this->top_video_views = isset($data['top_video_views']) && $data['top_video_views'] !== '' ? (int)$data['top_video_views'] : null;
        $this->category_hint = $data['category_hint'] ?? null;
        $this->brand_hint = $data['brand_hint'] ?? null;
        $this->problem_solved = $data['problem_solved'] ?? null;
        $this->target_audience = $data['target_audience'] ?? null;
        $this->research_notes = $data['research_notes'] ?? null;
        $this->primary_keyword = $data['primary_keyword'] ?? null;
        $this->competition_score = isset($data['competition_score']) && $data['competition_score'] !== '' ? (float)$data['competition_score'] : null;
        $this->seo_score = isset($data['seo_score']) && $data['seo_score'] !== '' ? (float)$data['seo_score'] : null;
        $this->raw_data = $data['raw_data'] ?? null;
        $this->evidence = $data['evidence'] ?? array();
        $this->discovery_source = $data['discovery_source'] ?? 'manual';
    }

    public function toArray()
    {
        return array(
            'name' => $this->name,
            'platform' => $this->platform,
            'source_url' => $this->source_url,
            'external_product_id' => $this->external_product_id,
            'image_url' => $this->image_url,
            'price' => $this->price,
            'original_price' => $this->original_price,
            'currency' => $this->currency,
            'sales_count' => $this->sales_count,
            'rating' => $this->rating,
            'review_count' => $this->review_count,
            'commission_rate' => $this->commission_rate,
            'commission_value' => $this->commission_value,
            'estimated_gmv' => $this->estimated_gmv,
            'creator_count' => $this->creator_count,
            'video_count' => $this->video_count,
            'top_video_views' => $this->top_video_views,
            'category_hint' => $this->category_hint,
            'brand_hint' => $this->brand_hint,
            'problem_solved' => $this->problem_solved,
            'target_audience' => $this->target_audience,
            'research_notes' => $this->research_notes,
            'primary_keyword' => $this->primary_keyword,
            'competition_score' => $this->competition_score,
            'seo_score' => $this->seo_score,
            'raw_data' => is_array($this->raw_data) ? json_encode($this->raw_data, JSON_UNESCAPED_UNICODE) : $this->raw_data,
            'discovery_source' => $this->discovery_source
        );
    }
}

interface ResearchProviderInterface
{
    public function getName();
    public function discover($seed, array $options = array());
}

abstract class BaseResearchProvider implements ResearchProviderInterface
{
    protected $d;
    protected $func;

    public function __construct($d = null, $func = null)
    {
        $this->d = $d;
        $this->func = $func;
    }
}

class ManualProvider extends BaseResearchProvider
{
    public function getName()
    {
        return 'manual';
    }

    public function discover($seed, array $options = array())
    {
        // Manual provider returns empty discovery list; candidates are added via Admin form
        return array();
    }
}

class AiResearchProvider extends BaseResearchProvider
{
    private $aiAgent;

    public function __construct($d = null, $func = null, $aiAgent = null)
    {
        parent::__construct($d, $func);
        $this->aiAgent = $aiAgent ?: new AIResearchAgent($d, $func);
    }

    public function getName()
    {
        return 'ai_agent';
    }

    public function discover($seed, array $options = array())
    {
        $keyword = is_array($seed) ? ($seed['keyword'] ?? '') : (string)$seed;
        $seedType = is_array($seed) ? ($seed['seed_type'] ?? 'keyword') : 'keyword';
        $maxResults = isset($options['max_results']) ? (int)$options['max_results'] : 5;
        $depth = isset($options['depth']) ? $options['depth'] : 'STANDARD';

        $prompt = "Bạn là chuyên gia nghiên cứu thị trường Gym & Fitness của FITNADO.\n";
        $prompt .= "Nhiệm vụ: Dựa trên hạt giống nghiên cứu sau, hãy đề xuất tối đa {$maxResults} sản phẩm tiềm năng nhất thị trường Việt Nam.\n";
        $prompt .= "Hạt giống (Seed): '{$keyword}' (Loại: {$seedType}, Mức độ: {$depth}).\n\n";
        $prompt .= "YÊU CẦU BẮT BUỘC:\n";
        $prompt .= "1. Không được tự bịa số liệu sự thật chính xác nếu không chắc chắn (hãy để null nếu không có bằng chứng).\n";
        $prompt .= "2. Phân biệt rõ ràng giữa phân tích nhận định (AI analysis) và số liệu thực tế.\n";
        $prompt .= "3. Trả về đúng định dạng JSON danh sách mảng 'candidates'.\n";

        $schema = array(
            'type' => 'array',
            'items' => array(
                'name' => 'string',
                'platform' => 'string (tiktok, shopee, lazada, brand)',
                'source_url' => 'string',
                'external_product_id' => 'string',
                'category_hint' => 'string',
                'brand_hint' => 'string',
                'problem_solved' => 'string',
                'target_audience' => 'string',
                'primary_keyword' => 'string',
                'content_angles' => 'array of string',
                'visual_demo_potential' => 'string (HIGH, MEDIUM, LOW)',
                'comparison_potential' => 'string (HIGH, MEDIUM, LOW)',
                'risk_notes' => 'string',
                'estimated_price' => 'number or null',
                'confidence' => 'number (0-100)'
            )
        );

        $aiResponse = $this->aiAgent->generateStructuredResponse($prompt, $schema, array(
            'system_instruction' => "FITNADO AI Product Discovery Engine v1.0. Chuyên phân tích và phát hiện sản phẩm thể thao/gym tiềm năng. Trả về định dạng JSON thuần túy, tuyệt đối không chèn HTML hay markdown ngoài block JSON."
        ));

        $results = array();
        if ($aiResponse['status'] && !empty($aiResponse['data'])) {
            $rawCandidates = isset($aiResponse['data']['candidates']) ? $aiResponse['data']['candidates'] : $aiResponse['data'];
            if (is_array($rawCandidates)) {
                foreach ($rawCandidates as $idx => $item) {
                    if (empty($item['name'])) continue;

                    $plat = !empty($item['platform']) ? strtolower($item['platform']) : 'tiktok';
                    if (!in_array($plat, array('tiktok', 'shopee', 'lazada', 'brand', 'other'))) $plat = 'tiktok';

                    $extId = !empty($item['external_product_id']) ? $item['external_product_id'] : 'AI_' . strtoupper(substr(md5($item['name'] . $plat), 0, 10));
                    $sourceUrl = !empty($item['source_url']) ? $item['source_url'] : 'https://' . ($plat === 'tiktok' ? 'tiktok.com/@fitnado/product/' : 'shopee.vn/product/') . $extId;

                    $dto = new ResearchCandidateDTO(array(
                        'name' => trim($item['name']),
                        'platform' => $plat,
                        'source_url' => $sourceUrl,
                        'external_product_id' => $extId,
                        'category_hint' => $item['category_hint'] ?? (is_array($seed) ? ($seed['title'] ?? '') : ''),
                        'brand_hint' => $item['brand_hint'] ?? null,
                        'price' => isset($item['estimated_price']) ? (float)$item['estimated_price'] : null,
                        'problem_solved' => $item['problem_solved'] ?? null,
                        'target_audience' => $item['target_audience'] ?? null,
                        'primary_keyword' => $item['primary_keyword'] ?? $keyword,
                        'research_notes' => "AI Discovery (Depth: {$depth}). Angles: " . (is_array($item['content_angles'] ?? null) ? implode(', ', $item['content_angles']) : ''),
                        'discovery_source' => 'ai_agent',
                        'raw_data' => $item,
                        'evidence' => array(
                            array(
                                'field_name' => 'ai_discovery_summary',
                                'field_value' => json_encode(array(
                                    'angles' => $item['content_angles'] ?? array(),
                                    'visual' => $item['visual_demo_potential'] ?? 'MEDIUM',
                                    'comparison' => $item['comparison_potential'] ?? 'MEDIUM',
                                    'risks' => $item['risk_notes'] ?? '',
                                    'confidence' => $item['confidence'] ?? 80
                                ), JSON_UNESCAPED_UNICODE),
                                'evidence_type' => 'AI_ANALYSIS',
                                'source_url' => $sourceUrl
                            )
                        )
                    ));

                    $results[] = $dto;
                }
            }
        }

        return $results;
    }
}

class MockPlatformProvider extends BaseResearchProvider
{
    private $platformName;

    public function __construct($platformName = 'tiktok', $d = null, $func = null)
    {
        parent::__construct($d, $func);
        $this->platformName = strtolower($platformName);
    }

    public function getName()
    {
        return $this->platformName;
    }

    public function discover($seed, array $options = array())
    {
        $keyword = is_array($seed) ? ($seed['keyword'] ?? '') : (string)$seed;
        $maxResults = isset($options['max_results']) ? (int)$options['max_results'] : 3;

        $mockProducts = array(
            array(
                'name' => "Dây kháng lực " . ucfirst($keyword) . " cao cấp FITNADO Pro",
                'price' => 199000,
                'sales_count' => 4500,
                'rating' => 4.8,
                'review_count' => 890,
                'commission_rate' => 15.0,
                'creator_count' => 28,
                'video_count' => 52,
                'top_video_views' => 1200000,
                'category_hint' => 'Dây kháng lực & Phụ kiện',
                'brand_hint' => 'FITNADO Pro',
                'problem_solved' => 'Hỗ trợ tập luyện toàn thân tại nhà, tiện lợi mang theo khi đi du lịch hoặc công tác.',
                'target_audience' => 'Gymer tập tại nhà, người mới bắt đầu',
                'external_product_id' => 'MOCK_' . strtoupper($this->platformName) . '_' . substr(md5($keyword . '1'), 0, 8),
                'source_url' => "https://{$this->platformName}.com/mock-product-" . substr(md5($keyword . '1'), 0, 8)
            ),
            array(
                'name' => "Đai hỗ trợ tập tạ " . ucfirst($keyword) . " siêu bền",
                'price' => 350000,
                'sales_count' => 2800,
                'rating' => 4.9,
                'review_count' => 450,
                'commission_rate' => 12.5,
                'creator_count' => 19,
                'video_count' => 34,
                'top_video_views' => 780000,
                'category_hint' => 'Phụ kiện bảo hộ Gym',
                'brand_hint' => 'Aolikes Heavy',
                'problem_solved' => 'Bảo vệ thắt lưng và khớp cổ tay khi gánh tạ nặng, giảm thiểu nguy cơ chấn thương.',
                'target_audience' => 'Gymer trung cấp & nâng cao',
                'external_product_id' => 'MOCK_' . strtoupper($this->platformName) . '_' . substr(md5($keyword . '2'), 0, 8),
                'source_url' => "https://{$this->platformName}.com/mock-product-" . substr(md5($keyword . '2'), 0, 8)
            )
        );

        $results = array();
        $count = 0;
        foreach ($mockProducts as $mp) {
            if ($count >= $maxResults) break;
            $dto = new ResearchCandidateDTO(array(
                'name' => $mp['name'],
                'platform' => $this->platformName,
                'source_url' => $mp['source_url'],
                'external_product_id' => $mp['external_product_id'],
                'price' => $mp['price'],
                'sales_count' => $mp['sales_count'],
                'rating' => $mp['rating'],
                'review_count' => $mp['review_count'],
                'commission_rate' => $mp['commission_rate'],
                'commission_value' => $mp['price'] * ($mp['commission_rate'] / 100),
                'creator_count' => $mp['creator_count'],
                'video_count' => $mp['video_count'],
                'top_video_views' => $mp['top_video_views'],
                'category_hint' => $mp['category_hint'],
                'brand_hint' => $mp['brand_hint'],
                'problem_solved' => $mp['problem_solved'],
                'target_audience' => $mp['target_audience'],
                'primary_keyword' => $keyword,
                'discovery_source' => $this->platformName . '_crawler',
                'raw_data' => $mp,
                'evidence' => array(
                    array(
                        'field_name' => 'sales_count',
                        'field_value' => (string)$mp['sales_count'],
                        'evidence_type' => 'FACT',
                        'source_url' => $mp['source_url']
                    ),
                    array(
                        'field_name' => 'rating',
                        'field_value' => (string)$mp['rating'],
                        'evidence_type' => 'FACT',
                        'source_url' => $mp['source_url']
                    ),
                    array(
                        'field_name' => 'commission_rate',
                        'field_value' => (string)$mp['commission_rate'] . '%',
                        'evidence_type' => 'FACT',
                        'source_url' => $mp['source_url']
                    )
                )
            ));
            $results[] = $dto;
            $count++;
        }

        return $results;
    }
}

class CsvProvider extends BaseResearchProvider
{
    public function getName()
    {
        return 'csv';
    }

    public function discover($seed, array $options = array())
    {
        $csvRows = isset($options['rows']) && is_array($options['rows']) ? $options['rows'] : array();
        $results = array();

        foreach ($csvRows as $row) {
            if (empty($row['name'])) continue;
            $dto = new ResearchCandidateDTO(array(
                'name' => trim($row['name']),
                'platform' => $row['platform'] ?? 'other',
                'source_url' => $row['source_url'] ?? '',
                'external_product_id' => $row['external_product_id'] ?? null,
                'image_url' => $row['image_url'] ?? null,
                'price' => isset($row['price']) ? (float)$row['price'] : null,
                'sales_count' => isset($row['sales_count']) ? (int)$row['sales_count'] : null,
                'rating' => isset($row['rating']) ? (float)$row['rating'] : null,
                'review_count' => isset($row['review_count']) ? (int)$row['review_count'] : null,
                'commission_rate' => isset($row['commission_rate']) ? (float)$row['commission_rate'] : null,
                'category_hint' => $row['category_hint'] ?? null,
                'brand_hint' => $row['brand_hint'] ?? null,
                'problem_solved' => $row['problem_solved'] ?? null,
                'discovery_source' => 'csv_import',
                'raw_data' => $row,
                'evidence' => array(
                    array(
                        'field_name' => 'csv_import_row',
                        'field_value' => json_encode($row, JSON_UNESCAPED_UNICODE),
                        'evidence_type' => 'RAW_PAYLOAD',
                        'source_url' => $row['source_url'] ?? ''
                    )
                )
            ));
            $results[] = $dto;
        }

        return $results;
    }
}

class ResearchProviderFactory
{
    public static function create($providerName, $d = null, $func = null)
    {
        $name = strtolower(trim($providerName));
        switch ($name) {
            case 'ai':
            case 'ai_agent':
            case 'gemini':
                return new AiResearchProvider($d, $func);
            case 'tiktok':
                return new MockPlatformProvider('tiktok', $d, $func);
            case 'shopee':
                return new MockPlatformProvider('shopee', $d, $func);
            case 'lazada':
                return new MockPlatformProvider('lazada', $d, $func);
            case 'csv':
                return new CsvProvider($d, $func);
            case 'manual':
            default:
                return new ManualProvider($d, $func);
        }
    }
}
