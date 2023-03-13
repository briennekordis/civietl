#!/usr/bin/php
<?php
namespace Civietl;

require __DIR__ . '/vendor/autoload.php';

// use Civietl\Cache\CacheService;
use Civietl\Reader\ReaderService;
use Civietl\Writer\WriterService;
use Civietl\Utils as U;

$cliArguments = U::ParseCli();
require_once $cliArguments['settings-file'];
// Perform CiviCRM bootstrap
// phpcs:ignore
eval(`cv --cwd=$webroot php:boot`);

$importSettings = U::StartFromStep($importSettings, $cliArguments['start-from']);
foreach ($importSettings as $stepName => $importSetting) {
  $time_start = microtime(TRUE);
  $importSetting += $importDefaults;
  $readerClassName = '\Civietl\Reader\\' . $importSetting['reader_type'];
  // $cacheClassName = '\Civietl\Cache\\' . $importSetting['cache_type'];
  $writerClassName = '\Civietl\Writer\\' . $importSetting['writer_type'];
  $clientClassName = '\Civietl\Projects\\' . $importSetting['project_name'] . "\\$stepName";

  $reader = new ReaderService(new $readerClassName($importSetting['readerOptions']));
  // $cache = new CacheService(new $cacheClassName($importSetting['data_primary_key']));
  $step = new $clientClassName();

  $rows = $reader->getRows();

  $rows = $step->transforms($rows);

  $writer = new WriterService(new $writerClassName($importSetting['writerOptions']));
  $writer->writeAll($rows);
  $time_end = microtime(TRUE);
  $executionTime = round($time_end - $time_start, 2);
  $timings = "$stepName: $executionTime seconds\n";
  echo $timings;
}
