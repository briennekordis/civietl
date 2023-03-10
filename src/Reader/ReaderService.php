<?php
namespace Civietl\Cache;

class CacheService {
  private $cacheService;

  public function __construct(CacheService $cacheService) {
    $this->cacheService = $cacheService;
  }

  public function addRow(array $row) : string {
    return $this->cacheService->addRow($row);
  }

  public function clearCache() : void {
    $this->cacheService->clearCache();
  }

  public function getRow($primaryKey) : array {
    return $this->cacheService->getRow($primaryKey);
  }

  public function getData() : array {
    return $this->cacheService->getData();
  }

}
