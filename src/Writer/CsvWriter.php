<?php
namespace Civietl\Writer;

use League\Csv\Writer;

class CsvWriter implements WriterInterface {
  private Writer $csv;

  public function __construct($options) {
    $this->csv = Writer::createFromPath($options['file_path'], 'w');
  }

  public function writeOne($row) : array {
    $this->csv->insertOne($row);
    return [];
  }

  public function writeAll($rows) : array {
    $columnNames = array_keys(reset($rows));
    $this->csv->insertOne($columnNames);
    $this->csv->insertAll($rows);
    return [];
  }

}
