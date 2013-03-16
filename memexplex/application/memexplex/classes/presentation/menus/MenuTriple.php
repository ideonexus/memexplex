<?php

/**
 * Builds the menu for the triple page.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Dr. Ignacio Metz
 */
class MenuTriple extends MenuViewEditToggle
implements MenuInterface
{

    /**
     * Sets the HTML for View/Edit Link.
     */
    public function setSource()
    {
        if ($_POST['id'] || $_GET['id'])
        {
            $this->buildViewEditToggleMenu("Triple","Triple");
        }
    }

}
