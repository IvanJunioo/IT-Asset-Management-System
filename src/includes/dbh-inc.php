<?php

$dbsource = "mysql:host=localhost;dbname=test"; # Change database here
$dbusername = "root";
$dbpassword = "password"; 

try {
  $pdo = new PDO($dbsource, $dbusername, $dbpassword);  # PHP Data Object
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}