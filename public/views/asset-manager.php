<?php $REQUIRED_ROLES = ["Admin", "SuperAdmin"];?>

<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <?php include '../partials/asset-styles.php'?>
<body>
  <?php include '../partials/header.php'?>
  
  <main class="asset-page">
    <?php include '../views/asset-page.php'?>
  </main>
  
  <?php include '../partials/footer.php'?>
  
  <script src="../script/asset-table-manager.js" type="module" defer></script>
</body>
</html>