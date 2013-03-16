<?php

/**
 * Builds the menu for the meme page.
 *
 * @package MemexPlex
 * @subpackage Presentation
 * @author C. A. Rotwang
 */
class MenuMeme extends MenuViewEditToggle
implements MenuInterface
{

    /**
     * Sets the HTML for View/Edit Link.
     */
    public function setSource()
    {
        if ($_POST['id'] || $_GET['id'])
        {
            $this->buildViewEditToggleMenu("Meme","Meme");
        }
    }

}
