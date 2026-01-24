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
	$assets = $_POST['assets'];
  $retDate = new DateTimeImmutable($_POST['return-date']);
  foreach ($assets as $pnum) {
    $manag->returnAsset($pnum, $retDate, $_POST['remarks']);
  }

  systemLog(
    "returned " . count($assets) . "asset(s)"  , ["assets" => $assets],
    []
  );
}

header('Location: ../../public/views/asset-manager.php');

exit;