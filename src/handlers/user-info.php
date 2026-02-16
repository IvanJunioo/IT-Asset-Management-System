<?php
require_once '../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../model/user.php';
require_once __DIR__ . '/../repos/user.php';

header('Content-Type: application/json');

try {  
  session_start();
  
  echo json_encode([
    "empID" => $_SESSION["user_id"],
    "fname" => $_SESSION["user_fname"],
    "lname" => $_SESSION["user_lname"],
    "privilege" => $_SESSION["privilege"],
  ]);
} catch (Exception $e) {
  echo json_encode(["error"=> $e->getMessage()]);
}

exit;