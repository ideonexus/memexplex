<?php

/**
 * Displays the default value whether view or edit.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldDisplayOnly extends FormField
implements FormFieldInterface
{

    /**
     * Sets the source to the defaultValue
     */
    public function setSource()
    {
        $this->source = $this->defaultValue;
    }

}
