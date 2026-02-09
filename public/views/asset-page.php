<div class="left-asset">
  <div id="search-box">
    <input type="text" id="search-input" placeholder="Search asset">
    <span class="material-icons search-icon">search</span>
  </div>
  
  <div class="table-container">
    <table class="asset-table">
      <thead>
        <tr>
          <th> Property Number </th>
          <th> Procurement Number </th>
          <th> Purchase Date </th>
          <th> Detailed Specification </th>
          <th> Price (₱) </th>
          <th> Status  </th>
          <th> Assigned to </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td> No assets to display. </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="right-asset">
  <div id="filter-box">
    <div id="head-filter">
      FILTERS
    </div>

    <div id="body-filter">
      <label>
        <input type="checkbox" name="status" value="Unassigned"> 
        <span class="badge unassigned">Unassigned</span>
      </label>
      <label>
        <input type="checkbox" name="status" value="Assigned"> 
        <span class="badge assigned">Assigned</span>
      </label>
      <label>
        <input type="checkbox" name="status" value="ToCondemn"> 
        <span class="badge tocondemn">ToCondemn</span>
      </label>
      <label>
        <input type="checkbox" name="status" value="Condemned">  
        <span class="badge condemned">Condemned</span>
      </label>
    </div>
      
    <button id="apply-filter"> Reset Filters </button>

  </div>
    <button id = "export" class="generate"> Export assets </button>
    
</div>
<script src="../script/asset-table.js" type="module" defer></script>
