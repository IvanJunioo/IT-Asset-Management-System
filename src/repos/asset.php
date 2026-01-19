<?php

declare (strict_types=1);

include_once '../model/asset.php';

interface AssetRepoInterface {
  public function search(AssetSearchCriteria $criteria): array;
  public function count(AssetSearchCriteria $criteria): int;

  public function add(Asset $asset): void;
  public function update(Asset $asset): void;
  public function delete(Asset $asset): void;
}

final class AssetRepo implements AssetRepoInterface {
  public function __construct(
    public readonly PDO $pdo,
  ) {}
  
  // TODO : assign assets searched
  public function search(AssetSearchCriteria $criteria = new AssetSearchCriteria()): array {     
    $st = implode(',',array_fill(0, count($criteria->status), '?'));
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
      LIMIT $criteria->limit
    ;";
    
    $stmt = $this->pdo->prepare($query);
    
    foreach ($criteria->status as $s ) {
      $params[] = $s->name;
    }
    
    $params[] = (string) $criteria->price_min;
    $params[] = (string) $criteria->price_max;
    $params[] = $criteria->base_date->format("Y-m-d");
    $params[] = $criteria->end_date->format("Y-m-d");
    $params[] = "%$criteria->propNum%";
    $params[] = "%$criteria->procNum%";
    $params[] = "%$criteria->serialNum%";
    $params[] = "%$criteria->description%";
    $params[] = "%$criteria->specs%";
    $params[] = "%$criteria->remarks%";
    
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

  public function count(AssetSearchCriteria $criteria): int {
    $st = implode(',',array_fill(0, count($criteria->status), '?'));
    $query = "SELECT COUNT(*) FROM asset WHERE 
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
      LIMIT $criteria->limit
    ;";
    
    $stmt = $this->pdo->prepare($query);
    
    foreach ($criteria->status as $s ) {
      $params[] = $s->name;
    }
    
    $params[] = (string) $criteria->price_min;
    $params[] = (string) $criteria->price_max;
    $params[] = $criteria->base_date->format("Y-m-d");
    $params[] = $criteria->end_date->format("Y-m-d");
    $params[] = "%$criteria->propNum%";
    $params[] = "%$criteria->procNum%";
    $params[] = "%$criteria->serialNum%";
    $params[] = "%$criteria->description%";
    $params[] = "%$criteria->specs%";
    $params[] = "%$criteria->remarks%";
    
    $stmt->execute($params);
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result[0]; // ?
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
      Remarks = CONCAT(Remarks, :r),
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
      ":r" => " $asset->remarks", 
      ":d" => $asset->description,
      ":p" => $asset->price,
      ":u" => $asset->url,
    ]);      
  }
  
  public function delete(Asset $asset): void {
    if ($asset->status != AssetStatus::ToCondemn) {
      return;
    }
    
    $query = "UPDATE asset SET Status = :st WHERE PropNum = :id;"; 
    $this->pdo->prepare($query)->execute([
      ":id" => $asset->propNum,
      ":st" => "Condemned",
    ]);
    
  }
}
