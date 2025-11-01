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

      <script>
        fetch("../handlers/asset-table.php", {method: "POST"})
          .then(res => res.json())
          .then(data => {
            const assetTableBody = document.querySelector('.asset-table tbody');
            assetTableBody.innerHTML = "";

            for (const asset of data) {
              const tr = document.createElement('tr');
              tr.innerHTML = `
                <td>${asset.PropNum}</td>
                <td>${asset.ProcNum}</td>
                <td>${asset.PurchaseDate}</td>
                <td>${asset.Specs}</td>
                <td>${asset.Price}</td>
                <td> ${asset.Status} </td>
                <td> - </td>
              `;
              
              assetTableBody.appendChild(tr);
            }
          })
      </script>

    </div>
    <div class="right-asset">
      <div id="filter-box">
        <div id="head-filter">
          FILTERS
        </div>

        <div id="body-filter">
          <label><input type="checkbox" id="available"> Available</label>
          <label><input type="checkbox" id="assigned"> Assigned</label>
          <label><input type="checkbox" id="condemned"> Condemned</label>
          <label><input type="checkbox" id="to-repair"> To Repair</label>
        </div>
          
        <button id="apply-filter"> Apply Filter </button>

      </div>
      <button id="export"> Export assets </button>
    </div>
  </main>
  <?php include '../partials/footer.php'?>
</body>
</html>