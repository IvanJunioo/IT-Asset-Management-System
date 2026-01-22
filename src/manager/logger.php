<?php

declare (strict_types=1);

require_once '../../config/config.php';

require_once '../repos/actlog.php';
require_once '../repos/user.php';


function systemLog(
  string $log,
  array $metadata,
  ): void {
  global $pdo;

  $logRepo = new ActLogRepo($pdo);
  $userRepo = new UserRepo($pdo);

  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  
  $empID = $_SESSION["user_id"];
  $user = $userRepo->identify($empID);
  $logRepo->add($user, "User $empID $log", $metadata);
}
