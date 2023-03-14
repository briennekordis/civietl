<?php
namespace Civietl;

class Utils {

  /**
   * Handles command-line arguments.
   */
  public static function parseCli() : array {
    $shortopts = '';
    $longopts = ['settings-file:', 'run-only:', 'start-from:', 'end-on:'];
    $cliArguments = getopt($shortopts, $longopts);
    self::checkRequired($cliArguments);
    $cliArguments = self::fillEmpty($cliArguments);
    return $cliArguments;
  }

  private static function checkRequired(array $cliArguments) : void {
    $requiredArguments = ['settings-file'];
    $arguments = array_keys($cliArguments);
    $missing = NULL;
    foreach ($requiredArguments as $required) {
      if (!in_array($required, $arguments)) {
        $missing .= " $required";
      }
    }
    if (isset($missing)) {
      echo "You are missing the following required arguments:$missing";
      exit(3);
    }
  }

  private static function fillEmpty(array $cliArguments) : array {
    $nonRequiredArguments = ['start-from'];
    $arguments = array_keys($cliArguments);
    foreach ($nonRequiredArguments as $nonRequired) {
      if (!in_array($nonRequired, $arguments)) {
        $cliArguments[$nonRequired] = '';
      }
    }
    return $cliArguments;
  }

  public static function filterSteps(array $importSettings, array $cliArguments) : array {
    $firstStep = $lastStep = $cliArguments['run-only'] ?? NULL;
    $firstStep ??= $cliArguments['start-from'] ?? NULL;
    $lastStep ??= $cliArguments['end-on'] ?? NULL;
    if (!$firstStep) {
      return $importSettings;
    }
    // First step.
    foreach ($importSettings as $stepName => $dontcare) {
      if ($firstStep === $stepName) {
        break;
      }
      unset($importSettings[$stepName]);
    }
    // Last step.
    $pastLastStep = FALSE;
    foreach ($importSettings as $stepName => $dontcare) {
      if ($pastLastStep) {
        unset($importSettings[$stepName]);
      }
      if ($lastStep === $stepName) {
        $pastLastStep = TRUE;
      }
    }
    return $importSettings;
  }

}
