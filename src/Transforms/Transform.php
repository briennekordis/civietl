<?php
namespace Civietl\Transforms;

class Transform {

  /**
   * Given a string with a delimiter, normalize the field by creating a new row.
   */
  public static function splitFieldToRows(array $rows, string $oldcolumnName, string $newColumnName, string $delimiter) : array {
    $newRows = [];
    foreach ($rows as $row) {
      $splitValues = explode($delimiter, $row[$oldcolumnName] ?? '');
      foreach ($splitValues as $splitValue) {
        $newRows[] = $row + [$newColumnName => $splitValue];
      }
    }
    return $newRows;
  }

  /**
   * Given a string with a delimiter, create new columns for each value.
   * E.g. if passed "street_address", and it explodes into an array with 3
   * elements, it will add "street_address_0', "street_address_1", "street_address_2" to $rows.
   */
  public static function splitFieldToFields(array $rows, string $columnName, string $delimiter) : array {
    $maxRows = 1;
    // Explode the field into separate columns.
    foreach ($rows as &$row) {
      $exploded = explode($delimiter, $row[$columnName]);
      foreach ($exploded as $columnKey => $field) {
        $row["{$columnName}_{$columnKey}"] = $field;
      }
      $maxRows = max($maxRows, count($exploded));
    }
    // Add blank columns into rows that don't have the maximum number of columns.
    for ($i = 0; $i < $maxRows; $i++) {
      $empties["{$columnName}_{$i}"] = '';
    }
    foreach ($rows as &$row) {
      $row += $empties;
    }
    return $rows;
  }

}
