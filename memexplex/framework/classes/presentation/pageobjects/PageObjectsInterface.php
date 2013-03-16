<?php
/**
 * Properties and Methods PageObject objects must implement.
 *
 * @package Framework
 * @subpackage Business
 * @author Ryan Somma
 */
interface PageObjectsInterface
{

    /**
     * Gets the SimpleXmlObject to inform the page.
     */
    public function getData();

}
