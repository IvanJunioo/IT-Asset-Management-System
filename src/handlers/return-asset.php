<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../manager/assign.php';
require_once __DIR__ . '/../manager/logger.php';

$manag = new AssignmentManager(
  new AssetRepo($pdo),
  new AssignmentRepo($pdo),
  new UserRepo($pdo),
);

if ($_POST["action"] == 'submit') {
	$assets = $_POST['assets'];

  date_default_timezone_set('Asia/Manila');
  $curTime = new DateTimeImmutable();
  $retDate = new DateTimeImmutable($_POST['return-date']);
  $retDate = $retDate->setTime((int)$curTime->format('H'), (int)$curTime->format('i'), (int)$curTime->format('s'));
  
  foreach ($assets as $pnum) {
    $manag->returnAsset($pnum, $retDate, $_POST['remarks']);

    systemLog(
      "returned asset $pnum",
      []
    );
  }
}

header('Location: ../../public/views/asset-manager.php');

exit;