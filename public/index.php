<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . '/../src/handlers/error.php';

$page = $_GET["page"] ?? "login";

// Sanitize URI
$page = basename($page);
$systemPages = ['login', 'error'];
$errorHandler = new ErrorHandler();

if (!isset($pages[$page])) {
  $errorHandler->handle(new RuntimeException("Page not found.", 404));
  exit;
}

if (!in_array($page, $systemPages)) {
  if (!empty($pages[$page]["roles"])) {

    require_once __DIR__ . "/../src/utilities/auth-guard.php";
    require_once __DIR__ . "/../src/utilities/role-guard.php";
    try {
      requireRole($pages[$page]["roles"]);
    } catch (Throwable $e) {
      $errorHandler->handle($e);
    }
    
  }
}

require_once __DIR__ . "/../src/views/{$page}.php";
exit;