<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../model/database.php';

$search = $_POST['search'] ?? "";

$db = new Database($pdo);

$users = $db->searchUser(empID: $search);
if (!empty($users)){
	$db->deleteUser($users[0]);
}

exit;