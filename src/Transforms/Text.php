<?php
namespace Civietl\Transforms;

class Text {

  /**
   * Trim values in listed columns.
   */
  public static function trim(array $rows, array $columns) : array {
    foreach ($rows as $key => $value) {
      if (in_array($key, $columns)) {
        $rows[$key] = trim($value);
      }
    }
    return $rows;
  }

  /**
   * Lowercase values in listed columns.
   */
  public static function lowercase(array $rows, array $columns) : array {
    foreach ($rows as $key => $value) {
      if (in_array($key, $columns)) {
        $rows[$key] = lowercase($value);
      }
    }
    return $rows;
  }

}
