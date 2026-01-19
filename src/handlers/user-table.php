<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/user.php';

header('Content-Type: application/json');

$search = $_POST['search'] ?? "";
$privilege = $_POST['priv'] ?? "";

try {
  $privilege = $privilege !== ""? array_map("UserPrivilege::from", explode(',', $privilege)) : null;

  $repo = new UserRepo($pdo);
  $users = array_values(array_map("unserialize", array_unique(array_map("serialize", array_merge(
    $repo->search(new UserSearchCriteria(empID: $search, privileges: $privilege)),
    $repo->search(new UserSearchCriteria(fullname: new Fullname(first: $search), privileges: $privilege)),
    $repo->search(new UserSearchCriteria(fullname: new Fullname(middle: $search), privileges: $privilege)),
    $repo->search(new UserSearchCriteria(fullname: new Fullname(last: $search), privileges: $privilege)),
    $repo->search(new UserSearchCriteria(email: $search, privileges: $privilege)),
  )))));
  
  echo json_encode($users);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;