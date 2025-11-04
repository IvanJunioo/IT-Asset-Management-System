document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const assetTableBody = document.querySelector('.asset-table tbody');
  const filterBox = document.getElementById("filter-box");
  
  async function fetchAssets() {
    const src = "../handlers/asset-table.php";
    
    const searchFilters = searchInput.value;
    
    const statusFilters = [];
    for (const cb of filterBox.querySelectorAll("input[name='status']")) {
      if (!cb.checked) continue;
      if (cb.id === "available") statusFilters.push("Unused");
      if (cb.id === "assigned") statusFilters.push("Used");
      if (cb.id === "condemned") statusFilters.push("Broken");
      if (cb.id === "to-repair") statusFilters.push("InRepair");
    }
    
    await fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${encodeURIComponent(searchFilters)}&status=${encodeURIComponent(statusFilters)}`,
    })
    .then(res => res.json())
    .then(data => showAssets(data))
    .catch(err => console.error("Error fetching assets: ", err))
  }
  
  
  function showAssets(assets) {
    assetTableBody.innerHTML = "";
    
    for (const asset of assets) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td data-col="PropNum">${asset.PropNum}</td>
        <td data-col="ProcNum">${asset.ProcNum}</td>
        <td data-col="PurchaseDate">${asset.PurchaseDate}</td>
        <td data-col="Specs">${asset.Specs}</td>
        <td data-col="Price">${asset.Price}</td>
        <td data-col="Status">${asset.Status} </td>
        <td data-col="AssignedTo"> - </td>
      `;
      assetTableBody.appendChild(tr);
    }
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
