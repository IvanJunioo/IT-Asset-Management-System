<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/asset.css">
<body>
  <?php include '../partials/header.php'?>

  <main class="assign-asset-form">
    <form action = '../handlers/add-assignment-form.php' method="post">
      <label for="selected-assets"> Selected Assets: </label>
      <p id="asset-list"></p>
      
      <label for="selected-user"> Selected User: </label>
      <p id="chosen-user" name = 'user'></p>

      <label for="assign-date"> Assign Date: </label>
      <input type="date" id = "adate" name="assign-date" placeholder="Enter Assign Date" required>

			<label for="remarks"> Remarks: </label>
      <textarea id = "remarks" name="remarks" placeholder="Enter Remarks" rows = "4" cols = "25"> </textarea>

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