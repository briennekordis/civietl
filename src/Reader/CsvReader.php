<?php
namespace Civietl\Reader;

use League\Csv\Reader;

class CsvReader implements ReaderInterface {
  /**
   * @var headers
   * Set to FALSE if the first row isn't column names.
   */
  private bool $headers = TRUE;
  private string $primaryKeyColumn;
  private Reader $csv;

  public function __construct($options) {
    $this->headers = $options['headers'] ?? $options;
    $this->csv = Reader::createFromPath($options['file'], 'r');
  }

  public function getColumnNames() : array {
    $header = [];
    if ($this->headers) {
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
    return (array) $this->csv->getRecords();
  }

  public function getPrimaryKeyColumn() : string {
    return $this->primaryKeyColumn;
  }

  public function setPrimaryKeyColumn(string $columnName) : void {
    $this->primaryKeyColumn = $columnName;
  }

}
