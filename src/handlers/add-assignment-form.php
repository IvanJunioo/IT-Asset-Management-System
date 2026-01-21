<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';

require_once '../repos/asset.php';
require_once '../repos/assignment.php';
require_once '../repos/user.php';

$assetRepo = new AssetRepo($pdo);
$assignrepo = new AssignmentRepo($pdo);
$userRepo = new UserRepo($pdo);

$action = $_POST['action'];

if ($_POST['assets'] != null) {
	setcookie("assets", $_POST['assets']);
}

if ($_POST['user'] != null) {
	setcookie("user", $_POST['user']);
}

if ($action == 'submit') {
	session_start();
  $assets = $_COOKIE['assets'];
	$assets = explode(',', $assets);
  $assignee = $userRepo->search(new UserSearchCriteria(empID: $_COOKIE['user']))[0];
	$assigner = $_SESSION['user_id'];
	
	$assDate = new DateTimeImmutable($_POST['assign-date']);
	foreach ($assets as $pnum){
		$asset = $assetRepo->search(new AssetSearchCriteria(propNum: $pnum))[0];
		if (is_array($asset)){
			$assignrepo->assign($asset[0], $assigner, $assignee, $assDate, $_POST['remarks']);
		}
	}
}

header('Location: ../../public/views/asset-manager.php');

exit;