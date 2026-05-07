<?php

function requireRole(array $allowedRoles): void {
  if (empty($allowedRoles)) return;

  if (!isset($_SESSION['privilege']) || !in_array($_SESSION['privilege'], $allowedRoles, true)) {
    throw new RuntimeException("You don't have the required privileges to access this page.", 401);
  }
}
