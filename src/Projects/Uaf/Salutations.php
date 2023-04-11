<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class Salutations {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Remove columns that will not be imported.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
      'Acknowledgment Preference',
      'Annual Report Name',
      'Addressee',
      'Salutation',
      'Alt. Addressee',
      'Alt. Salutation',
    ]);
    // Rename some columns that are one-to-one with Civi.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Constituent ID' => 'external_identifier',
      'Acknowledgment Preference' => 'preferred_communication_method:label',
      'Annual Report Name' => 'Annual_Report.Annual_Report_Name',
      'Alt. Addressee' => 'addressee_custom',
      'Alt. Salutation' => 'email_greeting_custom',
    ]);
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['external_identifier' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id']);
    // Remap Acknowledgment Preference.
    $rows = T\ValueTransforms::valueMapper($rows, 'preferred_communication_method:label', ['Prefers email' => 'Email']);
    // Create any missing preferred communication methods in the option values table.
    $commMethods = T\RowFilters::getUniqueValues($rows, 'preferred_communication_method:label');
    T\CiviCRM::createOptionValues('preferred_communication_method', $commMethods);

    // Handle Contacts with Alt. Addressee
    // Determine if the Alt. Addressee value is different than the Addressee value.
    $rows = T\Cleanup::compareAndSet($rows, 'Addressee', 'addressee_custom', '');
    // Create seperate $rows variables based on the above comparison
    $rowsWithAltAddressee = T\RowFilters::filterBlanks($rows, 'addressee_custom');
    $rowsWithoutAltAddressee = array_diff_key($rows, $rowsWithAltAddressee);
    // Merge rows back together.
    $rows = $rowsWithoutAltAddressee + $rowsWithAltAddressee;

    // Handle Contacts with Alt. Salutation
    // Determine if the Alt. Salutation value is different than the Salutation value.
    $rows = T\Cleanup::compareAndSet($rows, 'Salutation', 'email_greeting_custom', '');
    // Create seperate $rows variables based on the above comparison
    $rowsWithoutAltSalutation = T\RowFilters::filterBlanks($rows, 'email_greeting_custom');
    $rowsWithAltSalutation = array_diff_key($rows, $rowsWithoutAltSalutation);
    // Match the email greeting to the postal greeting.
    $rowsWithAltSalutation = T\Columns::copyColumn($rowsWithAltSalutation, 'email_greeting_custom', 'postal_greeting_custom');
    // Format the customized greetings with 'Dear '.
    $rowsWithAltSalutation = T\Text::prependText($rows, 'email_greeting_custom', 'Dear ');
    $rowsWithAltSalutation = T\Text::prependText($rows, 'addressee_custom', 'Dear ');
    // Merge rows back together.
    $rows = $rowsWithoutAltSalutation + $rowsWithAltSalutation;

    // Clean up columns for rows without Alt. Salutation or Alt. Addressee.
    $rowsWithoutAltSalutation = T\Columns::deleteColumns($rowsWithoutAltSalutation, ['email_greeting_custom', 'addressee_custom']);

    return $rows;
  }

}
