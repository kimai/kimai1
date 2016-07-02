<?php

/**
 * Responsible for logging messages to the logfile.
 */
class Kimai_Logger
{
    private static $instance = null;
    private $file;

    /**
     * Create a new logger instance.
     */
    private function __construct()
    {
        $this->file = fopen(WEBROOT . "temporary/logfile.txt", "a");
        set_error_handler("Kimai_Logger::errorHandler");
        set_exception_handler('Kimai_Logger::exceptionHandler');
    }

    /**
     * Close the file if the instance is destroyed.
     */
    public function __destruct()
    {
        fclose($this->file);
    }

    /**
     * Initialize the logger.
     *
     * @author sl
     */
    public static function init()
    {
        if (self::$instance == null) {
            self::$instance = new Kimai_Logger();
        }
    }

    /**
     * Simple static method to log lines to the logfile.
     *
     * @param string $value message
     * @author sl
     */
    public static function logfile($value)
    {
        if (self::$instance == null) {
            self::$instance = new Kimai_Logger();
        }

        $value = preg_replace('/\\n|\\s{2,}/i', '', $value);
        self::$instance->log($value);
    }

    /**
     * Write a line to the logfile.
     *
     * @param string $line line to log
     * @author sl
     */
    public function log($line)
    {
        fputs($this->file, date("[d.m.Y H:i:s] ", time()) . $line . "\n");
    }

    public static function exceptionHandler($exception)
    {
        Kimai_Logger::logfile("Uncaught exception: " . $exception->getMessage());
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {

        // If the @ error-control operator is set don't log the error.
        if (error_reporting() === 0) {
            return false;
        }

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

        Kimai_Logger::logfile($line);

        return false;
    }
}
