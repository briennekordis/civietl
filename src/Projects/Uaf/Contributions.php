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
      'Appeal',
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
      // 'Appeal' => 'source',
      // 'Gift Category',
      'Gift Amount' => 'total_amount',
      'Gift date' => 'receive_date',
      'Deposit Date' => 'Additional_Contribution_Data.Deposited_Date',
      'Payment type' => 'payment_instrument_id.label',
      'Check/Reference No.' => 'check_number',
      'Anonymous gift?' => 'Additional_Contribution_Data.Anonymous_gift',
      // 'Gift owner',
      // 'Gift batch ID',
      // 'Vehicle Name',
    ]);
    // Get random sampe of rows to test. (REMOVE FOR FINAL VERSION)
    $rows = T\RowFilters::randomSample($rows, 10);
    // // Remap true to 1 and false to 0 for Anonymous gift?.
    $rows = T\ValueTransforms::valueMapper($rows, 'Additional_Contribution_Data.Anonymous_gift', ['false' => 0, 'true' => 1]);
    // Look up and reutrn the id of Entities this Contribution is tied to.
    $rows = T\CiviCRM::lookup($rows, 'Contact', 'external_identifier', 'external_identifier', ['id']);
    $rows = T\CiviCRM::lookup($rows, 'Campaign', 'campaign_external_identifier', 'external_identifier', ['id']);
    return $rows;
  }

}
