<?php $REQUIRED_ROLES = ["Admin", "SuperAdmin"];?>

<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <?php include __DIR__ . '/../partials/asset-styles.php'?>
<body>
  <?php include __DIR__ . '/../partials/header.php'?>
  
  <main class="asset-page">
    <?php include __DIR__ . '/asset-page.php'?>
  </main>
  
  <?php include __DIR__ . '/../partials/footer.php'?>
  
  <script src="<?= BASE_URL ?>public/script/asset-table-manager.js" type="module" defer></script>
</body>
</html>