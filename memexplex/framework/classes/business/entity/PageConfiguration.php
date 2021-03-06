<?php
/**
 * Properties and methods for accessing page configuration properties as defined
 * in the config xml file matching the page code.
 *
 * @package Framework
 * @subpackage Business.Entity
 * @author Ryan Somma
 */
class PageConfiguration
{

    /**
     * @var PageConfiguration
     */
    private static $instance;

    /**
     * @var string
     */
    private static $currentPage;

    /**
     * @var SimpleXMLObject
     */
    private static $pageConfiguration;

    /**
    * @var string
    */                  
    private static function pageConfigDirectory() {
			  return $_SERVER['DOCUMENT_ROOT']
                       . ROOT_FOLDER
                       .'application/'
                       . Constants::getConstant('CURRENT_APPLICATION')
                       . '/config/pages/';
    }
    /**
    * @var string
    */
    private static function frameworkConfigDirectory() {
       return $_SERVER['DOCUMENT_ROOT']
                       . ROOT_FOLDER
                       . 'framework/config/pages/';
    }
    
    /**
    * Case-insensitve file search
    */
		private static function fileNameExists($directory, $fileName) {
		    $fileArray = glob($directory . '*', GLOB_NOSORT);

		    $fileNameLowerCase = strtolower($fileName);
		    foreach($fileArray as $file) {
		        if(strtolower(str_replace($directory,'',$file)) == $fileNameLowerCase) {
		            return str_replace('.xml','',str_replace($directory,'',$file));
		        }
		    }
		    return '';
		}

    /**
    * Because unix file systems are case-sensitive, but URLs are not,
    * we need to conduct a case-insensitve search of the page xml configuration directory.
    * If a case-insenstive file exists, replace the PageCode with it's name so
    * that it can properly key into all classes, etc.
    */
		public static function verifyPageCode($pageCode) {
				$xmlFile = $pageCode . '.xml';
				
				$pageConfigFile = self::fileNameExists(self::pageConfigDirectory(), $xmlFile);
				if ($pageConfigFile != '')
				{
					  return $pageConfigFile;
				}
				
				$frameworkConfigFile = self::fileNameExists(self::frameworkConfigDirectory(), $xmlFile);
				if ($frameworkConfigFile != '')
				{
					  return $frameworkConfigFile;
				}
				
				/* Return original value for 404 error */
		    return $pageCode;
		}

    /**
     * Loads the page configuration XML file and replaces nodes where
     * other files are referenced to build a single configuration.
     * 
     * Private function to make this a singleton.
     * 
     * [TODO: XSL Validate the page configuration XML file.]
     */
    private function __construct($pageCode)
    {
        // GET THE PAGE PROPERTIES FROM THE XML FILE
//THIS CODE ATTEMPTS TO VALIDATE THE PAGE CONFIGURATION XML
//AGAINST THE PAGE XSD FILE, BUT COULD NOT GET IT TO VALIDATE SUCCESSFULLY
//            $xdoc = new DomDocument;
//            $xdoc->Load($_SERVER['DOCUMENT_ROOT'] . ROOT_FOLDER . 'application/' . Constants::getConstant('CURRENT_APPLICATION') . '/config/pages/' . $pageCode . '.xml');
//            if ($xdoc->schemaValidate('../config/pages/page.xsd'))
//            {
//                //LOAD VALIDATED DOM INTO A SIMPLEXML OBJECT
//                //AND SERIALIZE INTO SESSION VARIABLE
//                $_SESSION['Configuration' . $pageCode] = serialize
//                                           (
//                                               new SimpleXMLSessioned
//                                               (
//                                                   $_SERVER['DOCUMENT_ROOT']
//                                                   . ROOT_FOLDER
//                                                   . 'application/'
//                                                   . Constants::getConstant('CURRENT_APPLICATION')
//                                                   . '/config/pages/'
//                                                   . $pageCode
//                                                   . '.xml'
//                                               )
//                                           );
//            }
//            else
//            {
//                throw new GeneralException($pageCode . ".xml failed to validate against page.xsd.","101010");
//            }

        //At this point our PageCode should be in the right Case
        $xmlFile = $pageCode . '.xml';

        if (file_exists(self::pageConfigDirectory() . $xmlFile))
        {
            $config = simplexml_load_file(self::pageConfigDirectory() . $xmlFile);
        }
        else if (file_exists(self::frameworkConfigDirectory() . $xmlFile))
        {
            //CHECK FRAMEWORK FOLDER
            $config = simplexml_load_file(self::frameworkConfigDirectory() . $xmlFile);
        }
        else
        {
            throw new ControlExceptionPageNotFound('Page not found.', '404');
        }

        //LOAD EXTERNAL CONFIG REFERENCES
        $xmlcfg = array();
        $node = array();
        foreach($config->forms->children() as $form)
        {
            if (isset($form->config))
            {
                if (file_exists(self::pageConfigDirectory() . $form->config . ".xml"))
                {
                    $xmlcfg[] = simplexml_load_file(self::pageConfigDirectory() . $form->config . ".xml");
                    $node[] = $form;
                }
                elseif (file_exists(self::frameworkConfigDirectory() . $form->config . ".xml"))
                {
                    $xmlcfg[] = simplexml_load_file(self::frameworkConfigDirectory() . $form->config . ".xml");
                    $node[] = $form;
                }
                else
                {
                    throw new ControlExceptionConfigurationError('Configuration not found:' . $form->config . ".");
                }
            }
        }

        $i = 0;
        foreach ($xmlcfg as $xml)
        {
            //REPLACE CONFIG NODE WITH EXTERNAL NODE
            $dom=dom_import_simplexml($node[$i]);
            $dom->parentNode->removeChild($dom);
            SimpleXml::simpleXmlAppend($config->forms, $xml);
            $i++;
        }

        self::$pageConfiguration = $config;
    }

    /**
     * Get and instance of the page config as a simplexml object.
     *
     * @param string $pageCode The current pagecode.
     * @return SimpleXmlElement Page configuration.
     */
    public static function getInstance($pageCode)
    {
        if (!(self::$instance instanceof self))
        {
            self::$currentPage = $pageCode;
            self::$instance = new self($pageCode);
        }

        return self::$instance;
    }

    /**
     * @return string Current page code.
     */
    public static function getCurrentPageCode()
    {
        return self::$currentPage;
    }

    /**
     * @return string Current application.
     */
    public static function getCurrentPageApplication()
    {
        return self::$pageConfiguration->application;
    }

    /**
     * @return string Human readable page title.
     */
    public static function getCurrentPageTitle()
    {
        return self::$pageConfiguration->title;
    }

    /**
     * @return string Whether or not to show the application menu.
     */
    public static function getCurrentPageApplicationMenu()
    {
        return self::$pageConfiguration->applicationmenu;
    }

    /**
     * @return string Whether or not to show the page header.
     */
    public static function getCurrentPageHeader()
    {
        return self::$pageConfiguration->header;
    }

    /**
     * @return string Whether or not to show the page-specific menu.
     */
    public static function getCurrentPageMenu()
    {
        return self::$pageConfiguration->menu;
    }

    /**
     * @return string Whether or not to display error messages.
     */
    public static function getCurrentPageErrorDisplay()
    {
        return self::$pageConfiguration->errordisplay;
    }

    /**
     * @return string Current page form configuration.
     */
    public static function getCurrentPageForms()
    {
        return self::$pageConfiguration->forms;
    }

    /**
     * @return string Current page form configuration.
     */
    public static function getCurrentPageSecurity()
    {
        return self::$pageConfiguration->security;
    }

    /**
     * @return string Current page JavaScript includes.
     */
    public static function getCurrentPageJavaScript()
    {
        return self::$pageConfiguration->javascript;
    }
    
    /**
     * @return string Current page JavaScript includes.
     */
    public static function getCurrentPageCascadingStyleSheets()
    {
        return self::$pageConfiguration->css;
    }
}
