<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../model/database.php';

header('Content-Type: application/json');

$search =  $_POST['search'] ?? "";
$status = $_POST['status'] ?? "";

try {
  $status = $status !== ""? array_map("AssetStatus::from", explode(',', $status)) : null;
  
  $db = new Database($pdo);
  $assets = array_values(array_map("unserialize", array_unique(array_map("serialize", array_merge(
    $db->searchAsset(propNum: $search, status: $status),
    $db->searchAsset(procNum: $search, status: $status),
    $db->searchAsset(serialNum: $search, status: $status),
    $db->searchAsset(specs: $search, status: $status),
    $db->searchAsset(description: $search, status: $status),
    $db->searchAsset(remarks: $search, status: $status),
  )))));  

  echo json_encode($assets);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;
