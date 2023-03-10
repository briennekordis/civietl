<?php
namespace Civietl\Cache;

class cacheService {
  private $cache;

  public function __construct(CacheInterface $cache) {
    $this->cache = $cache;
  }

  public function addRow(array $row) : string {
    return $this->cache->addRow($row);
  }

  public function clearCache() : void {
    $this->cache->clearCache();
  }

  public function getRow($id) : array {
    return $this->cache->getRow($id);
  }

  public function getData() : array {
    return $this->cache->getData();
  }

}
