<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/user.php';

header('Content-Type: application/json');

$empID = $_POST['search'] ?? "";

try {
  $repo = new UserRepo($pdo);
  $user = $repo->identify($empID);
  
  echo json_encode([
    ...$user->jsonSerialize(), 
    "ContactNums" => $repo->getContacts($empID)
  ]);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;