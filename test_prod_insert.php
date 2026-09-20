<?php
define('LIBRARIES', __DIR__ . '/libraries/');
$_SERVER['SERVER_NAME'] = 'localhost';
require_once LIBRARIES . 'config.php';
require_once LIBRARIES . 'autoload.php';
new AutoLoad();
$d = new PDODb($config['database']);

$time = time();
$d->rawQuery("INSERT INTO table_product (`namevi`, `slugvi`, `code`, `regular_price`, `sale_price`, `status`, `type`, `date_created`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", array(
    'Test P09 ' . $time,
    'test-p09-' . $time,
    'CODE_' . $time,
    100000,
    90000,
    'hienthi',
    'san-pham',
    $time
));
$lastId = $d->getLastInsertId();
echo "Inserted Product ID: " . $lastId . "\n";
