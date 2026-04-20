<?php
require_once __DIR__ . '/../../src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

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
  echo json_encode($router->handle(
    $res, 
    $action, 
    params: $_GET,
    input: json_decode(file_get_contents("php://input"), true) ?? $_POST,
  ));

  if ($res === APIResource::Log && $action === APIAction::Login) {
    header("Location: " . BASE_URL . "index.php?page=dashboard");
  }
  if ($res === APIResource::Log && $action === APIAction::Logout) {
    header("Location: " . BASE_URL . "index.php?page=login");
  }

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => $e->getMessage()]);
}

exit;