<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/asset.css">
<body>
  <?php include '../partials/header.php'?>

  <main class="assign-asset-form">
    <form action = '../../src/handlers/add-assignment-form.php' method="post">
      <label class="input-label"> 
        Selected Assets:
        <p id="asset-list"></p>
      </label>
      
      <label class="input-label"> 
        Selected User: 
        <p id="chosen-user" name = 'user'></p>
      </label>

      <label class="input-label"> 
        Assign Date: 
        <input 
          type="date" 
          id="adate" 
          name="assign-date" 
          placeholder="Enter Assign Date" 
          required
        >
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

      <button id="reset-button" type="reset">
        Reset
      </button>

      <button id="submit-button" type="submit" name="action" value="submit">
        Submit
      </button>  
    </form>
  </main>

  <script src="../script/edit-assignment.js" defer> </script>

  <?php include '../partials/footer.php'?>
</body>
</html>