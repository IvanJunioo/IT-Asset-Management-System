<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php';?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/error.css">
<body>
  <?php include __DIR__ . '/../partials/header-login.php'?>

  <?php 
    $errorCode = $_GET['code'] ?? 500;
    $errorMessage = $_GET['message'] ?? "Unknown Error";
    $errorDescription = $_GET['description'] ?? "Something went wrong.";
  
  ?>
    
  <main class="error-content">
     <section class="error-container">
      <h1 class="error-code">
        <?=  $errorCode ?>
      </h1>
      <h2 class="error-message">
        <?=  $errorMessage ?>
      </h2>
      <p class="error-description">
        <?= $errorDescription ?>
      </p>
    </section>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>