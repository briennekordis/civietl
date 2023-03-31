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
  public static function valueMapper(array $rows, string $columnName, array $mapping, ?string $newColumn = NULL, $caseSensitive = TRUE) : array {
    $newColumn ??= $columnName;
    // Uppercase for case-insensitive search.
    if (!$caseSensitive) {
      foreach ($mapping as $oldValue => $newValue) {
        $newMapping[strtoupper($oldValue)] = $newValue;
      }
      $mapping = $newMapping;
    }

    foreach ($rows as &$row) {
      // This ensures there's always a "newColumn" element even if there's no value mapping.
      $row[$newColumn] ??= '';
      if ($caseSensitive) {
        if (array_key_exists($row[$columnName], $mapping)) {
          $row[$newColumn] = $mapping[$row[$columnName]];
        }
      }
      else {
        if (array_key_exists(strtoupper($row[$columnName]), $mapping)) {
          $row[$newColumn] = $mapping[strtoupper($row[$columnName])];
        }
      }
    }
    // Remove dangling reference.
    unset($row);
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
