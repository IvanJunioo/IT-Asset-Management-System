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
	session_start();
	
	$assDate = new DateTimeImmutable($_POST['assign-date']);

  foreach ($_POST['assets'] as $pnum){
    $manag->assignAsset(
      $pnum, 
      $_SESSION['user_id'], 
      $_POST['user'], 
      $assDate, 
      $_POST['remarks']
    );
	}
}

header('Location: ../../public/views/asset-manager.php');

exit;