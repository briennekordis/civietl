<?php
namespace Civietl\Cache;

class ArrayCache implements CacheInterface {
  private array $data;
  private string $primaryKeyColumn;

  public function __construct($primaryKeyColumn) {
    $this->$primaryKeyColumn = $primaryKeyColumn;
  }

  public function addRow(array $row) : string {
    $id = $row[$this->primaryKeyColumn];
    $this->data[$id] = $row;
    return $id;
  }

  public function clearCache() : void {
    unset($this->data);
  }

  public function getRow($id) : array {
    return $this->data[$id];
  }

  public function getData() : array {
    return $this->data;
  }

}
