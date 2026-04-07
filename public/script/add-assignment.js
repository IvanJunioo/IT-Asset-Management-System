import { fetchUser } from "./user-router.js";

const urlParams = new URLSearchParams(window.location.search);
const assets = urlParams.getAll("propNums[]");
const empID = urlParams.get("empID");
const user = await fetchUser(empID);

const form = document.getElementById("assign-asset-form"); 

fillForm();

function fillForm() {
  document.getElementById('asset-list').textContent = `Property No's: ${assets.join(", ")}`;
  document.getElementById("chosen-user").textContent = `${user.FName} ${user.LName}`;

  const now = new Date();
  const today = now.getFullYear() + '-' +
  (now.getMonth() + 1 < 10 ? '0' : '') + (now.getMonth() + 1) + '-' +
  (now.getDate() < 10 ? '0' : '') + now.getDate() + 'T' +
  (now.getHours() < 10 ? '0' : '') + now.getHours() + ':' +
  (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();

  document.getElementById("adate").value = today;
  document.getElementById("adate").setAttribute('max', today);

  // add extra data with form submission by appending hidden input fields
  for (const asset of assets) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "assets[]";
    input.value = asset;
    form.appendChild(input);      
  }
  const input = document.createElement("input");
  input.type = "hidden";
  input.name = "user";
  input.value = empID;
  form.appendChild(input);      
}
  