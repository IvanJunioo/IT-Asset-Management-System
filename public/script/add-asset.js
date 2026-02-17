document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);

  if (params.get("pNumError") === "exists") {
    alert("One or more property numbers entered already exists");
  } 
  if (params.get("pNumError") === "dupEntry") {
    alert("You entered a duplicate entry");
  }

  const form = document.querySelector("form");
  form.action = `${window.location.origin}/src/handlers/add-asset-form.php`;
  form.method = "post";

  // Hide toCondemn radio button
  const toCondemnGrp = document.getElementById('tocondemn');
  toCondemnGrp.style.display = 'none';
  const unassignedBtn = document.getElementById('unused');
  unassignedBtn.setAttribute('checked', true);

  const date = new Date();
  const today = `${date.getFullYear().toString()}-${(date.getMonth() + 1).toString().padStart(2,'0')}-${date.getDate().toString().padStart(2,'0')}`;
  const pdate = document.getElementById('pdate');
  pdate.setAttribute('max', today);
  pdate.value = today;

  // Add "+" button
  const table = document.querySelector("#unique-asset-attr");
  const head = table.querySelector("thead").querySelector("tr");
  const body = table.querySelector("#input-row");

  const lastHead = document.createElement("th");
  lastHead.className = "last-child";
  const entry = document.createElement("td");
  entry.className = "last-child";
  entry.innerHTML = `<button type="button" class="add-input">
            <span class="material-icons">add</span>
          </button>`;
          
  head.appendChild(lastHead);
  body.appendChild(entry);

  // Make propNum, serialNum, Support Docs URL multivalued
  form.querySelector("input#pnum").name = "property-num[]";
  form.querySelector("input#snum").name = "serial-num[]";
  form.querySelector("input#img_url").name = "img-url[]";
  
form.addEventListener("input", (e) => {
  if (e.target.closest("input#pnum")){
    const pnums = Array.from(document.querySelectorAll("input#pnum"));
    let dupPnum = checkDuplicate(pnums);
    if (dupPnum){
      alert("You entered a duplicate property number");
    }
  } 
  if (e.target.closest("input#snum")){
    const snums = Array.from(document.querySelectorAll("input#snum"));
    let dupSnum = checkDuplicate(snums);
    if (dupSnum) {
      alert("You entered a duplicate serial number");
    }
  }
}) 


form.addEventListener("click", (e) => {
  const addBtn = e.target.closest(".add-input");
  if (addBtn) {
    const tbody = document.querySelector("#unique-asset-attr tbody");
    const row = addBtn.closest("#input-row").cloneNode(true);

    row.querySelectorAll("input").forEach(input => input.value = "");

    const btn = row.querySelector("button");
    btn.className = "remove-input";
    btn.querySelector("span").textContent = "remove";

    tbody.appendChild(row);
    return;
  }

  const removeBtn = e.target.closest(".remove-input");
  if (removeBtn) {
    removeBtn.closest("#input-row").remove();
    return;
  }

});

});

function checkDuplicate(array) {
  const set = new Set();
  array.forEach(inp => set.add(inp.value.trim()));
  if (set.size != array.length) {
    return true;
  }
  return false;
}