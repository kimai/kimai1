<?php

/**
 * Responsible for logging messages to the logfile.
 */
class Logger {

  private static $instance = null;
  private $file;

  /**
   * Create a new logger instance.
   */
  private function __construct() {
    $this->file=fopen(WEBROOT."temporary/logfile.txt","a");
  }

  /**
   * Close the file if the instance is destroyed.
   */
  function __destruct() {
    fclose($this->file);
  }

  /**
  * Simple static method to log lines to the logfile.
  *
  * @param string $value message
  * @param string $path relative path to temporary directory
  * @param boolean $success
  * @author sl
  */
  public static function logfile($value) {
      if (self::$instance == null)
        self::$instance = new Logger();

      $value = preg_replace('/\\n|\\s{2,}/i','',$value);
      self::$instance->log($value);
  }

  /**
   * Write a line to the logfile.
   * 
   * @param string $line line to log
   * @author sl
   */
  public function log($line) {
      fputs($this->file, date("[d.m.Y H:i:s] ",time()) . $line ."\n");

  }


}

?>