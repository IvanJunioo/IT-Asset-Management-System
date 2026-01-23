<?php

declare (strict_types=1);

include_once '../model/user.php';

interface ActLogRepoInterface {
  public function getLogs(
    string $search,   // search term
    int $limit,
  ): array;
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
    int $limit = 50,
  ): array {
    $query = "SELECT * FROM actlog 
      WHERE ActorID LIKE ?
      OR Log LIKE ?
      OR JSON_SEARCH(Metadata, 'one', ?) IS NOT NULL  
      ORDER BY Timestamp DESC 
      LIMIT $limit
    ";
    $stmt = $this->pdo->prepare($query);
    
    $stmt->execute([
      "%$search%",
      "%$search%",
      $search,
    ]);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  public function add(
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
