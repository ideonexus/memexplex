<?php

/**
 * Builds a password input.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormFieldText
 * @see FormFieldInterface
 */
class FormFieldPassword extends FormFieldText
implements FormFieldInterface
{
    /**
     * Uses GET method instead of AJAX Call
     * @var boolean
     */
    protected $formAction = "ajaxGetHTML();";

    /**
     * @var string $size
     */
    public function setFormAction($formAction = "ajaxGetHTML();")
    {
        $this->formAction = $formAction;
    }

    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {

        $this->source .=  
        	"<input"
            . " type=\"password\""
            . " name=\"{$this->id}\""
            . " id=\"{$this->id}\""
            . " value=\"{$this->defaultValue}\""
            . " size=\"{$this->size}\""
            . " maxlength=\"{$this->maxlength}\""
            . " onkeydown=\"enterPressed(event);\""
            . " disabled=\"disabled\""
            . " />"
            //If User Hits enter in password field, submit.
            ."<script type=\"text/javascript\">"
            ."function enterPressed(evn) {"
            ."if (window.event && window.event.keyCode == 13) {"
              .$this->formAction
            ."} else if (evn && evn.keyCode == 13) {"
              .$this->formAction
            ."}}"
            ."</script>";
                         
    }

}
