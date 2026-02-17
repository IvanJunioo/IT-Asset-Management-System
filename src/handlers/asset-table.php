<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/asset.php';
require_once __DIR__ . '/../repos/assignment.php';

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
  )))));  

  $assignRepo = new AssignmentRepo($pdo);
  $payload = [];
  foreach($assets as $asset){
    $user = $assignRepo->getCurrAssignedUser($asset);
    $asset->assignTo($user);
    $pl = [
      ...$asset->jsonSerialize(),
      "Assignee" => $user? $user->name->first . " " . $user->name->last : "",
    ];
    $payload[] = $pl;
  }

  echo json_encode($payload);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;
