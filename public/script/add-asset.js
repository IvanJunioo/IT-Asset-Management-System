document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  form.action = `${window.location.origin}/src/handlers/add-asset-form.php`;
  form.method = "post";

  // Replace Property Num input field
  const pNumLabel = form.querySelector(".input-label");
  pNumLabel.innerHTML = `
    Property Number(s): 
    <div class="input-rows">
      <div class="input-row">
        <input 
          type="text" 
          name="property-num[]" 
          placeholder="Enter Property Number" 
          maxlength="12" 
          minlength="12" 
          size="12" 
          required
        >
        <button type="button" class="add-input">
          <span class="material-icons">add</span>
        </button>
      </div>
    </div>
  `;

  // Hide toCondemn radio button
  const toCondemnGrp = document.getElementById('tocondemn');
  toCondemnGrp.style.display = 'none';
  const unassignedBtn = document.getElementById('unused');
  unassignedBtn.setAttribute('checked', true);
});