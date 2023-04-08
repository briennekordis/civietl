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

  /**
   * Given a column with different values, create a separate column for each value.
   * FIXME: Write example
   * denormalizeColumn($rows, 'contact_id', 'Communication Tag', ['Do not mail' => 'do_not_mail', 'Do not call' => 'do_not_phone', ''Do not email' => 'do_not_email']')
   * Given data:
   * contact_id | Communication Tag
   * 7          | Do Not Call
   * 7          | Do Not Mail
   * 8          | Do Not Email
   * 8          | Do Not Mail
   *
   * Return:
   * contact_id | do_not_call | do_not_mail | do_not_email
   * 7          |           1 |           1 |
   * 8          |             |           1 |            1
   *
   * FIXME: Not very efficient. Also you lose all your columns that aren't explicitly named.
   */
  public static function denormalizeColumn(array $rows, string $keyColumn, string $targetColumn, array $columnMap) : array {
    $newRows = [];
    // Set all the columns up in $newRows.
    foreach (array_unique(array_column($rows, $keyColumn)) as $keyColumnValue) {
      foreach ($columnMap as $newColumnName) {
        $newRows[$keyColumnValue][$newColumnName] = 0;
      }
    }
    foreach ($rows as $row) {
      // if the target column's value is a key in the $columnMap, set $newRows[the contact id][column map's value] = TRUE
      if ($columnMap[$row[$targetColumn]] ?? FALSE) {
        // Bitwise OR converts boolean to integer.
        $newRows[$row[$keyColumn]][$columnMap[$row[$targetColumn]]] |= TRUE;
      }
    }
    return $newRows;
  }

}
