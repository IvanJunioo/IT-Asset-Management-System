document.addEventListener("DOMContentLoaded", () => {
  const assetsToAssign = JSON.parse(sessionStorage.getItem("assetsToAssign"));
	const userAssigned = JSON.parse(sessionStorage.getItem("assignToUser"));
  const assetForm = document.querySelector(".assign-asset-form"); 

  if (!assetsToAssign || !userAssigned) return;

  fillForm(assetsToAssign, userAssigned);

  function fillForm(assets, user) {
    const p_asset = assetForm.querySelector('#asset-list');
		assetForm.querySelector("#chosen-user").textContent = `EmpID: ${user}`;
		// textContent = "";
    // for (const asset of assets) {
    //   textContent += `PropNum: ${asset}, `
    // }
		p_asset.textContent = `PropNum(s): ${assets}`;

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
    input.value = user;
    form.appendChild(input);      
  }
});
		