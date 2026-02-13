<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../model/user.php';
require_once __DIR__ . '/../repos/user.php';
require_once __DIR__ . '/../manager/logger.php';

if ($_POST['action'] == 'submit') {
  $repo = new UserRepo($pdo);
  
  $empID = $_POST['employee-id'];
  $name = new Fullname(
    first: $_POST['first-name'],
    last: $_POST['last-name'],
  );
  $status = $_POST["active-status"] === "Active";
  
  $user = match (UserPrivilege::from($_POST['privilege'])) {
    UserPrivilege::SuperAdmin => new SuperAdmin(
      empID: $empID,
      name: $name,
      email: $_POST['email'],
      isActive: $status,
    ),
    UserPrivilege::Admin => new Admin(
      empID: $empID,
      name: $name,
      email: $_POST['email'],
      isActive: $status,
    ),
    UserPrivilege::Faculty => new Faculty(
      empID: $empID,
      name: $name,
      email: $_POST['email'],
      isActive: $status,
    ),
  };
  
  $repo->add($user);
  $repo->updateContacts($user, $_POST["phone"]);

  systemLog(
    "added new user $empID",
    [
      "action" => "add",
      "object" => "user",
      "empID" => $empID,
    ]
  );
}

header('Location: ../../public/views/user-manager.php');

exit;
