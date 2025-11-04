<!DOCTYPE html>
<html lang="en">
    <?php include '../partials/head.php'?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/user.css">
<body>
  <!-- menu -->
  <?php include '../partials/header.php'?>

  <!-- user-page -->
  <main class="assign-user">
        <table class="assign-user-table">
          <thead>
            <tr>
              <th> Employee ID </th>
              <th> Email </th> 
              <th> First Name </th>
              <th> Middle Name </th>  
              <th> Last Name </th>
              <th> Privilege </th>
              <th> Status  </th>
              <th>
              </th>
            </tr>
          </thead>
            <tbody>
    
            </tbody>
        </table>
  </main>

  <script src="../script/assign-user.js" defer></script>

  <?php include '../partials/footer.php'?>
</body>
</html>