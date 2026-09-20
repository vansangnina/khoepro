<?php
define('LIBRARIES', __DIR__ . '/libraries/');
$_SERVER['SERVER_NAME'] = 'localhost';
require_once LIBRARIES . 'config.php';
require_once LIBRARIES . 'autoload.php';
new AutoLoad();
$d = new PDODb($config['database']);

$col = $d->rawQueryOne("SHOW COLUMNS FROM table_winner_evaluation LIKE 'is_legacy'");
if (empty($col)) {
    $d->rawQuery("ALTER TABLE table_winner_evaluation ADD COLUMN `is_legacy` tinyint(1) DEFAULT 0 AFTER `ai_analysis`");
    $d->rawQuery("ALTER TABLE table_winner_evaluation ADD KEY `idx_is_legacy` (`is_legacy`)");
    echo "Added is_legacy column to table_winner_evaluation.\n";
} else {
    echo "is_legacy column already exists.\n";
}

$tables = $d->rawQuery("SHOW TABLES LIKE 'table_optimization_%'");
echo "Optimization tables:\n";
foreach ($tables as $t) {
    echo " - " . reset($t) . "\n";
}
