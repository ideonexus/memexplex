<?php

/**
 * Button to remove rows from a table.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldButtonRemoveRow extends FormField
implements FormFieldInterface
{

    /**
     * Sets button source.
     */
    public function setSource()
    {
        $this->source =  "<input type=\"button\""
                         . " name=\"{$this->id}\""
                         . " id=\"{$this->id}\""
                         . " value=\"Remove\""
                         . $this->onClickJavaScript
                         . "/>";
    }

}
