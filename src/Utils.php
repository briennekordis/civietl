<?php
namespace Civietl;

class Utils {

  /**
   * Handles command-line arguments.
   */
  public static function ParseCli() : array {
    $shortopts = '';
    $longopts = ['settings-file:', 'start-from:'];
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

  public static function StartFromStep(array $importSettings, string $firstStep) : array {
    if (!$firstStep) {
      return $importSettings;
    }
    foreach ($importSettings as $stepName => $dontcare) {
      if ($firstStep === $stepName) {
        return $importSettings;
      }
      unset($importSettings[$stepName]);
    }
  }

}
