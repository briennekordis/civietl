<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class CommunicationPreferences {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Remove columns that will not be imported.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
      'Communication Tags',
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
    // Remap Acknowledgment Preference.
    $rows = T\ValueTransforms::valueMapper($rows, 'preferred_communication_method:label', ['Prefers email' => 'Email']);
    // Create any missing preferred communication methods in the option values table.
    $commMethods = T\RowFilters::getUniqueValues($rows, 'preferred_communication_method:label');
    T\CiviCRM::createOptionValues('preferred_communication_method', $commMethods);
    // Split 'Communication Tags' into relevant Civi fields.
    $rows = T\Transform::splitFieldToFields($rows, 'Communication Tags', ';');
    // $hasAltAddressee = 
    // Match the email greeting to the postal greeting.
    $rows = T\Columns::copyColumn($rows, 'email_greeting_custom', 'postal_greeting_custom');
    // Format the customized greetings with 'Dear '.
    $rows = T\Text::prependText($rows, 'email_greeting_custom', 'Dear ');
    $rows = T\Text::prependText($rows, 'addressee_custom', 'Dear ');

    return $rows;
  }

}
