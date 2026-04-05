const assetsToAssign = JSON.parse(sessionStorage.getItem("assetsToAssign"));
const userAssigned = JSON.parse(sessionStorage.getItem("assignToUser"));
const form = document.getElementById("assign-asset-form"); 

fillForm(assetsToAssign, userAssigned);

function fillForm(assets, user) {
  const formattedAssets = assets.join(", ");
  document.getElementById('asset-list').textContent = `Property No's: ${formattedAssets}`;
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
  input.value = user.EmpID;
  form.appendChild(input);      
}
  