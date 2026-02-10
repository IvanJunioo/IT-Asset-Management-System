<?php $REQUIRED_ROLES = ["Admin", "SuperAdmin"];?>

<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/forms.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/asset.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>
    
  <main class="asset-form">
    <?php include __DIR__ . '/asset-form.php'?>
  </main>
  
  <?php include __DIR__ . '/../partials/footer.php'?>
  
  <script src="<?= BASE_URL ?>public/script/edit-asset.js" type="module" defer></script>
</body>
</html>