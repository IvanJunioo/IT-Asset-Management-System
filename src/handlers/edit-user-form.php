<?php

require_once '../includes/request-guard.php';
require_once '../includes/dbh-inc.php';
require_once '../model/database.php';

if ($_POST['action'] == 'submit') {
  $db = new Database($pdo);
  
  $privilege = $_POST['privilege'];
  
  $user = match ($privilege) {
    'Super Admin' => new SuperAdmin(
        empID: $_POST['employee-id'],
        name: new Fullname(
          $_POST['first-name'],
          $_POST['middle-name'],
          $_POST['last-name']
        ),
        email: $_POST['email']
      ),
    'Admin' => new Admin(
      empID: $_POST['employee-id'],
      name: new Fullname(
        $_POST['first-name'],
        $_POST['middle-name'],
        $_POST['last-name'],
      ),
      email: $_POST['email']
    ),
    default => new Faculty(
      empID: $_POST['employee-id'],
      name: new Fullname(
        $_POST['first-name'],
        $_POST['middle-name'],
        $_POST['last-name'],
      ),
      email: $_POST['email']
    ),
  };

  if ($_POST['active-status'] === 'Inactive'){
    $user->setActiveStatus(False);
  }
  
  $db->updateUser($user);
}

header('Location: ../views/user-manager.php');
exit;