<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../model/database.php';

header('Content-Type: application/json');

try {
  $db = new Database($pdo);
  
  echo json_encode($db->getLogs());
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;
