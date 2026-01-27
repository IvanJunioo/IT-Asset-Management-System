<?php
// require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../model/user.php';
require_once '../repos/user.php';

header('Content-Type: application/json');

session_start();

if (!isset($_GET['code'])) {
  header("Location: ../../public/views/login.php?error=login_failed");
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

$repo = new UserRepo($pdo);
$users = $repo->search(new UserSearchCriteria(email: $email));

if (count($users) == 0) {
  throw new Exception("User email $email not found in database!");
}

$user = $users[0];

$_SESSION['user_id'] = $user->empID;
$_SESSION['user_fname'] = $user->name->first;
$_SESSION['user_mname'] = $user->name->middle;
$_SESSION['user_lname'] = $user->name->last;
$_SESSION['privilege'] = $user->getPrivilege()->value;
$_SESSION['logged_in'] = true;

header("Location: ../../public/views/dashboard.php");

exit;
