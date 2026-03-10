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


  // Reports
  if (e.target.closest("#report")) {
    if (selectedRows.size <= 0) {
      alert('Select a user first to get their assets');
      return;
    }

    if (selectedRows.size == 1) {
      const [tr] = selectedRows;
      window.open(
        `${window.location.origin}/public/api/index.php?resource=export&action=user-assets&user=` + encodeURIComponent(tr.dataset.empid),
        "_blank"
      );
      return;
    }

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

    let users = [];
    for (const tr of selectedRows) {
      users.push(tr.dataset.empid);
    }

    switch (target.dataset.type) {
      case "single":
        
        window.open(
          `${window.location.origin}/public/api/index.php?resource=export&action=faculty-assets&users=` + encodeURIComponent(users),
          "_blank"
        );
        break;
      case "multiple":
        window.open(
          `${window.location.origin}/public/api/index.php?resource=export&action=faculty-assets-multiple&users=` + encodeURIComponent(users),
          "_blank"
        );
        break;
    }

    e.target.closest("#reportModal").remove();
    return;
  }

});

tableFuncs.addEventListener("click", async (e) => {
  if (e.target.closest(".table-fn")) {
    const btn = e.target.closest(".table-fn");

    if (btn.value === "deactivate") {
      const hasAssignments = await checkAssignment();
      
      if (hasAssignments) {
        const proceed = confirm(`User ${hasAssignments} currently have assets assigned to them. Do you wish to proceed?`);

        if (!proceed) return;
      }
    }

    for (const tr of selectedRows) modifyUser(tr.dataset.empid, btn.value);
  }
});

// Listen to selection changes from user-table to update activate/deactivate buttons
userTableBody.addEventListener("selectionChanged", () => {
  updateTableFuncs();
});

tableFuncs.addEventListener("MultiSelectionChanged", () => {
  if (!inMultiSelect) addActionsButton();
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

  deactBtn.style.display = [...selectedRows].every(tr => tableData.get(Number(tr.dataset.empid)).ActiveStatus === "Active" && tableData.get(Number(tr.dataset.empid)).EmpID !== JSON.parse(sessionStorage.getItem("user-info")).EmpID)? "flex" : "none";
}

function updateSelectedRows() {
  var toAdd = new Set();
  var toDel = new Set();

  for (const tr1 of userTableBody.querySelectorAll("tr")) {
    if (tr1.dataset.activeStatus === "Inactive" ||
      session.user_id === tr1.dataset.empID
    ) {continue;}

    for (const tr2 of selectedRows) {
      if (tr2.dataset.empID === tr1.dataset.empID) {
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

function addReportModal() {
  const modalDiv = document.createElement("div");
  modalDiv.id = "reportModal";
  modalDiv.className = "modal";
  modalDiv.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">Choose Export Type
      </div>
      <div class="modal-body">
        <button class="report-option" data-type = "single"> 
          Download selected assets (Single PDF)
        </button>
        <button class="report-option" data-type="multiple"> 
          Download selected assets separately (Multiple PDFs)
        </button>
      </div>
      <button id="closeModal" class="btn-cancel">Cancel</button>
    </div>
  `;
  modalDiv.style.display = 'block';
  document.body.appendChild(modalDiv);
}

async function checkAssignment(){
  const url = new URL(`${window.location.origin}/public/api/index.php`);
  for (const tr of selectedRows) {
    const user = tableData.get(Number(tr.dataset.empid));

    url.search = new URLSearchParams({
      resource: "users",
      action: "search",
      search: user.EmpMail,
    });

    try {
      const resp = await fetch(url);
      if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);
      const data = await resp.json();
      const assignments = data[0]['assignments'];
      if (assignments.length>0) return `${user.FName[0]}. ${user.LName}`;
    } catch (err) {
      console.error("Error fetching users: ", err);
    }
  }
  return null;
}