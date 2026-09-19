<?php
include "config.php";

$id_city = (!empty($_POST['id_city'])) ? htmlspecialchars($_POST['id_city']) : 0;
$district = null;
if ($id_city) $district = $d->rawQuery("select name, id from #_district where id_city = ? and find_in_set('hienthi',status) order by numb asc", array($id_city));

if ($district) { ?>
    <option value=""><?= quanhuyen ?></option>
    <?php foreach ($district as $k => $v) { ?>
        <option value="<?= $v['id'] ?>"><?= $v['name'] ?></option>
    <?php }
} else { ?>
    <option value=""><?= quanhuyen ?></option>
<?php }
?>