<!DOCTYPE html>
<html lang="en">
  <?php require_once __DIR__ . '/../manager/logger.php'; ?>
  <?php include __DIR__ . '/../partials/head.php';?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/error.css">
  <body>

  

  <?php 
    $errorCode = $_GET['code'] ?? 500;
    $errorMessage = $_GET['message'] ?? "Unknown Error";
    $errorDescription = $_GET['description'] ?? "Something went wrong.";

    $isLoggedIn = isset($_SESSION['user_id']);
    $serverError = $errorCode == 500;

    
    if ($isLoggedIn && !$serverError) {
      $redirectURL = $_SERVER['HTTP_REFERER'] ??  BASE_URL . "index.php?page=dashboard";
      $buttonText = "Go Back";
    } 
    else {
      $redirectURL = BASE_URL . "index.php?page=login";
      $buttonText = "Go to Login";
    }
  ?>

  <?php if ($isLoggedIn && !$serverError): ?>
    <?php
      require_once __DIR__ . "/../utilities/auth-guard.php"; 
      include __DIR__ . '/../partials/header.php'
      ?>
  <?php else : ?>
    <?php include __DIR__ . '/../partials/header-login.php'?>
  <?php endif ?>
    
  <main class="error-content">
     <section class="error-container">
      <h1 class="error-code">
        <?= htmlspecialchars($errorCode) ?>
      </h1>

      <h2 class="error-message">
        <?= htmlspecialchars($errorMessage) ?>
      </h2>

      <p class="error-description">
        <?= htmlspecialchars($errorDescription) ?>
      </p>
      <a href="<?= htmlspecialchars($redirectURL) ?>" class="error-button">
      <?= htmlspecialchars($buttonText) ?>
    </a>
    </section>
    
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>