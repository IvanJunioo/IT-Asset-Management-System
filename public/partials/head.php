<?php
  if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
  }

  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  
  if (!isset($_SESSION['logged_in'])) {
    echo json_encode("User not logged in");
    exit;
  }

  $userFName = $_SESSION['user_fname'] ?? '';
  $userMName = $_SESSION['user_mname'] ?? '';
  $userLName = $_SESSION['user_lname'] ?? '';
  $privilege = $_SESSION['privilege'] ?? '';
  $privilege = $privilege == "SuperAdmin" ? "Super Admin" : $privilege
?>

<!-- head.php -->
<head>
    <meta charset="UTF-8">
    <title>IAMS</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/general.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/navigation.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/footer.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>