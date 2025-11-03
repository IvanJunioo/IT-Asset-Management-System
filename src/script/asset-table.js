document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const assetTableBody = document.querySelector('.asset-table tbody');
  const multiSelectButton = document.getElementById("multi-select");
  const currentPage = window.location.pathname;

  let isMultiSelect = false;
  
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
      
      tr += currentPage.includes("asset-manager") ? `
      <td class="actions">
        <button class="action-btn">
          <span class="material-icons">more_horiz</span>
        </button>

        <div class="action-menu">
          <a href="asset-form.php" class="menu-item" id="modify-action">Modify</a>
          <a href="asset-form.php" class="menu-item" id="delete-action">Delete</a>
          <a href="asset-form.php" class="menu-item" id="assign-action">Assign</a>
        </div>
      </td>` : ``
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
  
  document.querySelectorAll(".action-btn").forEach(btn => {
    btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const actionsCell = btn.closest(".actions");
        actionsCell.classList.toggle("show-menu");
      });
  });

  document.addEventListener("click", () => {
      document.querySelectorAll(".actions.show-menu")
      .forEach(cell => cell.classList.remove("show-menu"));
  });

  multiSelectButton.addEventListener("click", () => {
    isMultiSelect = !isMultiSelect
    const icon = multiSelectButton.querySelector(".material-icons");
    icon.textContent = isMultiSelect? "check_box" : "check_box_outline_blank";
  });
})
