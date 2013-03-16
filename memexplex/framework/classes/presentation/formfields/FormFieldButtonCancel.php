<?php
/**
 * Builds a Cancel
 *
 * @package Framework
 * @subpackage Presentation
 * @author Ryan Somma
 * @see FormFieldButton
 * @see FormFieldInterface
 */
class FormFieldButtonCancel extends FormFieldButton
implements FormFieldInterface
{

    /**
     * @var string
     */
    protected $defaultValue = 'Cancel';

    /**
     * @var string
     */
    protected $title = 'Click to Cancel.';

}
