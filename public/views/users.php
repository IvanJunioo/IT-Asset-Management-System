<?php $REQUIRED_ROLES = ["Admin"];?>

<!DOCTYPE html>
<html lang="en">
    <?php include '../partials/head.php'?>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/user.css">
<body>
  <?php include '../partials/header.php'?>
  
  <main class="user-page">
    <?php include '../views/user-page.php'?>
  </main>

  <?php include '../partials/footer.php'?>
</body>
</html>