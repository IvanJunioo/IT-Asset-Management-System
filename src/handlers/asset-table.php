<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/asset.php';
require_once '../repos/assignment.php';

header('Content-Type: application/json');

$search =  $_POST['search'] ?? "";
$status = $_POST['status'] ?? "";

try {
  $status = $status !== ""? array_map("AssetStatus::from", explode(',', $status)) : null;
  
  $assetRepo = new AssetRepo($pdo);
  $assets = array_values(array_map("unserialize", array_unique(array_map("serialize", array_merge(
    $assetRepo->search(new AssetSearchCriteria(propNum: $search, status: $status)),
    $assetRepo->search(new AssetSearchCriteria(procNum: $search, status: $status)),
    $assetRepo->search(new AssetSearchCriteria(serialNum: $search, status: $status)),
    $assetRepo->search(new AssetSearchCriteria(specs: $search, status: $status)),
    $assetRepo->search(new AssetSearchCriteria(description: $search, status: $status)),
    $assetRepo->search(new AssetSearchCriteria(remarks: $search, status: $status)),
  )))));  

  $assignRepo = new AssignmentRepo($pdo);
  foreach($assets as $asset){
    $user = $assignRepo->getCurrAssignedUser($asset);
    $asset->assignTo($user);
  }

  echo json_encode($assets);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;
