document.addEventListener("DOMContentLoaded", () => {
  const assetsToAssign = JSON.parse(sessionStorage.getItem("assetsToAssign"));
	const userAssigned = JSON.parse(sessionStorage.getItem("assignToUser"));
  const assetForm = document.querySelector(".assign-asset-form"); 

  // console.log(assetsToAssign);
	// console.log(Array.isArray(assetsToAssign))
	// console.log(userAssigned);
  if (!assetsToAssign || !userAssigned) return;

  fillForm(assetsToAssign, userAssigned);

	fetch("../../src/handlers/add-assignment-form.php", {
		method: "POST",
		headers: { "Content-Type": "application/x-www-form-urlencoded" },
		body: `assets=${encodeURIComponent(assetsToAssign)}&user=${encodeURIComponent(userAssigned)}`,
	})
  .catch(err => console.error("Error fetching: ", err))

  // sessionStorage.removeItem("assetsToAssign");
	// sessionStorage.removeItem("assignToUser");

  function fillForm(assets, user) {
    p_asset = assetForm.querySelector('#asset-list');
		assetForm.querySelector("#chosen-user").textContent = `EmpID: ${user}`;
		textContent = "";
    for (const asset of assets) {
      textContent += `PropNum: ${asset}, `
    }
		p_asset.textContent = textContent.slice(0,-2);
  }
});
		