<?php

/**
 * Spits out HtmlContent Objects given a Page Code.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
class HtmlContentFactory
{

    /**
     * @param string $pageCode Current Page Code
     * @return HtmlContent
     */
    public static function create($pageCode)
    {
        $reportClassName = "HtmlContent" . $pageCode;
        return new $reportClassName($pageCode); //AS $pageCode
    }

}
