<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../model/user.php';
require_once __DIR__ . '/../repos/user.php';
require_once __DIR__ . '/../manager/logger.php';

if ($_POST['action'] == 'submit') {
  $repo = new UserRepo($pdo);
  
  $old = $repo->identify($_POST['employee-id']);
  
  $user = new User(
    empID: $_POST['employee-id'],
    name: new Fullname(
      first: $_POST['first-name'],
      last: $_POST['last-name'],
    ),
    email: $_POST['email'],
    privilege: UserPrivilege::from($_POST['privilege']),
    isActive: $_POST['active-status'] === 'Active',
  );
  
  $repo->update($user);

  systemLog(
    "modified user $user->empID",
    [
      "action" => "modify",
      "object" => "user",
      "empID" => $user->empID,
      "diff" => array_diff_assoc($user->jsonSerialize(), $old->jsonSerialize()),
    ]
  );
}

header('Location: ../../public/views/user-manager.php');

exit;