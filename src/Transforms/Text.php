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

  /**
   * Replace a string within a column.
   */
  public static function replace($rows, string $columnName, bool $useReg, string $search, string $replace) {
    foreach ($rows as &$row) {
      foreach ($row as $columnName => $value) {
        if ($useReg) {
          $row[$columnName] = preg_replace($search, $replace, $value);
        }
        elseif (!$useReg) {
          $row[$columnName] = str_replace($search, $replace, $value);
        }
      }
    }
    return $rows;
  }

}
