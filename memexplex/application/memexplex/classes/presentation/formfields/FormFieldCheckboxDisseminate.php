<?php

/**
 * Builds a checkbox form element.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Dr. Totenkopf
 * @see FormField
 * @see FormFieldInterface
 */
class FormFieldCheckboxDisseminate extends FormFieldCheckbox
implements FormFieldInterface
{
    /**
     * Sets the source propery to the HTML string.
     */
    public function setSource()
    {
        if ($this->defaultValue === ''
            && ApplicationSession::isNameSet('CURATOR_PUBLISH_BY_DEFAULT')
        )
        {
            $this->defaultValue = ApplicationSession::getValue('CURATOR_PUBLISH_BY_DEFAULT');
        }
        parent::setSource();
    }

}
