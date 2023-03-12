<?php
namespace Civietl\Transforms;

class RenameColumn {

  /**
   * @var columnsToRename
   *   array in format `['oldname1' => 'newname1', 'oldname2' => 'newname2', etc.]`
   */
  public static function transform(array $rows, array $columnsToRename) : array {
    foreach ($rows as &$row) {
      foreach ($columnsToRename as $oldName => $newName) {
        $row[$newName] = $row[$oldName];
        unset($row[$oldName]);
      }
    }
    return $rows;
  }

}
