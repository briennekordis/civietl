<?php
namespace Civietl\Writer;

use Civietl\Logging;

class CivicrmApi4 implements WriterInterface {
  private string $primaryEntity;
  private bool $allowDuplicates;
  private array $matchFields;

  public function __construct($options) {
    $this->primaryEntity = $options['civi_primary_entity'];
    $this->allowDuplicates = $options['allow_duplicates'] ?? FALSE;
    $this->matchFields = $options['match_fields'] ?? ['id'];
  }

  public function writeOne($row) : array {
    $logEntry = ['Error' => FALSE];
    if ($this->allowDuplicates) {
      $result = civicrm_api4($this->primaryEntity, 'create', [
        'checkPermissions' => FALSE,
        'values' => $row,
      ]);
    }
    else {
      $result = civicrm_api4($this->primaryEntity, 'save', [
        'checkPermissions' => FALSE,
        'records' => [$row],
        'match' => $this->matchFields,
      ]);
    }
    if ($result['error_message'] ?? FALSE) {
      Logging::log("Failed to import: $row");
      Logging::log("Error: $result");
    }
    return $logEntry;
  }

  public function writeAll($rows) : array {
    foreach ($rows as $row) {
      $logEntries[] = $this->writeOne($row);
    }
    return $logEntries;
  }

}
