<?php

/**
 * Builds a checkbox form element.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldCheckbox extends FormField
implements FormFieldInterface
{

    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {

        $checked = '';
        $offswitch = '';
        if (BoolUtilities::toBoolFlexible($this->defaultValue))
        {
            $checked = ' checked';
        }

//        $this->source = "<input type=\"checkbox\""
//                      . " name=\"{$this->id}\""
//                      . " id=\"{$this->id}\""
//                      . " value=\"Y\""
//                      . $checked
//                      . " disabled=\"disabled\" />";

					$this->source = "<div class=\"onoffswitch\" id=\"{$this->id}onoffswitch\">"
			    ."<input type=\"checkbox\" name=\"{$this->id}\" id=\"{$this->id}\" value=\"Y\" class=\"onoffswitch-checkbox\" disabled=\"disabled\"{$checked} />"
			    ."<label class=\"onoffswitch-label\" for=\"{$this->id}\">"
			    ."<div class=\"onoffswitch-inner\"></div>"
			    ."<div class=\"onoffswitch-switch\"></div>"
			    ."</label>"
			    ."</div>";
    }

    /**
     * Sets the view source version to a checkmark if Y
     */
    public function getSource($view=false)
    {

        if ($view)
        {
            if (BoolUtilities::toBoolFlexible($this->defaultValue))
            {
                return "<img"
                        ." src=\""
                        .ROOT_FOLDER
                        ."framework/images/checkmark.gif\""
                        ." alt=\"{$this->label}\">";
            }
            else
            {
                return "&nbsp;";
            }
        }
        else
        {
            return $this->source;
        }
    }

}
