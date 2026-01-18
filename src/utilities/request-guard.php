<?php

# Guards web requests

if ($_SERVER["REQUEST_METHOD"] != "POST") {
  $dir = __DIR__;
  while (basename($dir) != "IT-Asset-Management-System") {
    $dir = dirname($dir);
    if ($dir === dirname($dir)) {
      throw new Exception("Outside the system!");
    }
  }
  echo "You should not be here.";
  // header("Location: ../../index.php");
  // exit;
}
