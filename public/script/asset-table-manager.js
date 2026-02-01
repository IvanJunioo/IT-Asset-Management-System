const leftAsset = document.querySelector(".left-asset");
const tableContainer = leftAsset.querySelector(".table-container");
const assetTable = tableContainer.querySelector(".asset-table");
const assetTableBody = assetTable.querySelector("tbody");

let selectedRows = new Set();

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("export").remove();
  const reportBtn = document.createElement("button");
  reportBtn.id = "report";
  reportBtn.className = "generate";
  reportBtn.textContent = "Generate Report";
  document.querySelector(".right-asset").appendChild(reportBtn);

  reportBtn.addEventListener('click', () => {
  const modal = `
    <div id="reportModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">Generate Report On
        </div>
        <div class="modal-body">
          <button class="report-option" data-type = "assigned-p"> 
            All Personal Assigned Assets
          </button>
          <button class="report-option" data-type="tocondemn"> 
            All Assets to be Condemned
          </button>
          <button class="report-option" data-type="unassigned">
            All Unassigned/Returned Assets
          </button>
        </div>
        <button id="closeModal" class="btn-cancel">Cancel</button>
      </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', modal);

    document.getElementById('closeModal').addEventListener('click', () => {
        document.getElementById('reportModal').remove();
    });

    const reportModal = document.getElementById('reportModal');
    reportModal.addEventListener('click', (e)=> {
      const target = e.target.closest(".report-option");

      if (target) {
        const report = target.dataset.type;

        switch (report){
          case "assigned-p":
            window.open("../../src/handlers/export-asset.php", "_blank");
            break;
          case "unassigned":
            window.open("../../src/handlers/export-asset-status.php?status=Unassigned", "_blank");
            break;
          case "tocondemn":
            window.open("../../src/handlers/export-asset-status.php?status=ToCondemn", "_blank");
            break;
        }

        document.getElementById('reportModal').remove();
      }
    })

  reportModal.style.display = 'block';
  });
  addTableFuncs();

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
        if (tr.dataset.status != "ToCondemn"){
          continue;
        }
        deleteAsset(tr.dataset.propNum);
      }
      return;
    }

    if (e.target.closest(".return")) {
      if (selectedRows.size === 0) return;
      returnAsset([...selectedRows].map(tr => tr.dataset.propNum));
    }

    if (e.target.closest("#multi-select")) {
      const multiSelectIcon = e.target.closest("#multi-select").querySelector(".material-icons");

      if (multiSelectIcon.textContent.trim() === "check_box_outline_blank") {
        addSelectAll();
        addCheckboxes();
        addAssignButton();
        addReturnButton();
        addCondemnButton();
        
        multiSelectIcon.textContent = "check_box";
        return;
      } 

      if (multiSelectIcon.textContent.trim() === "check_box") {
        resetMultiSelect();
        addActionsButton();
        return;
      }
    }
  });

  addAssetAdd();
});

assetTableBody.addEventListener("assetsLoaded", () => {  
  // Replace view buttons
  for (const tr of assetTableBody.querySelectorAll("tr")) {
    tr.lastElementChild.remove();
  }
  addActionsButton();

  const multiSelectIcon = document.querySelector("#multi-select").querySelector(".material-icons")
  if (multiSelectIcon.textContent.trim() === "check_box") {
    updateSelectedRows();
    addCheckboxes();
    return;
  } else {
    resetMultiSelect();
  }
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
    window.location.href = "../views/asset-view.php";
  })
  .catch(err => console.error("Error viewing asset: ", err));
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

function returnAsset(propNums) {
  sessionStorage.setItem("assetsToReturn", JSON.stringify(propNums));
  window.location.href = "../views/return-form.php";
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
  const tableFuncsClass = document.querySelector(".table-func");
  tableFuncsClass.insertAdjacentHTML("afterbegin", `
    <button id="multi-select">
      <span class="material-icons"> check_box_outline_blank </span>
    </button>
  `);
}

function addSelectAll() {
  const hr = assetTable.querySelector("thead tr");
  hr.lastElementChild.innerHTML = `
    <button id="select-all">
      <span class="material-icons"> select_all </span>
    </button>
  `;
}

function addCheckboxes() {
  for (const tr of assetTableBody.querySelectorAll("tr")) {
    if (tr.dataset.status === "Condemned"){
      continue;
    }

    const icon = selectedRows.has(tr) ? "check_box" : "check_box_outline_blank";
    tr.lastElementChild.innerHTML = `
    <button class="selectable-row">
      <span class="material-icons"> ${icon} </span>
    </button>
    `;
  }
}

function addAssignButton() {
  const tableFuncs = leftAsset.querySelector(".table-func");

  const assignButton = document.createElement("button");
  assignButton.className = "assign";
  assignButton.innerHTML = `<span class="material-icons">assignment_ind</span>`;
  if (!tableFuncs.querySelector(".assign")) tableFuncs.prepend(assignButton);
}

function addReturnButton() {
  const tableFuncs = leftAsset.querySelector(".table-func");

  const returnButton = document.createElement("button");
  returnButton.className = "return";
  returnButton.innerHTML = `<span class="material-icons">assignment_return</span>`;
  if (!tableFuncs.querySelector(".return")) tableFuncs.prepend(returnButton);
}

function addCondemnButton() {
  const tableFuncs = leftAsset.querySelector(".table-func");

  const deleteButton = document.createElement("button");
  deleteButton.className = "delete";
  deleteButton.innerHTML = `<span class="material-icons">delete</span>`;
  if (!tableFuncs.querySelector(".delete")) tableFuncs.prepend(deleteButton);
}

function updateSelectedRows() {
  var toAdd = new Set();
  var toDel = new Set();

  for (const tr1 of assetTableBody.querySelectorAll("tr")) {
    if (tr1.dataset.status === "Condemned"){
      continue;
    }

    for (const tr2 of selectedRows) {
      if (tr2.dataset.propNum == tr1.dataset.propNum) {
        toDel.add(tr2);
        toAdd.add(tr1);
      }
    }
  }

  for (const tr of toDel) {
    selectedRows.delete(tr)
  }

  for (const tr of toAdd) {
    selectedRows.add(tr)
  }
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
    if (tr.dataset.status === "Condemned"){
      continue;
    }

    if (tr.querySelector("td.actions")) {
      continue;
    }
    
    actionElem.className = "actions";
    
    let menuHTML = `
      <button class="action-btn">
        <span class="material-icons">more_horiz</span>
      </button>
      
      <div class="action-menu">
        <a class="menu-item" data-action="view">View</a>
        <a class="menu-item" data-action="modify">Modify</a>
    `;
    if (tr.dataset.status === "ToCondemn"){
      menuHTML += `<a class="menu-item" data-action="condemn">Condemn</a>`
    }

    if (tr.dataset.status === "Assigned"){
      menuHTML += `<a class="menu-item" data-action="return">Return</a>`
    }

    if (tr.dataset.status === "Unassigned"){
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

function resetMultiSelect() {
  const multiSelectBtn = document.querySelector("#multi-select");
  const icon = multiSelectBtn?.querySelector(".material-icons");

  // Reset icon
  if (icon) {
    icon.textContent = "check_box_outline_blank";
  }

  // Remove select-all button
  document.querySelectorAll("#select-all").forEach(btn => btn.remove());

  // Remove row checkboxes
  assetTableBody.querySelectorAll(".selectable-row")
    .forEach(btn => btn.closest("td")?.remove());

  // Remove extra table funcs
  document.querySelector(".table-func .assign")?.remove();
  document.querySelector(".table-func .delete")?.remove();
  document.querySelector(".table-func .return")?.remove();

  // Reset tracking
  selectedRows.clear();
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
      case "return":
        returnAsset([propNum]);
    }
    return;
  }

  // Closes actions menu
  document.querySelectorAll(".action-menu").forEach(menu => {
    menu.style.display = "none";
  });
});

function updateTableButtons() {
  const tableFuncs = leftAsset.querySelector(".table-func");
  const assignButton = tableFuncs.querySelector(".assign");
  const returnButton = tableFuncs.querySelector(".return");
  const deleteButton = tableFuncs.querySelector(".delete");

  if (!selectedRows.size) {
    if (assignButton) assignButton.style.display = "flex";
    if (returnButton) returnButton.style.display = "flex";
    if (deleteButton) deleteButton.style.display = "flex";
    return;
  }

  let allUnassigned = true;
  for (const tr of selectedRows) {
    if (tr.dataset.status !== "Unassigned") {
      allUnassigned = false;
      break;
    }
  }
  if (assignButton) assignButton.style.display = allUnassigned ? "flex" : "none";

  let allAssigned = true;
  for (const tr of selectedRows) {
    if (tr.dataset.status !== "Assigned") {
      allAssigned = false;
      break;
    }
  }
  if (returnButton) returnButton.style.display = allAssigned ? "flex" : "none";

  let allToCondemn = true;
  for (const tr of selectedRows) {
    if (tr.dataset.status !== "ToCondemn") {
      allToCondemn = false; 
      break;
    }
  }
  if (deleteButton) deleteButton.style.display = allToCondemn ? "flex" : "none";
}

function selectRow(tr) {
  selectedRows.add(tr);
  const icon = tr.querySelector(".material-icons");
  if (icon) icon.textContent = "check_box";
  updateTableButtons();
}

function deselectRow(tr) {
  selectedRows.delete(tr);
  const icon = tr.querySelector(".material-icons");
  if (icon) icon.textContent = "check_box_outline_blank";
  updateTableButtons();
}

// Handles all table clicks dynamically
tableContainer.addEventListener("click", (e) => {
  if (e.target.closest("#select-all")) {
    const rows = assetTableBody.querySelectorAll("tr");
    const activeRows = [...rows].filter(
      tr => tr.dataset.status !== "Condemned"
    );

    if (selectedRows.size === activeRows.length) {
      for (const tr of rows) {
        deselectRow(tr);
      }
    }
    else {
      for (const tr of rows) {
        if (tr.dataset.status==="Condemned") {
          continue;
        }
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


