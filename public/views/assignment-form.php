<?php
  $REQUIRED_ROLES = ["Admin", "SuperAdmin"];
  if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
  }
  require_once '../../src/utilities/auth-guard.php';
  require_once '../../src/utilities/role-guard.php';

  requireRole(allowedRoles: $REQUIRED_ROLES ?? []);
?>

<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <link rel="stylesheet" href="public/css/forms.css">
  <link rel="stylesheet" href="public/css/asset.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main class="assign-asset-form">
    <form action = "src/handlers/add-assignment-form.php" method="post">
      <label class="input-label"> 
        Selected Asset(s):
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

  <script src="public/script/add-assignment.js" type="module" defer> </script>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>