<?php

/**
 * Builds a TEXTAREA FormField
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldTextArea extends FormField
implements FormFieldInterface
{
    /**
     * ROWS attribute
     * @var string
     */
    protected $rows = "5";
    /**
     * COLS attribute
     * @var string
     */
    protected $cols = "60";

    /**
     * Sets ROWS attribute
     */
    public function setRows($rows = "5")
    {
        $this->rows = $rows;
    }

    /**
     * Sets COLS attribute
     */
    public function setCols($cols = "60")
    {
        $this->cols = $cols;
    }

    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {
        //Convert special character to htmlentities to
        //allow editing html in textarea.
        $textareaValue = $this->defaultValue;

        $this->source .=  '<textarea'
        				. ' rows="' . $this->rows .'"'
        				. ' cols="' . $this->cols . '"'
                . ' name="' . $this->id . '"'
                . ' title="Enter ' . $this->label . '"'
                . ' placeholder="Enter ' . $this->label . ' text."'
                . ' id="' . $this->id . '">'
                . $textareaValue
                . "</textarea>";
    }

}
