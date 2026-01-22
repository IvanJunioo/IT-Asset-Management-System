<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../model/user.php';
require_once '../repos/user.php';
require_once '../manager/logger.php';

if ($_POST['action'] == 'submit') {
  $repo = new UserRepo($pdo);
  
  $empID = $_POST['employee-id'];
  $name = new Fullname(
    $_POST['first-name'],
    $_POST['middle-name'],
    $_POST['last-name'],
  );
  $status = $_POST['active-status'] === 'Active';
  
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
  
  $repo->update($user);

  systemLog(
    "modified user $empID",
    []
  );
}

header('Location: ../../public/views/user-manager.php');

exit;