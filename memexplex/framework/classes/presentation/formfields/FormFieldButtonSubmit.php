<?php

/**
 * Builds an HTML Submit button.
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormFieldButton
 * @see FormFieldInterface
 */
class FormFieldButtonSubmit extends FormFieldButton
implements FormFieldInterface
{
    /**
     * @var string
     */
    protected $defaultValue = 'Submit';

    /**
     * @var string
     */
    protected $title = 'Click to Submit Form.';

}
