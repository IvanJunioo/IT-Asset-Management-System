document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");

  form.addEventListener("click", (e) => {
    if (e.target.closest(".add-input")) {
      const rows = e.target.closest(".input-rows");
      
      const row = document.createElement("div");
      row.className = "input-row";
      row.innerHTML = `
        <input type="text" name="property-num[]" placeholder="Enter Property Number" maxlength="12" minlength="12" size = "12" required>
        <button type="button" class="remove-input">
          <span class="material-icons">remove</span>
        </button>
      `;

      rows.appendChild(row);
      return;
    }

    if (e.target.closest(".remove-input")) {
      e.target.closest(".input-row").remove();      
      return;
    }
  });
});