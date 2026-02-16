<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/user.php';
require_once __DIR__ . '/../manager/assign.php';
require_once __DIR__ . '/../repos/assignment.php';
require_once __DIR__ . '/../manager/logger.php';

$data = json_decode(file_get_contents("php://input"), true);

try {
  $assignRepo = new AssignmentRepo($pdo);
  $userRepo = new UserRepo($pdo);
  $manag = new AssignmentManager(
    new AssetRepo($pdo),
    $assignRepo,
    $userRepo,
  );
  
  $empID = (int)$data["empID"];
  $action = $data["action"];
  
  $user = $userRepo->identify($empID);

  switch ($action) {
    case "activate":
      $user->isActive = true;
      break;
    case "deactivate":
      $user->isActive = false;
      break;
    default:
      throw new Exception("Invalid user action: $action");
  }
  
  $userRepo->update($user);

  systemLog(
    "modified user $empID",
    [
      "action" => $action,
      "object" => "user",
      "empID" => $empID,
    ]
  );  
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;