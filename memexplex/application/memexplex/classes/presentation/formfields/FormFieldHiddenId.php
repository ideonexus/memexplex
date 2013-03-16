<?php
/**
 * Builds a hidden formfield that populates itself from the $_GET id variable.
 * Necessary workaround for mod_rewriting the ids into the url.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma 08/21/2008
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldHiddenId extends FormField
implements FormFieldInterface
{

    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {
        $value = "";
        if (isset($_GET['id']))
        {
            $value = $_GET['id'];
        }
        
        $this->source = "<input type=\"hidden\""
                        . " id=\"{$this->id}\""
                        . " name=\"{$this->id}\""
                        . " value=\"{$value}\""
                        . " />";
    }

}
