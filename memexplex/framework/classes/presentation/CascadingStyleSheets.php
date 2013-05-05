<?php

/**
 * Retrieves and writes CSS includes to the client.
 *
 * @package MXPLX.Framework
 * @subpackage Presentation
 */
class CascadingStyleSheets extends Html
{

    /**
     * @var array CSS files array set in page config.
     */
    protected static $cascadingStyleSheetsFiles = null;

    /**
     * @var array CSS files included via static method.
     */
    protected static $cascadingStyleSheetsIncludes = array();

    /**
     * Initializes includes.
     */
    public static function initializeCascadingStyleSheetsIncludes()
    {
        self::$cascadingStyleSheetsIncludes = array();
    }

    /**
     * Adds a CSS file include.
     */
    public static function addCascadingStyleSheetsInclude($include='')
    {
        if (!is_array(self::$cascadingStyleSheetsIncludes))
        {
            self::$cascadingStyleSheetsIncludes = array();
        }

        if ($include != '' &&
            !in_array($include, self::$cascadingStyleSheetsIncludes))
        {
            self::$cascadingStyleSheetsIncludes[] = $include;
        }
    }

    /**
     * Returns an array of framework and application
     * cascadingstylesheet names and their realpath file location.
     *
     * @param bool $reload [optional]
     * @return array
     */
    public static function getCascadingStyleSheetsFiles()
    {
        //BUILD AND SESSIONALIZE AN ARRAY OF ALL FILES
        //IN THE CLASSES DIRECTORY
        if (!isset($_SESSION['cssDirectories']))
        {
            $_SESSION['cssDirectories'] = array();
            
            $cascadingStyleSheetsPath =
                $_SERVER['DOCUMENT_ROOT']
                . ROOT_FOLDER
                . 'framework/css/';

            $directoryIterator = new RecursiveDirectoryIterator($cascadingStyleSheetsPath);
            $it = new RecursiveIteratorIterator($directoryIterator, 2);
            foreach ($it as $path)
            {
                //ONLY GO ONE DIRECTORY DEEP TO AVOID JS INCLUDES
                if ($it->getDepth() <= 1 && !$path->isDir())
                {
                    self::$cascadingStyleSheetsFiles[$path->getFilename()] = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'], '', $path));
                }
            }

//                $dir = new RecursiveDirectoryIterator
//                (
//                    $_SERVER['DOCUMENT_ROOT']
//                    . ROOT_FOLDER
//                    . 'application/'
//                    . Constants::getConstant('CURRENT_APPLICATION')
//                    . '/css/'
//                );
//    
//                $it = new RecursiveIteratorIterator($dir, 2);
//                foreach ($it as $path)
//                {
//                    //ONLY GO ONE DIRECTORY DEEP TO AVOID JS INCLUDES
//                    if ($it->getDepth() <= 1 && !$path->isDir())
//                    {
//                        $_SESSION['javascriptDirectories'][$path->getFilename()] = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'], '', $path));
//                    }
//                }
            $_SESSION['cssDirectories'] = self::$cascadingStyleSheetsFiles;
        }
        else
        {
            self::$cascadingStyleSheetsFiles = $_SESSION['cssDirectories'];
        }

        return self::$cascadingStyleSheetsFiles;
    }

    /**
     * Autoload function for Framework CSS directory.
     *
     * @param string $cascadingstylesheetName <todo:description>
     * @return string <todo:description>
     * @throws {@link PresentationExceptionConfigurationError}
     */
    private function autoloadCascadingStyleSheets($cascadingstylesheetName)
    {
        $cascadingstylesheetName = "$cascadingstylesheetName.css";
        $cascadingStyleSheetsFiles = self::getCascadingStyleSheetsFiles();
        $cascadingStyleSheetsExists = isset($cascadingStyleSheetsFiles[$cascadingstylesheetName]);
        if (!$cascadingStyleSheetsExists)
        {
            throw new PresentationExceptionConfigurationError
            (
                "The requested cascadingstylesheet file '$cascadingstylesheetName' could not be found."
            );
        }
        $cascadingStyleSheetsPath = $cascadingStyleSheetsFiles[$cascadingstylesheetName];

        //Add date last modified as a query string to the end of js include files
        //to ensure they are refreshed. This overcomes the issue of some browsers
        //not properly detecting that a file has been changed.
        //http://stackoverflow.com/questions/32414/force-javascript-file-refresh-on-client
        $timestamp = filemtime($_SERVER['DOCUMENT_ROOT'] . $cascadingStyleSheetsPath);

        return "$cascadingStyleSheetsPath?$timestamp";
    }

    /**
     * <todo:description>
     *
     * @param array $cascadingStyleSheetsArray
     */
    public function setSource()
    {
        if ('none' != PageConfiguration::getCurrentPageCascadingStyleSheets())
        {
            // CSS COMMON TO ALL MXPLX PAGES
            $this->source =
                "\n<!-- BEGIN PAGE CSS -->\n"
                ."<link rel=\"Stylesheet\" type=\"text/css\" href=\""
                . ROOT_FOLDER . "framework/css/memexplex_style.css"
                . "?"
                . filemtime
                (
                    $_SERVER['DOCUMENT_ROOT']
                    . ROOT_FOLDER
                    . "framework/css/memexplex_style.css"
                )
                ."\"/>"
                .'<!--[if lt IE 7]>'
                .'<style media="screen" type="text/css">'
                .'#mainbody {'
                    .'height:100%;'
                .'}'
                .'</style>'
                .'<![endif]-->'
                .'<!--[if lte IE 9]>'
                .'<style type="text/css">'
                .'label {'
                    .'display:inline; !important'
                .'}'
                .'</style>'
                .'<![endif]-->'
                ;
                
            foreach(self::$cascadingStyleSheetsIncludes as $style)
            {
                $this->source .=
                    "<link rel=\"Stylesheet\" type=\"text/css\" href=\""
                    . $this->autoloadCascadingStyleSheets($style)
                    . "\"/>\n";
            }

            //USE A WHILE LOOP AND XML TO BUILD PAGE-SPECIFIC CSS
            if (isset(PageConfiguration::getCurrentPageCascadingStyleSheets()->style))
            {
                foreach(PageConfiguration::getCurrentPageCascadingStyleSheets()->style as $style)
                {
                    if (!in_array($style,self::$cascadingStyleSheetsIncludes))
                    {
                        if (!isset($style->attributes()->type))
                        {
                            $this->source .=
                                "<link rel=\"Stylesheet\" type=\"text/css\" href=\""
                                . $this->autoloadCascadingStyleSheets($style)
                                . "\"/>\n";
                        }
                        else if ($style->attributes()->type == 'dynamic')
                        {
                            $this->source .= $this->getCascadingStyleSheetsElement($style);
                        }
                    }
                }
            }
            $this->source .=
                "\n<!-- END PAGE CSS -->\n";

            // APPEND THE CSS LAST TO PREVENT PAGE HAVING TO WAIT
            // ON LOADING CSS INCLUDE FILES GET THE MENU/FILTER
            $this->source = $this->externalSource . $this->source;

            Benchmark::setBenchmark('CascadingStyleSheets.php', __FILE__, __LINE__);
        }
    }

    /**
     *
     * @param string $style <todo:description>
     * @return string <todo:description>
     */
    private function getCascadingStyleSheetsElement($style)
    {
        //CREATE CascadingStyleSheets HTML OBJECT
        $cascadingStyleSheetsObject = CascadingStyleSheetsFactory::create($style);

        //BUILD THE CascadingStyleSheets
        $cascadingStyleSheetsObject->buildCascadingStyleSheets();

        //RETURN HTML
        return $cascadingStyleSheetsObject->getSource();
    }

}
