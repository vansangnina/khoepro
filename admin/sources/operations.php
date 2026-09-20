<?php
if (!defined('SOURCES')) die("Error");

require_once LIBRARIES . 'class/class.OperationsService.php';

$ops = new OperationsService($d, $func);

$linkOverview  = "index.php?com=operations&act=overview";
$linkPipeline  = "index.php?com=operations&act=pipeline";
$linkJobs      = "index.php?com=operations&act=jobs";
$linkProviders = "index.php?com=operations&act=providers";
$linkCosts     = "index.php?com=operations&act=costs";
$linkAlerts    = "index.php?com=operations&act=alerts";
$linkLogs      = "index.php?com=operations&act=logs";
$linkSettings  = "index.php?com=operations&act=settings";

switch ($act) {
    /* 1. Overview Dashboard */
    case "overview":
        viewOverview();
        $template = "operations/overview";
        break;

    /* 2. Pipeline & Human Action Queue */
    case "pipeline":
        viewPipeline();
        $template = "operations/pipeline";
        break;

    /* 3. Jobs & Queues Management */
    case "jobs":
        viewJobs();
        $template = "operations/jobs";
        break;

    /* 4. Job Detail View */
    case "job_detail":
        viewJobDetail();
        $template = "operations/job_detail";
        break;

    /* 5. Providers Status */
    case "providers":
        viewProviders();
        $template = "operations/providers";
        break;

    /* 6. Costs & Budget Management */
    case "costs":
        viewCosts();
        $template = "operations/costs";
        break;

    /* 7. Alerts & Incidents Center */
    case "alerts":
        viewAlerts();
        $template = "operations/alerts";
        break;

    /* 8. Logs & Audit Trail */
    case "logs":
        viewLogs();
        $template = "operations/logs";
        break;

    /* 9. Operations Settings */
    case "settings":
        viewSettings();
        $template = "operations/settings";
        break;

    /* Actions (POST Mutating) */
    case "retry_job":
        retryJobAction();
        break;

    case "cancel_job":
        cancelJobAction();
        break;

    case "acknowledge_alert":
        acknowledgeAlertAction();
        break;

    case "resolve_alert":
        resolveAlertAction();
        break;

    case "toggle_automation":
        toggleAutomationAction();
        break;

    case "emergency_pause_paid":
        emergencyPausePaidAction();
        break;

    case "override_budget":
        overrideBudgetAction();
        break;

    case "save_settings":
        saveSettingsAction();
        break;

    default:
        viewOverview();
        $template = "operations/overview";
        break;
}

/**
 * 1. Overview Dashboard
 */
function viewOverview() {
    global $ops, $workerStatuses, $queueSummaries, $costToday, $activeAlerts, $humanActions, $envHealth, $freshness;

    $workerStatuses = $ops->getWorkerStatuses();
    $queueSummaries = $ops->getQueueSummaries();
    $costToday = $ops->getCostSummary('today');
    $activeAlerts = $ops->getAlerts('ACTIVE', 10);
    $humanActions = $ops->getHumanActionQueue();
    $envHealth = $ops->getEnvironmentHealth();
    $freshness = $ops->getDataFreshness();
}

/**
 * 2. Pipeline & Human Action Queue
 */
function viewPipeline() {
    global $ops, $queueSummaries, $humanActions, $workerStatuses;

    $queueSummaries = $ops->getQueueSummaries();
    $humanActions = $ops->getHumanActionQueue();
    $workerStatuses = $ops->getWorkerStatuses();
}

/**
 * 3. Jobs & Queues Management
 */
function viewJobs() {
    global $ops, $jobList, $filterModule, $filterStatus, $curPage, $queueSummaries, $stuckJobs;

    $filterModule = !empty($_GET['module']) ? htmlspecialchars(trim($_GET['module'])) : 'all';
    $filterStatus = !empty($_GET['status']) ? htmlspecialchars(trim($_GET['status'])) : 'all';
    $curPage = !empty($_GET['p']) ? max(1, (int)$_GET['p']) : 1;

    $filters = array('module' => $filterModule, 'status' => $filterStatus);
    $jobList = $ops->getAllJobs($filters, $curPage, 20);
    $queueSummaries = $ops->getQueueSummaries();
    $stuckJobs = $ops->detectAndFlagStuckJobs();
}

/**
 * 4. Job Detail View
 */
function viewJobDetail() {
    global $ops, $func, $jobDetail, $module, $jobId;

    $module = !empty($_GET['module']) ? htmlspecialchars(trim($_GET['module'])) : '';
    $jobId = !empty($_GET['id']) ? (int)$_GET['id'] : 0;

    if (empty($module) || empty($jobId)) {
        $func->transfer("Thông tin tác vụ không hợp lệ", "index.php?com=operations&act=jobs", false);
    }

    $jobDetail = $ops->getJobDetail($module, $jobId);
    if (!$jobDetail) {
        $func->transfer("Không tìm thấy dữ liệu tác vụ", "index.php?com=operations&act=jobs", false);
    }
}

/**
 * 5. Providers Status
 */
function viewProviders() {
    global $ops, $providersList;

    $providersList = $ops->getProvidersStatus();
}

/**
 * 6. Costs & Budget Management
 */
function viewCosts() {
    global $ops, $costData, $timeRange;

    $timeRange = !empty($_GET['range']) ? htmlspecialchars(trim($_GET['range'])) : 'today';
    if (!in_array($timeRange, array('today', '7days', '30days', 'all'))) {
        $timeRange = 'today';
    }

    $costData = $ops->getCostSummary($timeRange);
}

/**
 * 7. Alerts & Incidents Center
 */
function viewAlerts() {
    global $ops, $alertsList, $filterStatus;

    $filterStatus = !empty($_GET['status']) ? htmlspecialchars(trim($_GET['status'])) : 'ACTIVE';
    $alertsList = $ops->getAlerts($filterStatus, 50);
}

/**
 * 8. Logs & Audit Trail
 */
function viewLogs() {
    global $d, $ops, $logsList, $filterLevel, $filterModule, $keyword;

    $filterLevel = !empty($_GET['level']) ? htmlspecialchars(trim($_GET['level'])) : 'all';
    $filterModule = !empty($_GET['module']) ? htmlspecialchars(trim($_GET['module'])) : 'all';
    $keyword = !empty($_GET['keyword']) ? htmlspecialchars(trim($_GET['keyword'])) : '';

    $where = "WHERE 1=1";
    $params = array();

    if ($filterLevel !== 'all') {
        $where .= " AND severity = ?";
        $params[] = $filterLevel;
    }
    if ($filterModule !== 'all') {
        $where .= " AND module = ?";
        $params[] = $filterModule;
    }
    if (!empty($keyword)) {
        $where .= " AND (title LIKE ? OR message LIKE ?)";
        $params[] = "%{$keyword}%";
        $params[] = "%{$keyword}%";
    }

    $logsList = $d->rawQuery("SELECT * FROM table_system_alert {$where} ORDER BY last_seen_at DESC LIMIT 50", $params);
    if (!empty($logsList)) {
        foreach ($logsList as &$lg) {
            $lg['title'] = $ops->sanitizeSecrets($lg['title']);
            $lg['message'] = $ops->sanitizeSecrets($lg['message']);
        }
    }
}

/**
 * 9. Operations Settings
 */
function viewSettings() {
    global $ops, $settingsData;

    $settingsData = array(
        'automation_enabled' => (int)$ops->getSetting('automation_enabled', 1),
        'pause_paid_automation' => (int)$ops->getSetting('pause_paid_automation', 0),
        'research_automation_enabled' => (int)$ops->getSetting('research_automation_enabled', 1),
        'content_automation_enabled' => (int)$ops->getSetting('content_automation_enabled', 1),
        'video_automation_enabled' => (int)$ops->getSetting('video_automation_enabled', 1),
        'publishing_automation_enabled' => (int)$ops->getSetting('publishing_automation_enabled', 1),
        'optimization_automation_enabled' => (int)$ops->getSetting('optimization_automation_enabled', 1),
        'daily_external_api_budget' => (float)$ops->getSetting('daily_external_api_budget', 200000),
        'monthly_external_api_budget' => (float)$ops->getSetting('monthly_external_api_budget', 3000000),
        'worker_heartbeat_threshold_seconds' => (int)$ops->getSetting('worker_heartbeat_threshold_seconds', 300),
        'stuck_job_threshold_seconds' => (int)$ops->getSetting('stuck_job_threshold_seconds', 1800),
        'tracking_stale_threshold_hours' => (int)$ops->getSetting('tracking_stale_threshold_hours', 24),
        'affiliate_click_stale_threshold_hours' => (int)$ops->getSetting('affiliate_click_stale_threshold_hours', 48),
        'conversion_stale_threshold_days' => (int)$ops->getSetting('conversion_stale_threshold_days', 7)
    );
}

/**
 * Action: Retry Job
 */
function retryJobAction() {
    global $ops, $func;

    if (empty($_POST)) {
        $func->transfer("Phương thức yêu cầu không hợp lệ", "index.php?com=operations&act=jobs", false);
    }

    $module = !empty($_POST['module']) ? htmlspecialchars(trim($_POST['module'])) : '';
    $jobId = !empty($_POST['job_id']) ? (int)$_POST['job_id'] : 0;
    $adminUser = $_SESSION['loginAdmin']['username'] ?? 'admin';

    $res = $ops->retryJob($module, $jobId, $adminUser);
    if ($res['success']) {
        $func->transfer($res['message'], "index.php?com=operations&act=jobs&module=" . $module);
    } else {
        $func->transfer("Không thể retry: " . $res['message'], "index.php?com=operations&act=jobs&module=" . $module, false);
    }
}

/**
 * Action: Cancel Job
 */
function cancelJobAction() {
    global $ops, $func;

    if (empty($_POST)) {
        $func->transfer("Phương thức yêu cầu không hợp lệ", "index.php?com=operations&act=jobs", false);
    }

    $module = !empty($_POST['module']) ? htmlspecialchars(trim($_POST['module'])) : '';
    $jobId = !empty($_POST['job_id']) ? (int)$_POST['job_id'] : 0;
    $adminUser = $_SESSION['loginAdmin']['username'] ?? 'admin';

    $res = $ops->cancelJob($module, $jobId, $adminUser);
    if ($res['success']) {
        $func->transfer($res['message'], "index.php?com=operations&act=jobs&module=" . $module);
    } else {
        $func->transfer("Không thể hủy tác vụ: " . $res['message'], "index.php?com=operations&act=jobs&module=" . $module, false);
    }
}

/**
 * Action: Acknowledge Alert
 */
function acknowledgeAlertAction() {
    global $ops, $func;

    $alertId = !empty($_POST['alert_id']) ? (int)$_POST['alert_id'] : 0;
    $adminUser = $_SESSION['loginAdmin']['username'] ?? 'admin';

    if (!empty($alertId)) {
        $ops->acknowledgeAlert($alertId, $adminUser);
        $func->transfer("Đã xác nhận sự cố thành công", "index.php?com=operations&act=alerts");
    }
    $func->transfer("Không thể xử lý sự cố", "index.php?com=operations&act=alerts", false);
}

/**
 * Action: Resolve Alert
 */
function resolveAlertAction() {
    global $ops, $func;

    $alertId = !empty($_POST['alert_id']) ? (int)$_POST['alert_id'] : 0;
    if (!empty($alertId)) {
        $ops->resolveAlert($alertId);
        $func->transfer("Đã đánh dấu giải quyết sự cố", "index.php?com=operations&act=alerts");
    }
    $func->transfer("Không thể giải quyết sự cố", "index.php?com=operations&act=alerts", false);
}

/**
 * Action: Toggle Automation Switch
 */
function toggleAutomationAction() {
    global $ops, $func;

    $module = !empty($_POST['module']) ? htmlspecialchars(trim($_POST['module'])) : 'all';
    $enabled = !empty($_POST['enabled']) ? true : false;
    $adminUser = $_SESSION['loginAdmin']['username'] ?? 'admin';

    $ops->toggleAutomation($module, $enabled, $adminUser);
    $func->transfer("Đã cập nhật trạng thái tự động hóa", "index.php?com=operations&act=settings");
}

/**
 * Action: Emergency Pause Paid Automation
 */
function emergencyPausePaidAction() {
    global $ops, $func;

    $pause = !empty($_POST['pause']) ? true : false;
    $reason = !empty($_POST['reason']) ? htmlspecialchars(trim($_POST['reason'])) : '';
    $adminUser = $_SESSION['loginAdmin']['username'] ?? 'admin';

    $ops->toggleEmergencyPausePaid($pause, $reason, $adminUser);
    $msg = $pause ? "Đã KÍCH HOẠT DỪNG KHẨN CẤP toàn bộ API trả phí" : "Đã khôi phục tự động hóa API trả phí";
    $func->transfer($msg, "index.php?com=operations&act=overview");
}

/**
 * Action: Budget Override
 */
function overrideBudgetAction() {
    global $ops, $func;

    $amount = !empty($_POST['amount']) ? (float)$_POST['amount'] : 0;
    $reason = !empty($_POST['reason']) ? htmlspecialchars(trim($_POST['reason'])) : '';
    $adminUser = $_SESSION['loginAdmin']['username'] ?? 'admin';

    if (empty($reason)) {
        $func->transfer("Vui lòng nhập lý do giải trình phê duyệt vượt ngân sách", "index.php?com=operations&act=costs", false);
    }

    $ops->overrideBudget($amount, $reason, $adminUser);
    $func->transfer("Đã ghi nhận kiểm toán vượt ngân sách thành công", "index.php?com=operations&act=costs");
}

/**
 * Action: Save Settings
 */
function saveSettingsAction() {
    global $ops, $func;

    if (!empty($_POST['data'])) {
        foreach ($_POST['data'] as $k => $v) {
            $ops->saveSetting($k, $v, 'operations');
        }
        $func->transfer("Lưu cấu hình vận hành thành công", "index.php?com=operations&act=settings");
    }

    $func->transfer("Dữ liệu không hợp lệ", "index.php?com=operations&act=settings", false);
}
