const params = new URLSearchParams(window.location.search);

if (params.get("pNumError") === "exists") {
  alert("One or more property numbers entered already exists");
} 

const form = document.querySelector("form");
form.action = `${window.location.origin}/api/index.php?resource=assets&action=add`;
form.method = "post";

// Hide toCondemn radio button
const toCondemnGrp = document.getElementById('tocondemn');
toCondemnGrp.style.display = 'none';
const unassignedBtn = document.getElementById('unused');
unassignedBtn.setAttribute('checked', true);

// Add "+" button
const table = document.getElementById("unique-asset-attr");
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

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const pnums = Array.from(document.querySelectorAll("input#pnum"));
  const snums = Array.from(document.querySelectorAll("input#snum"));
  let valid = true;

  let dataPnum = await checkIfExists(pnums);
  let dataSnum = await checkIfExists(snums);
  let dupPnum = checkDuplicate(pnums);
  let dupSnum = checkDuplicate(snums);

  if (dataPnum) {
    valid = false;
    alert(`The property number ${dataPnum.PropNum} already exists`);
  }
  else if (dataSnum) {
    valid = false;
    alert(`The serial number ${dataSnum.SerialNum} already exists`);
  }
  else if (dupPnum) {
    valid = false;
    alert(`Please fix the duplicate Property Number: ${dupPnum}`);
  }
  else if (dupSnum) {
    valid = false;
    alert(`Please fix the duplicate Serial Number: ${dupSnum}`);
  }

  if (valid){
    form.submit();
    window.location.href = "index.php?page=asset-manager";
  }
});


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

const resetBtn = document.getElementById("reset-button");
resetBtn?.addEventListener("click", (_) => {
  form.reset();

  const date = new Date();
  const today = `${date.getFullYear()}-${(date.getMonth() + 1)
    .toString()
    .padStart(2, "0")}-${date.getDate().toString().padStart(2, "0")}`;

  const pdate = document.getElementById("pdate");
  if (pdate) {
    pdate.value = today;
  }
})

function checkDuplicate(inputs) {
  const set = new Set();
  for (const inp of inputs) {
    if (inp.value === '') continue

    if (set.has(inp.value)){
      return inp.value;
    }
    set.add(inp.value);
  }
  return "";
}

async function checkIfExists(inputs) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  for (const inp of inputs){
    if (inp.value === '') continue
    url.search = new URLSearchParams({
      resource: "assets",
      action: "search",
      search: inp.value,
      check_snum: true
    });

    try {
      const resp = await fetch(url);
      if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);
      const data = await resp.json();
      if (data && data.length > 0) return data[0];
    } catch (err) {
      console.error("Error fetching users: ", err);
    }
  }
  return null;
}
