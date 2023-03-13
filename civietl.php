#!/usr/bin/php
<?php
namespace Civietl;

require __DIR__ . '/vendor/autoload.php';

use Civietl\Cache\CacheService;
use Civietl\Reader\ReaderService;
use Civietl\Writer\WriterService;
use Civietl\Utils as U;

$cliArguments = U::ParseCli();
require_once $cliArguments['settings-file'];
// Perform CiviCRM bootstrap
// phpcs:ignore
eval(`cv --cwd=$webroot php:boot`);

foreach ($importSettings as $stepName => $importSetting) {
  $importSetting += $importDefaults;
  $readerClassName = '\Civietl\Reader\\' . $importSetting['reader_type'];
  $cacheClassName = '\Civietl\Cache\\' . $importSetting['cache_type'];
  $writerClassName = '\Civietl\Writer\\' . $importSetting['writer_type'];
  $clientClassName = '\Civietl\Projects\\' . $importSetting['project_name'] . "\\$stepName";

  $readerOptions['file_path'] = $importSetting['data_path'];

  $reader = new ReaderService(new $readerClassName($readerOptions));
  $cache = new CacheService(new $cacheClassName($importSetting['data_primary_key']));
  $step = new $clientClassName();

  $reader->setPrimaryKeyColumn($importSetting['data_primary_key']);
  $rows = $reader->getRows();

  $rows = $step->transforms($rows);

  // For CSV output.
  $writerOptions['file_path'] = $importSetting['output_path'];
  // $writerOptions['column_names'] = array_keys(reset($rows));
  $writerOptions['entity'] = $importSetting['civi_primary_entity'];
  $writer = new WriterService(new $writerClassName($writerOptions));
  $writer->writeAll($rows);

}
