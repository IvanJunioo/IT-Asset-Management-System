import { tableData, selectedRows, inMultiSelect, setInMulSel, addCheckboxes } from "./user-table.js";
import { editUser, modifyUser } from "./user-router.js";

const leftUser = document.querySelector(".left-user");
const tableFuncs = leftUser.querySelector(".table-func");
const tableContainer = leftUser.querySelector(".table-container");
const userTable = tableContainer.querySelector(".user-table");
const userTableBody = userTable.querySelector("tbody");

const session = JSON.parse(document.body.dataset.session);

addTableFuncs();
addActionsButton();
addUserAdd();

// ----- EVENT LISTENERS -----
document.addEventListener("click", (e) => {
  // Actions dropdown toggle
  const actionBtn = e.target.closest(".action-btn");
  if (actionBtn) {
    e.stopPropagation();
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

  // Actions menu items
  const menuBtn = e.target.closest(".menu-item[data-action]");
  if (menuBtn) {
    const tr = menuBtn.closest("tr");
    let empid = tr.dataset.empid;

    switch (menuBtn.dataset.action) {
      case "modify":
        editUser(empid);
        break;
      case "get-report":
        window.open(
          `${window.location.origin}/public/api/index.php?resource=export&action=user-assets&user=` + encodeURIComponent(empid),
          "_blank"
        );
        break;
    }
    return;
  }

  // Close actions menu
  document.querySelectorAll(".action-menu").forEach(menu => {
    menu.style.display = "none";
  });
});

tableFuncs.addEventListener("click", (e) => {
  if (e.target.closest(".table-fn")) {
    const btn = e.target.closest(".table-fn");
    for (const tr of selectedRows) modifyUser(tr.dataset.empid, btn.value);
  }
});

tableFuncs.addEventListener("click", (e) => {
  if (e.target.closest("#multi-select")) {
    const val = !inMultiSelect;
    if (!inMultiSelect && val) addActionsButton();
  }
});

// Listen to selection changes from user-table to update activate/deactivate buttons
userTableBody.addEventListener("selectionChanged", () => {
  updateTableFuncs();
});

userTableBody.addEventListener("usersLoaded", () => {
  addActionsButton();
  if (inMultiSelect) addCheckboxes();
});

// ----- FUNCTION DEFINITIONS -----
function addTableFuncs() {
  tableFuncs.insertAdjacentHTML("afterbegin", `
    <button class="table-fn" name="activate" value="activate" style="display: none">
      <span class="material-icons"> check_circle </span> Reactivate
    </button>
    <button class="table-fn" name="deactivate" value="deactivate" style="display: none">
      <span class="material-icons"> block </span> Deactivate
    </button>
  `);
}

function addActionsButton() {
  for (const tr of userTableBody.querySelectorAll("tr")) {
    tr.querySelector("td.actions")?.remove();

    const actionElem = document.createElement("td");
    actionElem.className = "actions";
    actionElem.innerHTML = `
      <button class="action-btn">
        <span class="material-icons">more_horiz</span>
      </button>
      <div class="action-menu">
        <a class="menu-item" data-action="modify">Modify</a>
        <a class="menu-item" data-action="get-report">Get assignments</a>
      </div>
    `;
    tr.appendChild(actionElem);
  }
}

function addUserAdd() {
  const userAdd = document.createElement("a");
  userAdd.href = "add-user-form.php";
  userAdd.id = "addUser";
  userAdd.innerHTML = `
    <span class="material-icons" id="add-asset-button">add</span>
    Add a New User
  `;
  leftUser.append(userAdd);
}

function updateTableFuncs() {
  const actBtn = tableFuncs.querySelector('button[name="activate"]');
  actBtn.style.display = inMultiSelect && selectedRows.size > 0 && [...selectedRows].every(tr =>
    tableData.get(Number(tr.dataset.empid))?.ActiveStatus === "Inactive"
  ) ? "flex" : "none";

  const deactBtn = tableFuncs.querySelector('button[name="deactivate"]');
  deactBtn.style.display = inMultiSelect && selectedRows.size > 0 && [...selectedRows].every(tr =>
    tableData.get(Number(tr.dataset.empid))?.ActiveStatus === "Active" &&
    tableData.get(Number(tr.dataset.empid))?.EmpID !== JSON.parse(sessionStorage.getItem("user-info")).EmpID
  ) ? "flex" : "none";
}