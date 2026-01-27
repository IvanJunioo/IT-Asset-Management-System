<?php
require_once '..\..\vendor\autoload.php';

$protocol = (
  !empty($_SERVER['HTTPS']) 
  && $_SERVER['HTTPS'] !== 'off'
  || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://"
;

$host = $_SERVER['HTTP_HOST'];

$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$projectRoot = preg_replace('#/(src|public).*#', '/', $scriptDir);

define('BASE_URL', $protocol . $host . $projectRoot);

// Database
$dbname = "itam"; # Change database here
$dbsource = "mysql:host=localhost;dbname=$dbname"; 
$dbusername = "root";
$dbpassword = ""; 

$pdo = new PDO($dbsource, $dbusername, $dbpassword);  # PHP Data Object
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Google Client API
$client = new Google\Client;
$client->setClientId("220342807876-1pfho30cmrv6msmj091015q6dptf9b2j.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost:3000/src/handlers/redirect.php");

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();
