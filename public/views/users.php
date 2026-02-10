<?php $REQUIRED_ROLES = ["Admin"];?>

<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <?php include __DIR__ . '/../partials/user-styles.php'?>
<body>
  <?php include __DIR__ . '/../partials/header.php'?>
  
  <main class="user-page">
    <?php include __DIR__ . '/user-page.php'?>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>