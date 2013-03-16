<?php
/**
 * We've customized the heck out of this, but this class essentially gathers
 * all errors for the current run, and then handles them at the end of the script
 * in the front controller.
 *
 * @package Framework
 * @subpackage Control
 * @link http://www.search-this.com/2007/01/24/oop-in-php-from-a-net-oop-perspective-the-error-class/
 */
class ErrorCollection
{

    /**
     * @var array Array of errors.
     */
    protected static $errors;

    /**
     * @var int Total everything.
     */
    protected static $arrayTotal;

    /**
     * @var int Number of errors.
     */
    protected static $numErrors;

    /**
     * @var int Number of warnings.
     */
    protected static $numWarnings;

    /**
     * @var int Number of notices
     */
    protected static $numNotices;

    /**
     * Sets error and exception handler.
     */
    public static function initialize()
    {

        self::$errors      = array();
        self::$numErrors   = 0;
        self::$numWarnings = 0;

        PageSession::setValue('lastException', '');
        PageSession::setValue('errorCollection', '');

        $ErrorHandler = new ErrorHandler();
        $ErrorHandler->setErrorHandler();

        $ExceptionHandler = ExceptionHandler::getInstance();
        $ExceptionHandler->setExceptionHandler();

    }

    /**
     * Adds and error.
     *
     * @param string $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @param string $errcontext
     * @return bool
     */
    public static function addError
    (
        $errno,
        $errstr,
        $errfile        = '',
        $errline        = '',
        $errcontext     = '',
        $errorType      = 'ERROR',
        $exceptionClass = 'NONE'
    )
    {
        $addError = self::_addError($errorType, $errno, $errstr,
                                    $errfile, $errline, $errcontext,
                                    $exceptionClass);
        if (!$addError)
        {
            return false;
        }
        else
        {
            self::$numErrors++;
            return true;
        }
    }

    /**
     * Does error collection have errors?
     *
     * @return bool yes/no/maybe
     */
    public static function hasErrors()
    {
        return (bool) self::$numErrors > 0;
    }

    /**
     * @return string Error messages as string.
     */
    public static function getErrorMessages()
    {
        $output = '';
        if (self::$numErrors > 0)
        {
            $count = count(self::$errors['ERROR']) + 1;
            for ($i = 1; $i < $count; $i++)
            {
                $output .= self::_errorToHtml('ERROR',$i);
            }
        }
        return $output;
    }

    /**
     * @return array Error messages as array.
     */
    public static function getErrorMessagesArray()
    {
        return self::$errors['ERROR'];
    }

    /**
     * Adds a user error message, to be displayed to user.
     *
     * @param string $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @param string $errcontext
     * @return bool
     */
    public static function addUserErrorMessage
    (
        $errno,
        $errstr,
        $errfile    = '',
        $errline    = '',
        $errcontext = ''
    )
    {
        $addError = self::_addError('USER ERROR', $errno, $errstr,
                                    $errfile, $errline, $errcontext);
        if (!$addError)
        {
            return false;
        }
        else
        {
            self::$numWarnings++;
            return true;
        }
    }

    /**
     * Does the collection have usererror messages?
     *
     * @return bool
     */
    public static function hasUserErrorMessages()
    {
        return (bool) self::$numWarnings > 0;
    }

    /**
     * @return string User error messages as string.
     */
    public static function getUserErrorMessages()
    {
        $output = '';
        if (self::$numWarnings > 0)
        {
            $count = count(self::$errors['USER ERROR']) + 1;
            for ($i = 0; $i < $count; $i++)
            {
                $output .= self::_errorToHtml('USER ERROR',$i);
            }
        }
        return $output;
    }

    /**
     * @return array User error messages as array.
     */
    public static function getUserErrorMessagesArray()
    {
        return self::$errors['USER ERROR'];
    }

    /**
     * Add a user success message.
     *
     * @param string $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @param string $errcontext
     * @return bool
     */
    public static function addUserSuccessMessage
    (
        $errno,
        $errstr,
        $errfile    = '',
        $errline    = '',
        $errcontext = ''
    )
    {
        $addError = self::_addError('USER SUCCESS', $errno, $errstr,
                                    $errfile, $errline, $errcontext);
        if (!$addError)
        {
            return false;
        }
        else
        {
            self::$numNotices++;
            return true;
        }
    }

    /**
     * @return bool Are user success messages present?
     */
    public static function hasUserSuccessMessages()
    {
        return (bool) self::$numNotices > 0;
    }

    /**
     * @return string User success messages as string.
     */
    public static function getUserSuccessMessages()
    {
        $output = '';
        if (self::$numNotices > 0)
        {
            $count = count(self::$errors['USER SUCCESS']) + 1;
            for ($i = 0; $i < $count; $i++)
            {
                $output .= self::_errorToHtml('USER SUCCESS',$i);
            }
        }
        return $output;
    }

    /**
     * @return array User success messages as array.
     */
    public static function getUserSuccessMessagesArray()
    {
        return self::$errors['USER SUCCESS'];
    }

    /**
     * @return string All messages as string.
     */
    public static function getAllMessages()
    {
        $output = '';
        if (!empty(self::$errors))
        {
            foreach (self::$errors as $key => $value)
            {
                if
                (
                    $key != 'USER SUCCESS'
                    && $key != 'USER ERROR'
                )
                {
                    $lastMessage = '';
                    $typeCount = count(self::$errors[$key]);
                    for ($i = 0; $i < $typeCount; $i++)
                    {
                        //ELIMINATE DUPLICATE ERRORS FROM RE-THROWN EXCEPTIONS
                        if ($lastMessage != self::$errors[$key][$i]['errstr'])
                        {
                            $output .= self::_errorToHtml($key,$i);
                        }
                        $lastMessage = self::$errors[$key][$i]['errstr'];
                    }
                }
            }
        }
        return $output;
    }

    /**
     * Write all errors to log.
     * 
     * @return boolean Success or no.
     */
    public static function logAllErrors()
    {
        if (!empty(self::$errors))
        {
            foreach (self::$errors as $key => $value)
            {
                if
                (
                    $key != 'USER SUCCESS'
                    && $key != 'USER ERROR'
                )
                {
                    $messages = '';
                    $lastMessage = '';
                    $typeCount = count(self::$errors[$key]);
                    for ($i = 0; $i < $typeCount; $i++)
                    {
                        //ELIMINATE DUPLICATE ERRORS FROM RE-THROWN EXCEPTIONS
                        if ($lastMessage != self::$errors[$key][$i]['errstr'])
                        {
                            $messages .= self::_errorToHtml($key,$i);
                        }
                        $lastMessage = self::$errors[$key][$i]['errstr'];
                    }

                    if ($messages != '')
                    {
                        Log::logToFile
                        (
                             $key,
                             str_replace
                             (
                                '<br />',
                                "\n",
                                '[-----PAGE ERRORS-----]'
                                . "\n"
                                . $messages
                                . '[-----PAGE ERRORS-----]'
                                . "\n"
                             )
                        );
                    }
                }
            }
        }

        return true;
    }

    /**
     * Add an exception to the collection as an error.
     *
     * @param Exception $e 
     * @param string $errorType Classification of error.
     */
    public static function addException(Exception $e,$errorType='ERROR')
    {
        self::addError
        (
            $e->getCode(),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString(),
            (string) $errorType,
            get_class($e)
        );
    }

    /**
     * Handles fatal errors by killing everything and dumping out of application.
     *
     * @param string $errstr
     * @param string $redirectLocation
     */
    public static function fatalError($errstr, $redirectLocation = 'error.php')
    {
        if ($redirectLocation == 'error.php')
        {
            PageSession::setValue('lastException',$errstr);
            PageSession::setValue('errorCollection',self::getAllMessages());
        }

        self::logAllErrors();

        $showHeader = '';
        //DISABLE ERROR.PHP HEADER IF THIS IS AN AJAX CALL
        if (Constants::getConstant('AJAX_METHOD'))
        {
            if ($redirectLocation == 'error.php')
            {
                $showHeader = '?ajaxMethod=true';
            }
            else
            {
                $showHeader = 'ajaxMethod=true';
            }
        }

        header
        (
            'Location: '
            . ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS')
            . $redirectLocation
            . $showHeader
        );
        exit;
    }

    /**
     * Boots exceptiont to fatalerror 
     *
     * @param Exception $e
     * @param string $redirectLocation
     */
    public static function fatalException(Exception $e, $redirectLocation = 'error.php')
    {
        self::fatalError($e->getMessage(), $redirectLocation);
    }

    /**
     * Clears all errors and warnings.
     */
    public static function clearAllErrorsAndWarnings()
    {
        self::$errors      = array();
        self::$numErrors   = 0;
        self::$numWarnings = 0;
    }

    /**
     * Adds an error to the collection.
     *
     * @param string $type
     * @param string $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @param string $errcontext
     * @return bool
     */
    protected static function _addError
    (
        $type,
        $errno,
        $errstr,
        $errfile,
        $errline,
        $errcontext,
        $exceptionClass = 'NONE'
    )
    {
        $_addError = false;
        if (strlen(trim($errno)) != 0)
        {
            if (!array_key_exists($type, self::$errors))
            {
                self::$errors[$type] = array();
            }

            self::$errors[$type][] = array
                                 (
                                     'errorId'        => UniqueId::getUniqueId(),
                                     'errno'          => $errno,
                                     'errstr'         => $errstr,
                                     'errfile'        => $errfile,
                                     'errline'        => $errline,
                                     'errcontext'     => $errcontext,
                                     'type'           => $type,
                                     'datetime'       => date("m/d/Y H:i:s"),
                                     'exceptionClass' => $exceptionClass
                                 );
            $_addError = true;
        }
        return $_addError;
    }

    /**
     * Converts error to html string
     *
     * @param string $errorType
     * @return integer $index
     */
    protected static function &_errorToHtml($errorType,$index)
    {
        $error = 'errorId ['        . self::$errors[$errorType][$index]['errorId']        . ']<br />'
               . 'type ['           . self::$errors[$errorType][$index]['type']           . ']<br />'
               . 'exceptionClass [' . self::$errors[$errorType][$index]['exceptionClass'] . ']<br />'
               . 'errno ['          . self::$errors[$errorType][$index]['errno']          . ']<br />'
               . 'errstr ['         . self::$errors[$errorType][$index]['errstr']         . ']<br />'
               . 'errfile ['        . self::$errors[$errorType][$index]['errfile']        . ']<br />'
               . 'errline ['        . self::$errors[$errorType][$index]['errline']        . ']<br />'
               . 'errcontext ['     . self::$errors[$errorType][$index]['errcontext']     . ']<br />'
               . 'datetime ['       . self::$errors[$errorType][$index]['datetime']       . ']<br /><br />';

        return $error;
    }

}
