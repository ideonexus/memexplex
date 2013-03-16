<?php
/**
 * Determines the current server environment and sets the 
 * appropriate properties.
 *
 * @package Framework
 * @subpackage Control
 * @author Ryan Somma 08/21/2008
 */
class Environment
{
    /**
     * @var string Current web address, used for links.
     */
    protected $phpApplicationWebAddress;

	/**
     * @var string Current log directory.
     */
    protected $phpApplicationLogDirectory;

    /**
     * Sets everything.
     */
    public function __construct()
    {
        if (!ApplicationSession::isNameSet('CURRENT_PHP_APPLICATION_WEB_ADDRESS'))
        {
            //RETRIEVE MENU LAYOUT FROM XML FILE
            $environments = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . ROOT_FOLDER . 'framework/config/environments.xml');

            foreach($environments->environment as $environment)
            {
                foreach($environment->phpAddress as $phpAddress)
                {
                    if (strstr($_SERVER['HTTP_HOST'],(string) $phpAddress) != '')
                    {
                        $this->phpApplicationLogDirectory   = (string) $environment->phpApplicationLogDirectory;
                        $this->phpApplicationWebAddress     = 'http://' . $phpAddress . ROOT_FOLDER;
                        break 2; // BREAKS OUT OF TWO FOREACH LOOPS
                    }
                }
            }

            ApplicationSession::setValue
            (
                'CURRENT_PHP_APPLICATION_WEB_ADDRESS'
                ,$this->phpApplicationWebAddress
            );

            ApplicationSession::setValue
            (
                'CURRENT_PHP_APPLICATION_LOG_DIRECTORY'
                ,$this->phpApplicationLogDirectory
            );

        }
        else
        {
            $this->phpApplicationWebAddress   = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_WEB_ADDRESS');
            $this->phpApplicationLogDirectory = ApplicationSession::getValue('CURRENT_PHP_APPLICATION_LOG_DIRECTORY');
        }

    }
}
