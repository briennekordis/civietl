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
      'LGL Gift ID',
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
    $rows = T\CiviCRM::lookup($rows, 'Contact', 'LGL Constituent ID', 'external_identifier', ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'target_contact_id']);
    // Get assignee contact IDs.
    $rows = T\CiviCRM::lookup($rows, 'Contact', 'Task owner', 'display_name', ['id']);
    $rows = T\Columns::renameColumns($rows, ['id' => 'assignee_contact_id']);
    // Remap statuses.
    $rows = T\ValueTransforms::valueMapper($rows, 'Task status', ['Open' => 'Scheduled', 'Completed' => 'Complete']);
    $rows = T\ValueTransforms::valueMapper($rows, 'Task type', [
      'Open' => 'Scheduled', 'Completed' => 'Complete']);
    

    $rows = T\Columns::renameColumns($rows, [
      'Call' => 'Phone Call',
      
Email
Final Report 
Mailing
Meeting
Other
Proposal
Thank You Card

      'Task name' => 'subject',
      'Task description' => 'description',
      'Task status' => 'status_id:label',
      'Created date' => 'activity_date_time',
    ]);
    return $rows;
  }

}
