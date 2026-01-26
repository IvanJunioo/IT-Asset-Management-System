<?php $REQUIRED_ROLES = ["SuperAdmin"];?>

<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/user.css">
<body>
  <?php include '../partials/header.php'?>

  <main class="user-form">
    <?php include '../views/user-form.php'?>
  </main>

  <?php include '../partials/footer.php'?>
  
  <script src="../script/add-user.js" type="module" defer></script>
</body>
</html>