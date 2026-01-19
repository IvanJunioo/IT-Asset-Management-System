<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/act-log.css">
<body>
  <?php include '../partials/header.php'?>
  
  <main class="activity-log">
    <?php include '../views/act-log.php'?>
  </main>

  <?php include '../partials/footer.php'?>

  <script>
    document.getElementById("actlog-table").class = "activity-log-table";
  </script>
</body>
</html>