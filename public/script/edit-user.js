import { fetchLogs } from "./act-log.js";
import { relayPage } from "./asset-router.js";
import { fetchUser } from "./user-router.js";

const urlParams = new URLSearchParams(window.location.search);
const userData = await fetchUser(urlParams.get("empID"));
const sessionUserData = await fetchSessionUser();

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
const sessionUser = sessionUserData;
const isSuperAdmin = sessionUser?.Privilege === "SuperAdmin";

fillForm(user);
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

// trabsform form to read-only for non SuperAdmin users
if (!isSuperAdmin) {
  transformFormToReadOnly();
}

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
    relayPage("return-form", {
      "retPage": window.location.href,
      "propNums[]": tr.dataset.propNum,
    });
    return;
  }
});

addAssignBtn.addEventListener("click", () => {
  relayPage("assign-asset", {
    "retPage": window.location.href,
    "empID": user.EmpID,
  });
});

userForm.addEventListener("submit", async (_) => {
  window.location.href = "index.php?page=user-manager";
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

function transformFormToReadOnly() {
  const formTitle = document.querySelectorAll(".card")[0].querySelector("h3").textContent = "User Details";

  // just disable the inputs and add a not-allowed cursor so the UI doesn't feel empty
  const textInputs = userForm.querySelectorAll('input[type="text"], input[type="email"]');
  for (const input of textInputs) {
    input.disabled = true;
    input.style.cursor = "not-allowed";
  }

  const radioGroups = new Map();
  const radioInputs = userForm.querySelectorAll('input[type="radio"]');
  for (const input of radioInputs) {
    if (!radioGroups.has(input.name)) {
      radioGroups.set(input.name, []);
    }
    radioGroups.get(input.name).push(input);
  }

  // for each radio group, display the selected value
  for (const [groupName, inputs] of radioGroups) {
    const checkedInput = inputs.find(input => input.checked);
    if (checkedInput) {
      const label = checkedInput.closest('label');
      if (label) {
        const displaySpan = document.createElement('span');
        
        if (groupName === 'active-status') {
          const badgeSpan = document.createElement('span');
          badgeSpan.className = `badge ${checkedInput.value.toLowerCase()}`;
          badgeSpan.textContent = checkedInput.value;
          displaySpan.appendChild(badgeSpan);
          displaySpan.style.cssText = "display: inline-block; padding: 0.5rem;";
        } else {
          displaySpan.textContent = checkedInput.value;
          displaySpan.style.cssText = "display: inline-block; padding: 0.5rem;";
        }
        
        label.replaceWith(displaySpan);
      }
    }
    
    // remove unchecked radio inputs
    for (const input of inputs) {
      const label = input.closest('label');
      if (label && !input.checked) {
        label.remove();
      }
    }
  }

  // remove reset and submit button
  const resetBtn = document.getElementById("reset-button");
  const submitBtn = userForm.querySelector('button[type="submit"]');
  resetBtn?.remove();
  submitBtn?.remove();
}
