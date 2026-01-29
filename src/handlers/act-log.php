<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/actlog.php';

header('Content-Type: application/json');

$page = max((int)$_POST['page'], 1) ?? 1;
$limit =  $_POST['limit'] ?? "";
$search =  $_POST['search'] ?? "";

try {
  $repo = new ActLogRepo($pdo);
  
  echo json_encode([
    "logs" => $repo->getLogs(
      search: $search,
      page: $page,
      limit: $limit,
    ),
    "count" => $repo->countLogs(search: $search),
  ]);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;
