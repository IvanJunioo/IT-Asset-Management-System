<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/user.php';

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