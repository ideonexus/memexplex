<?php

/**
 * Display only field, truncated to a specified string length.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormFieldDisplayOnly
 * @see FormFieldInterface
 */
class FormFieldDisplayOnlyTruncated extends FormFieldDisplayOnly
implements FormFieldInterface
{
    /**
     * Number of characters to truncate to.
     * @var integer
     */
    protected $truncatedLength = 100;
    public function setTruncateLength($length=100)
    {
        $this->truncatedLength = $length;
    }

    /**
     * Default value is truncated and stripped of HTML tags.
     */
    public function setDefaultValue($defaultValue = '')
    {
        if (strlen(strip_tags($defaultValue)) > $this->truncatedLength)
        {
            $this->defaultValue = substr(strip_tags($defaultValue),0,($this->truncatedLength-3)).'...';
        }
        else
        {
            $this->defaultValue = strip_tags($defaultValue);
        }
    }

}
