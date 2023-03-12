<?php
namespace Civietl\Transforms;

class Filters {

  /**
   * Given a two-dimensional array $rows, returns all the unique values in the row.
   */
  public static function getUniqueValues(array $rows, string $columnName) : array {
    $values = array_column($rows, $columnName);
    return array_unique($values);
  }

}
