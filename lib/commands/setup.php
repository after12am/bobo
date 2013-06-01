<?php
/*
    setup database command
*/
require_once('DB.php');

$query = file_get_contents(sprintf(
    '%s../../../sql/boobyTrap.sql',
    dirname(__FILE__)
));

$db = DB::getInstance();

if ($db->exec($query) !== false) {
    echo 'database setup succeeded.';
    exit;
}

echo 'database setup failed.';