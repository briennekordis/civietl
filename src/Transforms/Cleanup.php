<?php
namespace Civietl\Transforms;

use Civietl\Logging;
use Civietl\Transforms as T;

class Cleanup {

  /**
   * add "http://" to a column
   */
  public static function addUrlProtocol(array $rows, string $columnName) : array {
    foreach ($rows as &$row) {
      if (!str_starts_with($row[$columnName], 'http://') && !str_starts_with($row[$columnName], 'http://')) {
        $row[$columnName] = 'http://' . $row[$columnName];
      }
    }
    return $rows;
  }

  public static function filterInvalidWebsites($rows, $columnName) : array {
    $logger = new Logging('Bad_Addresses');
    foreach ($rows as $key => $row) {
      if (!filter_var($row[$columnName], FILTER_VALIDATE_URL)) {
        $logger->log("Invalid website in row: " . implode(', ', $row));
        unset($rows[$key]);
      }
    }
    return $rows;
  }

  public static function filterInvalidEmails($rows, $columnName) : array {
    $logger = new Logging('Bad_Addresses');
    foreach ($rows as $key => $row) {
      if (!filter_var($row[$columnName], FILTER_VALIDATE_EMAIL)) {
        $logger->log("Invalid email in row: " . implode(', ', $row));
        unset($rows[$key]);
      }
    }
    return $rows;
  }

  /**
   * The columnName passed into this function should be the external identifier.
   */
  public static function splitContacts(array $rows, string $columnName) {
    foreach ($rows as &$row) {
      $pattern = '/$/';
      $replacement = 's';
      $subject = &$row[$columnName];
      $row['spouseExID'] = preg_replace($pattern, $replacement, $subject);
    }
    return $rows;
  }

  /**
   * Split a single 'Name' string into an array of substrings for different parts of a name.
   */
  public static function splitNames(array $rows, string $columnName) {
    foreach ($rows as &$row) {
      $separator = ' ';
      $string = &$row[$columnName];
      $names = explode($separator, $string);
      $suffixes = \Civietl\Maps::SUFFIX_MAP;
      if (count($names) === 1) {
        $row['first_name'] = $names[0];
      }
      elseif (count($names) === 2) {
        $row['first_name'] = $names[0];
        $row['last_name'] = $names[1];
      }
      elseif (in_array($suffixes, $names)) {
        $row['first_name'] = $names[0];
        $row['last_name'] = $names[1];
        $row['suffix'] = end($names);
      }
      elseif ((count($names) === 3) && !(in_array($suffixes, $names))) {
        $row['first_name'] = $names[0];
        $row['middle_name'] = $names[1];
        $row['last_name'] = $names[2];
      }
    }
    return $rows;
  }

  public static function getLastName(array $rows, string $columnName) {
    foreach ($rows as &$row) {
      $separator = ' ';
      $string = &$row[$columnName];
      $names = explode($separator, $string);
      $row['last_name'] = end($names);
    }
    return $rows;
  }

  /**
   * Will generate an error log for all bad addresses and remove them from the data.
   * @var array $rows
   * @var array $fieldMapping
   *  Should contain a mapping of the Civi field and the field it's derived from.
   *  E.g. ['state_province_id' => 'State', 'country_id' => 'Country'], etc.
   */
  public static function validateAddresses(array $rows, array $fieldMapping) : array {
    foreach ($rows as &$row) {
      $row['errors'] = [];
      // Required fields.
      if (!$row['contact_id']) {
        $row['errors'][] = 'No contact ID.';
      }
      if (!(bool) ($row['location_type_id'] ?? $row['location_type_id:name'] ?? $row['location_type_id:label'] ?? FALSE)) {
        $row['errors'][] = 'No location type';
      }
      // State/country/county IDs are blank but the original field is not.
      foreach ($fieldMapping as $civiField => $originalField) {
        if (!$row[$civiField] && $row[$originalField]) {
          $row['errors'][] = "$civiField could not be determined from $originalField: $row[$originalField]";
        }
      }
      // Check for too-long fields.
      $maxLengths = array_fill_keys(['street_address', 'supplemental_address_1', 'supplemental_address_2', 'supplemental_address_3'], 96) +
       array_fill_keys(['postal_code', 'city'], 64);
      foreach ($maxLengths as $field => $maxLength) {
        if (mb_strlen($row[$field]) > $maxLength) {
          $row['errors'][] = "The $field must not exceed $maxLength characters.";
        }
      }
    }
    $headersWritten = FALSE;
    $logger = new Logging('Bad_Addresses');
    foreach ($rows as &$row) {
      $row['errors'] = implode(", ", $row['errors']);
      if ($row['errors']) {
        if (!$headersWritten) {
          $headersWritten = TRUE;
          $csv = Logging::arrayToCsv(array_keys($row));
          $logger->log($csv);
        }
        $csv = Logging::arrayToCsv($row);
        $logger->log($csv);
      }
      // Don't pass on rows with errors to the writer.
      unset($row);
    }
    return $rows;
  }

  /**
   * If your state is e.g. "Canada" then move it to country.
   */
  public static function moveStatesToCountries(array $rows, string $stateColumn, string $countryColumn) {
    T\Columns::columnsPresent($rows, [$stateColumn, $countryColumn], __FUNCTION__);
    // Only get countries whose name can't possibly be a state, or where the only state with its name is in the country (e.g. Belize).
    $dao = \CRM_Core_DAO::executeQuery('select cc.name from civicrm_country cc LEFT JOIN civicrm_state_province csp ON cc.name = csp.name WHERE csp.name IS NULL OR cc.id = csp.country_id;');
    while ($dao->fetch()) {
      $countries[] = $dao->name;
    }
    // Move states to countries.
    $rows = T\ValueTransforms::valueMapper($rows, $stateColumn, array_combine($countries, $countries), $countryColumn, FALSE);
    // Blank moved states.
    $rows = T\ValueTransforms::valueMapper($rows, $stateColumn, array_fill_keys($countries, ''), NULL, FALSE);
    return $rows;
  }

}
