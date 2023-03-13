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

}
