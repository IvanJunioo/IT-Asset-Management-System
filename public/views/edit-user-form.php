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
  <link rel="stylesheet" href="<?= BASE_URL ?>css/forms.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/table.css">
  <style>
    main {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      padding: 2rem;
      align-items: start;
      max-width: 1400px;
      margin: 0 auto;
    }

    main > div:first-child form {
      width: 100%;
      box-sizing: border-box;
    }

    .assignment-table {
      margin-bottom: 1rem;
    }

    /* Column Width Adjustments */
    #prnum { width: 40%; }
    #assDate { width: 30%; }
    #stat { width: 20%; }

    /* Responsive: Stack columns on smaller screens */
    @media (max-width: 1024px) {
      main {
        grid-template-columns: 1fr;
      }
      
      main > div:first-child form {
        width: 100%;
      }
    }
  </style>
<body>
    <?php include __DIR__ . '/../partials/header.php'?>

    <main>
      <div class="card">
        <h3>Edit User Details</h3>
        <?php include __DIR__ . '/user-form.php'?>
      </div>
    
      <div class="card">
        <h3>Assets Assigned</h3>
        <table class="assignment-table">
          <thead>
            <tr>
              <th id="prnum"><span>Property Number</span></th>
              <th id="assDate"><span>Assigned On</span></th>
              <th id="stat"><span>Status</span></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

        <button id="add-assignment-button">
          <i class="material-icons">add</i>
          Assign
        </button>
        <button id="export-assignment">
          <i class="material-icons">ios_share</i>
          Export
        </button>
      </div>

      <div class="card">
        <h3>Recent Activity</h3>
        <?php include __DIR__ . '/act-log.php'?>
      </div>    
    </main>
    
    <?php include __DIR__ . '/../partials/footer.php'?>
    
    <script src="<?= BASE_URL ?>script/edit-user.js" type="module" defer></script>
</body>
</html>