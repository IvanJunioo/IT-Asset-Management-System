<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/handlers/error.php';
require_once __DIR__ . '/router.php';

$errHand = new ErrorHandler();

try {
  // Initialize session
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
    
  // Initialize Database connection
  $pdo = new PDO($dbsource, $dbusername, $dbpassword, [
    PDO::ATTR_PERSISTENT => false,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);  # PHP Data Object

  // Initialize repos
  $assetRepo  = new AssetRepo($pdo);
  $userRepo   = new UserRepo($pdo);
  $assignRepo = new AssignmentRepo($pdo);
  $logRepo    = new ActLogRepo($pdo);
  
  // Initialize handlers and router
  $logHand = new LogHandler($logRepo, $userRepo);
  $router = new APIRouter(
    new AssetHandler($assetRepo, $assignRepo, $logHand),
    new UserHandler($userRepo, $assignRepo, $logHand),
    new AssignmentHandler(new AssignmentManager($assetRepo, $assignRepo, $userRepo), $logHand),
    $logHand,
    new ExportHandler($userRepo, $assetRepo, $assignRepo),
  );
}
catch (Throwable $e) {
  $errHand->handle($e);
}
