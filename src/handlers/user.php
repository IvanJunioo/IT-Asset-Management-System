<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/actlog.php';
require_once __DIR__ . '/../repos/user.php';

final class UserHandler {
  public function __construct(
    private readonly ActLogRepoInterface $logRepo,
    private readonly UserRepoInterface $userRepo,
  ) {}

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
}
