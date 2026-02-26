<?php
  $REQUIRED_ROLES = ["SuperAdmin"];
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
  <link rel="stylesheet" href="/../../css/forms.css">
  <link rel="stylesheet" href="/../../css/user.css">
    
<body>
    <?php include __DIR__ . '/../partials/header.php'?>

    <main class="user-form">
      <?php include __DIR__ . '/user-form.php'?>
    </main>
    
    <?php include __DIR__ . '/../partials/footer.php'?>
    
    <script src="/../../script/edit-user.js" type="module" defer></script>
</body>
</html>