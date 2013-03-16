<?php

/**
 * Builds an HTML save button.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormFieldButton
 * @see FormFieldInterface
 */
class FormFieldButtonSave extends FormFieldButton
implements FormFieldInterface
{
    /**
     * @var string
     */
    protected $defaultValue = 'Save';

    /**
     * @var string
     */
    protected $title = 'Click to Save.';

}
