document.addEventListener("DOMContentLoaded", () => {
  const assetToReturn = JSON.parse(sessionStorage.getItem("assetToReturn"));
  const assetForm = document.querySelector(".assign-asset-form"); 

  if (!assetToReturn) return;

  fillForm(assetToReturn);

  function fillForm(asset) {
    assetForm.querySelector('#asset-list').textContent = `PropNum: ${asset}`

    // add extra data with form submission by appending hidden input fields
    const form = assetForm.querySelector("form");
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "asset";
    input.value = asset;
    form.appendChild(input);      
  }
});
		