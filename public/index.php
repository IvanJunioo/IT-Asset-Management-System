<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . '/../src/bootstrap.php';

try {
  $page = $_GET["page"] ?? "login";
  
  // Sanitize URI
  $page = basename($page);
  
  if (!isset($pages[$page])) {
    throw new RuntimeException("Page not found.", 404);  
  }
  
  if (!empty($pages[$page]["roles"])) {
    require_once __DIR__ . "/../src/utilities/auth-guard.php";
    require_once __DIR__ . "/../src/utilities/role-guard.php";
    
    requireRole($pages[$page]["roles"]);
  }
  
  require_once __DIR__ . "/../src/views/{$page}.php";
}
catch (Throwable $e) {
  $errHand->handle($e);
}

exit;