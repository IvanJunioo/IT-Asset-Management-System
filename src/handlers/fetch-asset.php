<?php

require_once '../includes/request-guard.php';
require_once '../includes/dbh-inc.php';
require_once '../model/database.php';

header('Content-Type: application/json');

$search =  $_POST['search'] ?? "";

try{
  $db = new Database($pdo);
  $assets = array_values(array_map("unserialize", array_unique(array_map("serialize",
    $db->searchAsset(propNum: $search)
  ))));

  echo json_encode($assets);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;