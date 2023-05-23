<?php
namespace Civietl\Writer;

use Ifsnop\Mysqldump as IMysqldump;

class CsvWriter implements WriterInterface {
  private $fileName;

  public function __construct($options) {
    $this->fileName = $options['fileName'];
  }

  public function writeOne($row) : void {
  }

  public function writeAll($rows) : void {
    try {
      // $dump = new IMysqldump\Mysqldump('mysql:host=localhost;dbname=testdb', 'username', 'password');
      $dump = new IMysqldump\Mysqldump(CIVICRM_DSN);
      $dump->start($this->fileName);
    } catch (\Exception $e) {
      echo 'mysqldump-php error: ' . $e->getMessage();
    }
  }

}
