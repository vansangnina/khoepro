<?php
if (!defined('SOURCES')) die("Error");

switch ($act) {
    case "update":
        viewSetting();
        $template = "setting/man/man_add";
        break;
    case "save":
        saveSetting();
        break;

    default:
        $template = "404";
}

/* View setting */
function viewSetting()
{
    global $d, $langadmin, $item;

    $item = $d->rawQueryOne("select * from #_setting limit 0,1");
}

/* Save setting */
function saveSetting()
{
    global $d, $langadmin, $func, $flash, $config, $com;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=setting&act=update", false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
    $row = $d->rawQueryOne("select id, options from #_setting where id = ? limit 0,1", array($id));
    $option = json_decode($row['options'], true);
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            if (is_array($value)) {
                foreach ($value as $k2 => $v2) {
                    if ($k2 == 'coords_iframe') {
                        $option[$k2] = $func->sanitize($v2, 'iframe');
                    } else {
                        $option[$k2] = $v2;
                    }
                }

                $data[$column] = json_encode($option);
            } else {
                if ($column == 'mastertool') {
                    $data[$column] = htmlspecialchars($func->sanitize($value, 'meta'));
                } else if (in_array($column, array('headjs', 'bodyjs', 'analytics'))) {
                    $data[$column] = htmlspecialchars($func->sanitize($value, 'script'));
                } else {
                    $data[$column] = htmlspecialchars($func->sanitize($value));
                }
            }
        }
    }

    /* Post Seo */
    $dataSeo = (isset($_POST['dataSeo'])) ? $_POST['dataSeo'] : null;
    if ($dataSeo) {
        foreach ($dataSeo as $column => $value) {
            $dataSeo[$column] = htmlspecialchars($func->sanitize($value));
        }
    }


    $checkTitle = $func->checkTitle($data);

    if (!empty($checkTitle)) {
        foreach ($checkTitle as $k => $v) {
            $response['messages'][] = $v;
        }
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

        if (!empty($option)) {
            foreach ($option as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        if (!empty($dataSeo)) {
            foreach ($dataSeo as $k => $v) {
                if (!empty($v)) {
                    $flash->set($k, $v);
                }
            }
        }

        /* Errors */
        $response['status'] = 'danger';
        $message = base64_encode(json_encode($response));
        $flash->set('message', $message);
        $func->redirect("index.php?com=setting&act=update");
    }

    /* Save data */
    if (!empty($row)) {
        $d->where('id', $id);
        if ($d->update('setting', $data)) {
            /* SEO */
            $d->rawQuery("delete from #_seo where id_parent = ? and com = ? and act = ? and type = ?", array(0, $com, 'update', $com));
            $dataSeo['id_parent'] = 0;
            $dataSeo['com'] = $com;
            $dataSeo['act'] = 'update';
            $dataSeo['type'] = $com;
            $d->insert('seo', $dataSeo);

            $func->transfer(capnhatdulieuthanhcong, "index.php?com=setting&act=update");
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=setting&act=update", false);
        }
    } else {
        if ($d->insert('setting', $data)) {
            /* SEO */
            $dataSeo['id_parent'] = 0;
            $dataSeo['com'] = $com;
            $dataSeo['act'] = 'update';
            $dataSeo['type'] = $com;
            $d->insert('seo', $dataSeo);

            $func->transfer(luudulieuthanhcong, "index.php?com=setting&act=update");
        } else {
            $func->transfer(luudulieuthatbai, "index.php?com=setting&act=update", false);
        }
    }
}
