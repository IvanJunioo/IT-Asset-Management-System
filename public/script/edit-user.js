import { fetchLogs } from "./act-log.js";
import { relayPage } from "./asset-router.js";
import { fetchUser } from "./user-router.js";

const urlParams = new URLSearchParams(window.location.search);
const userData = await fetchUser(urlParams.get("empID"));
const sessionUserData = await fetchSessionUser();

const userForm = document.querySelector("form"); 
const tableFuncs = document.querySelector(".table-func");
const assignmentTable = document.querySelector(".assignment-table");
const assignmentTableBody = assignmentTable.querySelector("tbody");
const addAssignBtn = document.getElementById("add-assignment-button");
const exportButton = document.getElementById("export-assignment");

let assignmentData = new Map();
let latest = 0; // latest fetch id to avoid race conditions
let selectedRows = new Set();
let inMultiSelect = false;

userForm.action = `${window.location.origin}/api/index.php?resource=users&action=edit&redirect=${encodeURIComponent("index.php?page=user-manager")}`;
userForm.method = "post";

const user = Array.isArray(userData) ? userData[0] : userData;
const sessionUser = sessionUserData;

fillForm(user);
fillReadOnly(user);
fetchAssignments();
fetchLogs({actorID: user.EmpID});

const input = document.createElement("input");
input.type = "hidden";
input.name = "employee-id";
input.value = user["EmpID"];
userForm.appendChild(input);

// add session user role as hidden input for backend validation
const sessionRoleInput = document.createElement("input");
sessionRoleInput.type = "hidden";
sessionRoleInput.name = "session-user-role";
sessionRoleInput.value = sessionUser?.Privilege || "";
userForm.appendChild(sessionRoleInput);

const resetBtn = document.getElementById("reset-button");
resetBtn?.addEventListener("click", (_) => {
  fillForm(user);
})

userForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  
  const assignments = await getAssignments(user["EmpMail"]);

  const oldStatus = user['ActiveStatus'];
  const newStatus = document.querySelector('input[name="active-status"]:checked').value;
  
  if (oldStatus === "Active" && oldStatus !== newStatus){ 
    if (0 < assignments) {
      const sub = confirm("This user has assigned assets. Are you sure you want to deactivate this user?");

      if (!sub) return;
    }
  }

  userForm.submit();
})

assignmentTable.addEventListener("click", (e) => {
  const tr = e.target.closest("tr");
  if (!tr) return;

  if (e.target.closest("#select-all")) {
    const rows = Array.from(assignmentTableBody.querySelectorAll("tr"))
    .filter(tr => tr.dataset.propNum);

    const allSelected = rows.every(tr => selectedRows.has(tr));

    if (allSelected) {
      rows.forEach(deselectRow);
      selectedRows.clear();
    } else {
      rows.forEach(selectRow);
    }
    return;
  }

  if (e.target.closest(".select-btn")) {
    relayPage("return-form", {
      "redirect": "index.php" + window.location.search,
      "propNums[]": tr.dataset.propNum,
    });
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

})

// assignmentTableBody.addEventListener("click", (e) => {
  
// });

addAssignBtn.addEventListener("click", () => {
  relayPage("assign-asset", {
    "redirect": "index.php" + window.location.search,
    "empID": user.EmpID,
  });
});

exportButton.addEventListener("click", () => {
  let url = `${window.location.origin}/api/index.php?resource=export&action=user-assets&user=${user.EmpID}`;

  if ( document.getElementById("inc-remarks").checked) {
    url += "&add_remarks=true"
  }

  window.open(
    url,
    "_blank"
  );
})

tableFuncs.addEventListener("click", (e) => {
  if (e.target.closest("#return")) {
    if (selectedRows.size === 0) return;
    relayPage("return-form", {
      "redirect": "index.php" + window.location.search,
      "propNums[]": [...selectedRows].map(tr => tr.dataset.propNum),
    });
  }

  if (e.target.closest("#multi-select")) {
    const multiSelectBtn = e.target.closest("#multi-select");
    
    multiSelectBtn.classList.toggle('active');

    setInMulSel(!inMultiSelect);
  }
});

function fillForm(user) {
  const data = {
    'e': user['EmpMail'],
    'fn': user['FName'],
    'ln': user['LName'],
  };

  const childrenInput = userForm.querySelectorAll('input');
  for (const child of childrenInput) {
    if (child.id in data) {
      child.value = data[child.id];
    } else if (child.value === user['Privilege']) {
      child.checked = true;
    } else if (child.value === user['ActiveStatus']){
      child.checked = true;
    }
  }
}

async function getAssignments(employee) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: "search",
    search: employee,
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);
    const data = await resp.json();
    const assignments = data[0]['assignments'];
    return assignments.length;
  } catch (err) {
    console.error("Error fetching users: ", err);
  }
  
  return 0;
}

async function fetchAssignments() {    
  const fetchID = ++latest;

  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "assignment",
    action: "fetch",
    user: user.EmpID,
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);

    
    const data = await resp.json();
    assignmentData = new Map(data.map(asset => [asset.PropNum, asset]));
    
    if (fetchID !== latest) return;
    showAssignments();
  } catch (err) {
    console.error("Error fetching assets: ", err);
  }
}

function showAssignments() {
  if (assignmentData.size <= 0) {
    assignmentTableBody.innerHTML = `
      <tr>
        <td colSpan="${assignmentTable.querySelector("thead tr").children.length}" style="text-align: center;"> No assets to display. </td>
      </tr>
    `;
    return;
  }

  if (!tableFuncs.querySelector("#multi-select")){
    addTableFuncs();
  }
  
  // Add another header
  const hr = assignmentTable.querySelector("thead tr");
  if (!hr.querySelector("#actionsth")) {
    const actionsth = document.createElement("th");
    actionsth.id = "actionsth";
    hr.appendChild(actionsth);
  }

  assignmentTableBody.innerHTML = "";
  
  for (const [_, asset] of assignmentData) {
    const tr = document.createElement('tr');

    // Store id
    tr.dataset.propNum = asset.PropNum;

    for (const col of [
      asset.PropNum,
      asset.AssignedOn, 
      `<span class="badge ${asset.Status.toLowerCase()}">${asset.Status}</span>`,
    ]) {
      const td = document.createElement("td");
      td.innerHTML = col;
      tr.appendChild(td);
    }

    // Action button
    const td = document.createElement("td");
    td.innerHTML = `
      <button class="select-btn">
        <span class="material-icons">assignment_return</span>
        Return
      </button>
    `;
    tr.append(td);

    assignmentTableBody.appendChild(tr);
  }
}

async function fetchSessionUser() {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: "session",
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);
    const data = await resp.json();
    return data;
  } catch (err) {
    console.error("Error fetching session user: ", err);
    return null;
  }
}

function fillReadOnly(user) {
  // only fill if read-only display fields exist (for Admin users)
  const privilegeDisplay = document.getElementById("privilege-display");
  const statusDisplay = document.getElementById("status-display");

  if (privilegeDisplay) {
    privilegeDisplay.innerHTML = `<strong>Privilege:</strong> <span style="display: inline-block; padding: 0.5rem;">${user['Privilege']}</span>`;
  }

  if (statusDisplay) {
    const badgeClass = user['ActiveStatus'].toLowerCase();
    statusDisplay.innerHTML = `<strong>Status:</strong> <span style="display: inline-block; padding: 0.5rem;"><span class="badge ${badgeClass}">${user['ActiveStatus']}</span></span>`;
  }
}

function addTableFuncs() {
  tableFuncs.insertAdjacentHTML("afterbegin", `
    <button id="multi-select">
      <span class="material-icons"> check_box_outline_blank </span>
      Select Multiple
    </button>
  `);
}

function setInMulSel(val) {
  if (inMultiSelect && !val) {
    document.querySelectorAll("#select-all").forEach(btn => btn.remove());
    
    document.getElementById("return")?.remove();
  
    selectedRows.clear();

    for (const tr of assignmentTableBody.querySelectorAll("tr")) {
      tr.lastElementChild.innerHTML = `
        <button class="select-btn" id="select">
          Select
        </button>
      `;
    }
  }

  if (!inMultiSelect && val) {
    addSelectAll();
    addCheckboxes();
    addReturnButton();
  }

  inMultiSelect = val;
  
  const multiSelectIcon = document.querySelector("#multi-select .material-icons");
  if (multiSelectIcon) {
    multiSelectIcon.textContent = val ? "check_box" : "check_box_outline_blank";
  }
}

function addSelectAll() {
  const hr = assignmentTable.querySelector("thead tr");
  hr.lastElementChild.innerHTML = `
    <button id="select-all">
      <span class="material-icons"> select_all </span>
    </button>
  `;
}

function addCheckboxes() {
  for (const tr of assignmentTableBody.querySelectorAll("tr")) {
    const icon = selectedRows.has(tr) ? "check_box" : "check_box_outline_blank";
    tr.lastElementChild.innerHTML = `
      <button class="selectable-row">
        <span class="material-icons"> ${icon} </span>
      </button>
    `;
  }
}

function addReturnButton() {
  const tableFuncs = document.querySelector(".table-func");

  const returnButton = document.createElement("button");
  returnButton.id = "return";
  returnButton.innerHTML = `<span class="material-icons">assignment_return</span> Return`;
  if (!tableFuncs.querySelector("#return")) tableFuncs.prepend(returnButton);
}

function selectRow(tr) {
  selectedRows.add(tr);
  const icon = tr.querySelector(".material-icons");
  if (icon) icon.textContent = "check_box";
}

function deselectRow(tr) {
  selectedRows.delete(tr);
  const icon = tr.querySelector(".material-icons");
  if (icon) icon.textContent = "check_box_outline_blank";
}

function addActionsButton() {
  const hr = assetTable.querySelector("thead tr");
  if (!hr.querySelector("#actionsth")) {
    const actionsth = document.createElement("th");
    actionsth.id = "actionsth";
    hr.appendChild(actionsth);
  }

  for (const tr of assetTableBody.querySelectorAll("tr")) {
    if (tableData.get(tr.dataset.propNum).Status !== "Unassigned"){
      continue;
    }

    tr.lastElementChild.innerHTML = `
      <button class="select-btn" id="select">
        Select
      </button>
    `;
  }
}

