<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class Campaigns {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Remove columns that will not be imported.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'Name',
      'Description',
      'Goal',
      'Start Date',
      'End Date',
      'Is Active?',
    ]);
    // Rename the columns that will be imported to match CiviCRM fields.
    $rows = T\Columns::renameColumns($rows, [
      'Name' => 'title',
      'Description' => 'description',
      'Goal' => 'goal_revenue',
      'Start Date' => 'start_date',
      'End Date' => 'end_date',
      'Is Active?' => 'is_active',
    ]);
    return $rows;
  }

}
