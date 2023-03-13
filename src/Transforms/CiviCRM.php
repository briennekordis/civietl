<?php
namespace Civietl\Transforms;

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
   * Return one or more fields from a record based on an existing value.
   * E.g. from external_identifier, return the contact_id.
   */
  public static function lookup(array $rows, string $entity, string $lookupField, array $returnFields) : array {
    // Get all the lookup data in one query, much faster than one query per row.
    $lookupData = (array) civicrm_api4($entity, 'get', [
      'select' => [$lookupField] + $returnFields,
      'where' => [[$lookupField, 'IS NOT NULL']],
      'checkPermissions' => FALSE,
    ]);
    // Re-index by lookup field.
    $lookupData = array_combine(array_column($lookupData, $lookupField), $lookupData);
    foreach ($rows as &$row) {
      $row += $lookupData[$row[$lookupField]];
    }
    return $rows;
  }

}
