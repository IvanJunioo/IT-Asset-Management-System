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
	$propNum = $_POST['asset'];
  $retDate = new DateTimeImmutable($_POST['return-date']);
  $manag->returnAsset($propNum, $retDate, $_POST['remarks']);

  systemLog(
    "returned asset $propNum",
    []
  );
}

header('Location: ../../public/views/asset-manager.php');

exit;