<?php

/**
 * Builds the menu for the reference page.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Dr. Jack Griffin
 */
class MenuReference extends MenuViewEditToggle
implements MenuInterface
{

    /**
     * Sets the HTML for View/Edit Link.
     */
    public function setSource()
    {
        if ($_POST['id'] || $_GET['id'])
        {
            $this->buildViewEditToggleMenu("Reference","Reference");
        }
    }

}
