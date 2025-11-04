<main class="user-page">
  <div class="left-user">
    <div id="search-box">
      <input type="search" id="search-input" placeholder="Search user">
      <button id="search-button"> Search </button>
    </div>
    
    <div class="table-container">
      <table class="user-table">
        <thead>
          <tr>
            <th> Employee ID </th>
            <th> Email </th> 
            <th> First Name </th>
            <th> Middle Name </th>
            <th> Last Name </th>
            <th> Privilege </th>
            <th> Status  </th>
          </tr>
        </thead>
          <tbody>
  
          </tbody>
      </table>
    </div>
  </div>
  <div class="right-user">
    <div id="filter-box">
      <div id="head-filter">
        FILTERS
      </div>

      <div id="body-filter">
        <label><input type="checkbox" name="privilege" id="faculty"> Faculty</label>
        <label><input type="checkbox" name="privilege" id="admin"> Admin</label>
        <label><input type="checkbox" name="privilege" id="superadmin"> SuperAdmin</label>
      </div>
        
      <button id="apply-filter"> Reset Filters </button>

    </div>
  </div>
</main>

<script src="../script/user-table.js" defer></script>