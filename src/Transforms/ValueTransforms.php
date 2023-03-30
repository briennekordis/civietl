<?php
namespace Civietl\Transforms;

class ValueTransforms {

  /**
   * Within a particular column, map bad values to good ones.
   * E.g. ['New Yrok' => 'NY', 'N.Y.' => 'NY', etc.].
   * @var mapping
   *   array in format `['oldvalue1' => 'newvalue1', 'oldvalue2' => 'newvalue2', etc.]`
   * @var newColumn
   *   If NULL, map the new values over the existing values.  If not NULL, put the new values in $newColumn.
   */
  public static function valueMapper(array $rows, string $columnName, array $mapping, ?string $newColumn = NULL) : array {
    $newColumn ??= $columnName;
    foreach ($rows as &$row) {
      // This ensures there's always a "newColumn" element even if there's no value mapping.
      $row[$newColumn] ??= '';
      if (array_key_exists($row[$columnName], $mapping)) {
        $row[$newColumn] = $mapping[$row[$columnName]];
      }
    }
    return $rows;
  }

  public static function toArray(array $rows, string $columnName) {
    foreach ($rows as &$row) {
      foreach ($row as $columnName => $value) {
        $row[$columnName] = array($value);
      }
    }
    return $rows;
  }

}
