<?php
if (!defined('SOURCES')) die("Error");

/* Kiểm tra active seopage */
if (isset($config['seopage']) && count($config['seopage']['page']) > 0) {
    $arrCheck = array();
    foreach ($config['seopage']['page'] as $k => $v) $arrCheck[] = $k;
    if (!count($arrCheck) || !in_array($type, $arrCheck)) $func->transfer(trangkhongtontai, "index.php", false);
} else {
    $func->transfer(trangkhongtontai, "index.php", false);
}

switch ($act) {
    case "update":
        viewSeoPage();
        $template = "seopage/man/man_add";
        break;
    case "save":
        saveSeoPage();
        break;

    default:
        $template = "404";
}

/* View Seopage */
function viewSeoPage()
{
    global $d, $langadmin, $item, $type;

    $item = $d->rawQueryOne("select * from #_seopage where type = ? limit 0,1", array($type));
}

/* Save Seopage */
function saveSeoPage()
{
    global $d, $langadmin, $func, $config, $com, $type;

    /* Check post */
    if (empty($_POST)) {
        $func->transfer(khongnhanduocdulieu, "index.php?com=seopage&act=update&type=" . $type, false);
    }

    /* Post dữ liệu */
    $seopage = $d->rawQueryOne("select * from #_seopage where type = ? limit 0,1", array($type));
    $dataSeo = (!empty($_POST['dataSeo'])) ? $_POST['dataSeo'] : null;
    if ($dataSeo) {
        foreach ($dataSeo as $column => $value) {
            $dataSeo[$column] = htmlspecialchars($func->sanitize($value));
        }

        $dataSeo['type'] = $type;
    }

    /* Save data */
    if (!empty($seopage)) {
        $d->where('type', $type);
        if ($d->update('seopage', $dataSeo)) {
            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES["file"]["name"]);

                if ($photo = $func->uploadImage("file", $config['seopage']['img_type'], UPLOAD_SEOPAGE, $file_name)) {
                    $row = $d->rawQueryOne("select id, photo from #_seopage where type = ? limit 0,1", array($type));

                    if (!empty($row)) {
                        $func->deleteFile(UPLOAD_SEOPAGE . $row['photo']);
                    }

                    $photoUpdate['photo'] = $photo;
                    $d->where('type', $type);
                    $d->update('seopage', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            $func->transfer(capnhatdulieuthanhcong, "index.php?com=seopage&act=update&type=" . $type);
        } else {
            $func->transfer(capnhatdulieubiloi, "index.php?com=seopage&act=update&type=" . $type, false);
        }
    } else {
        if ($d->insert('seopage', $dataSeo)) {
            $id_insert = $d->getLastInsertId();

            /* Photo */
            if ($func->hasFile("file")) {
                $photoUpdate = array();
                $file_name = $func->uploadName($_FILES['file']["name"]);

                if ($photo = $func->uploadImage("file", $config['seopage']['img_type'], UPLOAD_SEOPAGE, $file_name)) {
                    $photoUpdate['photo'] = $photo;
                    $d->where('id', $id_insert);
                    $d->update('seopage', $photoUpdate);
                    unset($photoUpdate);
                }
            }

            $func->transfer(luudulieuthanhcong, "index.php?com=seopage&act=update&type=" . $type);
        } else {
            $func->transfer(luudulieubiloi, "index.php?com=seopage&act=update&type=" . $type, false);
        }
    }
}
