<?php
namespace Civietl\Reader;

use League\Csv\Reader;

class CsvReader implements ReaderInterface {
  /**
   * @var headers
   * Set to FALSE if the first row isn't column names.
   */
  private bool $hasHeaders = TRUE;
  private string $primaryKeyColumn;
  private Reader $csv;

  public function __construct($options) {
    $this->hasHeaders = $options['headers'] ?? $this->hasHeaders;
    $this->csv = Reader::createFromPath($options['file_path'], 'r');
    $this->setPrimaryKeyColumn($options['data_primary_key']);
  }

  public function getColumnNames() : array {
    $header = [];
    if ($this->hasHeaders) {
      $this->csv->setHeaderOffset(0);
      $header = $this->csv->getHeader();

    }
    else {
      $row = $this->csv->fetchOne(0);
      $header = range(0, count($row));
    }
    return $header;
  }

  public function getRow($id) : array {
    $query = (new \League\Csv\Statement())->limit(1)->where(fn(array $record) => $record[$this->primaryKeyColumn] == $id);
    $result = $query->process($this->csv);
    return (array) $result->getRecords();
  }

  public function getRows() : array {
    $rows = [];
    if ($this->hasHeaders) {
      $this->csv->setHeaderOffset(0);
    }
    $query = \League\Csv\Statement::create();
    $records = $query->process($this->csv);
    foreach ($records as $record) {
      $rows[] = $record;
    }
    return $rows;
  }

  public function getPrimaryKeyColumn() : string {
    return $this->primaryKeyColumn;
  }

  public function setPrimaryKeyColumn(string $columnName) : void {
    $this->primaryKeyColumn = $columnName;
  }

}
