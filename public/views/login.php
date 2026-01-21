<?php
require __DIR__ . "/../../vendor/autoload.php";

$client = new Google\Client;

$client->setClientId("220342807876-1pfho30cmrv6msmj091015q6dptf9b2j.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost:3000/src/handlers/redirect.php");

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();
?>  

<!DOCTYPE html>
<html lang="en">
<?php include '../partials/head.php' ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/login.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/header.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/navigation.css">
<body>  
    <!-- header -->
    <?php include '../partials/header-login.php' ?>

    <!-- main -->
    <main class="login-page">
        <div class="top-login">
            <a href="https://dcs.upd.edu.ph/" target="_blank">
                <img id="dcs-login" src="https://cms.dcs.upd.edu.ph/assets/9a8e9dd2-3851-4c88-9687-c4aca3aceea5?fit=cover&width=90&height=90">
            </a>
            <div id="system-name">
                DCS IT Assets Management System
            </div>
            <p class="system-caption">
                IT Assets Inventory and Management System for UP Diliman Department of Computer Science.
            </p>
        </div>

        <div class="bottom-login">
            <h2 class="sign-in">Log in Via UP Mail</h2>
            <a href="<?=  $url ?>" id="login-upmail">
                <img src="https://www.google.com/favicon.ico" id="google-icon" width="32" height="32">
                    Login Here
            </a>
        </div>
    </main>

    <?php include '../partials/footer.php'?>
</body>
</html>