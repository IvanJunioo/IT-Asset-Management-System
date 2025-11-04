<?php

require_once '../includes/request-guard.php';
require_once '../includes/dbh-inc.php';
require_once '../model/database.php';

$search = $_POST['search'] ?? "";

$db = new Database($pdo);
$users = $db->searchUser(empID: $search);
if (!empty($users)){
	$db->deleteUser($users[0]);
}

exit;