<?php
namespace Civietl\Writer;

use League\Csv\Writer;

class JsonWriter implements WriterInterface {
  private Writer $csv;

  public function __construct($options) {
    $this->csv = Writer::createFromPath($options['file_path'], 'w');
  }

  public function writeOne($row) : array {
    $row = json_encode($row);
    $this->csv->insertOne([$row]);
    return [];
  }

  public function writeAll($rows) : array {
    foreach ($rows as $row) {
      $this->writeOne($row);
    }
    return [];
  }

}
