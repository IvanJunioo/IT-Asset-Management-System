<?php

require_once '../includes/request-guard.php';
require_once '../includes/dbh-inc.php';
require_once '../model/database.php';

$db = new Database($pdo);
$assets = $db->searchAsset();

echo json_encode($assets);

exit;
