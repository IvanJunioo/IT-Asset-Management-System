<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/actlog.php';
require_once __DIR__ . '/../repos/user.php';

final class ActLogHandler {
  public function __construct(
    private readonly ActLogRepoInterface $logRepo,
    private readonly UserRepoInterface $userRepo,
  ) {}

  public function getLogs(
    string $search,
    int $page,
    int $limit,
  ): array {
    $logs = [];
    foreach ($this->logRepo->getLogs($search,$page,$limit) as $log) {
      $metadata = json_decode($log["Metadata"], true);
      
      switch ($metadata["object"]) {
        case "asset":
          break;
        case "user":
          $user = $this->userRepo->identify($metadata["empID"]);
          $log["objName"] = $user->name->FLast();
          break;
      }
      
      $logs[] = $log;
    }
    return [
      "logs" => $logs,
      "count" => $this->logRepo->countLogs(search: $search),
    ];
  }
}
