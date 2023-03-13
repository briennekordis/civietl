<?php
namespace Civietl;

class Utils {
  /**
   * Handles command-line arguments.
   */
  public static function ParseCli() : array {
    $shortopts = '';
    $longopts = ['settings-file:'];
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

}
