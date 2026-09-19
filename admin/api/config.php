<?php
session_start();
define('LIBRARIES', '../../libraries/');
define('SOURCES', '../sources/');
define('THUMBS', 'thumbs');

require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();
$d = new PDODb($config['database']);
$cache = new Cache($d);
$func = new Functions($d, $cache);
$langadmin = (!empty($_SESSION['langadmin']) && in_array($_SESSION['langadmin'], $config['website']['adminlang']['key'])) ? $_SESSION['langadmin'] : 'vi';
$_SESSION['langadmin'] = 'vi';
require_once LIBRARIES . "lang/admin/" . $langadmin . ".php";
if ($func->checkLoginAdmin() == false) {
    die();
}
