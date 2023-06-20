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
      'LGL Constituent ID',
      'LGL Gift ID',
      'LGL Parent Gift ID',
      'Gift Amount',
      'Vehicle Name',
    ]);

    // Rename the columns that will be imported to match CiviCRM fields.
    $rows = T\Columns::renameColumns($rows, [
      'Gift Amount' => 'amount',
      'Vehicle Name' => 'vehicle_name',
      'LGL Gift ID' => 'LGL_Gift_ID',
      'LGL Parent Gift ID' => 'LGL_Parent_Gift_ID',
    ]);

    // Look up the LGL Parent Gift ID. If it exsists in CiviCRM, perform the flip.
    $rows = T\CiviCRM::lookup($rows, 'Contribution', ['LGL_Parent_Gift_ID' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['id']);
    $rowsWithExistingParent = T\RowFilters::filterBlanks($rows, 'id');
    $rowsToNotImport = array_diff_key($rows, $rowsWithExistingParent);
    // If there are rows that will not be flipped because the parent contribution is not imported, write these to a CSV file.
    if ($rowsToNotImport) {
      $rowsToNotImport = T\Columns::deleteAllColumnsExcept($rowsToNotImport, ['LGL_Gift_ID', 'LGL_Parent_Gift_ID']);
      $noParentWriter = new \Civietl\Writer\CsvWriter(['file_path' => $GLOBALS['workroot'] . '/data/matching_gifts_flip_without_parent_not_imported.csv']);
      $noParentWriter->writeAll($rowsToNotImport);
    }
    // Remove the id field, since we are creating new Contributions.
    $rowsWithExistingParent = T\Columns::deleteColumns($rowsWithExistingParent, ['id']);
    // Continue the transformations with rows that have an existing parent contribution.
    $rows = $rowsWithExistingParent;

    // Look up and return the id of the Contact this Contribution is connected to.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['LGL Constituent ID' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id']);
    // Look up the Contact Type of the above Contact
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['contact_id' => 'id'], ['contact_type']);
    // Separate the rows in which the Contact is an Individual. These Contributions will not be imported by the civietl.
    $rowsWithIndividual = array_filter($rows, function($row) {
      return isset($row['contact_type']) && $row['contact_type'] === 'Individual';
    });
    if ($rowsWithIndividual) {
      $rowsWithIndividual = T\Columns::deleteAllColumnsExcept($rowsWithIndividual, ['LGL_Gift_ID']);
      $individualWriter = new \Civietl\Writer\CsvWriter(['file_path' => $GLOBALS['workroot'] . '/data/individual_matching_gifts_flip_not_imported.csv']);
      $individualWriter->writeAll($rowsWithIndividual);
    }
    $rowsWithOrganization = array_diff_key($rows, $rowsWithIndividual);
    $rows = $rowsWithOrganization;

    // Assign the Contact of this Contribution as the Matching Gift Organization.
    $rows = T\Columns::renameColumns($rows, ['contact_id' => 'gift_details.matching_gift']);

    // Connect the matching Contribution, to apply the Soft Credit.
    $rows = T\CiviCRM::lookup($rows, 'Contribution', ['LGL_Parent_Gift_ID' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['contact_id']);
    $rows = T\CiviCRM::lookup($rows, 'Contribution', ['LGL_Gift_ID' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contribution_id']);
    $rows = T\Columns::deleteAllColumnsExcept($rows, ['contribution_id', 'contact_id', 'amount']);
    return $rows;
  }

}
