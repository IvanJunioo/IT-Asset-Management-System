<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
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
                <th></th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
          
        <a href = "asset-form.php" id="addAsset"> <button> Add Asset</button> </a>
        
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
                <td class="actions">
                  <button class="action-btn">
                    <span class="material-icons">more_horiz</span>
                  </button>

                  <div class="action-menu">
                    <a href="asset-form.php" class="menu-item">Modify</a>
                    <a href="asset-form.php" class="menu-item">Delete</a>
                  </div>
                </td>
              `;
              
              assetTableBody.appendChild(tr);
            }
          })
          // const assetTableBody = document.querySelector('.asset-table tbody');

          // for (let i = 0; i < 30; i++) {
          //     const tr = document.createElement('tr');

          //     // const assetName = getRandomItem(assetNames) + " #" + (Math.floor(Math.random() * 900) + 100);
          //     // const status = getRandomItem(statuses);
          //     // const assignedTo = status === "Available" ? "-" : getRandomItem(names);
          //     // const infoLink = "https://example.com/assets/" + assetName.replace(/\s+/g, '-').toLowerCase();

          //     tr.innerHTML = `
          //         <td>${assetName}</td>
          //         <td> - </td>
          //         <td> - </td>
          //         <td> - </td>
          //         <td> - </td>
          //         <td> ${1000} </td>
          //         <td> ${status} </td>
          //         <td> ${assignedTo} </td>


          //     assetTableBody.appendChild(tr);
          // }

          // document.addEventListener("DOMContentLoaded", () => {
          //     document.querySelectorAll(".action-btn").forEach(btn => {
          //         btn.addEventListener("click", (e) => {
          //             e.stopPropagation();
          //             const actionsCell = btn.closest(".actions");
          //             actionsCell.classList.toggle("show-menu");
          //         });
          //     });

          //     document.addEventListener("click", () => {
          //         document.querySelectorAll(".actions.show-menu")
          //         .forEach(cell => cell.classList.remove("show-menu"));
          //     });
          // });
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