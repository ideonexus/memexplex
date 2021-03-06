<?php

/**
 * Builds a GET link, but with truncated text.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormFieldHrefGetLink
 * @see FormFieldInterface
 */
class FormFieldHrefGetLinkTruncated extends FormFieldHrefGetLink
implements FormFieldInterface
{

    /**
     * @param string $defaultValue
     */
    public function setDefaultValue($defaultValue = '')
    {
        if (strlen($defaultValue) > 60)
        {
            $defaultValue = htmlspecialchars(substr($defaultValue,0,57)).'...';
        }
        else
        {
            $defaultValue = htmlspecialchars($defaultValue);
        }
        parent::setDefaultValue($defaultValue);
    }

}
