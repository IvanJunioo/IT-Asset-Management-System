<?php

require_once '../includes/request-guard.php';
require_once '../includes/dbh-inc.php';
require_once '../model/database.php';

$search = isset($_POST['search'])? $_POST['search'] : "";

$db = new Database($pdo);
$users = array_values(array_map("unserialize", array_unique(array_map("serialize", array_merge(
  $db->searchUser(empID: $search),
  $db->searchUser(fullname: new Fullname(first: $search)),
  $db->searchUser(fullname: new Fullname(middle: $search)),
  $db->searchUser(fullname: new Fullname(last: $search)),
  $db->searchUser(email: $search),
)))));

echo json_encode($users);

exit;