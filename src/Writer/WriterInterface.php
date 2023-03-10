<?php
namespace Civietl\Writer;

interface WriterInterface {

  public function __construct($options);

  public function writeOne($row) : void;

  public function writeAll($rows) : void;

}
