<?php

$dbname = "itam"; # Change database here
$dbsource = "mysql:host=localhost;dbname=$dbname"; 
$dbusername = "root";
$dbpassword = ""; 

$pdo = new PDO($dbsource, $dbusername, $dbpassword);  # PHP Data Object
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
