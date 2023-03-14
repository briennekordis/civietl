<?php
namespace Civietl\Writer;

use League\Csv\Writer;

class CsvWriter implements WriterInterface {
  private Writer $csv;

  public function __construct($options) {
    $this->csv = Writer::createFromPath($options['file_path'], 'w');
  }

  public function writeOne($row) : void {
    $this->csv->insertOne($row);
  }

  public function writeAll($rows) : void {
    $columnNames = array_keys(reset($rows));
    $this->csv->insertOne($columnNames);
    $this->csv->insertAll($rows);
  }

}
