<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/user.php';

$search = $_POST['search'] ?? "";

$repo = new UserRepo($pdo);

$users = $repo->search(empID: $search);
if (!empty($users)){
	$repo->delete($users[0]);
}

exit;