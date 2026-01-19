const leftAsset = document.querySelector(".left-asset");
const tableContainer = leftAsset.querySelector(".table-container");
const assetTable = tableContainer.querySelector(".asset-table");
const assetTableBody = assetTable.querySelector("tbody");

document.addEventListener("DOMContentLoaded", () => {
  addTableFuncs();
  addAssetAdd();
});

function viewAsset(propNum) {
  fetch("../../src/handlers/fetch-asset.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `search=${propNum}`,
  })
  .then(res => res.json())
  .then(data => {
    sessionStorage.setItem("viewAssetData", JSON.stringify(data));
    window.location.href = "../views/asset-view.php"
  })
  .catch(err => console.error("Error viewing asset: ", err))
}

function editAsset(propNum) {
  fetch("../../src/handlers/fetch-asset.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `search=${propNum}`,
  })
  .then(res => res.json())
  .then(data => {
    sessionStorage.setItem("assetData", JSON.stringify(data));
    window.location.href = "../views/edit-asset-form.php";
  })
  .catch(err => console.error("Error editing assets: ", err));
}

function deleteAsset(propNum) {
  fetch("../../src/handlers/delete-asset.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `search=${propNum}`,
  }).then(_ => {
    window.location.href = "../views/asset-manager.php";
  })
  .catch(err => console.error("Error deleting assets: ", err));
}

function assignAssets(propNums) {
  sessionStorage.setItem("assetsToAssign", JSON.stringify(propNums));
  window.location.href = "../views/assign-user.php";
}

function addTableFuncs() {
  // uses buttons instead of checkboxes to use google material icons. checks state by icon content
  const tableFuncs = document.createElement("div");
  tableFuncs.className = "table-func";
  tableFuncs.innerHTML = `
    <button id="multi-select">
      <span class="material-icons"> check_box_outline_blank </span>
    </button>
    <button id="sort-by">
      <span class="material-icons"> sort </span>
    </button>
  `;

  leftAsset.insertBefore(tableFuncs, tableContainer);
}

function addActionsButton() {  
  const hr = assetTable.querySelector("thead tr");
  if (!hr.querySelector("#actionsth")) {
    const actionsth = document.createElement("th");
    actionsth.id = "actionsth";
    hr.appendChild(actionsth);
  }

  for (const tr of assetTableBody.querySelectorAll("tr")) {
    const actionElem = document.createElement("td");
    actionElem.className = "actions";
    if (tr.dataset.status == "Condemned"){
      actionElem.innerHTML = ``;
      continue;
    }
    
    let menuHTML = `
      <button class="action-btn">
        <span class="material-icons">more_horiz</span>
      </button>
      
      <div class="action-menu">
        <a class="menu-item" data-action="view">View</a>
        <a class="menu-item" data-action="modify">Modify</a>
    `;
    if (tr.dataset.status == "ToCondemn"){
      menuHTML += `<a class="menu-item" data-action="condemn">Condemn</a>`
    } else {
      menuHTML += `<a class="menu-item" data-action="assign">Assign</a>
      </div>`
    }
    
    actionElem.innerHTML = menuHTML;

    tr.appendChild(actionElem);
  }
}

function addAssetAdd() {
  const leftAsset = document.querySelector(".left-asset");

  const assetAdd = document.createElement("a");
  assetAdd.href = "add-asset-form.php";
  assetAdd.id = "addAsset";
  assetAdd.innerHTML = `
    <span class="material-icons" id="add-asset-button">add</span>
    Add a New Asset 
  `;

  leftAsset.append(assetAdd);
}

document.addEventListener("click", (e) => {
  // Actions dropdown toggle
  const actionBtn = e.target.closest(".action-btn");
  if (actionBtn) {
    e.stopPropagation(); // prevents document from closing dropdown
    const menu = actionBtn.parentElement.querySelector(".action-menu");
    const isVisible = menu.style.display == "flex";

    document.querySelectorAll(".action-menu").forEach(m => {
      m.style.display = "none";
    });

    if (!isVisible) {
      const boundingRect = actionBtn.getBoundingClientRect();
      const gap = 8;

      menu.style.top = `${boundingRect.top - gap}px`;
      menu.style.left = `${boundingRect.right + gap}px`;
      menu.style.display = "flex";
    }
    return;
  }
  
  // Actions menu
  const menuBtn = e.target.closest(".menu-item[data-action]");
  if (menuBtn) {
    const tr = menuBtn.closest("tr");
    const propNum = tr.dataset.propNum;

    switch (menuBtn.dataset.action) {
      case "view":
        viewAsset(propNum);
        break;
      case "modify":
        editAsset(propNum);
        break;
      case "condemn": 
        if (confirm(`Condemn item ${propNum}?`)) deleteAsset(propNum);
        break;
      case "assign":
        assignAssets([propNum]);
        break;
    }
    return;
  }

  // Closes actions menu
  document.querySelectorAll(".action-menu").forEach(menu => {
    menu.style.display = "none";
  });
});


assetTableBody.addEventListener("assetsLoaded", () => {  
  let selectedRows = new Set();

  function selectRow(tr) {
    selectedRows.add(tr);
    tr.querySelector(".material-icons").textContent = "check_box";
  }

  function deselectRow(tr) {
    selectedRows.delete(tr);
    tr.querySelector(".material-icons").textContent = "check_box_outline_blank";
  }

  // Replace view buttons
  for (const tr of assetTableBody.querySelectorAll("tr")) {
    tr.lastElementChild.remove();
  }
  addActionsButton();
  
  // Handles all table func clicks dynamically
  const tableFuncs = leftAsset.querySelector(".table-func");
  tableFuncs.addEventListener("click", (e) => {
    if (e.target.closest(".assign")) {
      if (selectedRows.size === 0) return;

      assignAssets([...selectedRows].map(tr => tr.dataset.propNum));
      return;
    }

    if (e.target.closest(".delete")) {
      if (selectedRows.size === 0) return;
      if (!confirm(`Condemn ${selectedRows.size} item(s)?`)) return;
      
      for (const tr of selectedRows) {
        deleteAsset(tr.dataset.propNum);
      }
      return;
    }

    if (e.target.closest("#multi-select")) {
      const multiSelectIcon = e.target.closest("#multi-select").querySelector(".material-icons");

      if (multiSelectIcon.textContent.trim() === "check_box_outline_blank") {
        // Add select-all button
        const hr = assetTable.querySelector("thead tr");
        hr.lastElementChild.innerHTML = `
          <button id="select-all">
            <span class="material-icons"> select_all </span>
          </button>
        `;
        
        // Add checkbox per row
        for (const tr of assetTableBody.querySelectorAll("tr")) {
          tr.lastElementChild.innerHTML = `
          <button class="selectable-row">
            <span class="material-icons"> check_box_outline_blank </span>
          </button>
          `;
        }
        
        // Add assign table func
        const assignButton = document.createElement("button");
        assignButton.className = "assign";
        assignButton.innerHTML = `<span class="material-icons">assignment_ind</span>`;
        if (!tableFuncs.querySelector(".assign")) tableFuncs.prepend(assignButton);
        
        // Add delete table func
        const deleteButton = document.createElement("button");
        deleteButton.className = "delete";
        deleteButton.innerHTML = `<span class="material-icons">delete</span>`;
        if (!tableFuncs.querySelector(".delete")) tableFuncs.prepend(deleteButton);
        
        multiSelectIcon.textContent = "check_box";
        return;
      } 

      if (multiSelectIcon.textContent.trim() === "check_box") {
        // Remove select-all button
        document.querySelectorAll("#select-all").forEach(elem => {
          elem?.remove();
        })

        // Remove checkbox per row
        for (const tr of assetTableBody.querySelectorAll("tr")) {
          tr.querySelector(".selectable-row")?.closest("td").remove();
        }
    
        // Remove extra table funcs
        document.querySelector(".table-func .assign")?.remove();
        document.querySelector(".table-func .delete")?.remove();
        
        addActionsButton();

        multiSelectIcon.textContent = "check_box_outline_blank";
        return;
      }
    }
  });

  // Handles all table clicks dynamically
  tableContainer.addEventListener("click", (e) => {
    if (e.target.closest("#select-all")) {
      const rows = assetTableBody.querySelectorAll("tr");

      if (selectedRows.size === rows.length) {
        for (const tr of rows) {
          deselectRow(tr);
        }
      }
      else {
        for (const tr of rows) {
          selectRow(tr);
        }
      }
      return;
    }

    if (e.target.closest(".selectable-row")) {
      const tr = e.target.closest("tr");
      if (selectedRows.has(tr)) {
        deselectRow(tr);
      } else {
        selectRow(tr);
      }
      return;
    }
  })
});
