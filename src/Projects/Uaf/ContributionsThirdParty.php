<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class ContributionsThirdParty {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Remove columns that will not be imported.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
        'LGL Constituent ID',
        'LGL Gift ID',
        'LGL Campaign ID',
        'Fund',
        'LGL Appeal ID',
        'Gift Category',
        'Gift Amount',
        'Deductible amount',
        'Gift date',
        'Deposit Date',
        'Payment type',
        'Check/Reference No.',
        'Anonymous gift?',
        'Vehicle Name',
        ]);
        // Rename the columns that will be imported to match CiviCRM fields.
        $rows = T\Columns::renameColumns($rows, [
        'LGL Constituent ID' => 'contact_external_identifier',
        'LGL Gift ID' => 'Legacy_Contribution_Data.LGL_Gift_ID',
        'LGL Campaign ID' => 'campaign_external_identifier',
        'Fund' => 'financial_type_id:label',
        'LGL Appeal ID' => 'appeal_external_identifier',
        'Gift Category' => 'Additional_Contribution_Data.Contribution_type:label',
        'Gift Amount' => 'total_amount',
        'Gift date' => 'receive_date',
        'Deposit Date' => 'Additional_Contribution_Data.Deposited_Date',
        'Payment type' => 'payment_instrument_id:label',
        'Check/Reference No.' => 'check_number',
        'Anonymous gift?' => 'Additional_Contribution_Data.Anonymous_gift',
        'Vehicle Name' => 'vehicle_name',
        ]);
    
    // Look up the LGL Gift ID. If it exsists in CiviCRM already, do not import.
    $rows = T\CiviCRM::lookup($rows, 'Contribution', ['Legacy_Contribution_Data.LGL_Gift_ID' => 'Legacy_Contribution_Data.LGL_Gift_ID'], ['id']);
    $rowsAlreadyImported = T\RowFilters::filterBlanks($rows, 'id');
    $rowsToImport = array_diff_key($rows, $rowsAlreadyImported);

    // Cleanup
    // Remap blanks to 'Unknown' for payment instrument.
    $rowsToImport = T\ValueTransforms::valueMapper($rowsToImport, 'payment_instrument_id:label', ['' => 'Unknown']);
    // Create any missing payment methods in the OptionValues table.
    $paymentMethods = T\RowFilters::getUniqueValues($rowsToImport, 'payment_instrument_id:label');
    T\CiviCRM::createOptionValues('payment_instrument', $paymentMethods);
    // Add a column that gives these Contributions a 'Completed' status.
    $rowsToImport = T\Columns::newColumnWithConstant($rowsToImport, 'contribution_status_id:label', 'Completed');
    // Remap true to 1 and false to 0 for Anonymous gift.
    $rowsToImport = T\ValueTransforms::valueMapper($rowsToImport, 'Additional_Contribution_Data.Anonymous_gift', ['FALSE' => 0, 'TRUE' => '1']);
    // Remap 'Donation' to 'Direct Donation' for Contribution type.
    $rowsToImport = T\ValueTransforms::valueMapper($rowsToImport, 'Additional_Contribution_Data.Contribution_type:label', ['Donation' => 'Direct Donation']);
    // Remap null to '' for Contribution type.
    $rowsToImportWithCategory = T\RowFilters::filterBlanks($rowsToImport, 'Additional_Contribution_Data.Contribution_type:label');
    $rowsToImportWithNoCategory = array_diff_key($rowsToImport, $rowsToImportWithCategory);
    if ($rowsToImportWithNoCategory) {
      $rowsToImportWithNoCategory = T\ValueTransforms::valueMapper($rowsToImport, 'Additional_Contribution_Data.Contribution_type:label', ['' => 'Uncategorized']);
    }
    // Merge the two types of rowsToImport back into one.
    $rowsToImport = $rowsToImportWithCategory + $rowsToImportWithNoCategory;
    // Map empty 'Fund' to 'Unrestricted/ General Support'
    $rowsToImport = T\ValueTransforms::valueMapper($rowsToImport, 'financial_type_id:label', ['' => 'Unrestricted/ General Support']);
    // Get the value needed for 'non_deductible_amount' from 'Deductible amount'.
    array_walk($rowsToImport, function(&$row) {
      $row['non_deductible_amount'] = $row['total_amount'] - $row['Deductible amount'];
    });
    
    // Contacts
    // Look up and return the id of the Contact this Contribution is connected to.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['contact_external_identifier' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id']);
    // Look up and return the id of the Vehicle.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['vehicle_name' => 'organization_name'], ['id']);
    // Assign the Vehicle to the Third Party Giving Vehicle custom field.
    $rows = T\Columns::renameColumns($rows, ['id' => 'Additional_Contribution_Data.Third_Party_Giving_Vehicle']);

    // Campaigns
    // Remap 0 to an empty string for the camapaign and/or appeal external ids.
    $rowsToImport = T\ValueTransforms::valueMapper($rowsToImport, 'campaign_external_identifier', ['0' => '', '497' => '']);
    $rowsToImport = T\ValueTransforms::valueMapper($rowsToImport, 'appeal_external_identifier', ['0' => '', '2772' => '', '2662' => '']);
    // If the Contribution has an Appeal id, use that, if not, use the Campaign id if not null.
    $rowsToImport = T\Columns::coalesceColumns($rowsToImport, ['appeal_external_identifier', 'campaign_external_identifier'], 'campaign_or_appeal');
    // Look up and return the id of the Campaign this Contribution is connected to.
    $rowsToImport = T\CiviCRM::lookup($rowsToImport, 'Campaign', ['campaign_or_appeal' => 'external_identifier'], ['id']);
    $rowsToImport = T\Columns::renameColumns($rowsToImport, ['id' => 'campaign_id']);

    return $rowsToImport;


  }
}