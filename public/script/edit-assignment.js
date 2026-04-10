const urlParams = new URLSearchParams(window.location.search);
const assets = urlParams.getAll("propNums[]");
const form = document.getElementById("return-asset-form"); 

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
  