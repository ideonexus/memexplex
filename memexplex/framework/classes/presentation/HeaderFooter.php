<?php

/**
 * Builds the header and footer for the page.
 *
 * @package Framework
 * @subpackage Presentation
 * @see Html
 * @author Ryan Somma 08/21/2008
 */
class HeaderFooter extends Html
{
    /**
     * Page Title
     * @var string
     */
    public static $title = null;
    
    /**
     * Meta Description
     * @var string
     */
    public static $description = null;
    
    /**
     * Meta Keywords
     * @var string
     */
    public static $keywords = null;
    
    /**
     * Header Title Display
     * @var string
     */
    public static $headerDisplay = null;
    
    /**
     * <todo:description>
     *
     * @param array $pageArray Page Configuration file.
     */
    public function setSource($pageArray)
    {
        if (!self::$title)
        {
            self::$title = 'MemexPlex: '.PageConfiguration::getCurrentPageTitle();
        }
        
        $headerTitle = self::$title;
        
        // Create Environment
        $environment = new Environment();

        $this->source =
//XHTML 1.0 Strict
"<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\""
." \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">"
."<html xmlns=\"http://www.w3.org/1999/xhtml\">"
    ."<head>"
        ."<title>{$headerTitle}</title>"
        ."<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\""
            .ROOT_FOLDER . "framework/images/icosahedron.ico\"/>";

            $css = new CascadingStyleSheets();
            $css->setSource();
            $this->source .= $css->getSource();

            $this->source .=
        '<meta http-equiv="content-type" content="text/html; charset=utf-8" />'
        .'<meta http-equiv="Pragma" content="no-store, no-cache" />'
        .'<meta http-equiv="cache-control" content="no-store, no-cache, must-revalidate" />'
        .'<meta http-equiv="Expires" content="0" />';

         if (self::$description)
         {
             $this->source .= 
         '<meta name="description" content="'.self::$description.'"/>';
         }
         
         if (self::$keywords)
         {
             $this->source .= 
         '<meta name="keywords" content="'.self::$keywords.'"/>';
         }
        
         if ('none' != PageConfiguration::getCurrentPageJavaScript())
         {
            $this->source .=
        "<script type=\"text/javascript\">"
                //REPLACES document.getElementById() WITH $()
            ."function $(element) {"
            .    "return document.getElementById(element);"
            ."}"
            //ALLOWS FOR ADDING MULTIPLE ONLOAD EVENTS
            //FROM DIFFERENT FUNCTIONS.
            //http://simonwillison.net/2004/May/26/addLoadEvent/
            ."function addLoadEvent(func)"
            ."{"
            .    "var oldonload = window.onload;"
            .    "if (typeof window.onload != 'function') {"
            .        "window.onload = func;"
            .    "} else {"
            .        "window.onload = function() {"
            .            "if (oldonload) {"
            .                "oldonload();"
            .            "}"
            .            "func();"
            .        "}"
            .    "}"
            ."}"
            ."var applicationRootFolder = \"" . ROOT_FOLDER . "\";"
        ."</script>";
         }

        $this->source .=
'</head><body>';
        
        $mxplxObjectImage = '';
        if (self::$headerDisplay)
        {
            $mxplxObjectText = null;
            if (stripos(self::$headerDisplay,'meme') !== false)
            {
                $mxplxObjectText = 'meme';
            }
            elseif (stripos(self::$headerDisplay,'reference') !== false)
            {
                $mxplxObjectText = 'reference';
            }
            elseif (stripos(self::$headerDisplay,'triple') !== false)
            {
                $mxplxObjectText = 'triple';
            }
            elseif (stripos(self::$headerDisplay,'schema') !== false)
            {
                $mxplxObjectText = 'schema';
            }
            
            if ($mxplxObjectText)
            {
                $mxplxObjectImage = '<img src="'
                    .ROOT_FOLDER . 'framework/images/'.$mxplxObjectText.'.png"'
                    .' id="taglineimage" class="'.$mxplxObjectText.'TagLineImage" alt="'.$mxplxObjectText.'" />';
            }
        }
        
        
        if ('none' != PageConfiguration::getCurrentPageHeader())
        {
        	  $menu = new MenuApplication();
        	  $menu->setSource();
        	  
            $this->source .=
'<div id="mainbody">'
    .'<div id="header" class="'.$mxplxObjectText.'">'
        .'<a href="'. ROOT_FOLDER . 'MemeList/" id="headerimage">'
            //.'<img src="'
            //.    ROOT_FOLDER . 'framework/images/memexplexlogo.jpg" width="257" height="50" alt="MemexPlex" />'   
            . 'mxplx'
        .'</a>'
        .$menu->getSource()
        .'<div id="tagline">'
        		.self::$headerDisplay
        		//.$mxplxObjectImage
        .'</div>'
    .'</div>'
    .'<div id="content">';
        }
        else 
        {
            $this->source .=
'<div id="modalbody">';
        }


        $this->source .=
"\n<!-- END PAGE HEADER -->\n";

        $this->source .= $this->externalSource;

        $this->source .=
'<!-- BEGIN PAGE FOOTER -->';

        if ('none' != PageConfiguration::getCurrentPageHeader())
        {
            $this->source .=
    '</div>' //closes content
    .'<div id="footer">'
    .'<a href="http://www.memexplex.org">About</a>'
    .' | <a href="http://www.memexplex.org/credits/">Credits</a>'
    .' | <a href="http://www.memexplex.org/the-four-laws-of-memexplex/">The Four Laws</a>'
    .' | <a href="http://www.memexplex.org/download/">Downloads</a>'
    .' | <a href="http://code.google.com/p/memexplex/">GoogleCode</a>'
    .' | <a href="https://github.com/ideonexus/memexplex">GitHub</a>'
    .'</div>';
        }
        $this->source .=
		'</div>' //closes mainbody
    .'</body>'
.'</html>';

        Benchmark::setBenchmark('HeaderFooter.php', __FILE__, __LINE__);
    }
}
