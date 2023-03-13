<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class Contacts {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
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
    $rows = T\ValueTransforms::valueMapper($rows, 'prefix_id:label', \Civietl\Maps::PREFIX_MAP);
    // Create any missing prefixes in the option values table.
    $prefixes = T\RowFilters::getUniqueValues($rows, 'prefix_id:label');
    T\CiviCRM::createOptionValues('individual_prefix', $prefixes);
    // For testing - just show 5 rows.
    $rows = T\RowFilters::randomSample($rows, 5);
    return $rows;
  }

}
