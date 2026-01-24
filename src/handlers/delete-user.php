<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/user.php';
require_once '../manager/assign.php';
require_once '../repos/assignment.php';
require_once '../manager/logger.php';

$empID = $_POST['search'] ?? "";

$assignRepo = new AssignmentRepo($pdo);
$userRepo = new UserRepo($pdo);
$manag = new AssignmentManager(
  new AssetRepo($pdo),
  $assignRepo,
  $userRepo,
);

$user = $userRepo->identify($empID);
$assets = $assignRepo->getAssignedAssets($user);
$retDate = new DateTimeImmutable('now');
foreach ($assets as $asset){
  $manag->returnAsset($asset->propNum, $retDate, "");
}

$user->isActive = false;
$userRepo->update($user);

systemLog(
  "deactivated user $empID",
  []
);


exit;