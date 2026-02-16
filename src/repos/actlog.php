<?php

declare (strict_types=1);

include_once '../model/user.php';

interface ActLogRepoInterface {
  public function getLogs(
    string $search,   // search term
    int $page,
    int $limit,
  ): array;
  public function countLogs(
    string $search,   // search term
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
    string $search = "",
    int $page = 1,
    int $limit = 50,
  ): array {
    $offset = ($page-1) * $limit;
    $query = "SELECT a.*, e.FName, e.LName 
      FROM actlog a LEFT JOIN employee e ON a.ActorID = e.EmpID
      WHERE a.ActorID LIKE ?
      OR a.Message LIKE ?
      OR JSON_SEARCH(a.Metadata, 'one', ?) IS NOT NULL  
      ORDER BY a.Timestamp DESC 
      LIMIT $limit
      OFFSET $offset
    ";
    $stmt = $this->pdo->prepare($query);
    
    $stmt->execute([
      "%$search%",
      "%$search%",
      $search,
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function countLogs(
    string $search = "",   // search term
  ): int {
    $query = "SELECT COUNT(*) FROM actlog 
      WHERE ActorID LIKE ?
      OR Message LIKE ?
      OR JSON_SEARCH(Metadata, 'one', ?) IS NOT NULL  
    ";
    $stmt = $this->pdo->prepare($query);
    
    $stmt->execute([
      "%$search%",
      "%$search%",
      $search,
    ]);

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
