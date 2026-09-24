<?php
if (!defined('SOURCES')) die("Error");

/* Check act */
switch ($act) {
    case "man":
        viewCandidates();
        $template = "product_research/mans";
        break;
    case "add":
        addCandidate();
        $template = "product_research/man_add";
        break;
    case "edit":
        editCandidate();
        $template = "product_research/man_add";
        break;
    case "save":
        saveCandidate();
        break;
    case "delete":
        deleteCandidate();
        break;
    case "approve":
        approveCandidate();
        break;
    case "reject":
        rejectCandidate();
        break;
    case "recalculate":
        recalculateCandidate();
        break;
    case "create_product":
        createProductView();
        $template = "product_research/create_product";
        break;
    case "save_product":
        saveProductCreation();
        break;
    case "weights":
        weightsView();
        $template = "product_research/weights";
        break;
    case "save_weights":
        saveWeights();
        break;

    /* Phase 04: Seeds Management */
    case "seeds":
        viewSeeds();
        $template = "product_research/seeds";
        break;
    case "add_seed":
        addSeed();
        $template = "product_research/seed_add";
        break;
    case "edit_seed":
        editSeed();
        $template = "product_research/seed_add";
        break;
    case "save_seed":
        saveSeed();
        break;
    case "delete_seed":
        deleteSeed();
        break;
    case "run_seed":
        runSeed();
        break;

    /* Phase 04: Jobs Management */
    case "jobs":
        viewJobs();
        $template = "product_research/jobs";
        break;
    case "retry_job":
        retryJobAction();
        break;

    /* Phase 04: AI & Provider Configuration */
    case "provider_config":
        providerConfigView();
        $template = "product_research/provider_config";
        break;
    case "save_provider_config":
        saveProviderConfig();
        break;

    /* Phase 10: Quick Import from ACCESSTRADE API */
    case "import_accesstrade":
        importFromAccessTradeAction();
        break;

    default:
        $template = "404";
}

/**
 * View Candidates List
 */
function viewCandidates()
{
    global $d, $func, $curPage, $items, $paging, $countTotal, $summaryStats;

    $where = "1=1";
    $params = array();

    // Filters
    if (!empty($_GET['platform'])) {
        $platform = htmlspecialchars($_GET['platform']);
        $where .= " and platform = ?";
        $params[] = $platform;
    }

    if (!empty($_GET['status'])) {
        $status = htmlspecialchars($_GET['status']);
        $where .= " and status = ?";
        $params[] = $status;
    }

    if (!empty($_GET['discovery_source'])) {
        $source = htmlspecialchars($_GET['discovery_source']);
        $where .= " and discovery_source = ?";
        $params[] = $source;
    }

    if (!empty($_GET['score_range'])) {
        $scoreRange = htmlspecialchars($_GET['score_range']);
        if ($scoreRange == '80-100') $where .= " and total_score >= 80";
        elseif ($scoreRange == '60-79') $where .= " and total_score >= 60 and total_score < 80";
        elseif ($scoreRange == '40-59') $where .= " and total_score >= 40 and total_score < 60";
        elseif ($scoreRange == '0-39') $where .= " and total_score < 40";
    }

    if (!empty($_GET['keyword'])) {
        $keyword = htmlspecialchars($_GET['keyword']);
        $where .= " and (name LIKE ? or category_hint LIKE ? or brand_hint LIKE ? or external_product_id LIKE ?)";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }

    // Sort
    $orderBy = "id desc";
    if (!empty($_GET['sort'])) {
        $sort = htmlspecialchars($_GET['sort']);
        if ($sort == 'score_desc') $orderBy = "total_score desc, id desc";
        elseif ($sort == 'score_asc') $orderBy = "total_score asc, id desc";
        elseif ($sort == 'sales_desc') $orderBy = "sales_count desc, id desc";
        elseif ($sort == 'commission_desc') $orderBy = "commission_rate desc, id desc";
        elseif ($sort == 'rating_desc') $orderBy = "rating desc, id desc";
        elseif ($sort == 'newest') $orderBy = "date_created desc, id desc";
    }

    $perPage = 20;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;

    $sql = "select * from #_product_research where $where order by $orderBy $limit";
    $items = $d->rawQuery($sql, $params);

    $sqlNum = "select count(id) as num from #_product_research where $where";
    $count = $d->rawQueryOne($sqlNum, $params);
    $total = (!empty($count)) ? $count['num'] : 0;
    $countTotal = $total;

    $url = "index.php?com=product_research&act=man";
    if (!empty($_GET['platform'])) $url .= "&platform=" . htmlspecialchars($_GET['platform']);
    if (!empty($_GET['status'])) $url .= "&status=" . htmlspecialchars($_GET['status']);
    if (!empty($_GET['discovery_source'])) $url .= "&discovery_source=" . htmlspecialchars($_GET['discovery_source']);
    if (!empty($_GET['score_range'])) $url .= "&score_range=" . htmlspecialchars($_GET['score_range']);
    if (!empty($_GET['sort'])) $url .= "&sort=" . htmlspecialchars($_GET['sort']);
    if (!empty($_GET['keyword'])) $url .= "&keyword=" . htmlspecialchars($_GET['keyword']);

    $paging = $func->pagination($total, $perPage, $curPage, $url);

    // Summary statistics for dashboard widgets
    $stats = array(
        'total' => 0,
        'discovered' => 0,
        'researched' => 0,
        'approved' => 0,
        'rejected' => 0,
        'product_created' => 0,
        'avg_score' => 0,
        'automated_total' => 0
    );

    $allStats = $d->rawQuery("select status, count(id) as total from #_product_research group by status");
    if (!empty($allStats)) {
        $totalSum = 0;
        foreach ($allStats as $st) {
            $totalSum += (int)$st['total'];
            $key = strtolower($st['status']);
            if (isset($stats[$key])) {
                $stats[$key] = (int)$st['total'];
            }
        }
        $stats['total'] = $totalSum;
    }

    $avgRow = $d->rawQueryOne("select avg(total_score) as avg_score from #_product_research where total_score is not null");
    if (!empty($avgRow['avg_score'])) {
        $stats['avg_score'] = round((float)$avgRow['avg_score'], 1);
    }

    $autoRow = $d->rawQueryOne("select count(id) as total from #_product_research where discovery_source != 'manual'");
    if (!empty($autoRow['total'])) {
        $stats['automated_total'] = (int)$autoRow['total'];
    }

    $summaryStats = $stats;
}

/**
 * Add Candidate View
 */
function addCandidate()
{
    global $d, $func, $item, $duplicateWarning;
    $item = array(
        'id' => 0,
        'name' => '',
        'category_hint' => '',
        'brand_hint' => '',
        'platform' => 'tiktok',
        'source_url' => '',
        'external_product_id' => '',
        'image_url' => '',
        'price' => '',
        'original_price' => '',
        'currency' => 'VND',
        'sales_count' => '',
        'rating' => '',
        'review_count' => '',
        'commission_rate' => '',
        'commission_value' => '',
        'estimated_gmv' => '',
        'creator_count' => '',
        'video_count' => '',
        'top_video_views' => '',
        'problem_solved' => '',
        'target_audience' => '',
        'research_notes' => '',
        'primary_keyword' => '',
        'competition_score' => '',
        'seo_score' => '',
        'status' => 'DISCOVERED',
        'discovery_source' => 'manual',
        'ai_analysis' => null
    );
    $duplicateWarning = null;
}

/**
 * Edit Candidate View (with Evidences & Snapshots)
 */
function editCandidate()
{
    global $d, $func, $item, $duplicateWarning, $linkedProduct, $evidences, $snapshots;

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) $func->transfer("Không tìm thấy dữ liệu", "index.php?com=product_research&act=man", false);

    $item = $d->rawQueryOne("select * from #_product_research where id = ? limit 0,1", array($id));
    if (empty($item)) $func->transfer("Không tìm thấy ứng viên nghiên cứu", "index.php?com=product_research&act=man", false);

    $research = new ProductResearch($d, $func);
    $duplicateWarning = $research->checkDuplicate(
        $item['platform'],
        $item['external_product_id'],
        $item['source_url'],
        $item['name'],
        $item['brand_hint'],
        $id
    );

    $linkedProduct = null;
    if (!empty($item['id_product'])) {
        $linkedProduct = $d->rawQueryOne("select id, namevi, slugvi, regular_price, status from #_product where id = ? limit 0,1", array((int)$item['id_product']));
    }

    // Load Phase 04 Evidences & Snapshots
    $evidences = $d->rawQuery("select * from #_product_research_evidence where id_research = ? order by id desc", array($id));
    $snapshots = $d->rawQuery("select * from #_product_research_snapshot where id_research = ? order by captured_at desc limit 0,10", array($id));
}

/**
 * Save Candidate Data
 */
function saveCandidate()
{
    global $d, $func;

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $research = new ProductResearch($d, $func);

    $name = !empty($_POST['data']['name']) ? htmlspecialchars(trim($_POST['data']['name'])) : '';
    $platform = !empty($_POST['data']['platform']) ? htmlspecialchars(trim($_POST['data']['platform'])) : 'manual';
    $sourceUrl = !empty($_POST['data']['source_url']) ? trim($_POST['data']['source_url']) : '';

    if (empty($name)) {
        $func->transfer("Vui lòng nhập tên sản phẩm", "index.php?com=product_research&act=add", false);
    }
    if (empty($sourceUrl)) {
        $func->transfer("Vui lòng nhập liên kết nguồn (Source URL)", "index.php?com=product_research&act=add", false);
    }

    $raw = $_POST['data'];
    $data = array();
    $data['name'] = $name;
    $data['normalized_name'] = $research->normalizeName($name);
    $data['platform'] = $platform;
    $data['source_url'] = $sourceUrl;
    $data['normalized_url'] = $research->normalizeUrl($sourceUrl);
    $data['external_product_id'] = !empty($raw['external_product_id']) ? htmlspecialchars(trim($raw['external_product_id'])) : null;
    $data['category_hint'] = !empty($raw['category_hint']) ? htmlspecialchars(trim($raw['category_hint'])) : null;
    $data['brand_hint'] = !empty($raw['brand_hint']) ? htmlspecialchars(trim($raw['brand_hint'])) : null;
    $data['image_url'] = !empty($raw['image_url']) ? trim($raw['image_url']) : null;
    $data['currency'] = !empty($raw['currency']) ? htmlspecialchars(trim($raw['currency'])) : 'VND';

    // Numbers & Metrics (Keep NULL if empty)
    $data['price'] = (isset($raw['price']) && $raw['price'] !== '') ? max(0, (float)$raw['price']) : null;
    $data['original_price'] = (isset($raw['original_price']) && $raw['original_price'] !== '') ? max(0, (float)$raw['original_price']) : null;
    $data['sales_count'] = (isset($raw['sales_count']) && $raw['sales_count'] !== '') ? max(0, (int)$raw['sales_count']) : null;
    $data['rating'] = (isset($raw['rating']) && $raw['rating'] !== '') ? min(5.0, max(0, (float)$raw['rating'])) : null;
    $data['review_count'] = (isset($raw['review_count']) && $raw['review_count'] !== '') ? max(0, (int)$raw['review_count']) : null;
    $data['commission_rate'] = (isset($raw['commission_rate']) && $raw['commission_rate'] !== '') ? max(0, (float)$raw['commission_rate']) : null;
    $data['commission_value'] = (isset($raw['commission_value']) && $raw['commission_value'] !== '') ? max(0, (float)$raw['commission_value']) : null;
    $data['estimated_gmv'] = (isset($raw['estimated_gmv']) && $raw['estimated_gmv'] !== '') ? max(0, (float)$raw['estimated_gmv']) : null;

    $data['creator_count'] = (isset($raw['creator_count']) && $raw['creator_count'] !== '') ? max(0, (int)$raw['creator_count']) : null;
    $data['video_count'] = (isset($raw['video_count']) && $raw['video_count'] !== '') ? max(0, (int)$raw['video_count']) : null;
    $data['top_video_views'] = (isset($raw['top_video_views']) && $raw['top_video_views'] !== '') ? max(0, (int)$raw['top_video_views']) : null;

    $data['problem_solved'] = !empty($raw['problem_solved']) ? trim($raw['problem_solved']) : null;
    $data['target_audience'] = !empty($raw['target_audience']) ? trim($raw['target_audience']) : null;
    $data['research_notes'] = !empty($raw['research_notes']) ? trim($raw['research_notes']) : null;
    $data['primary_keyword'] = !empty($raw['primary_keyword']) ? htmlspecialchars(trim($raw['primary_keyword'])) : null;

    $data['competition_score'] = (isset($raw['competition_score']) && $raw['competition_score'] !== '') ? min(100, max(0, (float)$raw['competition_score'])) : null;
    $data['seo_score'] = (isset($raw['seo_score']) && $raw['seo_score'] !== '') ? min(100, max(0, (float)$raw['seo_score'])) : null;

    // Calculate score
    $scoreResult = $research->calculateTotalScore($data);
    $data['demand_score'] = $scoreResult['demand_score'];
    $data['content_score'] = $scoreResult['content_score'];
    $data['commission_score'] = $scoreResult['commission_score'];
    $data['competition_score'] = $scoreResult['competition_score'];
    $data['seo_score'] = $scoreResult['seo_score'];
    $data['total_score'] = $scoreResult['total_score'];
    $data['score_breakdown'] = json_encode($scoreResult, JSON_UNESCAPED_UNICODE);

    // Status handling
    if (!empty($raw['status']) && in_array($raw['status'], array('DISCOVERED', 'RESEARCHED', 'APPROVED', 'REJECTED'))) {
        $data['status'] = $raw['status'];
    } elseif ($id === 0) {
        $data['status'] = 'DISCOVERED';
    }

    $timeNow = time();
    $data['date_updated'] = $timeNow;
    $data['last_seen_at'] = $timeNow;

    if ($id > 0) {
        $old = $d->rawQueryOne("select * from #_product_research where id = ? limit 0,1", array($id));
        $history = (!empty($old['history'])) ? json_decode($old['history'], true) : array();
        if (!is_array($history)) $history = array();
        $history[] = array('action' => 'UPDATED', 'time' => $timeNow, 'date' => date('Y-m-d H:i:s'));
        $data['history'] = json_encode($history, JSON_UNESCAPED_UNICODE);

        $d->where('id', $id);
        if ($d->update('product_research', $data)) {
            $func->transfer("Cập nhật ứng viên thành công", "index.php?com=product_research&act=edit&id=" . $id);
        } else {
            $func->transfer("Cập nhật thất bại", "index.php?com=product_research&act=edit&id=" . $id, false);
        }
    } else {
        $data['date_created'] = $timeNow;
        $data['first_seen_at'] = $timeNow;
        $data['discovery_source'] = 'manual';
        $history = array(array('action' => 'CREATED', 'time' => $timeNow, 'date' => date('Y-m-d H:i:s')));
        $data['history'] = json_encode($history, JSON_UNESCAPED_UNICODE);

        if ($newId = $d->insert('product_research', $data)) {
            $func->transfer("Thêm mới ứng viên thành công", "index.php?com=product_research&act=edit&id=" . $newId);
        } else {
            $func->transfer("Thêm mới thất bại", "index.php?com=product_research&act=add", false);
        }
    }
}

/**
 * Approve Candidate
 */
function approveCandidate()
{
    global $d, $func;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) $func->transfer("Không tìm thấy dữ liệu", "index.php?com=product_research&act=man", false);

    $old = $d->rawQueryOne("select * from #_product_research where id = ? limit 0,1", array($id));
    if (empty($old)) $func->transfer("Ứng viên không tồn tại", "index.php?com=product_research&act=man", false);

    $history = (!empty($old['history'])) ? json_decode($old['history'], true) : array();
    if (!is_array($history)) $history = array();
    $history[] = array('action' => 'APPROVED', 'time' => time(), 'date' => date('Y-m-d H:i:s'));

    $d->rawQuery("update #_product_research set status = 'APPROVED', history = ?, date_updated = ? where id = ?", array(
        json_encode($history, JSON_UNESCAPED_UNICODE),
        time(),
        $id
    ));

    $func->transfer("Đã duyệt ứng viên sang trạng thái APPROVED", "index.php?com=product_research&act=edit&id=" . $id);
}

/**
 * Reject Candidate
 */
function rejectCandidate()
{
    global $d, $func;
    $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
    $reason = isset($_POST['reject_reason']) ? htmlspecialchars(trim($_POST['reject_reason'])) : 'Không phù hợp tiêu chí FITNADO';

    if (!$id) $func->transfer("Không tìm thấy dữ liệu", "index.php?com=product_research&act=man", false);

    $old = $d->rawQueryOne("select * from #_product_research where id = ? limit 0,1", array($id));
    if (empty($old)) $func->transfer("Ứng viên không tồn tại", "index.php?com=product_research&act=man", false);

    $history = (!empty($old['history'])) ? json_decode($old['history'], true) : array();
    if (!is_array($history)) $history = array();
    $history[] = array('action' => 'REJECTED', 'reason' => $reason, 'time' => time(), 'date' => date('Y-m-d H:i:s'));

    $d->rawQuery("update #_product_research set status = 'REJECTED', reject_reason = ?, history = ?, date_updated = ? where id = ?", array(
        $reason,
        json_encode($history, JSON_UNESCAPED_UNICODE),
        time(),
        $id
    ));

    $func->transfer("Đã từ chối ứng viên (Lý do: $reason)", "index.php?com=product_research&act=man");
}

/**
 * Recalculate Candidate Score
 */
function recalculateCandidate()
{
    global $d, $func;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) $func->transfer("Không tìm thấy dữ liệu", "index.php?com=product_research&act=man", false);

    $item = $d->rawQueryOne("select * from #_product_research where id = ? limit 0,1", array($id));
    if (empty($item)) $func->transfer("Ứng viên không tồn tại", "index.php?com=product_research&act=man", false);

    $research = new ProductResearch($d, $func);
    $scoreResult = $research->calculateTotalScore($item);

    $history = (!empty($item['history'])) ? json_decode($item['history'], true) : array();
    if (!is_array($history)) $history = array();
    $history[] = array(
        'action' => 'SCORE_RECALCULATED',
        'old_score' => $item['total_score'],
        'new_score' => $scoreResult['total_score'],
        'time' => time(),
        'date' => date('Y-m-d H:i:s')
    );

    $d->rawQuery("update #_product_research set demand_score = ?, content_score = ?, commission_score = ?, competition_score = ?, seo_score = ?, total_score = ?, score_breakdown = ?, history = ?, date_updated = ? where id = ?", array(
        $scoreResult['demand_score'],
        $scoreResult['content_score'],
        $scoreResult['commission_score'],
        $scoreResult['competition_score'],
        $scoreResult['seo_score'],
        $scoreResult['total_score'],
        json_encode($scoreResult, JSON_UNESCAPED_UNICODE),
        json_encode($history, JSON_UNESCAPED_UNICODE),
        time(),
        $id
    ));

    $func->transfer("Đã tính toán lại điểm số: " . ($scoreResult['total_score'] ?? 'N/A') . "/100", "index.php?com=product_research&act=edit&id=" . $id);
}

/**
 * Delete Candidate
 */
function deleteCandidate()
{
    global $d, $func;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) $func->transfer("Không tìm thấy dữ liệu", "index.php?com=product_research&act=man", false);

    $item = $d->rawQueryOne("select id, id_product from #_product_research where id = ? limit 0,1", array($id));
    if (!empty($item['id_product'])) {
        $func->transfer("Không thể xóa ứng viên đã được liên kết với sản phẩm thật (ID Product #" . $item['id_product'] . ")", "index.php?com=product_research&act=man", false);
    }

    $d->rawQuery("delete from #_product_research where id = ?", array($id));
    $func->transfer("Xóa ứng viên thành công", "index.php?com=product_research&act=man");
}

/**
 * Create Product View (Mapping Screen)
 */
function createProductView()
{
    global $d, $func, $item, $categories, $brands, $duplicateWarning;

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) $func->transfer("Không tìm thấy dữ liệu", "index.php?com=product_research&act=man", false);

    $item = $d->rawQueryOne("select * from #_product_research where id = ? limit 0,1", array($id));
    if (empty($item)) $func->transfer("Ứng viên không tồn tại", "index.php?com=product_research&act=man", false);

    if (!empty($item['id_product'])) {
        $func->transfer("Ứng viên này đã được tạo sản phẩm (Product ID: #" . $item['id_product'] . ")", "index.php?com=product_research&act=edit&id=" . $id, false);
    }

    if ($item['status'] !== 'APPROVED') {
        $func->transfer("Ứng viên phải được duyệt (APPROVED) trước khi tạo sản phẩm", "index.php?com=product_research&act=edit&id=" . $id, false);
    }

    $categories = $d->rawQuery("select id, namevi from #_product_list where find_in_set('hienthi',status) order by numb, id desc");
    $brands = $d->rawQuery("select id, namevi from #_product_brand where find_in_set('hienthi',status) order by numb, id desc");

    $research = new ProductResearch($d, $func);
    $duplicateWarning = $research->checkDuplicate($item['platform'], $item['external_product_id'], $item['source_url'], $item['name'], $item['brand_hint'], $id);
}

/**
 * Save Product Creation from Candidate Mapping
 */
function saveProductCreation()
{
    global $d, $func;

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if (!$id) $func->transfer("Không tìm thấy dữ liệu", "index.php?com=product_research&act=man", false);

    $mappingData = array(
        'namevi' => !empty($_POST['namevi']) ? htmlspecialchars(trim($_POST['namevi'])) : '',
        'slugvi' => !empty($_POST['slugvi']) ? htmlspecialchars(trim($_POST['slugvi'])) : '',
        'id_list' => !empty($_POST['id_list']) ? (int)$_POST['id_list'] : 0,
        'id_brand' => !empty($_POST['id_brand']) ? (int)$_POST['id_brand'] : 0,
        'regular_price' => isset($_POST['regular_price']) ? (float)$_POST['regular_price'] : 0,
        'sale_price' => isset($_POST['sale_price']) ? (float)$_POST['sale_price'] : 0,
        'descvi' => !empty($_POST['descvi']) ? trim($_POST['descvi']) : '',
        'specs' => !empty($_POST['specs']) ? trim($_POST['specs']) : '',
        'affiliate_url' => !empty($_POST['affiliate_url']) ? trim($_POST['affiliate_url']) : ''
    );

    $research = new ProductResearch($d, $func);
    $res = $research->createProductFromCandidate($id, $mappingData);

    if ($res['status']) {
        $func->transfer($res['message'] . ". Sản phẩm được tạo ở chế độ NHÁP (chưa xuất bản). Vui lòng kiểm tra và duyệt hiển thị.", "index.php?com=product&act=edit&type=san-pham&id=" . $res['product_id']);
    } else {
        $func->transfer("Tạo sản phẩm thất bại: " . $res['message'], "index.php?com=product_research&act=create_product&id=" . $id, false);
    }
}

/**
 * Weights Configuration View
 */
function weightsView()
{
    global $d, $func, $activeWeights;
    $research = new ProductResearch($d, $func);
    $activeWeights = $research->getWeights();
}

/**
 * Save Weights Configuration
 */
function saveWeights()
{
    global $d, $func;

    $weights = array(
        'demand' => isset($_POST['demand']) ? (float)$_POST['demand'] : 0,
        'content' => isset($_POST['content']) ? (float)$_POST['content'] : 0,
        'commission' => isset($_POST['commission']) ? (float)$_POST['commission'] : 0,
        'competition' => isset($_POST['competition']) ? (float)$_POST['competition'] : 0,
        'seo' => isset($_POST['seo']) ? (float)$_POST['seo'] : 0
    );

    $research = new ProductResearch($d, $func);
    $res = $research->setWeights($weights);

    if ($res['status']) {
        $func->transfer($res['message'], "index.php?com=product_research&act=weights");
    } else {
        $func->transfer("Lỗi: " . $res['message'], "index.php?com=product_research&act=weights", false);
    }
}

/* ========================================================================= */
/* PHASE 04: SEEDS & AUTOMATED DISCOVERY ACTION HANDLERS                    */
/* ========================================================================= */

/**
 * View Research Seeds List
 */
function viewSeeds()
{
    global $d, $func, $curPage, $items, $paging, $countTotal;

    $where = "1=1";
    $params = array();

    if (!empty($_GET['status'])) {
        $where .= " and status = ?";
        $params[] = htmlspecialchars($_GET['status']);
    }

    if (!empty($_GET['seed_type'])) {
        $where .= " and seed_type = ?";
        $params[] = htmlspecialchars($_GET['seed_type']);
    }

    if (!empty($_GET['keyword'])) {
        $kw = htmlspecialchars($_GET['keyword']);
        $where .= " and (title LIKE ? or keyword LIKE ?)";
        $params[] = "%$kw%";
        $params[] = "%$kw%";
    }

    $perPage = 20;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;

    $items = $d->rawQuery("select * from #_product_research_seed where $where order by priority desc, id desc $limit", $params);
    $count = $d->rawQueryOne("select count(id) as num from #_product_research_seed where $where", $params);
    $total = (!empty($count)) ? $count['num'] : 0;
    $countTotal = $total;

    $url = "index.php?com=product_research&act=seeds";
    if (!empty($_GET['status'])) $url .= "&status=" . htmlspecialchars($_GET['status']);
    if (!empty($_GET['seed_type'])) $url .= "&seed_type=" . htmlspecialchars($_GET['seed_type']);
    if (!empty($_GET['keyword'])) $url .= "&keyword=" . htmlspecialchars($_GET['keyword']);

    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/**
 * Add Seed View
 */
function addSeed()
{
    global $d, $func, $item, $categories;
    $item = array(
        'id' => 0,
        'title' => '',
        'keyword' => '',
        'seed_type' => 'keyword',
        'category_id' => 0,
        'platform' => 'all',
        'priority' => 10,
        'depth' => 'STANDARD',
        'frequency' => 'manual',
        'max_results' => 10,
        'status' => 'active'
    );
    $categories = $d->rawQuery("select id, namevi from #_product_list where find_in_set('hienthi',status) order by numb, id desc");
}

/**
 * Edit Seed View
 */
function editSeed()
{
    global $d, $func, $item, $categories;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) $func->transfer("Không tìm thấy dữ liệu", "index.php?com=product_research&act=seeds", false);

    $item = $d->rawQueryOne("select * from #_product_research_seed where id = ? limit 0,1", array($id));
    if (empty($item)) $func->transfer("Hạt giống nghiên cứu không tồn tại", "index.php?com=product_research&act=seeds", false);

    $categories = $d->rawQuery("select id, namevi from #_product_list where find_in_set('hienthi',status) order by numb, id desc");
}

/**
 * Save Seed Data
 */
function saveSeed()
{
    global $d, $func;

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = !empty($_POST['data']['title']) ? htmlspecialchars(trim($_POST['data']['title'])) : '';
    $keyword = !empty($_POST['data']['keyword']) ? htmlspecialchars(trim($_POST['data']['keyword'])) : '';

    if (empty($title) || empty($keyword)) {
        $func->transfer("Vui lòng nhập tiêu đề và từ khóa nghiên cứu", "index.php?com=product_research&act=add_seed", false);
    }

    $data = array(
        'title' => $title,
        'keyword' => $keyword,
        'seed_type' => !empty($_POST['data']['seed_type']) ? htmlspecialchars($_POST['data']['seed_type']) : 'keyword',
        'category_id' => !empty($_POST['data']['category_id']) ? (int)$_POST['data']['category_id'] : 0,
        'platform' => !empty($_POST['data']['platform']) ? htmlspecialchars($_POST['data']['platform']) : 'all',
        'priority' => isset($_POST['data']['priority']) ? (int)$_POST['data']['priority'] : 10,
        'depth' => !empty($_POST['data']['depth']) ? htmlspecialchars($_POST['data']['depth']) : 'STANDARD',
        'frequency' => !empty($_POST['data']['frequency']) ? htmlspecialchars($_POST['data']['frequency']) : 'manual',
        'max_results' => isset($_POST['data']['max_results']) ? max(1, min(50, (int)$_POST['data']['max_results'])) : 10,
        'status' => !empty($_POST['data']['status']) ? htmlspecialchars($_POST['data']['status']) : 'active',
        'date_updated' => time()
    );

    if ($id > 0) {
        $d->where('id', $id);
        if ($d->update('product_research_seed', $data)) {
            $func->transfer("Cập nhật hạt giống nghiên cứu thành công", "index.php?com=product_research&act=seeds");
        } else {
            $func->transfer("Cập nhật thất bại", "index.php?com=product_research&act=edit_seed&id=" . $id, false);
        }
    } else {
        $data['date_created'] = time();
        if ($newId = $d->insert('product_research_seed', $data)) {
            $func->transfer("Thêm mới hạt giống thành công", "index.php?com=product_research&act=seeds");
        } else {
            $func->transfer("Thêm mới thất bại", "index.php?com=product_research&act=add_seed", false);
        }
    }
}

/**
 * Delete Seed
 */
function deleteSeed()
{
    global $d, $func;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) $func->transfer("Không tìm thấy dữ liệu", "index.php?com=product_research&act=seeds", false);

    $d->rawQuery("delete from #_product_research_seed where id = ?", array($id));
    $func->transfer("Xóa hạt giống nghiên cứu thành công", "index.php?com=product_research&act=seeds");
}

/**
 * Run Seed Now (Enqueue Job and execute immediate first pass)
 */
function runSeed()
{
    global $d, $func;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) $func->transfer("Không tìm thấy dữ liệu", "index.php?com=product_research&act=seeds", false);

    $seed = $d->rawQueryOne("select * from #_product_research_seed where id = ? limit 0,1", array($id));
    if (empty($seed)) $func->transfer("Hạt giống không tồn tại", "index.php?com=product_research&act=seeds", false);

    $queue = new ResearchJobQueue($d, $func);
    $jobId = $queue->createJob($id, $seed['platform'] ?: 'ai_agent', $seed['depth'] ?: 'STANDARD');

    if ($jobId) {
        // Execute the job immediately
        $res = $queue->executeJob($jobId);
        if ($res['status']) {
            $func->transfer("Tác vụ nghiên cứu #{$jobId} hoàn tất! Tìm thấy {$res['candidates_found']} sản phẩm, Thêm mới: {$res['candidates_created']}, Trùng lặp: {$res['duplicates_count']}", "index.php?com=product_research&act=jobs");
        } else {
            $func->transfer("Tác vụ #{$jobId} đã đưa vào hàng đợi nhưng gặp lỗi: " . ($res['error'] ?? ''), "index.php?com=product_research&act=jobs", false);
        }
    } else {
        $func->transfer("Không thể tạo tác vụ nghiên cứu", "index.php?com=product_research&act=seeds", false);
    }
}

/**
 * View Jobs Queue
 */
function viewJobs()
{
    global $d, $func, $curPage, $items, $paging, $countTotal, $jobStats;

    $where = "1=1";
    $params = array();

    if (!empty($_GET['status'])) {
        $where .= " and j.status = ?";
        $params[] = htmlspecialchars($_GET['status']);
    }

    if (!empty($_GET['provider'])) {
        $where .= " and j.provider = ?";
        $params[] = htmlspecialchars($_GET['provider']);
    }

    $perPage = 20;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;

    $sql = "select j.*, s.title as seed_title, s.keyword as seed_keyword 
            from #_product_research_job j 
            left join #_product_research_seed s on j.id_seed = s.id 
            where $where 
            order by j.id desc $limit";
    $items = $d->rawQuery($sql, $params);

    $count = $d->rawQueryOne("select count(id) as num from #_product_research_job j where $where", $params);
    $total = (!empty($count)) ? $count['num'] : 0;
    $countTotal = $total;

    $url = "index.php?com=product_research&act=jobs";
    if (!empty($_GET['status'])) $url .= "&status=" . htmlspecialchars($_GET['status']);
    if (!empty($_GET['provider'])) $url .= "&provider=" . htmlspecialchars($_GET['provider']);

    $paging = $func->pagination($total, $perPage, $curPage, $url);

    // Job stats
    $jobStats = array('total' => 0, 'pending' => 0, 'running' => 0, 'success' => 0, 'failed' => 0);
    $statsRows = $d->rawQuery("select status, count(id) as cnt from #_product_research_job group by status");
    if (!empty($statsRows)) {
        $totalJ = 0;
        foreach ($statsRows as $sr) {
            $totalJ += (int)$sr['cnt'];
            $k = strtolower($sr['status']);
            if (isset($jobStats[$k])) $jobStats[$k] = (int)$sr['cnt'];
        }
        $jobStats['total'] = $totalJ;
    }
}

/**
 * Retry Job
 */
function retryJobAction()
{
    global $d, $func;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) $func->transfer("Không tìm thấy dữ liệu", "index.php?com=product_research&act=jobs", false);

    $queue = new ResearchJobQueue($d, $func);
    $queue->retryJob($id);

    // Run retry immediately
    $res = $queue->executeJob($id);
    if ($res['status']) {
        $func->transfer("Chạy lại tác vụ #{$id} thành công!", "index.php?com=product_research&act=jobs");
    } else {
        $func->transfer("Chạy lại tác vụ #{$id} thất bại: " . ($res['error'] ?? ''), "index.php?com=product_research&act=jobs", false);
    }
}

/**
 * AI & Provider Configuration View
 */
function providerConfigView()
{
    global $d, $func, $aiConfig;
    $agent = new AIResearchAgent($d, $func);
    $aiConfig = $agent->getAiConfig();
}

/**
 * Save Provider Configuration
 */
function saveProviderConfig()
{
    global $d, $func;

    $agent = new AIResearchAgent($d, $func);
    $config = array(
        'active_provider' => !empty($_POST['active_provider']) ? htmlspecialchars($_POST['active_provider']) : 'mock',
        'gemini_api_key' => !empty($_POST['gemini_api_key']) ? trim($_POST['gemini_api_key']) : '',
        'gemini_model' => !empty($_POST['gemini_model']) ? trim($_POST['gemini_model']) : 'gemini-1.5-flash',
        'openai_api_key' => !empty($_POST['openai_api_key']) ? trim($_POST['openai_api_key']) : '',
        'openai_model' => !empty($_POST['openai_model']) ? trim($_POST['openai_model']) : 'gpt-4o-mini',
        'daily_request_limit' => isset($_POST['daily_request_limit']) ? max(5, (int)$_POST['daily_request_limit']) : 50,
        'default_depth' => !empty($_POST['default_depth']) ? htmlspecialchars($_POST['default_depth']) : 'STANDARD'
    );

    $agent->saveAiConfig($config);
    $func->transfer("Lưu cấu hình AI & Nhà cung cấp nghiên cứu thành công!", "index.php?com=product_research&act=provider_config");
}

/**
 * Quick Auto-Import Products from ACCESSTRADE Live API
 */
function importFromAccessTradeAction()
{
    global $d, $func;

    @set_time_limit(30);

    $keyword = !empty($_POST['keyword']) ? trim($_POST['keyword']) : (!empty($_GET['keyword']) ? trim($_GET['keyword']) : 'gym');
    $limit = !empty($_POST['limit']) ? min(50, max(1, (int)$_POST['limit'])) : 10;
    $filterRelevance = !empty($_POST['filter_relevance']) ? (bool)$_POST['filter_relevance'] : false;

    if (empty($keyword)) {
        $func->transfer("Vui lòng nhập từ khóa tìm kiếm sản phẩm ACCESSTRADE", "index.php?com=product_research&act=man", false);
    }

    require_once LIBRARIES . 'class/class.ResearchProvider.php';
    $atProvider = ResearchProviderFactory::create('accesstrade', $d, $func);
    $candidates = $atProvider->discoverCandidates(array(
        'keyword' => $keyword,
        'limit' => $limit,
        'filter_relevance' => $filterRelevance
    ));

    if (empty($candidates)) {
        $func->transfer("Không tìm thấy sản phẩm nào khớp với từ khóa '{$keyword}' từ ACCESSTRADE API (hoặc đã bị bộ lọc loại trừ). Vui lòng thử lại với từ khóa khác hoặc tắt bộ lọc ngành.", "index.php?com=product_research&act=man", false);
    }

    $research = new ProductResearch($d, $func);
    $createdCount = 0;
    $dupCount = 0;

    foreach ($candidates as $dto) {
        $normUrl = $research->normalizeUrl($dto->source_url);
        $normName = $research->normalizeName($dto->name);
        $extId = $dto->external_product_id;

        $dupCheck = $research->checkDuplicate('accesstrade', $extId, $dto->source_url, $dto->name, $dto->brand_hint);
        if ($dupCheck['is_duplicate']) {
            $dupCount++;
            continue;
        }

        $cArray = $dto->toArray();
        $cArray['normalized_name'] = $normName;
        $cArray['normalized_url'] = $normUrl;
        $cArray['status'] = 'DISCOVERED';
        $cArray['currency'] = 'VND';
        $cArray['date_created'] = time();
        $cArray['date_updated'] = time();

        $scoreRes = $research->calculateTotalScore($cArray);
        $cArray['demand_score'] = $scoreRes['demand_score'];
        $cArray['content_score'] = $scoreRes['content_score'];
        $cArray['commission_score'] = $scoreRes['commission_score'];
        $cArray['competition_score'] = $scoreRes['competition_score'];
        $cArray['seo_score'] = $scoreRes['seo_score'];
        $cArray['total_score'] = $scoreRes['total_score'];
        $cArray['score_breakdown'] = json_encode($scoreRes, JSON_UNESCAPED_UNICODE);

        $newId = $d->insert('product_research', $cArray);
        if ($newId) {
            $createdCount++;
        }
    }

    $msg = "Đã kéo thành công {$createdCount} sản phẩm từ ACCESSTRADE Live API (Trùng lặp bỏ qua: {$dupCount})!";
    $func->transfer($msg, "index.php?com=product_research&act=man");
}

