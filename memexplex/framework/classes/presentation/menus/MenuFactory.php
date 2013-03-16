<?php
/**
 * Simple factory for spitting out menus based on pagecode.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
class MenuFactory
{

    /**
     * @param string $pageCode
     * @return Menu
     */
    public static function create($pageCode)
    {
        $pageSpecificMenuName = "Menu" . $pageCode;
        return new $pageSpecificMenuName;
    }
}
