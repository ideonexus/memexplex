<?php

/**
 * Displays an uneditable date.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormFieldDisplayOnly
 * @see FormFieldInterface
 */
class FormFieldDisplayOnlyDate extends FormFieldDisplayOnly
implements FormFieldInterface
{

    /**
     * @var mixed View only value.
     */
    protected $defaultValue = '-&nbsp;-';

    /**
     * @param string $defaultValue
     */
    public function setDefaultValue($defaultValue = '')
    {
        if ($defaultValue == '')
        {
            $this->defaultValue = '-&nbsp;-';
        }
        else
        {
            $this->defaultValue = strtoupper
                                  (
                                      date
                                      (
                                          "d M Y"
                                          ,strtotime
                                          (
                                              $defaultValue
                                          )
                                      )
                                  );
        }
    }

}
