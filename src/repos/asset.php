<?php

declare (strict_types=1);

include_once '../model/asset.php';

interface AssetRepoInterface {
  public function identify(string $propNum): Asset;
  public function search(AssetSearchCriteria $criteria): array;
  public function count(AssetSearchCriteria $criteria): int;

  public function add(Asset $asset): void;
  public function update(Asset $asset): void;
}

final class AssetRepo implements AssetRepoInterface {
  public function __construct(
    public readonly PDO $pdo,
  ) {}

  public function identify(string $propNum): Asset {
    $assets = $this->search(new AssetSearchCriteria(propNum: $propNum));
    if (count($assets) == 0) throw new Exception("Asset not found!");
    return $assets[0];
  }
  
  public function search(AssetSearchCriteria $criteria = new AssetSearchCriteria()): array {
    $conds = ["1=1"];
    $params = [];
    
    if (!empty($criteria->status)) {
      $placeholders = implode(',',array_fill(0, count($criteria->status), '?'));
      $conds[] = "Status IN ($placeholders)";
      foreach ($criteria->status as $s) $params[] = $s->name; 
    }

    $conds[] = "Price >= ?"; 
    $conds[] = "Price <= ?";
    $conds[] = "PurchaseDate >= ?"; 
    $conds[] = "PurchaseDate <= ?";
    $params[] = $criteria->price_min;
    $params[] = $criteria->price_max;
    $params[] = $criteria->base_date->format("Y-m-d");
    $params[] = $criteria->end_date->format("Y-m-d");
  
    foreach ([
      "PropNum" => $criteria->propNum,
      "ProcNum" => $criteria->procNum,
      "SerialNum" => $criteria->serialNum,
      "ShortDesc" => $criteria->description,
      "Specs" => $criteria->specs,
      "Remarks" => $criteria->remarks,
    ] as $col => $val) {
      if (empty($val)) continue;
      $conds[] = "$col LIKE ?";
      $params[] = "%$val%";
    }

    $query = "SELECT * FROM asset WHERE " . implode(" AND ", $conds) . " LIMIT $criteria->limit";
    
    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $assets = [];
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

  public function count(AssetSearchCriteria $criteria = new AssetSearchCriteria()): int {
    $conds = ["1=1"];
    $params = [];
    
    if (!empty($criteria->status)) {
      $placeholders = implode(',',array_fill(0, count($criteria->status), '?'));
      $conds[] = "Status IN ($placeholders)";
      foreach ($criteria->status as $s) $params[] = $s->name; 
    }

    $conds[] = "Price >= ?"; 
    $conds[] = "Price <= ?";
    $conds[] = "PurchaseDate >= ?"; 
    $conds[] = "PurchaseDate <= ?";
    $params[] = $criteria->price_min;
    $params[] = $criteria->price_max;
    $params[] = $criteria->base_date->format("Y-m-d");
    $params[] = $criteria->end_date->format("Y-m-d");
  
    foreach ([
      "PropNum" => $criteria->propNum,
      "ProcNum" => $criteria->procNum,
      "SerialNum" => $criteria->serialNum,
      "ShortDesc" => $criteria->description,
      "Specs" => $criteria->specs,
      "Remarks" => $criteria->remarks,
    ] as $col => $val) {
      if (empty($val)) continue;
      $conds[] = "$col LIKE ?";
      $params[] = "%$val%";
    }

    $query = "SELECT COUNT(*) FROM asset WHERE " . implode(" AND ", $conds) . " LIMIT $criteria->limit";
    
    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);
    
    $result = $stmt->fetchColumn();

    return (int)$result;
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
      ":r" => " $asset->remarks", 
      ":d" => $asset->description,
      ":p" => $asset->price,
      ":u" => $asset->url,
    ]);      
  }
}
