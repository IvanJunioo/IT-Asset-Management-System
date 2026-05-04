<?php
require_once __DIR__ . '/../../src/bootstrap.php';

header('Content-Type: application/json');

try {
  $res = APIResource::tryFrom($_GET["resource"] ?? "");
  $action = APIAction::tryFrom($_GET["action"] ?? "");
  
  if (!$res || !$action) {
    throw new RuntimeException("Invalid resource or action", 405);
  }
  
  if (!$action->isIdempotent() && $_SERVER['REQUEST_METHOD'] !== "POST") {
    throw new RuntimeException("Invalid request method", 405);
  }
  
  $output = $router->handle(
    $res, 
    $action, 
    params: $_GET,
    input: json_decode(file_get_contents("php://input"), true) ?? $_POST,
  );
  
  echo json_encode($output);
}
catch (Throwable $e) {
  $errHand->handle($e);
}

exit;