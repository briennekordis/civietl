#!/usr/bin/php
<?php
require __DIR__ . '/vendor/autoload.php';

use Civietl\Cache\CacheService;
use Civietl\Reader\ReaderService;
use Civietl\Writer\WriterService;

// FIXME: Move this into a Utils class.
$shortopts = '';
$longopts = ['settings-file:'];
$cliArguments = getopt($shortopts, $longopts);
checkRequired($cliArguments);

require_once $cliArguments['settings-file'];


foreach ($importSettings as $importSetting) {
  $readerClassName = '\Civietl\Reader\\' . $importSetting['reader_type'];
  $cacheClassName = '\Civietl\Cache\\' . $importSetting['cache_type'];
  $writerClassName = '\Civietl\Writer\\' . $importSetting['writer_type'];

  $readerOptions['file_path'] = $importSetting['data_path'];

  $reader = new ReaderService(new $readerClassName($readerOptions));
  $cache = new CacheService(new $cacheClassName($importSetting['data_primary_key']));


  $reader->setPrimaryKeyColumn($importSetting['data_primary_key']);
  $dataWip = $reader->getRows();

  $header = $reader->getColumnNames();

  $writerOptions['file_path'] = $importSetting['output_path'];
  $writerOptions['column_names'] = $header;
  // FIXME: Transforms here. Move them...I dunno where. settings.php?
  $writer = new WriterService(new $writerClassName($writerOptions));
  $writer->writeAll($dataWip);
}

/**
 * FIXME: Move this into a Utils class.
 */
function checkRequired($options) {
  $requiredArguments = ['settings-file'];
  $arguments = array_keys($options);
  $missing = NULL;
  foreach ($requiredArguments as $required) {
    if (!in_array($required, $arguments)) {
      $missing .= " $required";
    }
  }
  if (isset($missing)) {
    echo "You are missing the following required arguments:$missing";
    exit(3);
  }
}
