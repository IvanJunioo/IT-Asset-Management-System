<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/asset.php';
require_once __DIR__ . '/../repos/user.php';

header('Content-Type: application/json');

try {
  $assetRepo = new AssetRepo($pdo);
  $userRepo = new UserRepo($pdo);
  
  echo json_encode([
    "assetsTotal" => $assetRepo->count(),
    "assetsAvail" => $assetRepo->count(new AssetSearchCriteria(status: [AssetStatus::Unassigned])),
    "usersTotal" => $userRepo->count(),
    "usersActive" => $userRepo->count(new UserSearchCriteria(isActive: ["Active"])),
  ]);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;