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
    $repo->search(new AssetSearchCriteria(propNum: $search, status: $status)),
    $repo->search(new AssetSearchCriteria(procNum: $search, status: $status)),
    $repo->search(new AssetSearchCriteria(serialNum: $search, status: $status)),
    $repo->search(new AssetSearchCriteria(specs: $search, status: $status)),
    $repo->search(new AssetSearchCriteria(description: $search, status: $status)),
    $repo->search(new AssetSearchCriteria(remarks: $search, status: $status)),
  )))));  

  echo json_encode($assets);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;
