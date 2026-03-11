const leftUser = document.querySelector(".left-user");
const tableContainer = leftUser.querySelector(".table-container");
const userTable = tableContainer.querySelector(".user-table");
const userTableBody = userTable.querySelector("tbody");
const searchInput = document.getElementById("search-input");
const filterBox = document.getElementsByClassName("filter-box");

export let tableData = new Map();
export let selectedRows = new Set();
export let inMultiSelect = false;

let latest = 0;
let currentSortKey = "LName";
let sortOrder = "asc";

fetchUsers();

const tableFuncs = document.createElement("div");
tableFuncs.className = "table-func";
tableFuncs.innerHTML = `
  <button id="multi-select">
    <span class="material-icons"> check_box_outline_blank </span> Select Multiple
  </button>
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

// Immediately add table header for actions column
const hr = userTable.querySelector("thead tr");
if (!hr.querySelector("#actionsth")) {
  const actionsth = document.createElement("th");
  actionsth.id = "actionsth";
  hr.appendChild(actionsth);
}

// ----- EVENT LISTENERS -----
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
    sortOrder = sortOrder === "asc" ? "desc" : "asc";
    sortUsers();
    reverseBtn.querySelector(".material-icons").textContent = sortOrder === "asc" ? "north" : "south";
  }

  // Report button
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
    for (const tr of selectedRows) users.push(tr.dataset.empid);

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

  document.querySelectorAll(".sort-menu").forEach(menu => {
    menu.style.display = "none";
  });
});

tableFuncs.addEventListener("click", (e) => {
  if (e.target.closest("#multi-select")) {
    const multiSelectBtn = e.target.closest("#multi-select");
    multiSelectBtn.classList.toggle('active');
    setInMulSel(!inMultiSelect);
  }
});

tableContainer.addEventListener("click", (e) => {
  if (e.target.closest("#select-all")) {
    const rows = userTableBody.querySelectorAll("tr");
    if (selectedRows.size === rows.length) {
      for (const tr of selectedRows) deselectRow(tr);
    } else {
      for (const tr of rows) selectRow(tr);
    }
    return;
  }

  if (e.target.closest(".get-assignment")) {
    const tr = e.target.closest("tr");
    let empid = tr.dataset.empid;
    window.open(
      `${window.location.origin}/public/api/index.php?resource=export&action=user-assets&user=` + encodeURIComponent(empid),
      "_blank"
    );
    return;
  }

  if (e.target.closest("tr") && inMultiSelect) {
    const tr = e.target.closest("tr");
    if (!tr.closest("tbody")) return;
    if (selectedRows.has(tr)) {
      deselectRow(tr);
    } else {
      selectRow(tr);
    }
    return;
  }

});

searchInput.addEventListener("input", () => {
  fetchUsers();
});

document.querySelectorAll(".filter-box .body-filter").forEach(box => {
  box.addEventListener("change", fetchUsers);
});

document.querySelector(".apply-filter").addEventListener("click", () => {
  document.querySelectorAll(".filter-box input[name='status']").forEach(cb => cb.checked = false);
  fetchUsers();
});

userTableBody.addEventListener("usersLoaded", () => {
  addActionsButton();
  if (inMultiSelect) {
    updateSelectedRows();
    addCheckboxes();
  }
});

// ----- FUNCTION DEFINITIONS -----
async function fetchUsers() {
  const fetchID = ++latest;
  const searchFilters = searchInput.value;
  const privFilters = [...new Set(
    [...document.querySelectorAll(".filter-box input[name='privilege']:checked")].map(cb => cb.value)
  )];
  const statusFilters = [...new Set(
    [...document.querySelectorAll(".filter-box input[name='status']:checked")].map(cb => cb.value)
  )];

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
    tr.dataset.empid = user.EmpID;

    for (const col of [
      user.EmpMail,
      user.FName,
      user.LName,
      user.Privilege,
      `<span class="badge ${user.ActiveStatus.toLowerCase()}">${user.ActiveStatus}</span>`
    ]) {
      const td = document.createElement("td");
      td.innerHTML = col;
      tr.appendChild(td);
    }
    userTableBody.appendChild(tr);
  }

  highlightSearch();
  sortUsers();
  userTableBody.dispatchEvent(new CustomEvent("usersLoaded"));
}

function sortUsers() {
  const rows = [...userTableBody.querySelectorAll("tr")];
  rows.sort((a, b) => {
    let valA = tableData.get(Number(a.dataset.empid))?.[currentSortKey] || "";
    let valB = tableData.get(Number(b.dataset.empid))?.[currentSortKey] || "";

    valA = valA ? valA.toLowerCase() : "";
    valB = valB ? valB.toLowerCase() : "";
    if (valA < valB) return sortOrder === "asc" ? -1 : 1;
    if (valA > valB) return sortOrder === "asc" ? 1 : -1;
    return 0;
  });
  for (const tr of rows) userTableBody.appendChild(tr);
}

function highlightSearch() {
  const search = searchInput.value.trim();
  if (!search) return;
  const regex = new RegExp(`(${search})`, "gi");
  const headerIDs = [...userTable.querySelectorAll("thead th")].map((th) => th.id);

  const dfs = (node) => {
    if (node.nodeType === Node.TEXT_NODE) {
      const span = document.createElement("span");
      span.innerHTML = node.nodeValue.replace(regex, "<mark>$1</mark>");
      node.replaceWith(...span.childNodes);
      return;
    }
    for (const child of node.childNodes) dfs(child); 
  };

  for (const tr of userTableBody.querySelectorAll("tr")) {
    for (const td of tr.querySelectorAll("td")) {
      if (!["email","fname","lname"].includes(headerIDs[td.cellIndex])) continue;
      dfs(td);
    }
  }
}

export function setInMulSel(val) {
  if (inMultiSelect === val) return;

  if (val) {
    addSelectAll();
    addCheckboxes();
  } else {
    document.querySelectorAll("#select-all").forEach(btn => btn.remove());
    
    // Reset tracking
    selectedRows.clear();

    userTableBody.querySelectorAll("tr").forEach(tr => tr.lastElementChild.remove());
    addActionsButton();
  }

  inMultiSelect = val;

  const multiSelectIcon = document.querySelector("#multi-select .material-icons");
  if (multiSelectIcon) multiSelectIcon.textContent = val? "check_box" : "check_box_outline_blank";

  // Show/hide report button
  const reportBtn = tableFuncs.querySelector("#report");
  if (reportBtn) reportBtn.style.display = val ? "flex" : "none";

  tableFuncs.dispatchEvent(new CustomEvent("MultiSelectionChanged"));
}

function selectRow(tr) {
  selectedRows.add(tr);
  const icon = tr.querySelector(".material-icons");
  if (icon) icon.textContent = "check_box";
  dispatchSelectionChanged();
}

function deselectRow(tr) {
  selectedRows.delete(tr);
  const icon = tr.querySelector(".material-icons");
  if (icon) icon.textContent = "check_box_outline_blank";
  dispatchSelectionChanged();
}

function addSelectAll() {
  const hr = userTable.querySelector("thead tr");
  hr.lastElementChild.innerHTML = `
    <button id="select-all">
      <span class="material-icons"> select_all </span>
    </button>
  `;
}

export function addCheckboxes() {
  for (const tr of userTableBody.querySelectorAll("tr")) {
    const icon = selectedRows.has(tr) ? "check_box" : "check_box_outline_blank";
    tr.lastElementChild.innerHTML = `
      <button class="selectable-row">
        <span class="material-icons"> ${icon} </span>
      </button>
    `;
  }
}

function updateSelectedRows() {
  const toAdd = new Set();
  const toDel = new Set();

  for (const tr1 of userTableBody.querySelectorAll("tr")) {
    for (const tr2 of selectedRows) {
      if (tr2.dataset.empid === tr1.dataset.empid) {
        toDel.add(tr2);
        toAdd.add(tr1);
      }
    }
  }

  for (const tr of toDel) selectedRows.delete(tr);
  for (const tr of toAdd) selectedRows.add(tr);
}

function dispatchSelectionChanged() {
  userTableBody.dispatchEvent(new CustomEvent("selectionChanged", {
    bubbles: true,
    detail: { selectedRows, inMultiSelect }
  }));
}

function addReportModal() {
  const modalDiv = document.createElement("div");
  modalDiv.id = "reportModal";
  modalDiv.className = "modal";
  modalDiv.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">Choose Export Type</div>
      <div class="modal-body">
        <button class="report-option" data-type="single"> 
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

function addActionsButton() {
  for (const tr of userTableBody.querySelectorAll("tr")) {
    if (tr.querySelector("td.actions")) continue;

    const actionElem = document.createElement("td");
    actionElem.className = "actions";
    actionElem.innerHTML = `
      <button class="get-assignment">
        Assignment(s)
      </button>
    `;
    tr.appendChild(actionElem);
  }
}