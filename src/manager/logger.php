<?php

declare (strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/actlog.php';
require_once __DIR__ . '/../repos/user.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$logRepo = new ActLogRepo($pdo);

function systemLog(
  string $log,
  array $metadata,
): void {
  global $logRepo;
  
  $empID = $_SESSION["user_id"];
  $logRepo->add(
    new User(
      empID: $empID, 
      name: new Fullname(), 
      email: "", 
      privilege: UserPrivilege::Staff
    ), // temporary User data object
    "User $empID $log", 
    $metadata
  );
}
