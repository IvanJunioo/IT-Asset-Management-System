<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../manager/assign.php';
require_once '../manager/logger.php';

$manag = new AssignmentManager(
  new AssetRepo($pdo),
  new AssignmentRepo($pdo),
  new UserRepo($pdo),
);

if ($_POST["action"] == 'submit') {
	session_start();
	
  $empID = $_SESSION['user_id'];

  date_default_timezone_set('Asia/Manila');
  $curTime = new DateTimeImmutable();
	$assDate = new DateTimeImmutable($_POST['assign-date']);
  $assDate= $assDate->setTime((int)$curTime->format('H'), (int)$curTime->format('i'), (int)$curTime->format('s'));
  $assets = $_POST['assets'];

  foreach ($assets as $pnum){
    $manag->assignAsset(
      $pnum, 
      $empID, 
      $_POST['user'], 
      $assDate, 
      $_POST['remarks']
    );
	}

  systemLog(
    "assigned " . count($assets) . "assets to user $empID",
    ["assets" => $assets]
  );
}

header('Location: ../../public/views/asset-manager.php');

exit;