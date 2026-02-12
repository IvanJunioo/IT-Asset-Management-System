<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/asset.php';

header('Content-Type: application/json');

$search =  $_POST['search'] ?? "";

try{
  $repo = new AssetRepo($pdo);
  
  $assets = $repo->search(new AssetSearchCriteria(propNum: $search));

  echo json_encode($assets);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;