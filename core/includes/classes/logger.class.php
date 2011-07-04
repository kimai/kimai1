<?php

/**
 * Responsible for logging messages to the logfile.
 */
class Logger {

  /**
  * writes errors during install or update to the logfile stored in temporary
  *
  * @param string $value message
  * @param string $path relative path to temporary directory
  * @param boolean $success
  * @author sl
  */
  public static function logfile($value) {
      $value = preg_replace('/[\\n\\s]/i','',$value);

      $logdatei=fopen(WEBROOT."temporary/logfile.txt","a");

      fputs($logdatei, date("[d.m.Y H:i:s] ",time()) . $value ."\n");
      fclose($logdatei);
  }


}

?>