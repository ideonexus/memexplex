<?php

/**
 * Builds an HTML button.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldButton extends FormField
implements FormFieldInterface
{

    /**
     * @var string Formfield TITLE attribute.
     */
    protected $title;

    /**
     * @param string
     */
    public function setTitle($title)
    {
        $this->title = " title=\"{$title}\"";
    }

    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {
        $this->source = "<input"
                        ." type=\"button\""
                        ." name=\"{$this->id}\""
                        ." id=\"{$this->id}\""
                        .$this->title
                        ." value=\"{$this->defaultValue}\""
                        .$this->onClickJavaScript
                        ." disabled=\"disabled\" />";
    }

}
