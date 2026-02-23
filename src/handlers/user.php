<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/user.php';
require_once __DIR__ . '/../manager/logger.php';

final class UserHandler {
  public function __construct(
    private readonly ActLogRepoInterface $logRepo,
    private readonly UserRepoInterface $userRepo,
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

    return array_values(array_map("unserialize", array_unique(array_map("serialize", [
      ...$this->userRepo->search(new UserSearchCriteria(fullname: new Fullname(first: $search), isActive: $status, privileges: $privilege)),
      ...$this->userRepo->search(new UserSearchCriteria(fullname: new Fullname(last: $search), isActive: $status, privileges: $privilege)),
      ...$this->userRepo->search(new UserSearchCriteria(email: $search, isActive: $status, privileges: $privilege)),
    ]))));
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
    $old = $this->userRepo->identify($user->empID);

    $diff = array_diff_assoc($user->jsonSerialize(), $old->jsonSerialize());
    if (empty($diff)) return;

    $this->userRepo->update($user);

    systemLog(
      "modified user $user->empID",
      [
        "action" => "modify",
        "object" => "user",
        "empID" => $user->empID,
        "diff" => $diff,
      ]
    );
  }

  public function changeStatus(string $empID, bool $isActive): void {
    $user = $this->userRepo->identify($empID);
    $user->isActive = $isActive;

    $action = $isActive? "activate" : "deactivate";

    $this->userRepo->update($user);

    systemLog(
      "modified user $empID",
      [
        "action" => $action,
        "object" => "user",
        "empID" => $empID,
      ]
    );  
  }
}
