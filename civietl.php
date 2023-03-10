<?php

use Civietl\Cache\CacheService;

$primaryKey = '';
$cache = new CacheService(new \Civietl\Cache\ArrayCache($primaryKey));
