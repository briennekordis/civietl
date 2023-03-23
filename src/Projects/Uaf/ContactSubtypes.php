<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class ContactSubtypes {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
      'Relationship Type',
    ]);
    // $contactSubtype = T\RowFilters::getUniqueValues($rows, 'Relationship Type');
    $contactSubtype = ['Donor Advised Fund', 'Fund'];
    T\CiviCRM::createContactSubtypes($contactSubtype);
    return $rows;
  }

}
