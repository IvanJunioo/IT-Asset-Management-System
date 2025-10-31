<?php

$dbname = "test"; # Change database here
$dbsource = "mysql:host=localhost;dbname=$dbname"; 
$dbusername = "root";
$dbpassword = ""; 

try {
  $pdo = new PDO($dbsource, $dbusername, $dbpassword);  # PHP Data Object
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}