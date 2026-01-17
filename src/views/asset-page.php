<div class="left-asset">
  <div id="search-box">
    <input type="text" id="search-input" placeholder="Search asset">
    <button id="search-button"> Search </button>
  </div>
  
  <div class="table-container">
    <table class="asset-table">
      <thead>
        <tr>
          <th> Property Number </th>
          <th> Procurement Number </th>
          <th> Purchase Date </th>
          <th> Detailed Specification </th>
          <th> Price </th>
          <th> Status  </th>
          <th> Assigned to </th>
        </tr>
      </thead>
      <tbody></tbody>
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
        <input type="checkbox" name="status" value="Available"> 
        Available
      </label>
      <label>
        <input type="checkbox" name="status" value="Assigned"> 
        Assigned
      </label>
      <label>
        <input type="checkbox" name="status" value="Condemned"> 
        Condemned
      </label>
      <label>
        <input type="checkbox" name="status" value="InRepair">  
        To Repair
      </label>
    </div>
      
    <button id="apply-filter"> Reset Filters </button>

  </div>
  <button id="export"> Export assets </button>
</div>

<script src="../script/asset-table.js" defer></script>
