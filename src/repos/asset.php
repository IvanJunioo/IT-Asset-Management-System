<?php

declare (strict_types=1);

include_once '../model/asset.php';

interface AssetRepoInterface {
  public function identify(string $propNum): Asset;
  public function search(AssetSearchCriteria $criteria): array;
  public function count(AssetSearchCriteria $criteria): int;
  public function getProcNums(string $propNum): array;

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
        procNums: $this->getProcNums($ass["PropNum"]),
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

  public function getProcNums(string $propNum): array {
    $query = "SELECT ProcNum FROM procurement WHERE PropNum = ?";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([$propNum]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }
    
  public function add(Asset $asset): void {
    $assoc = [
      "PropNum" => $asset->propNum,
      "SerialNum" => $asset->serialNum,
      "PurchaseDate" => $asset->purchaseDate,
      "Specs" => $asset->specs,
      "Remarks" => $asset->remarks,
      "Status" => $asset->status->value,
      "ShortDesc" => $asset->description,
      "Price" => $asset->price,
      "URL" => $asset->url,
    ];

    $query = "INSERT INTO asset (" . implode(',', array_keys($assoc)) .") VALUES (" . implode(',', array_fill(0, count($assoc), '?')) . ");"; 
    
    $this->pdo->prepare($query)->execute(array_values($assoc));      
  
    $this->updateProcNums($asset);
  }
    
  public function update(Asset $asset): void {
    $conds = [];
    $params = [];

    foreach([
      "SerialNum" => $asset->serialNum,
      "PurchaseDate" => $asset->purchaseDate,
      "Specs" => $asset->specs,
      "Status" => $asset->status->value,
      "Remarks" => $asset->remarks,
      "ShortDesc" => $asset->description,
      "Price" => $asset->price,
      "URL" => $asset->url,
    ] as $col => $val) {
      $conds[] = "$col = ?";
      $params[] = "$val";
    }
    
    $query = "UPDATE asset SET " . implode(',', $conds) . " WHERE PropNum = ?;";
    
    $this->pdo->prepare($query)->execute([...$params, $asset->propNum]);
    
    $this->updateProcNums($asset);
  }

  private function updateProcNums(Asset $asset): void {
    $this->pdo->beginTransaction();
    try {
      $stmt = $this->pdo->prepare("DELETE FROM procurement WHERE PropNum = ?");
      $stmt->execute([$asset->propNum]);

      $placeholders = implode(',', array_fill(0, count($asset->procNums), "(?, ?)"));
      $query = "INSERT INTO procurement (PropNum, ProcNum) VALUES $placeholders";

      $vals = [];
      foreach ($asset->procNums as $num) {
        $vals[] = $asset->propNum;
        $vals[] = $num;
      }

      $this->pdo->prepare($query)->execute($vals);

      $this->pdo->commit();
    } catch (Exception $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }
}
