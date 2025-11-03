<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/asset.css">
<body>
  <!-- menu -->
  <?php include '../partials/header.php'?>

  <!-- asset-page -->
  <main class="asset-page">
    <div class="left-asset">
      <div id="search-box">
        <input type="text" id="search-input" placeholder="Search asset">
        <button id="search-button"> Search </button>
      </div>
      
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
          <tbody></tbody>
        </table>
      </div>

      <script src="../script/asset-table.js"></script>

    </div>
    <div class="right-asset">
      <div id="filter-box">
        <div id="head-filter">
          FILTERS
        </div>

        <div id="body-filter">
          <label><input type="checkbox" name="status" id="available"> Available</label>
          <label><input type="checkbox" name="status" id="assigned"> Assigned</label>
          <label><input type="checkbox" name="status" id="condemned"> Condemned</label>
          <label><input type="checkbox" name="status" id="to-repair"> To Repair</label>
        </div>
          
        <button id="apply-filter"> Reset Filters </button>

      </div>
      <button id="export"> Export assets </button>
    </div>
  </main>
  <?php include '../partials/footer.php'?>
</body>
</html>