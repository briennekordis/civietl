<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class MatchingGiftOrganizations {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Remove columns that will not be imported.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
    ]);

    // Look up the Contact Type of the above Contact
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['LGL Constituent ID' => 'external_identifier'], ['contact_type', 'id']);
    // Separate the rows in which the Contact is an Individual. These Contributions will not be imported by the civietl.
    $rowsWithIndividual = array_filter($rows, function($row) {
      return isset($row['contact_type']) && $row['contact_type'] === 'Individual';
    });
    $rowsWithOrganization = array_diff_key($rows, $rowsWithIndividual);
    $rows = $rowsWithOrganization;
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id']);
    // Add a column with the name of the group for the automatchinggift extension.
    $rows = T\Columns::newColumnWithConstant($rows, 'group name', 'Matching Gift Organizations');
    // Look up the id of that group.
    $rows = T\CiviCRM::lookup($rows, 'Group', ['group name' => 'name'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'group_id']);
    // Only need the contact and group ids to make a GroupContact record.
    $rows = T\Columns::deleteAllColumnsExcept($rows, ['contact_id', 'group_id']);

    return $rows;
  }

}
