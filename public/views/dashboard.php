<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  $REQUIRED_ROLES = ["Faculty", "Admin", "SuperAdmin"];
  if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
  }
  require_once '../../src/utilities/auth-guard.php';
  require_once '../../src/utilities/role-guard.php';

  requireRole(allowedRoles: $REQUIRED_ROLES ?? []);
?>


<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php';?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/dashboard.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>
    
  <main class="dashboard">
      <h1 class="dashboard-title">
        <?= htmlspecialchars("Hello, $privilege!") ?>
      </h1>

      <hr>

      <h2 class="dashboard-text">
        Dashboard
      </h2>

      <section class="dashboard-cards">
        <?php foreach ($dashboardIslands as $label => $data): ?>
          <?php if (in_array($privilege, $data['roles'])): ?>
            <a href="<?=htmlspecialchars($data['url'])?>" class="card">
              <h2><?=htmlspecialchars($label)?></h2>
              <p><?=htmlspecialchars($data['body'])?></p>
            </a>
          <?php endif?>
        <?php endforeach?>
      </section>

      <div class="dashboard-bottom">
        <div class="recent-activity">
          <h2>Recent Activity</h2>
          <?php include '../views/act-log.php'?>
        </div>

        <section id="asset-distribution">
          <a href="./assets.php" class="distr-card" id="total-assets">
            <p>Assets</p>
          </a>

          <a href="./assets.php" class="distr-card" id="total-users">
            <p>Users</p>
          </a>

          <a href="./assets.php" class="distr-card" id="avail-assets">
            <p>Available Assets</p>
          </a>

          <a href="./assets.php" class="distr-card" id="active-users">
            <p>Active Users</p>
          </a>

        </section>
      </div>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>

  <script src="<?= BASE_URL ?>public/script/dashboard.js" type="module" defer></script>
</body>
</html>