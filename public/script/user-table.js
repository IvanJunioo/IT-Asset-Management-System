const leftUser = document.querySelector(".left-user");
const tableContainer = leftUser.querySelector(".table-container");
const userTable = tableContainer.querySelector(".user-table");
const userTableBody = userTable.querySelector("tbody");
const searchInput = document.getElementById("search-input");
const filterBox = document.getElementById("filter-box");

export let tableData = new Map();
let latest = 0; // latest fetch id to avoid race conditions
let currentSortKey = "LName"; // track which column is sorted
let sortOrder = "asc";

fetchUsers();

const tableFuncs = document.createElement("div");
tableFuncs.className = "table-func";
tableFuncs.innerHTML = `
  <button id="reverse-sort">
    <span class="material-icons">north</span>
    Reverse
  </button>
  <button id="sort-by">
    <span class="material-icons"> sort </span>
    Sort by
  </button>
  <div id="sort-menu" class="sort-menu">
    <a class="menu-item" data-sort="EmpMail">Email</a>
    <a class="menu-item" data-sort="FName">First Name</a>
    <a class="menu-item" data-sort="LName">Last Name</a>
  </div>
`;
leftUser.insertBefore(tableFuncs, tableContainer);

// ----- EVENT LISTENERS (KEEP MINIMAL) -----
document.addEventListener("click", (e) => {
  const sortBtn = e.target.closest("#sort-by");
  if (sortBtn) {
    e.stopPropagation();
    const menu = document.getElementById("sort-menu");
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
    currentSortKey = menuBtn.dataset.sort;
    sortUsers();
  }

  const reverseBtn = e.target.closest("#reverse-sort");
  if (reverseBtn) {
    reverseBtn.classList.toggle('active');
    sortOrder = sortOrder === "asc"? "desc" : "asc";
    sortUsers();
    reverseBtn.querySelector(".material-icons").textContent = sortOrder === "asc"? "north": "south";
  }

  document.querySelectorAll(".sort-menu").forEach(menu => {
    menu.style.display = "none";
  });
});

searchInput.addEventListener("input", () => {
  fetchUsers();
});

filterBox.addEventListener("change", fetchUsers);

filterBox.querySelector("button[id='apply-filter']").addEventListener("click", () => {
  filterBox.querySelectorAll('input').forEach(cb => cb.checked = false);
  fetchUsers();
});

// ----- FUNCTION DEFINITIONS -----
async function fetchUsers() {
  const fetchID = ++latest;
  const searchFilters = searchInput.value;
  const privFilters = [...filterBox.querySelectorAll("input[name='privilege']:checked")].map(cb => cb.value);
  const statusFilters = [...filterBox.querySelectorAll("input[name='status']:checked")].map(cb => cb.value);

  const url = new URL(`${window.location.origin}/public/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: "search",
    search: searchFilters,
    status: statusFilters,
    priv: privFilters,
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);

    const data = await resp.json();
    tableData = new Map(data.map(user => [user.EmpID, user]));
    
    if (fetchID !== latest) return;
    showUsers();
  } catch (err) {
    console.error("Error fetching users: ", err);
  }
}

function showUsers() {
  for (const tableFunc of tableFuncs.querySelectorAll("button")) {
    tableFunc.disabled = tableData.size <= 0;
  }

  if (tableData.size <= 0) {
    userTableBody.innerHTML = `
      <tr>
        <td colSpan="${userTable.querySelector("thead tr").children.length}"> No users to display. </td>
      </tr>
    `;
    return;
  }

  userTableBody.innerHTML = "";
  
  for (const [_, user] of tableData) {
    const tr = document.createElement('tr');

    // store identifier
    tr.dataset.empid = user.EmpID;

    for (const col of [
      user.EmpMail,
      user.FName,
      user.LName,
      user.Privilege,
      `<span class="badge ${user.ActiveStatus.toLowerCase()}">${user.ActiveStatus}</span>`,
    ]) {
      const td = document.createElement("td");
      td.innerHTML = col;
      tr.appendChild(td);
    }

    userTableBody.appendChild(tr);
  }

  sortUsers();
  userTableBody.dispatchEvent(new CustomEvent("usersLoaded"));
}

function sortUsers() {
  const rows = [...userTableBody.querySelectorAll("tr")];
  rows.sort((a, b) => {
    let valA = tableData.get(Number(a.dataset.empid))[currentSortKey] || "";
    let valB = tableData.get(Number(b.dataset.empid))[currentSortKey] || "";

    valA = valA ? valA.toLowerCase() : ""; 
    valB = valB ? valB.toLowerCase() : "";
    if (valA < valB) return sortOrder === "asc" ? -1 : 1;
    if (valA > valB) return sortOrder === "asc" ? 1 : -1;
    return 0;
  });
  for (const tr of rows) userTableBody.appendChild(tr);
}
