<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class ContributionsMatching {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Remove columns that will not be imported.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
      'LGL Gift ID',
      'Gift Type',
      'LGL Campaign ID',
      'Fund',
      'Gift Category',
      'Gift Amount',
      'Deductible amount',
      'Gift date',
      'Vehicle Name',
    ]);
    // Rename the columns that will be imported to match CiviCRM fields.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Constituent ID' => 'contact_external_identifier',
      'LGL Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID',
      'Gift Type' => 'Additional_Contribution_Data.Contribution_type:label',
      'LGL Campaign ID' => 'campaign_external_identifier',
      'Fund' => 'financial_type_id:label',
      'Gift Category' => 'Additional_Contribution_Data.Contribution_type:label',
      'Gift Amount' => 'total_amount',
      'Gift date' => 'receive_date',
      'Vehicle Name' => 'vehicle_name',
    ]);
    // Get random sampe of rows to test. (REMOVE FOR FINAL VERSION)
    // $rows = T\RowFilters::randomSample($rows, 50);

    // Cleanup
    // Add a column that gives these Contributions a payment method.
    $rows = T\Columns::newColumnWithConstant($rows, 'payment_instrument_id:label', 'Verify in LGL');
    T\CiviCRM::createOptionValues('payment_instrument', ['Verify in LGL']);
    // Add a column that gives these Contributions a 'Completed' status.
    $rows = T\Columns::newColumnWithConstant($rows, 'contribution_status_id:label', 'Completed');
    // Remap 'Donation' to 'Direct Donation' for Contribution type.
    $rows = T\ValueTransforms::valueMapper($rows, 'Additional_Contribution_Data.Contribution_type:label', ['Donation' => 'Direct Donation']);
    // Remap 'Matching' to 'Matching Gift' for Contribution type.
    $rows = T\ValueTransforms::valueMapper($rows, 'Additional_Contribution_Data.Contribution_type:label', ['Matching' => 'Matching Gift']);
    // Remap null to '' for Contribution type.
    $rowsWithCategory = T\RowFilters::filterBlanks($rows, 'Additional_Contribution_Data.Contribution_type:label');
    $rowsWithNoCategory = array_diff_key($rows, $rowsWithCategory);
    if ($rowsWithNoCategory) {
      $rowsWithNoCategory = T\ValueTransforms::valueMapper($rows, 'Additional_Contribution_Data.Contribution_type:label', ['' => 'Uncategorized']);
    }
    // Merge the two types of rows back into one.
    $rows = $rowsWithCategory + $rowsWithNoCategory;
    // Get the value needed for 'non_deductible_amount' from 'Deductible amount'.
    array_walk($rows, function(&$row) {
      $row['non_deductible_amount'] = $row['total_amount'] - $row['Deductible amount'];
    });

    // Contacts
    // Look up and return the id of the Contact this Contribution is connected to.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['contact_external_identifier' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id']);
    // Look up and return the id of the Vehicle. In the gifts_matching_gifts.csv, all Vehicles are Third Parties.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['vehicle_name' => 'organization_name'], ['id']);
    // Assign the Vehicle to the Third Party Giving Vehicle custom field.
    $rows = T\Columns::renameColumns($rows, ['id' => 'Additional_Contribution_Data.Third_Party_Giving_Vehicle']);

    // Campaigns
    // Remap 0 to an empty string for the camapaign and/or appeal external ids.
    $rows = T\ValueTransforms::valueMapper($rows, 'campaign_external_identifier', ['0' => '', '497' => '']);
    // Look up and return the id of the Campaign this Contribution is connected to.
    $rows = T\CiviCRM::lookup($rows, 'Campaign', ['campaign_external_identifier' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'campaign_id']);

    return $rows;
  }

}
