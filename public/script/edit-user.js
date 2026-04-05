import { fetchLogs } from "./act-log.js";
import { returnAssets } from "./asset-router.js";
import { assignUser } from "./user-router.js";

const userData = JSON.parse(sessionStorage.getItem("userData"));
const userForm = document.querySelector("form"); 

const assignmentTable = document.querySelector(".assignment-table");
const assignmentTableBody = assignmentTable.querySelector("tbody");
const addAssignBtn = document.getElementById("add-assignment-button");
const exportButton = document.getElementById("export-assignment");

let assignmentData = new Map();
let latest = 0; // latest fetch id to avoid race conditions

userForm.action = `${window.location.origin}/api/index.php?resource=users&action=edit`;
userForm.method = "post";
  
const user = Array.isArray(userData) ? userData[0] : userData;

fillForm(user);
fetchAssignments()
fetchLogs({actorID: user.EmpID});

if (user['EmpID'] === JSON.parse(sessionStorage.getItem("user-info")).empID) {
  userForm.querySelector("input#inact").disabled = true;
}

const input = document.createElement("input");
input.type = "hidden";
input.name = "employee-id";
input.value = user["EmpID"];
userForm.appendChild(input);

const resetBtn = document.getElementById("reset-button");
resetBtn?.addEventListener("click", (_) => {
  fillForm(user);
})

userForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  
  const assignments = await getAssignments(user["EmpMail"]);

  const oldStatus = user['ActiveStatus'];
  const newStatus = document.querySelector('input[name="active-status"]:checked').value;
  
  if (oldStatus === "Active" && oldStatus!==newStatus){ 
    if (assignments>0) {
      const sub = confirm("This user has assigned assets. Are you sure you want to deactivate this user?");

      if (!sub) return;
    }
  }

  userForm.submit();
})

assignmentTableBody.addEventListener("click", (e) => {
  const tr = e.target.closest("tr");
  if (!tr) return;

  if (e.target.closest(".select-btn")) {
    returnAssets([tr.dataset.propNum]);
    return;
  }
});

addAssignBtn.addEventListener("click", () => {
  assignUser(user.EmpID);
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
