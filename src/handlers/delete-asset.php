<?php

require_once '../includes/request-guard.php';
require_once '../includes/dbh-inc.php';
require_once '../model/database.php';

$search = $_POST['search'] ?? "";

$db = new Database($pdo);
$assets = $db->searchAsset(propNum: $search);
if (!empty($assets)){
	$db->deleteAsset($assets[0]);
}

exit;