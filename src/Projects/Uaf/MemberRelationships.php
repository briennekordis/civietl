<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;
use Civietl\Projects\Uaf\Relationships as R;

class MemberRelationships {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
      'Membership Level',
      'Membership Note',
      'Membership Start',
      'Membership End',
    ]);
    // Rename the columns that will be imported to match CiviCRM fields.
    $rows = T\Columns::renameColumns($rows, [
      'LGL Constituent ID' => 'contact_external_identifier',
      'Membership Level' => 'Relationship Type',
      'Membership Note' => 'description',
      'Membership Start' => 'start_date',
      'Membership End' => 'end_date',
    ]);

    // Get contact ID A.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['contact_external_identifier' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'contact_id_a']);
    // Set contact ID B to the default Organization (Urgent Action Fund).
    $rows = T\Columns::newColumnWithConstant($rows, 'contact_id_b', 1);
    // Clean up the relationships.
    $rows = T\Columns::newColumnWithConstant($rows, 'is_active', 0);
    $rows = T\ValueTransforms::valueMapper($rows, 'Relationship Type', ['UAF Board of Directors' => 'Board Member of', 'UAF Staff' => 'Employee of', 'US Advisory Council Members' => 'US Advisory Council Member for']);
    // Get relationship type IDs.
    $rows = T\CiviCRM::lookup($rows, 'RelationshipType', ['Relationship Type' => 'name_a_b'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'relationship_type_id']);
    return $rows;
  }

}
