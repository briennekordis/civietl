<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class ContactSplit {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    $rows = T\Columns:: deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
      'Constituent Name',
      'Spouse Name',
      'Spouse Nick Name',
    ]);
    $rows = T\RowFilters::filterBlanks($rows, 'Spouse Name');
    // Rename some columns that are one-to-one with Civi.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Constituent ID' => 'external_identifier',
      'Spouse Nick Name' => 'nick_name',
    ]);
    // Split contacts into separate Contact records.
    $rows = T\Cleanup::splitContacts($rows, 'external_identifier');
    $rows = T\Columns::deleteColumns($rows, ['external_identifier']);
    $rows = T\Columns::renameColumns($rows, ['spouseExID' => 'external_identifier']);
    // Split the Spouse Name into separate strings.
    $rows = T\Cleanup::splitNames($rows, 'Spouse Name');
    //Split rows into those with last names and those without
    $rowsWithLastName = T\RowFilters::filterBlanks($rows, 'last_name');
    $rowsWithoutLastName = array_diff_key($rows, $rowsWithLastName);
    // If the Spouse is missing a last name, use the last name from the Constituent Name column.
    $rowsWithoutLastName = T\Cleanup::getLastName($rowsWithoutLastName, 'Constituent Name');
    // Merge the two types of rows back into one.
    $rows = $rowsWithLastName + $rowsWithoutLastName;

    // For testing - just show 5 rows.
    // $rows = T\RowFilters::randomSample($rows, 5);
    return $rows;
  }

}
