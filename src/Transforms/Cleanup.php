<?php
namespace Civietl\Transforms;

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

}
