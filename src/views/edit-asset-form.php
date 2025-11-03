<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/asset.css">
  <body>
    <?php include '../partials/header.php'?>
    
  <main class="add-asset-form">
    <form action = '../handlers/edit-asset-form.php' method="post">
      <label for="property-num"> Property Number: </label>
      <input type="text" id ="pnum" name="property-num" placeholder="Enter Property Number" maxlength="12" minlength="12" size = "12" required>

      <label for="procurement-num"> Procurement Number: </label>
      <input type="text" id = "prnum" name="procurement-num" placeholder="Enter Procurement Number" maxlength="12" minlength="12" size = "12" required>

      <label for="serial-num"> Serial Number: </label>
      <input type="text" id = "snum" name="serial-num" placeholder="Enter Serial Number" maxlength="12" minlength="12" size = "12" required> 

      <label for="purchase-date"> Purchase Date: </label>
      <input type="date" id = "pdate" name="purchase-date" placeholder="Enter Purchase Date" required>

      <label for="price"> Price: </label>
      <input type="number" id = "price" name="price" placeholder="Enter Price" min = "0" maxlength = "15" size = "15" step = ".01" required>

      <label for="specs"> Specifications: </label>
      <textarea id = "specs" name="specs" placeholder="Enter Specifications"  rows = "4" cols = "25"required> </textarea>

      <label for="short-desc"> Short Description: </label>
      <textarea id ="desc" name="short-desc" placeholder="Enter Short Description" rows = "4" cols = "25"> </textarea>

      <label for="remarks"> Remarks: </label>
      <textarea id = "remarks" name="remarks" placeholder="Enter Remarks" rows = "4" cols = "25"> </textarea>

      <label for="img-url"> Img URL: </label>
      <input type="url" id = "img_url" name="img-url" placeholder="Enter Img URL" required>

      <label for="status"> Status: </label>
        <label>
          <input type="radio" id = "unused" name="asset-status" value = "Unused" required checked> Unused
        </label>
        <label>
          <input type="radio" id = "used" name="asset-status" value = "Used"> Used
        </label>
        <label>
          <input type="radio" id = "inrepair" name="asset-status" value = "InRepair"> In Repair 
        </label>
        <label>
          <input type="radio" id = "broken" name="asset-status" value = "Broken"> Broken
        </label>
      </label>
      
      <button id="reset-button" type="reset">
        Reset
      </button>

      <button id="submit-button" type="submit" name="action" value="submit">
        Submit
      </button>  
    </form>
    <script src="../script/edit-asset.js"></script>

  </main>
  <?php include '../partials/footer.php'?>
</body>
</html>