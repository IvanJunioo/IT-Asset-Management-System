<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/user.php';

header('Content-Type: application/json');

$search = $_POST['search'] ?? "";
$status = $_POST['status'] ?? "";
$privilege = $_POST['priv'] ?? "";

try {
  $status = $status !== ""? explode(',', $status) : null;
  $privilege = $privilege !== ""? array_map("UserPrivilege::from", explode(',', $privilege)) : null;

  $repo = new UserRepo($pdo);
  $users = array_values(array_map("unserialize", array_unique(array_map("serialize", array_merge(
    $repo->search(new UserSearchCriteria(fullname: new Fullname(first: $search), isActive: $status, privileges: $privilege)),
    $repo->search(new UserSearchCriteria(fullname: new Fullname(last: $search), isActive: $status, privileges: $privilege)),
    $repo->search(new UserSearchCriteria(email: $search, isActive: $status, privileges: $privilege)),
  )))));
  
  echo json_encode($users);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;