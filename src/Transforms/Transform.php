<?php
namespace Civietl\Transforms;

class Transform {

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

}
