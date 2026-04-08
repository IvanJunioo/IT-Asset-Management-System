<?php
require_once __DIR__ . "/../config/config.php";

$page = $_GET["page"] ?? "login";

// Sanitize URI
$page = basename($page);

if (isset($pages[$page])) {  
  if (!empty($pages[$page]["roles"])) {
    require_once __DIR__ . "/../src/utilities/auth-guard.php";
    require_once __DIR__ . "/../src/utilities/role-guard.php";
    requireRole($pages[$page]["roles"]);
  }

  require_once __DIR__ . "/../src/views/{$page}.php";
}
else {
  http_response_code(404);
  echo "Page not found";
}
