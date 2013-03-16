<?php

/**
 * Builds the menu for the Schema page.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author Armand Tesla
 */
class MenuSchema extends MenuViewEditToggle
implements MenuInterface
{

    /**
     * Sets the HTML for View/Edit Link.
     */
    public function setSource()
    {
        if ($_POST['id'] || $_GET['id'])
        {
            $this->buildViewEditToggleMenu("Schema","Schema");
        }
    }

}
