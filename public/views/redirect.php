<?php
session_start();

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../src/handlers/login-validation.php';

$client = new Google\Client;

$client->setClientId("220342807876-1pfho30cmrv6msmj091015q6dptf9b2j.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost:3000/public/views/redirect.php");

if (!isset($_GET['code'])) {
  header("Location: login.php?error=login_failed");
  exit('Login failed');
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($token['access_token']);

$oauth = new Google\Service\Oauth2($client);

$userinfo = $oauth->userinfo->get();

$email = $userinfo->email;

if (substr($email, -10) !== "@up.edu.ph") {
    exit("Only UP Mail accounts are allowed.");
}

if (!validateLogIn(email: $email)) {
  exit("Account not registered. Contact the admin.");
}

header("Location: dashboard.php");