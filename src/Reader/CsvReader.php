<?php
namespace Civietl\Reader;

class CsvReader implements ReaderService {
  /**
   * @var headers
   * Set to FALSE if the first row isn't column names.
   */
  private bool $headers = TRUE;

  public function __construct($options) {
    $this->headers = $options['headers'] ?? $options;
  }

  public function getColumnNames() : array {

  }

  public function getRow() : array {
    return $this->data[$primaryKey];
  }

  public function getData() : array {
    return $this->data;
  }

}
