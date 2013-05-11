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
    set_error_handler("Logger::errorHandler");
    set_exception_handler('Logger::exceptionHandler');
  }

  /**
   * Close the file if the instance is destroyed.
   */
  function __destruct() {
    fclose($this->file);
  }

  /**
   * Initialize the logger.
   *
   * @author sl
   */
  public static function init() {
      if (self::$instance == null)
        self::$instance = new Logger();
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

  public static function exceptionHandler($exception) {
    Logger::logfile("Uncaught exception: " . $exception->getMessage());
  }

  public static function errorHandler($errno ,$errstr , $errfile , $errline)  {

    // If the @ error-control operator is set don't log the error.
    if (error_reporting() === 0)
      return false;

    $line = '';
    switch ($errno) {
      case E_WARNING:
        $line .= 'E_WARNING';
        break;
      case E_NOTICE:
        $line .= 'E_NOTICE';
        break;
      case E_USER_ERROR:
        $line .= 'E_USER_ERROR';
        break;
      case E_USER_WARNING:
        $line .= 'E_USER_WARNING';
        break;
      case E_USER_NOTICE:
        $line .= 'E_USER_NOTICE';
        break;
      case E_STRICT:
        $line .= 'E_STRICT';
        break;
      case E_RECOVERABLE_ERROR:
        $line .= 'E_RECOVERABLE_ERROR';
        break;
    }

    $line .= ' ' . $errstr;

    $line .= " @${errfile} line ${errline}";

    Logger::logfile($line);

    return false; // let PHP do it's error handling as well
  }

}

?>