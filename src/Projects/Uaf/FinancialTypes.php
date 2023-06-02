<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class FinancialTypes {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Remove columns that will not be imported.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'Name',
      'Description',
      'Is Active?',
    ]);
    // Rename the columns that will be imported to match CiviCRM fields.
    $rows = T\Columns::renameColumns($rows, [
      'Name' => 'name',
      'Description' => 'description',
      'Is Active?' => 'is_active',
    ]);
    // Remap TRUE to 1 and FALSE to 0 for is_active.
    $rows = T\ValueTransforms::valueMapper($rows, 'is_active', ['false' => 0, 'true' => 1]);
    return $rows;
  }

}
