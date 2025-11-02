<?php

require_once '../includes/request-guard.php';
require_once '../includes/dbh-inc.php';
require_once '../model/database.php';

$search = isset($_POST['search'])? $_POST['search'] : "";

$db = new Database($pdo);
$assets = array_values(array_map("unserialize", array_unique(array_map("serialize", array_merge(
  $db->searchAsset(propNum: $search),
  $db->searchAsset(procNum: $search),
  $db->searchAsset(serialNum: $search),
  $db->searchAsset(specs: $search),
  $db->searchAsset(description: $search),
  $db->searchAsset(remarks: $search),
)))));

echo json_encode($assets);

exit;
