<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . '/../src/handlers/error.php';

$page = $_GET["page"] ?? "login";

// Sanitize URI
$page = basename($page);
$systemPages = ['login', 'error'];

if (!isset($pages[$page])) {
  ErrorHandler::handle(new RuntimeException("Page not found.", 404));
  exit;
}

if (!in_array($page, $systemPages)) {
  if (!empty($pages[$page]["roles"])) {

    require_once __DIR__ . "/../src/utilities/auth-guard.php";
    require_once __DIR__ . "/../src/utilities/role-guard.php";

    requireRole($pages[$page]["roles"]);
  }
}

require_once __DIR__ . "/../src/views/{$page}.php";
exit;