<?php
if (!defined('SOURCES')) die("Error");

/* Kiểm tra active static */
if (isset($config['static'])) {
    $arrCheck = array();
    foreach ($config['static'] as $k => $v) $arrCheck[] = $k;
    if (!count($arrCheck) || !in_array($type, $arrCheck)) $func->transfer(trangkhongtontai, "index.php", false);
} else {
    $func->transfer(trangkhongtontai, "index.php", false);
}

switch ($act) {
    case "update":
        viewStatic();
        $template = "static/man/man_add";
        break;
    case "save":
        saveStatic();
        break;

    default:
        $template = "404";
}

/* View static */
function viewStatic()
{
    global $d, $langadmin, $item, $type, $gallery, $com;

    $item = $d->rawQueryOne("select * from #_static where type = ? limit 0,1", array($type));
    $gallery = $d->rawQuery("select * from #_gallery where com = ? and type = ? and kind = ? and val = ? order by numb,id desc", array( $com, $type, 'man', $type));

}

/* Save static */
function saveStatic()
{
    global $d, $langadmin, $config, $func, $flash, $com, $type;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=static&act=update&type=" . $type, false);
    }

    /* Post dữ liệu */
    $message = '';
    $response = array();
    $static = $d->rawQueryOne("select * from #_static where type = ? limit 0,1", array($type));
    $data = (!empty($_POST['data'])) ? $_POST['data'] : null;
    if ($data) {
        foreach ($data as $column => $value) {
            if (strpos($column, 'content') !== false || strpos($column, 'desc') !== false) {
                $data[$column] = htmlspecialchars($func->sanitize($value, 'iframe'));
            } else {
                $data[$column] = htmlspecialchars($func->sanitize($value));
            }
        }

        if (isset($_POST['status'])) {
            $status = '';
            foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
            $data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
        } else {
            $data['status'] = "";
        }

        if (!empty($config['static'][$type]['name'])) {
            $data['slugvi'] = (!empty($data['namevi'])) ? $func->changeTitle($data['namevi']) : '';
            $data['slugen'] = (!empty($data['nameen'])) ? $func->changeTitle($data['nameen']) : '';
        }

        $data['type'] = $type;
    }

    /* Post Seo */
    if (isset($config['static'][$type]['seo']) && $config['static'][$type]['seo'] == true) {
        $dataSeo = (isset($_POST['dataSeo'])) ? $_POST['dataSeo'] : null;
        if ($dataSeo) {
            foreach ($dataSeo as $column => $value) {
                $dataSeo[$column] = htmlspecialchars($func->sanitize($value));
            }
        }
    }

    if (!empty($config['static'][$type]['name'])) {
        /* Valid data */
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
            $func->redirect("index.php?com=static&act=update&type=" . $type);
        }
    }

    /* Save data */
    if (!empty($static)) {
        $data['date_updated'] = time();

        $d->where('type', $type);
        if ($d->update('static', $data)) {
            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES["file"]["name"]);

                if ($photo = $func->uploadImage("file", $config['static'][$type]['img_type'], UPLOAD_NEWS, $file_name)) {
                    $row = $d->rawQueryOne("select id, photo from #_static where type = ? limit 0,1", array($type));

                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_NEWS . $row['photo']);
                    }

                    $photoUpdate['photo'] = $photo;
                    $d->where('type', $type);
                    $d->update('static', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            if ($func->hasFile("video")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES["video"]["name"]);

                if ($video = $func->uploadImage("video", $config['static'][$type]['video_type'], UPLOAD_NEWS, $file_name)) {
                    $row = $d->rawQueryOne("select id, video from #_static where type = ? limit 0,1", array($type));
                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_NEWS . $row['video']);
                    }
                    $photoUpdate['video'] = $video;
                    $d->where('type', $type);
                    $d->update('static', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            /* Photo1 */
            if ($func->hasFile("file1")) {
                $photoUpdate1 = array();
                $file_name1 = $func->uploadName($_FILES["file1"]["name"]);

                if ($photo1 = $func->uploadImage("file1", $config['static'][$type]['img_type'], UPLOAD_NEWS, $file_name1)) {
                    $row1 = $d->rawQueryOne("select id, photo1 from #_static where type = ? limit 0,1", array($type));

                    if (!empty($row1)) {
                        $func->deleteFile(UPLOAD_NEWS . $row1['photo1']);
                    }

                    $photoUpdate1['photo1'] = $photo1;
                    $d->where('type', $type);
                    $d->update('static', $photoUpdate1);
                    unset($photoUpdate1);
                }
            }

            /* File attach */
            if ($func->hasFile("file_attach")) {
                $fileUpdate = array();
                $file_name = $func->uploadName($_FILES["file_attach"]["name"]);

                if ($file_attach = $func->uploadImage("file_attach", $config['static'][$type]['file_type'], UPLOAD_FILE, $file_name)) {
                    $row = $d->rawQueryOne("select id, file_attach from #_static where type = ? limit 0,1", array($type));

                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_FILE . $row['file_attach']);
                    }

                    $fileUpdate['file_attach'] = $file_attach;
                    $d->where('type', $type);
                    $d->update('static', $fileUpdate);
                    unset($fileUpdate);
                }
            }

            /* SEO */
            if (isset($config['static'][$type]['seo']) && $config['static'][$type]['seo'] == true) {
                $d->rawQuery("delete from #_seo where com = ? and act = ? and type = ?", array($com, 'update', $type));

                $dataSeo['id_parent'] = 0;
                $dataSeo['com'] = $com;
                $dataSeo['act'] = 'update';
                $dataSeo['type'] = $type;
                $d->insert('seo', $dataSeo);
            }

            $func->transfer(capnhatdulieuthanhcong, "index.php?com=static&act=update&type=" . $type);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=static&act=update&type=" . $type, false);
        }
    } else {
        if (!empty($data['namevi']) || !empty($data['descvi']) || !empty($data['contentvi'])) {
            $data['date_created'] = time();

            if ($d->insert('static', $data)) {
                $id_insert = $d->getLastInsertId();

                /* Photo */
                if ($func->hasFile("file")) {
                    $photoUpdate = array();
                    $file_name = $func->uploadName($_FILES['file']["name"]);

                    if ($photo = $func->uploadImage("file", $config['static'][$type]['img_type'], UPLOAD_NEWS, $file_name)) {
                        $photoUpdate['photo'] = $photo;
                        $d->where('id', $id_insert);
                        $d->update('static', $photoUpdate);
                        unset($photoUpdate);
                    }
                }

                if ($func->hasFile("video")) {
                    $photoUpdate = array();
                    $file_name = $func->uploadName($_FILES['video']["name"]);

                    if ($video = $func->uploadImage("video", $config['static'][$type]['video_type'], UPLOAD_NEWS, $file_name)) {
                        $photoUpdate['video'] = $video;
                        $d->where('id', $id_insert);
                        $d->update('static', $photoUpdate);
                        unset($photoUpdate);
                    }
                }

                /* Photo1 */
                if ($func->hasFile("file_attach")) {
                    $fileUpdate = array();
                    $file_name = $func->uploadName($_FILES['file_attach']["name"]);

                    if ($file_attach = $func->uploadImage("file_attach", $config['static'][$type]['file_type'], UPLOAD_FILE, $file_name)) {
                        $fileUpdate['file_attach'] = $file_attach;
                        $d->where('id', $id_insert);
                        $d->update('static', $fileUpdate);
                        unset($fileUpdate);
                    }
                }

                /* file_attach */
                if ($func->hasFile("file_attach")) {
                    $fileUpdate = array();
                    $file_name = $func->uploadName($_FILES['file_attach']["name"]);

                    if ($file_attach = $func->uploadImage("file_attach", $config['static'][$type]['file_type'], UPLOAD_FILE, $file_name)) {
                        $fileUpdate['file_attach'] = $file_attach;
                        $d->where('id', $id_insert);
                        $d->update('static', $fileUpdate);
                        unset($fileUpdate);
                    }
                }

                /* SEO */
                if (isset($config['static'][$type]['seo']) && $config['static'][$type]['seo'] == true) {
                    $dataSeo['id_parent'] = 0;
                    $dataSeo['com'] = $com;
                    $dataSeo['act'] = 'update';
                    $dataSeo['type'] = $type;
                    $d->insert('seo', $dataSeo);
                }

                $func->transfer(luudulieuthanhcong, "index.php?com=static&act=update&type=" . $type);
            } else {
                $func->transfer(luudulieubiloi, "index.php?com=static&act=update&type=" . $type, false);
            }
        } else {
            $func->transfer(dulieurong, "index.php?com=static&act=update&type=" . $type, false);
        }
    }
}
