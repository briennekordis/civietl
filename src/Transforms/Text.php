<?php
namespace Civietl\Transforms;

class Text {

  /**
   * Trim values in listed columns.
   */
  public static function trim(array $rows, array $columns) : array {
    foreach ($rows as &$row) {
      foreach ($row as $columnName => $value) {
        if (in_array($columnName, $columns)) {
          $row[$columnName] = trim($value);
        }
      }
    }
    return $rows;
  }

  /**
   * Lowercase values in listed columns.
   */
  public static function lowercase(array $rows, array $columns) : array {
    foreach ($rows as &$row) {
      foreach ($row as $columnName => $value) {
        if (in_array($columnName, $columns)) {
          $row[$columnName] = strtolower($value);
        }
      }
    }
    return $rows;
  }

}
