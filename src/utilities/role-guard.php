<?php
include_once __DIR__ . "/../repos/user.php";
require_once __DIR__ . '/../exceptions/ErrorHandler.php';

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
    // session_destroy();
    // header("Location: " . BASE_URL . "index.php?page=login&error=user_deactivated");
    // exit("Inactive user status");
  }
}
