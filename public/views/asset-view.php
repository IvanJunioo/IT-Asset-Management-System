<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/asset.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>public/css/asset-view.css">
<body>
  <!-- menu -->
  <?php include '../partials/header.php'?>

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
					<b>Price:</b> 
				</div>
				<div id='sd_url'>
					<b>Support Docs URL:</b> 
				</div>
				<div id='alog'>
					<b>Asset Log:</b> 
				</div>
			</div>
		</div>

		<script src= ../script/view-asset.js defer></script>
  </main>

  <?php include '../partials/footer.php'?>
</body>
</html>