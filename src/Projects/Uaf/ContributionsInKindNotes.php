<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class ContributionsInKindNotes {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Delete unused columns. Not necessary but easier (and marginally faster) to work with.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Gift ID',
      'Gift note',
      'Gift date',
    ]);
    // Rename some columns that are one-to-one with Civi.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Gift ID' => 'external_identifier',
      'Gift note' => 'note',
      'Gift date' => 'note_date',
    ]);
    // Get random sampe of rows to test. (REMOVE FOR FINAL VERSION)
    $rows = T\RowFilters::randomSample($rows, 5);
    // $rows = T\CiviCRM::lookup($rows, 'Contact', ['external_identifier' => 'external_identifier'], ['id']);
    // $rows = T\Columns::renameColumns($rows, ['id' => 'entity_id']);
    $rows = T\Columns::newColumnWithConstant($rows, 'entity_table', 'civicrm_contribution');
    $rows = T\Columns::copyColumn($rows, 'note_date', 'modified_date');
    $rows = T\Text::replace($rows, 'note', 'Currency: USD.', '', FALSE);
    $rows = T\Text::trim($rows, ['note']);
    return $rows;
  }

}
