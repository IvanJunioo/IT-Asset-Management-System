import { tableData, selectedRows, inMultiSelect, addCheckboxes } from "./user-table.js";
import { fetchUser, modifyUser } from "./api.js";
import { relayPage } from "./nav.js";

const leftUser = document.querySelector(".left-page");
const tableFuncs = leftUser.querySelector(".table-func");
const tableContainer = leftUser.querySelector(".table-container");
const userTable = tableContainer.querySelector(".user-table");
const userTableBody = userTable.querySelector("tbody");

addTableFuncs();
addUserAdd();

// ----- EVENT LISTENERS -----
document.addEventListener("click", (e) => {
  if (e.target.closest("#addUser")) {
    addUserAddModal();
    return;
  }

  if (e.target.closest("#closeModal")) {
    e.target.closest("#reportModal")?.remove();
    e.target.closest("#addUserModal")?.remove();
    return;
  }

  if (e.target.closest("#addUserModal")) {
    const target = e.target.closest(".modal-option");
    if (!target) return;

    const type = target.dataset.type;

    relayPage(type === "manual" ? "add-user-form" : "csv-user-form", {
      "redirect": "index.php" + window.location.search,
    });
    
    e.target.closest("#addUserModal").remove();
    return;
  }
});

tableFuncs.addEventListener("click", async (e) => {
  if (e.target.closest(".table-fn")) {
    const btn = e.target.closest(".table-fn");

    if (btn.value === "deactivate") {      
      for (const tr of selectedRows) {
        const user = tableData.get(Number(tr.dataset.empid));
        if (0 < user.assignments.length) {
          const proceed = confirm(`User ${user.FName[0]}. ${user.LName} currently have assets assigned to them. Do you wish to proceed?`);
          if (!proceed) return;
        }
      }      
    }

    try {
      await Promise.all([...selectedRows].map(tr => modifyUser(tr.dataset.empid, btn.value)));
    }
    catch (err) {
      console.error("Error modifying users:", err);
      alert("Error: Some users could not be modified");
    }
    finally {
      location.reload();
    }
  }
});

// Listen to selection changes from user-table to update activate/deactivate buttons
userTableBody.addEventListener("selectionChanged", () => {
  updateTableFuncs();
});

tableFuncs.addEventListener("MultiSelectionChanged", () => {
  if (!inMultiSelect) {
    updateTableFuncs();
  }
});

userTableBody.addEventListener("usersLoaded", () => {
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

function addUserAdd() {
  const userAdd = document.createElement("a");
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

  deactBtn.style.display = selectedRows.size > 0 && [...selectedRows].every(tr =>
    tableData.get(Number(tr.dataset.empid)).ActiveStatus === "Active" &&
    !tableData.get(Number(tr.dataset.empid)).isCurrentUser
) ? "flex" : "none";
}

function addUserAddModal() {
  const modalDiv = document.createElement("div");
  modalDiv.id = "addUserModal";
  modalDiv.className = "modal";
  modalDiv.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">Add User(s) By
      </div>
      <div class="modal-body">
        <button class="modal-option" data-type = "manual"> 
          Manually Input User Information
        </button>
        <button class="modal-option" data-type="csv"> 
          Import from CSV File
        </button>
      </div>
      <button id="closeModal" class="btn-cancel">Cancel</button>
    </div>
  `;
  modalDiv.style.display = 'block';
  document.body.appendChild(modalDiv);

}