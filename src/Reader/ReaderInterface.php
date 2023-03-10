<?php
namespace Civietl\Reader;

interface ReaderInterface {

  public function __construct($primaryKey);

  public function getColumnNames() : array;

  public function getRow($id) : array;

  public function getRows() : array;

  public function getPrimaryKeyColumn() : string;

  public function setPrimaryKeyColumn(string $columnName) : void;

}
