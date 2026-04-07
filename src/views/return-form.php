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
    const today = now.getFullYear() + '-' +
    (now.getMonth() + 1 < 10 ? '0' : '') + (now.getMonth() + 1) + '-' +
    (now.getDate() < 10 ? '0' : '') + now.getDate() + 'T' +
    (now.getHours() < 10 ? '0' : '') + now.getHours() + ':' +
    (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
    document.getElementById('adate').value = today;
    document.getElementById('adate').setAttribute('max', today);
  </script>
  <script src="<?= BASE_URL ?>script/edit-assignment.js" type="module" defer> </script>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>