<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class Appeals {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Remove columns that will not be imported.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Appeal ID',
      'LGL Campaign ID',
      'Name',
      'Description',
      'Goal',
      'Date',
      'Is Active?',
    ]);
    // Rename the columns that will be imported to match CiviCRM fields.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Appeal ID' => 'external_identifier',
      'LGL Campaign ID' => 'campaign_external_identifier',
      'Name' => 'title',
      'Description' => 'description',
      'Goal' => 'goal_revenue',
      'Date' => 'start_date',
      'Is Active?' => 'is_active',
    ]);
    // Remap true to 1 and false to 0 for is_active.
    $rows = T\ValueTransforms::valueMapper($rows, 'is_active', ['false' => 0, 'true' => 1]);
    // Look up the LGL Campaign Id and then rename it as the parent_id.
    $rows = T\CiviCRM::lookup($rows, 'Campaign', ['campaign_external_identifier' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'parent_id']);

    return $rows;
  }

}
