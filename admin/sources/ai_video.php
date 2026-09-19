<?php
if (!defined('SOURCES')) die("Error");

require_once LIBRARIES . 'class/class.VideoProvider.php';
require_once LIBRARIES . 'class/class.AIVideoEngine.php';
require_once LIBRARIES . 'class/class.AIVideoJobQueue.php';

$videoEngine = new AIVideoEngine($d, $func);
$videoQueue = new AIVideoJobQueue($d, $func);

$linkMan = "index.php?com=ai_video&act=man";
$linkView = "index.php?com=ai_video&act=view";
$linkCreate = "index.php?com=ai_video&act=create";
$linkJobs = "index.php?com=ai_video&act=jobs";
$linkAssets = "index.php?com=ai_video&act=assets";
$linkSettings = "index.php?com=ai_video&act=settings";

switch ($act) {
    /* 1. Video Projects Library */
    case "man":
        viewVideoProjects();
        $template = "ai_video/mans";
        break;

    /* 2. Video Detail & Preview */
    case "view":
        viewVideoDetail();
        $template = "ai_video/view";
        break;

    /* 3. Create Project Form */
    case "create":
        viewCreateForm();
        $template = "ai_video/create";
        break;

    /* 4. Save Project */
    case "save_create":
        saveCreateProject();
        break;

    /* 5. Render Now (Enqueue & Trigger Worker) */
    case "render_now":
        renderVideoNow();
        break;

    /* 6. Batch Render */
    case "batch_render":
        batchRenderVideos();
        break;

    /* 7. Approve Video */
    case "approve":
        approveVideoProject();
        break;

    /* 8. Reject Video */
    case "reject":
        rejectVideoProject();
        break;

    /* 9. Jobs Queue Monitor */
    case "jobs":
        viewJobsMonitor();
        $template = "ai_video/jobs";
        break;

    case "job_retry":
        retryVideoJob();
        break;

    case "job_delete":
        deleteVideoJob();
        break;

    /* 10. Assets Manager */
    case "assets":
        viewAssetsManager();
        $template = "ai_video/assets";
        break;

    /* 11. Settings & Provider Configuration */
    case "settings":
        viewSettings();
        $template = "ai_video/settings";
        break;

    case "save_settings":
        saveSettings();
        break;

    default:
        $template = "404";
}

/**
 * 1. Danh sách dự án Video
 */
function viewVideoProjects() {
    global $d, $func, $items, $paging, $curPage, $stats, $productsList;

    $where = "WHERE 1=1";
    $params = array();

    if (!empty($_GET['keyword'])) {
        $kw = trim($_GET['keyword']);
        $where .= " AND (v.title LIKE ? OR p.namevi LIKE ?)";
        $params[] = '%' . $kw . '%';
        $params[] = '%' . $kw . '%';
    }

    if (!empty($_GET['id_product'])) {
        $where .= " AND v.id_product = ?";
        $params[] = (int)$_GET['id_product'];
    }

    if (!empty($_GET['status'])) {
        $where .= " AND v.status = ?";
        $params[] = trim($_GET['status']);
    }

    if (!empty($_GET['provider'])) {
        $where .= " AND v.provider = ?";
        $params[] = trim($_GET['provider']);
    }

    // Thống kê nhanh
    $stats = array(
        'total' => 0,
        'ready' => 0,
        'processing' => 0,
        'review_required' => 0,
        'approved' => 0,
        'outdated' => 0
    );
    $statRows = $d->rawQuery("SELECT status, is_outdated, COUNT(*) as cnt FROM table_ai_video GROUP BY status, is_outdated");
    foreach ($statRows as $sr) {
        $stats['total'] += (int)$sr['cnt'];
        $st = strtolower($sr['status']);
        if (isset($stats[$st])) {
            $stats[$st] += (int)$sr['cnt'];
        }
        if (!empty($sr['is_outdated'])) {
            $stats['outdated'] += (int)$sr['cnt'];
        }
    }

    // Phân trang
    $curPage = !empty($_GET['p']) ? (int)$_GET['p'] : 1;
    $perPage = 15;
    $startPoint = ($curPage - 1) * $perPage;

    $countRow = $d->rawQueryOne("SELECT COUNT(*) as total FROM table_ai_video v LEFT JOIN table_product p ON v.id_product = p.id $where", $params);
    $totalRecords = !empty($countRow['total']) ? (int)$countRow['total'] : 0;

    $sql = "SELECT v.*, p.namevi as product_name, p.photo as product_photo, c.title as content_title 
            FROM table_ai_video v 
            LEFT JOIN table_product p ON v.id_product = p.id 
            LEFT JOIN table_ai_content c ON v.id_content = c.id 
            $where 
            ORDER BY v.id DESC 
            LIMIT $startPoint, $perPage";

    $items = $d->rawQuery($sql, $params);
    $url = $func->getCurrentPageURL();
    $paging = $func->pagination($totalRecords, $perPage, $curPage, $url);

    $productsList = $d->rawQuery("SELECT id, namevi FROM table_product WHERE find_in_set('hienthi', status) ORDER BY id DESC LIMIT 100");
}

/**
 * 2. Chi tiết dự án Video & HTML5 Preview
 */
function viewVideoDetail() {
    global $d, $func, $item, $product, $content, $assets, $scenes, $qcReport, $historyList;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        $func->transfer("Dự án video không hợp lệ", "index.php?com=ai_video&act=man", false);
    }

    $item = $d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array($id));
    if (empty($item)) {
        $func->transfer("Dự án video không tồn tại", "index.php?com=ai_video&act=man", false);
    }

    // Kiểm tra Outdated tự động
    $videoEngine = new AIVideoEngine($d, $func);
    $videoEngine->checkVideoOutdated($id);
    $item = $d->rawQueryOne("SELECT * FROM table_ai_video WHERE id = ? LIMIT 1", array($id));

    $product = $d->rawQueryOne("SELECT id, namevi, photo, regular_price, sale_price FROM table_product WHERE id = ? LIMIT 1", array((int)$item['id_product']));
    $content = $d->rawQueryOne("SELECT * FROM table_ai_content WHERE id = ? LIMIT 1", array((int)$item['id_content']));
    $assets = $d->rawQuery("SELECT * FROM table_ai_video_asset WHERE id_video = ? ORDER BY scene_number ASC", array($id));

    $scenes = !empty($item['scenes_data']) ? json_decode($item['scenes_data'], true) : array();
    $qcReport = !empty($item['quality_report']) ? json_decode($item['quality_report'], true) : array();

    // Lịch sử các phiên bản khác của cùng sản phẩm
    $historyList = $d->rawQuery("SELECT id, version, status, video_type, duration_actual, date_created FROM table_ai_video WHERE id_product = ? AND id != ? ORDER BY version DESC", array((int)$item['id_product'], $id));
}

/**
 * 3. Giao diện Tạo Dự án Video
 */
function viewCreateForm() {
    global $d, $func, $approvedScripts, $templatesList, $voicesList;

    // Lấy các kịch bản TikTok đã được phê duyệt
    $approvedScripts = $d->rawQuery("SELECT c.id, c.id_product, c.title, c.target_duration, p.namevi as product_name, p.photo as product_photo 
                                     FROM table_ai_content c 
                                     LEFT JOIN table_product p ON c.id_product = p.id 
                                     WHERE c.content_type = 'tiktok_script' AND c.status IN ('APPROVED', 'APPLIED') 
                                     ORDER BY c.id DESC");

    $templatesList = AIVideoEngine::TEMPLATES;
    $voicesList = AIVideoEngine::VOICES;
}

/**
 * 4. Xử lý Tạo Dự án Video
 */
function saveCreateProject() {
    global $d, $func, $videoEngine;

    $idContent = !empty($_POST['id_content']) ? (int)$_POST['id_content'] : 0;
    if (!$idContent) {
        $func->transfer("Vui lòng chọn kịch bản TikTok đã duyệt", "index.php?com=ai_video&act=create", false);
    }

    $options = array(
        'title' => !empty($_POST['title']) ? trim($_POST['title']) : '',
        'video_type' => !empty($_POST['video_type']) ? trim($_POST['video_type']) : 'TIKTOK_9_16',
        'aspect_ratio' => !empty($_POST['aspect_ratio']) ? trim($_POST['aspect_ratio']) : '9:16',
        'target_duration' => !empty($_POST['target_duration']) ? (int)$_POST['target_duration'] : 30,
        'voice_id' => !empty($_POST['voice_id']) ? trim($_POST['voice_id']) : 'vi-VN-Standard-A',
        'template_id' => !empty($_POST['template_id']) ? trim($_POST['template_id']) : 'PROBLEM_SOLUTION',
        'provider' => !empty($_POST['provider']) ? trim($_POST['provider']) : 'mock'
    );

    $res = $videoEngine->createProjectFromApprovedContent($idContent, $options);
    if ($res['success']) {
        $msg = "Tạo dự án Video thành công (Version v" . $res['version'] . ")!";
        if ($res['status'] === 'WAITING_ASSET') {
            $msg .= " Lưu ý: Dự án đang thiếu " . $res['missing_assets_count'] . " tài nguyên trực quan.";
        }
        $func->transfer($msg, "index.php?com=ai_video&act=view&id=" . $res['id_video']);
    } else {
        $func->transfer("Lỗi tạo dự án video: " . $res['error'], "index.php?com=ai_video&act=create", false);
    }
}

/**
 * 5. Render ngay (Enqueue & Process)
 */
function renderVideoNow() {
    global $d, $func, $videoQueue;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        $func->transfer("Dự án không hợp lệ", "index.php?com=ai_video&act=man", false);
    }

    $res = $videoQueue->enqueueVideoRender($id);
    if ($res['success']) {
        // Kích hoạt ngay 1 lượt xử lý worker
        $videoQueue->processNextJob();
        $func->transfer("Đã đưa vào hàng đợi và kích hoạt tiến trình sản xuất video!", "index.php?com=ai_video&act=view&id=" . $id);
    } else {
        $func->transfer("Lỗi đưa vào hàng đợi: " . $res['error'], "index.php?com=ai_video&act=view&id=" . $id, false);
    }
}

/**
 * 6. Batch Render
 */
function batchRenderVideos() {
    global $d, $func, $videoQueue;

    $selectedIds = !empty($_POST['selected_ids']) ? $_POST['selected_ids'] : array();
    if (empty($selectedIds)) {
        $func->transfer("Chưa chọn video nào để render", "index.php?com=ai_video&act=man", false);
    }

    $provider = !empty($_POST['provider']) ? $_POST['provider'] : 'mock';
    $res = $videoQueue->enqueueBatch($selectedIds, $provider);

    // Xử lý ngay 1 đợt batch
    $videoQueue->processBatch(5);

    $msg = "Đã đưa " . $res['queued'] . "/" . $res['total'] . " dự án video vào hàng đợi render.";
    $func->transfer($msg, "index.php?com=ai_video&act=jobs");
}

/**
 * 7. Phê duyệt Video
 */
function approveVideoProject() {
    global $d, $func, $videoEngine;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    $adminName = !empty($_SESSION['login_admin']['username']) ? $_SESSION['login_admin']['username'] : 'Admin';

    $res = $videoEngine->approveVideo($id, $adminName);
    if ($res['success']) {
        $func->transfer("Phê duyệt Video thành công! Video đã sẵn sàng cho giai đoạn phân phối.", "index.php?com=ai_video&act=view&id=" . $id);
    } else {
        $func->transfer("Lỗi phê duyệt: " . $res['error'], "index.php?com=ai_video&act=view&id=" . $id, false);
    }
}

/**
 * 8. Từ chối Video
 */
function rejectVideoProject() {
    global $d, $func, $videoEngine;

    $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
    $reason = !empty($_POST['reject_reason']) ? trim($_POST['reject_reason']) : '';
    $adminName = !empty($_SESSION['login_admin']['username']) ? $_SESSION['login_admin']['username'] : 'Admin';

    $res = $videoEngine->rejectVideo($id, $reason, $adminName);
    if ($res['success']) {
        $func->transfer("Đã từ chối video và lưu lý do phản hồi.", "index.php?com=ai_video&act=view&id=" . $id);
    } else {
        $func->transfer("Lỗi từ chối video: " . $res['error'], "index.php?com=ai_video&act=view&id=" . $id, false);
    }
}

/**
 * 9. Giám sát Hàng đợi Jobs
 */
function viewJobsMonitor() {
    global $d, $func, $jobs, $paging, $curPage, $jobStats;

    $curPage = !empty($_GET['p']) ? (int)$_GET['p'] : 1;
    $perPage = 20;
    $startPoint = ($curPage - 1) * $perPage;

    $jobStats = array('PENDING' => 0, 'RUNNING' => 0, 'SUCCESS' => 0, 'FAILED' => 0);
    $statRows = $d->rawQuery("SELECT status, COUNT(*) as cnt FROM table_ai_video_job GROUP BY status");
    foreach ($statRows as $sr) {
        if (isset($jobStats[$sr['status']])) {
            $jobStats[$sr['status']] = (int)$sr['cnt'];
        }
    }

    $where = "WHERE 1=1";
    $params = array();
    if (!empty($_GET['status'])) {
        $where .= " AND j.status = ?";
        $params[] = trim($_GET['status']);
    }

    $countRow = $d->rawQueryOne("SELECT COUNT(*) as total FROM table_ai_video_job j $where", $params);
    $totalRecords = !empty($countRow['total']) ? (int)$countRow['total'] : 0;

    $sql = "SELECT j.*, v.title as video_title, v.target_duration, p.namevi as product_name 
            FROM table_ai_video_job j 
            LEFT JOIN table_ai_video v ON j.id_video = v.id 
            LEFT JOIN table_product p ON v.id_product = p.id 
            $where 
            ORDER BY j.id DESC 
            LIMIT $startPoint, $perPage";

    $jobs = $d->rawQuery($sql, $params);
    $url = $func->getCurrentPageURL();
    $paging = $func->pagination($totalRecords, $perPage, $curPage, $url);
}

function retryVideoJob() {
    global $d, $func, $videoQueue;
    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    $d->rawQuery("UPDATE table_ai_video_job SET status = 'PENDING', attempts = 0, error_message = NULL, date_updated = ? WHERE id = ?", array(time(), $id));
    $videoQueue->processNextJob();
    $func->transfer("Đã khởi động lại tác vụ render!", "index.php?com=ai_video&act=jobs");
}

function deleteVideoJob() {
    global $d, $func;
    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    $d->rawQuery("DELETE FROM table_ai_video_job WHERE id = ?", array($id));
    $func->transfer("Xóa tác vụ khỏi hàng đợi thành công!", "index.php?com=ai_video&act=jobs");
}

/**
 * 10. Quản trị Kho Tài nguyên Video (Assets)
 */
function viewAssetsManager() {
    global $d, $func, $assets, $paging, $curPage;

    $curPage = !empty($_GET['p']) ? (int)$_GET['p'] : 1;
    $perPage = 25;
    $startPoint = ($curPage - 1) * $perPage;

    $countRow = $d->rawQueryOne("SELECT COUNT(*) as total FROM table_ai_video_asset");
    $totalRecords = !empty($countRow['total']) ? (int)$countRow['total'] : 0;

    $sql = "SELECT a.*, v.title as video_title, p.namevi as product_name 
            FROM table_ai_video_asset a 
            LEFT JOIN table_ai_video v ON a.id_video = v.id 
            LEFT JOIN table_product p ON v.id_product = p.id 
            ORDER BY a.id DESC 
            LIMIT $startPoint, $perPage";

    $assets = $d->rawQuery($sql);
    $url = $func->getCurrentPageURL();
    $paging = $func->pagination($totalRecords, $perPage, $curPage, $url);
}

/**
 * 11. Cấu hình Cài đặt Provider & Hạn mức
 */
function viewSettings() {
    global $d, $func, $settingOptions, $aiVideoConfig, $templatesList, $voicesList;

    $settingRow = $d->rawQueryOne("SELECT options FROM table_setting LIMIT 1");
    $settingOptions = !empty($settingRow['options']) ? json_decode($settingRow['options'], true) : array();
    $aiVideoConfig = !empty($settingOptions['ai_video_config']) ? $settingOptions['ai_video_config'] : array();

    $templatesList = AIVideoEngine::TEMPLATES;
    $voicesList = AIVideoEngine::VOICES;
}

function saveSettings() {
    global $d, $func;

    $settingRow = $d->rawQueryOne("SELECT options FROM table_setting LIMIT 1");
    $options = !empty($settingRow['options']) ? json_decode($settingRow['options'], true) : array();

    $options['ai_video_config'] = array(
        'active_provider' => !empty($_POST['active_provider']) ? trim($_POST['active_provider']) : 'mock',
        'daily_video_limit' => !empty($_POST['daily_video_limit']) ? (int)$_POST['daily_video_limit'] : 20,
        'default_voice' => !empty($_POST['default_voice']) ? trim($_POST['default_voice']) : 'vi-VN-Standard-A',
        'default_template' => !empty($_POST['default_template']) ? trim($_POST['default_template']) : 'PROBLEM_SOLUTION',
        'creatify_api_key' => !empty($_POST['creatify_api_key']) ? trim($_POST['creatify_api_key']) : (!empty($options['ai_video_config']['creatify_api_key']) ? $options['ai_video_config']['creatify_api_key'] : ''),
        'arcads_api_key' => !empty($_POST['arcads_api_key']) ? trim($_POST['arcads_api_key']) : (!empty($options['ai_video_config']['arcads_api_key']) ? $options['ai_video_config']['arcads_api_key'] : ''),
        'heygen_api_key' => !empty($_POST['heygen_api_key']) ? trim($_POST['heygen_api_key']) : (!empty($options['ai_video_config']['heygen_api_key']) ? $options['ai_video_config']['heygen_api_key'] : '')
    );

    $d->rawQuery("UPDATE table_setting SET options = ? WHERE id = 1", array(json_encode($options, JSON_UNESCAPED_UNICODE)));
    $func->transfer("Cập nhật cấu hình AI Video Engine thành công!", "index.php?com=ai_video&act=settings");
}
