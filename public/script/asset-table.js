const leftAsset = document.querySelector(".left-asset");
const tableContainer = leftAsset.querySelector(".table-container");
const assetTable = tableContainer.querySelector(".asset-table");
const assetTableBody = assetTable.querySelector("tbody");

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const filterBox = document.getElementById("filter-box");
  const exportButton = document.getElementById("export");

  const tableFuncs = document.createElement("div");
  tableFuncs.className = "table-func";
  tableFuncs.innerHTML = `
    <button id="reverse-sort">
      <span class="material-icons">swap_vert</span>
    </button>
    <button id="sort-by">
      <span class="material-icons"> sort </span>
    </button>

    <div id="sort-menu" class="sort-menu">
      <a class="menu-item" data-sort="propNum">Property No</a>
      <a class="menu-item" data-sort="procNum">Procurement No</a>
      <a class="menu-item" data-sort="purchaseDate">Purchase Date</a>
      <a class="menu-item" data-sort="price">Price</a>
      <a class="menu-item" data-sort="assignedTo">Assigned User</a>
    </div>
  `;

  leftAsset.insertBefore(tableFuncs, tableContainer);
  
  function fetchAssets() {    
    const searchFilters = searchInput.value;
    const statusFilters = [...filterBox.querySelectorAll("input[name='status']:checked")].map(cb => cb.value);
    
    fetch("../../src/handlers/asset-table.php", {
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
        parseFloat(asset.Price).toFixed(2),
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
      const td = document.createElement("td");
      td.append(viewBtn);
      tr.append(td);
      assetTableBody.appendChild(tr);
    }

    // only one event listener for the whole table
    assetTableBody.addEventListener("click", (e) => {
      const tr = e.target.closest("tr");
      if (!tr) return;

      if (e.target.closest(".select-btn")) {
        fetch("../../src/handlers/fetch-asset.php", {
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
  
  searchInput.addEventListener("input", fetchAssets)
  filterBox.querySelector("#body-filter").addEventListener("change", fetchAssets);
  
  filterBox.querySelector("button[id='apply-filter']").addEventListener("click", () => {
    filterBox.querySelectorAll("input[name='status']").forEach(cb => cb.checked = false);
    fetchAssets();
  });

  exportButton.addEventListener("click", () => {
    window.open("../../src/handlers/export-asset.php", "_blank");
  })

});

let currentSortKey = "propNum"; // track which column is sorted
let sortOrder = "asc"; 

function sortAsset(sortKey) {
  if (!sortKey) return;
  currentSortKey = sortKey; 
  const rows = Array.from(assetTableBody.querySelectorAll("tr"));

  rows.sort((a, b) => {
    let valA = a.dataset[sortKey] || "";
    let valB = b.dataset[sortKey] || "";

    const dateA = Date.parse(valA);
    const dateB = Date.parse(valB);
    if (!isNaN(dateA) && !isNaN(dateB)) {
      return sortOrder === "asc" ? dateA - dateB : dateB - dateA;
    }

    valA = valA ? valA.toLowerCase() : ""; 
    valB = valB ? valB.toLowerCase() : "";
    if (valA < valB) return sortOrder === "asc" ? -1 : 1;
    if (valA > valB) return sortOrder === "asc" ? 1 : -1;
    return 0;
  });

  rows.forEach(tr => assetTableBody.appendChild(tr));
}

document.addEventListener("click", (e) => {
  const sortBtn = e.target.closest("#sort-by");
  if (sortBtn) {
    e.stopPropagation();
    const menu = document.querySelector("#sort-menu");
    const isVisible = menu.style.display === "flex";

    document.querySelectorAll(".sort-menu").forEach(m => m.style.display = "none");

    if (!isVisible) {
      const boundingRect = sortBtn.getBoundingClientRect();
      const gap = 8;

      menu.style.top = `${boundingRect.top - gap}px`;
      menu.style.left = `${boundingRect.right + gap}px`;
      menu.style.display = "flex";
    }
    return;
  }

  const menuBtn = e.target.closest(".menu-item[data-sort]");
  if (menuBtn) {
    sortOrder = 'asc'
    const sortKey = menuBtn.dataset.sort;
    sortAsset(sortKey);
    
  }

  const reverseBtn = e.target.closest("#reverse-sort");
  if (reverseBtn) {
    if (!currentSortKey) return;
    sortOrder = sortOrder === "asc" ? "desc" : "asc";
    sortAsset(currentSortKey);
  }

  document.querySelectorAll(".sort-menu").forEach(menu => {
    menu.style.display = "none";
  });

});


