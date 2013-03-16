<?php
/**
 * Sets the space for the page-specific menu.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see Html
 * @see MenuInterface
 */
class Menu extends Html
implements MenuInterface
{

    /**
     * Get the Html Source.
     */
    public function getSource()
    {
        return "<div>" . $this->source . "</div><br/>";
    }

}
