<?php
namespace Civietl\Transforms;

class ValueTransforms {

  /**
   * Within a particular column, map bad values to good ones.
   * E.g. ['New Yrok' => 'NY', 'N.Y.' => 'NY', etc.].
   * @var mapping
   *   array in format `['oldvalue1' => 'newvalue1', 'oldvalue2' => 'newvalue2', etc.]`
   */
  public static function valueMapper(array $rows, string $columnName, array $mapping) : array {
    foreach ($rows as &$row) {
      if ($mapping[$row[$columnName]] ?? FALSE) {
        $row[$columnName] = $mapping[$row[$columnName]];
      }
    }
    return $rows;
  }

}
