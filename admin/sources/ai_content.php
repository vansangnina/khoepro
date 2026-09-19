<?php
if (!defined('SOURCES')) die("Error");

require_once LIBRARIES . 'class/class.AIContentEngine.php';
require_once LIBRARIES . 'class/class.AIContentJobQueue.php';

$contentEngine = new AIContentEngine($d, $func);
$contentQueue = new AIContentJobQueue($d, $func);

$linkMan = "index.php?com=ai_content&act=man";
$linkView = "index.php?com=ai_content&act=view";
$linkGenerate = "index.php?com=ai_content&act=generate";
$linkJobs = "index.php?com=ai_content&act=jobs";
$linkSettings = "index.php?com=ai_content&act=settings";

switch ($act) {
    /* 1. Content Library */
    case "man":
        viewContentLibrary();
        $template = "ai_content/mans";
        break;

    /* 2. View / Review Content Detail */
    case "view":
        viewContentDetail();
        $template = "ai_content/view";
        break;

    /* 3. Generate Form */
    case "generate":
        viewGenerateForm();
        $template = "ai_content/generate";
        break;

    /* 4. Save & Dispatch Generation Job */
    case "save_generate":
        saveGenerateJobs();
        break;

    /* 5. Approve Content */
    case "approve":
        approveContentItem();
        break;

    /* 6. Reject Content */
    case "reject":
        rejectContentItem();
        break;

    /* 7. Diff View before applying to product */
    case "diff":
        viewDiffApply();
        $template = "ai_content/diff_apply";
        break;

    /* 8. Apply Content to Product */
    case "apply":
        applyContentToProduct();
        break;

    /* 9. Jobs Queue Monitor */
    case "jobs":
        viewJobsMonitor();
        $template = "ai_content/jobs";
        break;

    case "job_retry":
        retryJobItem();
        break;

    case "job_delete":
        deleteJobItem();
        break;

    /* 10. Settings & Prompt Inspector */
    case "settings":
        viewSettings();
        $template = "ai_content/settings";
        break;

    /* 11. Delete Content */
    case "delete":
        deleteContentItem();
        break;

    default:
        $template = "404";
}

/* =========================================================================
   CONTROLLER ACTION HANDLERS
   ========================================================================= */

function viewContentLibrary()
{
    global $d, $func, $items, $paging, $stats, $productsList;

    $where = "where 1=1";
    $params = array();

    // Filters
    if (!empty($_GET['id_product'])) {
        $where .= " and a.id_product = ?";
        $params[] = (int)$_GET['id_product'];
    }
    if (!empty($_GET['content_type'])) {
        $where .= " and a.content_type = ?";
        $params[] = htmlspecialchars($_GET['content_type']);
    }
    if (!empty($_GET['status'])) {
        $where .= " and a.status = ?";
        $params[] = htmlspecialchars($_GET['status']);
    }
    if (!empty($_GET['keyword'])) {
        $where .= " and (a.title like ? or p.namevi like ?)";
        $params[] = '%' . htmlspecialchars($_GET['keyword']) . '%';
        $params[] = '%' . htmlspecialchars($_GET['keyword']) . '%';
    }

    $curPage = !empty($_GET['p']) ? (int)$_GET['p'] : 1;
    $perPage = 15;
    $startIdx = ($curPage - 1) * $perPage;

    $sql = "select a.*, p.namevi as product_name, p.photo as product_photo, p.code as product_code 
            from #_ai_content a 
            left join #_product p on a.id_product = p.id 
            {$where} order by a.id desc limit {$startIdx}, {$perPage}";
    $items = $d->rawQuery($sql, $params);

    $sqlTotal = "select count(a.id) as total from #_ai_content a left join #_product p on a.id_product = p.id {$where}";
    $totalCount = (int)$d->rawQueryOne($sqlTotal, $params)['total'];
    $paging = $func->pagination($totalCount, $perPage, $curPage, "index.php?com=ai_content&act=man" . $func->getFilterQuery());

    // Stats
    $stats = array(
        'total' => (int)$d->rawQueryOne("select count(id) as c from #_ai_content")['c'],
        'review_required' => (int)$d->rawQueryOne("select count(id) as c from #_ai_content where status = 'REVIEW_REQUIRED'")['c'],
        'approved' => (int)$d->rawQueryOne("select count(id) as c from #_ai_content where status in ('APPROVED', 'APPLIED')")['c'],
        'applied' => (int)$d->rawQueryOne("select count(id) as c from #_ai_content where status = 'APPLIED'")['c'],
        'outdated' => (int)$d->rawQueryOne("select count(id) as c from #_ai_content where is_outdated = 1")['c']
    );

    // Products for filter dropdown
    $productsList = $d->rawQuery("select id, namevi, code from #_product where type = 'san-pham' order by namevi asc limit 0,100");
}

function viewContentDetail()
{
    global $d, $func, $item, $product, $research, $evidence, $structuredData, $qualityCheck, $versionHistory;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    $item = $d->rawQueryOne("select * from #_ai_content where id = ? limit 0,1", array($id));
    if (empty($item)) {
        $func->transfer("Nội dung không tồn tại", "index.php?com=ai_content&act=man", false);
    }

    $product = $d->rawQueryOne("select * from #_product where id = ? limit 0,1", array((int)$item['id_product']));
    $research = $d->rawQueryOne("select * from #_product_research where id_product = ? limit 0,1", array((int)$item['id_product']));

    $evidence = array();
    if (!empty($research['id'])) {
        $evidence = $d->rawQuery("select * from #_product_research_evidence where id_research = ? order by id asc", array((int)$research['id']));
    }

    $structuredData = !empty($item['structured_data']) ? json_decode($item['structured_data'], true) : array();
    $qualityCheck = !empty($item['quality_check']) ? json_decode($item['quality_check'], true) : array();

    // Version history of this content type for this product
    $versionHistory = $d->rawQuery(
        "select id, version, is_active, status, prompt_version, date_created from #_ai_content where id_product = ? and content_type = ? and language = ? order by version desc",
        array((int)$item['id_product'], $item['content_type'], $item['language'])
    );
}

function viewGenerateForm()
{
    global $d, $products, $selectedProductId;

    $selectedProductId = !empty($_GET['id_product']) ? (int)$_GET['id_product'] : 0;
    $products = $d->rawQuery("select id, namevi, code, sale_price, regular_price, photo from #_product where type = 'san-pham' order by id desc limit 0,200");
}

function saveGenerateJobs()
{
    global $d, $func, $contentQueue;

    $productIds = isset($_POST['product_ids']) && is_array($_POST['product_ids']) ? $_POST['product_ids'] : array();
    if (empty($productIds) && !empty($_POST['single_product_id'])) {
        $productIds = array((int)$_POST['single_product_id']);
    }

    if (empty($productIds)) {
        $func->transfer("Vui lòng chọn ít nhất một sản phẩm để tạo nội dung", "index.php?com=ai_content&act=generate", false);
    }

    $contentTypes = isset($_POST['content_types']) && is_array($_POST['content_types']) ? $_POST['content_types'] : array('product_analysis', 'tiktok_hooks', 'tiktok_script', 'seo_content');
    $tone = $_POST['tone'] ?? 'FITNADO_DEFAULT';
    $targetDuration = isset($_POST['target_duration']) ? (int)$_POST['target_duration'] : 30;
    $contentAngle = $_POST['content_angle'] ?? 'Problem/Solution';
    $language = $_POST['language'] ?? 'vi';

    $options = array(
        'tone' => $tone,
        'target_duration' => $targetDuration,
        'content_angle' => $contentAngle,
        'language' => $language,
        'priority' => 10
    );

    $dispatched = $contentQueue->createBatchJobs($productIds, $contentTypes, $options);

    // Auto trigger 1 cycle of worker if single job
    if (count($productIds) === 1 && !empty($dispatched['job_ids'][0])) {
        $contentQueue->executeJob($dispatched['job_ids'][0]);
        $func->transfer("Đã tạo nội dung AI thành công! Đang chuyển sang thư viện nội dung...", "index.php?com=ai_content&act=man&id_product=" . (int)$productIds[0]);
    }

    $func->transfer("Đã đưa " . count($dispatched['job_ids']) . " tác vụ vào Hàng đợi Content Jobs xử lý nền!", "index.php?com=ai_content&act=jobs");
}

function approveContentItem()
{
    global $func, $contentEngine;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    $res = $contentEngine->approveContent($id);

    if ($res) {
        $func->transfer("Đã phê duyệt nội dung AI thành công!", "index.php?com=ai_content&act=view&id=" . $id);
    } else {
        $func->transfer("Lỗi khi phê duyệt nội dung", "index.php?com=ai_content&act=view&id=" . $id, false);
    }
}

function rejectContentItem()
{
    global $func, $contentEngine;

    $id = !empty($_POST['id']) ? (int)$_POST['id'] : (!empty($_GET['id']) ? (int)$_GET['id'] : 0);
    $reason = $_POST['reject_reason'] ?? ($_GET['reason'] ?? '');

    $res = $contentEngine->rejectContent($id, $reason);

    if ($res) {
        $func->transfer("Đã từ chối nội dung thành công!", "index.php?com=ai_content&act=view&id=" . $id);
    } else {
        $func->transfer("Lỗi khi từ chối nội dung", "index.php?com=ai_content&act=view&id=" . $id, false);
    }
}

function viewDiffApply()
{
    global $d, $func, $item, $product, $structuredData, $currentFields, $newFields;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    $item = $d->rawQueryOne("select * from #_ai_content where id = ? limit 0,1", array($id));
    if (empty($item)) {
        $func->transfer("Nội dung không tồn tại", "index.php?com=ai_content&act=man", false);
    }

    $product = $d->rawQueryOne("select * from #_product where id = ? limit 0,1", array((int)$item['id_product']));
    $structuredData = !empty($item['structured_data']) ? json_decode($item['structured_data'], true) : array();

    // Prepare Diff Fields
    $currentFields = array();
    $newFields = array();

    if ($item['content_type'] === 'product_analysis') {
        $currentFields = array(
            'Mô tả ngắn (descvi)' => $product['descvi'] ?? '',
            'Ưu điểm (expert_pros)' => $product['expert_pros'] ?? '',
            'Nhược điểm (expert_cons)' => $product['expert_cons'] ?? '',
            'Đánh giá tổng quan (verdict)' => $product['verdict'] ?? '',
            'Phù hợp nhất với (best_for)' => $product['best_for'] ?? ''
        );
        $newFields = array(
            'Mô tả ngắn (descvi)' => $structuredData['problem_solved'] ?? '',
            'Ưu điểm (expert_pros)' => is_array($structuredData['pros'] ?? null) ? implode("\n", $structuredData['pros']) : '',
            'Nhược điểm (expert_cons)' => is_array($structuredData['cons'] ?? null) ? implode("\n", $structuredData['cons']) : '',
            'Đánh giá tổng quan (verdict)' => is_array($structuredData['buying_considerations'] ?? null) ? implode('. ', $structuredData['buying_considerations']) : '',
            'Phù hợp nhất với (best_for)' => is_array($structuredData['suitable_for'] ?? null) ? implode(', ', $structuredData['suitable_for']) : ''
        );
    } elseif ($item['content_type'] === 'review_draft') {
        $currentFields = array(
            'Bài viết đánh giá (contentvi)' => $product['contentvi'] ?? ''
        );
        $newFields = array(
            'Bài viết đánh giá (contentvi)' => $structuredData['article_body'] ?? $item['content_text']
        );
    } elseif ($item['content_type'] === 'seo_content') {
        $seo = $d->rawQueryOne("select * from #_seo where id_parent = ? and com = 'product' and act = 'man' and type = 'san-pham' limit 0,1", array($product['id']));
        $currentFields = array(
            'SEO Title' => $seo['titlevi'] ?? '',
            'SEO Description' => $seo['descriptionvi'] ?? '',
            'SEO Keywords' => $seo['keywordsvi'] ?? ''
        );
        $newFields = array(
            'SEO Title' => $structuredData['seo_title'] ?? '',
            'SEO Description' => $structuredData['seo_description'] ?? '',
            'SEO Keywords' => is_array($structuredData['secondary_keywords'] ?? null) ? implode(', ', $structuredData['secondary_keywords']) : ($structuredData['primary_keyword'] ?? '')
        );
    }
}

function applyContentToProduct()
{
    global $func, $contentEngine;

    $id = !empty($_POST['id']) ? (int)$_POST['id'] : (!empty($_GET['id']) ? (int)$_GET['id'] : 0);
    $adminUser = $_SESSION['login_admin']['username'] ?? 'admin';

    $res = $contentEngine->applyToProduct($id, $adminUser);

    if ($res['status']) {
        $func->transfer($res['message'], "index.php?com=ai_content&act=view&id=" . $id);
    } else {
        $func->transfer("Lỗi: " . $res['error'], "index.php?com=ai_content&act=diff&id=" . $id, false);
    }
}

function viewJobsMonitor()
{
    global $d, $func, $jobs, $paging;

    $curPage = !empty($_GET['p']) ? (int)$_GET['p'] : 1;
    $perPage = 20;
    $startIdx = ($curPage - 1) * $perPage;

    $where = "where 1=1";
    $params = array();
    if (!empty($_GET['status'])) {
        $where .= " and j.status = ?";
        $params[] = htmlspecialchars($_GET['status']);
    }

    $sql = "select j.*, p.namevi as product_name, p.photo as product_photo 
            from #_ai_content_job j 
            left join #_product p on j.id_product = p.id 
            {$where} order by j.id desc limit {$startIdx}, {$perPage}";
    $jobs = $d->rawQuery($sql, $params);

    $sqlTotal = "select count(id) as total from #_ai_content_job j {$where}";
    $totalCount = (int)$d->rawQueryOne($sqlTotal, $params)['total'];
    $paging = $func->pagination($totalCount, $perPage, $curPage, "index.php?com=ai_content&act=jobs" . $func->getFilterQuery());
}

function retryJobItem()
{
    global $func, $contentQueue;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    $contentQueue->retryJob($id);
    $contentQueue->executeJob($id);

    $func->transfer("Đã kích hoạt thử lại Job #{$id} thành công!", "index.php?com=ai_content&act=jobs");
}

function deleteJobItem()
{
    global $d, $func;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    $d->rawQuery("delete from #_ai_content_job where id = ?", array($id));

    $func->transfer("Đã xóa Job #{$id}", "index.php?com=ai_content&act=jobs");
}

function viewSettings()
{
    global $d, $aiConfig;

    $setting = $d->rawQueryOne("select options from #_setting limit 0,1");
    $opts = !empty($setting['options']) ? json_decode($setting['options'], true) : array();
    $aiConfig = $opts['ai_research_config'] ?? array();
}

function deleteContentItem()
{
    global $d, $func;

    $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
    $d->rawQuery("delete from #_ai_content where id = ?", array($id));

    $func->transfer("Đã xóa bản ghi nội dung AI", "index.php?com=ai_content&act=man");
}
