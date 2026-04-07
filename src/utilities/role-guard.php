<?php
include_once __DIR__ . "/../repos/user.php";

function requireRole(array $allowedRoles): void {
  if (empty($allowedRoles)) return;

  if (!isset($_SESSION['privilege']) || !in_array($_SESSION['privilege'], $allowedRoles, true)) {
    http_response_code(403);
    exit('Access denied');
  }
}

function verifyStatus(UserRepoInterface $userRepo) {
  if (!isset($_SESSION['user_id'])) return;

  $user = $userRepo->identify($_SESSION['user_id']);

  if (!$user || !$user->isActive) {
    session_destroy();
    header("Location: " . BASE_URL . "index.php?page=login&error=user_deactivated");
    exit("Inactive user status");
  }
}
