<form id="asset-form">
  <label class="input-label"> 
    Procurement Number: 
    <input 
      type="text" 
      id="prnum" 
      name="procurement-num" 
      placeholder="Enter Procurement Number" 
      maxlength="12" 
      minlength="12" 
      size="12" 
      required
    >
  </label>

  <table id="unique-asset-attr">
    <thead>
      <tr>
        <th>Property Number</th>
        <th>Serial Number</th>
        <th>Image URL</th>
      </tr>
    </thead>
    <tbody>
      <tr id="input-row">
        <td>
          <input 
            type="text" 
            class="pnum" 
            name="property-num" 
            placeholder="Enter Property Number" 
            maxlength="12" 
            minlength="12" 
            required
          >
        </td>
        <td>
          <input 
            type="text" 
            class="snum" 
            name="serial-num" 
            placeholder="Enter Serial Number" 
            maxlength="12" 
            minlength="12" 
          > 
        </td>
        <td>
          <input 
            type="url" 
            class="img_url" 
            name="img-url" 
            placeholder="https://example.com" 
            required
          >
        </td>
      </tr>
    </tbody>
  </table>

  <label class="input-label"> 
    Purchase Date: 
    <input 
      type="date" 
      id="pdate" 
      name="purchase-date"
      value="<?= date('Y-m-d') ?>"
      max="<?= date('Y-m-d') ?>"
      required
    >
  </label>

  <label class="input-label"> 
    Price (₱): 
    <input 
      type="number" 
      id="price" 
      name="price" 
      placeholder="Enter Price" 
      min="0" 
      max="1000000000" 
      maxlength="15" 
      size="15" 
      step=".01" 
      required
    >
  </label>

  <label class="input-label"> 
    Specifications: 
    <textarea 
      id="specs" 
      name="specs" 
      placeholder="Enter Specifications"  
      rows="4" 
      cols="25"
      required
    ></textarea>
  </label>

  <label class="input-label"> 
    Short Description: 
    <textarea 
      id="desc" 
      name="short-desc" 
      placeholder="Enter Short Description" 
      rows="4" 
      cols="25"
    ></textarea>
  </label>

  <label class="input-label"> 
    Remarks: 
    <textarea 
      id="remarks" 
      name="remarks" 
      placeholder="Enter Remarks" 
      rows="4" 
      cols="25"
    ></textarea>
  </label>

  <div id = "status-group">
    <label class="input-label"> 
      Status: 
      <label>
        <input 
          type="radio" 
          id="unused" 
          name="asset-status" 
          value="Unassigned" 
          required 
        > 
        <span class="badge unassigned">Unassigned</span>
      </label>
      <label id = 'tocondemn'>
        <input 
          type="radio" 
          id="inrepair" 
          name="asset-status" 
          value="ToCondemn"
        > 
        <span class="badge tocondemn">ToCondemn</span>
      </label>
    </label>
  </div>
  
  <button id="reset-button" type="button">
    Reset Changes
  </button>

  <button id="submit-button" type="submit" name="action" value="submit">
    Submit
  </button>  
</form>
