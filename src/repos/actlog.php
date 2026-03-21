<?php

declare (strict_types=1);

require_once __DIR__ . '/../model/actlog.php';
require_once __DIR__ . '/../model/user.php';

interface ActLogRepoInterface {
  public function getLogs(
    LogSearchCriteria $criteria,
    int $page,
    int $limit,
  ): array;
  public function countLogs(
    LogSearchCriteria $criteria,
  ): int;
  public function add(
    User $user,
    string $log,
    array $metadata,
  ): void;
}

final class ActLogRepo implements ActlogRepoInterface {
  public function __construct(
    public readonly PDO $pdo,
  ) {}

  public function getLogs(
    LogSearchCriteria $criteria = new LogSearchCriteria(),
    int $page = 1,
    int $limit = 50,
  ): array {
    $conds = ["1=1"]; // identity
    $params = [];

    if ($criteria->empID) {
      $conds[] = "a.ActorID = ?";
      $params[] = $criteria->empID;
    }

    foreach([
      "Message" => $criteria->message,
    ] as $col => $val) {
      if (empty($val)) continue;
      $conds[] = "a.$col LIKE ?";
      $params[] = "%$val%";
    }

    if (!empty($criteria->metadata)) {
      $conds[] = "JSON_SEARCH(a.Metadata, 'one', ?) IS NOT NULL";
      $params[] = $criteria->metadata;
    }

    $offset = ($page-1) * $limit;
    $query = "SELECT a.*, e.FName, e.LName 
      FROM actlog a LEFT JOIN employee e ON a.ActorID = e.EmpID
      WHERE " . implode(" AND ", $conds) . "
      ORDER BY a.Timestamp DESC 
      LIMIT $limit
      OFFSET $offset
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function countLogs(
    LogSearchCriteria $criteria = new LogSearchCriteria(),
  ): int {
    $conds = ["1=1"]; // identity
    $params = [];

    if ($criteria->empID) {
      $conds[] = "ActorID = ?";
      $params[] = $criteria->empID;
    }

    foreach([
      "Message" => $criteria->message,
    ] as $col => $val) {
      if (empty($val)) continue;
      $conds[] = "$col LIKE ?";
      $params[] = "%$val%";
    }

    if (!empty($criteria->metadata)) {
      $conds[] = "JSON_SEARCH(Metadata, 'one', ?) IS NOT NULL";
      $params[] = $criteria->metadata;
    }

    $query = "SELECT COUNT(*) FROM actlog WHERE " . implode(" AND ", $conds);

    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);

    $result = $stmt->fetchColumn();
    return (int)$result;
  }

  public function add(
    User $user,
    string $log,
    array $metadata,
  ): void {
    $assoc = [
      "ActorID" => $user->empID, 
      "Message" => $log,
      "Metadata" => json_encode($metadata),
    ];

    $query = "INSERT INTO actlog (" . implode(',', array_keys($assoc)) .") VALUES (" . implode(',', array_fill(0, count($assoc), '?')) . ");"; 
    
    $this->pdo->prepare($query)->execute(array_values($assoc));      
  }
}
