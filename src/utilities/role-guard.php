<?php
include_once __DIR__ . "/../repos/user.php";
require_once __DIR__ . '/../handlers/error.php';

function requireRole(array $allowedRoles): void {
  if (empty($allowedRoles)) return;

  if (!isset($_SESSION['privilege']) || !in_array($_SESSION['privilege'], $allowedRoles, true)) {
     throw new RuntimeException("You don't have the required privileges to access this page.", 403);
  }
}

function verifyStatus(UserRepoInterface $userRepo) {
  if (!isset($_SESSION['user_id'])) return;

  $user = $userRepo->identify($_SESSION['user_id']);

  if (!$user || !$user->isActive) {
    throw new UserInactiveException("User account is deactivated. Please contact the admin to reactivate your account.", 403);
  }
}
