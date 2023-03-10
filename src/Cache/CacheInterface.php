<?php
namespace Civietl\Cache;

interface CacheInterface {

  public function __construct($primaryKeyColumn);

  public function addRow(array $row) : string;

  public function clearCache() : void;

  public function getRow($id) : array;

  public function getData() : array;

}
