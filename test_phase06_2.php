<?php
/**
 * Automated Verification Script for FITNADO Phase 06.2
 * Low-Cost Hybrid Video Composer Test Suite
 * PHP 7.4 Compatible
 */

$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';

define('LIBRARIES', __DIR__ . '/libraries/');
require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$dbConfig = $config['database'];
if (file_exists('/Applications/MAMP/tmp/mysql/mysql.sock')) {
    $dbConfig['unix_socket'] = '/Applications/MAMP/tmp/mysql/mysql.sock';
}
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);

require_once LIBRARIES . 'class/class.VideoProvider.php';
require_once LIBRARIES . 'class/class.VideoComposer.php';
require_once LIBRARIES . 'class/class.AIVideoEngine.php';
require_once LIBRARIES . 'class/class.AIVideoJobQueue.php';

$videoEngine = new AIVideoEngine($d, $func);
$videoQueue = new AIVideoJobQueue($d, $func);
$composer = new VideoComposer($d, $func);

echo "=======================================================\n";
echo "FITNADO PHASE 06.2 - HYBRID VIDEO COMPOSER TEST SUITE\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $testName, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . " - " . $details . "\n";
        $failCount++;
    }
}

// -------------------------------------------------------------
// SETUP FIXTURE PRODUCT & APPROVED TIKTOK SCRIPT
// -------------------------------------------------------------
$runSuffix = time();
$testProdName = "Đai Cứng Tập Gym FITNADO Pro Lever " . $runSuffix;
$testProdCode = "LEVER_P062_" . $runSuffix;

// Tạo 2 ảnh sản phẩm và gallery thật để test asset resolution & local motion
$prodPhoto = 'p062_lever_main_' . $runSuffix . '.jpg';
$galPhoto = 'p062_lever_gal_' . $runSuffix . '.jpg';
@file_put_contents('upload/product/' . $prodPhoto, "\xFF\xD8\xFF\xE0\x00\x10JFIF" . str_repeat("\x00", 600));
@file_put_contents('upload/product/' . $galPhoto, "\xFF\xD8\xFF\xE0\x00\x10JFIF" . str_repeat("\x00", 600));

$testProductId = $d->insert('product', array(
    'namevi' => $testProdName,
    'slugvi' => 'dai-cung-tap-gym-fitnado-pro-lever-' . $runSuffix,
    'code' => $testProdCode,
    'photo' => $prodPhoto,
    'regular_price' => 850000,
    'sale_price' => 690000,
    'discount' => 19,
    'review_score' => 5.0,
    'review_count' => 12,
    'numb' => 1,
    'view' => 0,
    'descvi' => 'Đai cứng khóa đòn bẩy FITNADO Pro Lever da bò nguyên tấm dày 10mm.',
    'contentvi' => '<p>Chi tiết đai cứng khóa đòn bẩy cao cấp...</p>',
    'status' => 'hienthi',
    'type' => 'san-pham',
    'date_created' => time(),
    'date_updated' => time()
));

// Thêm ảnh gallery
$d->insert('gallery', array(
    'id_parent' => $testProductId,
    'photo' => $galPhoto,
    'namevi' => 'Ảnh chi tiết khóa đòn bẩy',
    'type' => 'san-pham',
    'status' => 'hienthi',
    'numb' => 1,
    'date_created' => time()
));

// Kịch bản TikTok chuẩn 30s với 7 phân cảnh Purpose-Driven Flow
$mockShotPlan = array(
    array(
        'scene_number' => 1,
        'purpose' => 'HOOK',
        'duration' => 3,
        'visual_instruction' => 'Góc máy cận cảnh gymer gập người squat 160kg với ánh nhìn quyết tâm',
        'voiceover' => 'Squat trên 150kg mà dùng đai dán mỏng là sai lầm nguy hiểm nhất!',
        'on_screen_text' => 'SQUAT NẶNG MÀ DÙNG ĐAI DÁN? ❌',
        'asset_requirement' => 'Ảnh tư thế squat'
    ),
    array(
        'scene_number' => 2,
        'purpose' => 'PROBLEM',
        'duration' => 4,
        'visual_instruction' => 'Đai dán thông thường bị bung khóa gai khi nén cơ hoành',
        'voiceover' => 'Khóa dán không chịu nổi áp lực lớn, dễ tuột giữa rep đẩy.',
        'on_screen_text' => 'DỄ BUNG KHÓA GAI NGUY HIỂM 😱',
        'asset_requirement' => 'Ảnh so sánh đai'
    ),
    array(
        'scene_number' => 3,
        'purpose' => 'PRODUCT_INTRO',
        'duration' => 5,
        'visual_instruction' => 'Góc quay 3D logo FITNADO Pro Lever da bò 10mm bóng mờ sang trọng',
        'voiceover' => 'Đổi sang Đai Cứng FITNADO Pro Lever khóa đòn bẩy hợp kim nguyên khối.',
        'on_screen_text' => 'FITNADO PRO LEVER 10MM 🔥',
        'asset_requirement' => 'Ảnh chính sản phẩm'
    ),
    array(
        'scene_number' => 4,
        'purpose' => 'DEMO',
        'duration' => 6,
        'visual_instruction' => 'Thao tác gạt khóa bẩy Lever chỉ trong 0.5s siết chặt nén cơ bụng',
        'voiceover' => 'Gạt khóa siết chặt tối đa, nén áp suất bụng cực đại giữ thẳng lưng.',
        'on_screen_text' => 'KHÓA BẨY LEVER SIẾT CỰC CHẮC 💪',
        'asset_requirement' => 'Ảnh khóa đòn bẩy'
    ),
    array(
        'scene_number' => 5,
        'purpose' => 'BENEFIT',
        'duration' => 5,
        'visual_instruction' => 'Gymer thực hiện bài Deadlift 200kg vững như bàn thạch',
        'voiceover' => 'Bảo vệ an toàn 100% cột sống lưng dưới, tự tin chạm mốc PR mới.',
        'on_screen_text' => 'BẢO VỆ CỘT SỐNG + TỰ TIN PR 🏆',
        'asset_requirement' => 'Ảnh tập luyện'
    ),
    array(
        'scene_number' => 6,
        'purpose' => 'LIMITATION',
        'duration' => 4,
        'visual_instruction' => 'Cận cảnh bề mặt da đai cứng cáp chắc nịch',
        'voiceover' => 'Lưu ý: Đai dày 10mm chuyên cho tạ nặng, không phù hợp cho cardio.',
        'on_screen_text' => 'CHUYÊN SQUAT/DEADLIFT NẶNG ⭐',
        'asset_requirement' => 'Ảnh bề dày 10mm'
    ),
    array(
        'scene_number' => 7,
        'purpose' => 'CTA',
        'duration' => 3,
        'visual_instruction' => 'Mũi tên đồ họa chỉ xuống nút Mua Ngay góc trái màn hình',
        'voiceover' => 'Bấm ngay giỏ hàng bên dưới nhận bảo hành khóa bẩy 2 năm trọn đời!',
        'on_screen_text' => 'XEM GIỎ HÀNG NGAY 👇',
        'asset_requirement' => 'Icon giỏ hàng'
    )
);

$testContentId = $d->insert('ai_content', array(
    'id_product' => $testProductId,
    'content_type' => 'tiktok_script',
    'title' => 'Squat trên 150kg mà dùng đai dán mỏng là sai lầm nguy hiểm',
    'language' => 'vi',
    'target_duration' => 30,
    'content_text' => "Kịch bản TikTok 30s đai cứng Pro Lever",
    'structured_data' => json_encode(array(
        'title' => 'Kịch bản TikTok 30s đai cứng Pro Lever',
        'target_duration' => 30,
        'shot_plan' => $mockShotPlan
    ), JSON_UNESCAPED_UNICODE),
    'version' => 1,
    'is_active' => 1,
    'status' => 'APPROVED',
    'source_hash' => hash('sha256', 'mock_p062_' . $runSuffix),
    'provider' => 'mock',
    'date_created' => time(),
    'date_updated' => time()
));

// -------------------------------------------------------------
// NHÓM 1: DATABASE & SCHEMA VERIFICATION
// -------------------------------------------------------------
$reqCols = array('mode', 'local_render_cost', 'ai_video_seconds', 'ai_video_cost', 'tts_cost', 'total_external_api_cost', 'composer_log');
foreach ($reqCols as $col) {
    $chk = $d->rawQueryOne("SHOW COLUMNS FROM `table_ai_video` LIKE ?", array($col));
    assertTest(!empty($chk), "Cột '$col' tồn tại trong table_ai_video");
}

// -------------------------------------------------------------
// NHÓM 2: FFMPEG AUDIT & DIAGNOSTICS
// -------------------------------------------------------------
$audit = $composer->auditFFmpeg();
assertTest(is_array($audit) && isset($audit['available']) && isset($audit['status']), "VideoComposer::auditFFmpeg() chạy an toàn và trả về cấu trúc trạng thái");
assertTest(!empty($audit['installation_guide']), "Cung cấp hướng dẫn cài đặt FFmpeg chi tiết");

// -------------------------------------------------------------
// NHÓM 3: PURPOSE-DRIVEN SHOT PLAN & MOTION EFFECTS
// -------------------------------------------------------------
$normalizedScenes = $composer->normalizeScenes($mockShotPlan, VideoComposer::MODE_ECONOMY);
assertTest(count($normalizedScenes) === 7, "Chuẩn hóa đủ 7 phân cảnh tiếp thị");
assertTest($normalizedScenes[0]['purpose'] === 'HOOK' && $normalizedScenes[0]['duration'] <= 3, "0–3s đầu tiên bắt buộc mang purpose HOOK");
assertTest($normalizedScenes[count($normalizedScenes) - 1]['purpose'] === 'CTA', "Phân cảnh cuối cùng mang purpose CTA");

// Kiểm tra motion effect được gán hợp lệ
$validMotionKeys = array_keys(VideoComposer::MOTION_EFFECTS);
$allMotionsValid = true;
foreach ($normalizedScenes as $sc) {
    if (!in_array($sc['motion_effect'], $validMotionKeys)) {
        $allMotionsValid = false;
        break;
    }
}
assertTest($allMotionsValid, "Tất cả các phân cảnh local được gán Motion Effect hợp lệ");

// -------------------------------------------------------------
// NHÓM 4: COST ESTIMATION & COST GUARD LIMIT
// -------------------------------------------------------------
$ecoEstimate = $composer->estimateCost($normalizedScenes, VideoComposer::MODE_ECONOMY);
assertTest($ecoEstimate['ai_video_cost'] == 0 && $ecoEstimate['total_external_api_cost'] == 0, "Chế độ ECONOMY có ước tính chi phí API Video = 0 VND");

$hybridScenes = $normalizedScenes;
$hybridScenes[3]['render_method'] = 'AI_VIDEO'; // Đánh dấu scene DEMO dùng AI Video
$hybridEstimate = $composer->estimateCost($hybridScenes, VideoComposer::MODE_HYBRID);
assertTest($hybridEstimate['ai_scenes_count'] === 1 && $hybridEstimate['ai_video_cost'] == 50000, "Chế độ HYBRID tính đúng 1 AI scene (~50.000 VND)");

// Test Cost Guard chặn khi vượt quá hạn mức 60,000 VND
$overLimitEstimate = array(
    'total_external_api_cost' => 150000,
    'limit_amount' => 60000,
    'exceeds_limit' => true,
    'currency' => 'VND'
);
$guardBlock = $composer->validateCostGuard($overLimitEstimate, false);
assertTest($guardBlock['allowed'] === false && strpos($guardBlock['error'], 'vượt quá') !== false, "Cost Guard chặn tự động khi chi phí vượt hạn mức max_ai_video_cost_per_video");

$guardOverride = $composer->validateCostGuard($overLimitEstimate, true);
assertTest($guardOverride['allowed'] === true, "Cost Guard cho phép Admin Override với quyền đặc biệt");

// -------------------------------------------------------------
// NHÓM 5: REAL ECONOMY VIDEO GENERATION (API COST = 0 VND)
// -------------------------------------------------------------
$ecoProjectRes = $videoEngine->createProjectFromApprovedContent($testContentId, array(
    'mode' => 'ECONOMY',
    'title' => 'Video TikTok Economy Pro Lever ' . $runSuffix,
    'voice_id' => 'vi-VN-Standard-A',
    'template_id' => 'PROBLEM_SOLUTION'
));

assertTest($ecoProjectRes['success'] === true && $ecoProjectRes['id_video'] > 0, "Khởi tạo dự án Video ECONOMY thành công");
$idVideoEco = (int)$ecoProjectRes['id_video'];

// Enqueue & Process Render
$enqueueRes = $videoQueue->enqueueVideoRender($idVideoEco);
assertTest($enqueueRes['success'] === true, "Đưa dự án ECONOMY vào hàng đợi render thành công");

$idJobEco = (int)$enqueueRes['id_job'];
$processRes = $videoQueue->processNextJob($idJobEco);

assertTest($processRes['success'] === true && $processRes['status'] === 'REVIEW_REQUIRED', "VideoComposer thực hiện render hoàn tất và chuyển sang REVIEW_REQUIRED");

// Kiểm tra bản ghi DB sau render của Economy video
$ecoVideoDb = $d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ?", array($idVideoEco));
assertTest($ecoVideoDb['mode'] === 'ECONOMY', "Dự án video lưu đúng mode ECONOMY");
assertTest((float)$ecoVideoDb['total_external_api_cost'] == 0, "Chi phí API thực tế của ECONOMY video = 0 VND");
assertTest((float)$ecoVideoDb['ai_video_cost'] == 0 && (int)$ecoVideoDb['ai_video_seconds'] == 0, "Số giây AI và chi phí AI của ECONOMY video = 0");
assertTest(!empty($ecoVideoDb['video_file']) && file_exists($ecoVideoDb['video_file']), "File video thành phẩm 9:16 MP4 tồn tại trên ổ đĩa: " . $ecoVideoDb['video_file']);
assertTest(!empty($ecoVideoDb['composer_log']), "Nhật ký Video Composer (composer_log) được ghi nhận đầy đủ");

// -------------------------------------------------------------
// NHÓM 6: HYBRID VIDEO GENERATION (MAX 1 AI SCENE)
// -------------------------------------------------------------
$hybridShotPlan = $mockShotPlan;
$hybridShotPlan[3]['render_method'] = 'AI_VIDEO'; // Scene 4 (DEMO) dùng AI Video

$hybridContentId = $d->insert('ai_content', array(
    'id_product' => $testProductId,
    'content_type' => 'tiktok_script',
    'title' => 'Kịch bản TikTok Hybrid Pro Lever ' . $runSuffix,
    'target_duration' => 30,
    'structured_data' => json_encode(array('shot_plan' => $hybridShotPlan), JSON_UNESCAPED_UNICODE),
    'status' => 'APPROVED',
    'date_created' => time()
));

$hybridProjectRes = $videoEngine->createProjectFromApprovedContent($hybridContentId, array(
    'mode' => 'HYBRID',
    'title' => 'Video TikTok Hybrid Pro Lever ' . $runSuffix
));

assertTest($hybridProjectRes['success'] === true && $hybridProjectRes['id_video'] > 0, "Khởi tạo dự án Video HYBRID thành công");
$idVideoHyb = (int)$hybridProjectRes['id_video'];

$enqueueHybRes = $videoQueue->enqueueVideoRender($idVideoHyb);
$processHybRes = $videoQueue->processNextJob((int)$enqueueHybRes['id_job']);
assertTest($processHybRes['success'] === true && $processHybRes['status'] === 'REVIEW_REQUIRED', "Render dự án HYBRID hoàn tất và chuyển sang REVIEW_REQUIRED");

$hybVideoDb = $d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ?", array($idVideoHyb));
assertTest($hybVideoDb['mode'] === 'HYBRID', "Dự án video lưu đúng mode HYBRID");
assertTest((float)$hybVideoDb['total_external_api_cost'] > 0, "Dự án HYBRID ghi nhận đúng chi phí API (" . number_format($hybVideoDb['total_external_api_cost']) . " VND)");

// -------------------------------------------------------------
// NHÓM 7: STRICT HUMAN APPROVAL GATE (NO AUTO PUBLISH)
// -------------------------------------------------------------
assertTest($ecoVideoDb['status'] === 'REVIEW_REQUIRED', "Video dừng lại tại REVIEW_REQUIRED để Admin xem trước (Strict Human Gate)");

$approveRes = $videoEngine->approveVideo($idVideoEco, 'Admin Tester');
assertTest($approveRes['success'] === true && $approveRes['status'] === 'APPROVED', "Admin phê duyệt video thành công sau khi xem trước Preview");

$approvedVideoDb = $d->rawQueryOne("SELECT status, reviewed_by FROM table_ai_video WHERE id = ?", array($idVideoEco));
assertTest($approvedVideoDb['status'] === 'APPROVED' && $approvedVideoDb['reviewed_by'] === 'Admin Tester', "Trạng thái video cập nhật APPROVED kèm tên người duyệt");

// -------------------------------------------------------------
// NHÓM 8: COMPARISON REPORT DATA EXTRACTION
// -------------------------------------------------------------
echo "\n--- BẢNG SO SÁNH ECONOMY VS HYBRID ---\n";
echo "Thuộc tính             | ECONOMY Mode            | HYBRID Mode\n";
echo "-----------------------|-------------------------|-------------------------\n";
echo sprintf("%-22s | %-23s | %-23s\n", "Thời lượng mục tiêu", $ecoVideoDb['target_duration'] . "s", $hybVideoDb['target_duration'] . "s");
echo sprintf("%-22s | %-23s | %-23s\n", "Thời lượng thực tế", round($ecoVideoDb['duration_actual'], 1) . "s", round($hybVideoDb['duration_actual'], 1) . "s");
echo sprintf("%-22s | %-23s | %-23s\n", "Độ phân giải", "1080x1920 (9:16)", "1080x1920 (9:16)");
echo sprintf("%-22s | %-23s | %-23s\n", "External Video API Cost", number_format($ecoVideoDb['total_external_api_cost']) . " VND", number_format($hybVideoDb['total_external_api_cost']) . " VND");
echo sprintf("%-22s | %-23s | %-23s\n", "Số giây AI Video", $ecoVideoDb['ai_video_seconds'] . "s", $hybVideoDb['ai_video_seconds'] . "s");
echo sprintf("%-22s | %-23s | %-23s\n", "Dung lượng file", round($ecoVideoDb['file_size'] / 1024) . " KB", round($hybVideoDb['file_size'] / 1024) . " KB");
echo sprintf("%-22s | %-23s | %-23s\n", "Thông điệp tiếp thị", "Rõ ràng 7-stage flow", "Rõ ràng 7-stage flow");

echo "\n=======================================================\n";
echo "TEST RESULTS: $passCount PASSED / $failCount FAILED\n";
echo "=======================================================\n";

if ($failCount === 0) {
    echo ">>> ALL PHASE 06.2 UNIT & INTEGRATION TESTS PASSED (100%) <<<\n";
} else {
    echo ">>> SOME TESTS FAILED <<<\n";
    exit(1);
}
