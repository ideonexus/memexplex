<?php
/**
 * Contract properties and methods for HtmlContent objects.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
interface HtmlContentInterface
{

    /**
     * Sets the filter array
     */
    public function setFilterArray();

    /**
     * Sets the HTML Source.
     */
    public function setSource();

    /**
     * Gets the HTML Source.
     */
    public function getSource();

}
