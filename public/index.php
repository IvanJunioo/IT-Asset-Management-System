<?php
$pages = [
  "activity-log"    => ["roles" => ["Faculty", "Staff", "Admin", "SuperAdmin"]],
  "add-asset-form"  => ["roles" => ["Admin", "SuperAdmin"]],
  "add-user-form"   => ["roles" => ["SuperAdmin"]],
  "asset-manager"   => ["roles" => ["Admin", "SuperAdmin"]],
  "asset-view"      => ["roles" => ["Faculty", "Staff", "Admin", "SuperAdmin"]],
  "assets"          => ["roles" => ["Faculty", "Staff"]],
  "assign-asset"    => ["roles" => ["Admin", "SuperAdmin"]],
  "assign-user"     => ["roles" => ["Admin", "SuperAdmin"]],
  "assignment-form" => ["roles" => ["Admin", "SuperAdmin"]],
  "dashboard"       => ["roles" => ["Faculty", "Staff", "Admin", "SuperAdmin"]],
  "edit-asset-form" => ["roles" => ["Admin", "SuperAdmin"]],
  "edit-user-form"  => ["roles" => ["SuperAdmin"]],
  "login"           => ["roles" => []], // Allows everyone
  "return-form"     => ["roles" => ["Admin", "SuperAdmin"]],
  "user-manager"    => ["roles" => ["SuperAdmin"]],
  "users"           => ["roles" => ["Admin"]],
];

$page = $_GET["page"] ?? "login";

// Sanitize URI
$page = basename($page);

if (isset($pages[$page])) {
  require_once __DIR__ . "/../config/config.php";
  
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
