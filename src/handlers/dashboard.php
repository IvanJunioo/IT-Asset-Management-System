<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/asset.php';
require_once '../repos/user.php';

header('Content-Type: application/json');

try {
  $assetRepo = new AssetRepo($pdo);
  $userRepo = new UserRepo($pdo);
  
  echo json_encode([
    "assets-total" => $assetRepo->count(),
    "assets-avail" => $assetRepo->count(new AssetSearchCriteria(status: [AssetStatus::Available])),
    "users-total" => $userRepo->count(),
    "users-active" => $userRepo->count(new UserSearchCriteria(isActive: ["Active"])),
  ]);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;