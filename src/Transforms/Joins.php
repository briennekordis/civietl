<?php
namespace Civietl\Transforms;

class Joins {

  public static function removeRowsInBoth(array $rows1, array $rows2, string $rows1JoinColumn, string $rows2JoinColumn) : array {
    $row2List = array_combine(array_keys($rows2), array_column($rows2, $rows2JoinColumn));
    foreach ($rows1 as $key => $row) {
      if (in_array($row[$rows1JoinColumn], $row2List)) {
        unset($rows1[$key]);
      }
    }
    return $rows1;
  }

}
