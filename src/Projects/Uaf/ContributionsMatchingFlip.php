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
      'Vehicle Name',
    ]);

    // Filter out Contributions with Vehciles since they are all Third Party Giving Contributions.
    // Split rows into those with a Vehicle and those without.
    $rowsWithVehicle = T\RowFilters::filterBlanks($rows, 'Vehicle Name');
    $rowsWithNoVehicle = array_diff_key($rows, $rowsWithVehicle);
    // Only import those without a Vehicle (Third Party Giving Contribution).
    $rows = $rowsWithNoVehicle;

    // Connect the matching Contribution.
    $rows = T\CiviCRM::lookup($rows, 'Contribution', ['LGL Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'Additional_Contribution_Data.Matching_Contribution.id']);

    // Look up and return the id of the Parent Contribution
    $rows = T\CiviCRM::lookup($rows, 'Contribution', ['LGL Parent Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['id']);

    return $rows;
  }

}
