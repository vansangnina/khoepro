<?php
if (!defined('SOURCES')) die("Error");

/* Check access user */
if (!empty($act) && !in_array($act, array('login', 'info_admin')) && in_array($act, array('man_admin', 'add_admin', 'edit_admin', 'delete_admin', 'man_member', 'add_member', 'edit_member', 'delete_member', 'permission_group', 'add_permission_group', 'edit_permission_group', 'delete_permission_group')) && $func->checkRole()) {
    $func->transfer(bankhongcoquyentruycapvaokhuvucnay, "index.php", false);
    exit;
}

switch ($act) {
        /* Admins */
    case "login":
        if (!empty($_SESSION[$loginAdmin]['active'])) $func->transfer(trangkhongtontai, "index.php", false);
        else $template = "user/login";
        break;
    case "logout":
        logout();
        break;
    case "man_admin":
        viewAdmins();
        $template = "user/man_admin/mans";
        break;
    case "add_admin":
        $template = "user/man_admin/man_add";
        break;
    case "edit_admin":
        editAdmin();
        $template = "user/man_admin/man_add";
        break;
    case "info_admin":
        infoAdmin();
        $template = "user/man_admin/info";
        break;
    case "save_admin":
        saveAdmin();
        break;
    case "delete_admin":
        deleteAdmin();
        break;

        /* Members */
    case "man_member":
        viewMembers();
        $template = "user/man_member/mans";
        break;
    case "add_member":
        $template = "user/man_member/man_add";
        break;
    case "edit_member":
        editMember();
        $template = "user/man_member/man_add";
        break;
    case "save_member":
        saveMember();
        break;
    case "delete_member":
        deleteMember();
        break;

        /* Permission */
    case "permission_group":
        viewPermissionGroups();
        $template = "user/permission/permission_groups";
        break;
    case "add_permission_group":
        $template = "user/permission/permission_group";
        break;
    case "edit_permission_group":
        editPermissionGroup();
        $template = "user/permission/permission_group";
        break;
    case "save_permission_group":
        savePermissionGroup();
        break;
    case "delete_permission_group":
        deletePermissionGroup();
        break;

    default:
        $template = "404";
}

/* View permission */
function viewPermissionGroups()
{
    global $d, $langadmin, $func, $curPage, $items, $paging;

    $where = "";

    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and fullname LIKE '%$keyword%'";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_permission_group where id<>0 $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql);
    $sqlNum = "select count(*) as 'num' from #_permission_group where id<>0 $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum);
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=user&act=permission_group";
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit permission */
function editPermissionGroup()
{
    global $d, $langadmin, $func, $curPage, $item, $lists_permission;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        /* Lấy nhóm quyền */
        $item = $d->rawQueryOne("select * from #_permission_group where id = ? limit 0,1", array($id));

        if (empty($item)) {
            $func->transfer(nhomquyennaykhongtontai, "index.php?com=user&act=permission_group&p=" . $curPage, false);
        }

        /* Lấy quyền */
        $arr = $d->rawQuery("select permission from #_permission where id_permission_group = ?", array($id));

        if (!empty($arr)) {
            foreach ($arr as $permission) {
                $lists_permission[] = $permission['permission'];
            }
        } else {
            $lists_permission[] = array();
        }
    } else {
        $func->transfer(nhomquyennaykhongtontai, "index.php?com=user&act=permission_group&p=" . $curPage, false);
    }
}

/* Save permission */
function savePermissionGroup()
{
    global $d, $langadmin, $func, $curPage;

    /* Post dữ liệu */
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    $dataPermission = (!empty($_POST['dataPermission'])) ? $_POST['dataPermission'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            $data[$column] = htmlspecialchars($value);
        }
    }

    if (isset($_POST['status'])) {
        $status = '';
        foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
        $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
    } else {
        $data['status'] = "";
    }

    if ($id) {
        /* Kiểm tra nhóm quyền */
        $row = $d->rawQueryOne("select id from #_permission_group where id = ? limit 0,1", array($id));
        if (empty($row)) {
            $func->transfer(nhomquyennaykhongtontai, "index.php?com=user&act=permission_group&p=" . $curPage, false);
        }

        /* Xử lý tham số bắt buộc */
        if (empty($_POST['dataPermission'])) {
            $func->transfer(vuilongchonquyenchonhomnay, "index.php?com=user&act=edit_permission_group&id=" . $id . "&p=" . $curPage, false);
        }

        /* Cập nhật thông tin nhóm quyền */
        $data['date_updated'] = time();
        $d->where('id', $id);
        $d->update('permission_group', $data);

        /* Xóa hết các quyên hiện tại */
        $d->rawQuery("delete from #_permission where id_permission_group = ?", array($id));

        /* Thêm các quyền mới vào */
        if ($dataPermission) {
            for ($i = 0; $i < count($dataPermission); $i++) {
                $data_permission['id_permission_group'] = $id;
                $data_permission['permission'] = $dataPermission[$i];
                $d->insert('permission', $data_permission);
            }

            $func->transfer(capnhatnhomquyenthanhcong, "index.php?com=user&act=permission_group&p=" . $curPage);
        } else {
            $func->transfer(capnhatnhomquyenthatbai, "index.php?com=user&act=permission_group&p=" . $curPage);
        }
    } else {
        /* Xử lý tham số bắt buộc */
        if (empty($_POST['dataPermission'])) {
            $func->transfer(vuilongchonquyenchonhomnay, "index.php?com=user&act=add_permission_group&p=" . $curPage, false);
        }

        /* Lưu thông tin nhóm quyền */
        $data['date_created'] = time();
        $d->insert('permission_group', $data);

        /* Lưu quyền cho nhóm quyền */
        if ($dataPermission) {
            $id_permission = $d->getLastInsertId();

            for ($i = 0; $i < count($dataPermission); $i++) {
                $data_permission['id_permission_group'] = $id_permission;
                $data_permission['permission'] = $dataPermission[$i];
                $d->insert('permission', $data_permission);
            }

            $func->transfer(taonhomquyenthanhcong, "index.php?com=user&act=permission_group&p=" . $curPage);
        } else {
            $func->transfer(taonhomquyenthatbai, "index.php?com=user&act=permission_group&p=" . $curPage);
        }
    }
}

/* Delete permission */
function deletePermissionGroup()
{
    global $d, $langadmin, $func, $curPage;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        $row = $d->rawQuery("select * from #_permission_group where id = ?", array($id));

        if (count($row)) {
            $d->rawQuery("delete from #_permission_group where id = ?", array($id));
            $row = $d->rawQuery("select * from #_permission where id_permission_group = ?", array($id));
            if (count($row)) $d->rawQuery("delete from #_permission where id_permission_group = ?", array($id));
            $row = $d->rawQuery("select * from #_user where id_permission = ?", array($id));

            if (count($row)) {
                $data_user['id_permission'] = 0;
                $d->where('id_permission', $id);
                $d->update('user', $data_user);
            }

            $func->transfer(xoadulieuthanhcong, "index.php?com=user&act=permission_group&p=" . $curPage);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=user&act=permission_group&p=" . $curPage, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);
            $row = $d->rawQuery("select * from #_permission_group where id = ?", array($id));

            if (count($row)) {
                $d->rawQuery("delete from #_permission_group where id = ?", array($id));
                $row = $d->rawQuery("select * from #_permission where id_permission_group = ?", array($id));
                if (count($row)) $d->rawQuery("delete from #_permission where id_permission_group = ?", array($id));
                $row = $d->rawQuery("select * from #_user where id_permission = ?", array($id));

                if (count($row)) {
                    $data_user['id_permission'] = 0;
                    $d->where('id_permission', $id);
                    $d->update('user', $data_user);
                }
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=user&act=permission_group&p=" . $curPage);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=user&act=permission_group&p=" . $curPage, false);
    }
}

/* View admin */
function viewAdmins()
{
    global $d, $langadmin, $func, $curPage, $items, $paging, $config;

    $where = "";

    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (username LIKE '%$keyword%' or fullname LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_user where id <> 0 and role = 1 $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql);
    $sqlNum = "select count(*) as 'num' from #_user where id <> 0 and role = 1 $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum);
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=user&act=man_admin";
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit admin */
function editAdmin()
{
    global $d, $langadmin, $func, $curPage, $item;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=user&act=man_admin&p=" . $curPage, false);
    } else {
        $item = $d->rawQueryOne("select * from #_user where role = 1 and id = ? limit 0,1", array($id));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=user&act=man_admin&p=" . $curPage, false);
        }
    }
}

/* Info admin */
function infoAdmin()
{
    global $d, $langadmin, $func, $flash, $curPage, $item, $config, $loginAdmin;

    /* Check change password */
    if (!empty($_GET['changepass']) && ($_GET['changepass'] == 1)) {
        $changepass = true;
    } else {
        $changepass = false;
    }

    if (!empty($_POST)) {
        /* Post dữ liệu */
        $message = '';
        $response = array();
        $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
        $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
        if ($data) {
            foreach ($data as $column => $value) {
                $data[$column] = htmlspecialchars($func->sanitize($value));
            }
        }

        if (!empty($changepass)) {
            $old_pass = (!empty($_POST['old-password'])) ? $_POST['old-password'] : '';
            $new_pass = (!empty($_POST['new-password'])) ? $_POST['new-password'] : '';
            $renew_pass = (!empty($_POST['renew-password'])) ? $_POST['renew-password'] : '';

            /* Valid data */
            if (empty($old_pass)) {
                $response['messages'][] = 'Mật khẩu cũ không được trống';
            }

            if (!empty($old_pass)) {
                $row = $d->rawQueryOne("select id, password from #_user where username = ? limit 0,1", array($_SESSION[$loginAdmin]['username']));

                if (empty($row['id']) || (!empty($row['id']) && ($row['password'] != md5($config['website']['secret'] . $old_pass . $config['website']['salt'])))) {
                    $response['messages'][] = matkhaucukhongchinhxac;
                }
            }

            if (empty($new_pass)) {
                $response['messages'][] = matkhaumoikhongduoctrong;
            }

            if (!empty($new_pass) && in_array($new_pass, array('123', '123qwe', '123456', 'ninaco'))) {
                $response['messages'][] = matkhaubandatquadongian;
            }

            if (!empty($new_pass) && empty($renew_pass)) {
                $response['messages'][] = xacnhanmatkhaumoikhongduoctrong;
            }

            if (!empty($new_pass) && !empty($renew_pass) && !$func->isMatch($new_pass, $renew_pass)) {
                $response['messages'][] = matkhaumoikhongtrungkhop;
            }

            if (!empty($response)) {
                $response['status'] = 'danger';
                $message = base64_encode(json_encode($response));
                $flash->set('message', $message);
                $func->redirect("index.php?com=user&act=info_admin&changepass=1");
            }

            /* Change to new password */
            $data['password'] = md5($config['website']['secret'] . $new_pass . $config['website']['salt']);
            $flagchangepass = true;
        } else {
            $birthday = $data['birthday'];
            $data['birthday'] = strtotime(str_replace("/", "-", $data['birthday']));

            /* Valid data */
            if (empty($data['username'])) {
                $response['messages'][] = taikhoankhongduoctrong;
            }

            if (!empty($data['username']) && !$func->isAlphaNum($data['username'])) {
                $response['messages'][] = taikhoanchiduocnhapchuthuongvaso;
            }

            if (!empty($data['username'])) {
                if ($func->checkAccount($data['username'], 'username', 'user', $id)) {
                    $response['messages'][] = taikhoandatontai;
                }
            }

            if (empty($data['fullname'])) {
                $response['messages'][] = hotenkhongthetrong;
            }

            if (empty($data['email'])) {
                $response['messages'][] = emailkhongduoctrong;
            }

            if (!empty($data['email']) && !$func->isEmail($data['email'])) {
                $response['messages'][] = emailkhonghople;
            }

            if (!empty($data['email'])) {
                if ($func->checkAccount($data['email'], 'email', 'user', $id)) {
                    $response['messages'][] = emaildatontai;
                }
            }

            if (!empty($data['phone']) && !$func->isPhone($data['phone'])) {
                $response['messages'][] = sodienthoaikhonghople;
            }

            if (empty($data['gender'])) {
                $response['messages'][] = chuachongioitinh;
            }

            if (empty($birthday)) {
                $response['messages'][] = ngaysinhkhongduoctrong;
            }

            if (!empty($birthday) && !$func->isDate($birthday)) {
                $response['messages'][] = ngaysinhkhonghople;
            }

            if (empty($data['address'])) {
                $response['messages'][] = diachikhongduoctrong;
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
                $func->redirect("index.php?com=user&act=info_admin");
            }
        }

        /* Save data */
        $d->where('username', $_SESSION[$loginAdmin]['username']);
        if ($d->update('user', $data)) {
            if (isset($flagchangepass) && $flagchangepass == true) {
                if (!empty($_SESSION[TOKEN])) unset($_SESSION[TOKEN]);
                unset($_SESSION[$loginAdmin]);
                $func->transfer(capnhatdulieuthanhcong, "index.php");
            }

            $func->transfer(capnhatdulieuthanhcong, "index.php?com=user&act=info_admin");
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=user&act=info_admin");
        }
    }

    $item = $d->rawQueryOne("select * from #_user where username = ? limit 0,1", array($_SESSION[$loginAdmin]['username']));
}

/* Save admin */
function saveAdmin()
{
    global $d, $langadmin, $func, $flash, $curPage, $config;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=user&act=man_admin&p=" . $curPage, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            $data[$column] = ($column == 'password') ? $value : htmlspecialchars($func->sanitize($value));
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        $data['role'] = 1;
        $birthday = $data['birthday'];
        $data['birthday'] = strtotime(str_replace("/", "-", $data['birthday']));
    }

    /* Valid data */
    if (empty($data['id_permission'])) {
        $response['messages'][] = chuachonnhomquyen;
    }

    if (empty($data['username'])) {
        $response['messages'][] = taikhoankhongduoctrong;
    }

    if (!empty($data['username']) && !$func->isAlphaNum($data['username'])) {
        $response['messages'][] = taikhoanchiduocnhapchuthuongvaso;
    }

    if (!empty($data['username'])) {
        if ($func->checkAccount($data['username'], 'username', 'user', $id)) {
            $response['messages'][] = taikhoandatontai;
        }
    }

    if (empty($id) || !empty($data['password'])) {
        if (empty($data['password'])) {
            $response['messages'][] = matkhaukhongduoctrong;
        }

        if (!empty($data['password']) && empty($_POST['confirm_password'])) {
            $response['messages'][] = xacnhanmatkhaukhongduoctrong;
        }

        if (!empty($data['password']) && !empty($_POST['confirm_password']) && !$func->isMatch($data['password'], $_POST['confirm_password'])) {
            $response['messages'][] = matkhaukhongtrungkhop;
        }
    }

    if (empty($data['fullname'])) {
        $response['messages'][] = hotenkhongthetrong;
    }

    if (empty($data['email'])) {
        $response['messages'][] = emailkhongduoctrong;
    }

    if (!empty($data['email']) && !$func->isEmail($data['email'])) {
        $response['messages'][] = emailkhonghople;
    }

    if (!empty($data['email'])) {
        if ($func->checkAccount($data['email'], 'email', 'user', $id)) {
            $response['messages'][] = emaildatontai;
        }
    }

    if (!empty($data['phone']) && !$func->isPhone($data['phone'])) {
        $response['messages'][] = sodienthoaikhonghople;
    }

    if (empty($data['gender'])) {
        $response['messages'][] = chuachongioitinh;
    }

    if (empty($birthday)) {
        $response['messages'][] = ngaysinhkhongduoctrong;
    }

    if (!empty($birthday) && !$func->isDate($birthday)) {
        $response['messages'][] = ngaysinhkhonghople;
    }

    if (empty($data['address'])) {
        $response['messages'][] = diachikhongduoctrong;
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

        if (empty($id)) {
            $func->redirect("index.php?com=user&act=add_admin");
        } else {
            $func->redirect("index.php?com=user&act=edit_admin&id=" . $id);
        }
    }

    /* Save data */
    if ($id) {
        if ($func->checkRole()) {
            $row = $d->rawQueryOne("select id from #_user where id = ? limit 0,1", array($id));

            if (!empty($row)) {
                $func->transfer(bankhongcoquyentrentaikhoannaymoithacmacxinlienhequantriwebsite, "index.php?com=user&act=man_admin&p=" . $curPage, false);
            }
        }

        if (!empty($data['password'])) {
            $password = $data['password'];
            $confirm_password = !empty($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            $data['password'] = md5($config['website']['secret'] . $password . $config['website']['salt']);
        } else {
            unset($data['password']);
        }

        $d->where('id', $id);
        if ($d->update('user', $data)) {
            $func->transfer(capnhatdulieuthanhcong, "index.php?com=user&act=man_admin&p=" . $curPage);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=user&act=man_admin&p=" . $curPage, false);
        }
    } else {
        if (!empty($data['password'])) {
            $data['password'] = md5($config['website']['secret'] . $data['password'] . $config['website']['salt']);
        }

        if ($d->insert('user', $data)) {
            $func->transfer(luudulieuthanhcong, "index.php?com=user&act=man_admin&p=" . $curPage);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=user&act=man_admin", false);
        }
    }
}

/* Delete admin */
function deleteAdmin()
{
    global $d, $langadmin, $func, $curPage;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        $row = $d->rawQueryOne("select id from #_user where id = ? limit 0,1", array($id));

        if (!empty($row['id'])) {
            $d->rawQuery("delete from #_user where id = ? and role = 1", array($id));
            $func->transfer(xoadulieuthanhcong, "index.php?com=user&act=man_admin&p=" . $curPage);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=user&act=man_admin&p=" . $curPage, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);
            $row = $d->rawQueryOne("select id from #_user where id = ? limit 0,1", array($id));

            if (!empty($row['id'])) {
                $d->rawQuery("delete from #_user where id = ? and role = 1", array($id));
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=user&act=man_admin&p=" . $curPage);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=user&act=man_admin&p=" . $curPage, false);
    }
}

/* Logout admin */
function logout()
{
    global $d, $langadmin, $func, $loginAdmin;

    /* Hủy bỏ quyền */
    $data_update_permission['secret_key'] = '';
    $d->where('id', $_SESSION[$loginAdmin]['id']);
    $d->update('user', $data_update_permission);

    /* Hủy bỏ login */
    if (!empty($_SESSION[TOKEN])) unset($_SESSION[TOKEN]);
    unset($_SESSION[$loginAdmin]);
    $func->redirect("index.php?com=user&act=login");
}

/* View member */
function viewMembers()
{
    global $d, $langadmin, $func, $curPage, $items, $paging, $config;

    $where = "";

    if (isset($_REQUEST['keyword'])) {
        $keyword = htmlspecialchars($_REQUEST['keyword']);
        $where .= " and (username LIKE '%$keyword%' or fullname LIKE '%$keyword%')";
    }

    $perPage = 10;
    $startpoint = ($curPage * $perPage) - $perPage;
    $limit = " limit " . $startpoint . "," . $perPage;
    $sql = "select * from #_member where id <> 0 $where order by numb,id desc $limit";
    $items = $d->rawQuery($sql);
    $sqlNum = "select count(*) as 'num' from #_member where id <> 0 $where order by numb,id desc";
    $count = $d->rawQueryOne($sqlNum);
    $total = (!empty($count)) ? $count['num'] : 0;
    $url = "index.php?com=user&act=man_member";
    $paging = $func->pagination($total, $perPage, $curPage, $url);
}

/* Edit member */
function editMember()
{
    global $d, $langadmin, $func, $curPage, $item;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if (empty($id)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=user&act=man_member&p=" . $curPage, false);
    } else {
        $item = $d->rawQueryOne("select * from #_member where id = ? limit 0,1", array($id));

        if (empty($item)) {
            $func->transfer(dulieukhongcothuc, "index.php?com=user&act=man_member&p=" . $curPage, false);
        }
    }
}

/* Save member */
function saveMember()
{
    global $d, $langadmin, $func, $flash, $curPage;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=user&act=man_member&p=" . $curPage, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            $data[$column] = ($column == 'password') ? $value : htmlspecialchars($func->sanitize($value));
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        $birthday = $data['birthday'];
        $data['birthday'] = strtotime(str_replace("/", "-", $data['birthday']));
    }

    /* Valid data */
    if (empty($data['username'])) {
        $response['messages'][] = taikhoankhongduoctrong;
    }

    if (!empty($data['username']) && !$func->isAlphaNum($data['username'])) {
        $response['messages'][] = taikhoanchiduocnhapchuthuongvaso;
    }

    if (!empty($data['username'])) {
        if ($func->checkAccount($data['username'], 'username', 'member', $id)) {
            $response['messages'][] = taikhoandatontai;
        }
    }

    if (empty($id) || !empty($data['password'])) {
        if (empty($data['password'])) {
            $response['messages'][] = matkhaukhongduoctrong;
        }

        if (!empty($data['password']) && empty($_POST['confirm_password'])) {
            $response['messages'][] = xacnhanmatkhaukhongduoctrong;
        }

        if (!empty($data['password']) && !empty($_POST['confirm_password']) && !$func->isMatch($data['password'], $_POST['confirm_password'])) {
            $response['messages'][] = matkhaukhongtrungkhop;
        }
    }

    if (empty($data['fullname'])) {
        $response['messages'][] = hotenkhongthetrong;
    }

    if (empty($data['email'])) {
        $response['messages'][] = emailkhongduoctrong;
    }

    if (!empty($data['email']) && !$func->isEmail($data['email'])) {
        $response['messages'][] = emailkhonghople;
    }

    if (!empty($data['email'])) {
        if ($func->checkAccount($data['email'], 'email', 'member', $id)) {
            $response['messages'][] = emaildatontai;
        }
    }

    if (!empty($data['phone']) && !$func->isPhone($data['phone'])) {
        $response['messages'][] = sodienthoaikhonghople;
    }

    if (empty($data['gender'])) {
        $response['messages'][] = chuachongioitinh;
    }

    if (empty($birthday)) {
        $response['messages'][] = ngaysinhkhongduoctrong;
    }

    if (!empty($birthday) && !$func->isDate($birthday)) {
        $response['messages'][] = ngaysinhkhonghople;
    }

    if (empty($data['address'])) {
        $response['messages'][] = diachikhongduoctrong;
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

        if (empty($id)) {
            $func->redirect("index.php?com=user&act=add_member");
        } else {
            $func->redirect("index.php?com=user&act=edit_member&id=" . $id);
        }
    }

    /* Save data */
    if ($id) {
        if ($func->checkRole()) {
            $row = $d->rawQueryOne("select id from #_member where id = ? limit 0,1", array($id));

            if (!empty($row)) {
                $func->transfer(bankhongcoquyentrentaikhoannaymoithacmacxinlienhequantriwebsite, "index.php?com=user&act=man_member&p=" . $curPage, false);
            }
        }

        if (!empty($data['password'])) {
            $password = $data['password'];
            $confirm_password = !empty($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            $data['password'] = md5($password);
        } else {
            unset($data['password']);
        }

        $d->where('id', $id);
        if ($d->update('member', $data)) {
            $func->transfer(capnhatdulieuthanhcong, "index.php?com=user&act=man_member&p=" . $curPage);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=user&act=man_member&p=" . $curPage, false);
        }
    } else {
        if (!empty($data['password'])) {
            $data['password'] = md5($data['password']);
        }

        if ($d->insert('member', $data)) {
            $func->transfer(luudulieuthanhcong, "index.php?com=user&act=man_member&p=" . $curPage);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=user&act=man_member&p=" . $curPage, false);
        }
    }
}

/* Delete member */
function deleteMember()
{
    global $d, $langadmin, $func, $curPage;

    $id = (!empty($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

    if ($id) {
        $row = $d->rawQueryOne("select id from #_member where id = ? limit 0,1", array($id));

        if (!empty($row['id'])) {
            $d->rawQuery("delete from #_member where id = ?", array($id));
            $func->transfer(xoadulieuthanhcong, "index.php?com=user&act=man_member&p=" . $curPage);
        } else {
            $func->transfer(xoadulieubiloi, "index.php?com=user&act=man_member&p=" . $curPage, false);
        }
    } elseif (isset($_GET['listid'])) {
        $listid = explode(",", $_GET['listid']);

        for ($i = 0; $i < count($listid); $i++) {
            $id = htmlspecialchars($listid[$i]);
            $row = $d->rawQueryOne("select id from #_member where id = ? limit 0,1", array($id));

            if (!empty($row['id'])) {
                $d->rawQuery("delete from #_member where id = ?", array($id));
            }
        }

        $func->transfer(xoadulieuthanhcong, "index.php?com=user&act=man_member&p=" . $curPage);
    } else {
        $func->transfer(khongnhanduocdulieu, "index.php?com=user&act=man_member&p=" . $curPage, false);
    }
}
