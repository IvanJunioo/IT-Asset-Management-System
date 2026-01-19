<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/actlog.php';

header('Content-Type: application/json');

try {
  $repo = new ActLogRepo($pdo);
  
  echo json_encode($repo->getLogs());
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;
