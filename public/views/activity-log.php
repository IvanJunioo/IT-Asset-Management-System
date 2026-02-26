<?php 
  $REQUIRED_ROLES = ["Faculty", "Staff", "Admin", "SuperAdmin"];
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
  <link rel="stylesheet" href="/../../css/act-log.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>
  
  <main class="activity-log">
    <?php include __DIR__ . '/act-log.php'?>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>

  <script defer>
    document.getElementById("actlog-table").className = "activity-log-table";
  </script>
</body>
</html>