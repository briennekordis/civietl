<?php
namespace Civietl\Transforms;

class Columns {

  /**
   * @var columnsToRename
   *   array in format `['oldname1' => 'newname1', 'oldname2' => 'newname2', etc.]`
   */
  public static function renameColumns(array $rows, array $columnsToRename) : array {
    self::columnsPresent($rows, array_keys($columnsToRename), __FUNCTION__);
    foreach ($rows as &$row) {
      foreach ($columnsToRename as $oldName => $newName) {
        $row[$newName] = $row[$oldName];
        unset($row[$oldName]);
      }
    }
    return $rows;
  }

  public static function deleteColumns(array $rows, array $columnsToDelete) : array {
    self::columnsPresent($rows, $columnsToDelete, __FUNCTION__);
    foreach ($rows as &$row) {
      foreach ($row as $columnName => $dontcare) {
        if (in_array($columnName, $columnsToDelete)) {
          unset($row[$columnName]);
        }
      }
    }
    return $rows;
  }

  public static function deleteAllColumnsExcept(array $rows, array $columnsToKeep) : array {
    self::columnsPresent($rows, $columnsToKeep, __FUNCTION__);
    foreach ($rows as &$row) {
      foreach ($row as $columnName => $dontcare) {
        if (!in_array($columnName, $columnsToKeep)) {
          unset($row[$columnName]);
        }
      }
    }
    return $rows;
  }

  public static function newColumnWithConstant(array $rows, string $newColumnName, mixed $constant) : array {
    foreach ($rows as &$row) {
      $row[$newColumnName] = $constant;
    }
    return $rows;
  }

  /**
   * Array element can be empty or missing.
   */
  public static function fillEmptyValues(array $rows, string $newColumnName, mixed $constant) : array {
    foreach ($rows as &$row) {
      $row[$newColumnName] ?: $constant;
    }
    return $rows;
  }

  public static function copyColumn(array $rows, string $oldColumnName, string $newColumnName) : array {
    self::columnsPresent($rows, [$oldColumnName], __FUNCTION__);
    foreach ($rows as &$row) {
      $row[$newColumnName] = $row[$oldColumnName];
    }
    return $rows;
  }

  /**
   * Note that when called, the $columnNames should be passed in order of preference as to which value should be selected if both/all columns have a value.
   */
  public static function coalesceColumns(array $rows, array $columnNames, string $outputColumn) {
    self::columnsPresent($rows, $columnNames, __FUNCTION__);
    foreach ($rows as &$row) {
      $row[$outputColumn] = NULL;
      foreach ($columnNames as $column => $dontcare) {
        $value = $row[$columnNames[$column]] ?? NULL;
        if (!empty($value)) {
          $row[$outputColumn] = $value;
          break;
        }
      }
    }
    return $rows;
  }

  /**
   * Ensure all the columns we're operating on exist.
   */
  public static function columnsPresent(array $rows, array $columns, string $functionName) {
    $missingColumns = array_diff($columns, array_keys(reset($rows)));
    if ($missingColumns) {
      throw new \Exception("Error in $functionName: These columns don\'t exist in the data: " . implode(', ', $missingColumns));
    }
  }

}
