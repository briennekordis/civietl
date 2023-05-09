<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class StepOne {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Rename some columns that are one-to-one with Civi.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Constituent ID' => 'external_identifier',
      'First Name' => 'first_name',
      'Middle Name' => 'middle_name',
      'Last Name' => 'last_name',
      'Nick Name' => 'nick_name',
      'Org Name' => 'organization_name',
      'Constituent Type' => 'contact_type',
      'Prefix' => 'prefix_id:label',
      'Suffix' => 'suffix_id:label',
      'Job Title' => 'job_title',
      'Deceased?' => 'is_deceased',
    ]);
    $rowsIndividual = array_filter($rows, function($row) {
      return $row['contact_type'] === 'Individual';
    });
    $rowsOrganization = array_diff_key($rows, $rowsIndividual);

    // INDIVIDUALS
    // Clean up prefix IDs.
    $rowsIndividual = T\ValueTransforms::valueMapper($rowsIndividual, 'prefix_id:label', \Civietl\Maps::PREFIX_MAP);
    // Create any missing prefixes in the option values table.
    $prefixes = T\RowFilters::getUniqueValues($rows, 'prefix_id:label');
    T\CiviCRM::createOptionValues('individual_prefix', $prefixes);
    // Clean up suffix IDs.
    $rows = T\ValueTransforms::valueMapper($rows, 'suffix_id:label', \Civietl\Maps::SUFFIX_MAP);
    $suffixes = T\RowFilters::getUniqueValues($rows, 'suffix_id:label');
    T\CiviCRM::createOptionValues('individual_suffix', $suffixes);

    // ORGANIZATIONS
    $rowsOrganization = T\Columns::newColumnWithConstant($rowsOrganization, 'first_name', '');
    $rowsOrganization = T\Columns::newColumnWithConstant($rowsOrganization, 'last_name', '');

    $rows = $rowsIndividual + $rowsOrganization;
    // Reduce to only the columns we care about.
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'external_identifier',
      'first_name',
      'middle_name',
      'last_name',
      'nick_name',
      'organization_name',
      'contact_type',
      'prefix_id:label',
      'suffix_id:label',
      'job_title',
      'is_deceased',
    ]);
    // Get the prefix and suffix IDs so we can import via API3.
    $rows = T\CiviCRM::OptionValuelookup($rows, 'individual_prefix', ['prefix_id:label' => 'label'], ['value']);
    $rows = T\Columns::renameColumns($rows, ['value' => 'prefix_id']);
    $rows = T\CiviCRM::OptionValuelookup($rows, 'individual_suffix', ['suffix_id:label' => 'label'], ['value']);
    $rows = T\Columns::renameColumns($rows, ['value' => 'suffix_id']);
    $rows = T\Columns::newColumnWithConstant($rows, 'skip_greeting_processing', 1);
    $rows = T\Columns::deleteColumns($rows, ['prefix_id:label', 'suffix_id:label']);
    return $rows;
  }

}
