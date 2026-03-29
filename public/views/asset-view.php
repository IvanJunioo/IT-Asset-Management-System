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
  <?php include __DIR__ . '/../partials/asset-styles.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/asset.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/asset-view.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/table.css">

  <style>
    main {
      display: grid;
      place-items: center;
      grid-template-columns: 1fr;
      gap: 20px;
    }    
  </style>
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main>
		<div id="asset-info" class="card">
			<h3>Asset Details</h3>
      <div id='pnum'>
        <b>Property Number:</b>
      </div>
      <div id='prnum'>
        <b>Procurement Number:</b>
      </div>
      <div id='snum'>
        <b>Serial Number:</b>
      </div>
      <div id='pdate'>
        <b>Purchase Date:</b>
      </div>
      <div id='specs'>
        <b>Specs:</b>
      </div>
      <div id='remarks'>
        <b>Remarks:</b>
      </div>
      <div id='desc'>
        <b>Description:</b>
      </div>
      <div id='stats'>
        <b>Status:</b> 
      </div>
      <div id='price'>
        <b>Price (₱):</b> 
      </div>
      <div id='sd_url'>
        <b>Support Docs URL:</b> 
      </div>
		</div>

    <div id="alog" class="card">
      <h3>Asset Log:</h3> 
      <?php include __DIR__ . '/act-log.php'?>
    </div>

		<script src="<?= BASE_URL ?>script/view-asset.js" type="module" defer></script>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>