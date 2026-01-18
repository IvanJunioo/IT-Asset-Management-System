<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../model/database.php';

$db = new Database($pdo);

$action = $_POST['action'];

if ($_POST['assets'] != null) {
	setcookie("assets", $_POST['assets']);
}

if ($_POST['user'] != null) {
	setcookie("user", $_POST['user']);
}

if ($action == 'submit') {
  $assets = $_COOKIE['assets'];
	$assets = explode(',', $assets);
  $user = $db->searchUser(empID: $_COOKIE['user'])[0];
	
	$assDate = new DateTimeImmutable($_POST['assign-date']);
	foreach ($assets as $pnum){
		$asset = $db->searchAsset(propNum: $pnum);
		if (is_array($asset)){
      // TODO : implement user authentication or session data
			//$db->assignAsset($asset[0], $user, $assDate, $_POST['remarks']);
		}
	}
}

header('Location: ../views/asset-manager.php');

exit;