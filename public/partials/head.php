<?php
  if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
  }
  require_once '../../src/utilities/auth-guard.php';
  require_once '../../src/utilities/role-guard.php';

  requireRole(allowedRoles: $REQUIRED_ROLES ?? []);
?>

<!-- head.php -->
<head>
    <meta charset="UTF-8">
    <title>IAMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/general.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/navigation.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/footer.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/paging.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>