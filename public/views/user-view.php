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
	<link rel="stylesheet" href="<?= BASE_URL ?>css/user-view.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/table.css">
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
      </header>

      <div class="table-container">
        <h3>Assets Assigned</h3>
        <table class="asset-table">
          <thead>
            <tr>
              <th id="pnum"><span>Procurement Number</span></th>
              <th id="prnum"><span>Property Number</span></th>
              <th id="pdate"><span>Purchase Date</span></th>
              <th id="specs"><span>Detailed Specification</span></th>
              <th id="price"><span>Price (₱)</span></th>
              <th id="stat"><span>Status </span></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

        <button class="function">Assign New Asset(s)</button>
      </div>

      <div class="table-container">
        <h3>Recent Activity</h3>
        <?php include __DIR__ . '/act-log.php'?>
      </div>    
    </div>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>

  <script src="<?= BASE_URL ?>script/user-view.js" type="module" defer></script>
</body>
</html>