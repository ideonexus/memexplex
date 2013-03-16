<?php

/**
 * No menu for the 404 page.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see Html
 * @see MenuInterface
 */
class Menu404 extends Menu
implements MenuInterface
{

    /**
     * <todo:description>
     *
     */
    function setSource()
    {
        $this->source = "";
    }

}
