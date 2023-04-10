<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class SoftCreditsInMemory {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Delete unused columns. Not necessary but easier (and marginally faster) to work with.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
      'LGL Parent Gift ID',
      'Gift Amount',
    ]);
    // Rename some columns that are one-to-one with Civi.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Constituent ID' => 'contact_external_identifier',
      'LGL Parent Gift ID' => 'contribution_external_identifier',
      'Gift Amount' => 'amount',
    ]);
    // // Get random sampe of rows to test. (REMOVE FOR FINAL VERSION)
    // $rows = T\RowFilters::randomSample($rows, 5);
    // Look up and return the external_identifier of the Contribution.
    $rows = T\CiviCRM::lookup($rows, 'Contribution', ['contribution_external_identifier' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contribution_id']);
    // Look up and return the external_identifier of the Contact to whom the soft credit is assigned.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['contact_external_identifier' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id']);
    // Create a new column with constant of 'In Honor of' for the soft credit type.
    $rows = T\Columns::newColumnWithConstant($rows, 'soft_credit_type_id:label', 'In Memory of');
    return $rows;
  }

}
