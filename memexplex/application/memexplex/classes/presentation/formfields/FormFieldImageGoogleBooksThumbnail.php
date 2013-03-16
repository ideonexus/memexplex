<?php

/**
 * Extended from FormFieldImage, this replaces the 
 * stupid little curly thingy google books adds to its thumbnails.
 *
 * @package Mxplx
 * @subpackage Presentation
 * @author Hari Seldon
 * @see FormFieldImage
 * @see FormFieldInterface
 */
class FormFieldImageGoogleBooksThumbnail extends FormFieldImage
implements FormFieldInterface
{
    /**
     * @param string $defaultValue
     */
    public function setDefaultValue($defaultValue = '')
    {
        $this->defaultValue = str_replace ('&edge=curl','',$defaultValue);
    }
}
