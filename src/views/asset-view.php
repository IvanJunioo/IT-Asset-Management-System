<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/asset.css">
	<link rel="stylesheet" href="<?= BASE_URL ?>css/asset-view.css">
<body>
  <!-- menu -->
  <?php include '../partials/header.php'?>

  <!-- asset-page -->
  <main class="asset-page">
    <!-- <div class="left-asset">
      <div class="table-container">
        <table class="asset-table">
          <thead>
            <tr>
              <th> Property Number </th>
              <th> Procurement Number </th>
              <th> Purchase Date </th>
              <th> Detailed Specification </th>
              <th> Price </th>
              <th> Status  </th>
              <th> Assigned to </th>
            </tr>
          </thead>
          <tbody>
						<td>111111111111</td>
						<td>PR12345</td>
						<td>SN998877</td>
						<td>Laptop Dell Latitude</td>
						<td><span class="status unused">Unused</span></td>
						<td>₱45,000</td>
						<td></td>
					</tbody>
        </table>
      </div>
    </div> -->

		<div class="asset-card">
			<h2>Asset Details</h2>
			<div class="asset-info">
				<div class=>
					<strong>Property Number:</strong>
					<span id = 'pnum'>

					</span>
				</div>
				<div>
					<strong>Procurement Number:</strong>
					<span id = 'prnum'>

					</span>
				</div>
				<div>
					<strong>Serial Number:</strong>
					<span id = 'snum'>

					</span>
				</div>
				<div>
					<strong>Purchase Date:</strong>
					<span id = 'pdate'>

					</span>
				</div>
				<div>
					<strong>Specs:</strong>
					<span id = 'specs'>

					</span>
				</div>
				<div>
					<strong>Remarks:</strong>
					<span id = 'remarks'>

					</span>
				</div>
				<div>
					<strong>Description:</strong>
					<span id = 'desc'>

					</span>
				</div>
				<div>
					<strong>Status:</strong> 
					<span id = 'stats'>

					</span>
				</div>
				<div>
					<strong>Price:</strong> 
					<span id = 'p'>

					</span>
				</div>
				<div>
					<strong>Image URL: </strong> 
					<span id = 'img_url'>

					</span>
				</div>
				<div>
					<strong>Asset Log:</strong> 
					<span id = 'alog'>
					</span>
				</div>
			</div>
		</div>

		<script src = ../script/view-asset.js></script>
  </main>

  <?php include '../partials/footer.php'?>
</body>
</html>