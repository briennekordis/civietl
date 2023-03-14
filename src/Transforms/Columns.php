<?php
namespace Civietl\Transforms;

class Columns {

  /**
   * @var columnsToRename
   *   array in format `['oldname1' => 'newname1', 'oldname2' => 'newname2', etc.]`
   */
  public static function renameColumns(array $rows, array $columnsToRename) : array {
    // Ensure all the columns to rename exist.
    $missingColumns = array_diff(array_keys($columnsToRename), array_keys($rows[0]));
    if ($missingColumns) {
      throw new \Exception('Error in ' . __FUNCTION__ . ': These columns don\'t exist in the data: ' . implode(', ', $missingColumns));
    }
    foreach ($rows as &$row) {
      foreach ($columnsToRename as $oldName => $newName) {
        $row[$newName] = $row[$oldName];
        unset($row[$oldName]);
      }
    }
    return $rows;
  }

  public static function deleteColumns(array $rows, array $columnsToDelete) : array {
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

  public static function copyColumn(array $rows, string $oldColumnName, string $newColumnName) : array {
    foreach ($rows as &$row) {
      $row[$newColumnName] = $row[$oldColumnName];
    }
    return $rows;
  }

}
