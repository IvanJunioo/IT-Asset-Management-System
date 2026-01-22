<?php $REQUIRED_ROLES = ["Admin", "SuperAdmin"];?>

<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/asset.css">
<body>
  <?php include '../partials/header.php'?>
    
  <main class="asset-form">
    <?php include '../views/asset-form.php'?>
  </main>
  
  <?php include '../partials/footer.php'?>
  
  <script src="../script/edit-asset.js" type="module" defer></script>
</body>
</html>