<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class ContributionsMatchingFlip {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Remove columns that will not be imported.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Gift ID',
      'LGL Parent Gift ID',
      'Gift Amount',
      'Vehicle Name',
    ]);

    // Rename the columns that will be imported to match CiviCRM fields.
    $rows = T\Columns::renameColumns($rows, [
      'Gift Amount' => 'total_amount',
    ]);

    // Filter out Contributions with Vehciles since they are all Third Party Giving Contributions.
    // Split rows into those with a Vehicle and those without.
    $rowsWithVehicle = T\RowFilters::filterBlanks($rows, 'Vehicle Name');
    $rowsWithNoVehicle = array_diff_key($rows, $rowsWithVehicle);
    // Only import those without a Vehicle (Third Party Giving Contribution).
    $rows = $rowsWithNoVehicle;

    // Connect the matching Contribution, to apply the Soft Credit.
    $rows = T\CiviCRM::lookup($rows, 'Contribution', ['LGL Parent Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['id', 'contact_id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'matching_gift.matching_gift_contribution']);
    $rows = T\Columns::renameColumns($rows, ['contact_id' => 'matching_gift.matching_gift_contribution']);

    return $rows;
  }

}
