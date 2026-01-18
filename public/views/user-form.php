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
    Privilege: 
    <label>
      Faculty
      <input 
        type="radio" 
        id="f" 
        name="privilege" 
        value="Faculty" 
        required 
      > 
    </label>
    <label>
      Admin 
      <input 
        type="radio" 
        id="a" 
        name="privilege" 
        value="Admin"
      > 
    </label>
    <label>
      Super Admin
      <input 
        type="radio" 
        id="sa" 
        name="privilege" 
        value="SuperAdmin"
      > 
    </label>
  </label>

  <label class="input-label">
    Status: 
    <label>
      Active 
      <input 
        type="radio" 
        id="act" 
        name="active-status" 
        value="Active" 
        required 
      > 
    </label>
    <label>
      Inactive 
      <input 
        type="radio" 
        id = "inact" 
        name="active-status" 
        value="Inactive"
      > 
    </label>
  </label>

  <button id="reset-button" type="reset">
    Reset
  </button>

  <button id="submit-button" type="submit" name="action" value="submit">
    Submit
  </button>  
</form>
