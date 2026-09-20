<?php
/**
 * FITNADO Voice Service & Voice Preparation Engine
 * Phase 06.4: Natural Vietnamese Voice Benchmark
 * Flow: Approved Script -> Voice Preparation -> TTS Provider -> Cached Audio & ffprobe Duration
 * PHP 7.4 / 8.2 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

require_once LIBRARIES . 'class/class.VoiceProvider.php';

class VoiceService {
    private $d;
    private $func;
    private $resolvedFfprobe;
    private $resolvedFFmpeg;

    // Default Pronunciation Dictionary for Brand and Fitness Terminology
    const DEFAULT_PRONUNCIATION_DICT = array(
        'FITNADO' => 'Phít na đô',
        'Fitnado' => 'Phít na đô',
        'fitnado' => 'Phít na đô',
        'TikTok' => 'Tíc tóc',
        'Tiktok' => 'Tíc tóc',
        'tiktok' => 'Tíc tóc',
        'Squat' => 'S quat',
        'squat' => 'S quat',
        'Deadlift' => 'Đét líp',
        'deadlift' => 'Đét líp',
        'Cardio' => 'Cạc đi ô',
        'cardio' => 'Cạc đi ô',
        'Powerlifting' => 'Pao oa líp tinh',
        'powerlifting' => 'Pao oa líp tinh',
        'Resistance Band' => 'Dây kháng lực',
        'resistance band' => 'Dây kháng lực',
        'Whey' => 'Uây',
        'whey' => 'Uây',
        'Creatine' => 'Cờ ri a tin',
        'creatine' => 'Cờ ri a tin',
        'Quick-Lock' => 'Quích lóc',
        'quick-lock' => 'Quích lóc',
        'Quicklock' => 'Quích lóc',
        'quicklock' => 'Quích lóc',
        'Pro Lever' => 'Pờ rô Le vơ',
        'pro lever' => 'Pờ rô Le vơ',
        'Lever Belt' => 'Đai đòn bẩy',
        'lever belt' => 'Đai đòn bẩy',
        'PR' => 'Pi A',
        'pr' => 'Pi A',
        'Rep' => 'Rép',
        'rep' => 'Rép',
        'reps' => 'Rép',
        'Set' => 'Sét',
        'set' => 'Sét',
        'sets' => 'Sét',
        'Gym' => 'Gim',
        'gym' => 'Gim',
        'Gymer' => 'Gim mơ',
        'gymer' => 'Gim mơ'
    );

    public function __construct($d = null, $func = null) {
        $this->d = $d;
        $this->func = $func;
        $this->detectFFmpegBinaries();
    }

    /**
     * Tự động phát hiện FFmpeg & FFprobe
     */
    private function detectFFmpegBinaries() {
        global $config;

        $ffmpegPath = $config['video_composer']['ffmpeg_path'] ?? 'ffmpeg';
        $ffprobePath = $config['video_composer']['ffprobe_path'] ?? 'ffprobe';

        if (file_exists($ffmpegPath)) {
            $this->resolvedFFmpeg = $ffmpegPath;
        } else {
            $this->resolvedFFmpeg = 'ffmpeg';
        }

        if (file_exists($ffprobePath)) {
            $this->resolvedFfprobe = $ffprobePath;
        } else {
            $this->resolvedFfprobe = 'ffprobe';
        }
    }

    /**
     * Lấy từ điển phiên âm hợp nhất (Mặc định + Admin Override từ DB)
     * @return array
     */
    public function getPronunciationDictionary() {
        $dict = self::DEFAULT_PRONUNCIATION_DICT;

        if ($this->d) {
            $settingRow = $this->d->rawQueryOne("SELECT options FROM table_setting LIMIT 1");
            if (!empty($settingRow['options'])) {
                $opts = json_decode($settingRow['options'], true);
                if (!empty($opts['pronunciation_dictionary']) && is_array($opts['pronunciation_dictionary'])) {
                    $dict = array_merge($dict, $opts['pronunciation_dictionary']);
                }
            }
        }

        return $dict;
    }

    /**
     * Lưu từ điển phiên âm tùy chỉnh vào CSDL
     * @param array $customDict
     * @return bool
     */
    public function savePronunciationDictionary(array $customDict) {
        if (!$this->d) return false;

        $settingRow = $this->d->rawQueryOne("SELECT options FROM table_setting LIMIT 1");
        $opts = !empty($settingRow['options']) ? json_decode($settingRow['options'], true) : array();

        $opts['pronunciation_dictionary'] = $customDict;

        return $this->d->rawQuery("UPDATE table_setting SET options = ? WHERE id = 1", array(
            json_encode($opts, JSON_UNESCAPED_UNICODE)
        ));
    }

    /**
     * Fact Evidence Traceability & Verification Engine (Phase 06 Hotfix)
     * Xác minh nguồn gốc các thông số kỹ thuật, vật liệu, cam kết bảo hành và đo lường
     * Nguồn hợp lệ: PRODUCT_ADMIN, PRODUCT_SPEC, RESEARCH_FACT, RESEARCH_EVIDENCE, EDITOR_APPROVED_FACT
     * Nếu không trace được -> đánh dấu UNVERIFIED
     * @param int|null $productId
     * @param string $text
     * @param array $options
     * @return array
     */
    public function verifyFactualClaims($productId, $text, array $options = array()) {
        $text = trim($text);
        if (empty($text)) {
            return array('verified' => true, 'claims' => array(), 'unverified_count' => 0, 'cleaned_text' => '');
        }

        // 1. Thu thập dữ liệu nguồn xác thực
        $factCorpus = array(
            'PRODUCT_ADMIN' => '',
            'PRODUCT_SPEC' => '',
            'RESEARCH_FACT' => '',
            'RESEARCH_EVIDENCE' => '',
            'EDITOR_APPROVED_FACT' => ''
        );

        if ($this->d && !empty($productId)) {
            $prod = $this->d->rawQueryOne("SELECT namevi, descvi, contentvi, specs FROM table_product WHERE id = ? LIMIT 1", array((int)$productId));
            if (!empty($prod)) {
                $factCorpus['PRODUCT_ADMIN'] = mb_strtolower(($prod['namevi'] ?? '') . ' ' . ($prod['descvi'] ?? '') . ' ' . strip_tags($prod['contentvi'] ?? ''), 'UTF-8');
                $factCorpus['PRODUCT_SPEC'] = mb_strtolower($prod['specs'] ?? '', 'UTF-8');
            }

            $research = $this->d->rawQueryOne("SELECT id, name, problem_solved, target_audience, research_notes FROM table_product_research WHERE id_product = ? ORDER BY id DESC LIMIT 1", array((int)$productId));
            if (!empty($research)) {
                $factCorpus['RESEARCH_FACT'] = mb_strtolower(($research['name'] ?? '') . ' ' . ($research['problem_solved'] ?? '') . ' ' . ($research['target_audience'] ?? '') . ' ' . ($research['research_notes'] ?? ''), 'UTF-8');
                
                $evidences = $this->d->rawQuery("SELECT field_name, field_value FROM table_product_research_evidence WHERE id_research = ? AND evidence_type = 'FACT'", array((int)$research['id']));
                if (!empty($evidences)) {
                    $evTexts = array();
                    foreach ($evidences as $ev) {
                        $evTexts[] = ($ev['field_name'] ?? '') . ' ' . ($ev['field_value'] ?? '');
                    }
                    $factCorpus['RESEARCH_EVIDENCE'] = mb_strtolower(implode(' ', $evTexts), 'UTF-8');
                }
            }
        }

        // 2. Trích xuất các claim kỹ thuật, vật liệu, số đo, cam kết
        $candidatePatterns = array(
            '/(\d+\s*(?:mm|cm|m|kg|g|gam|ml|l|%))/iu',
            '/(\bda\s+bò\b|\bda\s+thật\b|\bhợp\s+kim\b|\bnguyên\s+khối\b|\bthép\s+không\s+gỉ\b|\bđệm\s+eva\b|\bkhóa\s+đòn\s+bẩy\b)/iu',
            '/(\bbảo\s+hành\s+(?:\d+\s+năm|trọn\s+đời)\b)/iu',
            '/(\bchữa\s+khỏi\b|\btrị\s+dứt\s+điểm\b|\bbảo\s+vệ\s+100%\b|\bcam\s+kết\s+giảm\s+\d+kg\b)/iu'
        );

        $extractedClaims = array();
        foreach ($candidatePatterns as $pat) {
            if (preg_match_all($pat, $text, $matches)) {
                foreach ($matches[1] as $m) {
                    $extractedClaims[] = trim($m);
                }
            }
        }
        $extractedClaims = array_unique($extractedClaims);

        $claimsReport = array();
        $unverifiedCount = 0;
        $unverifiedTerms = array();

        foreach ($extractedClaims as $claim) {
            $claimLower = mb_strtolower($claim, 'UTF-8');
            $matchedSource = null;

            // Tìm kiếm trong từng nguồn Fact theo thứ tự ưu tiên
            foreach ($factCorpus as $sourceName => $sourceText) {
                if (!empty($sourceText) && mb_strpos($sourceText, $claimLower) !== false) {
                    $matchedSource = $sourceName;
                    break;
                }
            }

            if ($matchedSource !== null) {
                $claimsReport[] = array(
                    'claim' => $claim,
                    'status' => 'VERIFIED',
                    'source' => $matchedSource
                );
            } else {
                $unverifiedCount++;
                $unverifiedTerms[] = $claim;
                $claimsReport[] = array(
                    'claim' => $claim,
                    'status' => 'UNVERIFIED',
                    'source' => null
                );
            }
        }

        // 3. Lọc bỏ các claim unverified nếu có yêu cầu strict_filter
        $cleanedText = $text;
        if (!empty($options['strict_filter']) && !empty($unverifiedTerms)) {
            foreach ($unverifiedTerms as $unverified) {
                $escaped = preg_quote($unverified, '/');
                $cleanedText = preg_replace('/\b' . $escaped . '\b/u', '', $cleanedText);
            }
            $cleanedText = preg_replace('/\s+/', ' ', $cleanedText);
        }

        return array(
            'verified' => ($unverifiedCount === 0),
            'claims' => $claimsReport,
            'unverified_count' => $unverifiedCount,
            'unverified_terms' => $unverifiedTerms,
            'cleaned_text' => trim($cleanedText)
        );
    }

    /**
     * Voice Preparation Engine
     * Chuyển đổi văn bản kịch bản thô thành văn bản tối ưu cho phát âm tự nhiên
     * - Chuẩn hóa giá tiền, số, đơn vị đo lường
     * - Áp dụng từ điển phát âm thương hiệu & thuật ngữ Gym
     * - Tinh chỉnh nhịp thở và ngắt nghỉ tự nhiên (4-12 từ / phrase)
     * @param string $text
     * @param array $options
     * @return string
     */
    public function prepareSpokenScript($text, array $options = array()) {
        $text = trim($text);
        if (empty($text)) {
            return '';
        }

        // 0. Fact Traceability Check (Nếu có productId và cờ verify_facts)
        if (!empty($options['product_id']) && !empty($options['verify_facts'])) {
            $factCheck = $this->verifyFactualClaims((int)$options['product_id'], $text, $options);
            if (!empty($options['strict_filter']) && !$factCheck['verified']) {
                $text = $factCheck['cleaned_text'];
            }
        }

        // 1. Chuẩn hóa giá tiền (399.000đ, 399.000 VNĐ, 399k...)
        $text = $this->normalizePrices($text);

        // 2. Chuẩn hóa số phạm vi (10-15 lần, 8-12 rep...)
        $text = preg_replace_callback('/(\d+)\s*[-–—]\s*(\d+)\s*(lần|rep|hiệp|set|kg|cân|phút|giây)/u', function($m) {
            $num1 = $this->numberToVietnameseWords((int)$m[1]);
            $num2 = $this->numberToVietnameseWords((int)$m[2]);
            return $num1 . ' đến ' . $num2 . ' ' . $m[3];
        }, $text);

        // 3. Chuẩn hóa số kèm đơn vị (150kg, 10mm, 80%, 1.5kg...)
        $text = $this->normalizeNumbersAndUnits($text);

        // 4. Áp dụng từ điển phát âm thuật ngữ và thương hiệu
        $dict = $this->getPronunciationDictionary();
        foreach ($dict as $word => $replacement) {
            $escaped = preg_quote($word, '/');
            $text = preg_replace('/\b' . $escaped . '\b/u', $replacement, $text);
        }

        // 5. Tinh chỉnh nhịp điệu và ngắt câu tự nhiên cho Reviewer
        $text = $this->optimizePunctuationAndPauses($text);

        return trim($text);
    }

    /**
     * Chuẩn hóa giá tiền thành văn nói tiếng Việt
     * @param string $text
     * @return string
     */
    private function normalizePrices($text) {
        $text = preg_replace_callback('/(\d{1,3}(?:\.\d{3})+)\s*(?:đ|vnd|vnđ|đồng)/iu', function($m) {
            $cleanNum = (int)str_replace('.', '', $m[1]);
            return $this->numberToVietnameseWords($cleanNum) . ' đồng';
        }, $text);

        $text = preg_replace_callback('/(\d+)\s*k\s*(?:đ|vnd|vnđ|đồng)?\b/iu', function($m) {
            $cleanNum = (int)$m[1] * 1000;
            return $this->numberToVietnameseWords($cleanNum) . ' đồng';
        }, $text);

        return $text;
    }

    /**
     * Chuẩn hóa các đại lượng số và đơn vị đo
     * @param string $text
     * @return string
     */
    private function normalizeNumbersAndUnits($text) {
        // Số thập phân kèm đơn vị: 1.5kg, 2.5cm...
        $text = preg_replace_callback('/(\d+)[,\.](\d+)\s*(kg|cân|g|gam|cm|mm|ml|l|m|%)/iu', function($m) {
            $intPart = $this->numberToVietnameseWords((int)$m[1]);
            $decPart = $this->numberToVietnameseWords((int)$m[2]);
            $unitName = $this->unitToVietnameseWords($m[3]);
            return $intPart . ' phẩy ' . $decPart . ' ' . $unitName;
        }, $text);

        // Số nguyên kèm đơn vị: 150kg, 10mm, 80%, 2 năm, 1 giây...
        $text = preg_replace_callback('/(\d+)\s*(kg|cân|g|gam|cm|mm|ml|l|m|%|năm|tháng|ngày|giây|phút)\b/iu', function($m) {
            $numWords = $this->numberToVietnameseWords((int)$m[1]);
            $unitName = $this->unitToVietnameseWords($m[2]);
            return $numWords . ' ' . $unitName;
        }, $text);

        return $text;
    }

    /**
     * Chuyển đổi ký hiệu đơn vị thành văn bản tiếng Việt
     * @param string $unit
     * @return string
     */
    private function unitToVietnameseWords($unit) {
        $u = strtolower(trim($unit));
        switch ($u) {
            case 'kg':
            case 'cân':
                return 'cân';
            case 'g':
            case 'gam':
                return 'gam';
            case 'mm':
                return 'mi li mét';
            case 'cm':
                return 'xen ti mét';
            case 'm':
                return 'mét';
            case 'ml':
                return 'mi li lít';
            case 'l':
                return 'lít';
            case '%':
                return 'phần trăm';
            default:
                return $unit;
        }
    }

    /**
     * Chuyển đổi số nguyên thành chuỗi đọc tiếng Việt chuẩn xác
     * @param int $number
     * @return string
     */
    public function numberToVietnameseWords($number) {
        $number = (int)$number;
        if ($number === 0) return 'không';
        if ($number < 0) return 'âm ' . $this->numberToVietnameseWords(abs($number));

        $digits = array('không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín');
        $units = array('', 'nghìn', 'triệu', 'tỷ');

        $readThreeDigits = function($n, $isHighestGroup) use ($digits) {
            $h = (int)($n / 100);
            $t = (int)(($n % 100) / 10);
            $u = $n % 10;
            $res = '';

            if ($h > 0 || !$isHighestGroup) {
                $res .= $digits[$h] . ' trăm ';
            }

            if ($t > 1) {
                $res .= $digits[$t] . ' mươi ';
                if ($u === 1) $res .= 'mốt';
                elseif ($u === 5) $res .= 'lăm';
                elseif ($u > 0) $res .= $digits[$u];
            } elseif ($t === 1) {
                $res .= 'mười ';
                if ($u === 5) $res .= 'lăm';
                elseif ($u > 0) $res .= $digits[$u];
            } else {
                if ($u > 0) {
                    if ($h > 0 || !$isHighestGroup) $res .= 'lẻ ';
                    $res .= $digits[$u];
                }
            }

            return trim($res);
        };

        $groups = array();
        $temp = $number;
        while ($temp > 0) {
            $groups[] = $temp % 1000;
            $temp = (int)($temp / 1000);
        }

        $out = array();
        $groupCount = count($groups);
        for ($i = $groupCount - 1; $i >= 0; $i--) {
            $grp = $groups[$i];
            if ($grp > 0) {
                $read = $readThreeDigits($grp, $i === $groupCount - 1);
                $unitName = $units[$i] ?? '';
                $out[] = trim($read . ' ' . $unitName);
            }
        }

        return implode(' ', $out);
    }

    /**
     * Tinh chỉnh ngắt nhịp và dấu câu đàm thoại
     * @param string $text
     * @return string
     */
    private function optimizePunctuationAndPauses($text) {
        // Chuẩn hóa khoảng trắng thừa
        $text = preg_replace('/\s+/', ' ', $text);

        // Thay dấu gạch nối giữa các mệnh đề độc lập bằng dấu chấm lửng nhẹ
        $text = preg_replace('/\s+[-–—]\s+/u', '... ', $text);

        // Đảm bảo sau dấu chấm, phẩy, chấm than, hỏi có khoảng cách
        $text = preg_replace('/([,\.\?!])([^\s\d])/u', '$1 $2', $text);

        // Tránh nhiều dấu chấm liên tiếp thừa
        $text = preg_replace('/\.{4,}/', '...', $text);

        return $text;
    }

    /**
     * Tổng hợp âm thanh TTS với Cơ chế Caching toàn diện
     * @param string $rawText Văn bản kịch bản thô từ Shot Plan
     * @param array $options [provider, model, voice, speed, format, bypass_cache, custom_filename]
     * @return array ['success' => bool, 'audio_path' => string, 'duration' => float, 'characters' => int, 'cost' => float, 'spoken_script' => string, 'cached' => bool, 'error' => string|null]
     */
    public function synthesize($rawText, array $options = array()) {
        $rawText = trim($rawText);
        if (empty($rawText)) {
            $rawText = 'FITNADO sản phẩm thể thao chính hãng.';
        }

        $audioDir = defined('UPLOAD_AUDIO') ? UPLOAD_AUDIO : 'upload/audio/';
        if (!is_dir($audioDir)) {
            @mkdir($audioDir, 0777, true);
        }

        // 1. Voice Preparation
        $spokenScript = $this->prepareSpokenScript($rawText, $options);

        // 2. Xác định các tham số tổng hợp
        $providerName = !empty($options['provider']) ? strtolower(trim($options['provider'])) : 'beeknoee';
        $model = !empty($options['model']) ? trim($options['model']) : 'openai/tts-1-hd';
        $voice = !empty($options['voice']) ? trim($options['voice']) : 'nova';
        $speed = !empty($options['speed']) ? (float)$options['speed'] : 1.0;
        $format = !empty($options['format']) ? strtolower(trim($options['format'])) : 'mp3';
        $bypassCache = !empty($options['bypass_cache']);

        // 3. Tạo Cache Key duy nhất
        $cacheHash = md5($providerName . '|' . $model . '|' . $voice . '|' . md5($spokenScript) . '|' . $speed . '|' . $format);
        $audioFileName = !empty($options['custom_filename']) ? $options['custom_filename'] : ('voice_' . $providerName . '_' . $voice . '_' . $cacheHash . '.' . $format);
        $audioPath = $audioDir . $audioFileName;

        // 4. Kiểm tra Cache
        if (!$bypassCache && file_exists($audioPath) && filesize($audioPath) > 500) {
            $duration = $this->getAudioDuration($audioPath);
            return array(
                'success' => true,
                'audio_path' => $audioPath,
                'duration' => $duration,
                'characters' => mb_strlen($spokenScript, 'UTF-8'),
                'cost' => 0.0, // Cache Hit = 0 VND
                'spoken_script' => $spokenScript,
                'cached' => true,
                'provider' => $providerName,
                'model' => $model,
                'voice' => $voice,
                'error' => null
            );
        }

        // 5. Khởi tạo Voice Provider và gọi API tổng hợp
        $provider = VoiceProviderFactory::create($providerName, $this->d, $this->func);
        $synthResult = $provider->synthesize($spokenScript, array(
            'model' => $model,
            'voice' => $voice,
            'speed' => $speed,
            'format' => $format
        ));

        if (!$synthResult['success'] || empty($synthResult['audio_data'])) {
            // Fallback sang Google Translate nếu provider bị lỗi
            if ($providerName !== 'google_translate') {
                $fallbackProvider = new GoogleTranslateVoiceProvider();
                $fallbackResult = $fallbackProvider->synthesize($spokenScript, array('lang' => 'vi'));
                if ($fallbackResult['success'] && !empty($fallbackResult['audio_data'])) {
                    file_put_contents($audioPath, $fallbackResult['audio_data']);
                    $duration = $this->getAudioDuration($audioPath);
                    return array(
                        'success' => true,
                        'audio_path' => $audioPath,
                        'duration' => $duration,
                        'characters' => mb_strlen($spokenScript, 'UTF-8'),
                        'cost' => 0.0,
                        'spoken_script' => $spokenScript,
                        'cached' => false,
                        'fallback' => true,
                        'provider' => 'google_translate_fallback',
                        'error' => 'Primary provider failed (' . ($synthResult['error'] ?? '') . '). Fallback to Google Translate.'
                    );
                }
            }

            return array(
                'success' => false,
                'audio_path' => null,
                'duration' => 0.0,
                'characters' => mb_strlen($spokenScript, 'UTF-8'),
                'cost' => 0.0,
                'spoken_script' => $spokenScript,
                'cached' => false,
                'error' => $synthResult['error'] ?? 'TTS synthesis failed'
            );
        }

        // 6. Ghi file âm thanh thành phẩm
        file_put_contents($audioPath, $synthResult['audio_data']);

        // 7. Đo đạc thời lượng chính xác bằng ffprobe
        $duration = $this->getAudioDuration($audioPath);

        return array(
            'success' => true,
            'audio_path' => $audioPath,
            'duration' => $duration,
            'characters' => $synthResult['characters'] ?? mb_strlen($spokenScript, 'UTF-8'),
            'cost' => $synthResult['cost'] ?? 0.0,
            'spoken_script' => $spokenScript,
            'cached' => false,
            'provider' => $providerName,
            'model' => $model,
            'voice' => $voice,
            'error' => null
        );
    }

    /**
     * Đo đạc thời lượng file âm thanh chính xác bằng ffprobe (hoặc fallback tính toán)
     * @param string $audioFile
     * @return float
     */
    public function getAudioDuration($audioFile) {
        if (!file_exists($audioFile) || filesize($audioFile) < 100) {
            return 3.0;
        }

        if ($this->resolvedFfprobe) {
            $cmd = sprintf(
                '"%s" -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "%s" 2>&1',
                $this->resolvedFfprobe,
                $audioFile
            );
            $out = @exec($cmd);
            if ($out && is_numeric(trim($out))) {
                return round((float)trim($out), 2);
            }
        }

        // Fallback ước lượng theo bitrate 64-128kbps nếu không có ffprobe
        $fileSize = filesize($audioFile);
        $estDuration = round($fileSize / 16000, 2);
        return max(1.5, min(60.0, $estDuration));
    }
}
