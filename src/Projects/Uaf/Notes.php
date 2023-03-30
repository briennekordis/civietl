<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class Notes {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Delete unused columns. Not necessary but easier (and marginally faster) to work with.
    $rows = T\Columns::deleteColumns($rows, ['LGL Note ID', 'Constituent Name', 'Note Type', 'Note Creator']);
    // Rename some columns that are one-to-one with Civi.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Constituent ID' => 'external_identifier',
      'Note Text' => 'note',
      'Note Date' => 'note_date',
    ]);
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['external_identifier' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'entity_id']);
    $rows = T\Columns::newColumnWithConstant($rows, 'entity_table', 'civicrm_contact');
    $rows = T\Columns::copyColumn($rows, 'note_date', 'modified_date');
    return $rows;
  }

}
