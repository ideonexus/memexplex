<?php
/**
 * Identifies the class of exception and routes it through the appropriate
 * actions based on the exceptions.xml file configuration.
 *
 * Uses singleton pattern.
 *
 * @package Framework
 * @subpackage Exceptions
 * @author Ryan Somma
 */
class ExceptionHandler
{

    /**
     * @var ExceptionsConfiguration Instance of this class.
     */
    private static $instance;

    /**
     * @var SimpleXMLObject configuration.xml
     */
    private static $exceptionsConfiguration = null;

    /**
     * Private function to make this a singleton.
     */
    private function __construct()
    {
        // GET THE EXCEPTION PROPERTIES FROM THE XML FILE
        if (!isset($_SESSION['ExceptionsConfiguration']))
        {
            //AND SERIALIZE INTO SESSION VARIABLE
            try
            {
                $_SESSION['ExceptionsConfiguration'] = serialize
                                           (
                                               new SimpleXMLSessioned
                                               (
                                                   $_SERVER['DOCUMENT_ROOT']
                                                   . ROOT_FOLDER
                                                   . 'framework/config/exceptions.xml'
                                               )
                                           );
            }
            catch (ControlException $e)
            {
                die('exceptions.xml configuration file not found.');
            }
        }
        $exceptionsConfigurationXML = unserialize($_SESSION['ExceptionsConfiguration']);

        self::$exceptionsConfiguration = $exceptionsConfigurationXML->getSimpleXMLObject();
    }

    /**
     * Private function to make this a singleton.
     */
    private function __clone()
    {
        // EMPTY
    }

    /**
     * Gets and instance of this object. Keeps it to one intance.
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Sets the PHP session to use this as the exception handler.
     */
    public function setExceptionHandler()
    {
        set_exception_handler(array($this, 'catchExceptions'));
    }

    /**
     * Uncaught exceptions get collected and treated as fatal.
     *
     * @param Exception $exception
     */
    public function catchExceptions(Exception $exception)
    {
        ErrorCollection::addException($exception);
        ErrorCollection::fatalException($exception);
    }

    /**
     *
     * 1. PhpParseError: Unrecoverable to PHP, Application Crashes.
     * 2. PHPError: Warnings, Notices, etc thrown by PHP. Are not exceptions.
     *     Caught and handled by ErrorHandler.php
     * 3. FatalException: Unrecoverable Error, Application Ejects user.
     * 4. RecoverableException: Error Occurred in System, Log It and Handle
     *     Gracefully on front end.
     * 5. NoticeException: Not a True Error, Used for Metrics Gathering.
     * 6. UserNoticeException: User-Input Error or Successful Transaction,
     *    Usually Not Logged. Displayed to the User in Messages portion of screen.
     *
     *   sendEmail: (true/false) Send an e-mail to the application administrators.
     *
     *   logImmediately: (true/false) Whether to write the error to the log file
     *       immediately. Not recommended except in cases where an exception is
     *       killing the system without making an entry in the log file and exceptions
     *       leading up to it are needed for debugging.
     *
     *   logImmediatelyFile: (optional) name of file in "../log/" directory where the
     *       error should be recorded, default is today's date logfile.
     *
     *   collectError: (true/false) collect the exception to the ErrorCollection, where it
     *       will be logged at the end of the script.
     *
     *   collectErrorFile: (optional) name of file in "../log/" directory where the
     *       error should be recorded, default is today's date logfile.
     *
     * @param Exception $exception
     */
    public static function handleException(Exception $e)
    {
        if (!self::$exceptionsConfiguration)
        {
            echo $e->getMessage()."<br/>";
            die("Parameters.");
        }

        $exceptionClass = get_class($e);

        //GET EXCEPTION CONFIGURATION IN EXCEPTION EXTENSIONS
        $currentExceptionConfigurationXml = SimpleXml::getSimpleXmlItem
               (
                   self::$exceptionsConfiguration
                   ,'exception/extension'
                       . '['
                       .     'exceptionClass=\'' . $exceptionClass . '\''
                       . ']'
               );

        if (!$currentExceptionConfigurationXml)
        {
            //CHECK BASE EXCEPTIONS IF NOT EXTENSION
            $currentExceptionConfigurationXml = SimpleXml::getSimpleXmlItem
                   (
                       self::$exceptionsConfiguration
                       ,'exception'
                           . '['
                           .     'exceptionClass=\'' . $exceptionClass . '\''
                           . ']'
                   );
        }

        if (!$currentExceptionConfigurationXml)
        {
            //EXCEPTION TYPE NOT FOUND
            //TREAT AS FATAL
            $exceptionXML = "<extension>";
            $exceptionXML .= "<exceptionClass>UnknownException</exceptionClass>";
            $exceptionXML .= "<collectError>true</collectError>";
            $exceptionXML .= "<fatalError>true</fatalError>";
            $exceptionXML .= "</extension>";
            $currentExceptionConfigurationXml = new SimpleXMLElement($exceptionXML);
        }

        //PLACEHOLDER -----------------------------------
        //E-MAIL ADMINISTRATORS ERROR OCCURRED
        /*
        if
        (
            SimpleXml::getSimpleXmlItem
            (
                $currentExceptionConfigurationXml
                ,"sendEmail"
            )
            == "true"
        )
        {
            mail($to, $subject, $message);
        }
		*/

        //LOG EXCEPTION TO FILE IMMEDIATELY
        //NOT RECOMMENDED: USE ONLY IF EXCEPTION IS PREVENTING
        //COMPLETION OF PAGE LOAD, WHICH WOULD PREVENT LOGGING
        if
        (
            SimpleXml::getSimpleXmlItem
            (
                $currentExceptionConfigurationXml
                ,"logImmediately"
            )
            == "true"
        )
        {
            if
            (
                SimpleXml::getSimpleXmlItem
                (
                    $currentExceptionConfigurationXml
                    ,"logImmediatelyFile"
                )
                != ""
            )
            {
                //LOG TO CUSTOM FILE
                $logFile = SimpleXml::getSimpleXmlItem
                            (
                                $currentExceptionConfigurationXml
                                ,"logImmediatelyFile"
                            );
            }
            else
            {
                //LOG TO STANDARD FILE
                $logFile = 'ERROR';
            }

            Log::logToFile
            (
                $logFile
                ,str_replace
                (
                    '<br />'
                    ,"\n"
                    ,'errorClass ['   . get_class($e)          . ']<br />'
                     . 'errorId ['    . UniqueId::getUniqueId()   . ']<br />'
                     . 'errno ['      . $e->getCode()          . ']<br />'
                     . 'errstr ['     . $e->getMessage()       . ']<br />'
                     . 'errfile ['    . $e->getFile()          . ']<br />'
                     . 'errline ['    . $e->getLine()          . ']<br />'
                     . 'errcontext [' . $e->getTraceAsString() . ']<br />'
                     . 'datetime ['   . date("m/d/Y H:i:s")       . ']<br /><br />'
                )
            );
        }

        //COLLECT EXCEPTION TO BE LOGGED AT END OF SCRIPT
        if
        (
            (
                SimpleXml::getSimpleXmlItem
                (
                    $currentExceptionConfigurationXml
                    ,"collectError"
                )
                == "true"
            )
        )
        {
            if
            (
                SimpleXml::getSimpleXmlItem
                (
                    $currentExceptionConfigurationXml
                    ,"collectErrorFile"
                )
                != ""
            )
            {
                $errorCollectionType = SimpleXml::getSimpleXmlItem
                (
                    $currentExceptionConfigurationXml
                    ,"collectErrorFile"
                );
            }
            else
            {
                $errorCollectionType = 'ERROR';
            }

            ErrorCollection::addException($e,$errorCollectionType);
        }

        //REDIRECT USER TO ERROR PAGE
        if
        (
            SimpleXml::getSimpleXmlItem
            (
                $currentExceptionConfigurationXml
                ,"fatalError"
            )
            == "true"
        )
        {
            $redirectLocation = 'error.php';
            if
            (
                SimpleXml::getSimpleXmlItem
                (
                    $currentExceptionConfigurationXml
                    ,"fatalErrorRedirect"
                )
                != ""
            )
            {
                $redirectLocation = SimpleXml::getSimpleXmlItem
                                    (
                                        $currentExceptionConfigurationXml
                                        ,"fatalErrorRedirect"
                                    );
            }

            ErrorCollection::fatalException($e,$redirectLocation);
        }

        //IF DEBUG IS ON, DISPLAY EXCEPTION
        if (ApplicationSession::getValue('debugFlag'))
        {
             echo '<p><span style="color: #FF0000; font-weight: bold;">errorClass [' . get_class($e) . ']</span><br />'
                     . 'errorId ['    . UniqueId::getUniqueId()   . ']<br />'
                     . 'errno ['      . $e->getCode()          . ']<br />'
                     . 'errstr ['     . $e->getMessage()       . ']<br />'
                     . 'errfile ['    . $e->getFile()          . ']<br />'
                     . 'errline ['    . $e->getLine()          . ']<br />'
                     . 'errcontext [' . $e->getTraceAsString() . ']<br />'
                     . 'datetime ['   . date("m/d/Y H:i:s")       . ']<br />'
                     . '<span style="color: #FF0000; font-weight: bold;">END ERROR</span></p>';
        }
    }

}
