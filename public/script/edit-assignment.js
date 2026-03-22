const assetsToReturn = JSON.parse(sessionStorage.getItem("assetsToReturn"));
const assetForm = document.querySelector(".assign-asset-form"); 

fillForm(assetsToReturn);

function fillForm(assets) {
  const p_asset = assetForm.querySelector('#asset-list');
  const formattedAssets = assets.join(", ");

  p_asset.textContent = `${formattedAssets}`;

  // add extra data with form submission by appending hidden input fields
  const form = assetForm.querySelector("form");
  for (const asset of assets) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "assets[]";
    input.value = asset;
    form.appendChild(input);      
  }   
}
  