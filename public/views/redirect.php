<?php
require __DIR__ . "/../../vendor/autoload.php";

$client = new Google\Client;

$client->setClientId("220342807876-1pfho30cmrv6msmj091015q6dptf9b2j.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost:3000/public/views/dashboard.php");

if (!isset($_GET['code'])) {
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

var_dump(
  $userinfo->email,
  $userinfo->familyName,
  $userinfo->givenName,
  $userinfo->name
);

header("Location: views/dashboard.php");
?>
