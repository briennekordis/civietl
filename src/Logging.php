<?php
namespace Civietl;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Logging {
  private static string $logFolder;
  private static Logger $logger;

  public static function setLogFolder(string $logFolder) {
    self::$logFolder = $logFolder;
    self::$logger = new Logger('civietl');
    self::$logger->pushHandler(new StreamHandler(self::$logFolder . '/errors.log', Level::Debug));
    self::$logger->info('Begin logging.');
  }

  public static function log(string $message) {

    self::$logger->info($message);
  }

}
