<?php
require_once __DIR__ . '/../vendor/autoload.php';

$protocol = (
  !empty($_SERVER['HTTPS']) 
  && $_SERVER['HTTPS'] !== 'off'
  || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http"
;

$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);

define('BASE_URL', "$protocol://$host/"); 
$dbname = "itam";
$dbhost = "localhost";
$dbsource = "mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4"; 
$dbusername = "root";
$dbpassword = "";


$pdo = new PDO($dbsource, $dbusername, $dbpassword, [
  PDO::ATTR_PERSISTENT => false,
]);  # PHP Data Object
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Google Client API
$client = new Google\Client;
$client->setClientId("220342807876-1pfho30cmrv6msmj091015q6dptf9b2j.apps.googleusercontent.com");
$client->setClientSecret("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"
);
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
