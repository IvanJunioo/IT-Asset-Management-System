document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const assetTableBody = document.querySelector('.asset-table tbody');
  const filterBox = document.getElementById("filter-box");
  
  function fetchAssets() {    
    const searchFilters = searchInput.value;
    const statusFilters = [...filterBox.querySelectorAll("input[name='status']:checked")].map(cb => cb.value);
    
    fetch("../handlers/asset-table.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${encodeURIComponent(searchFilters)}&status=${encodeURIComponent(statusFilters)}`,
    })
    .then(res => res.json())
    .then(data => {
      showAssets(data);
      assetTableBody.dispatchEvent(new CustomEvent("assetsLoaded"))
    })
    .catch(err => console.error("Error fetching assets: ", err));
  }
  
  function showAssets(assets) {
    // Add another header
    const hr = document.querySelector(".asset-table thead tr");
    if (!hr.querySelector("#actionsth")) {
      const actionsth = document.createElement("th");
      actionsth.id = "actionsth";
      hr.appendChild(actionsth);
    }

    assetTableBody.innerHTML = "";
    
    for (const asset of assets) {
      const tr = document.createElement('tr');

      // store asset data locally
      tr.dataset.propNum = asset.PropNum;
      tr.dataset.procNum = asset.ProcNum; 
      tr.dataset.purchaseDate = asset.PurchaseDate; 
      tr.dataset.specs = asset.Specs; 
      tr.dataset.price = asset.Price; 
      tr.dataset.status = asset.Status; 
      tr.dataset.assignedTo = asset.AssignedTo; 

      for (const col of [
        asset.PropNum,
        asset.ProcNum,
        asset.PurchaseDate,
        asset.Specs,
        asset.Price,
        asset.Status,
        asset.AssignedTo,        
      ]) {
        const td = document.createElement("td");
        td.textContent = col;
        tr.appendChild(td);
      }

      // view button
      const viewBtn = document.createElement("button");
      viewBtn.className = "select-btn";
      viewBtn.textContent = "View";
      tr.appendChild(document.createElement("td").appendChild(viewBtn)); 

      assetTableBody.appendChild(tr);
    }

    // only one event listener for the whole table
    assetTableBody.addEventListener("click", (e) => {
      const tr = e.target.closest("tr");
      if (!tr) return;

      if (e.target.closest(".select-btn")) {
        fetch("../handlers/fetch-asset.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `search=${tr.dataset.propNum}`,
        })
        .then(res => res.json())
        .then(data => {
          sessionStorage.setItem("viewAssetData", JSON.stringify(data));
          window.location.href = "../views/asset-view.php";
        })
        .catch(err => console.error("Error fetching asset: ", err));
        return;
      }
    });    
  }

  fetchAssets();
  
  searchInput.addEventListener("input", fetchAssets);
  searchButton.addEventListener("click", () => {
    fetchAssets();
    searchInput.value = "";
  }); 
  
  filterBox.querySelectorAll("input[name='status']").forEach(cb => cb.addEventListener("change", fetchAssets));
  
  filterBox.querySelector("button[id='apply-filter']").addEventListener("click", () => {
    filterBox.querySelectorAll("input[name='status']").forEach(cb => cb.checked = false);
    fetchAssets();
  });

});
