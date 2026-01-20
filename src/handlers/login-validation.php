<?php

function validateLogIn(string $email): bool
{
  require_once __DIR__ . '/../../config/config.php';
  require_once __DIR__ . '/../model/user.php';
  require_once __DIR__ . '/../repos/user.php';
  
  $repo = new UserRepo($pdo);
  $user = $repo->search(new UserSearchCriteria(email: $email));

  if (count(value: $user) == 0) {
    return false;
  }
  
  $userInfo = $user[0];
  $_SESSION['user_id'] = $userInfo->id;
  $_SESSION['user_fname'] = $userInfo->name->first;
  $_SESSION['user_mname'] = $userInfo->name->middle;
  $_SESSION['user_lname'] = $userInfo->name->last;
  $_SESSION['role'] = $userInfo->getPrivilege()->value;
  $_SESSION['logged_in'] = true;

  return true;
}
