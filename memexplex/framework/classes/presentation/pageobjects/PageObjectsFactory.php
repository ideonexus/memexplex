<?php
/**
 * Simple factor for producing pageobjecs based on pagecode.
 *
 * @package Framework
 * @subpackage Business
 * @author Ryan Somma
 */
class PageObjectsFactory
{

    /**
     * @param string $pageCode Page code.
     * @return PageObject
     */
    public static function create($pageCode)
    {
        $pageObjectsClassName = "PageObjects" . $pageCode;
        return new $pageObjectsClassName;
    }
}
