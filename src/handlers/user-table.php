<?php

require_once '../includes/request-guard.php';
require_once '../includes/dbh-inc.php';
require_once '../model/database.php';

$db = new Database($pdo);
$users = $db->searchUser();

echo json_encode($users);

exit;