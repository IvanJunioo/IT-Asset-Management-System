<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . '/../src/handler/ErrorHandler.php';

$page = $_GET["page"] ?? "login";

// Sanitize URI
$page = basename($page);

if (isset($pages[$page])) {  
  if (!empty($pages[$page]["roles"])) {
    require_once __DIR__ . "/../src/utilities/auth-guard.php";
    require_once __DIR__ . "/../src/utilities/role-guard.php";

    try {
      requireRole($pages[$page]["roles"]);
    } catch (Throwable $e) {
      ErrorHandler::handle($e);
    }
  }
  require_once __DIR__ . "/../src/views/{$page}.php";
}
else {
  ErrorHandler::handle(new RuntimeException("Page not found.", 404));
  // http_response_code(404);
  // echo "Page not found";
}
