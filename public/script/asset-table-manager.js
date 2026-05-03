import { tableData } from "./asset-table.js";
import { condemnAsset, relayPage} from "./asset-router.js";

const leftAsset = document.querySelector(".left-page");
const tableFuncs = leftAsset.querySelector(".table-func");
const tableContainer = leftAsset.querySelector(".table-container");
const assetTable = tableContainer.querySelector(".asset-table");
const assetTableBody = assetTable.querySelector("tbody");

let selectedRows = new Set();
let inMultiSelect = false;

addTableFuncs();
addAssetAdd();

// Replace Reports Button
const reportBtn = document.createElement("button");
reportBtn.id = "report";
reportBtn.className = "generate";
reportBtn.innerHTML = `
  <span class="material-icons">ios_share</span>
  Generate Report
`;
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

      menu.style.visibility = "hidden";
      menu.style.display = "flex";
      const menuWidth = menu.offsetWidth;
      menu.style.visibility = "";

      const overflowsRight = boundingRect.right + gap + menuWidth > window.innerWidth;

      menu.style.top = `${boundingRect.top - gap}px`;

      if (overflowsRight) {
        menu.style.left = `${boundingRect.left - gap - menuWidth}px`;
      } else {
        menu.style.left = `${boundingRect.right + gap}px`;
      }

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
        relayPage("edit-asset-form", {"propNum": propNum});
        break;
      case "condemn": 
        if (confirm(`Condemn item ${propNum}?`)) condemnAsset(propNum);
        break;
      case "assign":
        relayPage("assign-user", {
          "redirect": "index.php" + window.location.search,
          "propNums[]": propNum,
        });
        break;
      case "return":
        relayPage("return-form", {
          "redirect": "index.php" + window.location.search,
          "propNums[]": propNum,
        });
        break;
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
    const addRemarks = document.getElementById("inc-remarks").checked;
    if (!target) return;

    let url = "";
    let type = target.dataset.type;

    if (type === "assigned-p") {
      url = `${window.location.origin}/api/index.php?resource=export&action=user-assets`;
    } else if (type === "unassigned") {
      url = `${window.location.origin}/api/index.php?resource=export&action=status&status=Unassigned`;
    } else {
      url = `${window.location.origin}/api/index.php?resource=export&action=status&status=ToCondemn`;
    }

    if (addRemarks) {
      url += "&add_remarks=true"
    }

    window.open(url, "_blank");

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
    relayPage("assign-user", {
      "redirect": "index.php" + window.location.search,
      "propNums[]": [...selectedRows].map(tr => tr.dataset.propNum),
    })
    return;
  }

  if (e.target.closest("#delete")) {
    if (selectedRows.size === 0) return;
    if (!confirm(`Condemn ${selectedRows.size} item(s)?`)) return;
    
    for (const tr of selectedRows) {
      if (tableData.get(tr.dataset.propNum).Status !== "ToCondemn"){
        continue;
      }
      condemnAsset(tr.dataset.propNum);
    }
    return;
  }

  if (e.target.closest("#return")) {
    if (selectedRows.size === 0) return;
    relayPage("return-form", {
      "redirect": "index.php" + window.location.search,
      "propNums[]": [...selectedRows].map(tr => tr.dataset.propNum),
    });
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

  if (e.target.closest("a")) {
    const a = e.target.closest("a");
    const tr = e.target.closest("tr");
    if (!tr) return;

    if (a.dataset.type === "assignee") {
      relayPage("edit-user-form", {"empID": tableData.get(tr.dataset.propNum).AssignedTo});
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
})

assetTableBody.addEventListener("assetsLoaded", () => {  
  for (const tr of assetTableBody.querySelectorAll("tr")) {
    // Replace assignee with clickable link to user
    const assToIdx = document.getElementById("assto").cellIndex;
    tr.cells[assToIdx].innerHTML = `
      <a data-type="assignee">${tr.cells[assToIdx].textContent.trim()}</a>
    `;

    tr.lastElementChild.remove();
  }
  addModifyButton();

  if (inMultiSelect) {
    updateSelectedRows();
    addCheckboxes();
  } 
});

assetTableBody.addEventListener("click", (e) => {
  const tr = e.target.closest("tr");
  if (!tr) return;

  if (e.target.closest(".action-btn")) {
    relayPage("edit-asset-form", {"propNum": tr.dataset.propNum});
    return;
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
    
    // Remove extra table funcs
    document.getElementById("assign")?.remove();
    document.getElementById("delete")?.remove();
    document.getElementById("return")?.remove();
  
    // Reset tracking
    selectedRows.clear();

    // Replace last td's
    assetTableBody.querySelectorAll("tr").forEach(tr => tr.lastElementChild.remove());
    addModifyButton();
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
    const actionBtn = document.createElement("button");
    actionBtn.className = "action-btn";
    actionBtn.innerHTML = `<span class="material-icons">more_horiz</span>`;
    
    const actionMenu = document.createElement("div");
    actionMenu.className = "action-menu";
    Object.entries({
      "modify": "Modify",
      ...(tableData.get(tr.dataset.propNum).Status === "ToCondemn" && {"condemn": "Condemn"}), // adds only if satisfied
      ...(tableData.get(tr.dataset.propNum).Status === "Assigned" && {"return": "Return"}),
      ...(tableData.get(tr.dataset.propNum).Status === "Unassigned" && {"assign": "Assign"}),
    }).forEach(([action, label]) => {
      const a = document.createElement("a");
      a.className = "menu-item";
      a.dataset.action = action;
      a.textContent = label;
      actionMenu.appendChild(a);
    });
    
    const actionTd = document.createElement("td");
    if (tableData.get(tr.dataset.propNum).Status !== "Condemned") {
      actionTd.appendChild(actionBtn);
      actionTd.appendChild(actionMenu);
    }

    tr.appendChild(actionTd)
  }
}

function addModifyButton() {
  for (const tr of assetTableBody.querySelectorAll("tr")) {
    const td = document.createElement("td");    
    if (tableData.get(tr.dataset.propNum).Status !== "Condemned") {
      td.innerHTML = `
        <button class="action-btn">
          <span class="material-icons">edit</span>
          Modify
        </button>
      `;
    }
    tr.appendChild(td);
  }
}

function addAssetAdd() {
  const leftAsset = document.querySelector(".left-page");

  const assetAdd = document.createElement("a");
  assetAdd.href = "?page=add-asset-form";
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

        <div class="modal-input">
          <input type="checkbox" id="inc-remarks">
          <label for="inc-remarks">Include Remarks</label>
        </div>
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
  assignButton.style.display = [...selectedRows].every(tr => tableData.get(tr.dataset.propNum).Status === "Unassigned")? "flex" : "none";

  const returnButton = tableFuncs.querySelector("#return");
  returnButton.style.display = [...selectedRows].every(tr => tableData.get(tr.dataset.propNum).Status === "Assigned")? "flex" : "none";

  const deleteButton = tableFuncs.querySelector("#delete");
  deleteButton.style.display = [...selectedRows].every(tr => tableData.get(tr.dataset.propNum).Status === "ToCondemn")?"flex" : "none";
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
