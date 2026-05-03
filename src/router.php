<?php
require_once __DIR__ . '/utilities/role-guard.php';

require_once __DIR__ . '/handlers/syslog.php';
require_once __DIR__ . '/handlers/asset.php';
require_once __DIR__ . '/handlers/assignment.php';
require_once __DIR__ . '/handlers/user.php';
require_once __DIR__ . '/handlers/export.php';
require_once __DIR__ . '/repos/actlog.php';
require_once __DIR__ . '/repos/asset.php';
require_once __DIR__ . '/repos/assignment.php';
require_once __DIR__ . '/repos/user.php';

enum APIResource: string {
  case User = "users";
  case Asset = "assets";
  case Assignment = "assignment";
  case Log = "logs";
  case Exportable = "export";
  case Error = "error";
}

enum APIAction: string {
  case Fetch = "fetch";
  case Search = "search";
  case Add = "add";
  case Edit = "edit";
  case AssignAsset = "assign";
  case RessignAsset = "reassign";
  case ReturnAsset = "return";
  case CondemnAsset = "condemn";
  case ActivateUser = "activate";
  case DeactivateUser = "deactivate";
  case DBStats = "stats";
  case GetSessionUser = "session";
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

final class APIRouter {
  public function __construct(
    private AssetHandler $assetHand,
    private UserHandler $userHand,
    private AssignmentHandler $assignHand,
    private LogHandler $logHand,
    private ExportHandler $expHand,
    private UserRepoInterface $userRepo,
    ) {}

  public function handle(
    APIResource $res,
    APIAction $action,
    array $params,
    array $input,
  ) {
    // Affirm user is still active
    // verifyStatus($this->userRepo);

    $data = match ($res) {
      APIResource::User       => $this->handleUser($action, $params, $input),
      APIResource::Asset      => $this->handleAsset($action, $params, $input),
      APIResource::Assignment => $this->handleAssignment($action, $params, $input),
      APIResource::Log        => $this->handleLog($action, $params, $input),
      APIResource::Exportable => $this->handleExportable($action, $params, $input),
      default                 => throw new Exception("Resource $res->value not found."),
    };

    if (isset($params["redirect"])) {
      header("Location: " . BASE_URL . $params["redirect"]);
      exit;
    }

    return $data;
  }

  private function handleUser(
    APIAction $action,
    array $params,
    array $input,
  ) {
    return match ($action) {
      APIAction::Fetch => $this->userHand->getUser((int)$params["search"]),
      APIAction::DBStats => $this->userHand->getRepoStats(),
      APIAction::GetSessionUser => $this->userHand->getSessionUser(),
      APIAction::Search => $this->userHand->searchUsers(
        search: $params["search"] ?? "",
        status: $params["status"] ?? "",
        privilege: $params["priv"] ?? "",
      ),
      APIAction::Add => $this->userHand->addUser(new User(
        name: new Fullname(
          first: $input['first-name'],
          last: $input['last-name'],
        ),
        email: $input['email'],
        privilege: UserPrivilege::from($input['privilege']),
        isActive: $input["active-status"] === "Active",
      )),
      APIAction::Edit => $this->userHand->editUser(new User(
        empID: $input['employee-id'],
        name: new Fullname(
          first: $input['first-name'],
          last: $input['last-name'],
        ),
        email: $input['email'],
        privilege: UserPrivilege::from($input['privilege']),
        isActive: $input['active-status'] === 'Active',
      )),
      APIAction::ActivateUser => $this->userHand->changeStatus(
        empID: $input["empID"] ?? "", 
        isActive: true, 
      ),
      APIAction::DeactivateUser => $this->userHand->changeStatus(
        empID: $input["empID"] ?? "", 
        isActive: false, 
      ),
      default => throw new Exception("Invalid User action"),
    };
  }

  private function handleAsset(
    APIAction $action,
    array $params,
    array $input,
  ) {
    return match ($action) {
      APIAction::Fetch => $this->assetHand->getAsset($params["search"]),
      APIAction::DBStats => $this->assetHand->getRepoStats(),
      APIAction::Search => $this->assetHand->searchAssets(
        search: $params["search"] ?? "",
        status: $params["status"] ?? "",
        base_date: !empty($params["base_date"])
            ? new DateTimeImmutable($params["base_date"])
            : new DateTimeImmutable("0001-01-01"),
        end_date: !empty($params["end_date"])
            ? new DateTimeImmutable($params["end_date"])
            : new DateTimeImmutable("9999-12-31"),
        check_snum: isset($params["check_snum"])
      ),
      APIAction::Add => $this->assetHand->addAsset(array_map(
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
      )),
      APIAction::Edit => $this->assetHand->editAsset(new Asset(
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
      )),
      APIAction::CondemnAsset => $this->assetHand->changeStatus(
        propNum:  $input["search"],
        status:   AssetStatus::Condemned,
      ),
      default => throw new Exception("Invalid Asset action"),
    };
  }

  private function handleAssignment(
    APIAction $action,
    array $params,
    array $input,
  ) {
    return match ($action) {
      APIAction::Fetch => $this->assignHand->getAssignments($params["user"]),
      APIAction::AssignAsset => $this->assignHand->assign(
        propNums:   $input["assets"],
        date:       new DateTimeImmutable($input['assign-date']),
        assigneeID: $input["user"],
        remarks:    $input["remarks"],
      ),
      APIAction::RessignAsset => $this->assignHand->reassign(
        propNums:   $input["assets"],
        date:       new DateTimeImmutable($input['assign-date']),
        assigneeID: $input["user"],
        remarks:    $input["remarks"],
      ),
      APIAction::ReturnAsset => $this->assignHand->return(
        propNums: $input['assets'],
        date:     new DateTimeImmutable($input['return-date']),
        remarks:  $input['remarks'],
      ),
      default => throw new Exception("Invalid Assignment action"),
    };
  }

  private function handleLog(
    APIAction $action,
    array $params,
    array $input,
  ) {
    return match ($action) {
      APIAction::Search => $this->logHand->getLogs(
        criteria: new LogSearchCriteria(
          empID: $params["actorID"]? (int)$params["actorID"] : null,
          message: $params["message"] ?? "",
          metadata: $params["metadata"] ?? "",
        ),
        page: (int)($params["page"] ?? 1),
        limit: (int)($params["limit"] ?? 20),
      ),
      APIAction::Login => $this->logHand->login($params["code"]),
      APIAction::Logout => $this->logHand->logout(),
      default => throw new Exception("Invalid Log action"),
    };
  }

  private function handleExportable(
    APIAction $action,
    array $params,
    array $input,
  ) {
    return match ($action) {
      APIAction::ExportByStatus => $this->expHand->exportAssetsByStatus(
        statusName:   $params["status"] ?? null,
        add_remarks:  isset($params["add_remarks"]),
      ),
      APIAction::ExportByUser => $this->expHand->exportUserAssignedAssets(
        userParam:    $params["user"] ?? null,
        add_remarks:  isset($params["add_remarks"]),
      ),
      APIAction::ExportByFaculty => $this->expHand->exportFacultyAssignedAssets(
        usersParam:   $params["users"] ?? null,
        add_remarks:  isset($params["add_remarks"]),
      ),
      default => throw new Exception("Invalid Exportable action"),
    };
  }

}