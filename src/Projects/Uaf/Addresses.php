<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class Addresses {

  public function transforms(array $rows) : array {
    // Get Contact ID.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['LGL Constituent ID' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, [
      'id' => 'contact_id',
      'Address Type' => 'location_type_id:label',
      'City' => 'city',
      'Postal Code/ZIP' => 'postal_code',
      'Is Preferred?' => 'is_primary',
    ]);
    // Split Address into two fields when there's a carriage return.
    $rows = T\Transform::splitFieldToFields($rows, 'Street', "\n");
    $rows = T\Columns::renameColumns($rows, [
      'Street_0' => 'street_address',
      'Street_1' => 'supplemental_address_1',
      'Street_2' => 'supplemental_address_2',
      'Street_3' => 'supplemental_address_3',
    ]);
    $rows = T\Columns::deleteColumns($rows, ['LGL Constituent ID', 'Constituent Name', 'LGL Address ID', 'County', 'Seasonal from', 'Seasonal to', 'Is Valid?', 'Street']);
    // Trim every field.
    $rows = T\Text::trim($rows, array_keys(reset($rows)));
    // Clean up and map countries. First the ones that are special to this data, then all the rest.
    $rows = T\ValueTransforms::valueMapper($rows, 'Country', \Civietl\Maps::COUNTRY_MAP);
    $rows = T\ValueTransforms::valueMapper($rows, 'Country', self::UAF_COUNTRY_MAP);
    // Set a default country (necessary for doing state cleanups)
    $rows = T\ValueTransforms::valueMapper($rows, 'Country', ['' => 'United States']);
    // Do lookups by ISO code and name separately.
    $rowsWithISOCode = array_filter($rows, function($row) {
      return strlen($row['Country']) === 2;
    });
    $rowsWithISOCode = T\CiviCRM::lookup($rowsWithISOCode, 'Country', ['Country' => 'iso_code'], ['id']);
    $rowsWithISOCode = T\Columns::deleteColumns($rowsWithISOCode, ['iso_code']);
    $rowsWithName = array_diff_key($rows, $rowsWithISOCode);
    $rowsWithName = T\CiviCRM::lookup($rowsWithName, 'Country', ['Country' => 'name'], ['id']);
    // rejoin the rows.
    $rows = $rowsWithISOCode + $rowsWithName;
    $rows = T\Columns::renameColumns($rows, ['id' => 'country_id']);

    // Lookup state_province by abbreviation.
    $rows = T\CiviCRM::lookup($rows, 'StateProvince', ['State' => 'name', 'country_id' => 'country_id'], ['id'], FALSE);
    $completedLookupRows = array_filter($rows, function($row) {
      return $row['id'] || !$row['State'];
    });
    // Filter the list to records that weren't yet matched but have a state value in case they're abbreviations.
    $rowsWithPossibleAbbreviations = array_diff_key($rows, $completedLookupRows);
    // Drop the 'id' field so we can fill it anew.
    $rowsWithPossibleAbbreviations = T\Columns::deleteColumns($rowsWithPossibleAbbreviations, ['id']);
    $rowsWithPossibleAbbreviations = T\CiviCRM::lookup($rowsWithPossibleAbbreviations, 'StateProvince', ['State' => 'abbreviation', 'country_id' => 'country_id'], ['id']);

    // rejoin.
    $rows = $rowsWithPossibleAbbreviations + $completedLookupRows;
    $rows = T\Columns::renameColumns($rows, ['id' => 'state_province_id']);
    return $rows;
  }

  const UAF_COUNTRY_MAP = [
    '10023-5565' => '',
    'S. America' => '',
    'B- 1049 Brussels' => 'Belgium',
    'Harare, Zimbabwe' => 'Zimbabwe',
    '10032' => 'United States',
    '94559' => 'United States',
    '91436-4487' => 'United States',
    '70115' => 'United States',
    'Czechia' => 'Czech Republic',
  ];

}
