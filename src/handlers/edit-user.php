<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/user.php';

header('Content-Type: application/json');

$search = $_POST['search'] ?? "";

try {
  $repo = new UserRepo($pdo);
  $users = array_values(array_map("unserialize", array_unique(array_map("serialize", array_merge(
    $repo->search(empID: $search)
  )))));
  
  echo json_encode($users);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;