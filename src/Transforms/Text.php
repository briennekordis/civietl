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
  public static function replace(array $rows, string $columnName, string $search, string $replace, bool $useRegex) {
    foreach ($rows as &$row) {
      foreach ($row as $columnName => $value) {
        if ($useRegex) {
          $row[$columnName] = preg_replace($search, $replace, $value);
        }
        elseif (!$useRegex) {
          $row[$columnName] = str_replace($search, $replace, $value);
        }
      }
    }
    return $rows;
  }

  /**
   * Add a specified string to the beginning of another string.
   */
  public static function prependText(array $rows, string $columnName, string $prependString) {
    foreach ($rows as &$row) {
      foreach ($row as $columnName => $value) {
        $row[$columnName] = $prependString . $value;
      }
    }
    return $rows;
  }

}
