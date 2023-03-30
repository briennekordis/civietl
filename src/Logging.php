<?php
namespace Civietl;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Logging {
  private Logger $logger;

  public function __construct(string $channel) {
    if ($GLOBALS['newEtlStarted']) {
      $this->deletePreviousLogs();
      $GLOBALS['newEtlStarted'] = FALSE;
    }
    $this->logger = new Logger($channel);
    $stream = new StreamHandler($GLOBALS['logFolder'] . "/$channel.csv", Logger::INFO);
    $formatter = new LineFormatter("[%datetime%], %channel%, %level_name%, %message%\n");
    $stream->setFormatter($formatter);
    $this->logger->pushHandler($stream);
  }

  public function log(string $message) : void {
    $this->logger->info($message);
  }

  private function deletePreviousLogs() : void {
    if (!$GLOBALS['logFolder']) {
      echo 'Log folder not set - something went very wrong. Aborting to avoid deleting random CSVs.';
      die;
    }
    $files = glob($GLOBALS['logFolder'] . '/*.csv');
    foreach($files as $file){
      if(is_file($file)) {
        unlink($file);
      }
    }
  }

  public static function arrayToCsv(array $array) : string {
    // convert array to CSV.
    $fp = fopen('php://memory', 'w+');
    fputcsv($fp, $array);
    rewind($fp);
    return stream_get_contents($fp);
  }

}
