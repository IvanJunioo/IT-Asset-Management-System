<?php $REQUIRED_ROLES = ["SuperAdmin"];?>

<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <?php include '../partials/user-styles.php'?>
<body>
  <?php include '../partials/header.php'?>

  <main class="user-page">
    <?php include '../views/user-page.php'?>
  </main>
  
  <?php include '../partials/footer.php'?>
  
  <script src="../script/user-table-manager.js" type="module" defer></script>
</body>
</html>