import { tableData, setAssetSelectFunc } from "./asset-table.js";
import { relayPage } from "./nav.js";

const assetTable = document.querySelector(".asset-table");
const assetTableBody = assetTable.querySelector("tbody");
const leftAsset = document.querySelector(".left-page");
const tableFuncs = leftAsset.querySelector(".table-func");
const tableContainer = leftAsset.querySelector(".table-container");

let selectedRows = new Set();
let inMultiSelect = false;

addTableFuncs();

// Override Select Button behavior
setAssetSelectFunc((propNum) => {
  relayPage("assignment-form", {"propNums[]": propNum});
});

document.getElementById("export")?.remove();

tableFuncs.addEventListener("click", (e) => {
  if (e.target.closest("#assign")) {
    if (selectedRows.size === 0) return;
    relayPage("assignment-form", {"propNums[]": [...selectedRows].map(tr => tr.dataset.propNum)});
    return;
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
      tr => tableData.get(tr.dataset.propNum).Status !== "Condemned"
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

  if (e.target.closest("tr") && inMultiSelect) {
    const tr = e.target.closest("tr");
    if (!tr.closest("tbody")) return;
    if (tableData.get(tr.dataset.propNum).Status === "Condemned") return;

    if (selectedRows.has(tr)) {
      deselectRow(tr);
    } else {
      selectRow(tr);
    }
    return;
  }
});

assetTableBody.addEventListener("assetsLoaded", () => {
  for (const tr of assetTableBody.querySelectorAll("tr")) {
    tr.lastElementChild.innerHTML = "";
  }
  addActionsButton();

  if (inMultiSelect) {
    updateSelectedRows();
    addCheckboxes();
  } 
});


function addTableFuncs() {
  tableFuncs.insertAdjacentHTML("afterbegin", `
    <button id="multi-select">
      <span class="material-icons"> check_box_outline_blank </span>
      Select Multiple
    </button>
  `);
}

function setInMulSel(val) {
  if (inMultiSelect && !val) {
    document.querySelectorAll("#select-all").forEach(btn => btn.remove());
    
    document.getElementById("assign")?.remove();
  
    selectedRows.clear();

    // assetTableBody.querySelectorAll("tr").forEach(tr => tr.lastElementChild.remove());

    for (const tr of assetTableBody.querySelectorAll("tr")) {
      if (tableData.get(tr.dataset.propNum).Status !== "Unassigned"){
        tr.lastElementChild.innerHTML = ``;
      continue;
    }

      tr.lastElementChild.innerHTML = `
        <button class="select-btn" id="select">
          Select
        </button>
      `;
    }
    
  }

  if (!inMultiSelect && val) {
    addSelectAll();
    addCheckboxes();
    addAssignButton();
  }

  inMultiSelect = val;
  
  const multiSelectIcon = document.querySelector("#multi-select .material-icons");
  if (multiSelectIcon) {
    multiSelectIcon.textContent = val ? "check_box" : "check_box_outline_blank";
  }
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
    if (tableData.get(tr.dataset.propNum).Status === "Condemned"){
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

function updateTableButtons() {
  const tableFuncs = leftAsset.querySelector(".table-func");

  const assignButton = tableFuncs.querySelector("#assign");
  assignButton.style.display = [...selectedRows].every(tr => tableData.get(tr.dataset.propNum).Status === "Unassigned")? "flex" : "none";
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

function addActionsButton() {
  const hr = assetTable.querySelector("thead tr");
  if (!hr.querySelector("#actionsth")) {
    const actionsth = document.createElement("th");
    actionsth.id = "actionsth";
    hr.appendChild(actionsth);
  }

  for (const tr of assetTableBody.querySelectorAll("tr")) {
    if (tableData.get(tr.dataset.propNum).Status !== "Unassigned"){
      continue;
    }

    tr.lastElementChild.innerHTML = `
      <button class="select-btn" id="select">
        Select
      </button>
    `;
  }
}
