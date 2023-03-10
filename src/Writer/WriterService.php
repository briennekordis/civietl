<?php
namespace Civietl\Writer;

class WriterService {
  private $writer;

  public function __construct(WriterInterface $writer) {
    $this->writer = $writer;
  }

  public function writeOne($row) : void {
    $this->writer->writeOne($row);
  }

  public function writeAll($rows) : void {
    $this->writer->writeAll($rows);
  }

}
