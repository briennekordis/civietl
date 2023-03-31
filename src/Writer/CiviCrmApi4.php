<?php
namespace Civietl\Writer;

use Civietl\Logging;

class CivicrmApi4 implements WriterInterface {
  private string $primaryEntity;
  private bool $allowDuplicates;
  private array $matchFields;
  /**
   * @var bool
   * This is so we know whether to print the row headers.
   */
  private bool $errorFound = FALSE;
  private Logging $logger;

  public function __construct($options) {
    $this->primaryEntity = $options['civi_primary_entity'];
    $this->allowDuplicates = $options['allow_duplicates'] ?? FALSE;
    $this->matchFields = $options['match_fields'] ?? ['id'];
    $this->logger = new Logging($this->primaryEntity . '-api4writer');
  }

  public function writeOne($row) : void {
    try {
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
        //Don't think this is used.
        $this->logger->log("If you see this error message, let Jon know what you ran.");
        // $this->logger->log("Failed to import: $row");
        // $this->logger->log("Error: $result");
      }
    }
    catch (\CRM_Core_Exception $e) {
      if (!$this->errorFound) {
        $this->errorFound = TRUE;
        $this->logger->log("Error, " . implode(', ', array_keys($row)));
      }
      $this->logger->log("\"Error: " . $e->getMessage() . "\", " . implode(', ', $row));
    }
  }

  public function writeAll($rows) : void {
    foreach ($rows as $row) {
      $this->writeOne($row);
    }
  }

}
