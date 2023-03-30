<?php
namespace Civietl\Transforms;

use Civietl\Logging;

class CiviCRM {

  /**
   * Create missing option values - e.g. prefix/suffixes, website types, etc.
   * @var label
   *   array of strings
   */
  public static function createOptionValues(string $optionGroupName, array $labels) : void {
    // FIXME: This can be done in less/faster code by using "Save", see "createLocationTypes" below.
    $existingValues = \Civi\Api4\OptionValue::get(FALSE)
      ->addSelect('label')
      ->addWhere('option_group_id:name', '=', $optionGroupName)
      ->execute()
      ->indexBy('label');
    foreach ($labels as $label) {
      if (!$label || ($existingValues[$label] ?? FALSE)) {
        continue;
      }
      \Civi\Api4\OptionValue::create(FALSE)
        ->addValue('option_group_id.name', $optionGroupName)
        ->addValue('label', $label)
        ->execute();
    }
  }

  /**
   * Create missing location types.
   */
  public static function createLocationTypes(array $locationTypes) : void {
    $api = \Civi\Api4\LocationType::save(FALSE)->setMatch(['name']);
    foreach ($locationTypes as $locationType) {
      $api->addRecord([
        'name' => $locationType,
        'label' => $locationType,
      ]);
    }
    $api->execute();
  }

  /**
   * Create missing groups.
   */
  public static function createGroups(array $groups) : void {
    $api = \Civi\Api4\Group::save(FALSE)->setMatch(['title']);
    foreach ($groups as $group) {
      $api->addRecord([
        'title' => $group,
      ]);
    }
    $api->execute();
  }

  /**
   * Return one or more fields from a record based on an existing value(s).
   * E.g. from external_identifier, return the contact_id.
   */
  public static function lookup(array $rows, string $entity, array $lookupFields, array $returnFields, bool $logErrors = TRUE) : array {
    $logger = new Logging($entity);
    $logHeadersWritten = FALSE;
    $lookupData = self::buildLookupTable($entity, $lookupFields, $returnFields);
    // For when the lookup value is blank.
    $noLookupColumns = array_fill_keys($returnFields, '');

    foreach ($rows as &$row) {
      // If we don't have values for all columns, don't do a lookup, assign the default value.
      $match = TRUE;
      $compositeRowKey = [];
      foreach ($lookupFields as $columnName => $dontcare) {
        $match = $match && (bool) $row[$columnName];
        $compositeRowKey[] = $row[$columnName];
      }
      $compositeRowKey = implode("\x01", $compositeRowKey);
      $ucCompositeRowKey = strtoupper($compositeRowKey);
      if ($match) {
        if ($logErrors && !isset($lookupData[$ucCompositeRowKey])) {
          if (!$logHeadersWritten) {
            $logHeadersWritten = TRUE;
            $csv = Logging::arrayToCsv(array_keys($row));
            $logger->log('New Headers, ' . $csv);
          }
          $csv = Logging::arrayToCsv($row);
          $logger->log("Invalid $compositeRowKey lookup failed on: $columnName, " . $csv);
        }
      }
      // Create the new columns even if we don't fill them.
      $row += $lookupData[$ucCompositeRowKey] ?? $noLookupColumns;
    }

    // Delete new columns that aren't in the $returnFields.
    $columnsToDelete = [];
    foreach ($lookupFields as $lookupField) {
      if (!in_array($lookupField, $returnFields)) {
        $columnsToDelete[] = $lookupField;
      }
    }
    if (!in_array('id', $returnFields)) {
      $columnsToDelete[] = 'id';
    }
    if ($columnsToDelete) {
      $rows = Columns::deleteColumns($rows, $columnsToDelete);
    }

    return $rows;
  }

  /**
   * Query CiviCRM and cache an array of all the values we might return.
   */
  private static function buildLookupTable(string $entity, array $lookupFields, array $returnFields) : array {
    // Get all the lookup data in one query, much faster than one query per row.
    foreach ($lookupFields as $lookupField) {
      $where[] = [$lookupField, 'IS NOT NULL'];
    }
    $results = (array) \civicrm_api4($entity, 'get', [
      'select' => array_merge($lookupFields, $returnFields),
      'where' => $where,
      'checkPermissions' => FALSE,
    ]);
    // Reindex the cache data for easiest lookup speed. Composite keys when there are multiple lookup fields.
    // This is a fancier array_column + array_combine.
    $lookupData = [];
    foreach ($results as $result) {
      $compositeKey = [];
      foreach ($lookupFields as $lookupField) {
        $compositeKey[] = $result[$lookupField];
      }
      $compositeKey = strtoupper(implode("\x01", $compositeKey));
      $lookupData[$compositeKey] = $result;
    }
    return $lookupData;
  }

}
