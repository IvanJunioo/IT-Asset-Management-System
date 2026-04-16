<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/forms.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/asset.css">
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
      <h3>Add New Asset(s)</h3>
      <?php include __DIR__ . '/asset-form.php'?>
    </div>
  </main>
  
  <?php include __DIR__ . '/../partials/footer.php'?>
  
  <script src="<?= BASE_URL ?>script/add-asset.js" type="module" defer></script>
</body>
</html>