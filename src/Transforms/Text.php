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
   * Split a string into an array of substrings.
   */
  public static function stringSplit(array $rows, string $columnName) {
    foreach ($rows as &$row) {
      $separator = ' ';
      $string = &$row[$columnName];
      $names = explode($separator, $string);
      $suffixes = ['Jr.']
      if (count($names) === 2) {
        $row['first_name'] = $names[0];
        $row['last_name'] = $names[1];
      }
      else if (in_array($suffixes, $names)) {
        $row['suffix'] = end($names);
      }
    }
    return $rows;
  }

}
