<?php 
  $REQUIRED_ROLES = ["Admin", "SuperAdmin"];
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
  <link rel="stylesheet" href="public/css/forms.css">
  <link rel="stylesheet" href="public/css/asset.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main class="asset-form">
    <?php include __DIR__ . '/asset-form.php'?>
  </main>
  
  <?php include __DIR__ . '/../partials/footer.php'?>
  
  <script src="public/script/add-asset.js" type="module" defer></script>
</body>
</html>