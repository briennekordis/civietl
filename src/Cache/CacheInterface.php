<?php
namespace Civietl\Cache;

interface CacheInterface {

  public function __construct($primaryKey);

  public function addRow(array $row) : string;

  public function clearCache() : void;

  public function getRow($primaryKey) : array;

  public function getData() : array;

}
