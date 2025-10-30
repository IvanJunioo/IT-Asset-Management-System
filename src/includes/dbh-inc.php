<?php

$dbsource = "mysql:host=localhost;dbname=test";
$dbusername = "root";
$dbpassword = " "; 

try {
  $pdo = new PDO($dbsource, $dbusername, $dbpassword);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}