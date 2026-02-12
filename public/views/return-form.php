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
  <link rel="stylesheet" href="/../../public/css/forms.css">
  <link rel="stylesheet" href="/../../public/css/asset.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main class="assign-asset-form">
    <form action = "src/handlers/return-asset.php" method="post">
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

  <script>
    const date = new Date();
    const today = `${date.getFullYear().toString()}-${(date.getMonth() + 1).toString().padStart(2,'0')}-${date.getDate().toString().padStart(2,'0')}`;
    document.getElementById('adate').setAttribute('max', today);
  </script>
  <script src="/../../public/script/edit-assignment.js" type="module" defer> </script>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>