<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/user.php';

$search = $_POST['search'] ?? "";

$repo = new UserRepo($pdo);

$users = $repo->search(new UserSearchCriteria(empID: $search));
if (!empty($users)){
  $user =  $users[0];
  $user->isActive = false;
	$repo->update($user);
}

exit;