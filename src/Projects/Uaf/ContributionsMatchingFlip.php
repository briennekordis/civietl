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
      'Gift Amount' => 'total_amount',
    ]);

    // Filter out Contributions with Vehciles since they are all Third Party Giving Contributions.
    // Split rows into those with a Vehicle and those without.
    $rowsWithVehicle = T\RowFilters::filterBlanks($rows, 'Vehicle Name');
    $rowsWithNoVehicle = array_diff_key($rows, $rowsWithVehicle);
    // Only import those without a Vehicle (Third Party Giving Contribution).
    $rowsWithVehicle = T\CiviCRM::lookup($rowsWithVehicle, 'Contact', ['contact_id' => 'id'], ['contact_sub_type']);
    // // Separate the rows in which the Contact is a Third Part Giving Vehicle. These Contributions will not be imported by the civietl.
    $rowsWithThirdParty = array_filter($rowsWithVehicle, function($row) {
      return isset($row['contact_sub_type'][0]) && $row['contact_sub_type'][0] === 'Third Party Giving Vehicle';
    });
    $rowsWithoutThirdParty = array_diff_key($rows, $rowsWithThirdParty);
    $rows = $rowsWithNoVehicle + $rowsWithoutThirdParty;

    // Look up and return the id of the Contact this Contribution is connected to.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['LGL Constituent ID' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id']);
    // Look up the Contact Type of the above Contact
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['contact_id' => 'id'], ['contact_type']);
    // Separate the rows in which the Contact is an Individual. These Contributions will not be imported by the civietl.
    $rowsWithIndividual = array_filter($rows, function($row) {
      return isset($row['contact_type']) && $row['contact_type'] === 'Individual';
    });
    $rowsWithOrganization = array_diff_key($rows, $rowsWithIndividual);
    $rows = $rowsWithOrganization;

    // Assign the Contact of this Contribution as the Matching Gift Organization.
    $rows = T\Columns::renameColumns($rows, ['contact_id' => 'gift_details.matching_gift']);

    // Connect the matching Contribution, to apply the Soft Credit.
    $rows = T\CiviCRM::lookup($rows, 'Contribution', ['LGL Parent Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['id', 'contact_id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contribution_id']);

    return $rows;
  }

}
