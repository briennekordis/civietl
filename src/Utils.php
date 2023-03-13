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
    return $cliArguments;
  }

  private static function checkRequired(array $options) {
    $requiredArguments = ['settings-file'];
    $arguments = array_keys($options);
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

  public static function StartFromStep(array $importSettings, ?string $firstStep) : array {
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
