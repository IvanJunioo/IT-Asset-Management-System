<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/handlers/syslog.php';
require_once __DIR__ . '/../../src/handlers/asset.php';
require_once __DIR__ . '/../../src/handlers/assignment.php';
require_once __DIR__ . '/../../src/handlers/user.php';
require_once __DIR__ . '/../../src/handlers/export.php';
require_once __DIR__ . '/../../src/repos/actlog.php';
require_once __DIR__ . '/../../src/repos/asset.php';
require_once __DIR__ . '/../../src/repos/assignment.php';
require_once __DIR__ . '/../../src/repos/user.php';


header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents("php://input"), true) ?? $_POST;

switch ($_GET["resource"]) {
  case 'users':
    $handler = new UserHandler(
      userRepo: new UserRepo($pdo),
      assignRepo: new AssignmentRepo($pdo),
    );

    switch ($_GET["action"]) {
      case "fetch":
        echo json_encode($handler->getUser((int)$_GET["search"]));
        exit;

      case "stats":
        echo json_encode($handler->getRepoStats());
        exit;

      case "session":
        echo json_encode($handler->getSessionUser());
        exit;
      
      case "search":
        echo json_encode($handler->searchUsers(
          search: $_GET["search"] ?? "",
          status: $_GET["status"] ?? "",
          privilege: $_GET["priv"] ?? "",
        ));
        exit;

      case "add":
        $handler->addUser(new User(
          name: new Fullname(
            first: $input['first-name'],
            last: $input['last-name'],
          ),
          email: $input['email'],
          privilege: UserPrivilege::from($input['privilege']),
          isActive: $input["active-status"] === "Active",
        ));
        header('Location: ../../public/views/user-manager.php');
        exit;
      
      case "edit":
        $handler->editUser(new User(
          empID: $input['employee-id'],
          name: new Fullname(
            first: $input['first-name'],
            last: $input['last-name'],
          ),
          email: $input['email'],
          privilege: UserPrivilege::from($input['privilege']),
          isActive: $input['active-status'] === 'Active',
        ));
        header('Location: ../../public/views/user-manager.php');
        exit;

      case "activate":
        $handler->changeStatus(
          empID: $input["empID"] ?? "", 
          isActive: true, 
        );
        exit;

      case "deactivate":
        $handler->changeStatus(
          empID: $input["empID"] ?? "", 
          isActive: false, 
        );
        exit;
      
      default:
        http_response_code(404);
        echo json_encode(["error" => "Handler action unknown"]);
    }
    exit;
  
  case 'assets':
    $handler = new AssetHandler(
      assetRepo: new AssetRepo($pdo),
      assignRepo: new AssignmentRepo($pdo),
    );
    
    switch ($_GET["action"]) {
      case "fetch":
        echo json_encode($handler->getAsset($_GET["search"]));
        exit;
      
      case "stats":
        echo json_encode($handler->getRepoStats());
        exit;

      case "search":
        echo json_encode($handler->searchAssets(
          search: $_GET["search"] ?? "",
          status: $_GET["status"] ?? "",
        ));
        exit;
      
      case "add":
        $handler->addAsset(array_map(
          fn($propNum, $serialNum, $url) => new Asset(
            propNum:      $propNum ?? "",
            procNum:      $input['procurement-num'] ?? "",
            serialNum:    $serialNum ?? "",
            purchaseDate: $input['purchase-date'] ?? "",
            specs:        $input['specs'] ?? "",
            description:  $input['short-desc'] ?? "",
            url:          $url ?? "",
            remarks:      $input['remarks'] ?? "",
            price:        $input['price'] ?? "",
            status:       AssetStatus::from($input['asset-status'] ?? ""),
          ),
          $input["property-num"],
          $input["serial-num"],
          $input["img-url"],
        ));
        header('Location: ../../public/views/asset-manager.php'); 
        exit;
      
      case "edit":
        $handler->editAsset(new Asset(
          propNum:      $input["property-num"] ?? "",
          procNum:      $input["procurement-num"] ?? "",
          serialNum:    $input["serial-num"] ?? "",
          purchaseDate: $input["purchase-date"] ?? "",
          specs:        $input["specs"] ?? "",
          description:  $input["short-desc"] ?? "",
          url:          $input["img-url"] ?? "",
          remarks:      $input["remarks"] ?? "",
          price:        $input["price"] ?? "",
          status:       AssetStatus::from($input["asset-status"] ?? ""),
        ));
        
        header('Location: ../../public/views/asset-manager.php');
        exit;
      
      case "condemn":
        $handler->changeStatus($input["search"], AssetStatus::Condemned);
        exit;
      
      default:
        http_response_code(404);
        echo json_encode(["error" => "Handler action unknown"]);
    }
    exit;
  
  case "assignment":
    $handler = new AssignmentHandler(new AssignmentManager(
      assetRepo: new AssetRepo($pdo),
      assignRepo: new AssignmentRepo($pdo),
      userRepo: new UserRepo($pdo),
    ));

    switch ($_GET["action"]) {
      case "assign":
        $handler->assign(
          propNums:   $input["assets"],
          date:       new DateTimeImmutable($input['assign-date']),
          assigneeID: $input["user"],
          remarks:    $input["remarks"],
        );
        header('Location: ../../public/views/asset-manager.php');
        exit;      
      
      case "return":
        $handler->return(
          propNums: $input['assets'],
          date:     new DateTimeImmutable($input['return-date']),
          remarks:  $input['remarks'],
        );
        header('Location: ../../public/views/asset-manager.php');
        exit;

      default:
        http_response_code(404);
        echo json_encode(["error" => "Handler action unknown"]);
    }
    exit;

  case "logs":
    $handler = new LogHandler(
      logRepo: new ActLogRepo($pdo),
      userRepo: new UserRepo($pdo),
    );
    
    switch ($_GET["action"]) {
      case "search":
        echo json_encode($handler->getLogs(
          search: $_GET["search"] ?? "",
          page: (int)($_GET["page"] ?? 1),
          limit: (int)($_GET["limit"] ?? 20),
        ));
        exit;
      
      case "logurl":
        echo json_encode($handler->getLoginUrl());
        exit;
      
      case "login":
        if (!isset($_GET['code'])) {
          header("Location: ../../public/views/login.php?error=login_failed");
          exit('Login failed');
        }

        $handler->login($_GET["code"]);
        exit;

      case "logout":
        $handler->logout();
        header("Location: ../../public/views/login.php");
        exit;
      
      default:
        http_response_code(404);
        echo json_encode(["error" => "Handler action unknown"]);
    }
    exit;

  case "export":
    $handler = new ExportHandler($pdo);

    switch ($_GET["action"] ?? "") {
      case "status":
      $handler->exportAssetsByStatus($_GET["status"] ?? null);
      exit;

      case "user-assets":
        $handler->exportUserAssignedAssets($_GET["user"] ?? null);
        exit;

      case "faculty-assets":
        $handler->exportFacultyAssignedAssets($_GET["users"] ?? null);
        exit;

      default:
        http_response_code(404);
        echo json_encode(["error" => "Handler action unknown"]);
    }

  default:
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
}
