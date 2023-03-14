<?php
namespace Civietl\Writer;

use League\Csv\Writer;

class JsonWriter implements WriterInterface {
  private Writer $csv;

  public function __construct($options) {
    $this->csv = Writer::createFromPath($options['file_path'], 'w');
  }

  public function writeOne($row) : void {
    $row = json_encode($row);
    $this->csv->insertOne([$row]);
  }

  public function writeAll($rows) : void {
    foreach ($rows as $row) {
      $this->writeOne($row);
    }
  }

}
