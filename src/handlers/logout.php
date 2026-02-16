<?php

declare(strict_types=1);

session_start();

$_SESSION = []; // unset all session variables

if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    "",
    time() - 999999,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
  );
}

session_destroy();
header("Location: ../../public/views/login.php");
exit;