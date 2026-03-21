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
  <link rel="stylesheet" href="<?= BASE_URL ?>css/act-log.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/user-view.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>
  
  <main>
    <div class="user-card">
      <header class="log-header">
        <div class="user-profile">
          <a class="back-link">← Return</a>
          <div class="user-info">
            <h2 class="user-name"></h2>
          </div>
        </div>
        
        <div class="log-actions">
          <p class="log-count">Recent Activity</p>
        </div>
      </header>

      <div class="activity-log">
        <?php include __DIR__ . '/act-log.php'?>
      </div>    
    </div>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>

  <script src="<?= BASE_URL ?>script/user-view.js" type="module" defer></script>
</body>
</html>