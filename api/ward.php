<?php
include "config.php";

$id_district = (!empty($_POST['id_district'])) ? htmlspecialchars($_POST['id_district']) : 0;
$ward = null;
if ($id_district) $ward = $d->rawQuery("select name, id from #_ward where id_district = ? and find_in_set('hienthi',status) order by numb asc", array($id_district));

if ($ward) { ?>
    <option value=""><?= phuongxa ?></option>
    <?php foreach ($ward as $k => $v) { ?>
        <option value="<?= $v['id'] ?>"><?= $v['name'] ?></option>
    <?php }
} else { ?>
    <option value=""><?= phuongxa ?></option>
<?php }
?>