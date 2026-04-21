<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/user.php';
require_once __DIR__ . '/../repos/assignment.php';
require_once __DIR__ . '/../manager/logger.php';

final class UserHandler {
  public function __construct(
    private readonly UserRepoInterface $userRepo,
    private readonly AssignmentRepo $assignRepo,
  ) {}

  public function getUser(int $empID): User {
    return $this->userRepo->identify($empID);
  }

  public function getRepoStats(): array {
    return [
      "usersTotal" => $this->userRepo->count(new UserSearchCriteria()),
      "usersActive" => $this->userRepo->count(new UserSearchCriteria(isActive: ["Active"])),
    ];
  }

  public function getSessionUser(): User {
    return $this->userRepo->identify($_SESSION["user_id"]);
  }

  public function searchUsers(
    string $search,
    string $status,
    string $privilege,
  ) {
    $status = $status !== ""? explode(',', $status) : null;
    $privilege = $privilege !== ""? array_map("UserPrivilege::from", explode(',', $privilege)) : null;

    $users = [];
    foreach (array_values(array_map("unserialize", array_unique(array_map("serialize", [
      ...$this->userRepo->search(new UserSearchCriteria(fullname: new Fullname(first: $search), isActive: $status, privileges: $privilege)),
      ...$this->userRepo->search(new UserSearchCriteria(fullname: new Fullname(last: $search), isActive: $status, privileges: $privilege)),
      ...$this->userRepo->search(new UserSearchCriteria(email: $search, isActive: $status, privileges: $privilege)),
    ])))) as $user) {
      $users[] = [
        ...$user->jsonSerialize(),
        "isCurrentUser" => $_SESSION['user_id'] == $user->empID,
        "assignments" => array_map(fn($asset) => $asset->propNum, $this->assignRepo->getAssignedAssets($user)),
      ];
    }
    return $users;
  }

  public function addUser(User $user): void {
    $this->userRepo->add($user);

    // Sets user empID (assuming unique email)
    $user = $this->userRepo->search(new UserSearchCriteria(email: $user->email))[0];

    systemLog(
      "added new user " . $user->name->last,
      [
        "action" => "add",
        "object" => "user",
        "empID" => $user->empID,
      ]
    );
  }

  public function editUser(User $user): void {
    // Only SuperAdmin can edit user details
    if (isset($_SESSION['privilege']) && $_SESSION['privilege'] !== 'SuperAdmin') {
      http_response_code(403);
      throw new Exception("Access denied: Only SuperAdmin can modify user details");
    }

    $old = $this->userRepo->identify($user->empID);
    $diff = array_diff_assoc($user->jsonSerialize(), $old->jsonSerialize());
    if (empty($diff)) return;

    $logMessage = "modified user $user->empID";
    $logData = [
      "action" => "modify",
      "object" => "user",
      "empID" => $user->empID,
    ];

    if ($user->isActive !== $old->isActive) {
      $action = $user->isActive ? "activate" : "deactivate";
      $logData["action"] = $action;

      if (!$user->isActive) {
        $assets = $this->assignRepo->getAssignedAssets($user);
        if (!empty($assets)) {
          $logData["hasAssets"] = true;
        }
      }
    } else {
      $logData["diff"] = $diff;
    }
    systemLog($logMessage, $logData);

    $this->userRepo->update($user);
  }

  public function changeStatus(string $empID, bool $isActive): void {
    // Only SuperAdmin can change user status
    if (isset($_SESSION['privilege']) && $_SESSION['privilege'] !== 'SuperAdmin') {
      http_response_code(403);
      throw new Exception("Access denied: Only SuperAdmin can modify user status");
    }

    $user = $this->userRepo->identify($empID);
    $user->isActive = $isActive;

    $action = $isActive? "activate" : "deactivate";

    $this->userRepo->update($user);

    $logData = [
      "action" => $action,
      "object" => "user",
      "empID" => $empID,
    ];

    if (!$user->isActive) {
      $assets = $this->assignRepo->getAssignedAssets($user);
      if (!empty($assets)) {
        $logData["hasAssets"] = true;
      }
    }

    systemLog(
      "modified user $empID",
      $logData
    );  
  }
}
