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
    ]);
    // Rename some columns that are one-to-one with Civi.
    $rows = T\Columns::renameColumns($rows, [
      'Acknowledgment Preference' => 'preferred_communication_method:label',
      'Annual Report Name' => 'Additional_Communication_Preferences.Annual_Report_Name',
    ]);
    // Look up the contact by their external identifier.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['LGL Constituent ID' => 'external_identifier'], ['id']);
    $rows = T\Columns::deleteColumns($rows, ['LGL Constituent ID']);
    // Remap Acknowledgment Preference.
    $rows = T\ValueTransforms::valueMapper($rows, 'preferred_communication_method:label', ['Prefers email' => 'Email']);
    // Create any missing preferred communication methods in the option values table.
    $commMethods = T\RowFilters::getUniqueValues($rows, 'preferred_communication_method:label');
    T\CiviCRM::createOptionValues('preferred_communication_method', $commMethods);

    $rowsWithComsTags = T\RowFilters::filterBlanks($rows, 'Communication Tags');
    $rowsWithoutComsTags = array_diff_key($rows, $rowsWithComsTags);
    if ($rowsWithComsTags) {
      foreach ($rowsWithComsTags as &$row) {
        if (str_contains($row['Communication Tags'], 'Do not mail')) {
          $row['do_not_mail'] = 1;
        }
        elseif (str_contains($row['Communication Tags'], 'Do not call')) {
          $row['do_not_phone'] = 1;
        }
        elseif (str_contains($row['Communication Tags'], 'Do not email')) {
          $row['do_not_email'] = 1;
        }
        elseif (str_contains($row['Communication Tags'], 'Requests no solicitations.')) {
          $row['Additional_Communication_Preferences.Requests_No_Solicitations'] = 1;
        }
      }
    }
    $rows = T\Columns::deleteColumns($rows, ['Communication Tags']);
    // Merge the two types of rows back into one.
    $rows = $rowsWithComsTags + $rowsWithoutComsTags;
    return $rows;
  }

}
