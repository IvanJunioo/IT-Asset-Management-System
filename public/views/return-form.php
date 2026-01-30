<?php $REQUIRED_ROLES = ["Admin", "SuperAdmin"];?>

<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/forms.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/asset.css">
<body>
  <?php include '../partials/header.php'?>

  <main class="assign-asset-form">
    <form action = '../../src/handlers/return-asset.php' method="post">
      <label class="input-label"> 
        Asset(s):
        <p id="asset-list"></p>
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

      <label class="input-label"> 
        Return Date: 
        <input 
          type="date" 
          id="adate" 
          name="return-date" 
          placeholder="Enter Return Date" 
          required
        >
      </label>

      <button id="reset-button" type="reset">
        Reset
      </button>

      <button id="submit-button" type="submit" name="action" value="submit">
        Submit
      </button>  
    </form>
  </main>

  <script src="../script/edit-assignment.js" type="module" defer> </script>

  <?php include '../partials/footer.php'?>
</body>
</html>