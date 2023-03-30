<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class Websites {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    // Delete unused columns. Not necessary but easier (and marginally faster) to work with.
    $rows = T\Columns::deleteColumns($rows, ['LGL Website ID', 'Constituent Name']);
    // Rename some columns that are one-to-one with Civi.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Constituent ID' => 'external_identifier',
      'URL' => 'url',
      'Website Type' => 'website_type_id:label',
    ]);
    // Trim and lowercase URLs.
    $rows = T\Text::lowercase($rows, ['url']);
    $rows = T\Text::trim($rows, ['url']);

    // Add protocol if necessary.
    $rows = T\Cleanup::addUrlProtocol($rows, 'url');

    // Log and remove any invalid websites.
    $rows = T\Cleanup::filterInvalidWebsites($rows, 'url');

    // Website type ID: "Website" to "Main".
    $rows = T\ValueTransforms::valueMapper($rows, 'website_type_id:label', ['Website' => 'Main']);
    // Create any missing website types in the option values table.
    $websiteTypes = T\RowFilters::getUniqueValues($rows, 'website_type_id:label');
    T\CiviCRM::createOptionValues('website_type', $websiteTypes);
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['external_identifier' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id']);
    return $rows;
  }

}
