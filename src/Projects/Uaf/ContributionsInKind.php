<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class ContributionsInKind {

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
      'LGL Appeal ID',
      'Gift Category',
      'Gift Amount',
      'Deductible amount',
      'Gift date',
    ]);
    // Rename the columns that will be imported to match CiviCRM fields.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Constituent ID' => 'contact_external_identifier',
      'LGL Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID',
      'Gift Type' => 'Additional_Contribution_Data.Contribution_type:label',
      'LGL Campaign ID' => 'campaign_external_identifier',
      'Fund' => 'financial_type_id:label',
      'LGL Appeal ID' => 'appeal_external_identifier',
      'Gift Category' => 'payment_instrument_id:label',
      'Gift Amount' => 'total_amount',
      'Gift date' => 'receive_date',
    ]);
    // Get random sampe of rows to test. (REMOVE FOR FINAL VERSION)
    // $rows = T\RowFilters::randomSample($rows, 50);

    // Cleanup
    // Create any missing payment methods in the OptionValues table.
    $paymentMethods = T\RowFilters::getUniqueValues($rows, 'payment_instrument_id:label');
    T\CiviCRM::createOptionValues('payment_instrument', $paymentMethods);
    // Add a column that gives these Contributions a 'Completed' status.
    $rows = T\Columns::newColumnWithConstant($rows, 'contribution_status_id:label', 'Completed');
    // Get the value needed for 'non_deductible_amount' from 'Deductible amount'.
    array_walk($rows, function(&$row) {
      $row['non_deductible_amount'] = $row['total_amount'] - $row['Deductible amount'];
    });
    // Map empty 'Fund' to 'In-Kind'
    $rows = T\ValueTransforms::valueMapper($rows, 'financial_type_id:label', ['' => 'In-Kind']);

    // Look up and return the id of the Contact for this Contribution.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['contact_external_identifier' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id']);

    // Campaigns
    // Remap 0 to an empty string for the camapaign and/or appeal external ids.
    $rows = T\ValueTransforms::valueMapper($rows, 'campaign_external_identifier', ['0' => '', '497' => '']);
    $rows = T\ValueTransforms::valueMapper($rows, 'appeal_external_identifier', ['0' => '', '2772' => '', '2662' => '']);
    // If the Contribution has an Appeal id, use that, if not, use the Campaign id if not null.
    $rows = T\Columns::coalesceColumns($rows, ['appeal_external_identifier', 'campaign_external_identifier'], 'campaign_or_appeal');
    // Look up and return the id of the Campaign this Contribution is connected to.
    $rows = T\CiviCRM::lookup($rows, 'Campaign', ['campaign_or_appeal' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'campaign_id']);

    return $rows;
  }

}
