document.addEventListener("DOMContentLoaded", () => {
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
  document.getElementById('pdate').setAttribute('max', today);

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