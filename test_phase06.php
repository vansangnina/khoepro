<?php
/**
 * Automated Verification Script for FITNADO Phase 06
 * AI Video Production Engine Test Suite
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
require_once LIBRARIES . 'class/class.AIVideoEngine.php';
require_once LIBRARIES . 'class/class.AIVideoJobQueue.php';

$videoEngine = new AIVideoEngine($d, $func);
$videoQueue = new AIVideoJobQueue($d, $func);

echo "=======================================================\n";
echo "FITNADO PHASE 06 - AUTOMATED TEST SUITE (PHP 7.4)\n";
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
$testProdName = "Đai Lưng Mềm FITNADO Quick-Lock " . $runSuffix;
$testProdCode = "PROD_P06_" . $runSuffix;

// Tạo ảnh giả lập để test asset resolution
$mockPhotoName = 'test_p06_belt_' . $runSuffix . '.jpg';
@file_put_contents('upload/product/' . $mockPhotoName, "\xFF\xD8\xFF\xE0\x00\x10JFIF" . str_repeat("\x00", 500));

$testProductId = $d->insert('product', array(
    'namevi' => $testProdName,
    'slugvi' => 'dai-lung-mem-fitnado-quick-lock-' . $runSuffix,
    'code' => $testProdCode,
    'photo' => $mockPhotoName,
    'regular_price' => 450000,
    'sale_price' => 380000,
    'discount' => 15,
    'review_score' => 0.0,
    'review_count' => 0,
    'numb' => 1,
    'view' => 0,
    'descvi' => 'Đai lưng mềm FITNADO Quick-Lock hỗ trợ bảo vệ cột sống khi tập squat/deadlift.',
    'contentvi' => '<p>Chi tiết đai lưng mềm...</p>',
    'status' => 'hienthi',
    'type' => 'san-pham',
    'date_created' => time(),
    'date_updated' => time()
));

// Tạo kịch bản TikTok đã duyệt (APPROVED) từ Phase 05
$mockShotPlan = array(
    array(
        'scene_number' => 1,
        'duration' => 3,
        'visual_instruction' => 'Cận cảnh gymer gập người nhấc tạ nặng bị võng lưng',
        'voiceover' => 'Squat nặng mà sợ chấn thương cột sống lưng dưới?',
        'on_screen_text' => 'SQUAT NẶNG MÀ ĐAU LƯNG? 😱',
        'asset_requirement' => 'Video tư thế squat'
    ),
    array(
        'scene_number' => 2,
        'duration' => 5,
        'visual_instruction' => 'Thao tác gạt khóa Quick-Lock đai lưng FITNADO và siết chặt vòng eo',
        'voiceover' => 'Giải pháp với đai lưng mềm FITNADO khóa Quick-Lock siêu nhanh chỉ 1 giây.',
        'on_screen_text' => 'KHÓA QUICK-LOCK 1 GIÂY 🔥',
        'asset_requirement' => 'Ảnh đai lưng thực tế'
    ),
    array(
        'scene_number' => 3,
        'duration' => 12,
        'visual_instruction' => 'Người tập gồng bụng nén core và đẩy mức tạ 120kg nhẹ nhàng',
        'voiceover' => 'Tăng áp lực khoang bụng, giữ thẳng cột sống và tự tin bứt phá PR tạ mới.',
        'on_screen_text' => 'TĂNG ÁP LỰC CORE + BẢO VỆ CỘT SỐNG 💪',
        'asset_requirement' => 'Video nâng tạ'
    ),
    array(
        'scene_number' => 4,
        'duration' => 6,
        'visual_instruction' => 'Cận cảnh lớp đệm thoáng khí và đường chỉ may gia cố 4 lớp',
        'voiceover' => 'Đệm mút thoáng khí không gây cấn hông, thoải mái trong mọi bài tập.',
        'on_screen_text' => 'ĐỆM ÊM THOÁNG KHÍ ⭐',
        'asset_requirement' => 'Ảnh chi tiết chất liệu'
    ),
    array(
        'scene_number' => 5,
        'duration' => 4,
        'visual_instruction' => 'Mũi tên chỉ xuống góc trái màn hình nơi có icon giỏ hàng TikTok',
        'voiceover' => 'Bấm ngay vào link bên dưới để nhận ưu đãi chính hãng hôm nay!',
        'on_screen_text' => 'XEM GIỎ HÀNG NGAY 👇',
        'asset_requirement' => 'Mũi tên chỉ giỏ hàng'
    )
);

$testContentId = $d->insert('ai_content', array(
    'id_product' => $testProductId,
    'content_type' => 'tiktok_script',
    'title' => 'Kịch bản TikTok 30s Quick-Lock Belt',
    'language' => 'vi',
    'target_duration' => 30,
    'content_text' => "Kịch bản TikTok 30s về đai lưng mềm Quick-Lock",
    'structured_data' => json_encode(array(
        'title' => 'Kịch bản TikTok 30s Quick-Lock Belt',
        'target_duration' => 30,
        'shot_plan' => $mockShotPlan
    ), JSON_UNESCAPED_UNICODE),
    'version' => 1,
    'is_active' => 1,
    'status' => 'APPROVED',
    'source_hash' => hash('sha256', 'mock_content_' . $runSuffix),
    'provider' => 'mock',
    'date_created' => time(),
    'date_updated' => time()
));

// Tạo 1 kịch bản DRAFT để test Content Gate
$draftContentId = $d->insert('ai_content', array(
    'id_product' => $testProductId,
    'content_type' => 'tiktok_script',
    'title' => 'Kịch bản DRAFT Chưa Duyệt',
    'status' => 'REVIEW_REQUIRED',
    'structured_data' => json_encode(array('shot_plan' => $mockShotPlan)),
    'date_created' => time()
));

// -------------------------------------------------------------
// NHÓM 1: DATABASE & SCHEMA VERIFICATION
// -------------------------------------------------------------
$tables = array('table_ai_video', 'table_ai_video_job', 'table_ai_video_asset');
foreach ($tables as $tbl) {
    $chk = $d->rawQueryOne("SHOW TABLES LIKE ?", array($tbl));
    assertTest(!empty($chk), "Bảng $tbl tồn tại trong cơ sở dữ liệu");
}

// -------------------------------------------------------------
// NHÓM 2: VIDEO PROVIDER ABSTRACTION
// -------------------------------------------------------------
$mockProvider = VideoProviderFactory::create('mock', $d, $func);
assertTest($mockProvider instanceof VideoProviderInterface, "VideoProviderFactory khởi tạo MockVideoProvider hợp lệ");

$capabilities = $mockProvider->getCapabilities();
assertTest(!empty($capabilities['is_configured']) && in_array('9:16', $capabilities['aspect_ratios']), "MockVideoProvider hỗ trợ định dạng 9:16 TikTok");

$extProvider = VideoProviderFactory::create('creatify', $d, $func);
assertTest($extProvider instanceof VideoProviderInterface, "VideoProviderFactory hỗ trợ ExternalVideoProvider (Creatify)");

$beeknoeeProvider = VideoProviderFactory::create('beeknoee', $d, $func);
assertTest($beeknoeeProvider instanceof VideoProviderInterface, "VideoProviderFactory hỗ trợ BeeknoeeVideoProvider");

$bkCaps = $beeknoeeProvider->getCapabilities();
assertTest($bkCaps['provider'] === 'beeknoee' && $bkCaps['model'] === 'veo-3.1-fast-generate-preview' && $bkCaps['duration'] === 8, "BeeknoeeVideoProvider nạp đúng cấu hình veo-3.1-fast-generate-preview (8s, 9:16)");

// Test bảo vệ khi chưa cấu hình API key
$unconfiguredRes = $beeknoeeProvider->createRenderJob(array('scenes_data' => json_encode($mockShotPlan)));
assertTest($unconfiguredRes['success'] === false && strpos($unconfiguredRes['error'], 'Beeknoee') !== false, "BeeknoeeVideoProvider chặn an toàn khi chưa kích hoạt/chưa có API key");

// -------------------------------------------------------------
// NHÓM 3: CONTENT GATE & PROJECT CREATION
// -------------------------------------------------------------
// Chặn tạo video từ kịch bản chưa APPROVED
$gateRes = $videoEngine->createProjectFromApprovedContent($draftContentId);
assertTest($gateRes['success'] === false && strpos($gateRes['error'], 'APPROVED') !== false, "Content Gate chặn tạo video từ kịch bản chưa duyệt (REVIEW_REQUIRED)");

// Tạo thành công từ kịch bản APPROVED
$projectRes = $videoEngine->createProjectFromApprovedContent($testContentId, array(
    'title' => 'Video Dự án Đai Lưng Quick-Lock ' . $runSuffix,
    'video_type' => 'TIKTOK_9_16',
    'aspect_ratio' => '9:16',
    'target_duration' => 30,
    'voice_id' => 'vi-VN-Standard-A',
    'template_id' => 'PROBLEM_SOLUTION',
    'provider' => 'mock'
));

assertTest($projectRes['success'] === true && $projectRes['id_video'] > 0, "Khởi tạo Video Project thành công từ Approved Script");
$idVideo = (int)$projectRes['id_video'];

// -------------------------------------------------------------
// NHÓM 4: ASSET RESOLUTION PIPELINE
// -------------------------------------------------------------
$videoRow = $d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array($idVideo));
assertTest(!empty($videoRow) && $videoRow['status'] === 'READY', "Asset Pipeline tự động ánh xạ ảnh sản phẩm và chuyển trạng thái sang READY");

$assetsInDb = $d->rawQuery("SELECT * FROM table_ai_video_asset WHERE id_video = ?", array($idVideo));
assertTest(count($assetsInDb) === 5, "Ánh xạ đầy đủ tài nguyên cho 5 phân cảnh trong table_ai_video_asset");

// Test phát hiện thiếu Asset (WAITING_ASSET)
$noPhotoProdId = $d->insert('product', array(
    'namevi' => 'Sản phẩm không có ảnh ' . $runSuffix,
    'slugvi' => 'san-pham-khong-co-anh-' . $runSuffix,
    'code' => 'NOPHOTO_' . $runSuffix,
    'regular_price' => 100000,
    'sale_price' => 90000,
    'discount' => 10,
    'review_score' => 0.0,
    'review_count' => 0,
    'numb' => 1,
    'view' => 0,
    'status' => 'hienthi',
    'type' => 'san-pham',
    'date_created' => time()
));
$noPhotoContentId = $d->insert('ai_content', array(
    'id_product' => $noPhotoProdId,
    'content_type' => 'tiktok_script',
    'title' => 'Kịch bản cho SP không ảnh',
    'status' => 'APPROVED',
    'structured_data' => json_encode(array('shot_plan' => $mockShotPlan)),
    'date_created' => time()
));
$missingAssetRes = $videoEngine->createProjectFromApprovedContent($noPhotoContentId);
assertTest($missingAssetRes['status'] === 'WAITING_ASSET' && $missingAssetRes['missing_assets_count'] > 0, "Phát hiện thiếu tài nguyên trực quan và đặt trạng thái WAITING_ASSET");

// -------------------------------------------------------------
// NHÓM 5: SCRIPT HASH & OUTDATED DETECTION
// -------------------------------------------------------------
$isOutdatedBefore = $videoEngine->checkVideoOutdated($idVideo);
assertTest($isOutdatedBefore === false, "Video mới khởi tạo có mã băm khớp kịch bản (is_outdated = 0)");

// Sửa nội dung kịch bản trong table_ai_content
$d->rawQuery("UPDATE table_ai_content SET content_text = 'Nội dung kịch bản đã bị sửa đổi...', date_updated = ? WHERE id = ?", array(time(), $testContentId));
$isOutdatedAfter = $videoEngine->checkVideoOutdated($idVideo);
assertTest($isOutdatedAfter === true, "Phát hiện kịch bản gốc bị sửa đổi và tự động gắn cờ is_outdated = 1");

// -------------------------------------------------------------
// NHÓM 6: VIDEO VERSIONING (KHÔNG GHI ĐÈ VIDEO CŨ)
// -------------------------------------------------------------
$projectV2Res = $videoEngine->createProjectFromApprovedContent($testContentId, array('title' => 'Video Dự án v2'));
assertTest($projectV2Res['version'] === 2, "Tự động tăng phiên bản Version v2 cho dự án mới");

$v1Check = $d->rawQueryOne("SELECT id, is_active FROM table_ai_video WHERE id = ?", array($idVideo));
assertTest(!empty($v1Check) && (int)$v1Check['is_active'] === 0, "Bản ghi v1 vẫn tồn tại nguyên vẹn trong DB và chuyển is_active = 0");

// -------------------------------------------------------------
// NHÓM 7: BACKGROUND JOB QUEUE & WORKER EXECUTION
// -------------------------------------------------------------
// Thử enqueue dự án thiếu asset -> phải chặn
$failQueueRes = $videoQueue->enqueueVideoRender($missingAssetRes['id_video']);
assertTest($failQueueRes['success'] === false, "Job Queue chặn đưa dự án WAITING_ASSET vào hàng đợi render");

// Enqueue dự án hợp lệ
$enqueueRes = $videoQueue->enqueueVideoRender($idVideo, 'mock');
assertTest($enqueueRes['success'] === true && $enqueueRes['id_job'] > 0, "Đưa dự án video hợp lệ vào hàng đợi table_ai_video_job thành công");
$idJob = (int)$enqueueRes['id_job'];

// Worker xử lý Job
$processRes = $videoQueue->processNextJob($idJob);
assertTest($processRes['success'] === true && $processRes['status'] === 'REVIEW_REQUIRED', "Background Worker render, tải về và chuyển trạng thái sang REVIEW_REQUIRED");

$jobDb = $d->rawQueryOne("SELECT * FROM table_ai_video_job WHERE id = ?", array($idJob));
assertTest(!empty($jobDb) && $jobDb['status'] === 'SUCCESS', "Trạng thái tác vụ hàng đợi chuyển sang SUCCESS");

$videoRendered = $d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ?", array($idVideo));
assertTest(!empty($videoRendered['video_file']) && file_exists($videoRendered['video_file']), "File video MP4 đã được tải về lưu trữ cục bộ an toàn: " . ($videoRendered['video_file'] ?? ''));

// -------------------------------------------------------------
// NHÓM 8: MEDIA VALIDATION & QUALITY CHECK
// -------------------------------------------------------------
$qcReport = !empty($videoRendered['quality_report']) ? json_decode($videoRendered['quality_report'], true) : array();
assertTest(!empty($qcReport['passed']) && $qcReport['score'] >= 80.0, "Bộ kiểm định Media QC xác nhận video đạt chuẩn (Score: " . ($qcReport['score'] ?? 0) . "/100)");

// Test phát hiện zero-byte file
$corruptPath = 'upload/video/test_corrupt_' . $runSuffix . '.mp4';
@file_put_contents($corruptPath, '');
$badQc = $videoEngine->validateRenderedMedia($corruptPath);
assertTest($badQc['passed'] === false && $badQc['checks']['non_zero_byte'] === false, "Media QC phát hiện file video rỗng 0-byte và từ chối");
@unlink($corruptPath);

// -------------------------------------------------------------
// NHÓM 9: STRICT HUMAN GATE (NO AUTO-PUBLISH)
// -------------------------------------------------------------
// Video render xong dừng ở REVIEW_REQUIRED, không auto approve
assertTest($videoRendered['status'] === 'REVIEW_REQUIRED', "Video sau khi render bắt buộc dừng ở trạng thái REVIEW_REQUIRED (Strict Human Gate)");

// Admin từ chối video
$rejectRes = $videoEngine->rejectVideo($idVideo, "Giọng đọc chưa đúng ngữ điệu mong muốn", "AdminTest");
assertTest($rejectRes['success'] === true && $rejectRes['status'] === 'REJECTED', "Admin từ chối video (REJECTED) kèm lý do phản hồi thành công");

// Đưa về trạng thái REVIEW_REQUIRED để test Approve
$d->rawQuery("UPDATE table_ai_video SET status = 'REVIEW_REQUIRED' WHERE id = ?", array($idVideo));

// Admin phê duyệt video
$approveRes = $videoEngine->approveVideo($idVideo, "SuperAdmin");
assertTest($approveRes['success'] === true && $approveRes['status'] === 'APPROVED', "Admin phê duyệt video (APPROVED) thành công");

$finalVideo = $d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ?", array($idVideo));
assertTest($finalVideo['status'] === 'APPROVED' && $finalVideo['reviewed_by'] === 'SuperAdmin', "Video đã được lưu trạng thái APPROVED và ghi nhận người duyệt");

// Kiểm tra rào chắn Auto-publish: Không có cờ tự động đẩy lên TikTok/YouTube
assertTest(!isset($finalVideo['published_tiktok_id']) && !isset($finalVideo['auto_posted']), "Không có bất kỳ cơ chế auto-post TikTok nào trong Phase 06");

// -------------------------------------------------------------
// NHÓM 10: RECOVERY & BATCH PROCESSING
// -------------------------------------------------------------
// Giả lập stale job treo > 10 phút
$staleJobId = $d->insert('ai_video_job', array(
    'id_video' => $idVideo,
    'provider' => 'mock',
    'status' => 'RUNNING',
    'attempts' => 1,
    'max_attempts' => 3,
    'started_at' => time() - 700, // 11 phút trước
    'date_created' => time() - 700
));
$videoQueue->recoverStaleJobs();
$staleAfter = $d->rawQueryOne("SELECT status FROM table_ai_video_job WHERE id = ?", array($staleJobId));
assertTest($staleAfter['status'] === 'PENDING', "Tự động phục hồi Stale Job bị treo sau 10 phút về trạng thái PENDING");

// Dọn dẹp fixture
@unlink('upload/product/' . $mockPhotoName);

echo "\n=======================================================\n";
echo "TEST RESULTS: " . $passCount . " PASSED / " . $failCount . " FAILED\n";
echo "=======================================================\n";

if ($failCount === 0) {
    echo ">>> ALL PHASE 06 UNIT & INTEGRATION TESTS PASSED (100%) <<<\n";
    exit(0);
} else {
    echo ">>> SOME TESTS FAILED! <<<\n";
    exit(1);
}
