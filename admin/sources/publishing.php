<?php
if (!defined('SOURCES')) die("Error");

require_once LIBRARIES . 'class/class.PublishProvider.php';
require_once LIBRARIES . 'class/class.PublishingCenter.php';
require_once LIBRARIES . 'class/class.PublishJobQueue.php';

$publishingCenter = new PublishingCenter($d, $func);
$publishQueue = new PublishJobQueue($d, $func);

$linkMan = "index.php?com=publishing&act=man";
$linkView = "index.php?com=publishing&act=view";
$linkCreate = "index.php?com=publishing&act=create";
$linkQueue = "index.php?com=publishing&act=queue";
$linkCalendar = "index.php?com=publishing&act=calendar";
$linkAccounts = "index.php?com=publishing&act=accounts";
$linkSettings = "index.php?com=publishing&act=settings";

switch ($act) {
    /* 1. Posts List */
    case "man":
        viewPublishingPosts();
        $template = "publishing/mans";
        break;

    /* 2. Post Detail & Manual Package */
    case "view":
        viewPublishingDetail();
        $template = "publishing/view";
        break;

    /* 3. Create Post Form */
    case "create":
        viewCreatePostForm();
        $template = "publishing/create";
        break;

    /* 4. Save New Post */
    case "save_create":
        saveCreatePost();
        break;

    /* 5. Save Edit Post */
    case "save_edit":
        saveEditPost();
        break;

    /* 6. Mark Ready */
    case "ready":
        markPostAsReady();
        break;

    /* 7. Schedule Post */
    case "schedule":
        schedulePostAction();
        break;

    /* 8. Publish Now */
    case "publish_now":
        publishNowAction();
        break;

    /* 9. Mark Manual Published */
    case "mark_published":
        markManualPublishedAction();
        break;

    /* 10. Duplicate Post */
    case "duplicate":
        duplicatePostAction();
        break;

    /* 11. Delete Post */
    case "delete":
        deletePostAction();
        break;

    /* 12. Publishing Queue & Worker Trigger */
    case "queue":
        viewQueueMonitor();
        $template = "publishing/queue";
        break;

    case "process_queue":
        processQueueNow();
        break;

    case "retry_failed":
        retryFailedPostAction();
        break;

    /* 13. Publishing Calendar */
    case "calendar":
        viewPublishingCalendar();
        $template = "publishing/calendar";
        break;

    /* 14. Multi-channel Accounts */
    case "accounts":
        viewAccountsList();
        $template = "publishing/accounts";
        break;

    case "save_account":
        savePublishAccount();
        break;

    case "delete_account":
        deletePublishAccount();
        break;

    /* 15. Settings & TikTok API Status */
    case "settings":
        viewPublishingSettings();
        $template = "publishing/settings";
        break;

    case "save_settings":
        savePublishingSettings();
        break;

    default:
        $template = "404";
}

/* -------------------------------------------------------------------------- */
/* Controller Action Implementations                                          */
/* -------------------------------------------------------------------------- */

function viewPublishingPosts() {
    global $d, $func, $curPage, $items, $paging, $linkMan, $publishingCenter, $stats;

    $where = "WHERE 1=1";
    $params = array();

    // Lọc theo trạng thái
    $status = !empty($_GET['status']) ? htmlspecialchars($_GET['status']) : '';
    if (!empty($status)) {
        $where .= " AND p.status = ?";
        $params[] = $status;
    }

    // Lọc theo nền tảng
    $platform = !empty($_GET['platform']) ? htmlspecialchars($_GET['platform']) : '';
    if (!empty($platform)) {
        $where .= " AND p.platform = ?";
        $params[] = $platform;
    }

    // Tìm kiếm từ khóa
    $keyword = !empty($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '';
    if (!empty($keyword)) {
        $where .= " AND (p.title LIKE ? OR p.caption LIKE ? OR pr.namevi LIKE ?)";
        $params[] = "%{$keyword}%";
        $params[] = "%{$keyword}%";
        $params[] = "%{$keyword}%";
    }

    $perPage = 15;
    $startPoint = ($curPage * $perPage) - $perPage;

    $sqlCount = "SELECT count(p.id) as total FROM table_publish_post p LEFT JOIN table_product pr ON p.id_product = pr.id {$where}";
    $countRow = $d->rawQueryOne($sqlCount, $params);
    $total = !empty($countRow['total']) ? (int)$countRow['total'] : 0;

    $sql = "SELECT p.*, pr.namevi as product_name, pr.slugvi as product_slug, v.video_file, v.thumbnail as video_thumbnail, v.duration_actual as video_duration, v.mode as video_mode, a.account_name, a.account_handle 
            FROM table_publish_post p 
            LEFT JOIN table_product pr ON p.id_product = pr.id 
            LEFT JOIN table_ai_video v ON p.id_video = v.id 
            LEFT JOIN table_publish_account a ON p.account_id = a.id 
            {$where} 
            ORDER BY p.id DESC LIMIT {$startPoint}, {$perPage}";
    $items = $d->rawQuery($sql, $params);

    $url = $linkMan;
    if ($status) $url .= "&status={$status}";
    if ($platform) $url .= "&platform={$platform}";
    if ($keyword) $url .= "&keyword={$keyword}";
    $paging = $func->pagination($total, $perPage, $curPage, $url);

    // Thống kê widget
    $stats = array(
        'total' => (int)($d->rawQueryOne("SELECT count(id) as c FROM table_publish_post")['c'] ?? 0),
        'draft' => (int)($d->rawQueryOne("SELECT count(id) as c FROM table_publish_post WHERE status = 'DRAFT'")['c'] ?? 0),
        'ready' => (int)($d->rawQueryOne("SELECT count(id) as c FROM table_publish_post WHERE status = 'READY'")['c'] ?? 0),
        'scheduled' => (int)($d->rawQueryOne("SELECT count(id) as c FROM table_publish_post WHERE status = 'SCHEDULED'")['c'] ?? 0),
        'published' => (int)($d->rawQueryOne("SELECT count(id) as c FROM table_publish_post WHERE status = 'PUBLISHED'")['c'] ?? 0),
        'failed' => (int)($d->rawQueryOne("SELECT count(id) as c FROM table_publish_post WHERE status = 'FAILED'")['c'] ?? 0),
    );
}

function viewPublishingDetail() {
    global $d, $func, $item, $checklist, $publishingCenter, $linkMan;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        $func->transfer("Không tìm thấy bài đăng.", $linkMan, false);
    }

    $item = $publishingCenter->getPost($id);
    if (empty($item)) {
        $func->transfer("Bài đăng không tồn tại.", $linkMan, false);
    }

    $checklist = $publishingCenter->validatePrePublishChecklist($id);
}

function viewCreatePostForm() {
    global $d, $func, $approvedVideos, $accounts, $linkMan;

    // Lấy danh sách video đã duyệt (APPROVED)
    $approvedVideos = $d->rawQuery(
        "SELECT v.id, v.title, v.video_file, v.thumbnail, v.duration_actual, v.mode, v.is_outdated, pr.id as product_id, pr.namevi as product_name 
         FROM table_ai_video v 
         LEFT JOIN table_product pr ON v.id_product = pr.id 
         WHERE v.status = 'APPROVED' 
         ORDER BY v.id DESC"
    );

    if (empty($approvedVideos)) {
        $func->transfer("Hiện tại chưa có video nào được Admin duyệt (APPROVED). Vui lòng duyệt video tại AI Video Engine trước khi tạo Post Package.", "index.php?com=ai_video&act=mans", false);
    }

    $accounts = $d->rawQuery("SELECT * FROM table_publish_account WHERE status = 'active' ORDER BY is_default DESC, id ASC");
}

function saveCreatePost() {
    global $d, $func, $publishingCenter, $linkMan, $linkView;

    $idVideo = !empty($_POST['id_video']) ? (int)$_POST['id_video'] : 0;
    if (!$idVideo) {
        $func->transfer("Vui lòng chọn một video đã được duyệt.", "index.php?com=publishing&act=create", false);
    }

    $options = array(
        'title' => !empty($_POST['title']) ? trim($_POST['title']) : '',
        'caption' => !empty($_POST['caption']) ? trim($_POST['caption']) : '',
        'hashtags' => !empty($_POST['hashtags']) ? trim($_POST['hashtags']) : '',
        'platform' => !empty($_POST['platform']) ? trim($_POST['platform']) : 'tiktok',
        'account_id' => !empty($_POST['account_id']) ? (int)$_POST['account_id'] : null,
        'provider' => !empty($_POST['provider']) ? trim($_POST['provider']) : 'manual',
        'disclosure_text' => !empty($_POST['disclosure_text']) ? trim($_POST['disclosure_text']) : ''
    );

    $adminUser = !empty($_SESSION['login_admin']['username']) ? $_SESSION['login_admin']['username'] : 'admin';
    $res = $publishingCenter->createPostFromApprovedVideo($idVideo, $options, $adminUser);

    if ($res['success']) {
        $func->transfer("Khởi tạo Post Package thành công!", "{$linkView}&id={$res['id_post']}");
    } else {
        $func->transfer("Lỗi tạo Post Package: " . $res['error'], "index.php?com=publishing&act=create", false);
    }
}

function saveEditPost() {
    global $d, $func, $publishingCenter, $linkView, $linkMan;

    $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
    if (!$id) {
        $func->transfer("Bài đăng không hợp lệ.", $linkMan, false);
    }

    $data = array(
        'title' => !empty($_POST['title']) ? trim($_POST['title']) : '',
        'caption' => !empty($_POST['caption']) ? trim($_POST['caption']) : '',
        'hashtags' => !empty($_POST['hashtags']) ? trim($_POST['hashtags']) : '',
        'platform' => !empty($_POST['platform']) ? trim($_POST['platform']) : 'tiktok',
        'account_id' => !empty($_POST['account_id']) ? (int)$_POST['account_id'] : null,
        'provider' => !empty($_POST['provider']) ? trim($_POST['provider']) : 'manual',
        'disclosure_text' => !empty($_POST['disclosure_text']) ? trim($_POST['disclosure_text']) : '',
        'affiliate_offer_id' => !empty($_POST['affiliate_offer_id']) ? (int)$_POST['affiliate_offer_id'] : null
    );

    $adminUser = !empty($_SESSION['login_admin']['username']) ? $_SESSION['login_admin']['username'] : 'admin';
    $res = $publishingCenter->updatePost($id, $data, $adminUser);

    if ($res['success']) {
        $msg = "Cập nhật bài đăng thành công!";
        if (!empty($res['invalidated'])) {
            $msg .= " Lưu ý: Do nội dung đã thay đổi, trạng thái được chuyển về DRAFT để kiểm định lại.";
        }
        $func->transfer($msg, "{$linkView}&id={$id}");
    } else {
        $func->transfer("Lỗi cập nhật: " . $res['error'], "{$linkView}&id={$id}", false);
    }
}

function markPostAsReady() {
    global $d, $func, $publishingCenter, $linkView, $linkMan;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        $func->transfer("Bài đăng không hợp lệ.", $linkMan, false);
    }

    $adminUser = !empty($_SESSION['login_admin']['username']) ? $_SESSION['login_admin']['username'] : 'admin';
    $res = $publishingCenter->markPostReady($id, $adminUser);

    if ($res['success']) {
        $func->transfer("Bài đăng đã sẵn sàng xuất bản (Trạng thái: {$res['status']}) và đã đóng băng nội dung Snapshot thành công!", "{$linkView}&id={$id}");
    } else {
        $func->transfer("Checklist không đạt: " . implode('; ', $res['errors']), "{$linkView}&id={$id}", false);
    }
}

function schedulePostAction() {
    global $d, $func, $publishingCenter, $linkView, $linkMan;

    $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
    $scheduleDate = !empty($_POST['schedule_date']) ? trim($_POST['schedule_date']) : '';
    $scheduleTime = !empty($_POST['schedule_time']) ? trim($_POST['schedule_time']) : '18:00';

    if (!$id || empty($scheduleDate)) {
        $func->transfer("Vui lòng chọn ngày giờ lên lịch hợp lệ.", "{$linkView}&id={$id}", false);
    }

    $dateTimeStr = $scheduleDate . ' ' . $scheduleTime . ':00';
    $timestamp = strtotime($dateTimeStr);

    if ($timestamp <= time()) {
        $func->transfer("Thời gian lên lịch phải ở tương lai.", "{$linkView}&id={$id}", false);
    }

    $adminUser = !empty($_SESSION['login_admin']['username']) ? $_SESSION['login_admin']['username'] : 'admin';
    $publishingCenter->updatePost($id, array('scheduled_at' => $timestamp), $adminUser);
    $res = $publishingCenter->markPostReady($id, $adminUser);

    if ($res['success']) {
        $func->transfer("Đã lên lịch xuất bản bài đăng vào: {$dateTimeStr} thành công!", "{$linkView}&id={$id}");
    } else {
        $func->transfer("Lỗi lên lịch: " . implode('; ', $res['errors']), "{$linkView}&id={$id}", false);
    }
}

function publishNowAction() {
    global $d, $func, $publishingCenter, $linkView, $linkMan;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        $func->transfer("Bài đăng không hợp lệ.", $linkMan, false);
    }

    $adminUser = !empty($_SESSION['login_admin']['username']) ? $_SESSION['login_admin']['username'] : 'admin';
    $res = $publishingCenter->publishNow($id, $adminUser);

    if ($res['success']) {
        if (($res['mode'] ?? '') === 'MANUAL') {
            $func->transfer("Đã chuẩn bị xong gói đăng bài thủ công! Vui lòng tải video, sao chép caption và cập nhật link sau khi đăng.", "{$linkView}&id={$id}&open_manual=1");
        } else {
            $func->transfer("Xuất bản video thành công qua API!", "{$linkView}&id={$id}");
        }
    } else {
        $func->transfer("Lỗi xuất bản: " . ($res['error'] ?? 'Unknown error'), "{$linkView}&id={$id}", false);
    }
}

function markManualPublishedAction() {
    global $d, $func, $publishingCenter, $linkView, $linkMan;

    $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
    $externalUrl = !empty($_POST['external_post_url']) ? trim($_POST['external_post_url']) : '';
    $externalId = !empty($_POST['external_post_id']) ? trim($_POST['external_post_id']) : null;

    if (!$id || empty($externalUrl)) {
        $func->transfer("Vui lòng nhập URL bài đăng TikTok thực tế.", "{$linkView}&id={$id}", false);
    }

    $adminUser = !empty($_SESSION['login_admin']['username']) ? $_SESSION['login_admin']['username'] : 'admin';
    $res = $publishingCenter->markManualPublished($id, $externalUrl, $externalId, $adminUser);

    if ($res['success']) {
        $func->transfer("Xác nhận xuất bản bài đăng thành công! Trạng thái chuyển sang PUBLISHED.", "{$linkView}&id={$id}");
    } else {
        $func->transfer("URL không hợp lệ: " . $res['error'], "{$linkView}&id={$id}", false);
    }
}

function duplicatePostAction() {
    global $d, $func, $publishingCenter, $linkView, $linkMan;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        $func->transfer("Bài đăng không hợp lệ.", $linkMan, false);
    }

    $adminUser = !empty($_SESSION['login_admin']['username']) ? $_SESSION['login_admin']['username'] : 'admin';
    $res = $publishingCenter->duplicatePost($id, $adminUser);

    if ($res['success']) {
        $func->transfer("Đã nhân bản bài đăng thành công!", "{$linkView}&id={$res['id_new_post']}");
    } else {
        $func->transfer("Lỗi nhân bản: " . $res['error'], "{$linkView}&id={$id}", false);
    }
}

function deletePostAction() {
    global $d, $func, $linkMan;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id) {
        $d->rawQuery("DELETE FROM table_publish_post WHERE id = ?", array($id));
        $d->rawQuery("DELETE FROM table_publish_log WHERE id_post = ?", array($id));
        $func->transfer("Đã xóa bài đăng và lịch sử liên quan.", $linkMan);
    }
    $func->transfer("Bài đăng không hợp lệ.", $linkMan, false);
}

function viewQueueMonitor() {
    global $d, $func, $scheduledPosts, $queueStats;

    $now = time();
    $scheduledPosts = $d->rawQuery(
        "SELECT p.*, pr.namevi as product_name, v.video_file, v.thumbnail, a.account_handle 
         FROM table_publish_post p 
         LEFT JOIN table_product pr ON p.id_product = pr.id 
         LEFT JOIN table_ai_video v ON p.id_video = v.id 
         LEFT JOIN table_publish_account a ON p.account_id = a.id 
         WHERE p.status IN ('SCHEDULED', 'QUEUED', 'PUBLISHING') 
         ORDER BY p.scheduled_at ASC"
    );

    $queueStats = array(
        'scheduled_count' => (int)($d->rawQueryOne("SELECT count(id) as c FROM table_publish_post WHERE status = 'SCHEDULED'")['c'] ?? 0),
        'due_count' => (int)($d->rawQueryOne("SELECT count(id) as c FROM table_publish_post WHERE status = 'SCHEDULED' AND scheduled_at <= ?", array($now))['c'] ?? 0),
        'publishing_count' => (int)($d->rawQueryOne("SELECT count(id) as c FROM table_publish_post WHERE status = 'PUBLISHING'")['c'] ?? 0),
        'failed_count' => (int)($d->rawQueryOne("SELECT count(id) as c FROM table_publish_post WHERE status = 'FAILED'")['c'] ?? 0),
    );
}

function processQueueNow() {
    global $d, $func, $publishQueue, $linkQueue;

    $adminUser = !empty($_SESSION['login_admin']['username']) ? $_SESSION['login_admin']['username'] : 'admin';
    $res = $publishQueue->processScheduledPosts(20, $adminUser);

    $func->transfer(
        "Đã xử lý xong hàng đợi: {$res['processed']} bài. Trong đó: {$res['manual_due']} bài Manual đã đến hạn (Ready), {$res['api_published']} bài API xuất bản, {$res['failed']} thất bại.",
        $linkQueue
    );
}

function retryFailedPostAction() {
    global $d, $func, $publishQueue, $linkView, $linkQueue;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        $func->transfer("Bài đăng không hợp lệ.", $linkQueue, false);
    }

    $adminUser = !empty($_SESSION['login_admin']['username']) ? $_SESSION['login_admin']['username'] : 'admin';
    $res = $publishQueue->retryFailedPost($id, $adminUser);

    if ($res['success']) {
        $func->transfer("Đã chuyển bài đăng sang READY để xử lý lại (Lần thử: {$res['attempts']}).", "{$linkView}&id={$id}");
    } else {
        $func->transfer("Lỗi: " . $res['error'], $linkQueue, false);
    }
}

function viewPublishingCalendar() {
    global $d, $func, $calendarItems;

    // Lấy tất cả bài đăng có lịch trình hoặc đã xuất bản
    $calendarItems = $d->rawQuery(
        "SELECT p.id, p.title, p.platform, p.status, p.scheduled_at, p.published_at, p.date_created, pr.namevi as product_name, a.account_handle 
         FROM table_publish_post p 
         LEFT JOIN table_product pr ON p.id_product = pr.id 
         LEFT JOIN table_publish_account a ON p.account_id = a.id 
         WHERE p.status IN ('SCHEDULED', 'READY', 'PUBLISHED', 'FAILED') 
         ORDER BY COALESCE(p.scheduled_at, p.published_at, p.date_created) DESC LIMIT 100"
    );
}

function viewAccountsList() {
    global $d, $func, $accounts;
    $accounts = $d->rawQuery("SELECT * FROM table_publish_account ORDER BY platform ASC, is_default DESC, id ASC");
}

function savePublishAccount() {
    global $d, $func, $linkAccounts;

    $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
    $data = array(
        'platform' => !empty($_POST['platform']) ? strtolower(trim($_POST['platform'])) : 'tiktok',
        'account_name' => !empty($_POST['account_name']) ? trim($_POST['account_name']) : 'FITNADO Channel',
        'account_handle' => !empty($_POST['account_handle']) ? trim($_POST['account_handle']) : '@fitnado.vn',
        'channel_id' => !empty($_POST['channel_id']) ? trim($_POST['channel_id']) : null,
        'provider' => !empty($_POST['provider']) ? trim($_POST['provider']) : 'manual',
        'status' => !empty($_POST['status']) ? trim($_POST['status']) : 'active',
        'auth_status' => !empty($_POST['auth_status']) ? trim($_POST['auth_status']) : 'MANUAL_ONLY',
        'is_default' => !empty($_POST['is_default']) ? 1 : 0,
        'date_updated' => time()
    );

    if ($id) {
        $d->rawQuery("UPDATE table_publish_account SET platform = ?, account_name = ?, account_handle = ?, channel_id = ?, provider = ?, status = ?, auth_status = ?, is_default = ?, date_updated = ? WHERE id = ?", array(
            $data['platform'], $data['account_name'], $data['account_handle'], $data['channel_id'], $data['provider'], $data['status'], $data['auth_status'], $data['is_default'], $data['date_updated'], $id
        ));
        $func->transfer("Cập nhật tài khoản xuất bản thành công!", $linkAccounts);
    } else {
        $data['date_created'] = time();
        $d->insert('publish_account', $data);
        $func->transfer("Thêm mới tài khoản xuất bản thành công!", $linkAccounts);
    }
}

function deletePublishAccount() {
    global $d, $func, $linkAccounts;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id) {
        $d->rawQuery("DELETE FROM table_publish_account WHERE id = ?", array($id));
        $func->transfer("Đã xóa tài khoản xuất bản.", $linkAccounts);
    }
    $func->transfer("Tài khoản không hợp lệ.", $linkAccounts, false);
}

function viewPublishingSettings() {
    global $d, $func, $tiktokStatus, $publishConfig;

    $tiktokProvider = new TikTokPublishProvider($d, $func);
    $tiktokStatus = $tiktokProvider->getStatusInfo();

    $settingRow = $d->rawQueryOne("SELECT options FROM table_setting LIMIT 1");
    $opts = !empty($settingRow['options']) ? json_decode($settingRow['options'], true) : array();
    $publishConfig = !empty($opts['publishing']) ? $opts['publishing'] : array(
        'default_provider' => 'manual',
        'max_hashtags' => 5,
        'default_disclosure' => 'FITNADO Affiliate Partner - Tham khảo kỹ thông số trước khi mua',
        'enable_utm' => true,
        'utm_source' => 'tiktok',
        'utm_medium' => 'organic_video'
    );
}

function savePublishingSettings() {
    global $d, $func, $linkSettings;

    $settingRow = $d->rawQueryOne("SELECT options FROM table_setting LIMIT 1");
    $opts = !empty($settingRow['options']) ? json_decode($settingRow['options'], true) : array();

    $opts['publishing'] = array(
        'default_provider' => !empty($_POST['default_provider']) ? trim($_POST['default_provider']) : 'manual',
        'max_hashtags' => !empty($_POST['max_hashtags']) ? (int)$_POST['max_hashtags'] : 5,
        'default_disclosure' => !empty($_POST['default_disclosure']) ? trim($_POST['default_disclosure']) : '',
        'enable_utm' => !empty($_POST['enable_utm']) ? true : false,
        'utm_source' => !empty($_POST['utm_source']) ? trim($_POST['utm_source']) : 'tiktok',
        'utm_medium' => !empty($_POST['utm_medium']) ? trim($_POST['utm_medium']) : 'organic_video'
    );

    $d->rawQuery("UPDATE table_setting SET options = ? WHERE id = 1", array(
        json_encode($opts, JSON_UNESCAPED_UNICODE)
    ));

    $func->transfer("Cấu hình Publishing Center đã được lưu thành công!", $linkSettings);
}
