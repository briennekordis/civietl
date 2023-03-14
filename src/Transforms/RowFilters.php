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

  /**
   * Get a random sample of rows for testing.
   */
  public static function filterBlanks(array $rows, string $columnName) : array {
    foreach ($rows as $key => $row) {
      if (empty($row[$columnName])) {
        unset($rows[$key]);
      }
    }
    return $rows;
  }

  /**
   * Filter out records we don't want.
   * @var filters
   * Filters is a two-dimensional array, the inner array has the following values:
   * string columnName
   * string operator (one of: =, !=, is empty, is not empty, >=, >, <, <=, between)
   * mixed value (for operators that use them)
   * mixed value2 (for between operator)
   */
  // public static function filter(array $rows, array $filters) : array {
  //   foreach ($filters as $filter) {
  //     $columnName = $filter[0];
  //     $operator = $filter[1];
  //     $value = $filter[2] ?? NULL;
  //     $value2 = $filter[3] ?? NULL;
  //   }
  // }

}
