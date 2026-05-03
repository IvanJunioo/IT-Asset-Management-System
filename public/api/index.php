<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_once __DIR__ . '/../src/handler/ErrorHandler.php';

header('Content-Type: application/json');

$res = APIResource::tryFrom($_GET["resource"] ?? "");
$action = APIAction::tryFrom($_GET["action"] ?? "");

if (!$res || !$action) {
  http_response_code(405);
  exit(json_encode(["error" => "Invalid resource or action"]));
}

if (!$action->isIdempotent() && $_SERVER['REQUEST_METHOD'] !== "POST") {
  http_response_code(405);
  exit(json_encode(["error" => "Invalid request method"]));
}

try {
  $output = $router->handle(
    $res, 
    $action, 
    params: $_GET,
    input: json_decode(file_get_contents("php://input"), true) ?? $_POST,
  );

  echo json_encode($output);
  exit;
} catch (Throwable $e) {
  ErrorHandler::handle($e);
  // http_response_code(500);
  // echo json_encode(["error" => $e->getMessage()]);
}

exit;