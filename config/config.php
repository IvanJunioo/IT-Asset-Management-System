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

define('BASE_URL', $protocol . $host . $projectRoot);

// Database (ngrok public MySQL)
$dbHost = getenv('DB_HOST') ?: '0.tcp.ap.ngrok.io';
$dbPort = getenv('DB_PORT') ?: '19806';
$dbName = getenv('DB_NAME') ?: 'itam';
$dbUser = getenv('DB_USER') ?: 'demo_user';
$dbPass = getenv('DB_PASS') ?: 'password';

$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Google Client API
$client = new Google\Client;
$client->setClientId("220342807876-1pfho30cmrv6msmj091015q6dptf9b2j.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost:8080/src/handlers/redirect.php");

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();
