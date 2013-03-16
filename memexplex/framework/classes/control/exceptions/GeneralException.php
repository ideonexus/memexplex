<?php
/**
 * Extends the Exception class and provides a base exception to extend
 * all other exceptions. Based on the exception class, the system routes 
 * to the appropriate behavior in the ExceptionHandler.
 *
 * @package Framework
 * @subpackage Exceptions
 */
class GeneralException extends Exception
{

    /**
     * @param string $message Exception 
     * @param int $code optional <todo:description>
     */
    public function __construct
    (
        $message,
        $code = 0
    )
    {
        parent::__construct($message, $code);
        ExceptionHandler::handleException($this);
    }

}
