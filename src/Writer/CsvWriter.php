<?php
namespace Civietl\Reader;

use League\Csv\Writer;

class CsvWriter implements WriterInterface {
  private Writer $csv;

  public function __construct($options) {
    $this->csv->createFromPath($options['csv_output'], 'w');
    $this->csv->insertOne($options['column_names']);
  }

  public function writeOne($row) : void {
    $this->csv->insertOne($row);
  }

  public function writeAll($rows) : void {
    $this->csv->insertAll($rows);
  }

}
