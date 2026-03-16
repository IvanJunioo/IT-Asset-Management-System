const userData = JSON.parse(sessionStorage.getItem("userData"));
const userForm = document.querySelector(".user-form"); 
const form = userForm.querySelector("form");

form.action = `${window.location.origin}/api/index.php?resource=users&action=edit`;
form.method = "post";
  
const user = Array.isArray(userData) ? userData[0] : userData;

fillForm(user);

if (user['EmpID'] === JSON.parse(sessionStorage.getItem("user-info")).empID) {
  form.querySelector("input#inact").disabled = true;
}

const input = document.createElement("input");
input.type = "hidden";
input.name = "employee-id";
input.value = user["EmpID"];
form.appendChild(input);

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

const resetBtn = document.getElementById("reset-button");
resetBtn?.addEventListener("click", (_) => {
  fillForm(user);
})


form.addEventListener("submit", async (e) => {
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

  form.submit();
})

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