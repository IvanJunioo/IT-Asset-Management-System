<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/forms.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/asset.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main>
    <div class="card">
      <h3>Assign Asset(s)</h3>
      <form id="assign-asset-form" action = "<?= BASE_URL ?>api/index.php?resource=assignment&action=assign" method="post">
        <div class="input-label"> 
          <b>Selected Asset(s):</b>
          <p id="asset-list"></p>
        </div>
        
        <div class="input-label"> 
          <b>Selected User:</b>
          <p id="chosen-user" name = 'user'></p>
        </div>
  
        <label class="input-label"> 
          Datetime: 
          <input 
            type="datetime-local" 
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
  
        <button id="submit-button" type="submit" value="submit">
          Submit
        </button>  
      </form>
    </div>
  </main>

  <script>
    const date = new Date();
    const today = `${date.getFullYear().toString()}-${(date.getMonth() + 1).toString().padStart(2,'0')}-${date.getDate().toString().padStart(2,'0')}`;
    document.getElementById('adate').setAttribute('max', today);
  </script>
  <script src="<?= BASE_URL ?>script/add-assignment.js" type="module" defer> </script>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>