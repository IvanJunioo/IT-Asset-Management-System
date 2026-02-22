<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/handlers/actlog.php';
require_once __DIR__ . '/../../src/handlers/asset.php';
require_once __DIR__ . '/../../src/handlers/user.php';
require_once __DIR__ . '/../../src/repos/actlog.php';
require_once __DIR__ . '/../../src/repos/asset.php';
require_once __DIR__ . '/../../src/repos/assignment.php';
require_once __DIR__ . '/../../src/repos/user.php';

header('Content-Type: application/json; charset=utf-8');

switch ($_GET["resource"]) {
  case 'users':
    $handler = new UserHandler(
      logRepo: new ActLogRepo($pdo),
      userRepo: new UserRepo($pdo),
    );

    switch ($_GET["action"]) {
      case "search":
        echo json_encode($handler->searchUsers(
          search: $_GET["search"] ?? "",
          status: $_GET["status"] ?? "",
          privilege: $_GET["priv"] ?? "",
        ));
        break;
      default:
        http_response_code(404);
        echo json_encode(["error" => "Handler action unknown"]);
    }

    break;
  case 'assets':
    $handler = new AssetHandler(
      logRepo: new ActLogRepo($pdo),
      assetRepo: new AssetRepo($pdo),
      assignRepo: new AssignmentRepo($pdo),
    );
    
    switch ($_GET["action"]) {
      case "search":
        echo json_encode($handler->searchAssets(
          search: $_GET["search"] ?? "",
          status: $_GET["status"] ?? "",
        ));
        break;
      default:
        http_response_code(404);
        echo json_encode(["error" => "Handler action unknown"]);
    }

    break;
  case "logs":
    $handler = new ActLogHandler(
      logRepo: new ActLogRepo($pdo),
      userRepo: new UserRepo($pdo),
    );
    
    echo json_encode($handler->getLogs(
      search: $_GET["search"] ?? "",
      page: (int)($_GET["page"] ?? 1),
      limit: (int)($_GET["limit"] ?? 20),
    ));
    break;
  default:
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
}
