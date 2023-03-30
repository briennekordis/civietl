<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class Groups {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    $rows = T\Columns::deleteAllColumnsExcept($rows, ['LGL Constituent ID', 'Groups']);
    $rows = T\RowFilters::filterBlanks($rows, 'Groups');
    $rows = T\Transform::splitFieldToRows($rows, 'Groups', 'Group', ';');
    $rows = T\Text::trim($rows, ['Group']);
    // Create any missing website types in the option values table.
    $groups = T\RowFilters::getUniqueValues($rows, 'Group');
    T\CiviCRM::createGroups($groups);
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['LGL Constituent ID' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id']);
    $rows = T\CiviCRM::lookup($rows, 'Group', ['Group' => 'title'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'group_id']);
    $rows = T\Columns::deleteAllColumnsExcept($rows, ['group_id', 'contact_id']);
    return $rows;
  }

}
