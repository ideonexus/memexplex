<?php

/**
 * Uneditable date and time.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormFieldDisplayOnly
 * @see FormFieldInterface
 */
class FormFieldDisplayOnlyDateTime extends FormFieldDisplayOnly
implements FormFieldInterface
{

    /**
     * @var mixed
     */
    protected $defaultValue = '-&nbsp;-';

    /**
     * @var string <todo:description>
     */
    protected $zuluLocal = 'zulu';

    /**
     * @param string $zuluLocal
     */
    public function setZuluLocal($zuluLocal = 'zulu')
    {
        $this->zuluLocal = $zuluLocal;
    }

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
                                          "d M Y / Hi"
                                          ,strtotime
                                          (
                                              $defaultValue
                                          )
                                      )
                                  );

            if ($this->zuluLocal == "zulu")
            {
                $this->defaultValue .= " Z";
            }
        }
    }

}
