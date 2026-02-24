document.addEventListener("DOMContentLoaded", () => {
  const assetsToAssign = JSON.parse(sessionStorage.getItem("assetsToAssign"));
	const userAssigned = JSON.parse(sessionStorage.getItem("assignToUser"));
  const assetForm = document.querySelector(".assign-asset-form"); 

  if (!assetsToAssign || !userAssigned) return;

  fillForm(assetsToAssign, userAssigned);

  function fillForm(assets, user) {
    document.getElementById('asset-list').textContent = `Property No's: ${assets}`;
		document.getElementById("chosen-user").textContent = `${user.fName} ${user.lName}`;

    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById("adate").value = now.toISOString().slice(0, 16);

    // add extra data with form submission by appending hidden input fields
    const form = assetForm.querySelector("form");
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
    input.value = user.empID;
    form.appendChild(input);      
  }
});
		