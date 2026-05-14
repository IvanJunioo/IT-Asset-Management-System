<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <?php include __DIR__ . '/../partials/component-styles.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/pages/asset-manager.css">
  <style>
    main {
      display: flex;
      justify-content: center;
    }
  </style>
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main>
    <div class="card">
      <h3>Assign Asset(s)</h3>
      <form id="assign-asset-form" method="post">
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
            value="<?= date('Y-m-d\TH:i') ?>"
            max="<?= date('Y-m-d\TH:i') ?>"
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

  <script src="<?= BASE_URL ?>script/add-assignment.js" type="module" defer> </script>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>