<?php
namespace Civietl\Transforms;

use Civietl\Logging;

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
    foreach ($rows as $key => $row) {
      if (!filter_var($row[$columnName], FILTER_VALIDATE_URL)) {
        Logging::log("Invalid website in row: " . implode(', ', $row));
        unset($rows[$key]);
      }
    }
    return $rows;
  }

  public static function filterInvalidEmails($rows, $columnName) : array {
    foreach ($rows as $key => $row) {
      if (!filter_var($row[$columnName], FILTER_VALIDATE_EMAIL)) {
        Logging::log("Invalid email in row: " . implode(', ', $row));
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
      $suffixes = ['Jr.'];
      if (count($names) === 2) {
        $row['first_name'] = $names[0];
        $row['last_name'] = $names[1];
      }
      elseif (in_array($suffixes, $names)) {
        $row['suffix'] = end($names);
      }
    }
    return $rows;
  }

}
