const leftUser = document.querySelector(".left-user");
const tableContainer = leftUser.querySelector(".table-container");
const userTable = tableContainer.querySelector(".user-table");
const userTableBody = userTable.querySelector("tbody");

var inactiveCnt = 0;
let selectedRows = new Set();

document.addEventListener("DOMContentLoaded", () => {
  addTableFuncs();

  // Handles all table func clicks dynamically
  const tableFuncs = leftUser.querySelector(".table-func");
  tableFuncs.addEventListener("click", (e) => {
    if (e.target.closest(".delete")) {
      if (selectedRows.size === 0) return;
      if (!confirm(`Deactivate ${selectedRows.size} user(s)?`)) return;
      for (const tr of selectedRows) {
        deleteUser(tr.dataset.empID);
      }
      return;
    }

    if (e.target.closest("#multi-select")) {
      const multiSelectIcon = e.target.closest("#multi-select").querySelector(".material-icons");
      
      if (multiSelectIcon.textContent.trim() === "check_box_outline_blank") {
        addSelectAll();
        addCheckboxes();
        addDeactivateButton();

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

  addUserAdd();
});

userTableBody.addEventListener("usersLoaded", () => {
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

function editUser(empid){
  fetch("../../src/handlers/edit-user.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `search=${empid}`,
  })
  .then(res => res.json())
  .then(data => {
    sessionStorage.setItem("userData", JSON.stringify(data));
    window.location.href = "../views/edit-user-form.php";
  })
  .catch(err => console.error("Error editing user: ", err));
}

function deleteUser(empid){
  fetch("../../src/handlers/delete-user.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `search=${empid}`,
  })
  .then(_ => {
    window.location.href = "../views/user-manager.php";
  })
  .catch(err => console.error("Error deleting user: ", err));
}

function addTableFuncs() {
  const tableFuncsClass = document.querySelector(".table-func");
  tableFuncsClass.insertAdjacentHTML("afterbegin", `
  <button id="multi-select">
    <span class="material-icons"> check_box_outline_blank </span>
  </button>
  `);
}

function addSelectAll() {
  const hr = userTable.querySelector("thead tr");
  hr.lastElementChild.innerHTML = `
    <button id="select-all">
      <span class="material-icons"> select_all </span>
    </button>
  `;
}

function addCheckboxes() {
  for (const tr of userTableBody.querySelectorAll("tr")) {
    if (tr.dataset.activeStatus === "Inactive"){
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

function addDeactivateButton() {
  const tableFuncs = leftUser.querySelector(".table-func");

  const deleteButton = document.createElement("button");
  deleteButton.className = "delete";
  deleteButton.innerHTML = `<span class="material-icons">delete</span>`;
  if (!tableFuncs.querySelector(".delete")) tableFuncs.prepend(deleteButton);
}

function addReportButton(){
  const rightUser = document.querySelector(".right-user");
  const reportBtn = document.getElementById("report");

  if (selectedRows.size > 0){
    if (!reportBtn){
      const reportBtn = document.createElement("button");
      reportBtn.id = "report";
      reportBtn.className = "generate";
      reportBtn.textContent = "Get Assigned Assets";
      rightUser.appendChild(reportBtn)
    }
  } else {
    reportBtn?.remove();
  }
}

function updateSelectedRows() {
  var toAdd = new Set();
  var toDel = new Set();

  for (const tr1 of userTableBody.querySelectorAll("tr")) {
    if (tr1.dataset.activeStatus === "Inactive"){
      continue;
    }

    for (const tr2 of selectedRows) {
      if (tr2.dataset.empID == tr1.dataset.empID) {
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
  const hr = userTable.querySelector("thead tr");
  if (!hr.querySelector("#actionsth")) {
    const actionsth = document.createElement("th");
    actionsth.id = "actionsth";
    hr.appendChild(actionsth);
  }

  for (const tr of userTableBody.querySelectorAll("tr")) {
    const actionElem = document.createElement("td");

    if (tr.querySelector("td.actions")) {
      continue;
    }
    
    actionElem.className = "actions";
    if (tr.dataset.activeStatus === "Active"){
      actionElem.innerHTML = `
        <button class="action-btn">
          <span class="material-icons">more_horiz</span>
        </button>
        
        <div class="action-menu">
          <a class="menu-item" data-action="modify">Modify</a>
          <a class="menu-item" data-action="deactivate">Deactivate</a>
        </div>
      `;
    } else {
      inactiveCnt++;
    }
    tr.appendChild(actionElem);
  }
}

function addUserAdd() {
  const leftUser = document.querySelector(".left-user");

  const userAdd = document.createElement("a");
  userAdd.href = "add-user-form.php";
  userAdd.id = "addUser";
  userAdd.innerHTML = `
    <span class="material-icons" id="add-asset-button">add</span>
    Add a New User
  `;

  leftUser.append(userAdd);
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
  userTableBody.querySelectorAll(".selectable-row")
    .forEach(btn => btn.closest("td")?.remove());

  // Remove deactivate button
  document.querySelector(".table-func .delete")?.remove();
  
  // Reset tracking
  inactiveCnt = 0;
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
    const empid = tr.dataset.empID;

    switch (menuBtn.dataset.action) {
      case "modify":
        editUser(empid);
        break;
      case "deactivate": 
        if (confirm(`Deactivate user ${empid}?`)) deleteUser(empid);
        break;
    }
    return;
  }

  // Closes actions menu
  document.querySelectorAll(".action-menu").forEach(menu => {
    menu.style.display = "none";
  });
});


function selectRow(tr) {
  selectedRows.add(tr);
  const icon = tr.querySelector(".material-icons");
  if (icon) icon.textContent = "check_box";
  addReportButton();
}

function deselectRow(tr) {    
  selectedRows.delete(tr);
  const icon = tr.querySelector(".material-icons");
  if (icon) icon.textContent = "check_box_outline_blank";
  addReportButton();
}

// Handles all table clicks dynamically
tableContainer.addEventListener("click", (e) => {
  if (e.target.closest("#select-all")) {
    const rows = userTableBody.querySelectorAll("tr");

    if (selectedRows.size === (rows.length - inactiveCnt)) {
      for (const tr of rows) {
        deselectRow(tr);
      }
    }
    else {
      for (const tr of rows) {
        if (tr.dataset.activeStatus ==="Inactive") {
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
});

document.addEventListener("click", (e) => {
  if (e.target.closest("#report")){
    let users = [];
    for (const tr of selectedRows) {
      users.push(tr.dataset.empID);
    }
    const url = "../../src/handlers/export-faculty-asset.php?users=" + encodeURIComponent(users);
    window.location.href = url;
    }
});