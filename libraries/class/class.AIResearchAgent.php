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
        // 1. Phase 05: Product Analysis
        if (strpos($prompt, 'phân tích chuyên sâu 12 khía cạnh') !== false) {
            return array(
                'status' => true,
                'provider' => 'mock',
                'model' => 'mock-content-v1',
                'data' => array(
                    'problem_solved' => 'Hỗ trợ bảo vệ cổ tay và lòng bàn tay, tăng độ bám khi thực hiện các bài kéo nặng (Deadlift, Pull-up) và đẩy ngực (Bench Press).',
                    'target_audience' => 'Người tập gym từ cơ bản đến nâng cao, người hay bị trơn trượt mồ hôi tay khi nâng tạ.',
                    'key_benefits' => array(
                        'Tăng sức nắm và trợ lực cổ tay giúp nâng tạ nặng hơn',
                        'Đệm silicon chống chai tay và phân bổ áp lực đều',
                        'Chất liệu thoáng khí thoát mồ hôi nhanh chóng'
                    ),
                    'limitations' => array(
                        'Cần chọn đúng size tay để ôm khít',
                        'Cần giặt tay để bảo vệ độ bám của hạt silicon'
                    ),
                    'pros' => array(
                        'Quấn cổ tay dài 45cm chắc chắn',
                        'Chất liệu vải dệt co giãn 4 chiều siêu bền',
                        'Đường may kép gia cố ở các vị trí chịu lực'
                    ),
                    'cons' => array(
                        'Hơi ấm tay khi tập trong phòng gym không có máy lạnh',
                        'Màu sắc hạn chế (chủ yếu màu đen/xám)'
                    ),
                    'suitable_for' => array(
                        'Gymer tập bài Deadlift, Xà đơn, Đẩy ngực',
                        'Người sợ chai lòng bàn tay và đau khớp cổ tay'
                    ),
                    'not_suitable_for' => array(
                        'Người chỉ tập cardio chạy bộ nhẹ nhàng',
                        'Người bị dị ứng với thành phần cao su/silicon'
                    ),
                    'buying_considerations' => array(
                        'Nên đo chu vi cổ tay và lòng bàn tay trước khi chọn size',
                        'Ưu tiên mua kèm ưu đãi từ gian hàng Mall chính hãng để đảm bảo độ bền'
                    ),
                    'comparison_angles' => array(
                        'So sánh độ êm vs găng tay da bò truyền thống',
                        'So sánh độ tiện dụng vs dây kéo lưng lifting straps thông thường'
                    ),
                    'risk_notes' => 'Kiểm tra độ chắc của miếng dán velcro trước mỗi set tập nặng.',
                    'confidence' => 92
                )
            );
        }

        // 2. Phase 05: TikTok Hooks (7 Strategic Variations)
        if (strpos($prompt, '7 biến thể TikTok Strategic Hooks') !== false) {
            return array(
                'status' => true,
                'provider' => 'mock',
                'model' => 'mock-content-v1',
                'data' => array(
                    'hooks' => array(
                        array(
                            'hook_type' => 'problem',
                            'headline' => 'Tập Deadlift mà tạ chưa tuột mà tay đã đau rát?',
                            'hook_script' => 'Có ai tập deadlift mà tạ chưa kịp mỏi lưng xô mà tay đã tuột không giữ nổi không? Đây là lý do!',
                            'target_emotion' => 'Đồng cảm và chạm đúng nỗi đau thể lực',
                            'visual_action' => 'Quay cận cảnh lòng bàn tay đỏ ửng và tuột thanh đòn tạ'
                        ),
                        array(
                            'hook_type' => 'mistake',
                            'headline' => 'Sai lầm 90% người mới tập tạ đều mắc phải!',
                            'hook_script' => 'Đừng bao giờ gánh tạ nặng bằng cổ tay trần nếu bạn không muốn nghỉ tập 3 tháng vì viêm khớp!',
                            'target_emotion' => 'Cảnh báo và tò mò',
                            'visual_action' => 'Chèn hiệu ứng tia sét đỏ cảnh báo vào cổ tay đang bẻ gập'
                        ),
                        array(
                            'hook_type' => 'comparison',
                            'headline' => 'Dây kéo lưng 50k vs Dây FITNADO Pro khác nhau thế nào?',
                            'hook_script' => 'Cùng là dây kéo lưng nhưng tại sao loại này gymer chuyên nghiệp dùng còn loại kia thì rách sau 2 buổi?',
                            'target_emotion' => 'Tò mò so sánh chất lượng',
                            'visual_action' => 'Đặt 2 sản phẩm cạnh nhau và test lực kéo'
                        ),
                        array(
                            'hook_type' => 'curiosity',
                            'headline' => 'Món phụ kiện gym bí mật giúp tăng 10kg mức tạ ngay lập tức!',
                            'hook_script' => 'Nếu bạn đang bị chững mức tạ xô và deadlift, món đồ nhỏ này sẽ thay đổi hoàn toàn buổi tập của bạn.',
                            'target_emotion' => 'Háo hức và mong muốn bứt phá',
                            'visual_action' => 'Lấy sản phẩm ra từ túi gym và quấn vào thanh tạ'
                        ),
                        array(
                            'hook_type' => 'demo',
                            'headline' => 'Thử thách treo người trên xà đơn 2 phút không tuột tay!',
                            'hook_script' => 'Hôm nay mình sẽ test độ bám thực tế của em quấn cổ tay này trên thanh xà trơn xem chịu được bao lâu nhé!',
                            'target_emotion' => 'Hồi hộp theo dõi thử thách',
                            'visual_action' => 'Đồng hồ đếm ngược trên màn hình khi đang đu xà'
                        ),
                        array(
                            'hook_type' => 'buyer_warning',
                            'headline' => 'Dừng lại ngay nếu bạn đang định mua găng tay tập gym giá rẻ!',
                            'hook_script' => 'Trước khi bấm mua bất kỳ đôi găng tay nào trên mạng, hãy kiểm tra 3 điểm này để không phí tiền!',
                            'target_emotion' => 'Thận trọng bảo vệ túi tiền',
                            'visual_action' => 'Giơ tay làm động tác dừng lại trước màn hình'
                        ),
                        array(
                            'hook_type' => 'value',
                            'headline' => 'Chỉ hơn 100k nhưng bảo vệ cổ tay bạn suốt cả năm tập luyện!',
                            'hook_script' => 'Một buổi đi khám cổ tay tốn cả triệu, trong khi giải pháp phòng ngừa chỉ bằng 2 ly trà sữa!',
                            'target_emotion' => 'Thấy rõ giá trị đầu tư kinh tế',
                            'visual_action' => 'Hiển thị so sánh chi phí đồ bảo hộ vs chi phí điều trị'
                        )
                    )
                )
            );
        }

        // 3. Phase 05: TikTok Script + Shot Plan
        if (strpos($prompt, 'kịch bản video ngắn TikTok') !== false) {
            return array(
                'status' => true,
                'provider' => 'mock',
                'model' => 'mock-content-v1',
                'data' => array(
                    'title' => 'Bí Quyết Nâng Tạ Nặng Không Lo Đau Cổ Tay',
                    'target_duration' => 30,
                    'hook_text' => 'Tập deadlift mà tạ chưa mỏi cơ xô mà tay đã tuột? Xem ngay bí quyết này!',
                    'full_script' => "Tập deadlift mà tạ chưa mỏi cơ xô mà tay đã tuột? Đừng để sức nắm cổ tay làm giới hạn sự phát triển của cơ bắp! Em dây kéo lưng FITNADO Pro này sử dụng đệm cao su non chống trượt và quấn trợ lực cổ tay chuẩn công thái học. Thử kéo 100kg vẫn êm ru, không hề cấn rát da. Nhược điểm duy nhất là nhớ chọn đúng chiều quấn trái phải nhé. Bấm ngay vào giỏ hàng góc trái để xem ưu đãi độc quyền hôm nay!",
                    'cta_text' => 'Bấm ngay vào link/giỏ hàng bên dưới để nhận ưu đãi chính hãng hôm nay!',
                    'shot_plan' => array(
                        array(
                            'scene_number' => 1,
                            'duration' => 3,
                            'visual_instruction' => 'Cận cảnh thanh tạ trượt khỏi tay và rơi xuống sàn cao su',
                            'voiceover' => 'Tập deadlift mà cơ chưa mỏi mà tay đã tuột thanh tạ?',
                            'on_screen_text' => 'TẠ TUỘT KHI TẬP NẶNG? 😱',
                            'asset_requirement' => 'Video thanh tạ deadlift trượt tay'
                        ),
                        array(
                            'scene_number' => 2,
                            'duration' => 5,
                            'visual_instruction' => 'Cảnh gymer xoa cổ tay bị đỏ rát sau set tập',
                            'voiceover' => 'Đừng để sức nắm yếu làm giảm hiệu quả phát triển cơ lưng xô của bạn!',
                            'on_screen_text' => 'SỨC NẮM LÀ ĐIỂM YẾU? ❌',
                            'asset_requirement' => 'Video cổ tay đỏ rát'
                        ),
                        array(
                            'scene_number' => 3,
                            'duration' => 10,
                            'visual_instruction' => 'Quay chi tiết thao tác quấn dây FITNADO Pro vào đòn tạ và nhấc lên nhẹ nhàng',
                            'voiceover' => 'Giải pháp là dòng dây kéo lưng trợ lực FITNADO Pro với đệm cao su non bám dính siêu chắc.',
                            'on_screen_text' => 'FITNADO PRO - KHÓA TẠ CHẮC CHẮN 🔥',
                            'asset_requirement' => 'Video sản phẩm thực tế'
                        ),
                        array(
                            'scene_number' => 4,
                            'duration' => 7,
                            'visual_instruction' => 'Góc máy ngang người tập kéo mức tạ nặng với tư thế chuẩn form',
                            'voiceover' => 'Trợ lực đến 80% sức nắm, giúp bạn tập trung 100% vào việc co bóp cơ bắp.',
                            'on_screen_text' => 'TĂNG MỨC TẠ + BẢO VỆ CỔ TAY 💪',
                            'asset_requirement' => 'Video deadlift chuẩn form'
                        ),
                        array(
                            'scene_number' => 5,
                            'duration' => 3,
                            'visual_instruction' => 'Cận cảnh đường may kép và logo FITNADO chính hãng',
                            'voiceover' => 'Lưu ý nhớ quấn đúng chiều theo hướng dẫn đi kèm nhé.',
                            'on_screen_text' => 'CHẤT LƯỢNG GIA CÔNG CAO CẤP ⭐',
                            'asset_requirement' => 'Ảnh chi tiết đường may'
                        ),
                        array(
                            'scene_number' => 6,
                            'duration' => 2,
                            'visual_instruction' => 'Tay chỉ xuống góc trái màn hình nơi có icon giỏ hàng / link affiliate',
                            'voiceover' => 'Bấm ngay vào link bên dưới để xem giá ưu đãi tốt nhất hôm nay!',
                            'on_screen_text' => 'XEM ƯU ĐÃI NGAY 👇',
                            'asset_requirement' => 'Mũi tên chỉ icon giỏ hàng'
                        )
                    )
                )
            );
        }

        // 4. Phase 05: SEO Metadata & FAQs
        if (strpos($prompt, 'bộ tối ưu hóa SEO') !== false) {
            return array(
                'status' => true,
                'provider' => 'mock',
                'model' => 'mock-content-v1',
                'data' => array(
                    'primary_keyword' => 'dây kéo lưng tập gym lifting straps',
                    'secondary_keywords' => array(
                        'dây trợ lực deadlift',
                        'găng tay quấn cổ tay tập gym',
                        'phụ kiện gym bảo hộ cổ tay',
                        'dây kéo lưng gym chính hãng giá tốt'
                    ),
                    'search_intent' => 'Commercial / Transactional',
                    'seo_title' => 'Dây Kéo Lưng Tập Gym FITNADO Pro - Đánh Giá & Mua Giá Tốt Nhất',
                    'seo_description' => 'Đánh giá chi tiết dây kéo lưng lifting straps FITNADO Pro. Trợ lực sức nắm 80%, bảo vệ cổ tay, đệm cao su non chống trượt. Xem giá ưu đãi đa sàn tại FITNADO.',
                    'article_outline' => array(
                        '1. Dây kéo lưng lifting straps là gì và vì sao gymer cần có?',
                        '2. Đánh giá thiết kế, chất liệu đệm và độ bám thực tế của FITNADO Pro',
                        '3. Hướng dẫn cách quấn dây kéo lưng đúng kỹ thuật tránh chấn thương',
                        '4. So sánh mức giá và chính sách bảo hành giữa các sàn thương mại'
                    ),
                    'faqs' => array(
                        array(
                            'question' => 'Dây kéo lưng có làm yếu sức nắm tự nhiên của bàn tay không?',
                            'answer' => 'Không nếu bạn sử dụng hợp lý. Chỉ nên dùng dây kéo lưng ở các hiệp tập tạ nặng nhất (top set) khi sức nắm bị đuối trước nhóm cơ chính.'
                        ),
                        array(
                            'question' => 'Nên chọn loại dây kéo lưng Cotton hay Da bò?',
                            'answer' => 'Dây cotton bọc silicon phù hợp với hầu hết bài tập vì mềm mại và thấm mồ hôi tốt. Dây da bò phù hợp cho các bài deadlift cực nặng (Powerlifting).'
                        ),
                        array(
                            'question' => 'Cách vệ sinh và bảo quản dây kéo lưng như thế nào?',
                            'answer' => 'Nên giặt tay bằng nước ấm và xà phòng loãng sau mỗi 1-2 tuần tập luyện, phơi ở nơi thoáng mát tránh ánh nắng gắt trực tiếp.'
                        )
                    )
                )
            );
        }

        // 5. Phase 05: Editorial Review Draft
        if (strpos($prompt, 'bản thảo bài viết đánh giá chuyên sâu') !== false) {
            return array(
                'status' => true,
                'provider' => 'mock',
                'model' => 'mock-content-v1',
                'data' => array(
                    'article_title' => 'Đánh Giá Chi Tiết Dây Kéo Lưng FITNADO Pro: Trợ Lực Tốt, Đáng Mua Trong Tầm Giá',
                    'summary' => 'Một trong những phụ kiện gym không thể thiếu cho những buổi tập lưng xô và deadlift nặng.',
                    'article_body' => "<h2>1. Giới thiệu tổng quan</h2><p>Trong quá trình tập luyện thể hình, sức nắm của ngón tay và khớp cổ tay thường là điểm yếu đầu tiên bị mỏi trước khi nhóm cơ lưng xô đạt ngưỡng kích thích tối đa. Dây kéo lưng FITNADO Pro ra đời để giải quyết triệt để bài toán này.</p><h2>2. Đánh giá chất liệu và độ hoàn thiện</h2><p>Sản phẩm sử dụng sợi dệt mật độ cao kết hợp đệm mút cao su Neoprene dày 5mm tại vị trí tiếp xúc cổ tay, giúp giảm thiểu tối đa hiện tượng cấn rát hay bầm tím khi gánh tạ trên 100kg.</p><h2>3. Trải nghiệm thực tế khi tập luyện</h2><p>Khả năng ma sát của lớp vân cao su non giúp khóa chặt thanh đòn tạ chỉ với một vòng quấn. Người tập có thể thả lỏng các ngón tay mà tạ vẫn được giữ vững chắc.</p><h2>4. Lời khuyên chọn mua</h2><p>Với mức giá hợp lý cùng độ bền cao, đây là sự đầu tư xứng đáng cho bất kỳ ai muốn nâng cao hiệu suất tập luyện mà vẫn đảm bảo an toàn khớp cổ tay.</p>",
                    'verdict_summary' => 'Sản phẩm hoàn thiện chắc chắn, trợ lực hiệu quả, rất đáng sở hữu cho người tập thể hình từ cơ bản đến nâng cao.',
                    'pros_summary' => array('Đệm Neoprene êm ái', 'Độ bám cao', 'Giá thành hợp lý'),
                    'cons_summary' => array('Cần thời gian làm quen cách quấn đúng chiều')
                )
            );
        }

        // 6. Phase 04: Single candidate research analysis
        if (strpos($prompt, 'ứng viên sản phẩm Gym/Fitness sau') !== false) {
            return array(
                'status' => true,
                'provider' => 'mock',
                'model' => 'mock-research-v1',
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
