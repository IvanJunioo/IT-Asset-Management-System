<div class="left-asset">
  <div id="search-box">
    <input type="text" id="search-input" placeholder="Search asset">
    <span class="material-icons search-icon">search</span>
  </div>

  <div class="table-container">
    <table class="asset-table">
      <thead>
        <tr>
          <th id="pnum"><span>Procurement Number</span> </th>
          <th id="prnum"><span>Property Number</span> </th>
          <th id="pdate"><span>Purchase Date</span> </th>
          <th id="specs"><span>Detailed Specification</span> </th>
          <th id="price"><span>Price (₱)</span> </th>
          <th id="stat"><span>Status </span> </th>
          <th id="assto"><span>Assigned to</span> </th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<div class="right-asset">
  <div class="filter-box" id="filter-box-reg">
    <div class="head-filter">
      FILTERS
    </div>

    <div class="body-filter">
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

      <div class="date-filter">
        <div class="date-grp">
          <span>Start Date</span>
          <input type="date" id="date-from" placeholder="From">
        </div>  
        <div class="date-grp">
          <span>End Date</span>
          <input type="date" id="date-to" placeholder="To">
        </div>
      </div>
    </div>
      
    <button class="reset-filter"> 
      <span class="material-icons">refresh</span>
      Reset Filters 
    </button>

  </div>
  <button id="export" class="generate"> 
    <span class="material-icons">ios_share</span>    
    Export assets 
  </button>
</div>
<script>
  const date = new Date();
  const today = `${date.getFullYear().toString()}-${(date.getMonth() + 1).toString().padStart(2,'0')}-${date.getDate().toString().padStart(2,'0')}`;
  document.getElementById('date-from').setAttribute('max', today);
  document.getElementById('date-to').setAttribute('max', today);
</script>

<script src="<?= BASE_URL ?>script/asset-table.js" type="module" defer></script>
