<?php
namespace Civietl\Cache;

class ArrayCache implements CacheService {
  private array $data;
  private string $primaryKey;

  public function __construct($primaryKey) {
    $this->$primaryKey = $primaryKey;
  }

  public function addRow(array $row) : string {
    $this->data[$row[$this->primaryKey]] = $row;
    return $row[$this->primaryKey];
  }

  public function clearCache() : void {
    unset($this->data);
  }

  public function getRow($primaryKey) : array {
    return $this->data[$primaryKey];
  }

  public function getData() : array {
    return $this->data;
  }

}
