<?php
/**
 * FITNADO - AI Research Agent & LLM Abstraction (Phase 04)
 * PHP 7.4 Compatible
 */

interface AIProviderInterface
{
    public function generateStructuredResponse($prompt, array $schema = array(), array $options = array());
    public function getName();
}

class AIResearchAgent
{
    private $d;
    private $func;
    private $provider;
    private $promptVersion = 'research-v1.0';

    public function __construct($d = null, $func = null, $providerName = null)
    {
        $this->d = $d;
        $this->func = $func;
        $this->initProvider($providerName);
    }

    private function initProvider($name = null)
    {
        $config = $this->getAiConfig();
        $providerName = $name ?: ($config['active_provider'] ?? 'mock');

        if ($providerName === 'gemini' && !empty($config['gemini_api_key'])) {
            $this->provider = new GeminiAIProvider($config['gemini_api_key'], $config['gemini_model'] ?? 'gemini-1.5-flash');
        } elseif ($providerName === 'openai' && !empty($config['openai_api_key'])) {
            $this->provider = new OpenAICompatibleProvider($config['openai_api_key'], $config['openai_endpoint'] ?? 'https://api.openai.com/v1', $config['openai_model'] ?? 'gpt-4o-mini');
        } else {
            $this->provider = new MockAIProvider();
        }
    }

    public function getAiConfig()
    {
        if ($this->d) {
            $setting = $this->d->rawQueryOne("select options from #_setting limit 0,1");
            if (!empty($setting['options'])) {
                $opts = json_decode($setting['options'], true);
                if (!empty($opts['ai_research_config']) && is_array($opts['ai_research_config'])) {
                    return $opts['ai_research_config'];
                }
            }
        }
        return array(
            'active_provider' => 'mock',
            'gemini_api_key' => '',
            'gemini_model' => 'gemini-1.5-flash',
            'openai_api_key' => '',
            'openai_model' => 'gpt-4o-mini',
            'daily_request_limit' => 50,
            'default_depth' => 'STANDARD',
            'requests_today' => 0,
            'last_reset_date' => date('Y-m-d')
        );
    }

    public function saveAiConfig(array $newConfig)
    {
        if (!$this->d) return false;
        $setting = $this->d->rawQueryOne("select id, options from #_setting limit 0,1");
        $opts = (!empty($setting['options'])) ? json_decode($setting['options'], true) : array();
        if (!is_array($opts)) $opts = array();

        $current = $opts['ai_research_config'] ?? array();
        // If api keys are submitted masked, preserve existing key
        if (!empty($newConfig['gemini_api_key']) && strpos($newConfig['gemini_api_key'], '****') !== false) {
            $newConfig['gemini_api_key'] = $current['gemini_api_key'] ?? '';
        }
        if (!empty($newConfig['openai_api_key']) && strpos($newConfig['openai_api_key'], '****') !== false) {
            $newConfig['openai_api_key'] = $current['openai_api_key'] ?? '';
        }

        $opts['ai_research_config'] = array_merge($current, $newConfig);

        return $this->d->rawQuery(
            "update #_setting set options = ? where id = ?",
            array(json_encode($opts, JSON_UNESCAPED_UNICODE), $setting['id'] ?? 1)
        );
    }

    public function getPromptVersion()
    {
        return $this->promptVersion;
    }

    /**
     * Generate Structured Response with Retry & Strict Validation
     */
    public function generateStructuredResponse($prompt, array $schema = array(), array $options = array())
    {
        $maxAttempts = isset($options['max_attempts']) ? (int)$options['max_attempts'] : 3;
        $attempt = 0;
        $lastError = '';

        // Check daily rate limits
        $config = $this->getAiConfig();
        $today = date('Y-m-d');
        if (($config['last_reset_date'] ?? '') !== $today) {
            $config['requests_today'] = 0;
            $config['last_reset_date'] = $today;
        }

        $dailyLimit = (int)($config['daily_request_limit'] ?? 50);
        if (($config['requests_today'] ?? 0) >= $dailyLimit) {
            return array(
                'status' => false,
                'error' => "Vượt quá giới hạn gọi AI hôm nay ({$dailyLimit} requests/ngày)",
                'data' => null
            );
        }

        while ($attempt < $maxAttempts) {
            $attempt++;
            try {
                $rawResult = $this->provider->generateStructuredResponse($prompt, $schema, $options);
                if ($rawResult['status'] && !empty($rawResult['data'])) {
                    // Increment request count
                    $config['requests_today'] = ($config['requests_today'] ?? 0) + 1;
                    $this->saveAiConfig($config);

                    return array(
                        'status' => true,
                        'data' => $rawResult['data'],
                        'provider' => $this->provider->getName(),
                        'prompt_version' => $this->promptVersion,
                        'attempts' => $attempt
                    );
                } else {
                    $lastError = $rawResult['error'] ?? 'Invalid structured response from AI provider';
                }
            } catch (Exception $e) {
                $lastError = $e->getMessage();
            }
        }

        return array(
            'status' => false,
            'error' => "AI Response Failed after {$maxAttempts} attempts: {$lastError}",
            'data' => null
        );
    }

    /**
     * Analyze a single product candidate in-depth
     */
    public function analyzeCandidate(array $candidateData, array $options = array())
    {
        $name = $candidateData['name'] ?? '';
        $platform = $candidateData['platform'] ?? 'tiktok';
        $price = $candidateData['price'] ?? 'Chưa rõ';
        $category = $candidateData['category_hint'] ?? '';
        $depth = $options['depth'] ?? 'STANDARD';

        $prompt = "Hãy phân tích chuyên sâu ứng viên sản phẩm Gym/Fitness sau cho FITNADO:\n";
        $prompt .= "- Tên sản phẩm: {$name}\n";
        $prompt .= "- Nền tảng: {$platform}\n";
        $prompt .= "- Giá bán: {$price} VND\n";
        $prompt .= "- Danh mục: {$category}\n";
        $prompt .= "- Mức độ phân tích: {$depth}\n\n";
        $prompt .= "QUY TẮC:\n";
        $prompt .= "1. Không tự bịa số liệu sự thật (sales, reviews, rating).\n";
        $prompt .= "2. Phân tích rõ: Vấn đề sản phẩm giải quyết (Pain point), Khách hàng mục tiêu, 3 Góc tiếp cận nội dung (Content angles), Tiềm năng thị giác/demo video (HIGH/MEDIUM/LOW), Tiềm năng so sánh đối đầu (HIGH/MEDIUM/LOW), Rủi ro/nhược điểm cần lưu ý, Độ tin cậy (0-100).\n";

        $schema = array(
            'type' => 'object',
            'properties' => array(
                'problem_solved' => 'string',
                'target_audience' => 'string',
                'content_angles' => 'array of string',
                'visual_demo_potential' => 'string (HIGH, MEDIUM, LOW)',
                'comparison_potential' => 'string (HIGH, MEDIUM, LOW)',
                'risk_notes' => 'string',
                'confidence' => 'number (0-100)'
            )
        );

        return $this->generateStructuredResponse($prompt, $schema);
    }
}

class MockAIProvider implements AIProviderInterface
{
    public function getName()
    {
        return 'mock_ai';
    }

    public function generateStructuredResponse($prompt, array $schema = array(), array $options = array())
    {
        // Check if prompt requests a list of candidates or a single analysis
        if (strpos($prompt, 'ứng viên sản phẩm Gym/Fitness sau') !== false) {
            return array(
                'status' => true,
                'data' => array(
                    'problem_solved' => 'Hỗ trợ tập luyện tăng cường sức mạnh cơ bắp và bảo vệ khớp an toàn khi tập gym tại nhà.',
                    'target_audience' => 'Người mới bắt đầu tập luyện, dân văn phòng bận rộn muốn giữ dáng.',
                    'content_angles' => array(
                        'Thử thách 14 ngày cải thiện vóc dáng với dụng cụ nhỏ gọn',
                        'So sánh hiệu quả tập tại nhà vs máy tạ phòng gym',
                        'Top 3 lỗi sai phổ biến khiến tập không vào cơ và cách sửa'
                    ),
                    'visual_demo_potential' => 'HIGH',
                    'comparison_potential' => 'HIGH',
                    'risk_notes' => 'Cần kiểm tra độ bền dây và móc khóa trước khi dùng mức tải nặng nhất.',
                    'confidence' => 88
                )
            );
        }

        $seedHash = substr(md5($prompt), 0, 8);
        // Discovery candidates list
        return array(
            'status' => true,
            'data' => array(
                array(
                    'name' => 'Dây kháng lực tập mông đùi Fabric Booty Bands',
                    'platform' => 'tiktok',
                    'source_url' => 'https://tiktok.com/@fitnado/product/MOCK_FABRIC_BOOTY_' . $seedHash,
                    'external_product_id' => 'MOCK_BOOTY_' . $seedHash,
                    'category_hint' => 'Dây kháng lực & Phụ kiện mông đùi',
                    'brand_hint' => 'FITNADO Shape',
                    'problem_solved' => 'Không bị cuộn hay trượt khi tập squat/hip thrust như dây cao su thông thường.',
                    'target_audience' => 'Phái nữ muốn phát triển vòng 3 săn chắc tại nhà',
                    'primary_keyword' => 'day khang luc tap mong vai booty band',
                    'content_angles' => array('5 bài tập mông không to đùi', 'Cách chọn size dây booty band chuẩn'),
                    'visual_demo_potential' => 'HIGH',
                    'comparison_potential' => 'HIGH',
                    'risk_notes' => 'Cần phân loại 3 cấp độ kháng lực rõ ràng cho người mới',
                    'estimated_price' => 175000,
                    'confidence' => 90
                ),
                array(
                    'name' => 'Găng tay tập Gym có quấn cổ tay đệm Silicone chống chai',
                    'platform' => 'shopee',
                    'source_url' => 'https://shopee.vn/product/MOCK_GYM_GLOVES_' . $seedHash,
                    'external_product_id' => 'MOCK_GLOVE_' . $seedHash,
                    'category_hint' => 'Găng tay & Bảo hộ',
                    'brand_hint' => 'Aolikes Pro',
                    'problem_solved' => 'Chống chai tay, trợ lực cổ tay tránh trật khớp khi đẩy ngực bench press hoặc đu xà pull-up.',
                    'target_audience' => 'Gymer cả nam và nữ sợ chai lòng bàn tay và đau khớp cổ tay',
                    'primary_keyword' => 'gang tay tap gym co cuon co tay',
                    'content_angles' => array('Test độ bám của đệm silicone khi mồ hôi nhiều', 'Đeo găng tay vs dùng phấn tập xà'),
                    'visual_demo_potential' => 'MEDIUM',
                    'comparison_potential' => 'HIGH',
                    'risk_notes' => 'Chọn size chuẩn theo chu vi lòng bàn tay',
                    'estimated_price' => 149000,
                    'confidence' => 85
                )
            )
        );
    }
}

class GeminiAIProvider implements AIProviderInterface
{
    private $apiKey;
    private $model;

    public function __construct($apiKey, $model = 'gemini-1.5-flash')
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    public function getName()
    {
        return 'gemini';
    }

    public function generateStructuredResponse($prompt, array $schema = array(), array $options = array())
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $systemInstruction = $options['system_instruction'] ?? "Bạn là trợ lý AI nghiên cứu sản phẩm Gym/Fitness của FITNADO. Hãy trả về kết quả bằng định dạng JSON hợp lệ thuần túy.";

        $body = array(
            'system_instruction' => array('parts' => array(array('text' => $systemInstruction))),
            'contents' => array(
                array('parts' => array(array('text' => $prompt . "\n\nQUAN TRỌNG: Chỉ trả về JSON nguyên bản, không dùng markdown ```json ... ``` bao quanh.")))
            ),
            'generationConfig' => array(
                'temperature' => 0.2,
                'responseMimeType' => 'application/json'
            )
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return array('status' => false, 'error' => "cURL Error: " . $err);
        }

        if ($httpCode !== 200) {
            return array('status' => false, 'error' => "HTTP {$httpCode}: " . $response);
        }

        $resData = json_decode($response, true);
        $text = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $text = trim($text);

        // Strip markdown backticks if present
        if (substr($text, 0, 7) === "```json") {
            $text = substr($text, 7);
            if (substr($text, -3) === "```") $text = substr($text, 0, -3);
        } elseif (substr($text, 0, 3) === "```") {
            $text = substr($text, 3);
            if (substr($text, -3) === "```") $text = substr($text, 0, -3);
        }
        $text = trim($text);

        $parsed = json_decode($text, true);
        if ($parsed === null) {
            return array('status' => false, 'error' => 'Invalid JSON returned from Gemini: ' . substr($text, 0, 200));
        }

        return array('status' => true, 'data' => $parsed);
    }
}

class OpenAICompatibleProvider implements AIProviderInterface
{
    private $apiKey;
    private $endpoint;
    private $model;

    public function __construct($apiKey, $endpoint = 'https://api.openai.com/v1', $model = 'gpt-4o-mini')
    {
        $this->apiKey = $apiKey;
        $this->endpoint = rtrim($endpoint, '/');
        $this->model = $model;
    }

    public function getName()
    {
        return 'openai';
    }

    public function generateStructuredResponse($prompt, array $schema = array(), array $options = array())
    {
        $url = "{$this->endpoint}/chat/completions";
        $systemInstruction = $options['system_instruction'] ?? "Bạn là trợ lý AI nghiên cứu sản phẩm Gym/Fitness của FITNADO. Hãy trả về kết quả bằng định dạng JSON hợp lệ thuần túy.";

        $body = array(
            'model' => $this->model,
            'messages' => array(
                array('role' => 'system', 'content' => $systemInstruction),
                array('role' => 'user', 'content' => $prompt . "\n\nQUAN TRỌNG: Chỉ trả về JSON nguyên bản.")
            ),
            'temperature' => 0.2,
            'response_format' => array('type' => 'json_object')
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return array('status' => false, 'error' => "cURL Error: " . $err);
        }

        if ($httpCode !== 200) {
            return array('status' => false, 'error' => "HTTP {$httpCode}: " . $response);
        }

        $resData = json_decode($response, true);
        $content = $resData['choices'][0]['message']['content'] ?? '';
        $parsed = json_decode($content, true);

        if ($parsed === null) {
            return array('status' => false, 'error' => 'Invalid JSON returned from OpenAI: ' . substr($content, 0, 200));
        }

        return array('status' => true, 'data' => $parsed);
    }
}
