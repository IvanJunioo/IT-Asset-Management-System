document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const assetTableBody = document.querySelector('.asset-table tbody');
  
  function fetchAssets(filters = "") {
    const src = "../handlers/asset-table.php";
  
    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${encodeURIComponent(filters)}`,
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
  }
  
  fetchAssets();
  
  searchInput.addEventListener("input", () => {
    fetchAssets(searchInput.value);
  });

  searchButton.addEventListener("click", () => {
    fetchAssets(searchInput.value)
    searchInput.value = "";
  });
})
