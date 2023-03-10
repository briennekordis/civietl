<?php
namespace Civietl\Reader;

class ReaderService {
  private $reader;

  public function __construct(ReaderInterface $reader) {
    $this->reader = $reader;
  }

  public function getRow($columnName, $value) : array {
    return $this->reader->getRow($columnName, $value);
  }

  public function getRows() : array {
    return $this->reader->getRows();
  }

  public function getPrimaryKeyColumn() : string {
    return $this->reader->getPrimaryKeyColumn();
  }

  public function setPrimaryKeyColumn(string $columnName) : void {
    $this->reader->setPrimaryKeyColumn($columnName);
  }

}
