<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/asset.php';
require_once '../manager/logger.php';

$propNum = $_POST['search'] ?? "";

$repo = new AssetRepo($pdo);

$asset = $repo->identify($propNum);
$asset->status = AssetStatus::Condemned;
$repo->update($asset);

session_start();
$empID = $_SESSION["user_id"];

systemLog(
  "condemned asset $propNum",
  []
);

exit;
