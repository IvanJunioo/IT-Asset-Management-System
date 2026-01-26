<form>
  <label class="input-label"> 
    Employee ID: 
    <input 
      type="text" 
      id="empid" 
      name="employee-id" 
      placeholder="Enter Employee ID" 
      maxlength="8" 
      minlength="8" 
      size="8" 
      required
    >
  </label>

  <label class="input-label"> 
    First Name: 
    <input 
      type="text" 
      id="fn" 
      name="first-name" 
      placeholder="Enter First Name" 
      maxlength="20" 
      size="20" 
      required
    >
  </label>

  <label class="input-label"> 
    Middle Name: 
    <input 
      type="text" 
      id="mn" 
      name="middle-name" 
      placeholder="Enter Middle Name" 
      maxlength="20" 
      size="20"
    >
  </label>

  <label class="input-label"> 
    Last Name: 
    <input 
      type="text" 
      id="ln" 
      name="last-name" 
      placeholder="Enter Last Name" 
      maxlength="20" 
      size="20" 
      required
    >
  </label>

  <label class="input-label"> 
    Email: 
    <input 
      type="email" 
      id="e" 
      name="email" 
      placeholder="Enter UP Mail" 
      maxlength="50" 
      size="30" 
      required
    > 
  </label>

  <label class="input-label"> 
    Contact Number: 
    <div class="input-rows">
      <div class="input-row">
        <input 
          type="tel" 
          name="phone[]" 
          placeholder="Enter Contact No" 
          maxlength="16"
          pattern="^[\+0-9\-\(\)\s]{7,20}$"
          size="30" 
          required
        >
        <button type="button" class="add-input">
          <span class="material-icons">add</span>
        </button>
      </div>
    </div>
  </label>

  <label class="input-label"> 
    Privilege: 
    <label>
      <input 
        type="radio" 
        id="f" 
        name="privilege" 
        value="Faculty" 
        required 
      > 
      Faculty
    </label>
    <label>
      <input 
        type="radio" 
        id="a" 
        name="privilege" 
        value="Admin"
      > 
      Admin 
    </label>
    <label>
      <input 
        type="radio" 
        id="sa" 
        name="privilege" 
        value="SuperAdmin"
      > 
      Super Admin
    </label>
  </label>

  <label class="input-label">
    Status: 
    <label>
      <input 
        type="radio" 
        id="act" 
        name="active-status" 
        value="Active" 
        checked
      > 
      Active
    </label>
  </label>

  <button id="reset-button" type="reset">
    Reset
  </button>

  <button id="submit-button" type="submit" name="action" value="submit">
    Submit
  </button>  
</form>

<script type="module" defer>
  document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    form.addEventListener("click", (e) => {
      if (e.target.closest(".add-input")) {
        const rows = e.target.closest(".input-rows");
        
        const row = rows.querySelector(".input-row").cloneNode(true);
        row.querySelector("input").value = "";
        const btn = row.querySelector("button");
        btn.className = "remove-input";
        btn.querySelector("span").textContent = "remove";

        rows.appendChild(row);
        return;
      }

      if (e.target.closest(".remove-input")) {
        e.target.closest(".input-row").remove();      
        return;
      }
    });
  });
</script>
