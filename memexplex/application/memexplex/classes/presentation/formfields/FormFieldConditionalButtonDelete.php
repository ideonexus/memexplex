<?php

/**
 * Builds a delete button conditionally on Id existing.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldConditionalButtonDelete extends FormFieldButtonDelete
implements FormFieldInterface
{
    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {
        if (isset($_GET['id']))
        {
    	  		parent::setSource();
    	  }
    	  else
    	  {
    	  		$this->source = "";
    	  }
    }

}
