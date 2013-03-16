<?php
/**
 * Builds a hidden formfield.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma 08/21/2008
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldHidden extends FormField
implements FormFieldInterface
{

    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {

        $this->source = "<input type=\"hidden\""
                        . " id=\"{$this->id}\""
                        . " name=\"{$this->id}\""
                        . " value=\"{$this->defaultValue}\""
                        . " />";

    }

}
