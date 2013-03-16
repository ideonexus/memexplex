<?php

/**
 * Builds a text formfield.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldText extends FormField
implements FormFieldInterface
{

    /**
     * SIZE Attribute.
     * @var string
     */
    protected $size = "32";
    /**
     * MAXLENGTH Attribute
     * @var string
     */
    protected $maxlength = "32";

    /**
     * Sets SIZE Attribute.
     */
    public function setSize($size = "32")
    {
        $this->size = $size;
    }

    /**
     * Sets MAXLENGTH Attribute
     */
    public function setMaxlength($maxlength = "32")
    {
        $this->maxlength = $maxlength;
    }

    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {

        $this->source .=  "<input"
                         . " type=\"text\""
                         . " name=\"{$this->id}\""
                         . " id=\"{$this->id}\""
                         . " value=\""
                         .htmlentities($this->defaultValue)
                         ."\""
                         . " size=\"{$this->size}\""
                         . " title=\"Enter {$this->label}\""
                         . " maxlength=\"{$this->maxlength}\""
                         . " disabled=\"disabled\""
                         . " />";

    }

}
