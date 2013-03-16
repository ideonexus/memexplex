<?php
/**
 * All proccessform classes must implement the process() method.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 */
interface ProcessFormInterface
{

    /**
     * Loops through form values, builds appropriate objects, and sends them
     * to the appropriate DAC.
     */
    public function process();

}
