document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const assetTableBody = document.querySelector('.asset-table tbody');
  const status = document.getElementById("body-filter").querySelectorAll("input[name='status']");
  const resetFilterButton = document.getElementById("filter-box").querySelector("button[id='apply-filter']")
  // const assetForm = document.querySelector('.add-asset-form');
  const multiSelectButton = document.getElementById("multi-select");
  // const actionButtons = document.getElementsByClassName("menu-item");
  const currentPage = window.location.pathname;

  let isMultiSelect = false;
  
  function fetchAssets() {
    const src = "../handlers/asset-table.php";

    const searchFilters = searchInput.value;
    const statusFilters = [];

    for (const cb of status) {
      if (cb.id === "available" && cb.checked) statusFilters.push("Unused");
      if (cb.id === "assigned" && cb.checked) statusFilters.push("Used");
      if (cb.id === "condemned" && cb.checked) statusFilters.push("Broken");
      if (cb.id === "to-repair" && cb.checked) statusFilters.push("InRepair");
    }
    
    fetch(src, {
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
        <td>${asset.PropNum}</td>
        <td>${asset.ProcNum}</td>
        <td>${asset.PurchaseDate}</td>
        <td>${asset.Specs}</td>
        <td>${asset.Price}</td>
        <td> ${asset.Status} </td>
        <td> - </td>
      `;
      
      if (currentPage.includes("asset-manager")) {
        const action = document.createElement("td");
        action.className = "actions";
        action.innerHTML = `
          <button class="action-btn">
            <span class="material-icons">more_horiz</span>
          </button>
          
          <div class="action-menu">
            <a class="menu-item" id="modify-action">Modify</a>
            <a class="menu-item" id="delete-action">Delete</a>
            <a class="menu-item" id="assign-action">Assign</a>
          </div>
        `
        tr.appendChild(action);
      }
      
      assetTableBody.appendChild(tr);
    }
    
    if (currentPage.includes("asset-manager")) {
      document.querySelectorAll(".action-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const menu = btn.parentElement.querySelector(".action-menu");
            if (menu.style.display == "flex") {
              menu.style.display = "none";
            } else {
              menu.style.display = "flex";
            }
          });
      });

      document.addEventListener("click", () => {
        document.querySelectorAll(".action-menu").forEach(menu => {
          menu.style.display = "none";
        });
      });

      const actionButtons = document.getElementsByClassName("menu-item");
      Array.from(actionButtons).forEach(btn => {
        btn.addEventListener("click", (e) => {
          const row = e.target.closest("tr");
          const cells = row.querySelectorAll("td");
          const propNum = cells[0].textContent.trim();
          switch (btn.id) {
            case "modify-action":
              editAssets(propNum);
              break;
            case "delete-action":
              deleteAssets(propNum);
              break;
            default: 
          }
        })
      })
    }
  }

  function editAssets(filter){
    const src = "../handlers/edit-asset.php";

    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${filter}`,
    })
    .then(res => res.json())
    .then(data => {
      localStorage.setItem("assetData", JSON.stringify(data));
      window.location.href = "../views/edit-asset-form.php";
    }
    )
    .catch(err => console.error("Error edit assets: ", err))
  }

  function deleteAssets(filter){
    const src = "../handlers/delete-asset.php";
    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${filter}`,
    }).then(res => {
      window.location.href = "../views/asset-manager.php";
    })
  }
  
  fetchAssets();
  
  searchInput.addEventListener("input", fetchAssets);
  searchButton.addEventListener("click", () => {
    fetchAssets();
    searchInput.value = "";
  });

  status.forEach(cb => cb.addEventListener("change", fetchAssets));

  resetFilterButton.addEventListener("click", () => {
    status.forEach(cb => cb.checked = false);
    fetchAssets();
  });

  multiSelectButton.addEventListener("click", () => {
    isMultiSelect = !isMultiSelect
    const icon = multiSelectButton.querySelector(".material-icons");
    icon.textContent = isMultiSelect? "check_box" : "check_box_outline_blank";
  });

  // for (const action in actionButtons){
  //   console.log(action.id);
  // }
})
