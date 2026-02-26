<div class="left-user">
  <div id="search-box">
    <input type="search" id="search-input" placeholder="Search user">
    <span class="material-icons search-icon">search</span>
  </div>

  <div class="filter-box" id="filter-box-inline">
    <div class="head-filter">
      FILTERS
    </div>

    <div class="body-filter">
      <label>
        <input type="checkbox" name="privilege" value="Faculty"> 
        Faculty
      </label>
      <label>
        <input type="checkbox" name="privilege" value="Staff"> 
        Staff
      </label>
      <label>
        <input type="checkbox" name="privilege" value="Admin"> 
        Admin
      </label>
      <label>
        <input type="checkbox" name="privilege" value="SuperAdmin"> 
        SuperAdmin
      </label>
    </div>

    <div class="body-filter">
      <label>
        <input type="checkbox" name="status" value="Active"> 
        <span class="badge active">Active</span>
      </label>
      <label>
        <input type="checkbox" name="status" value="Inactive"> 
        <span class="badge inactive">Inactive</span>
      </label>
    </div>
      
    <button class="apply-filter"> Reset Filters </button>
  </div>

  <div class="table-container">
    <table class="user-table">
      <thead>
        <tr>
          <th> <span>Email</span> </th> 
          <th> <span>First Name</span> </th>
          <th> <span>Last Name</span> </th>
          <th> <span>Privilege</span> </th>
          <th> <span>Status </span> </th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
<div class="right-user">
  <div class="filter-box" id="filter-box-reg">
    <div class="head-filter">
      FILTERS
    </div>

    <div class="body-filter">
      <label>
        <input type="checkbox" name="privilege" value="Faculty"> 
        Faculty
      </label>
      <label>
        <input type="checkbox" name="privilege" value="Staff"> 
        Staff
      </label>
      <label>
        <input type="checkbox" name="privilege" value="Admin"> 
        Admin
      </label>
      <label>
        <input type="checkbox" name="privilege" value="SuperAdmin"> 
        SuperAdmin
      </label>
    </div>

    <div class="body-filter">
      <label>
        <input type="checkbox" name="status" value="Active"> 
        <span class="badge active">Active</span>
      </label>
      <label>
        <input type="checkbox" name="status" value="Inactive"> 
        <span class="badge inactive">Inactive</span>
      </label>
    </div>
      
    <button class="apply-filter"> Reset Filters </button>
  </div>
  <button id = "report" class="generate"> Get Assigned Assets </button>
</div>

<script src="/../../script/user-table.js" type="module" defer></script>
