<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

$protocol = (
  !empty($_SERVER['HTTPS']) 
  && $_SERVER['HTTPS'] !== 'off'
  || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http"
;

$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);

define('BASE_URL', "$protocol://$host/");

// Database Credentials
$dbname = $_ENV["DB_NAME"];
$dbhost = $_ENV["DB_HOST"];
$dbsource = "mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4";
$dbusername = $_ENV["DB_USER"];
$dbpassword = $_ENV["DB_PASS"];
$pdo = new PDO($dbsource, $dbusername, $dbpassword, [
  PDO::ATTR_PERSISTENT => false,
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
]);  # PHP Data Object

// Google Client API
$client = new Google\Client;
$client->setClientId($_ENV["GOOGLE_CLIENT_ID"]);
$client->setClientSecret($_ENV["GOOGLE_CLIENT_SECRET"]);
$client->setRedirectUri(BASE_URL . "api/index.php?resource=logs&action=login");
$client->addScope("email");
$client->addScope("profile");
$url = $client->createAuthUrl();

// Set page access privileges
$pages = [
  "activity-log"    => ["roles" => ["Faculty", "Staff", "Admin", "SuperAdmin"]],
  "add-asset-form"  => ["roles" => ["Admin", "SuperAdmin"]],
  "add-user-form"   => ["roles" => ["SuperAdmin"]],
  "asset-manager"   => ["roles" => ["Admin", "SuperAdmin"]],
  "asset-view"      => ["roles" => ["Faculty", "Staff", "Admin", "SuperAdmin"]],
  "assets"          => ["roles" => ["Faculty", "Staff"]],
  "assign-asset"    => ["roles" => ["Admin", "SuperAdmin"]],
  "assign-user"     => ["roles" => ["Admin", "SuperAdmin"]],
  "assignment-form" => ["roles" => ["Admin", "SuperAdmin"]],
  "dashboard"       => ["roles" => ["Faculty", "Staff", "Admin", "SuperAdmin"]],
  "edit-asset-form" => ["roles" => ["Admin", "SuperAdmin"]],
  "edit-user-form"  => ["roles" => ["Admin", "SuperAdmin"]],
  "login"           => ["roles" => []], // Allows everyone
  "return-form"     => ["roles" => ["Admin", "SuperAdmin"]],
  "user-manager"    => ["roles" => ["SuperAdmin"]],
  "users"           => ["roles" => ["Admin"]],
  "error"      => ["roles" => []],
];

date_default_timezone_set('Asia/Manila'); 
