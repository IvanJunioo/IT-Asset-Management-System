<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../manager/assign.php';

$manag = new AssignmentManager(
  new AssetRepo($pdo),
  new AssignmentRepo($pdo),
  new UserRepo($pdo),
);

if ($_POST["action"] == 'submit') {
	$retDate = new DateTimeImmutable($_POST['return-date']);
  $manag->returnAsset($_POST['asset'], $retDate, $_POST['remarks']);
}

header('Location: ../../public/views/asset-manager.php');

exit;