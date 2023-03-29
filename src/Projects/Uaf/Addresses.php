<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class Addresses {

  public function transforms(array $rows) : array {
    $rows = T\CiviCRM::lookup($rows, 'Contact', 'LGL Constituent ID', 'external_identifier', ['id']);
    $rows = T\Columns::renameColumns($rows, [
      'id' => 'contact_id',
      'Address Type' => 'location_type_id:label',
      'City' => 'city',
      'Postal Code/Zip' => 'postal_code',
      'Is Preferred' => 'is_primary',
    ]);
    $rows = T\Columns::deleteColumns($rows, ['LGL Constituent ID', 'Constituent Name', 'LGL Address ID', 'County', 'Seasonal From', 'Seasonal To', 'Is Valid']);
    $rows = T\Text::trim($rows, array_keys(reset($rows)));
    return $rows;
  }

}
