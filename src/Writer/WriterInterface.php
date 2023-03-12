<?php
namespace Civietl\Writer;

interface WriterInterface {

  public function __construct($options);

  public function writeOne($row) : array;

  public function writeAll($rows) : array;

}
