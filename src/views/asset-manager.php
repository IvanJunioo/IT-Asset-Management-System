<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <?php include __DIR__ . '/../partials/component-styles.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/pages/asset-manager.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>
  
  <main class="asset-page">
    <?php include __DIR__ . '/asset-page.php'?>
  </main>
  
  <?php include __DIR__ . '/../partials/footer.php'?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script> <!-- PapaParse for CSV parsing -->
  <script src="<?= BASE_URL ?>script/asset-table-manager.js" type="module" defer></script>
</body>
</html>