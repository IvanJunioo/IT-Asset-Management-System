<?php

if ($_SERVER["REQUEST_METHOD"] != "POST") {
  $dir = __DIR__;
  while (basename($dir) != "IT-Asset-Management-System") {
    $dir = dirname($dir);
    if ($dir === dirname($dir)) {
      throw new Exception("Outside the DBMS!");
    }
  }
  echo "YOU SHALL NOT PASS!";
  // header("Location: ../../index.php");
  // exit;
}
