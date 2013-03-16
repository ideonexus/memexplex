<?php

/**
 * Appends a page-specific menu to the begining of the html content.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma 08/21/2008
 * @see Html
 */
class MenuPage extends Html
{
    /**
     * Gets the page-specific menu.
     */
    public function setSource()
    {
        if ('none' != PageConfiguration::getCurrentPageMenu())
        {
            $menuObject = MenuFactory::create(PageConfiguration::getCurrentPageCode());
            $menuObject->setSource();

            $this->source = '<!-- BEGIN PAGE-SPECIFIC MENU --><div id="pagespecificmenu">'
                .$menuObject->getSource()
                .'</div><!-- BEGIN PAGE-SPECIFIC MENU -->'
                .$this->externalSource;

            Benchmark::setBenchmark('MenuMain.php', __FILE__, __LINE__);
        }
    }
}
