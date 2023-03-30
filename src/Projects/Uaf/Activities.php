<?php
namespace Civietl\Projects\Uaf;

use Civietl\Transforms as T;

class Activities {

  /**
   * Do all the transforms associated with this step.
   */
  public function transforms(array $rows) : array {
    $rows = T\Columns::deleteAllColumnsExcept($rows, [
      'LGL Constituent ID',
      'Task name',
      'Task description',
      'Task type',
      'Task status',
      'Task owner',
      'Created date',
    ]);
    // Fix mispelled task owners.
    $rows = T\ValueTransforms::valueMapper($rows, 'Task owner', [
      'Keishla Gonzales-Quiles' => 'Keishla Gonzalez-Quiles',
      'Mila  Krambo' => 'Mila Krambo',
      'Samaria Johnson' => '',
    ]);

    // Get target contact IDs.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['LGL Constituent ID' => 'external_identifier'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'target_contact_id']);
    // Get assignee contact IDs.
    $rows = T\CiviCRM::lookup($rows, 'Contact', ['Task owner' => 'display_name'], ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'assignee_contact_id']);
    // We need a source_contact_id, use target_contact_id.
    $rows = T\Columns::copyColumn($rows, 'target_contact_id', 'source_contact_id');
    // Ugh, Task type needs trimming.
    $rows = T\Text::trim($rows, ['Task type']);
    // Remap statuses and types.
    $rows = T\ValueTransforms::valueMapper($rows, 'Task status', ['Open' => 'Scheduled', 'Completed' => 'Complete']);
    $rows = T\ValueTransforms::valueMapper($rows, 'Task type', [
      'Call' => 'Phone Call',
      'Final Report' => 'LGL Final Report',
      'Mailing' => 'Bulk Email',
      'Other' => 'LGL Other',
      'Proposal' => 'LGL Proposal',
      'Thank You Card' => 'LGL Thank You Card',
      '' => 'LGL Other',
    ]);

    $rows = T\Columns::renameColumns($rows, [
      'Task name' => 'subject',
      'Task description' => 'details',
      'Task status' => 'status_id:label',
      'Created date' => 'activity_date_time',
      'Task type' => 'activity_type_id:label',
    ]);
    return $rows;
  }

}
