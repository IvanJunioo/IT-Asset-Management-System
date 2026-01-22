const leftUser = document.querySelector(".left-user");
const tableContainer = leftUser.querySelector(".table-container");
const userTable = tableContainer.querySelector(".user-table");
const userTableBody = userTable.querySelector("tbody");

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const userTableBody = document.querySelector('.user-table tbody');
  const filterBox = document.getElementById("filter-box");

  const tableFuncs = document.createElement("div");
  tableFuncs.className = "table-func";
  tableFuncs.innerHTML = `
    <button id="sort-by">
      <span class="material-icons"> sort </span>
    </button>
    <div id="sort-menu" class="sort-menu">
      <a class="menu-item" data-sort="empID">Employee ID</a>
      <a class="menu-item" data-sort="empMail">Email</a>
      <a class="menu-item" data-sort="fName">First Name</a>
      <a class="menu-item" data-sort="mName">Middle Name</a>
      <a class="menu-item" data-sort="lName">Last Name</a>
    </div>
  `;

  leftUser.insertBefore(tableFuncs, tableContainer);

  function fetchUsers() {
    const searchFilters = searchInput.value;
    const privFilters = [...filterBox.querySelectorAll("input[name='privilege']:checked")].map(cb => cb.value);
      
    fetch("../../src/handlers/user-table.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${encodeURIComponent(searchFilters)}&priv=${encodeURIComponent(privFilters)}`,
    })
    .then(res => res.json())
    .then(data => {
      showUsers(data);
      userTableBody.dispatchEvent(new CustomEvent("usersLoaded"));
    })
    .catch(err => console.error("Error fetching users: ", err));
  }
  
  function showUsers(users) {
    userTableBody.innerHTML = "";
    
    for (const user of users) {
      const tr = document.createElement('tr');

      // store user data locally
      tr.dataset.empID = user.EmpID;
      tr.dataset.empMail = user.EmpMail;
      tr.dataset.fName = user.FName;
      tr.dataset.mName = user.MName;
      tr.dataset.lName = user.LName;
      tr.dataset.privilege = user.Privilege;
      tr.dataset.activeStatus = user.ActiveStatus;

      for (const col of [
        user.EmpID,
        user.EmpMail,
        user.FName,
        user.MName,
        user.LName,
        user.Privilege,
        user.ActiveStatus,
      ]) {
        const td = document.createElement("td");
        td.textContent = col;
        tr.appendChild(td);
      }

			userTableBody.appendChild(tr);
		}
  }
    
  fetchUsers();
  
  searchInput.addEventListener("input", () => {
    fetchUsers();
  });

  searchButton.addEventListener("click", () => {
    fetchUsers()
    searchInput.value = "";
  });

  filterBox.querySelector("#body-filter").addEventListener("change", fetchUsers);

  filterBox.querySelector("button[id='apply-filter']").addEventListener("click", () => {
    filterBox.querySelectorAll('input[name="privilege"]').forEach(cb => cb.checked = false);
    fetchUsers();
  });
});

function sortUser(sortKey) {
  if (!sortKey) return;
  const rows = Array.from(userTableBody.querySelectorAll("tr"));
  rows.sort((a, b) => {
    let valA = a.dataset[sortKey];
    let valB = b.dataset[sortKey];

    valA = valA ? valA.toLowerCase() : ""; 
    valB = valB ? valB.toLowerCase() : "";
    if (valA < valB) return -1;
    if (valA > valB) return 1;
    return 0;
  });

  rows.forEach(tr => userTableBody.appendChild(tr));
}

document.addEventListener("click", (e) => {
  const sortBtn = e.target.closest("#sort-by");
  if (sortBtn) {
    e.stopPropagation();
    const menu = document.querySelector("#sort-menu");
    const isVisible = menu.style.display === "flex";

    document.querySelectorAll(".sort-menu").forEach(m => m.style.display = "none");

    if (!isVisible) {
      const boundingRect = sortBtn.getBoundingClientRect();
      const gap = 8;

      menu.style.top = `${boundingRect.top - gap}px`;
      menu.style.left = `${boundingRect.right + gap}px`;
      menu.style.display = "flex";
    }
    return;
  }

  const menuBtn = e.target.closest(".menu-item[data-sort]");
  if (menuBtn) {
    const sortKey = menuBtn.dataset.sort;
    sortUser(sortKey);
  }

  document.querySelectorAll(".sort-menu").forEach(menu => {
    menu.style.display = "none";
  });

});
