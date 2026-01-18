<?php

declare (strict_types=1);

include_once '../model/user.php';

interface ActLogRepoInterface {
  public function getLogs(int $limit = 50): array;
  public function systemLog(
    User $user,
    string $log,
    array $metadata,
  ): void;
}

final class ActLogRepo implements ActlogRepoInterface {
  public function __construct(
    public readonly PDO $pdo,
  ) {}

  public function getLogs(int $limit = 50): array {
    $query = "SELECT * FROM actlog LIMIT $limit";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  public function systemLog(
    User $user,
    string $log,
    array $metadata,
  ): void {
    $query = "INSERT INTO actlog (ActorID, Log, Metadata) VALUES (?,?,?)";

    $this->pdo->prepare($query)->execute([
      $user->empID,
      $log,
      json_encode($metadata),
    ]);
  }
}
