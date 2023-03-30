<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class SplitContactsRelationships {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    $rows = T\Columns:: deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
      'Spouse Name',
    ]);
    $rows = T\RowFilters::filterBlanks($rows, 'Spouse Name');
    // Copy the external identifier of the Contact A.
    $rows = T\Columns::copyColumn($rows, 'LGL Constituent ID', 'Related LGL Constituent ID');
    // Add the 's' suffix to the related Contact's external identifier.
    $rows = T\Cleanup::splitContacts($rows, 'Related LGL Constituent ID');
    // Delete the old 'Related LGL Constituent ID' column.
    $rows = T\Columns::deleteColumns($rows, ['Related LGL Constituent ID']);
    // Rename the column with modified external identifir to match the relationship steps below.
    $rows = T\Columns::renameColumns($rows, ['spouseExID' => 'Related LGL Constituent ID']);
    // Get contact ID A.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['LGL Constituent ID' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id_a']);
    // Get contact ID B.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['Related LGL Constituent ID' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id_b']);
    // Create a Relationship Type column.
    $rows = T\Columns::newColumnWithConstant($rows, 'Relationship Type', 'Spouse of');
    // Change Relationship Type from 'Spouse' to 'Partner' if the in the Spouse Name.
    // $rows = T\Text::replace();
    // Get relationship type IDs.
    $rows = T\CiviCRM::lookup($rows, 'RelationshipType', ['Relationship Type' => 'name_a_b'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'relationship_type_id']);
    return $rows;
  }

}
