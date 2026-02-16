<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/user.php';

header('Content-Type: application/json');

$rawSearch = $_POST['search'] ?? null;

$empID = filter_var($rawSearch, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

try {
  $repo = new UserRepo($pdo);
  $user = $repo->identify($empID);
  
  echo json_encode([
    ...$user->jsonSerialize(), 
  ]);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;