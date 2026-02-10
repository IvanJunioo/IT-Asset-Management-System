<?php 
  $REQUIRED_ROLES = ["Faculty"];
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
  <?php include __DIR__ . '/../partials/asset-styles.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/table.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/asset.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main class="asset-page">
    <?php include __DIR__ . '/asset-page.php'?>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>