<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <?php include __DIR__ . '/../partials/component-styles.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/pages/user-manager.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main class="user-page">
    <?php include __DIR__ . '/user-page.php'?>
  </main>
  
  <?php include __DIR__ . '/../partials/footer.php'?>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script> <!-- PapaParse for CSV parsing -->
  <script src="<?= BASE_URL ?>script/user-table-manager.js" type="module" defer></script>
</body>
</html>