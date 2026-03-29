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
  <link rel="stylesheet" href="<?= BASE_URL ?>css/forms.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/asset.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main>
    <div class="card">
      <h3>Return Asset(s)</h3>
      <form id="return-asset-form" action = "<?= BASE_URL ?>api/index.php?resource=assignment&action=return" method="post">
        <div class="input-label"> 
          <b>Selected Asset(s):</b>
          <p id="asset-list"></p>
        </div>
        
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
          Datetime:
          <input 
            type="datetime-local" 
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
    </div>
  </main>

  <script>
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    const today = `${now.getFullYear().toString()}-${(now.getMonth() + 1).toString().padStart(2,'0')}-${now.getDate().toString().padStart(2,'0')}`;
    document.getElementById("adate").value = now.toISOString().slice(0, 16);
    document.getElementById('adate').setAttribute('max', today);
  </script>
  <script src="<?= BASE_URL ?>script/edit-assignment.js" type="module" defer> </script>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>