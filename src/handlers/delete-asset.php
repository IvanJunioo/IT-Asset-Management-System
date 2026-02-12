<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/asset.php';
require_once __DIR__ . '/../manager/logger.php';

$propNum = $_POST['search'] ?? "";

$repo = new AssetRepo($pdo);

$asset = $repo->identify($propNum);
$asset->status = AssetStatus::Condemned;
$repo->update($asset);

session_start();
$empID = $_SESSION["user_id"];

systemLog(
  "condemned asset $propNum",
  [
    "action" => "condemn",
    "object" => "asset",
    "propNum" => $propNum,
  ]
);

exit;
