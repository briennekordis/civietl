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
  public static function lookup(array $rows, string $entity, string $columnName, array $lookupFields, array $returnFields, string $defaultValue = '') : array {
    // Get all the lookup data in one query, much faster than one query per row.
    foreach ($lookupFields as $lookupField) {
      $where[] = [$lookupField, 'IS NOT NULL'];
    }
    $result = (array) civicrm_api4($entity, 'get', [
      'select' => array_merge($lookupFields, $returnFields),
      'where' => $where,
      'checkPermissions' => FALSE,
    ]);
    // Reindex the cache data for easiest lookup speed.
    $lookupKeys = array_column($result, implode("\x01", $lookupFields));
    array_walk($lookupKeys, function(&$lookupKey) {
      $lookupKey = strtoupper($lookupKey);
    });
    $lookupData = array_combine($lookupKeys, $result);
    // We needed the lookupField in the original result, but drop it if we're not supposed to return it, otherwise we'll duplicate that field.
    $columnsToDelete = [];
    if (!in_array($lookupField, $returnFields)) {
      $columnsToDelete[] = $lookupField;
    }
    if (!in_array('id', $returnFields)) {
      $columnsToDelete[] = 'id';
    }
    if ($columnsToDelete) {
      $lookupData = Columns::deleteColumns($lookupData, $columnsToDelete);
    }
    // For when the lookup value is blank.
    $blankLookup = array_fill_keys($returnFields, $defaultValue);

    foreach ($rows as &$row) {
      if ($row[$columnName]) {
        if (!$lookupData[strtoupper($row[$columnName])]) {
          Logging::log("Invalid $columnName lookup: $row[$columnName] . Row: " . implode(', ', $row));
        }
        $row += $lookupData[strtoupper($row[$columnName])] ?? [];
      }
      else {
        $row += $blankLookup;
      }
    }
    return $rows;
  }

}
