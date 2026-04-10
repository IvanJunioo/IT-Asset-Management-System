import { fetchUser } from "./user-router.js";

const urlParams = new URLSearchParams(window.location.search);
const assets = urlParams.getAll("propNums[]");
const empID = urlParams.get("empID");
const user = await fetchUser(empID);

const form = document.getElementById("assign-asset-form"); 

fillForm();

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  try {
    await fetch(e.target.action, {
      method: "POST",
      body: new FormData(e.target),
    });

    window.location.href = urlParams.get("retPage") ?? "index.php?page=dashboard";
  } catch (err) {
    console.error("Error submitting form: ", err);
  }
});

function fillForm() {
  document.getElementById('asset-list').textContent = `Property No's: ${assets.join(", ")}`;
  document.getElementById("chosen-user").textContent = `${user.FName} ${user.LName}`;

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
  