<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/user.php';
require_once '../manager/logger.php';

$empID = $_POST['search'] ?? "";

$repo = new UserRepo($pdo);

$user = $repo->identify($empID);
$user->isActive = false;
$repo->update($user);

systemLog(
  "deactivated user $empID",
  []
);


exit;