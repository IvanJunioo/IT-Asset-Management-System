<?php

declare (strict_types=1);

include_once '../model/asset.php';

interface AssetRepoInterface {
  public function search(
    # Returns array of Asset objects if successful
    float $price_min,
    float $price_max,
    DateTimeImmutable $base_date,
    DateTimeImmutable $end_date,
    array $status,
    string $propNum,
    string $procNum,
    string $serialNum,
    string $specs,
    string $description,
    string $remarks,
    int $limit,
  ): array;

  public function add(Asset $asset): void;
  public function update(Asset $asset): void;
  public function delete(Asset $asset): void;
}

final class AssetRepo implements AssetRepoInterface {
  public function __construct(
    public readonly PDO $pdo,
  ) {}
  
  // TODO : assign assets searched
  public function search(
    float $price_min = 0,
    float $price_max = 10**12 - 0.01,
    ?DateTimeImmutable $base_date = null,
    ?DateTimeImmutable $end_date = null,
    ?array $status = null,  # must be nonempty
    
    string $propNum = "",
    string $procNum = "",
    string $serialNum = "",
    string $specs = "",
    string $description = "",
    string $remarks = "",
    int $limit = 50,
  ): array { 
    $base_date ??= new DateTimeImmutable("0001-01-01");
    $end_date ??= new DateTimeImmutable("9999-12-31");
    $status ??= AssetStatus::cases();
    
    $st = implode(',',array_fill(0, count($status), '?'));
    $query = "SELECT * FROM asset WHERE 
      Status IN ($st)
      AND Price >= ? 
      AND Price <= ?
      AND PurchaseDate >= ? 
      AND PurchaseDate <= ?
      AND PropNum LIKE ?
      AND ProcNum LIKE ?
      AND SerialNum LIKE ?
      AND ShortDesc LIKE ?
      AND Specs LIKE ?
      AND Remarks LIKE ?
      LIMIT $limit
    ;";
    
    $stmt = $this->pdo->prepare($query);
    
    foreach ($status as $s ) {
      $params[] = $s->name;
    }
    
    $params[] = (string) $price_min;
    $params[] = (string) $price_max;
    $params[] = $base_date->format("Y-m-d");
    $params[] = $end_date->format("Y-m-d");
    $params[] = "%$propNum%";
    $params[] = "%$procNum%";
    $params[] = "%$serialNum%";
    $params[] = "%$description%";
    $params[] = "%$specs%";
    $params[] = "%$remarks%";
    
    $stmt->execute($params);
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $assets = []; # Initially empty list (not null)
    foreach ($result as $ass) {
      $asset = new Asset(
        propNum: $ass["PropNum"],
        procNum: $ass["ProcNum"],
        serialNum: $ass["SerialNum"],
        purchaseDate: $ass["PurchaseDate"],
        specs: $ass["Specs"],
        description: $ass["ShortDesc"],
        status: AssetStatus::from($ass["Status"]),
        url: $ass["URL"],
        remarks: $ass["Remarks"],
        price: (float)$ass["Price"],
      );
      
      $assets[] = $asset;
    }
    return $assets;
  }
    
  public function add(Asset $asset): void {
    $query = "INSERT INTO asset (PropNum, SerialNum, ProcNum, PurchaseDate, Specs, Remarks, Status, ShortDesc, Price, URL) VALUES (?,?,?,?,?,?,?,?,?,?);"; 
    
    $this->pdo->prepare($query)->execute([
      $asset->propNum,
      $asset->serialNum, 
      $asset->procNum,
      $asset->purchaseDate,
      $asset->specs,
      $asset->remarks,
      $asset->status->value,
      $asset->description,
      $asset->price,
      $asset->url,
    ]);      
  }
    
  public function update(Asset $asset): void {
    $query = "UPDATE asset SET 
      SerialNum = :snum,
      ProcNum = :pnum,
      PurchaseDate = :pdate,
      Specs = :s,
      Status = :st,
      Remarks = :r,
      ShortDesc = :d,   
      Price = :p, 
      URL = :u
      WHERE PropNum = :id;";
    
    $this->pdo->prepare($query)->execute([
      ":id" => $asset->propNum,
      ":snum" => $asset->serialNum,
      ":pnum" => $asset->procNum,
      ":pdate" => $asset->purchaseDate,
      ":s" => $asset->specs,
      ":st" => $asset->status->value,
      ":r" => $asset->remarks,
      ":d" => $asset->description,
      ":p" => $asset->price,
      ":u" => $asset->url,
    ]);      
  }
  
  public function delete(Asset $asset): void {
    $query1 = "DELETE FROM assignment WHERE assignment.PropNum = ?;";
    $query2 = "DELETE FROM asset WHERE asset.PropNum = ?;"; 

    $this->pdo->prepare($query1)->execute([$asset->propNum]);
    $this->pdo->prepare($query2)->execute([$asset->propNum]);
  }
}
