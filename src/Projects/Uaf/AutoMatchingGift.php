<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class AutoMatchingGift {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Remove columns that will not be imported.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
      'LGL Gift ID',
      'LGL Parent Gift ID',
      'Vehicle Name',
    ]);
    // Rename the columns that will be imported to match CiviCRM fields.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID',
    ]);

    // Look up the Contact Type of the above Contact
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['LGL Constituent ID' => 'external_identifier'], ['contact_type', 'id']);
    // Separate the rows in which the Contact is an Individual. These Contributions will not be imported by the civietl.
    $rowsWithIndividual = array_filter($rows, function($row) {
      return isset($row['contact_type']) && $row['contact_type'] === 'Individual';
    });
    $rowsWithOrganization = array_diff_key($rows, $rowsWithIndividual);
    $rows = $rowsWithOrganization;

    // Filter out Contributions with Vehciles since they are all Third Party Giving Contributions.
    // Split rows into those with a Vehicle and those without.
    $rowsWithVehicle = T\RowFilters::filterBlanks($rows, 'Vehicle Name');
    $rowsWithNoVehicle = array_diff_key($rows, $rowsWithVehicle);
    // Only import those without a Vehicle (Third Party Giving Contribution).
    if ($rowsWithVehicle) {
      $rowsWithVehicle = T\CiviCRM::lookup($rowsWithVehicle, 'Contact', ['contact_id' => 'id'], ['contact_sub_type']);
    }
    // // Separate the rows in which the Contact is a Third Part Giving Vehicle. These Contributions will not be imported by the civietl.
    $rowsWithThirdParty = array_filter($rowsWithVehicle, function($row) {
      return isset($row['contact_sub_type'][0]) && $row['contact_sub_type'][0] === 'Third Party Giving Vehicle';
    });
    $rows = $rowsWithNoVehicle;

    // Connect the matching Contribution, to assign the Contact of this Contribution as the Matching Gift Organization
    $rows = T\Columns::renameColumns($rows, ['id' => 'gift_details.matching_gift']);
    $rows = T\CiviCRM::lookup($rows, 'Contribution', ['LGL Parent Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['id']);
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['contact_external_identifier' => 'external_identifier'], ['id']);

    return $rows;
  }

}
