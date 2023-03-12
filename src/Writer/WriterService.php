<?php
namespace Civietl\Writer;

class WriterService {
  private $writer;

  public function __construct(WriterInterface $writer) {
    $this->writer = $writer;
  }

  public function writeOne($row) : array {
    return $this->writer->writeOne($row);
  }

  public function writeAll($rows) : array {
    return $this->writer->writeAll($rows);
  }

}
