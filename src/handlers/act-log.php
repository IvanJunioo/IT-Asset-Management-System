<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/actlog.php';
require_once __DIR__ . '/../repos/user.php';

header('Content-Type: application/json');

$page = max((int)$_POST['page'], 1) ?? 1;
$limit =  $_POST['limit'] ?? "";
$search =  $_POST['search'] ?? "";

try {
  $logRepo = new ActLogRepo($pdo);
  $userRepo = new UserRepo($pdo);
  
  $logs = $logRepo->getLogs(
    search: $search,
    page: $page,
    limit: $limit,
  );

  $payload = [];
  foreach ($logs as $log) {
    $pl = [...$log];
    $metadata = json_decode($log["Metadata"], true);
    
    switch ($metadata["object"]) {
      case "asset":
        break;
      case "user":
        $user = $userRepo->identify($metadata["empID"]);
        $pl["objName"] = $user->name->FLast();
        break;
    }
    
    $payload[] = $pl;
  }

  echo json_encode([
    "logs" => $payload,
    "count" => $logRepo->countLogs(search: $search),
  ]);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;
