<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/actlog.php';

header('Content-Type: application/json');

$search =  $_POST['search'] ?? "";

try {
  $repo = new ActLogRepo($pdo);
  
  echo json_encode($repo->getLogs(search: $search));
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;
