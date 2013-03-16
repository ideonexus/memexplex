<?php
/**
 * Handles methods for managing log files.
 *
 * @package Framework
 * @subpackage Control
 * @author Ryan Somma
 */
class Log
{
    /**
     * Appends a message to a Log file. 
     *
     * @param string $filename Filename to write to.
     * @param string $msg Message to write.
     */
    public static function logToFile($filename, $msg)
    {

        $logDirectory = $_SERVER['DOCUMENT_ROOT']
            . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_LOG_DIRECTORY');

        if (!is_dir($logDirectory))
        {
            mkdir($logDirectory);
        }

        // open file
        $fd = fopen
        (
            $logDirectory
            . date("Ymd", time())
            . $filename . ".log", "a"
        );

        if ($fd)
        {
            // append date/time to message
            $str = "[" . date("Y/m/d h:i:s", time()) . "] \n"
                 . $_SERVER['REQUEST_URI'] . "\n"
                 . ApplicationSession::getValue('CURATOR_ID') . "\n"
                 . $msg . "\n"
            ;

            fwrite($fd, $str . "\n");
            fclose($fd);
        }
    }

}
