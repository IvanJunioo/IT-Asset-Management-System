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

enum APIResource: string {
  case User = "users";
  case Asset = "assets";
  case Assignment = "assignment";
  case Log = "logs";
  case Exportable = "export";
}

enum APIAction: string {
  case Fetch = "fetch";
  case Search = "search";
  case Add = "add";
  case Edit = "edit";
  case AssignAsset = "assign";
  case ReturnAsset = "return";
  case CondemnAsset = "condemn";
  case ActivateUser = "activate";
  case DeactivateUser = "deactivate";
  case DBStats = "stats";
  case GetSessionUser = "session";
  case FetchLoginUrl = "logurl";
  case Login = "login";
  case Logout = "logout";
  case ExportByStatus = "status";
  case ExportByUser = "user-assets";
  case ExportByFaculty = "faculty-assets";
  case ExportByFacultyMulti = "faculty-assets-multiple";

  public function isIdempotent(): bool {
    return match ($this) {
      self::Fetch,
      self::Search,
      self::DBStats,
      self::GetSessionUser,
      self::FetchLoginUrl,
      self::Login,
      self::Logout,
      self::ExportByStatus,
      self::ExportByUser,
      self::ExportByFaculty,
      self::ExportByFacultyMulti => true,
      default => false,
    };
  }
}

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

$input = json_decode(file_get_contents("php://input"), true) ?? $_POST;

// Initialize repos and handlers
$assetRepo = new AssetRepo($pdo);
$userRepo = new UserRepo($pdo);
$assignRepo = new AssignmentRepo($pdo);
$logRepo = new ActLogRepo($pdo);
$assetHand = new AssetHandler($assetRepo, $assignRepo);
$userHand = new UserHandler($userRepo, $assignRepo);
$assignHand = new AssignmentHandler(new AssignmentManager($assetRepo, $assignRepo, $userRepo));
$logHand = new LogHandler($logRepo, $userRepo);
$expHand = new ExportHandler($userRepo, $assetRepo, $assignRepo);

match ($res) {
  APIResource::User => match ($action) {
    APIAction::Fetch => (function(UserHandler $handler) {
      echo json_encode($handler->getUser((int)$_GET["search"]));
    })($userHand),

    APIAction::DBStats => (function(UserHandler $handler) {
      echo json_encode($handler->getRepoStats());
    })($userHand),

    APIAction::GetSessionUser => (function(UserHandler $handler) {
      echo json_encode($handler->getSessionUser());
    })($userHand),

    APIAction::Search => (function(UserHandler $handler) {
      echo json_encode($handler->searchUsers(
        search: $_GET["search"] ?? "",
        status: $_GET["status"] ?? "",
        privilege: $_GET["priv"] ?? "",
      ));
    })($userHand),

    APIAction::Add => (function(UserHandler $handler) use ($input) {
      $handler->addUser(new User(
        name: new Fullname(
          first: $input['first-name'],
          last: $input['last-name'],
        ),
        email: $input['email'],
        privilege: UserPrivilege::from($input['privilege']),
        isActive: $input["active-status"] === "Active",
      ));
      header('Location: ' . BASE_URL . 'views/user-manager.php');
    })($userHand),

    APIAction::Edit => (function(UserHandler $handler) use ($input) {
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
      header('Location: ' . BASE_URL . 'views/user-manager.php');
    })($userHand),

    APIAction::ActivateUser => (function(UserHandler $handler) use ($input) {
      $handler->changeStatus(
        empID: $input["empID"] ?? "", 
        isActive: true, 
      );
    })($userHand),

    APIAction::DeactivateUser => (function(UserHandler $handler) use ($input) {
      $handler->changeStatus(
        empID: $input["empID"] ?? "", 
        isActive: false, 
      );
    })($userHand),

    default => throw new Exception("Invalid $res->value action"),
  },
  
  APIResource::Asset => match ($action) {
    APIAction::Fetch => (function(AssetHandler $handler) {
      echo json_encode($handler->getAsset($_GET["search"]));
    })($assetHand),

    APIAction::DBStats => (function(AssetHandler $handler) {
      echo json_encode($handler->getRepoStats());
    })($assetHand),

    APIAction::Search => (function(AssetHandler $handler) {
      echo json_encode($handler->searchAssets(
        search: $_GET["search"] ?? "",
        status: $_GET["status"] ?? "",
        base_date: new DateTimeImmutable($_GET["base_date"]),
        end_date: new DateTimeImmutable($_GET['end_date'])
      ));
    })($assetHand),

    APIAction::Add => (function(AssetHandler $handler) use ($input) {
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
      header('Location: ' . BASE_URL . 'views/asset-manager.php'); 
    })($assetHand),

    APIAction::Edit => (function(AssetHandler $handler) use ($input) {
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
      
      header('Location: ' . BASE_URL . 'views/asset-manager.php');
    })($assetHand),

    APIAction::CondemnAsset => (function(AssetHandler $handler) use ($input) {
      $handler->changeStatus($input["search"], AssetStatus::Condemned);
    })($assetHand),

    default => throw new Exception("Invalid $res->value action"),
  },
    
  APIResource::Assignment => match ($action) {
    APIAction::AssignAsset => (function(AssignmentHandler $handler) use ($input) {
      $handler->assign(
        propNums:   $input["assets"],
        date:       new DateTimeImmutable($input['assign-date']),
        assigneeID: $input["user"],
        remarks:    $input["remarks"],
      );
      header('Location: ' . BASE_URL . 'views/asset-manager.php');
    })($assignHand),
    
    APIAction::ReturnAsset => (function(AssignmentHandler $handler) use ($input) {
      $handler->return(
        propNums: $input['assets'],
        date:     new DateTimeImmutable($input['return-date']),
        remarks:  $input['remarks'],
      );
      header('Location: ' . BASE_URL . 'views/asset-manager.php');
    })($assignHand),

    default => throw new Exception("Invalid $res->value action"),
  },

  APIResource::Log => match ($action) {
    APIAction::Search => (function(LogHandler $handler) {
      echo json_encode($handler->getLogs(
        search: $_GET["search"] ?? "",
        page: (int)($_GET["page"] ?? 1),
        limit: (int)($_GET["limit"] ?? 20),
      ));
    })($logHand),

    APIAction::FetchLoginUrl => (function(LogHandler $handler) {
      echo json_encode($handler->getLoginUrl());
    })($logHand),

    APIAction::Login => (function(LogHandler $handler) {
      if (!isset($_GET['code'])) {
        header("Location: " . BASE_URL . "views/login.php?error=login_failed");
        exit('Login failed');
      }

      $handler->login($_GET["code"]);
      header("Location: " . BASE_URL . "views/dashboard.php");
    })($logHand),

    APIAction::Logout => (function(LogHandler $handler) {
      $handler->logout();
      header("Location: " . BASE_URL . "views/login.php");
    })($logHand),

    default => throw new Exception("Invalid $res->value action"),
  },

  APIResource::Exportable => match ($action) {
    APIAction::ExportByStatus => (function(ExportHandler $handler) {
      $handler->exportAssetsByStatus($_GET["status"] ?? null);
    })($expHand),

    APIAction::ExportByUser => (function(ExportHandler $handler) {
      $handler->exportUserAssignedAssets($_GET["user"] ?? null);
    })($expHand),

    APIAction::ExportByFaculty => (function(ExportHandler $handler) {
      $handler->exportFacultyAssignedAssets($_GET["users"] ?? null);
    })($expHand),

    APIAction::ExportByFacultyMulti => (function(ExportHandler $handler) {
      $handler->exportMultipleFiles($_GET["users"] ?? null);
    })($expHand),

    default => throw new Exception("Invalid $res->value action"),
  },
};

exit;