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
      'Spouse Name',
      'Spouse Nick Name',
    ]);
    // Rename some columns that are one-to-one with Civi.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Constituent ID' => 'external_identifier',
      'Spouse Nick Name' => 'nick_name',
    ]);
    // Split contacts into separate Contact records.
    $rows = T\Cleanup::splitContacts($rows, 'external_identifier');
    $rows = T\Columns::deleteColumns($rows, ['external_identifier']);
    $rows = T\Columns::renameColumns($rows, [
      'spouseExID' => 'external_identifier',
    ]);
    // Split the Spouse Name into separate strings.
    $rows = T\Cleanup::splitNames($rows, 'Spouse Name');
    // For testing - just show 5 rows.
    // $rows = T\RowFilters::randomSample($rows, 5);
    return $rows;
  }

}
