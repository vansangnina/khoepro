<?php
include "config.php";

$id = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
$lienhe = $d->rawQueryOne("select contentvi from #_static where type = ? limit 0,1", array('lienhe'));

echo htmlspecialchars_decode($lienhe['contentvi']);
?>