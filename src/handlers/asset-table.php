<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/asset.php';

header('Content-Type: application/json');

$search =  $_POST['search'] ?? "";
$status = $_POST['status'] ?? "";

try {
  $status = $status !== ""? array_map("AssetStatus::from", explode(',', $status)) : null;
  
  $repo = new AssetRepo($pdo);
  $assets = array_values(array_map("unserialize", array_unique(array_map("serialize", array_merge(
    $repo->search(propNum: $search, status: $status),
    $repo->search(procNum: $search, status: $status),
    $repo->search(serialNum: $search, status: $status),
    $repo->search(specs: $search, status: $status),
    $repo->search(description: $search, status: $status),
    $repo->search(remarks: $search, status: $status),
  )))));  

  echo json_encode($assets);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;
