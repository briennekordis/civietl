#!/usr/bin/php
<?php
namespace Civietl;

require __DIR__ . '/vendor/autoload.php';

use \Civietl\Cache\CacheService;
use \Civietl\Reader\ReaderService;
use \Civietl\Writer\WriterService;
use \Civietl\Transforms as T;

// FIXME: Move this into a Utils class.
$shortopts = '';
$longopts = ['settings-file:'];
$cliArguments = getopt($shortopts, $longopts);
checkRequired($cliArguments);

require_once $cliArguments['settings-file'];
// Perform CiviCRM bootstrap
// phpcs:ignore
eval(`cv --cwd=$webroot php:boot`);

foreach ($importSettings as $importSetting) {
  $readerClassName = '\Civietl\Reader\\' . $importSetting['reader_type'];
  $cacheClassName = '\Civietl\Cache\\' . $importSetting['cache_type'];
  $writerClassName = '\Civietl\Writer\\' . $importSetting['writer_type'];

  $readerOptions['file_path'] = $importSetting['data_path'];

  $reader = new ReaderService(new $readerClassName($readerOptions));
  $cache = new CacheService(new $cacheClassName($importSetting['data_primary_key']));

  $reader->setPrimaryKeyColumn($importSetting['data_primary_key']);
  $rows = $reader->getRows();
  // $header = $reader->getColumnNames();

  // FIXME: Transforms here. Move them...I dunno where. settings.php?
  // FIXME: This should be using dependency injection maybe?
  // Rename some columns that are one-to-one with Civi.
  $rows = T\Columns::renameColumns($rows, [
    'LGL Constituent ID' => 'external_identifier',
    'First Name' => 'first_name',
    'Middle Name' => 'middle_name',
    'Last Name' => 'last_name',
    'Nick Name' => 'nick_name',
    'Constituent Type' => 'contact_type',
    'Prefix' => 'prefix_id:label',
    'Suffix' => 'suffix_id:label',
  ]);
  $rows = T\ValueTransforms::valueMapper($rows, 'prefix_id:label', Maps::PREFIX_MAP);
  // Create any missing prefixes in the option values table.
  $prefixes = T\RowFilters::getUniqueValues($rows, 'prefix_id:label');
  T\CiviCRM::createOptionValues('individual_prefix', $prefixes);
  // For testing - just show 5 rows.
  $rows = T\RowFilters::randomSample($rows, 5);

  // For CSV output.
  $writerOptions['file_path'] = $importSetting['output_path'];
  // $writerOptions['column_names'] = array_keys(reset($rows));
  $writerOptions['entity'] = $importSetting['civi_primary_entity'];
  $writer = new WriterService(new $writerClassName($writerOptions));
  $writer->writeAll($rows);
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
