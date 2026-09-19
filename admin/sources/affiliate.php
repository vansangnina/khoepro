<?php
if (!defined('SOURCES')) die("Error");

/* Check act */
switch ($act) {
    case "man":
        viewClicks();
        $template = "affiliate/mans";
        break;
    default:
        $template = "404";
}

/* View click logs */
function viewClicks()
{
    global $d, $func, $curPage, $items, $paging, $countTotal, $summaryPlatform;

    $where = "1=1";
    $params = array();

    if (!empty($_GET['platform'])) {
        $platform = htmlspecialchars($_GET['platform']);
        $where .= " and c.platform = ?";
        $params[] = $platform;
    }

    if (!empty($_GET['source_page'])) {
        $source_page = htmlspecialchars($_GET['source_page']);
        $where .= " and c.source_page = ?";
        $params[] = $source_page;
    }

    if (!empty($_GET['keyword'])) {
        $keyword = htmlspecialchars($_GET['keyword']);
        $where .= " and (p.namevi LIKE ? or c.seller_name LIKE ? or c.platform LIKE ?)";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }

    $perPage = 20;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;

    $sql = "select c.*, p.namevi as product_name, p.photo as product_photo, p.slugvi as product_slug, a.seller_name, a.price as offer_price, a.affiliate_url 
            from #_affiliate_click c 
            left join #_product p on c.id_product = p.id 
            left join #_product_affiliate a on c.id_affiliate = a.id 
            where $where 
            order by c.id desc $limit";
    $items = $d->rawQuery($sql, $params);

    $sqlNum = "select count(c.id) as num 
               from #_affiliate_click c 
               left join #_product p on c.id_product = p.id 
               left join #_product_affiliate a on c.id_affiliate = a.id 
               where $where";
    $count = $d->rawQueryOne($sqlNum, $params);
    $total = (!empty($count)) ? $count['num'] : 0;
    $countTotal = $total;

    $url = "index.php?com=affiliate&act=man";
    if (!empty($_GET['platform'])) $url .= "&platform=" . htmlspecialchars($_GET['platform']);
    if (!empty($_GET['source_page'])) $url .= "&source_page=" . htmlspecialchars($_GET['source_page']);
    if (!empty($_GET['keyword'])) $url .= "&keyword=" . htmlspecialchars($_GET['keyword']);

    $paging = $func->pagination($total, $perPage, $curPage, $url);

    /* Summary by platform */
    $summaryPlatform = $d->rawQuery("select platform, count(id) as total_clicks from #_affiliate_click group by platform order by total_clicks desc");
}
