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
  <link rel="stylesheet" href="/../../public/css/asset.css">
	<link rel="stylesheet" href="/../../public/css/asset-view.css">
<body>
  <!-- menu -->
  <?php include __DIR__ . '/../partials/header.php'?>

  <!-- asset-page -->
  <main class="asset-page">
		<div class="asset-card">
			<h2>Asset Details</h2>
			<div class="asset-info">
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
				<div id='alog'>
					<b>Asset Log:</b> 
          <?php include __DIR__ . '/act-log.php'?>
				</div>
			</div>
		</div>

		<script src="/../../public/script/view-asset.js" type="module" defer></script>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>