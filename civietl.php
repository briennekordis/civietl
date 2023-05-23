#!/usr/bin/php
<?php
namespace Civietl;

require __DIR__ . '/vendor/autoload.php';

// use Civietl\Cache\CacheService;
use Civietl\Reader\ReaderService;
use Civietl\Writer\WriterService;
use Civietl\Utils as U;
use DateTime;
use DateTimeZone;

$GLOBALS['newEtlStarted'] = TRUE;
$cliArguments = U::parseCli();
require_once $cliArguments['settings-file'];
// Perform CiviCRM bootstrap
// phpcs:ignore
eval(`cv --cwd=$webroot php:boot`);

$importSettings = U::filterSteps($importSettings, $cliArguments);
foreach ($importSettings as $stepName => $importSetting) {
  $time_start = microtime(TRUE);
  $timeZone = new DateTimeZone('America/New_York');
  $humanTimeStart = (new DateTime('now', $timeZone))->format('H:i:s');
  $importSetting += $importDefaults;
  $readerClassName = '\Civietl\Reader\\' . $importSetting['reader_type'];
  // $cacheClassName = '\Civietl\Cache\\' . $importSetting['cache_type'];
  $writerClassName = '\Civietl\Writer\\' . $importSetting['writer_type'];
  $clientClassName = '\Civietl\Projects\\' . $importSetting['project_name'] . "\\$stepName";

  $reader = new ReaderService(new $readerClassName($importSetting['readerOptions']));
  // $cache = new CacheService(new $cacheClassName($importSetting['data_primary_key']));
  $step = new $clientClassName();
  $rows = [];
  if ($reader) {
    $rows = $reader->getRows();
    $rows = $step->transforms($rows);
  }

  $writer = new WriterService(new $writerClassName($importSetting['writerOptions']));
  $writer->writeAll($rows);
  $time_end = microtime(TRUE);
  $humanTimeEnd = (new DateTime('now', $timeZone))->format('H:i:s');
  $executionTime = round($time_end - $time_start, 2);
  $timings = "$stepName: $executionTime seconds from $humanTimeStart - $humanTimeEnd\n";
  echo $timings;
}