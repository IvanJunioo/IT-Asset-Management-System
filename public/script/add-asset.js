import { searchAssets } from "./api.js";

const params = new URLSearchParams(window.location.search);

if (params.get("pNumError") === "exists") {
  alert("One or more property numbers entered already exists");
} 

const form = document.querySelector("form");
form.action = `${window.location.origin}/api/index.php?resource=assets&action=add&redirect=${encodeURIComponent("index.php?page=asset-manager")}`;
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
form.querySelector("input.pnum").name = "property-num[]";
form.querySelector("input.snum").name = "serial-num[]";
form.querySelector("input.img_url").name = "img-url[]";

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const pnums = Array.from(document.querySelectorAll("input.pnum")).map(inp => inp.value);
  const snums = Array.from(document.querySelectorAll("input.snum")).map(inp => inp.value);

  try {
    if (new Set(pnums).size !== pnums.length) throw new Error(`Property numbers must be unique!`);
    if (new Set(snums).size !== snums.length) throw new Error(`Serial numbers must be unique!`);

    const pnumChecks = pnums.map(async (pnum) => {
      let data = await searchAssets({search: pnum});
      if (data[0]) throw new Error(`Property number ${pnum} already exists!`);
    });

    const snumChecks = snums.map(async (snum) => {
      let data = await searchAssets({search: snum, check_snum: true});
      if (data[0]) throw new Error(`Serial number ${snum} already exists!`);
    });

    await Promise.all([...pnumChecks, ...snumChecks]);
    
    form.submit();
  }
  catch (err) {
    alert(err.message);
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
