<?php
require_once __DIR__ . '/../vendor/autoload.php';

$protocol = (
  !empty($_SERVER['HTTPS']) 
  && $_SERVER['HTTPS'] !== 'off'
  || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://"
;

$host = $_SERVER['HTTP_HOST'];

$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$projectRoot = preg_replace('#/(src|public).*#', '/', $scriptDir);

define('BASE_URL', $protocol . $host . $projectRoot . 'public/'); // "/public/"

// Database
$dbname = "itam"; # Change database here
// $dbsource = "mysql:host=db;dbname=$dbname;charset=utf8mb4"; 
// $dbusername = "user";      // match docker-compose.yml
// $dbpassword = "userpassword";
$dbsource = "mysql:host=localhost;dbname=$dbname;charset=utf8mb4"; 
$dbusername = "root";      // match docker-compose.yml
$dbpassword = "";


$pdo = new PDO($dbsource, $dbusername, $dbpassword);  # PHP Data Object
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Google Client API
$client = new Google\Client;
$client->setClientId("220342807876-1pfho30cmrv6msmj091015q6dptf9b2j.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost:3000/public/api/index.php?resource=logs&action=login");

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();
