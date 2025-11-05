const userTable = document.querySelector(".user-table");
const userTableBody = userTable.querySelector("tbody");

document.addEventListener("DOMContentLoaded", () => {
  addTableFuncs();
  userTable.querySelector("thead tr").appendChild(document.createElement("th"));
  addUserAdd();

});

userTableBody.addEventListener("usersLoaded", () => {
  const multiSelectButton = document.getElementById("multi-select");
  const rows = userTableBody.querySelectorAll("tr");
  const menuButtons = document.getElementsByClassName("menu-item");

  addActionsButton();

  Array.from(menuButtons).forEach(btn => {
    btn.addEventListener("click", (e) => {
      const row = e.target.closest("tr");
      const cells = row.querySelectorAll("td");
      const empid = cells[0].textContent.trim();
      switch (btn.id) {
        case "modify-action":
          editUsers(empid);
          break;
        case "delete-action":
          deleteUsers(empid);
          break;
        default: 
      }
    })
  })
  
  function editUsers(filter){
    const src = "../handlers/edit-user.php";
  
    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${filter}`,
    })
    .then(res => res.json())
    .then(data => {
      sessionStorage.setItem("userData", JSON.stringify(data));
      window.location.href = "../views/edit-user-form.php";
    }
    )
    .catch(err => console.error("Error edit users: ", err))
  }
  
  function deleteUsers(filter){
    const src = "../handlers/delete-user.php";
    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${filter}`,
    }).then(_ => {
      window.location.href = "../views/user-manager.php";
    })
  }
  
  function addActionsButton() {
    for (const row of rows) {
      row.innerHTML += `
      <td class="actions">
        <button class="action-btn">
          <span class="material-icons">more_horiz</span>
        </button>
        
        <div class="action-menu">
          <a class="menu-item" id="modify-action">Modify</a>
          <a class="menu-item" id="delete-action">Delete</a>
        </div>
      </td>
      `;

      row.querySelectorAll(".action-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const menu = btn.parentElement.querySelector(".action-menu");
          const isVisible = menu.style.display == "flex";

          document.querySelectorAll(".action-menu").forEach(m => {
            m.style.display = "none";
          });

          if (!isVisible) {
            const boundingRect = btn.getBoundingClientRect();
            const gap = 8;

            menu.style.top = `${boundingRect.top - gap}px`;
            menu.style.left = `${boundingRect.right + gap}px`;
            menu.style.display = "flex";
          }
        });
      });
    }
  
  
    document.addEventListener("click", () => {
      document.querySelectorAll(".action-menu").forEach(menu => {
        menu.style.display = "none";
      });
    });
  }

});

function addTableFuncs() {
  const leftUser = document.querySelector(".left-user");
  const tableContainer = leftUser.querySelector(".table-container");

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

function addUserAdd() {
  const leftUser = document.querySelector(".left-user");

  const userAdd = document.createElement("a");
  userAdd.href = "user-form.php";
  userAdd.id = "addUser";
  userAdd.innerHTML = `
    <span class="material-icons" id="add-asset-button">add</span>
    Add a New User
  `;

  leftUser.append(userAdd);
}
