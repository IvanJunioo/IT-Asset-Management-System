const assetsToReturn = JSON.parse(sessionStorage.getItem("assetsToReturn"));
const form = document.getElementById("return-asset-form"); 

fillForm(assetsToReturn);

function fillForm(assets) {
  const p_asset = form.querySelector('#asset-list');
  const formattedAssets = assets.join(", ");

  p_asset.textContent = `${formattedAssets}`;

  // add extra data with form submission by appending hidden input fields
  for (const asset of assets) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "assets[]";
    input.value = asset;
    form.appendChild(input);      
  }   
}
  