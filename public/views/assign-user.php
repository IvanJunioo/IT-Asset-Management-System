<?php 
  $REQUIRED_ROLES = ["SuperAdmin", "Faculty"];
  if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
  }
  require_once '../../src/utilities/auth-guard.php';
  require_once '../../src/utilities/role-guard.php';

  requireRole(allowedRoles: $REQUIRED_ROLES ?? []);
?>

<!DOCTYPE html>
<html lang="en">
    <?php include __DIR__ . '/../partials/head.php'?>
    <link rel="stylesheet" href="/../../public/css/table-view.css">
    <link rel="stylesheet" href="/../../public/css/user-table.css">
    <link rel="stylesheet" href="/../../public/css/filters.css">
    <link rel="stylesheet" href="/../../public/css/user.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main class="user-page">
    <?php include __DIR__ . '/user-page.php'?>
  </main>
  
  <?php include __DIR__ . '/../partials/footer.php'?>
  
  <script src="/../../public/script/assign-user.js" type="module" defer></script>
</body>
</html>