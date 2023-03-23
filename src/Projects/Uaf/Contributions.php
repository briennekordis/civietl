<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class Contributions {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Remove columns that will not be imported.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
      'LGL Gift ID',
      'LGL Parent Gift ID',
      'LGL Campaign ID',
      'Fund',
      'LGL Appeal ID',
      'Gift Category',
      'Gift note',
      'Gift Amount',
      'Gift date',
      'Deposit Date',
      'Payment type',
      'Check/Reference No.',
      'Anonymous gift?',
      'Gift owner',
      'Gift batch ID',
      'Vehicle Name',
    ]);
    // Rename the columns that will be imported to match CiviCRM fields.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Constituent ID' => 'contact_external_identifier',
      'LGL Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID',
      // 'LGL Parent Gift ID',
      'LGL Campaign ID' => 'campaign_external_identifier',
      'Fund' => 'financial_type_id:label',
      'LGL Appeal ID' => 'appeal_external_identifier',
      // 'Gift Category',
      'Gift Amount' => 'total_amount',
      'Gift date' => 'receive_date',
      'Deposit Date' => 'Additional_Contribution_Data.Deposited_Date',
      'Payment type' => 'payment_instrument_id:label',
      'Check/Reference No.' => 'check_number',
      'Anonymous gift?' => 'Additional_Contribution_Data.Anonymous_gift',
      // 'Gift owner',
      // 'Gift batch ID',
      // 'Vehicle Name' => 'vehicle_external_identifier',
    ]);
    // Get random sampe of rows to test. (REMOVE FOR FINAL VERSION)
    // $rows = T\RowFilters::randomSample($rows, 5);
    // Create any missing payment methods in the OptionValues table.
    $paymentMethods = T\RowFilters::getUniqueValues($rows, 'payment_instrument_id:label');
    T\CiviCRM::createOptionValues('payment_instrument', $paymentMethods);
    // Remap true to 1 and false to 0 for Anonymous_gift.
    $rows = T\ValueTransforms::valueMapper($rows, 'Additional_Contribution_Data.Anonymous_gift', ['FALSE' => 0, 'TRUE' => 1]);
    // $rows = T\ValueTransforms::toArray($rows, 'Additional_Contribution_Data.Anonymous_gift');
    // Add a column that gives these Contributions a 'Completed' status.
    $rows = T\Columns::newColumnWithConstant($rows, 'contribution_status_id:label', 'Completed');

    // Contact
    // Look up and reutrn the id of the Contact this Contribution is connected to.
    $rows = T\CiviCRM::lookup($rows, 'Contact', 'contact_external_identifier', 'external_identifier', ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id']);

    // Campaign
    // Remap 0 to an empty string for the camapaign and/or appeal external ids.
    $rows = T\ValueTransforms::valueMapper($rows, 'campaign_external_identifier', ['0' => '', '497' => '']);
    $rows = T\ValueTransforms::valueMapper($rows, 'appeal_external_identifier', ['0' => '', '2772' => '', '2662' => '']);
    // If the Contribution has an Appeal id, use that, if not, use the Campaign id if not null.
    $rows = T\Columns::coalesceColumns($rows, ['appeal_external_identifier', 'campaign_external_identifier'], 'campaign_or_appeal');
    // Look up and reutrn the id of the Campaign this Contribution is connected to.
    $rows = T\CiviCRM::lookup($rows, 'Campaign', 'campaign_or_appeal', 'external_identifier', ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'campaign_id']);

    // Vehicle
    // $rows = T\CiviCRM::lookup($rows, '', 'vehicle_external_identifier', 'external_identifier', ['id']);
    return $rows;
  }

}
