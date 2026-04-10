<?php
require_once __DIR__ . '/../config/config.php';

require_once __DIR__ . '/utilities/role-guard.php';
require_once __DIR__ . '/router.php';

// Initialize repos and router
$assetRepo = new AssetRepo($pdo);
$userRepo = new UserRepo($pdo);
$assignRepo = new AssignmentRepo($pdo);
$logRepo = new ActLogRepo($pdo);

$router = new APIRouter(
  new AssetHandler($assetRepo, $assignRepo),
  new UserHandler($userRepo, $assignRepo),
  new AssignmentHandler(new AssignmentManager($assetRepo, $assignRepo, $userRepo)),
  new LogHandler($logRepo, $userRepo),
  new ExportHandler($userRepo, $assetRepo, $assignRepo),
);

// Affirm user is still active
verifyStatus($userRepo);
