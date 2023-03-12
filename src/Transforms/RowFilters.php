<?php
namespace Civietl\Transforms;

class RowFilters {

  /**
   * Given a two-dimensional array $rows, returns all the unique values in the row.
   */
  public static function getUniqueValues(array $rows, string $columnName) : array {
    $values = array_column($rows, $columnName);
    return array_unique($values);
  }

  /**
   * Get a random sample of rows for testing.
   */
  public static function randomSample(array $rows, int $numberOfRows) : array {
    $randomKeys = array_rand($rows, $numberOfRows);
    foreach ($randomKeys as $key) {
      $newRows[$key] = $rows[$key];
    }
    return $newRows;
  }

}

