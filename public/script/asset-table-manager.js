import { editAsset, returnAsset, condemnAsset, assignAssets} from "./asset-router.js";
import {editUser} from './user-router.js';

const leftAsset = document.querySelector(".left-asset");
const tableFuncs = leftAsset.querySelector(".table-func");
const tableContainer = leftAsset.querySelector(".table-container");
const assetTable = tableContainer.querySelector(".asset-table");
const assetTableBody = assetTable.querySelector("tbody");

let selectedRows = new Set();
let inMultiSelect = false;

addTableFuncs();

// Immediately add header for actions
const hr = assetTable.querySelector("thead tr");
if (!hr.querySelector("#actionsth")) {
  const actionsth = document.createElement("th");
  actionsth.id = "actionsth";
  hr.appendChild(actionsth);
}

addAssetAdd();

// Replace Reports Button
const reportBtn = document.createElement("button");
reportBtn.id = "report";
reportBtn.className = "generate";
reportBtn.textContent = "Generate Report";
document.getElementById("export").replaceWith(reportBtn);

// ----- EVENT LISTENERS (KEEP MINIMAL) -----
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
      case "modify":
        editAsset(propNum);
        break;
      case "condemn": 
        if (confirm(`Condemn item ${propNum}?`)) condemnAsset(propNum);
        break;
      case "assign":
        assignAssets([propNum]);
        break;
      case "return":
        returnAsset([propNum]);
    }
    return;
  }

  if (e.target.closest("#report")) {
    addReportModal();
    return;
  }

  if (e.target.closest("#closeModal")) {
    e.target.closest("#reportModal").remove();
    return;
  }

  if (e.target.closest("#reportModal")) {
    const target = e.target.closest(".report-option");
    if (!target) return;

    switch (target.dataset.type) {
      case "assigned-p":
        window.open(`${window.location.origin}/src/handlers/export-asset.php`, "_blank");
        break;
      case "unassigned":
        window.open(`${window.location.origin}/src/handlers/export-asset-status.php?status=Unassigned`, "_blank");
        break;
      case "tocondemn":
        window.open(`${window.location.origin}/src/handlers/export-asset-status.php?status=ToCondemn`, "_blank");
        break;
    }

    e.target.closest("#reportModal").remove();
    return;
  }

  // Closes actions menu
  document.querySelectorAll(".action-menu").forEach(menu => {
    menu.style.display = "none";
  });
});

tableFuncs.addEventListener("click", (e) => {
  if (e.target.closest("#assign")) {
    if (selectedRows.size === 0) return;

    assignAssets([...selectedRows].map(tr => tr.dataset.propNum));
    return;
  }

  if (e.target.closest("#delete")) {
    if (selectedRows.size === 0) return;
    if (!confirm(`Condemn ${selectedRows.size} item(s)?`)) return;
    
    for (const tr of selectedRows) {
      if (tr.dataset.status != "ToCondemn"){
        continue;
      }
      condemnAsset(tr.dataset.propNum);
    }
    return;
  }

  if (e.target.closest("#return")) {
    if (selectedRows.size === 0) return;
    returnAsset([...selectedRows].map(tr => tr.dataset.propNum));
  }

  if (e.target.closest("#multi-select")) {
    const multiSelectBtn = e.target.closest("#multi-select");
    
    multiSelectBtn.classList.toggle('active');

    setInMulSel(!inMultiSelect);
  }
});

tableContainer.addEventListener("click", (e) => {
  if (e.target.closest("#select-all")) {
    const rows = assetTableBody.querySelectorAll("tr");
    const activeRows = [...rows].filter(
      tr => tr.dataset.status !== "Condemned"
    );

    if (selectedRows.size === activeRows.length) {
      for (const tr of selectedRows) {
        deselectRow(tr);
      }
    } else {
      for (const tr of activeRows) {
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

  if (e.target.closest("a")) {
    const a = e.target.closest("a");
    const tr = e.target.closest("tr");
    if (!tr) return;

    if (a.dataset.type === "assignee") {
      editUser(tr.dataset.assignedTo);
    }
    return;
  }

  if (e.target.closest("tr") && inMultiSelect) {
    const tr = e.target.closest("tr");
    if (!tr.closest("tbody")) return;
    if (selectedRows.has(tr)) {
      deselectRow(tr);
    } else {
      selectRow(tr);
    }
    return;
  }
})

assetTableBody.addEventListener("assetsLoaded", () => {  
  // Replace view buttons
  for (const tr of assetTableBody.querySelectorAll("tr")) {
    tr.lastElementChild.remove();

    // Replace assignee with clickable link to user
    tr.lastElementChild.innerHTML = `
      <a data-type="assignee">${tr.lastElementChild.textContent.trim()}</a>
    `;
  }
  addActionsButton();

  if (inMultiSelect) {
    updateSelectedRows();
    addCheckboxes();
  } 
});

// ----- FUNCTION DEFINITIONS -----
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

function setInMulSel(val) {
  if (inMultiSelect && !val) {
    // Remove select-all button
    document.querySelectorAll("#select-all").forEach(btn => btn.remove());
  
    // Remove row checkboxes
    assetTableBody.querySelectorAll(".selectable-row")
      .forEach(btn => btn.closest("td")?.remove());
  
    // Remove extra table funcs
    document.querySelector("#assign")?.remove();
    document.querySelector("#delete")?.remove();
    document.querySelector("#return")?.remove();
  
    // Reset tracking
    selectedRows.clear();

    addActionsButton();
  }

  if (!inMultiSelect && val) {
    addSelectAll();
    addCheckboxes();
    addAssignButton();
    addReturnButton();
    addCondemnButton();
  }

  inMultiSelect = val;
  
  const multiSelectIcon = document.querySelector("#multi-select .material-icons");
  if (multiSelectIcon) multiSelectIcon.textContent = val? "check_box" : "check_box_outline_blank";
}

function addTableFuncs() {
  tableFuncs.insertAdjacentHTML("afterbegin", `
    <button id="multi-select">
      <span class="material-icons"> check_box_outline_blank </span>
      Select Multiple
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
  assignButton.id = "assign";
  assignButton.innerHTML = `<span class="material-icons">assignment_ind</span> Assign`;
  if (!tableFuncs.querySelector("#assign")) tableFuncs.prepend(assignButton);
}

function addReturnButton() {
  const tableFuncs = leftAsset.querySelector(".table-func");

  const returnButton = document.createElement("button");
  returnButton.id = "return";
  returnButton.innerHTML = `<span class="material-icons">assignment_return</span> Return`;
  if (!tableFuncs.querySelector("#return")) tableFuncs.prepend(returnButton);
}

function addCondemnButton() {
  const tableFuncs = leftAsset.querySelector(".table-func");

  const deleteButton = document.createElement("button");
  deleteButton.id = "delete";
  deleteButton.innerHTML = `<span class="material-icons">block</span> Condemn`;
  if (!tableFuncs.querySelector("#delete")) tableFuncs.prepend(deleteButton);
}

function addActionsButton() {  
  for (const tr of assetTableBody.querySelectorAll("tr")) {
    const actionElem = document.createElement("td");
    if (tr.dataset.status === "Condemned"){
      tr.appendChild(actionElem);
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

function addReportModal() {
  const modalDiv = document.createElement("div");
  modalDiv.id = "reportModal";
  modalDiv.className = "modal";
  modalDiv.innerHTML = `
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
          All Unassigned Assets
        </button>
      </div>
      <button id="closeModal" class="btn-cancel">Cancel</button>
    </div>
  `;
  modalDiv.style.display = 'block';
  document.body.appendChild(modalDiv);
}

function updateTableButtons() {
  const tableFuncs = leftAsset.querySelector(".table-func");

  const assignButton = tableFuncs.querySelector("#assign");
  assignButton.style.display = [...selectedRows].every(tr => tr.dataset.status === "Unassigned")? "flex" : "none";

  const returnButton = tableFuncs.querySelector("#return");
  returnButton.style.display = [...selectedRows].every(tr => tr.dataset.status === "Assigned")? "flex" : "none";

  const deleteButton = tableFuncs.querySelector("#delete");
  deleteButton.style.display = [...selectedRows].every(tr => tr.dataset.status === "ToCondemn")?"flex" : "none";
}

function updateSelectedRows() {
  let toAdd = new Set();
  let toDel = new Set();

  for (const tr1 of assetTableBody.querySelectorAll("tr")) {
    if (tr1.dataset.status === "Condemned"){
      continue;
    }

    for (const tr2 of selectedRows) {
      if (tr2.dataset.propNum === tr1.dataset.propNum) {
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
