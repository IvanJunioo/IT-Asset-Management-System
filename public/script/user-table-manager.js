const leftUser = document.querySelector(".left-user");
const tableContainer = leftUser.querySelector(".table-container");
const userTable = tableContainer.querySelector(".user-table");
const userTableBody = userTable.querySelector("tbody");

document.addEventListener("DOMContentLoaded", () => {
  addTableFuncs();
  addUserAdd();
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

  leftUser.insertBefore(tableFuncs, tableContainer);
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
    if (tr.dataset.activeStatus === "Inactive"){
      continue;
    }
    actionElem.className = "actions";
    actionElem.innerHTML = `
      <button class="action-btn">
        <span class="material-icons">more_horiz</span>
      </button>
      
      <div class="action-menu">
        <a class="menu-item" data-action="modify">Modify</a>
        <a class="menu-item" data-action="deactivate">Deactivate</a>
      </div>
    `;

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

userTableBody.addEventListener("usersLoaded", () => {
  let selectedRows = new Set();

  function selectRow(tr) {
    selectedRows.add(tr);
    tr.querySelector(".material-icons").textContent = "check_box";
  }

  function deselectRow(tr) {
    selectedRows.delete(tr);
    tr.querySelector(".material-icons").textContent = "check_box_outline_blank";
  }

  addActionsButton();

  // Handles all table func clicks dynamically
  const tableFuncs = leftUser.querySelector(".table-func");
  tableFuncs.addEventListener("click", (e) => {
    if (e.target.closest("#multi-select")) {
      const multiSelectIcon = e.target.closest("#multi-select").querySelector(".material-icons");
      
      if (multiSelectIcon.textContent.trim() === "check_box_outline_blank") {
        // Add select-all button
        const hr = userTable.querySelector("thead tr");
        hr.lastElementChild.innerHTML = `
          <button id="select-all">
            <span class="material-icons"> select_all </span>
          </button>
        `;

        // Add checkbox per row
        for (const tr of userTableBody.querySelectorAll("tr")) {
          if (tr.dataset.activeStatus === "Inactive"){
            continue;
          }
          tr.lastElementChild.innerHTML = `
            <button class="selectable-row">
              <span class="material-icons"> check_box_outline_blank </span>
            </button>
          `;
        }

        multiSelectIcon.textContent = "check_box";
        return;
      } 

      if (multiSelectIcon.textContent.trim() === "check_box") {
        // Remove select-all button
        document.querySelectorAll("#select-all").forEach(elem => {
          elem?.remove();
        })

        // Remove checkbox per row
        for (const tr of userTableBody.querySelectorAll("tr")) {
          if (tr.dataset.activeStatus === "Inactive"){
            continue;
          }
          tr.lastElementChild.remove();
        }

        addActionsButton();

        multiSelectIcon.textContent = "check_box_outline_blank";
        return;
      }
    }
  });

  // Handles all table clicks dynamically
  tableContainer.addEventListener("click", (e) => {
    if (e.target.closest("#select-all")) {
      const rows = userTableBody.querySelectorAll("tr");

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
  });
});
