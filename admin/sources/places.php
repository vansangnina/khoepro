<?php
if (!defined('SOURCES')) die("Error");

/* Kiểm tra active places */
if (!isset($config['places']['active']) || $config['places']['active'] == false) $func->transfer(trangkhongtontai, "index.php", false);

/* Cấu hình đường dẫn trả về */
$strUrl = "";
$arrUrl = array('id_region', 'id_city', 'id_district', 'id_ward');
if (!empty($_POST['data'])) {
    $dataUrl = $_POST['data'];
    foreach ($arrUrl as $k => $v) {
        if (!empty($dataUrl[$arrUrl[$k]])) $strUrl .= "&" . $arrUrl[$k] . "=" . htmlspecialchars($dataUrl[$arrUrl[$k]]);
    }
} else {
    foreach ($arrUrl as $k => $v) {
        if (!empty($_REQUEST[$arrUrl[$k]])) $strUrl .= "&" . $arrUrl[$k] . "=" . htmlspecialchars($_REQUEST[$arrUrl[$k]]);
    }

    if (!empty($_REQUEST['keyword'])) $strUrl .= "&keyword=" . htmlspecialchars($_REQUEST['keyword']);
}

switch ($act) {
        /* City */
    case "man_city":
        viewCitys();
        $template = "places/city/mans";
        break;
    case "add_city":
        $template = "places/city/man_add";
        break;
    case "edit_city":
        editCity();
        $template = "places/city/man_add";
        break;
    case "save_city":
        saveCity();
        break;
    case "delete_city":
        deleteCity();
        break;

        /* District */
    case "man_district":
        viewDistricts();
        $template = "places/district/mans";
        break;
    case "add_district":
        $template = "places/district/man_add";
        break;
    case "edit_district":
        editDistrict();
        $template = "places/district/man_add";
        break;
    case "save_district":
        saveDistrict();
        break;
    case "delete_district":
        deleteDistrict();
        break;

        /* Ward */
    case "man_ward":
        viewWards();
        $template = "places/ward/mans";
        break;
    case "add_ward":
        $template = "places/ward/man_add";
        break;
    case "edit_ward":
        editWard();
        $template = "places/ward/man_add";
        break;
    case "save_ward":
        saveWard();
        break;
    case "delete_ward":
        deleteWard();
        break;

    default:
        $template = "404";
}

/* View city */
function viewCitys()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $items, $paging;

    $where = "";

    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (name LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_city where id<>0 $where order by numb,id asc $limit";
    $items = $d->rawQuery($sql);
    $sqlNum = "select count(*) as 'num' from #_city where id<>0 $where order by numb,id asc";
    $count = $d->rawQueryOne($sqlNum);
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=places" . $strUrl . "&act=man_city";
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit city */
function editCity()
{
    global $d, $langadmin, $strUrl, $func, $curPage, $item;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=places&act=man_city&p=" . $curPage . $strUrl, false);
    } else {
        $item = $d->rawQueryOne("select * from #_city where id = ? limit 0,1", array($id));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=places&act=man_city&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Save city */
function saveCity()
{
    global $d, $langadmin, $func, $flash, $strUrl, $curPage;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=places&act=man_city&p=" . $curPage . $strUrl, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            $data[$column] = htmlspecialchars($func->sanitize($value));
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        $data['slug'] = (!empty($data['name'])) ? $func->changeTitle($data['name']) : '';
    }

    /* Valid data */
    if (empty($data['name'])) {
        $response['messages'][] = tieudekhongduoctrong;
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id) {
            $func->redirect("index.php?com=places&act=edit_city&p=" . $curPage . $strUrl . "&id=" . $id);
        } else {
            $func->redirect("index.php?com=places&act=add_city&p=" . $curPage . $strUrl);
        }
    }

    /* Save data */
    if ($id) {
        $data['date_updated'] = time();

        $d->where('id', $id);
        if ($d->update('city', $data)) {
            $dataJson = array('type' => 'city');
            $func->writeJson($dataJson);
            $func->transfer(capnhatdulieuthanhcong, "index.php?com=places&act=man_city&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=places&act=man_city&p=" . $curPage . $strUrl, false);
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('city', $data)) {
            $dataJson = array('type' => 'city');
            $func->writeJson($dataJson);
            $func->transfer(luudulieuthanhcong, "index.php?com=places&act=man_city&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=places&act=man_city&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Delete city */
function deleteCity()
{
    global $d, $langadmin, $func, $strUrl, $curPage;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        $row = $d->rawQueryOne("select id from #_city where id = ? limit 0,1", array($id));

        if (!empty($row)) {
            $d->rawQuery("delete from #_city where id = ?", array($id));
            $dataJson = array('type' => 'city');
            $func->writeJson($dataJson);
            $func->transfer(xoadulieuthanhcong, "index.php?com=places&act=man_city&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=places&act=man_city&p=" . $curPage . $strUrl, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);
            $row = $d->rawQueryOne("select id from #_city where id = ? limit 0,1", array($id));

            if (!empty($row)) $d->rawQuery("delete from #_city where id = ?", array($id));
        }
        $dataJson = array('type' => 'city');
        $func->writeJson($dataJson);
        $func->transfer(xoadulieuthanhcong, "index.php?com=places&act=man_city&p=" . $curPage . $strUrl);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=places&act=man_city&p=" . $curPage . $strUrl, false);
    }
}

/* View district */
function viewDistricts()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $items, $paging;

    $where = "";

    $id_city = (isset($_REQUEST['id_city'])) ? htmlspecialchars($_REQUEST['id_city']) : 0;

    if (!empty($id_city)) $where .= " and id_city=$id_city";
    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (name LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_district where id<>0 $where order by numb,id asc $limit";
    $items = $d->rawQuery($sql);
    $sqlNum = "select count(*) as 'num' from #_district where id<>0 $where order by numb,id asc";
    $count = $d->rawQueryOne($sqlNum);
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=places" . $strUrl . "&act=man_district";
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit district */
function editDistrict()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $item;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=places&act=man_district&p=" . $curPage . $strUrl, false);
    } else {
        $item = $d->rawQueryOne("select * from #_district where id = ? limit 0,1", array($id));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=places&act=man_district&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Save district */
function saveDistrict()
{
    global $d, $langadmin, $func, $flash, $strUrl, $curPage;

    /* check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=places&act=man_district&p=" . $curPage . $strUrl, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            $data[$column] = htmlspecialchars($func->sanitize($value));
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        $data['slug'] = (!empty($data['name'])) ? $func->changeTitle($data['name']) : '';
    }

    /* Valid data */
    if (empty($data['id_city'])) {
        $response['messages'][] = chuachontinhthanhpho;
    }

    if (empty($data['name'])) {
        $response['messages'][] = tieudekhongduoctrong;
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id) {
            $func->redirect("index.php?com=places&act=edit_district&p=" . $curPage . $strUrl . "&id=" . $id);
        } else {
            $func->redirect("index.php?com=places&act=add_district&p=" . $curPage . $strUrl);
        }
    }

    /* Save data */
    if ($id) {
        $data['date_updated'] = time();

        $d->where('id', $id);
        if ($d->update('district', $data)) {
            $dataJson = array('type' => 'district','idCity'=>$data['id_city']);
            $func->writeJson($dataJson);
            $func->transfer(capnhatdulieuthanhcong, "index.php?com=places&act=man_district&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=places&act=man_district&p=" . $curPage . $strUrl, false);
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('district', $data)) {
            $dataJson = array('type' => 'district');
            $func->writeJson($dataJson);
            $func->transfer(luudulieuthanhcong, "index.php?com=places&act=man_district&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=places&act=man_district&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Delete district */
function deleteDistrict()
{
    global $d, $langadmin, $func, $strUrl, $curPage;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        $row = $d->rawQueryOne("select id from #_district where id = ? limit 0,1", array($id));

        if (!empty($row)) {
            $d->rawQuery("delete from #_district where id = ?", array($id));
            $dataJson = array('type' => 'district');
            $func->writeJson($dataJson);
            $func->transfer(xoadulieuthanhcong, "index.php?com=places&act=man_district&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=places&act=man_district&p=" . $curPage . $strUrl, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);
            $row = $d->rawQueryOne("select id from #_district where id = ? limit 0,1", array($id));

            if (!empty($row)) $d->rawQuery("delete from #_district where id = ?", array($id));
        }
        $dataJson = array('type' => 'district');
        $func->writeJson($dataJson);
        $func->transfer(xoadulieuthanhcong, "index.php?com=places&act=man_district&p=" . $curPage . $strUrl);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=places&act=man_district&p=" . $curPage . $strUrl, false);
    }
}

/* View ward */
function viewWards()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $items, $paging;

    $where = "";

    $id_city = (isset($_REQUEST['id_city'])) ? htmlspecialchars($_REQUEST['id_city']) : 0;
    $id_district = (isset($_REQUEST['id_district'])) ? htmlspecialchars($_REQUEST['id_district']) : 0;

    if (!empty($id_city)) $where .= " and id_city=$id_city";
    if (!empty($id_district)) $where .= " and id_district=$id_district";
    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (name LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_ward where id<>0 $where order by numb,id asc $limit";
    $items = $d->rawQuery($sql);
    $sqlNum = "select count(*) as 'num' from #_ward where id<>0 $where order by numb,id asc";
    $count = $d->rawQueryOne($sqlNum);
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=places" . $strUrl . "&act=man_ward";
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit ward */
function editWard()
{
    global $d, $langadmin, $func, $strUrl, $curPage, $item;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=places&act=man_ward&p=" . $curPage . $strUrl, false);
    } else {
        $item = $d->rawQueryOne("select * from #_ward where id = ? limit 0,1", array($id));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=places&act=man_ward&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Save ward */
function saveWard()
{
    global $d, $langadmin, $func, $flash, $strUrl, $curPage, $config;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=places&act=man_ward&p=" . $curPage . $strUrl, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            $data[$column] = htmlspecialchars($func->sanitize($value));
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        $data['slug'] = (!empty($data['name'])) ? $func->changeTitle($data['name']) : '';
        $data['ship_price'] = (!empty($data['ship_price'])) ? str_replace(",", "", $data['ship_price']) : 0;
    }

    /* Valid data */
    if (empty($data['id_city'])) {
        $response['messages'][] = chuachontinhthanhpho;
    }

    if (empty($data['id_district'])) {
        $response['messages'][] = chuachonquanhuyen;
    }

    if (empty($data['name'])) {
        $response['messages'][] = tieudekhongduoctrong;
    }

    if (!empty($data['ship_price']) && !$func->isNumber($data['ship_price'])) {
        $response['messages'][] = phivanchuyenkhonghople;
    }

    if (!empty($response)) {
        /* Flash data */
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);

        if ($id) {
            $func->redirect("index.php?com=places&act=edit_ward&p=" . $curPage . $strUrl . "&id=" . $id);
        } else {
            $func->redirect("index.php?com=places&act=add_ward&p=" . $curPage . $strUrl);
        }
    }

    /* Save data */
    if ($id) {
        $data['date_updated'] = time();

        $d->where('id', $id);
        if ($d->update('ward', $data)) {
            $dataJson = array('type' => 'wards','idCity'=>$data['id_city'],'idDistrict'=>$data['id_district']);
            $func->writeJson($dataJson);
            $func->transfer(capnhatdulieuthanhcong, "index.php?com=places&act=man_ward&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=places&act=man_ward&p=" . $curPage . $strUrl, false);
        }
    } else {
        $data['date_created'] = time();

        if ($d->insert('ward', $data)) {
            $dataJson = array('type' => 'wards');
            $func->writeJson($dataJson);
            $func->transfer(luudulieuthanhcong, "index.php?com=places&act=man_ward&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=places&act=man_ward&p=" . $curPage . $strUrl, false);
        }
    }
}

/* Delete ward */
function deleteWard()
{
    global $d, $langadmin, $func, $strUrl, $curPage;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        $row = $d->rawQueryOne("select id from #_ward where id = ? limit 0,1", array($id));

        if (!empty($row)) {
            $d->rawQuery("delete from #_ward where id = ?", array($id));
            $dataJson = array('type' => 'wards');
            $func->writeJson($dataJson);
            $func->transfer(xoadulieuthanhcong, "index.php?com=places&act=man_ward&p=" . $curPage . $strUrl);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=places&act=man_ward&p=" . $curPage . $strUrl, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);
            $row = $d->rawQueryOne("select id from #_ward where id = ? limit 0,1", array($id));

            if (!empty($row)) $d->rawQuery("delete from #_ward where id = ?", array($id));
        }
        $dataJson = array('type' => 'wards');
        $func->writeJson($dataJson);
        $func->transfer(xoadulieuthanhcong, "index.php?com=places&act=man_ward&p=" . $curPage . $strUrl);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=places&act=man_ward&p=" . $curPage . $strUrl, false);
    }
}
