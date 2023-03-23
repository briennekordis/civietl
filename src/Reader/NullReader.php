<?php
namespace Civietl\Reader;

class NullReader implements ReaderInterface {

  public function __construct($options) {
  }

  public function getColumnNames() : array {
    return [];
  }

  public function getRow($id) : array {
    return [];
  }

  public function getRows() : array {
    return [];
  }

  public function getPrimaryKeyColumn() : string {
    return '';
  }

  public function setPrimaryKeyColumn(string $columnName) : void {
  }

}
