<?php

/**
 * Sets the standard Javascript and dynamically added includes based on FormField needs
 * and configuration.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see Html
 */
class JavaScript extends Html
{

    /**
     * JavaScript includes array.
     * @var array
     */
    protected static $javaScriptIncludes;

    /**
     * Go Go Includes!
     */
    public static function initializeJavaScriptIncludes()
    {
        self::$javaScriptIncludes = array();
    }

    /**
     * Go Go Gadget Add Include!
     */
    public static function addJavaScriptInclude($include='')
    {
        if (!is_array(self::$javaScriptIncludes))
        {
            self::$javaScriptIncludes = array();
        }

        if
        (
            $include != ''
            && !in_array($include, self::$javaScriptIncludes)
        )
        {
            self::$javaScriptIncludes[] = $include;
        }
    }

    /**
     * An autoload function, but for JavaScript directories. Loads into
     * session, and then just keys into it for performance.
     *
     * @param string $javascriptName
     * @return string
     * @throws {@link PresentationExceptionConfigurationError}
     */
    private function autoloadJavaScript($javascriptName)
    {

        //BUILD AND SESSIONALIZE AN ARRAY OF ALL FILES
        //IN THE CLASSES DIRECTORY
        if (!isset($_SESSION['javascriptDirectories']))
        {
            $_SESSION['javascriptDirectories'] = array();

            $dir = new RecursiveDirectoryIterator
            (
                $_SERVER['DOCUMENT_ROOT']
                . ROOT_FOLDER
                . 'framework/javascript/'
            );

            $it = new RecursiveIteratorIterator($dir, 2);
            foreach ($it as $path)
            {
                //ONLY GO ONE DIRECTORY DEEP TO AVOID JS INCLUDES
                if ($it->getDepth() <= 1 && !$path->isDir())
                {
                    $_SESSION['javascriptDirectories'][$path->getFilename()] = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'], '', $path));
                }
            }

            $dir = new RecursiveDirectoryIterator
            (
                $_SERVER['DOCUMENT_ROOT']
                . ROOT_FOLDER
                . 'application/'
                . Constants::getConstant('CURRENT_APPLICATION')
                . '/javascript/'
            );

            $it = new RecursiveIteratorIterator($dir, 2);
            foreach ($it as $path)
            {
                //ONLY GO ONE DIRECTORY DEEP TO AVOID JS INCLUDES
                if ($it->getDepth() <= 1 && !$path->isDir())
                {
                    $_SESSION['javascriptDirectories'][$path->getFilename()] = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'], '', $path));
                }
            }
        }

        $fileExists = false;

        //CHECK FOR FILE
        if(array_key_exists($javascriptName . '.js',$_SESSION['javascriptDirectories']))
        {
            $fileExists = true;
            //Add date last modified as a query string to the end of js include files
            //to ensure they are refreshed. This overcomes the issue of some browsers
            //not properly detecting that a file has been changed.
            //http://stackoverflow.com/questions/32414/force-javascript-file-refresh-on-client
            return $_SESSION['javascriptDirectories'][$javascriptName . '.js']
                . "?"
                . filemtime
                (
                    $_SERVER['DOCUMENT_ROOT']
                    . $_SESSION['javascriptDirectories'][$javascriptName . '.js']
                );
        }

        if ($fileExists == false)
        {
            throw new PresentationExceptionConfigurationError
            (
                'The requested javascript file, "'
                . $javascriptName
                . '.js", could not be found.'
            );
        }
    }

    /**
     * Sets the source for the JavaScript includes, found below HtmlContentMain.
     *
     * @param array $javaScriptArray
     */
    public function setSource()
    {
        if ('none' != PageConfiguration::getCurrentPageJavaScript())
        {
            // JAVASCRIPT COMMON TO ALL EAL PAGES
            $this->source =
"\n<!-- BEGIN PAGE JAVASCRIPT -->\n"
."<script type=\"text/javascript\" src=\""
. ROOT_FOLDER
. "framework/javascript/mxplxJavaScriptFunctions.js"
. "?"
. filemtime
(
    $_SERVER['DOCUMENT_ROOT']
    . ROOT_FOLDER
    . "framework/javascript/mxplxJavaScriptFunctions.js"
)
."\"></script>\n";

            //USE A WHILE LOOP AND XML TO BUILD PAGE-SPECIFIC JAVASCRIPT
            foreach(self::$javaScriptIncludes as $script)
            {
                $this->source .=
                    "<script type=\"text/javascript\" src=\""
                    . $this->autoloadJavaScript($script)
                    . "\"></script>\n";
            }

            //USE A WHILE LOOP AND XML TO BUILD PAGE-SPECIFIC JAVASCRIPT
            foreach(PageConfiguration::getCurrentPageJavaScript()->script as $script)
            {
                if (!in_array($script,self::$javaScriptIncludes))
                {
                    if (!isset($script->attributes()->type))
                    {
                        //EXTERNALIZE JAVASCRIPT TO INCLUDE FILES STRATEGY
                        //PROBLEM: Loading JS directly into the page circumvents the browser's capability
                        //to cache it. JS includes are preferable to inline JS, but includes must be limited.
                        //http://developer.yahoo.com/performance/rules.html#external
                        //http://ajaxian.com/archives/douglas-crockford-video-advanced-javascript
                        $this->source .=
                            "<script type=\"text/javascript\" src=\""
                            . $this->autoloadJavaScript($script)
                            . "\"></script>\n";

                        //INLINE JAVASCRIPT CODE INTO HTML PAGE STRATEGY
                        //Page-specific external js files are currently loaded by reading the file contents
                        //and appending them to javascript written directly into the page. It might be
                        //better to load them from classes. Currently this class runs very quickly.
                        //http://www.websiteoptimization.com/speed/tweak/suture/
                        //$this->source .= file_get_contents($this->autoloadJavaScript($script));
                    }
                    else if ($script->attributes()->type == 'dynamic')
                    {
                        $this->source .= $this->getJavaScriptElement($script);
                    }
                }
            }

            $this->source .=
    "\n<!-- END PAGE JAVASCRIPT -->\n";

            // APPEND THE JAVASCRIPT LAST TO PREVENT PAGE HAVING TO WAIT
            // ON LOADING JAVASCRIPT INCLUDE FILES GET THE MENU/FILTER
            $this->source = $this->externalSource . $this->source;

            Benchmark::setBenchmark('JavaScript.php', __FILE__, __LINE__);
        }
    }

    /**
     * Gets a JavaScript Object... if we still have those.
     * @param string $script
     * @return string
     */
    private function getJavaScriptElement($script)
    {
          //CREATE JavaScript HTML OBJECT
          $javaScriptObject = JavaScriptFactory::create($script);

          //BUILD THE JavaScript
          $javaScriptObject->buildJavaScript();

          //RETURN HTML
          return $javaScriptObject->getSource();
    }

}
