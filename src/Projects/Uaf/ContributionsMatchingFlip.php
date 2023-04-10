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
    ]);

    // Look up and return the id of the Parent Contribution
    $rows = T\CiviCRM::lookup($rows, 'Contirbution', ['LGL Parent Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['id']);

    // Connect the matching Contribution.
    $rows = T\CiviCRM::lookup($rows, 'Contirbution', ['LGL Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'Additional_Contribution_Data.Matching_Contribution.id']);

    return $rows;
  }

}
