<?php
if (!defined('LIBRARIES')) die("Error");
date_default_timezone_set('Asia/Ho_Chi_Minh');
define('NN_CONTRACT', 'MSHD');
define('NN_AUTHOR', 'xxxx.nina@gmail.com');
$config = array(
    'arrayDomainSSL' => array(),
    'database' => array(
        'server-name' => !empty($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : 'khoepro.com',
        'url' => '/',
        'type' => 'mysql',
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'masterpdo',
        'port' => 3306,
        'prefix' => 'table_',
        'charset' => 'utf8mb4',
        'unix_socket' => (file_exists('/Applications/MAMP/tmp/mysql/mysql.sock')) ? '/Applications/MAMP/tmp/mysql/mysql.sock' : ''
    ),
    'website' => array(
        'error-reporting' => false,
        'secret' => '$nina@',
        'salt' => '(EF}vhmn>q',
        'debug-developer' => true,
        'debug-css' => true,
        'debug-js' => true,
        'index' => false,
        'linkredirect' => false,
        'image' => array(),
        'noseo' => array('user','order','search'),//source
        'video' => array(
            'extension' => array('mp4', 'mkv'),
            'poster' => array(
                'width' => 700,
                'height' => 610,
                'extension' => '.jpg|.png|.jpeg'
            ),
            'allow-size' => '100Mb',
            'max-size' => 100 * 1024 * 1024
        ),
        'upload' => array(
            'max-width' => 1600,
            'max-height' => 1600
        ),
        'adminlang' => array(
            'active' => true,
            'key' => array('vi','en'),
            'lang' =>array(
                'vi' => 'Tiếng Việt',
                'en' => 'Tiếng Anh'
            )
        ),
        'lang' => array(
            'vi' => 'Tiếng Việt',
            'en' => 'Tiếng Anh'
        ),
        'lang-doc' => 'vi|en',
        'slug' => array(
            'vi' => 'Tiếng Việt',
            'en' => 'Tiếng Anh'
        ),
        'seo' => array(
            'vi' => 'Tiếng Việt',
            'en' => 'Tiếng Anh'
        ),
        'comlang' => array(
            "gioi-thieu" => array("vi" => "gioi-thieu", "en" => "about-us"),
            "san-pham" => array("vi" => "san-pham", "en" => "product"),
            "tin-tuc" => array("vi" => "tin-tuc", "en" => "news"),
            "tuyen-dung" => array("vi" => "tuyen-dung", "en" => "recruitment"),
            "thu-vien-anh" => array("vi" => "thu-vien-anh", "en" => "gallery"),
            "video" => array("vi" => "video", "en" => "video"),
            "lien-he" => array("vi" => "lien-he", "en" => "contact")
        )
    ),
    'order' => array(
        'ship' => true
    ),
    'login' => array(
        'admin' => 'LoginAdmin' . NN_CONTRACT,
        'member' => 'LoginMember' . NN_CONTRACT,
        'attempt' => 5,
        'delay' => 15
    ),
    'googleAPI' => array(
        'recaptcha' => array(
            'active' => false,
            'urlapi' => 'https://www.google.com/recaptcha/api/siteverify',
            'sitekey' => '6LezS5kUAAAAAF2A6ICaSvm7R5M-BUAcVOgJT_31',
            'secretkey' => '6LezS5kUAAAAAGCGtfV7C1DyiqlPFFuxvacuJfdq'
        )
    ),
    'oneSignal' => array(
        'active' => false,
        'id' => 'af12ae0e-cfb7-41d0-91d8-8997fca889f8',
        'restId' => 'MWFmZGVhMzYtY2U0Zi00MjA0LTg0ODEtZWFkZTZlNmM1MDg4'
    ),
    'beeknoee' => array(
        'active' => true,
        'api_key' => 'sk-bee-670e35421b47cdc2b81398d10c77c14caacf245ac0fee30f18eb26e67098ed7e',
        'base_url' => 'https://platform.beeknoee.com',
        'video_model' => 'veo-3.1-fast-generate-preview',
        'aspect_ratio' => '9:16',
        'duration' => 8,
        'timeout' => 120,
        'poll_interval' => 5,
        'max_poll_attempts' => 60
    ),
    'accesstrade' => array(
        'active' => true,
        'access_key' => 'AEsZZsLDUSPjBRBd7noBUClS5wDtnaKV',
        'base_url' => 'https://api.accesstrade.vn',
        'timeout' => 30,
        'sync_enabled' => true,
        'sync_interval_minutes' => 30,
        'rate_limit_per_minute' => 30,
        'sandbox' => false
    ),
    'video_composer' => array(
        'default_mode' => 'ECONOMY', // ECONOMY (0 VND default), HYBRID (max 1-2 AI scenes), PREMIUM
        'max_ai_video_cost_per_video' => 60000, // VND limit per video
        'hybrid_max_ai_scenes' => 2,
        'hybrid_max_ai_seconds' => 8,
        'ai_scene_cost_estimate' => 50000, // Estimated VND per 8s AI clip
        'ffmpeg_path' => getenv('FFMPEG_PATH')
            ?: (PHP_OS_FAMILY === 'Windows'
            ? 'C:/Users/VanSang/AppData/Local/Microsoft/WinGet/Packages/Gyan.FFmpeg.Essentials_Microsoft.Winget.Source_8wekyb3d8bbwe/ffmpeg-9.0.1-essentials_build/bin/ffmpeg.exe'
            : '/usr/bin/ffmpeg'),
        'ffprobe_path' => getenv('FFPROBE_PATH')
            ?: (PHP_OS_FAMILY === 'Windows'
            ? 'C:/Users/VanSang/AppData/Local/Microsoft/WinGet/Packages/Gyan.FFmpeg.Essentials_Microsoft.Winget.Source_8wekyb3d8bbwe/ffmpeg-9.0.1-essentials_build/bin/ffprobe.exe'
            : '/usr/bin/ffprobe'),
        'ffmpeg_binary' => getenv('FFMPEG_PATH')
    ?: (PHP_OS_FAMILY === 'Windows' ? 'ffmpeg' : '/usr/bin/ffmpeg'),
        'ffprobe_binary' => getenv('FFPROBE_PATH')
    ?: (PHP_OS_FAMILY === 'Windows' ? 'ffprobe' : '/usr/bin/ffprobe'),
        'caption_font_size' => 36,
        'safe_area_bottom_pct' => 20,
        'enable_branding' => true,
        'brand_name' => 'Khỏe Pro',
        'default_voice_provider' => 'beeknoee',
        'default_voice' => 'onyx',
        'default_voice_speed' => 1.0,
        'default_voice_model' => 'openai/tts-1-hd'
    ),
);
error_reporting(($config['website']['error-reporting']) ? E_ALL : 0);
$http = 'http://';
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'khoepro.com') || (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'khoepro.com')) {
    $http = 'https://';
}

/* Cấu hình base */
$configUrl = $config['database']['server-name'] . $config['database']['url'];
$configBase = $http . $configUrl;

/* Token */
define('TOKEN', md5(NN_CONTRACT . $config['database']['url']));

/* Path */
define('ROOT', str_replace(basename(__DIR__), '', __DIR__));
define('ASSET', $http . $configUrl);
define('ADMIN', 'admin');

/* Cấu hình login */
$loginAdmin = $config['login']['admin'];
$loginMember = $config['login']['member'];

/* Cấu hình upload */
require_once LIBRARIES . "constant.php";
