<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../model/user.php';
require_once __DIR__ . '/../repos/user.php';
require_once __DIR__ . '/../manager/logger.php';

if ($_POST['action'] == 'submit') {
  $repo = new UserRepo($pdo);
        
  $user = new User(
    empID: 0, // DB auto-increments
    name: new Fullname(
      first: $_POST['first-name'],
      last: $_POST['last-name'],
    ),
    email: $_POST['email'],
    privilege: UserPrivilege::from($_POST['privilege']),
    isActive: $_POST["active-status"] === "Active",
  );

  $repo->add($user);

  systemLog(
    "added new user " . $user->name->last,
    [
      "action" => "add",
      "object" => "user",
      "empID" => $user->empID,
    ]
  );
}

header('Location: ../../public/views/user-manager.php');

exit;
