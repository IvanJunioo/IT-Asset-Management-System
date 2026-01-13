<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/act-log.css">
<body>
  <?php include '../partials/header.php'?>
  
  <main class="activity-log">
    <table class="activity-log-table">
      <thead>
        <tr>
          <th> Timestamp </th>
          <th> Employee ID </th>
          <th> Description </th>
        </tr>
      </thead>  
      <tbody></tbody>
    </table>
  </main>

  <?php include '../partials/footer.php'?>

  <script src="../script/act-log.js" defer></script>
</body>
</html>