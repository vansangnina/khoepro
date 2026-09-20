<?php
/**
 * FITNADO Voice Provider Abstraction Layer
 * Phase 06.4: Natural Vietnamese Voice Benchmark
 * PHP 7.4 / 8.2 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

/**
 * Interface VoiceProviderInterface
 */
interface VoiceProviderInterface {
    /**
     * Tổng hợp giọng nói từ văn bản (Spoken Script)
     * @param string $text Văn bản đã qua Voice Preparation
     * @param array $options [voice, speed, format, model, pitch, style]
     * @return array ['success' => bool, 'audio_data' => string|null, 'format' => string, 'characters' => int, 'cost' => float, 'error' => string|null]
     */
    public function synthesize($text, array $options = array());

    /**
     * Danh sách các giọng đọc hỗ trợ
     * @return array
     */
    public function getVoices();

    /**
     * Khai báo năng lực của provider
     * @return array
     */
    public function getCapabilities();
}

/**
 * Class GoogleTranslateVoiceProvider
 * Baseline TTS (Free / TW-OB endpoint)
 * Hỗ trợ tự động cắt đoạn câu khi text > 180 ký tự để vượt giới hạn 200 ký tự của Google URL
 */
class GoogleTranslateVoiceProvider implements VoiceProviderInterface {
    public function synthesize($text, array $options = array()) {
        $text = trim($text);
        if (empty($text)) {
            return array(
                'success' => false,
                'audio_data' => null,
                'format' => 'mp3',
                'characters' => 0,
                'cost' => 0.0,
                'error' => 'Văn bản cần đọc không được để trống'
            );
        }

        $lang = !empty($options['lang']) ? $options['lang'] : 'vi';

        // Chia nhỏ văn bản theo câu nếu vượt quá 150 ký tự
        $chunks = $this->splitTextIntoChunks($text, 150);
        $combinedAudio = '';

        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);
            if (empty($chunk)) continue;

            $url = 'https://translate.google.com/translate_tts?ie=UTF-8&tl=' . $lang . '&client=tw-ob&q=' . urlencode($chunk);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $audioData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 && strlen($audioData) > 100) {
                $combinedAudio .= $audioData;
            } else {
                return array(
                    'success' => false,
                    'audio_data' => null,
                    'format' => 'mp3',
                    'characters' => mb_strlen($text, 'UTF-8'),
                    'cost' => 0.0,
                    'error' => 'Google Translate TTS error (HTTP ' . $httpCode . '): ' . ($curlErr ?: 'Chunk fetch failed')
                );
            }
        }

        if (strlen($combinedAudio) > 200) {
            return array(
                'success' => true,
                'audio_data' => $combinedAudio,
                'format' => 'mp3',
                'characters' => mb_strlen($text, 'UTF-8'),
                'cost' => 0.0,
                'error' => null
            );
        }

        return array(
            'success' => false,
            'audio_data' => null,
            'format' => 'mp3',
            'characters' => mb_strlen($text, 'UTF-8'),
            'cost' => 0.0,
            'error' => 'No audio data received from Google Translate'
        );
    }

    private function splitTextIntoChunks($text, $maxLength = 150) {
        if (mb_strlen($text, 'UTF-8') <= $maxLength) {
            return array($text);
        }

        // Tách theo dấu câu . ! ? ... ,
        $sentences = preg_split('/(?<=[,\.\?!])\s+/u', $text);
        $chunks = array();
        $current = '';

        foreach ($sentences as $s) {
            if (mb_strlen($current . ' ' . $s, 'UTF-8') <= $maxLength) {
                $current .= ($current === '' ? '' : ' ') . $s;
            } else {
                if (!empty($current)) {
                    $chunks[] = $current;
                }
                $current = $s;
            }
        }

        if (!empty($current)) {
            $chunks[] = $current;
        }

        return $chunks;
    }

    public function getVoices() {
        return array(
            'vi-VN-Standard' => array(
                'id' => 'vi-VN-Standard',
                'name' => 'Google Tiếng Việt (Baseline)',
                'gender' => 'FEMALE',
                'accent' => 'NORTH',
                'style' => 'Newsreader / Monotone',
                'recommended_speed' => 1.0
            )
        );
    }

    public function getCapabilities() {
        return array(
            'name' => 'Google Translate TTS (Baseline)',
            'provider' => 'google_translate',
            'supports_speed' => false,
            'supports_pitch' => false,
            'supports_ssml' => false,
            'supports_emotion' => false,
            'formats' => array('mp3'),
            'cost_per_1k_chars' => 0.0,
            'currency' => 'VND',
            'is_configured' => true
        );
    }
}

/**
 * Class BeeknoeeVoiceProvider
 * Tích hợp Beeknoee TTS Audio Speech API (OpenAI & Gemini TTS models)
 */
class BeeknoeeVoiceProvider implements VoiceProviderInterface {
    private $apiKey;
    private $baseUrl;
    private $defaultModel;
    private $timeout;

    public function __construct($beeknoeeConfig = array()) {
        if (empty($beeknoeeConfig)) {
            global $config;
            if (isset($config['beeknoee'])) {
                $beeknoeeConfig = $config['beeknoee'];
            }
        }

        $this->apiKey = !empty($beeknoeeConfig['api_key']) ? trim($beeknoeeConfig['api_key']) : '';
        $this->baseUrl = !empty($beeknoeeConfig['base_url']) ? rtrim($beeknoeeConfig['base_url'], '/') : 'https://platform.beeknoee.com';
        $this->defaultModel = !empty($beeknoeeConfig['tts_model']) ? trim($beeknoeeConfig['tts_model']) : 'openai/tts-1-hd';
        $this->timeout = !empty($beeknoeeConfig['tts_timeout']) ? (int)$beeknoeeConfig['tts_timeout'] : 30;
    }

    public function synthesize($text, array $options = array()) {
        $text = trim($text);
        if (empty($text)) {
            return array(
                'success' => false,
                'audio_data' => null,
                'format' => 'mp3',
                'characters' => 0,
                'cost' => 0.0,
                'error' => 'Văn bản cần đọc không được để trống'
            );
        }

        if (empty($this->apiKey)) {
            return array(
                'success' => false,
                'audio_data' => null,
                'format' => 'mp3',
                'characters' => 0,
                'cost' => 0.0,
                'error' => 'Beeknoee API Key chưa được cấu hình'
            );
        }

        $model = !empty($options['model']) ? trim($options['model']) : $this->defaultModel;
        $voice = !empty($options['voice']) ? trim($options['voice']) : 'nova';
        $speed = !empty($options['speed']) ? (float)$options['speed'] : 1.0;
        $format = !empty($options['format']) ? strtolower(trim($options['format'])) : 'mp3';

        $payload = array(
            'model' => $model,
            'input' => $text,
            'voice' => $voice,
            'speed' => $speed,
            'response_format' => $format
        );

        $endpoint = $this->baseUrl . '/v1/audio/speech';
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        $charCount = mb_strlen($text, 'UTF-8');
        // Chi phí ước tính theo model:
        $costPerCharVnd = 0.75;
        if (strpos($model, 'gemini') !== false) {
            $costPerCharVnd = 0.15;
        } elseif ($model === 'openai/tts-1') {
            $costPerCharVnd = 0.375;
        }
        $estCostVnd = round(($charCount / 1000) * $costPerCharVnd * 1000, 2);

        if ($httpCode === 200 && strlen($response) > 500) {
            return array(
                'success' => true,
                'audio_data' => $response,
                'format' => $format,
                'characters' => $charCount,
                'cost' => $estCostVnd,
                'model' => $model,
                'voice' => $voice,
                'error' => null
            );
        }

        $errMsg = 'Beeknoee TTS API error (HTTP ' . $httpCode . '): ' . ($curlErr ?: $response);
        $jsonErr = json_decode($response, true);
        if (!empty($jsonErr['error']['message'])) {
            $errMsg = 'Beeknoee Error: ' . $jsonErr['error']['message'];
        }

        return array(
            'success' => false,
            'audio_data' => null,
            'format' => $format,
            'characters' => $charCount,
            'cost' => 0.0,
            'error' => $errMsg
        );
    }

    public function getVoices() {
        return array(
            'nova' => array(
                'id' => 'nova',
                'name' => 'Nữ Nova (Năng động / Reviewer TikTok / Tự nhiên)',
                'gender' => 'FEMALE',
                'accent' => 'Conversational',
                'style' => 'Fitness Creator, High Energy, Natural Intonation',
                'recommended_speed' => 1.0,
                'models' => array('openai/tts-1-hd', 'openai/tts-1', 'openai/gpt-4o-mini-tts')
            ),
            'shimmer' => array(
                'id' => 'shimmer',
                'name' => 'Nữ Shimmer (Ấm áp / Cuốn hút / Kể chuyện)',
                'gender' => 'FEMALE',
                'accent' => 'Warm',
                'style' => 'Storytelling, Soft, Persuasive',
                'recommended_speed' => 1.0,
                'models' => array('openai/tts-1-hd', 'openai/tts-1')
            ),
            'coral' => array(
                'id' => 'coral',
                'name' => 'Nữ Coral (Trẻ trung / Tươi tắn / Gần gũi)',
                'gender' => 'FEMALE',
                'accent' => 'Youthful',
                'style' => 'Casual, Friendly, Modern',
                'recommended_speed' => 1.0,
                'models' => array('openai/tts-1-hd', 'openai/tts-1')
            ),
            'sage' => array(
                'id' => 'sage',
                'name' => 'Nữ Sage (Điềm tĩnh / Chuyên gia / Đáng tin cậy)',
                'gender' => 'FEMALE',
                'accent' => 'Authoritative',
                'style' => 'Expert, Clinical, Confident',
                'recommended_speed' => 0.95,
                'models' => array('openai/tts-1-hd', 'openai/tts-1')
            ),
            'onyx' => array(
                'id' => 'onyx',
                'name' => 'Nam Onyx (Trầm ấm / Uy lực Gym / Thể thao)',
                'gender' => 'MALE',
                'accent' => 'Athletic Deep',
                'style' => 'Gym Coach, Strong, Confident Reviewer',
                'recommended_speed' => 1.0,
                'models' => array('openai/tts-1-hd', 'openai/tts-1')
            ),
            'echo' => array(
                'id' => 'echo',
                'name' => 'Nam Echo (Cân bằng / Tự nhiên / Truyền cảm)',
                'gender' => 'MALE',
                'accent' => 'Smooth',
                'style' => 'Balanced, Commercial Voiceover',
                'recommended_speed' => 1.0,
                'models' => array('openai/tts-1-hd', 'openai/tts-1')
            ),
            'ash' => array(
                'id' => 'ash',
                'name' => 'Nam Ash (Đời thường / Thân thiện / Gần gũi)',
                'gender' => 'MALE',
                'accent' => 'Casual Male',
                'style' => 'Conversational, Authentic',
                'recommended_speed' => 1.0,
                'models' => array('openai/tts-1-hd', 'openai/tts-1')
            ),
            'alloy' => array(
                'id' => 'alloy',
                'name' => 'Trung tính Alloy (Tiêu chuẩn / Đa dụng)',
                'gender' => 'NEUTRAL',
                'accent' => 'Standard',
                'style' => 'Neutral Commercial',
                'recommended_speed' => 1.0,
                'models' => array('openai/tts-1-hd', 'openai/tts-1', 'google/gemini-2.5-flash-tts')
            )
        );
    }

    public function getCapabilities() {
        return array(
            'name' => 'Beeknoee AI Voice Engine (HD Speech)',
            'provider' => 'beeknoee',
            'supports_speed' => true,
            'speed_range' => array('min' => 0.25, 'max' => 4.0, 'step' => 0.05),
            'benchmark_speeds' => array(0.95, 1.00, 1.05),
            'supports_pitch' => false,
            'supports_ssml' => false,
            'supports_emotion' => true,
            'formats' => array('mp3', 'opus', 'aac', 'flac', 'wav'),
            'models' => array(
                'openai/tts-1-hd' => 'OpenAI TTS-1 HD (Chất lượng cao nhất, tự nhiên nhất)',
                'openai/tts-1' => 'OpenAI TTS-1 Standard',
                'openai/gpt-4o-mini-tts' => 'GPT-4o Mini TTS',
                'google/gemini-2.5-flash-tts' => 'Google Gemini 2.5 Flash TTS (Tốc độ cao)',
                'google/gemini-3.1-flash-tts-preview' => 'Google Gemini 3.1 Flash TTS Preview',
                'google/gemini-2.5-pro-tts' => 'Google Gemini 2.5 Pro TTS (WAV)'
            ),
            'cost_per_1k_chars' => 750.0,
            'currency' => 'VND',
            'is_configured' => !empty($this->apiKey)
        );
    }
}

/**
 * Class MockVoiceProvider
 */
class MockVoiceProvider implements VoiceProviderInterface {
    public function synthesize($text, array $options = array()) {
        $text = trim($text);
        $charCount = mb_strlen($text, 'UTF-8');

        $mp3Header = "\xFF\xFB\x90\x64\x00\x00\x00\x00\x00\x00\x00\x00";
        $mockAudio = $mp3Header . str_repeat("\x00\xFF", 512);

        return array(
            'success' => true,
            'audio_data' => $mockAudio,
            'format' => 'mp3',
            'characters' => $charCount,
            'cost' => 0.0,
            'error' => null
        );
    }

    public function getVoices() {
        return array(
            'mock-voice-vi' => array(
                'id' => 'mock-voice-vi',
                'name' => 'Mock Voice Tiếng Việt',
                'gender' => 'FEMALE',
                'accent' => 'MOCK',
                'style' => 'Offline Simulation',
                'recommended_speed' => 1.0
            )
        );
    }

    public function getCapabilities() {
        return array(
            'name' => 'Mock Voice Provider',
            'provider' => 'mock',
            'supports_speed' => true,
            'supports_pitch' => false,
            'supports_ssml' => false,
            'supports_emotion' => false,
            'formats' => array('mp3'),
            'cost_per_1k_chars' => 0.0,
            'currency' => 'VND',
            'is_configured' => true
        );
    }
}

/**
 * Class VoiceProviderFactory
 */
class VoiceProviderFactory {
    public static function create($providerName = 'beeknoee', $d = null, $func = null) {
        $providerName = strtolower(trim($providerName));

        global $config;

        $settingOptions = array();
        if ($d) {
            $settingRow = $d->rawQueryOne("SELECT options FROM table_setting LIMIT 1");
            if (!empty($settingRow['options'])) {
                $settingOptions = json_decode($settingRow['options'], true);
            }
        }
        $aiVideoConfig = !empty($settingOptions['ai_video_config']) ? $settingOptions['ai_video_config'] : array();

        switch ($providerName) {
            case 'beeknoee':
            case 'openai':
                $beeknoeeConfig = !empty($config['beeknoee']) ? $config['beeknoee'] : array();
                if (!empty($aiVideoConfig['beeknoee_api_key'])) {
                    $beeknoeeConfig['api_key'] = $aiVideoConfig['beeknoee_api_key'];
                }
                return new BeeknoeeVoiceProvider($beeknoeeConfig);

            case 'google':
            case 'google_translate':
                return new GoogleTranslateVoiceProvider();

            case 'mock':
            default:
                return new MockVoiceProvider();
        }
    }
}
