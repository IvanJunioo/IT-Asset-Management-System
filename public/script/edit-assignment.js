const urlParams = new URLSearchParams(window.location.search);
const assets = urlParams.getAll("propNums[]");
const form = document.getElementById("return-asset-form");
form.action = `${window.location.origin}/api/index.php?resource=assignment&action=return&redirect=${encodeURIComponent(urlParams.get("redirect") ?? "index.php?page=dashboard")}`;

fillForm();

function fillForm() {
  const p_asset = form.querySelector('#asset-list');

  p_asset.textContent = `${assets.join(", ")}`;

  // add extra data with form submission by appending hidden input fields
  for (const asset of assets) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "assets[]";
    input.value = asset;
    form.appendChild(input);      
  }   
}
  