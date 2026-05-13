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
        relayPage("edit-user-form", {"empID": empid});
        break;
      case "get-report":
        window.open(
          `${window.location.origin}/api/index.php?resource=export&action=user-assets&user=` + encodeURIComponent(empid),
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
  userAdd.href = "?page=add-user-form";
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
